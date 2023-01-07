<?php

namespace Modules\Crm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\User;
use Modules\Crm\Entities\Schedule;
use DB;
use Yajra\DataTables\Facades\DataTables;
use App\Contact;
use App\Utils\Util;

class ReportController extends Controller
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
        $business_id = request()->session()->get('user.business_id');

        $is_admin = $this->commonUtil->is_admin(auth()->user(), $business_id);

        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $statuses = Schedule::statusDropdown();

        return view('crm::reports.index')->with(compact('statuses'));
    }

    /**
     * Lists follow ups count assigned to users.
     * @return Response
     */
    public function followUpsByUser()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $statuses = Schedule::statusDropdown();

            $query = User::where('users.business_id', $business_id)
                        ->user()
                        ->where('is_cmmsn_agnt', 0)
                        ->join('crm_schedule_users as su', 'su.user_id', '=', 'users.id')
                        ->join('crm_schedules as follow_ups', 'follow_ups.id', '=', 'su.schedule_id')
                        ->select(
                            DB::raw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as full_name"),
                            DB::raw("COUNT(su.id) as total_follow_ups"),
                            DB::raw("SUM( IF(follow_ups.status IS NULL AND follow_ups.id IS NOT NULL, 1, 0) ) as count_nulled")
                        )->groupBy('users.id');

            foreach ($statuses as $key => $value) {
                $query->addSelect(DB::raw("SUM(IF(follow_ups.status='$key', 1, 0)) as count_$key"));
            }

            return Datatables::of($query)
                    ->filterColumn('full_name', function ($query, $keyword) {
                        $query->whereRaw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) like ?", ["%{$keyword}%"]);
                    })
                    ->make(true);
        }
    }

    /**
     * Lists follow ups count assigned to contacts
     * @return Response
     */
    public function followUpsContact()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $statuses = Schedule::statusDropdown();

            $query = Contact::where('contacts.business_id', $business_id)
                        ->join('crm_schedules as follow_ups', 'follow_ups.contact_id', '=', 'contacts.id')
                        ->select(
                            'contacts.name',
                            'contacts.supplier_business_name',
                            DB::raw("COUNT(follow_ups.id) as total_follow_ups"),
                            DB::raw("SUM( IF(follow_ups.status IS NULL AND follow_ups.id IS NOT NULL, 1, 0) ) as count_nulled")
                        )->groupBy('contacts.id');

            foreach ($statuses as $key => $value) {
                $query->addSelect(DB::raw("SUM(IF(follow_ups.status='$key', 1, 0)) as count_$key"));
            }

            return Datatables::of($query)
                    ->addColumn('contact_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}} <br> @endif {{$name}}')
                    ->rawColumns(['contact_name'])
                    ->filterColumn('contact_name', function ($query, $keyword) {
                        $query->where( function($q) use($keyword) {
                            $q->where('contacts.name', 'like', "%{$keyword}%")
                                ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                        });
                    })
                    ->make(true);
        }
    }

    /**
     * Lists leads to customer conversion count
     * @return Response
     */

    public function leadToCustomerConversion()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $query = User::where('users.business_id', $business_id)
                        ->user()
                        ->where('is_cmmsn_agnt', 0)
                        ->join('contacts as c', 'c.converted_by', '=', 'users.id')
                        ->select(
                            DB::raw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as full_name"),
                            DB::raw("COUNT(c.id) as total_conversions"),
                            'users.id as DT_RowId'
                        )->groupBy('users.id');

            return Datatables::of($query)
                    ->filterColumn('full_name', function ($query, $keyword) {
                        $query->whereRaw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) like ?", ["%{$keyword}%"]);
                    })
                    ->make(true);
        }
    }

    /**
     * Lists leads to customer conversion details by a users
     * @return Response
     */
    public function showLeadToCustomerConversionDetails($user_id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $contacts = Contact::where('business_id', $business_id)
                            ->where('converted_by', $user_id)
                            ->orderBy('converted_on', 'desc')
                            ->get();

            return view('crm::reports.leads_to_customer_details')
                    ->with(compact('contacts'));
        }

    }
}
