<?php

namespace Modules\Crm\Http\Controllers;

use App\Contact;
use App\Http\Controllers\Controller;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Modules\Crm\Entities\CrmContact;
use Modules\Crm\Entities\Schedule;
use Yajra\DataTables\Facades\DataTables;
use Modules\Crm\Utils\CrmUtil;
use App\Transaction;

class ScheduleController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $moduleUtil;
    protected $crmUtil;
    /**
     * Constructor
     *
     * @param CommonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, CrmUtil $crmUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->crmUtil = $crmUtil;
        $this->status_bg = [
            'scheduled' => 'bg-yellow',
            'open' => 'bg-blue',
            'canceled' => 'bg-red',
            'cancelled' => 'bg-red',
            'completed' => 'bg-green',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_schedule = auth()->user()->can('crm.access_all_schedule');
        $can_access_own_schedule = auth()->user()->can('crm.access_own_schedule');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_schedule || $can_access_own_schedule)) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $schedules = Schedule::leftjoin('contacts', 'crm_schedules.contact_id', '=', 'contacts.id')
                        ->leftjoin('users as U', 'crm_schedules.created_by', '=', 'U.id')
                        ->with(['users'])
                        ->where('crm_schedules.business_id', $business_id)
                        ->select('crm_schedules.*', 'contacts.name as contact', 'contacts.supplier_business_name as biz_name',
                        'U.surname', 'U.first_name', 'U.last_name', 'crm_schedules.status as status', 'crm_schedules.created_at as added_on', 'contacts.type as contact_type', 'contacts.id as contact_id');

            if (request()->input('is_recursive') == 1) {
                $schedules->where('crm_schedules.is_recursive', 1);
            } else {
                $schedules->where('crm_schedules.is_recursive', 0);
            }

            if (!empty(request()->input('contact_id'))) {
                $schedules->where('crm_schedules.contact_id', request()->input('contact_id'));
            }

            if (!empty(request()->input('assgined_to'))) {
                $user_id = request()->input('assgined_to');
                $schedules->whereHas('users', function($q) use ($user_id){
                        $q->where('user_id', $user_id);
                    });
            }

            if (!empty(request()->input('status'))) {
                $schedules->where('crm_schedules.status', request()->input('status'));
            }

            if (!empty(request()->input('schedule_type'))) {
                $schedules->where('crm_schedules.schedule_type', request()->input('schedule_type'));
            }

            if (!empty(request()->input('start_date_time')) && !empty(request()->input('end_date_time'))) {
                $start_date = request()->input('start_date_time');
                $end_date = request()->input('end_date_time');
                $schedules->whereBetween(DB::raw('date(start_datetime)'), [$start_date, $end_date]);
            }

            if (!empty(request()->input('follow_up_by'))) {
                $schedules->where('crm_schedules.follow_up_by', request()->input('follow_up_by'));
            }
            
            if (!auth()->user()->can('superadmin') && !$can_access_all_schedule) {
                $user_id = auth()->user()->id;
                $schedules->whereHas('users', function($q) use ($user_id){
                    $q->where('user_id', $user_id);
                });
            }
            
            return Datatables::of($schedules)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                                <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                    '. __("messages.action").'
                                    <span class="caret"></span>
                                    <span class="sr-only">'
                                       . __("messages.action").'
                                    </span>
                                </button>
                                  <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                   <li>
                                        <a href="' . action('\Modules\Crm\Http\Controllers\ScheduleController@show', ['follow_up' => $row->id]) . '" class="cursor-pointer view_schedule">
                                            <i class="fa fa-eye"></i>
                                            '.__("messages.view").'
                                        </a>
                                    </li>';
                            if ($row->is_recursive != 1) {
                                $html .= '<li>
                                        <a data-href="' . action('\Modules\Crm\Http\Controllers\ScheduleController@edit', ['follow_up' => $row->id]) . '"class="cursor-pointer schedule_edit">
                                            <i class="fa fa-edit"></i>
                                            '.__("messages.edit").'
                                        </a>
                                    </li>';
                            }

                            $html .= '<li>
                                        <a data-href="' . action('\Modules\Crm\Http\Controllers\ScheduleController@destroy', ['follow_up' => $row->id]) . '" class="cursor-pointer schedule_delete">
                                            <i class="fas fa-trash"></i>
                                            '.__("messages.delete").'
                                        </a>
                                    </li>';

                    $html .= '</ul>
                            </div>';

                    return $html;
                })
                ->editColumn('start_datetime', ' @if(!empty($start_datetime))
                    {{@format_datetime($start_datetime)}}<br>
                    <i>(<span class="time-from-now">{{$start_datetime}}</span>)</i> @endif
                ')
                ->editColumn('end_datetime', '
                    @if(!empty($end_datetime)){{@format_datetime($end_datetime)}} @endif
                ')
                ->editColumn('contact', '
                    @if(!empty($biz_name)) {{$biz_name}},<br>@endif {{$contact}}
                    <br>
                    @if($contact_type == "lead")
                        <a href="{{action(\'\Modules\Crm\Http\Controllers\LeadController@show\', [\'lead\' => $contact_id])}}" target="_blank">
                            <i class="fas fa-external-link-square-alt text-info"></i>
                        </a>
                    @else
                        <a href="{{action(\'ContactController@show\', [$contact_id])}}" target="_blank">
                            <i class="fas fa-external-link-square-alt text-info"></i>
                        </a>
                    @endif
                ')
                ->addColumn('added_by', function($row) {
                    return "{$row->surname} {$row->first_name} {$row->last_name}";
                })
                ->addColumn('additional_info', function($row) {
                    $html = '';
                    $infos = $row->followup_additional_info;
                    if (!empty($infos)) {
                        foreach ($infos as $key => $value) {
                            $html .= $key .' : '.$value.'<br>';
                        }
                    }
                    return $html;
                })
                ->editColumn('added_on', '
                    {{@format_datetime($added_on)}}
                ')
                ->editColumn('schedule_type', function($row) {
                    $html = '';
                    if (!empty($row->schedule_type)) {
                        $html = '<div class="schedule_type" data-orig-value="'.__('crm::lang.'.$row->schedule_type).'" data-status-name="'.__('crm::lang.'.$row->schedule_type). '">
                                    '.__('crm::lang.'.$row->schedule_type).
                                '</div>';
                    }
                    return $html;
                })
                ->editColumn('users', function ($row) {
                    $html = '&nbsp;';
                    if ($row->users->count() > 0) {
                        foreach ($row->users as $user) {
                            if (isset($user->media->display_url)) {
                                $html .= '<img class="user_avatar" src="'.$user->media->display_url.'" data-toggle="tooltip" title="'.$user->user_full_name.'">';
                            } else {
                                $html .= '<img class="user_avatar" src="https://ui-avatars.com/api/?name='.$user->first_name.'" data-toggle="tooltip" title="'.$user->user_full_name.'">';
                            }
                        }
                    }
                    return $html;
                })
                ->editColumn('status', function($row) {
                    $html = '';
                    if (!empty($row->status)) {
                        $html = '<span class="text-center label status '.$this->status_bg[$row->status].'" data-orig-value="'.__('crm::lang.'.$row->status).'" data-status-name="'.__('crm::lang.'.$row->status). '"><small>
                                    '.__('crm::lang.'.$row->status).
                                '</small></span>';
                    }
                    return $html;
                })
                ->editColumn('follow_up_by', function($row) {
                    $follow_up_by = '';

                    if ($row->follow_up_by == 'payment_status') {
                        $follow_up_by = __('sale.payment_status') . ' - ' . __('lang_v1.' . $row->follow_up_by_value);
                    }  elseif ($row->follow_up_by == 'orders') {
                        $follow_up_by = __('restaurant.orders') . ' - ' . __('crm::lang.has_no_transactions');
                    }

                    return $follow_up_by;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'start_datetime', 'end_datetime', 'users', 'contact', 'added_on',
                    'additional_info', 'schedule_type', 'status', 'description'])
                ->make(true);
        }

        $leads = CrmContact::leadsDropdown($business_id, false);
        $contacts = Contact::customersDropdown($business_id, false)->toArray();

        foreach ($contacts as $key => $value) {
            $contacts[$key] = $value . ' {' . __('contact.customer') . '}';
        }
        foreach ($leads as $key => $value) {
            $contacts[$key] = $value . ' {' . __('crm::lang.lead') . '}';
        }

        $assigned_to = User::forDropdown($business_id, false);
        $statuses = Schedule::statusDropdown();
        $follow_up_types = Schedule::followUpTypeDropdown();

        return view('crm::schedule.index')
            ->with(compact('contacts', 'assigned_to', 'statuses', 'follow_up_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $schedule_for = request()->get('schedule_for', 'customer');
        $statuses = Schedule::statusDropdown();
        $follow_up_types = Schedule::followUpTypeDropdown();
        $notify_type = Schedule::followUpNotifyTypeDropdown();
        $followup_tags = $this->crmUtil->getAdvFollowupsTags();
        $users = User::forDropdown($business_id, false);

        if (request()->has('is_recursive')) {
            return view('crm::schedule.create_recursive_follow_up')
                ->with(compact('statuses', 'follow_up_types', 'notify_type', 'followup_tags', 'users'));
        }

        $customers = $this->getCustomerDropdown($business_id);
        if (request()->ajax()) {
            $contact_id = request()->get('contact_id', '');
            return view('crm::schedule.create')
                ->with(compact('customers', 'users', 'statuses', 'contact_id', 'schedule_for', 'follow_up_types', 'notify_type'));
        }

        return view('crm::schedule.create_advance_follow_up')
                ->with(compact('statuses', 'schedule_for', 'follow_up_types', 'notify_type', 'followup_tags', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->except(['_token', 'schedule_for', 'contact_ids']);
            if (empty($input['is_recursive'])) {
                $input['start_datetime'] = $this->commonUtil->uf_date($input['start_datetime'], true);
                $input['end_datetime'] = $this->commonUtil->uf_date($input['end_datetime'], true);
            }

            DB::beginTransaction();
            if (empty($input['follow_ups']) && empty($input['is_recursive'])) {
                $this->crmUtil->addFollowUp($input, \Auth::user());
            } else if (!empty($input['is_recursive'])) {
                $this->crmUtil->addRecursiveFollowUp($input, \Auth::user());
            } else {
                $this->crmUtil->addAdvanceFollowUp($input, \Auth::user());
            }
            DB::commit();

            $schedule_for = request()->get('schedule_for', 'customer');

            $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                    'schedule_for' => $schedule_for
                ];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
        }

        if (request()->ajax()) {
            return $output;
        } else {
            return redirect()->action('\Modules\Crm\Http\Controllers\ScheduleController@index')->with(['status' => $output]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_schedule = auth()->user()->can('crm.access_all_schedule');
        $can_access_own_schedule = auth()->user()->can('crm.access_own_schedule');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_schedule || $can_access_own_schedule)) {
            abort(403, 'Unauthorized action.');
        }

        $query = Schedule::with(['customer', 'users', 'invoices', 'invoices.payment_lines'])
                        ->where('business_id', $business_id);

        if (!$can_access_all_schedule && $can_access_own_schedule) {
            $query->where( function($qry) {
                $qry->whereHas('users', function($q){
                    $q->where('user_id', auth()->user()->id);
                })->orWhere('created_by', auth()->user()->id);
            });
        }
        $schedule = $query->findOrFail($id);

        return view('crm::schedule.show')
            ->with(compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_schedule = auth()->user()->can('crm.access_all_schedule');
        $can_access_own_schedule = auth()->user()->can('crm.access_own_schedule');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_schedule || $can_access_own_schedule)) {
            abort(403, 'Unauthorized action.');
        }

        $query = Schedule::with(['customer', 'users'])
                        ->where('business_id', $business_id);

        if (!$can_access_all_schedule && $can_access_own_schedule) {
            $query->where( function($qry) {
                $qry->whereHas('users', function($q){
                    $q->where('user_id', auth()->user()->id);
                })->orWhere('created_by', auth()->user()->id);
            });
        }
        $schedule = $query->findOrFail($id);

        $schedule_for = request()->get('schedule_for', 'customer');

        $leads = CrmContact::leadsDropdown($business_id, false, false);
        $customers = Contact::customersDropdown($business_id, false, false)->toArray();

        foreach ($customers as $key => $value) {
            $customers[$key] = $value . ' (' . __('contact.customer') . ')';
        }
        foreach ($leads as $key => $value) {
            $customers[$key] = $value . ' (' . __('crm::lang.lead') . ')';
        }

        $users = User::forDropdown($business_id, false);
        $statuses = Schedule::statusDropdown();
        $follow_up_types = Schedule::followUpTypeDropdown();
        $notify_type = Schedule::followUpNotifyTypeDropdown();

        return view('crm::schedule.edit')
            ->with(compact('schedule', 'customers', 'users', 'statuses', 'schedule_for', 'follow_up_types', 'notify_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_schedule = auth()->user()->can('crm.access_all_schedule');
        $can_access_own_schedule = auth()->user()->can('crm.access_own_schedule');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_schedule || $can_access_own_schedule)) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $request = $request->except(['_method', '_token', 'schedule_for']);
            $request['start_datetime'] = $this->commonUtil->uf_date($request['start_datetime'], true);
            $request['end_datetime'] = $this->commonUtil->uf_date($request['end_datetime'], true);

            $this->crmUtil->updateFollowUp($id, $request, \Auth::user());
            
            $schedule_for = request()->get('schedule_for', 'customer');

            $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                    'schedule_for' => $schedule_for
                ];
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_schedule = auth()->user()->can('crm.access_all_schedule');
        $can_access_own_schedule = auth()->user()->can('crm.access_own_schedule');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_schedule || $can_access_own_schedule)) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $query = Schedule::where('business_id', $business_id);

                if (!$can_access_all_schedule && $can_access_own_schedule) {
                    $query->where( function($qry) {
                        $qry->whereHas('users', function($q){
                            $q->where('user_id', auth()->user()->id);
                        })->orWhere('created_by', auth()->user()->id);
                    });
                }
                $schedule = $query->findOrFail($id);

                $schedule->delete();

                $view_type = request()->get('view_type', 'schedule');
                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                    'action' => action('\Modules\Crm\Http\Controllers\ScheduleController@index'),
                    'view_type' => $view_type
                ];
            } catch (Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }
            return $output;
        }
    }

    /**
     * Get today's schedule
     *
     * @return \Illuminate\Http\Response
     */
    public function getTodaysSchedule()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $start_date = Carbon::today();

            $query = $this->crmUtil->getFollowUpForGivenDate(\Auth::user(), $start_date);

            $schedules = $query->get();

            $schedule_html = View::make('crm::schedule.partial.today_schedule')
                        ->with(compact('schedules'))
                        ->render();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'todays_schedule' => $schedule_html
            ];
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }

    public function getLeadSchedule(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $lead_id = $request->get('lead_id');
        $schedules = Schedule::with('users')
                        ->where('business_id', $business_id)
                        ->where('contact_id', $lead_id)
                        ->select('*');

        return Datatables::of($schedules)
            ->addColumn('action', function ($row) {
                $html = '<div class="btn-group">
                            <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                '. __("messages.action").'
                                <span class="caret"></span>
                                <span class="sr-only">'
                                   . __("messages.action").'
                                </span>
                            </button>
                              <ul class="dropdown-menu dropdown-menu-left" role="menu">
                               <li>
                                    <a href="' . action('\Modules\Crm\Http\Controllers\ScheduleController@show', ['follow_up' => $row->id]) . '" class="cursor-pointer view_schedule">
                                        <i class="fa fa-eye"></i>
                                        '.__("messages.view").'
                                    </a>
                                </li>
                                <li>
                                    <a data-href="' . action('\Modules\Crm\Http\Controllers\ScheduleController@edit', ['follow_up' => $row->id]) . '?schedule_for=lead"class="cursor-pointer schedule_edit">
                                        <i class="fa fa-edit"></i>
                                        '.__("messages.edit").'
                                    </a>
                                </li>
                                <li>
                                    <a data-href="' . action('\Modules\Crm\Http\Controllers\ScheduleController@destroy', ['follow_up' => $row->id]) . '" class="cursor-pointer schedule_delete">
                                        <i class="fas fa-trash"></i>
                                        '.__("messages.delete").'
                                    </a>
                                </li>';

                $html .= '</ul>
                        </div>';

                return $html;
            })
            ->editColumn('start_datetime', '
                {{@format_datetime($start_datetime)}}
            ')
            ->editColumn('end_datetime', '
                {{@format_datetime($end_datetime)}}
            ')
            ->editColumn('users', function ($row) {
                $html = '&nbsp;';
                foreach ($row->users as $user) {
                    if (isset($user->media->display_url)) {
                        $html .= '<img class="user_avatar" src="'.$user->media->display_url.'" data-toggle="tooltip" title="'.$user->user_full_name.'">';
                    } else {
                        $html .= '<img class="user_avatar" src="https://ui-avatars.com/api/?name='.$user->first_name.'" data-toggle="tooltip" title="'.$user->user_full_name.'">';
                    }
                }

                return $html;
            })
            ->removeColumn('id')
            ->rawColumns(['action', 'start_datetime', 'end_datetime', 'users'])
            ->make(true);
    }

    /**
     * Get invoices dropdaown by payment status or transaction activity
     *
     * @return array
     */
    public function getInvoicesForFollowUp()
    {
        $business_id = request()->session()->get('user.business_id');
        $follow_up_by = request()->input('follow_up_by');
        $payment_status = request()->input('payment_status');

        $query = Transaction::with(['contact'])
                            ->where('business_id', $business_id)
                            ->where('type', 'sell')
                            ->where('status', 'final');

        if ($follow_up_by == 'payment_status') {
            if ($payment_status == 'all') {
                $query->whereIn('payment_status', ['due', 'partial']);
            } else if ($payment_status == 'due') {
                $query->where('payment_status', 'due');
            } else if ($payment_status == 'partial') {
                $query->where('payment_status', 'partial');
            } else if ($payment_status == 'overdue') {
                $query->overDue();
            }
        }

        $sells = $query->select('id', 'invoice_no', 'payment_status', 'contact_id', 'pay_term_number', 'pay_term_type', 'transaction_date')
            ->get();

        $sells_array = [];

        foreach ($sells as $sell) {
            $payment_status = Transaction::getPaymentStatus($sell);
            $contact = ' - '.$sell->contact->name;
            if (!empty($sell->contact->supplier_business_name)) {
                $contact = ' - ' .$sell->contact->supplier_business_name . $contact;
            }

            $sells_array[] = [
                'id' => $sell->id,
                'text' => $sell->invoice_no . ' (' . __('lang_v1.' . $payment_status) . $contact .')'
            ];
        }

        return $sells_array;
    }

    /**
     * Groups customers for follow-up based on payment status 
     * or transaction activity
     *
     * @return html
     */
    public function getFollowUpGroups()
    {
        $business_id = request()->session()->get('user.business_id');
        $users = User::forDropdown($business_id, false);
        $follow_up_by = request()->input('follow_up_by');
        if ($follow_up_by == 'payment_status') {
            $invoices = request()->input('invoices');
            $sells = Transaction::where('business_id', $business_id)
                            ->where('type', 'sell')
                            ->where('status', 'final')
                            ->whereIn('id', $invoices)
                            ->with(['contact'])
                            ->select('id', 'invoice_no', 'contact_id')
                            ->get();

            $sells_by_customer = [];

            foreach ($sells as $sell) {
                $sells_by_customer[$sell->contact_id][] = $sell;
            }

            return view('crm::schedule.partial.group_invoices_by_customer')
                ->with(compact('sells_by_customer', 'users'));
        } elseif ($follow_up_by == 'contact_name') {
            $contact_ids = request()->input('contact_ids');
            $customers = Contact::where('contacts.business_id', $business_id)
                            ->whereIn('id', $contact_ids)
                            ->get();

            return view('crm::schedule.partial.group_customers')
                ->with(compact('customers', 'users'));
        } else {
            $days = request()->input('days');

            $from_transaction_date = \Carbon::now()->subDays($days)->format('Y-m-d');
            $query = Contact::where('contacts.business_id', $business_id)
                            ->OnlyCustomers()
                            ->leftJoin('transactions as t', 't.contact_id', '=', 'contacts.id');

            if ($follow_up_by == 'has_transactions') {
                $query->whereNotNull('t.id')
                    ->havingRaw("MAX(DATE(transaction_date)) >= '{$from_transaction_date}'");
            }

            if ($follow_up_by == 'has_no_transactions') {
                $query->havingRaw("MAX(DATE(transaction_date)) < '{$from_transaction_date}'")
                     ->orHavingRaw('transaction_date IS NULL');
            }

            $customers = $query->select('contacts.*', 'transaction_date')
                            ->groupBy('contacts.id')
                            ->get();

            return view('crm::schedule.partial.group_customers')
                ->with(compact('customers', 'users'));
        }
        

    }

    public function getCustomerDropdown($business_id)
    {
        $leads = CrmContact::leadsDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false)->toArray();

        foreach ($customers as $key => $value) {
            $customers[$key] = $value . ' {' . __('contact.customer') . '}';
        }
        foreach ($leads as $key => $value) {
            $customers[$key] = $value . ' {' . __('crm::lang.lead') . '}';
        }

        return $customers;
    }
}
