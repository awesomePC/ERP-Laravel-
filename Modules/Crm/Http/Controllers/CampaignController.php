<?php

namespace Modules\Crm\Http\Controllers;

use App\Business;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Crm\Entities\Campaign;
use Modules\Crm\Entities\CrmContact;
use Modules\Crm\Notifications\SendCampaignNotification;
use Notification;
use Yajra\DataTables\Facades\DataTables;
use App\Transaction;

class CampaignController extends Controller
{
    protected $notificationUtil;
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param NotificationUtil $notificationUtil
     * @return void
     */
    public function __construct(NotificationUtil $notificationUtil, ModuleUtil $moduleUtil)
    {
        $this->notificationUtil = $notificationUtil;
        $this->moduleUtil = $moduleUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        $can_access_all_campaigns = auth()->user()->can('crm.access_all_campaigns');
        $can_access_own_campaigns = auth()->user()->can('crm.access_own_campaigns');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_campaigns || $can_access_own_campaigns) ) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $campaigns = Campaign::with('createdBy')
                        ->where('business_id', $business_id)
                        ->select('*');

            if (!$can_access_all_campaigns && $can_access_own_campaigns) {
                $campaigns->where('created_by', auth()->user()->id);
            }

            if (!empty(request()->get('campaign_type'))) {
                $campaigns->where('campaign_type', request()->get('campaign_type'));
            }

            return Datatables::of($campaigns)
                    ->addColumn('action', function ($row) {
                        $html = '<a data-href="' . action('\Modules\Crm\Http\Controllers\CampaignController@show', ['campaign' => $row->id]) . '" class="cursor-pointer view_a_campaign btn btn-xs btn-info m-2">
                            <i class="fa fa-eye"></i>
                            '.__("messages.view").'
                            </a>';

                        if (empty($row->sent_on)) {
                            $html .= '
                            <a href="' . action('\Modules\Crm\Http\Controllers\CampaignController@edit', ['campaign' => $row->id]) . '"class="cursor-pointer btn btn-xs btn-primary m-2">
                                <i class="fa fa-edit"></i>
                                '.__("messages.edit").'
                            </a>';
                        }

                        $html .= '<a data-href="' . action('\Modules\Crm\Http\Controllers\CampaignController@destroy', ['campaign' => $row->id]) . '" class="cursor-pointer delete_a_campaign btn btn-xs btn-danger m-2">
                            <i class="fas fa-trash"></i>
                            '.__("messages.delete").'
                            </a>';

                        if (empty($row->sent_on)) {
                            $html .= '<a data-href="' . action('\Modules\Crm\Http\Controllers\CampaignController@sendNotification', ['id' => $row->id]) . '" class="cursor-pointer send_campaign_notification btn btn-xs btn-warning m-2">
                                <i class="fas fa-envelope-square"></i>
                                '.__("crm::lang.send_notification").'
                            </a>';
                        }

                        return $html;
                    })
                    ->editColumn('campaign_type', '
                        @if($campaign_type == "sms")
                            {{__("crm::lang.sms")}}
                        @elseif($campaign_type == "email")
                            {{__("business.email")}}
                        @endif
                    ')
                    ->editColumn('created_at', '
                        {{@format_date($created_at)}}
                    ')
                    ->editColumn('name', function ($row) {
                        $is_notified = '';
                        if (!empty($row->sent_on)) {
                            $is_notified = '<br> <span class="label label-success">'.
                                                __('crm::lang.sent') .
                                            '</span>';
                        }

                        return $row->name . $is_notified;
                    })
                    ->editColumn('createdBy', function ($row) {
                        return optional($row->createdBy)->user_full_name;
                    })
                    ->removeColumn('id')
                    ->rawColumns(['action', 'name', 'campaign_type', 'createdBy', 'created_at'])
                    ->make(true);
        }

        return view('crm::campaign.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_campaigns = auth()->user()->can('crm.access_all_campaigns');
        $can_access_own_campaigns = auth()->user()->can('crm.access_own_campaigns');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_campaigns || $can_access_own_campaigns) ) {
            abort(403, 'Unauthorized action.');
        }

        $tags = Campaign::getTags();
        $leads = CrmContact::leadsDropdown($business_id, false);
        $customers = CrmContact::customersDropdown($business_id, false);
        $contact_ids = $request->get('contact_ids', '');

        $contacts = [];
        foreach ($leads as $key => $lead) {
            $contacts[$key] = $lead;
        }

        foreach ($customers as $key => $customer) {
            $contacts[$key] = $customer;
        }

        return view('crm::campaign.create')
            ->with(compact('tags', 'leads', 'customers', 'contact_ids', 'contacts'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('name', 'campaign_type', 'subject', 'email_body', 'sms_body');
            
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->user()->id;
            $customers = $request->input('contact_id', []);
            $leads = $request->input('lead_id', []);
            $contacts = $request->input('contact', []); //birthday_wishes

            $input['contact_ids'] = array_merge($customers, $leads, $contacts);

            $input['additional_info'] = [
                'to' => $request->input('to'),
                'trans_activity' => $request->input('trans_activity'),
                'in_days' => $request->input('in_days')
            ];

            DB::beginTransaction();
            
            $campaign = Campaign::create($input);

            DB::commit();

            if ($request->get('send_notification') && !empty($campaign)) {
                $this->__sendCampaignNotification($campaign->id, $business_id);
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()
            ->action('\Modules\Crm\Http\Controllers\CampaignController@index')
            ->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_campaigns = auth()->user()->can('crm.access_all_campaigns');
        $can_access_own_campaigns = auth()->user()->can('crm.access_own_campaigns');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_campaigns || $can_access_own_campaigns) ) {
            abort(403, 'Unauthorized action.');
        }

        $query = Campaign::with('createdBy')
                    ->where('business_id', $business_id);

        if (!$can_access_all_campaigns && $can_access_own_campaigns) {
            $query->where('created_by', auth()->user()->id);
        }

        $campaign = $query->findOrFail($id);

        $notifiable_users = CrmContact::find($campaign->contact_ids);
        
        return view('crm::campaign.show')
            ->with(compact('campaign', 'notifiable_users'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_campaigns = auth()->user()->can('crm.access_all_campaigns');
        $can_access_own_campaigns = auth()->user()->can('crm.access_own_campaigns');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_campaigns || $can_access_own_campaigns) ) {
            abort(403, 'Unauthorized action.');
        }

        $query = Campaign::where('business_id', $business_id);

        if (!$can_access_all_campaigns && $can_access_own_campaigns) {
            $query->where('created_by', auth()->user()->id);
        }

        $campaign = $query->findOrFail($id);

        $tags = Campaign::getTags();
        $leads = CrmContact::leadsDropdown($business_id, false);
        $customers = CrmContact::customersDropdown($business_id, false);

        $contacts = [];
        foreach ($leads as $key => $lead) {
            $contacts[$key] = $lead;
        }

        foreach ($customers as $key => $customer) {
            $contacts[$key] = $customer;
        }

        return view('crm::campaign.edit')
            ->with(compact('tags', 'campaign', 'leads', 'customers', 'contacts'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_campaigns = auth()->user()->can('crm.access_all_campaigns');
        $can_access_own_campaigns = auth()->user()->can('crm.access_own_campaigns');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_campaigns || $can_access_own_campaigns) ) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('name', 'campaign_type', 'subject', 'email_body', 'sms_body');
            
            $customers = $request->input('contact_id', []);
            $leads = $request->input('lead_id', []);
            $contacts = $request->input('contact', []); //birthday_wishes

            $input['contact_ids'] = array_merge($customers, $leads, $contacts);

            $input['additional_info'] = [
                'to' => $request->input('to'),
                'trans_activity' => $request->input('trans_activity'),
                'in_days' => $request->input('in_days')
            ];
            
            $query = Campaign::where('business_id', $business_id);

            if (!$can_access_all_campaigns && $can_access_own_campaigns) {
                $query->where('created_by', auth()->user()->id);
            }

            $campaign = $query->findOrFail($id);

            DB::beginTransaction();

            $campaign->update($input);

            DB::commit();

            if ($request->get('send_notification') && !empty($campaign)) {
                $this->__sendCampaignNotification($campaign->id, $business_id);
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()
            ->action('\Modules\Crm\Http\Controllers\CampaignController@index')
            ->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_campaigns = auth()->user()->can('crm.access_all_campaigns');
        $can_access_own_campaigns = auth()->user()->can('crm.access_own_campaigns');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_campaigns || $can_access_own_campaigns) ) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $query = Campaign::where('business_id', $business_id);

                if (!$can_access_all_campaigns && $can_access_own_campaigns) {
                    $query->where('created_by', auth()->user()->id);
                }

                $query->where('id', $id)
                    ->delete();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
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

    public function sendNotification($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $output = $this->__sendCampaignNotification($id, $business_id);
            
            return $output;
        }
    }

    public function __sendCampaignNotification($campaign_id, $business_id)
    {   
        try {
            $campaign = Campaign::where('business_id', $business_id)
                            ->findOrFail($campaign_id);

            $business = Business::findOrFail($business_id);

            if ((!empty($campaign->additional_info['to']) && in_array($campaign->additional_info['to'], ['lead', 'customer', 'contact']))
                || (!isset($campaign->additional_info['to']) && !empty($campaign->contact_ids))) {
                $contact_ids = $campaign->contact_ids;
            } elseif (!empty($campaign->additional_info['to']) && in_array($campaign->additional_info['to'], ['transaction_activity'])) {
                $day = Carbon::now()->subDays($campaign->additional_info['in_days'])->toDateTimeString();
                $query = Transaction::where('business_id', $business_id)
                                ->select('contact_id', DB::raw('MAX(transaction_date) as last_shoped'));

                if ($campaign->additional_info['trans_activity'] == 'has_transactions') {
                    $query->having('last_shoped', '>=', $day);
                } elseif ($campaign->additional_info['trans_activity'] == 'has_no_transactions') {
                    $query->having('last_shoped', '<=', $day);
                }

                $transactions = $query
                                ->groupBy('contact_id')
                                ->get();
                                
                $contact_ids = $transactions->pluck('contact_id')->toArray();
            }

            $notifiable_users = CrmContact::find($contact_ids);
            
            if (!empty($notifiable_users) && $campaign->campaign_type == 'sms') {
                $notification_data['sms_settings'] = request()->session()->get('business.sms_settings');
                foreach ($notifiable_users as $user) {
                    $notification_data['mobile_number'] = $user->mobile;
                    $notification_data['sms_body'] = preg_replace(["/{contact_name}/", "/{campaign_name}/", "/{business_name}/"], [$user->name, $campaign->name, $business->name], $campaign->sms_body);

                    $this->notificationUtil->sendSms($notification_data);
                }
            } elseif (!empty($notifiable_users) && $campaign->campaign_type == 'email') {
                Notification::send($notifiable_users, new SendCampaignNotification($campaign, $business));
            }

            DB::beginTransaction();

            $campaign->sent_on = Carbon::now();
            $campaign->save();

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
}
