<?php

namespace Modules\Crm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Crm\Entities\CrmCallLog;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Carbon\CarbonInterval;
use App\Contact;
use App\User;
use App\Utils\Util;

class CallLogController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if((!auth()->user()->can('crm.view_all_call_log') && !auth()->user()->can('crm.view_own_call_log')) || !config('constants.enable_crm_call_log')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $query = CrmCallLog::where('crm_call_logs.business_id', $business_id)
                            ->leftJoin('contacts as c', 'crm_call_logs.contact_id', '=', 'c.id')
                            ->leftJoin('users as u', 'crm_call_logs.user_id', '=', 'u.id')
                            ->leftJoin('users as created_users', 'crm_call_logs.created_by', '=', 'created_users.id')
                            ->select(
                                'crm_call_logs.*',
                                'c.name as customer_name',
                                'c.supplier_business_name',
                                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user_full_name"),
                                DB::raw("CONCAT(COALESCE(created_users.surname, ''), ' ', COALESCE(created_users.first_name, ''), ' ', COALESCE(created_users.last_name, '')) as created_user_name")
                            );

            if (!auth()->user()->can('crm.view_all_call_log')) {
                $query->where('crm_call_logs.created_by', auth()->user()->id);
            }

            if (!empty(request()->get('contact_id'))) {
                $query->where('crm_call_logs.contact_id', request()->get('contact_id'));
            }

            if (!empty(request()->get('user_id'))) {
                $query->where('crm_call_logs.created_by', request()->get('user_id'));
            }

            if (!empty(request()->input('start_time')) && !empty(request()->input('end_time'))) {
                $start_time = request()->input('start_time');
                $end_time = request()->input('end_time');
                $query->whereDate('crm_call_logs.start_time', '>=', $start_time)
                    ->whereDate('crm_call_logs.start_time', '<=', $end_time);
            }

            return Datatables::of($query)
                ->editColumn('start_time', '@if(!empty($start_time)) {{@format_datetime($start_time)}} @endif')
                ->editColumn('end_time', '@if(!empty($end_time)) {{@format_datetime($end_time)}} @endif')
                ->editColumn('duration', function($row){
                    $duration = !empty($row->duration) ? CarbonInterval::seconds($row->duration)->cascade()->forHumans() : '';
                        return $duration;
                })
                ->addColumn('contact_number', '{{$mobile_number}} @if(!empty($mobile_name))
                <br> ({{$mobile_name}}) @endif')
                ->addColumn('contact_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}} <br> @endif {{$customer_name}}')
                ->addColumn('mass_delete', function ($row) {
                    return  '<input type="checkbox" class="row-select" value="' . $row->id .'">' ;
                })
                ->rawColumns(['contact_name', 'contact_number', 'mass_delete'])
                ->filterColumn('user_full_name', function ($query, $keyword) {
                        $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                    })
                ->filterColumn('created_user_name', function ($query, $keyword) {
                        $query->whereRaw("CONCAT(COALESCE(created_users.surname, ''), ' ', COALESCE(created_users.first_name, ''), ' ', COALESCE(created_users.last_name, '')) like ?", ["%{$keyword}%"]);
                    })
                ->filterColumn('contact_name', function ($query, $keyword) {
                    $query->where( function($q) use($keyword) {
                        $q->where('c.name', 'like', "%{$keyword}%")
                            ->orWhere('c.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('contact_number', function ($query, $keyword) {
                    $query->where( function($q) use($keyword) {
                        $q->where('mobile_number', 'like', "%{$keyword}%")
                            ->orWhere('mobile_name', 'like', "%{$keyword}%");
                    });
                })
                ->make(true);
        }

        $contacts = Contact::contactDropdown($business_id, false, false);
        $users = User::forDropdown($business_id, false);

        $is_admin = $this->commonUtil->is_admin(auth()->user());

        return view('crm::call_logs.index')->with(compact('contacts', 'users', 'is_admin'));
    }

     /**
     * Mass deletes call logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        $is_admin = $this->commonUtil->is_admin(auth()->user());
        
        $business_id = $request->session()->get('user.business_id');

        $selected_rows = explode(',', $request->input('selected_rows'));

        if (!empty($selected_rows)) {
            CrmCallLog::where('business_id', $business_id)
                    ->whereIn('id', $selected_rows)
                    ->delete();
        }
        $output = ['success' => 1,
                            'msg' => __('lang_v1.deleted_success')
                        ];

        return redirect()->back()->with(['status' => $output]);
    }
}
