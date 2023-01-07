<?php

namespace Modules\Essentials\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\User;
use Yajra\DataTables\Facades\DataTables;
use Modules\Essentials\Entities\EssentialsUserSalesTarget;
use App\Utils\ModuleUtil;
use DB;

class SalesTargetController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.access_sales_target')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $user_id = request()->session()->get('user.id');

            $users = User::where('business_id', $business_id)
                        ->user()
                        ->where('allow_login', 1)
                        ->select(['id',
                            DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name")]);

            return Datatables::of($users)
                ->addColumn(
                    'action',
                    '<button type="button" data-href="{{action(\'\Modules\Essentials\Http\Controllers\SalesTargetController@setSalesTarget\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container="#set_sales_target_modal"><i class="fas fa-bullseye"></i> @lang("essentials::lang.set_sales_target")</button>'
                )
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->where( function($q) use ($keyword){
                        $q->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"])
                        ->orWhere('username', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('essentials::sales_targets.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function setSalesTarget($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.access_sales_target')) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('business_id', $business_id)
                    ->find($id);

        $sales_targets = EssentialsUserSalesTarget::where('user_id', $id)
                                                ->get();

        return view('essentials::sales_targets.sales_target_modal')->with(compact('user', 'sales_targets'));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function saveSalesTarget(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.access_sales_target')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $target_ids = [];
            if (!empty($request->input('edit_target'))) {
                foreach($request->input('edit_target') as $key => $value) {
                    $target = EssentialsUserSalesTarget::where('user_id', 
                                        $request->input('user_id'))
                                        ->where('id', $key)
                                        ->update([
                                            'target_start' => $this->moduleUtil->num_uf($value['target_start']),
                                            'target_end' => $this->moduleUtil->num_uf($value['target_end']),
                                            'commission_percent' => $this->moduleUtil->num_uf($value['commission_percent'])
                                        ]);
                    $target_ids[] = $key;
                }
            }

            EssentialsUserSalesTarget::where('user_id', 
                                        $request->input('user_id'))
                                    ->whereNotIn('id', $target_ids)
                                    ->delete();

            foreach($request->input('sales_amount_start') as $key => $value) {
                $sales_amount_end = $request->input('sales_amount_end')[$key];
                $commission_percent = $this->moduleUtil->num_uf($request->input('commission')[$key]);

                $target_start = $this->moduleUtil->num_uf($value);
                $target_end = $this->moduleUtil->num_uf($sales_amount_end);

                if (empty($target_start) && empty($target_end)) {
                    continue;
                }

                $data = [
                    'user_id' => $request->input('user_id'),
                    'target_start' => $target_start,
                    'target_end' => $target_end,
                    'commission_percent' => $commission_percent
                ];
                EssentialsUserSalesTarget::create($data);
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                        'success' => false,
                        'msg' => __('messages.something_went_wrong')
                        ];

        }

        return back()->with('status', $output);
    }

}
