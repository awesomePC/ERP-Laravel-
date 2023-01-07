<?php

namespace Modules\Essentials\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsLeaveType;
use Yajra\DataTables\Facades\DataTables;

class EssentialsLeaveTypeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
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

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('essentials.crud_leave_type')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $leave_types = EssentialsLeaveType::where('business_id', $business_id)
                        ->select(['leave_type', 'max_leave_count', 'id']);

            return Datatables::of($leave_types)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>'
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('essentials::leave_type.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        if (!auth()->user()->can('essentials.crud_leave_type')) {
            abort(403, 'Unauthorized action.');
        }

        return view('essentials::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('essentials.crud_leave_type')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['leave_type', 'max_leave_count', 'leave_count_interval']);
            
            $input['business_id'] = $business_id;

            EssentialsLeaveType::create($input);
            $output = ['success' => true,
                            'msg' => __("lang_v1.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('essentials::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('essentials.crud_leave_type')) {
            abort(403, 'Unauthorized action.');
        }

        $leave_type = EssentialsLeaveType::where('business_id', $business_id)
                                        ->find($id);

        return view('essentials::leave_type.edit')->with(compact('leave_type'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('essentials.crud_leave_type')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['leave_type', 'max_leave_count',
                'leave_count_interval']);

            $input['business_id'] = $business_id;

            EssentialsLeaveType::where('business_id', $business_id)
                            ->where('id', $id)
                            ->update($input);

            $output = ['success' => true,
                            'msg' => __("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
