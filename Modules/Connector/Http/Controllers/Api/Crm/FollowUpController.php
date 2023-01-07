<?php

namespace Modules\Connector\Http\Controllers\Api\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Connector\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Utils\Util;
use Modules\Crm\Utils\CrmUtil;

/**
 * @group CRM
 * @authenticated
 *
 * APIs for managing follow up
 */
class FollowUpController extends ApiController
{   
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $commonUtil;
    protected $crmUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, CrmUtil $crmUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->crmUtil = $crmUtil;
    }

    /**
     * List Follow ups
     *
     * @queryParam start_date format: Y-m-d (Ex: 2020-12-16) Example: 2020-12-16
     * @queryParam end_date format: Y-m-d (Ex: 2020-12-16) Example: 2020-12-16
     * @queryParam status filter the result through status, get status from getFollowUpResources->statuses
     * @queryParam follow_up_type filter the result through follow_up_type, get follow_up_type from getFollowUpResources->follow_up_types
     * @queryParam order_by Column name to sort the result, Column: start_datetime Example:start_datetime
     * @queryParam direction Direction to sort the result, Required if using 'order_by', direction: desc, asc Example:desc
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     * @response {
            "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "contact_id": 50,
                    "title": "Test Follow up",
                    "status": "scheduled",
                    "start_datetime": "2020-12-16 15:15:00",
                    "end_datetime": "2020-12-16 15:15:00",
                    "description": "<p>tst</p>",
                    "schedule_type": "call",
                    "allow_notification": 0,
                    "notify_via": {
                        "sms": 0,
                        "mail": 1
                    },
                    "notify_before": null,
                    "notify_type": "minute",
                    "created_by": 1,
                    "followup_additional_info": null,
                    "created_at": "2020-12-16 03:15:23",
                    "updated_at": "2020-12-16 15:46:34",
                    "customer": {
                        "id": 50,
                        "business_id": 1,
                        "type": "lead",
                        "supplier_business_name": null,
                        "name": " Lead 4  ",
                        "prefix": null,
                        "first_name": "Lead 4",
                        "middle_name": null,
                        "last_name": null,
                        "email": null,
                        "contact_id": "CO0011",
                        "contact_status": "active",
                        "tax_number": null,
                        "city": null,
                        "state": null,
                        "country": null,
                        "address_line_1": null,
                        "address_line_2": null,
                        "zip_code": null,
                        "dob": null,
                        "mobile": "234567",
                        "landline": null,
                        "alternate_number": null,
                        "pay_term_number": null,
                        "pay_term_type": null,
                        "credit_limit": null,
                        "created_by": 1,
                        "balance": "0.0000",
                        "total_rp": 0,
                        "total_rp_used": 0,
                        "total_rp_expired": 0,
                        "is_default": 0,
                        "shipping_address": null,
                        "position": null,
                        "customer_group_id": null,
                        "crm_source": "55",
                        "crm_life_stage": "62",
                        "custom_field1": null,
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null,
                        "custom_field5": null,
                        "custom_field6": null,
                        "custom_field7": null,
                        "custom_field8": null,
                        "custom_field9": null,
                        "custom_field10": null,
                        "deleted_at": null,
                        "created_at": "2020-12-15 23:14:48",
                        "updated_at": "2021-01-07 15:32:52",
                        "remember_token": null,
                        "password": null
                    }
                },
                {
                    "id": 2,
                    "business_id": 1,
                    "contact_id": 50,
                    "title": "Test Follow up 1",
                    "status": "completed",
                    "start_datetime": "2020-12-16 15:46:00",
                    "end_datetime": "2020-12-16 15:46:00",
                    "description": "<p>Test Follow up</p>",
                    "schedule_type": "call",
                    "allow_notification": 0,
                    "notify_via": {
                        "sms": 0,
                        "mail": 1
                    },
                    "notify_before": null,
                    "notify_type": "minute",
                    "created_by": 1,
                    "followup_additional_info": null,
                    "created_at": "2020-12-16 15:46:57",
                    "updated_at": "2020-12-17 10:24:11",
                    "customer": {
                        "id": 50,
                        "business_id": 1,
                        "type": "lead",
                        "supplier_business_name": null,
                        "name": " Lead 4  ",
                        "prefix": null,
                        "first_name": "Lead 4",
                        "middle_name": null,
                        "last_name": null,
                        "email": null,
                        "contact_id": "CO0011",
                        "contact_status": "active",
                        "tax_number": null,
                        "city": null,
                        "state": null,
                        "country": null,
                        "address_line_1": null,
                        "address_line_2": null,
                        "zip_code": null,
                        "dob": null,
                        "mobile": "234567",
                        "landline": null,
                        "alternate_number": null,
                        "pay_term_number": null,
                        "pay_term_type": null,
                        "credit_limit": null,
                        "created_by": 1,
                        "balance": "0.0000",
                        "total_rp": 0,
                        "total_rp_used": 0,
                        "total_rp_expired": 0,
                        "is_default": 0,
                        "shipping_address": null,
                        "position": null,
                        "customer_group_id": null,
                        "crm_source": "55",
                        "crm_life_stage": "62",
                        "custom_field1": null,
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null,
                        "custom_field5": null,
                        "custom_field6": null,
                        "custom_field7": null,
                        "custom_field8": null,
                        "custom_field9": null,
                        "custom_field10": null,
                        "deleted_at": null,
                        "created_at": "2020-12-15 23:14:48",
                        "updated_at": "2021-01-07 15:32:52",
                        "remember_token": null,
                        "password": null
                    }
                }
            ],
            "links": {
                "first": "http://local.pos.com/connector/api/crm/follow-ups?page=1",
                "last": "http://local.pos.com/connector/api/crm/follow-ups?page=21",
                "prev": null,
                "next": "http://local.pos.com/connector/api/crm/follow-ups?page=2"
            },
            "meta": {
                "current_page": 1,
                "from": 1,
                "last_page": 21,
                "path": "http://local.pos.com/connector/api/crm/follow-ups",
                "per_page": "2",
                "to": 2,
                "total": 42
            }
        }
     */
    public function index()
    {      
        $user = Auth::user();
        if (!($this->moduleUtil->isModuleInstalled('Crm') && ($user->can('crm.access_all_schedule') || $user->can('crm.access_own_schedule')))) {
            abort(403, 'Unauthorized action.');
        }

        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');

        $query = $this->crmUtil->getFollowUpForGivenDate($user, $start_date, $end_date);

        if (!empty(request()->input('status'))) {
            $query->where('status', request()->input('status'));
        }

        if (!empty(request()->input('follow_up_type'))) {
            $query->where('schedule_type', request()->input('follow_up_type'));
        }

        $order_by = request()->input('order_by');
        $order_by_dir = request()->input('direction');
        if (!empty($order_by) && !empty($order_by_dir)) {
            $query->orderBy($order_by, $order_by_dir);
        }

        $per_page = !empty(request()->input('per_page')) ? request()->input('per_page') : $this->perPage;

        if ($per_page != -1) {
            $follow_ups = $query->paginate($per_page);
            $follow_ups->appends(request()->query());
        } else{
            $follow_ups = $query->get();
        }

        return CommonResource::collection($follow_ups);
    }

    /**
     * Get follow up resources
     *
     * @response {
            "data": {
                "statuses": {
                    "scheduled": "Scheduled",
                    "open": "Open",
                    "canceled": "Cancelled",
                    "completed": "Completed"
                },
                "follow_up_types": {
                    "call": "Call",
                    "sms": "Sms",
                    "meeting": "Meeting",
                    "email": "Email"
                },
                "notify_type": {
                    "minute": "Minute",
                    "hour": "Hour",
                    "day": "Day"
                },
                "notify_via": {
                    "sms": "Sms",
                    "mail": "Email"
                }
            }
        }
     */
    public function getFollowUpResources()
    {   
        if (!$this->moduleUtil->isModuleInstalled('Crm')) {
            abort(403, 'Unauthorized action.');
        }

        $params['statuses'] = \Modules\Crm\Entities\Schedule::statusDropdown();
        $params['follow_up_types'] = \Modules\Crm\Entities\Schedule::followUpTypeDropdown();
        $params['notify_type'] = \Modules\Crm\Entities\Schedule::followUpNotifyTypeDropdown();
        $params['notify_via'] = \Modules\Crm\Entities\Schedule::followUpNotifyViaDropdown();

        return new CommonResource($params);
        
    }

    /**
    * Add follow up
    *
    * @bodyParam title string required Follow up title Example: Meeting with client
    * @bodyParam contact_id integer required Contact to be followed up Example: 2
    * @bodyParam description text Follow up description
    * @bodyParam schedule_type string required Follow up type default get from getFollowUpResources->follow_up_types
    * @bodyParam user_id array required Integer ID; Follow up to be assigned Ex: [2,3,8] Example:[2,3,5]
    * @bodyParam notify_before integer Integer value will be used to send auto notification before follow up starts. Example:5
    * @bodyParam notify_type string Notify type Ex: 'minute', 'hour', 'day'. default is hour Example: minute
    * @bodyParam status string Follow up status Example: open
    * @bodyParam notify_via array Will be used to send notification Ex: ['sms' => 0 ,'mail' => 1] Example: ['sms' => 0 ,'mail' => 1]
    * @bodyParam start_datetime datetime required Follow up start datetime format: Y-m-d H:i:s Ex: 2020-12-16 03:15:23 Example: 2021-01-06 13:05:00
    * @bodyParam end_datetime datetime required Follow up end datetime format: Y-m-d H:i:s Ex: 2020-12-16 03:15:23 Example: 2021-01-06 13:05:00
    * @bodyParam followup_additional_info array Follow up additional info Ex: ['call duration' => '1 hour'] Example:['call duration' => '1 hour']
    * @bodyParam allow_notification boolean 0/1 : If notification will be send before follow up starts. default is 1(true) Example:1
    *
    * @response {
        "data": {
            "title": "test",
            "contact_id": "1",
            "description": null,
            "schedule_type": "call",
            "notify_before": null,
            "status": null,
            "start_datetime": "2021-01-06 15:27:00",
            "end_datetime": "2021-01-06 15:27:00",
            "allow_notification": 0,
            "notify_via": {
                "sms": 1,
                "mail": 1
            },
            "notify_type": "hour",
            "business_id": 1,
            "created_by": 1,
            "updated_at": "2021-01-06 17:04:54",
            "created_at": "2021-01-06 17:04:54",
            "id": 20
        }
    }
    */
    public function store(Request $request)
    { 
        if (!$this->moduleUtil->isModuleInstalled('Crm')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'title' => 'required',
                'contact_id' => 'required',
                'start_datetime' => 'required',
                'end_datetime' => 'required',
                'schedule_type' => 'required',
                'user_id' => 'required',
            ]);

            $params = $request->only(['title', 'contact_id', 'description', 'notify_before',
                        'status','start_datetime', 'end_datetime', 'allow_notification','notify_via',
                        'notify_type', 'schedule_type', 'user_id', 'followup_additional_info'
                    ]);

            DB::beginTransaction();
            
            $schedule = $this->crmUtil->addFollowUp($params, Auth::user());

            DB::commit();

            return new CommonResource($schedule);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->otherExceptions($e);
        }
    }

    /**
     * Get the specified followup
     *
     * @urlParam follow_up required comma separated ids of the follow_ups Example: 1,2
     * @response {
            "data": [
                {
                    "id": 20,
                    "business_id": 1,
                    "contact_id": 1,
                    "title": "Meeting with client",
                    "status": null,
                    "start_datetime": "2021-01-06 15:27:00",
                    "end_datetime": "2021-01-06 15:27:00",
                    "description": null,
                    "schedule_type": "call",
                    "allow_notification": 0,
                    "notify_via": {
                        "sms": 1,
                        "mail": 1
                    },
                    "notify_before": null,
                    "notify_type": "hour",
                    "created_by": 1,
                    "created_at": "2021-01-06 17:04:54",
                    "updated_at": "2021-01-06 17:04:54",
                    "customer": {
                        "id": 1,
                        "business_id": 1,
                        "type": "customer",
                        "supplier_business_name": null,
                        "name": "Walk-In Customer",
                        "prefix": null,
                        "first_name": "Walk-In Customer",
                        "middle_name": null,
                        "last_name": null,
                        "email": null,
                        "contact_id": "CO0005",
                        "contact_status": "active",
                        "tax_number": null,
                        "city": "Phoenix",
                        "state": "Arizona",
                        "country": "USA",
                        "address_line_1": "Linking Street",
                        "address_line_2": null,
                        "zip_code": null,
                        "dob": null,
                        "mobile": "(378) 400-1234",
                        "landline": null,
                        "alternate_number": null,
                        "pay_term_number": null,
                        "pay_term_type": null,
                        "credit_limit": null,
                        "created_by": 1,
                        "balance": "0.0000",
                        "total_rp": 0,
                        "total_rp_used": 0,
                        "total_rp_expired": 0,
                        "is_default": 1,
                        "shipping_address": null,
                        "position": null,
                        "customer_group_id": null,
                        "crm_source": null,
                        "crm_life_stage": null,
                        "custom_field1": null,
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null,
                        "custom_field5": null,
                        "custom_field6": null,
                        "custom_field7": null,
                        "custom_field8": null,
                        "custom_field9": null,
                        "custom_field10": null,
                        "deleted_at": null,
                        "created_at": "2018-01-03 20:45:20",
                        "updated_at": "2018-06-11 22:22:05",
                        "remember_token": null,
                        "password": null
                    },
                    "users": [
                        {
                            "id": 2,
                            "user_type": "user",
                            "surname": "Mr",
                            "first_name": "Demo",
                            "last_name": "Cashier",
                            "username": "cashier",
                            "email": "cashier@example.com",
                            "language": "en",
                            "contact_no": null,
                            "address": null,
                            "business_id": 1,
                            "max_sales_discount_percent": null,
                            "allow_login": 1,
                            "essentials_department_id": null,
                            "essentials_designation_id": null,
                            "status": "active",
                            "crm_contact_id": null,
                            "is_cmmsn_agnt": 0,
                            "cmmsn_percent": "0.00",
                            "selected_contacts": 0,
                            "dob": null,
                            "gender": null,
                            "marital_status": null,
                            "blood_group": null,
                            "contact_number": null,
                            "fb_link": null,
                            "twitter_link": null,
                            "social_media_1": null,
                            "social_media_2": null,
                            "permanent_address": null,
                            "current_address": null,
                            "guardian_name": null,
                            "custom_field_1": null,
                            "custom_field_2": null,
                            "custom_field_3": null,
                            "custom_field_4": null,
                            "bank_details": null,
                            "id_proof_name": null,
                            "id_proof_number": null,
                            "deleted_at": null,
                            "created_at": "2018-01-04 02:20:58",
                            "updated_at": "2018-01-04 02:20:58",
                            "pivot": {
                                "schedule_id": 20,
                                "user_id": 2
                            }
                        }
                    ]
                }
            ]
        }
     */
    public function show($follow_up_ids)
    {   
        $user = Auth::user();
        if (!($this->moduleUtil->isModuleInstalled('Crm') && ($user->can('crm.access_all_schedule') || $user->can('crm.access_own_schedule')))) {
            abort(403, 'Unauthorized action.');
        }

        $follow_up_ids = explode(',', $follow_up_ids);

        $query = \Modules\Crm\Entities\Schedule::with(['customer', 'users'])
                        ->where('business_id', $user->business_id)
                        ->whereIn('id', $follow_up_ids);

        if (!$user->can('crm.access_all_schedule') && $user->can('crm.access_own_schedule')) {
            $query->where(function($qry) use ($user) {
                $qry->whereHas('users', function($q){
                    //$q->where('user_id', $user->id);
                })->orWhere('created_by', $user->id);
            });
        }

        $follow_ups = $query->get();

        return CommonResource::collection($follow_ups);
    }

    /**
    * Update follow up
    *
    * @urlParam follow_up required id of the follow up to be updated Example: 20
    * @bodyParam title string required Follow up title Example: Meeting with client
    * @bodyParam contact_id integer required Contact to be followed up Example: 2
    * @bodyParam description text Follow up description
    * @bodyParam schedule_type string required Follow up type default get from getFollowUpResources->follow_up_types
    * @bodyParam user_id array required Integer ID; Follow up to be assigned Ex: [2,3,8] Example:[2,3,5]
    * @bodyParam notify_before integer Integer value will be used to send auto notification before follow up starts. Example:5
    * @bodyParam notify_type string Notify type Ex: 'minute', 'hour', 'day'. default is hour Example: minute
    * @bodyParam status string Follow up status Example: open
    * @bodyParam notify_via array Will be used to send notification Ex: ['sms' => 0 ,'mail' => 1] Example: ['sms' => 0 ,'mail' => 1]
    * @bodyParam followup_additional_info array Follow up additional info Ex: ['call duration' => '1 hour'] Example:['call duration' => '1 hour']
    * @bodyParam start_datetime datetime required Follow up start datetime format: Y-m-d H:i:s Ex: 2020-12-16 03:15:23 Example: 2021-01-06 13:05:00
    * @bodyParam end_datetime datetime required Follow up end datetime format: Y-m-d H:i:s Ex: 2020-12-16 03:15:23 Example: 2021-01-06 13:05:00
    * @bodyParam allow_notification boolean 0/1 : If notification will be send before follow up starts. default is 1(true) Example:1
    *
    * @response {
        "data": {
        "id": 20,
        "business_id": 1,
        "contact_id": "1",
        "title": "Meeting with client",
        "status": null,
        "start_datetime": "2021-01-06 15:27:00",
        "end_datetime": "2021-01-06 15:27:00",
        "description": null,
        "schedule_type": "call",
        "allow_notification": 0,
        "notify_via": {
            "sms": 1,
            "mail": 0
        },
        "notify_before": null,
        "notify_type": "hour",
        "created_by": 1,
        "created_at": "2021-01-06 17:04:54",
        "updated_at": "2021-01-06 18:22:21"
    }
    }
    */
    public function update(Request $request, $follow_up_id)
    {
        $user = Auth::user();
        if (!($this->moduleUtil->isModuleInstalled('Crm') && ($user->can('crm.access_all_schedule') || $user->can('crm.access_own_schedule')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'title' => 'required',
                'contact_id' => 'required',
                'start_datetime' => 'required',
                'end_datetime' => 'required',
                'schedule_type' => 'required',
                'user_id' => 'required',
            ]);
            
            $params = $request->only(['title', 'contact_id', 'description', 'notify_before',
                        'status','start_datetime', 'end_datetime', 'allow_notification','notify_via',
                        'notify_type', 'schedule_type', 'user_id', 'followup_additional_info'
                    ]);
            

            DB::beginTransaction();

            $schedule = $this->crmUtil->updateFollowUp($follow_up_id, $params, Auth::user());

            DB::commit();
            return new CommonResource($schedule);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->otherExceptions($e);
        }
    }

    /**
     * List lead
     * @queryParam assigned_to comma separated ids of users to whom lead is assigned (Ex: 1,2,3) Example: 1,2,3
     * @queryParam name Search term for lead name
     * @queryParam biz_name Search term for lead's business name
     * @queryParam mobile_num Search term for lead's mobile number
     * @queryParam contact_id Search term for lead's contact_id. Ex(CO0005)
     * @queryParam order_by Column name to sort the result, Column: name, supplier_business_name
     * @queryParam direction Direction to sort the result, Required if using 'order_by', direction: desc, asc Example:desc
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     * @response {
            "data": [
                {
                    "contact_id": "CO0010",
                    "name": "mr Lead 3 kr kr 2",
                    "supplier_business_name": "POS",
                    "email": null,
                    "mobile": "9437638555",
                    "tax_number": null,
                    "created_at": "2020-12-15 23:14:30",
                    "custom_field1": null,
                    "custom_field2": null,
                    "custom_field3": null,
                    "custom_field4": null,
                    "custom_field5": null,
                    "custom_field6": null,
                    "alternate_number": null,
                    "landline": null,
                    "dob": null,
                    "contact_status": "active",
                    "type": "lead",
                    "custom_field7": null,
                    "custom_field8": null,
                    "custom_field9": null,
                    "custom_field10": null,
                    "id": 49,
                    "business_id": 1,
                    "crm_source": "55",
                    "crm_life_stage": "60",
                    "address_line_1": null,
                    "address_line_2": null,
                    "city": null,
                    "state": null,
                    "country": null,
                    "zip_code": null,
                    "last_follow_up_id": 18,
                    "upcoming_follow_up_id": null,
                    "last_follow_up": "2021-01-07 10:26:00",
                    "upcoming_follow_up": null,
                    "last_follow_up_additional_info": "{\"test\":\"test done\",\"call_duration\":\"1.5 Hour\",\"rand\":1}",
                    "upcoming_follow_up_additional_info": null,
                    "source": {
                        "id": 55,
                        "name": "Facebook",
                        "business_id": 1,
                        "short_code": null,
                        "parent_id": 0,
                        "created_by": 1,
                        "category_type": "source",
                        "description": "Facebook",
                        "slug": null,
                        "woocommerce_cat_id": null,
                        "deleted_at": null,
                        "created_at": "2020-12-15 23:07:53",
                        "updated_at": "2020-12-15 23:07:53"
                    },
                    "life_stage": {
                        "id": 60,
                        "name": "Open Deal",
                        "business_id": 1,
                        "short_code": null,
                        "parent_id": 0,
                        "created_by": 1,
                        "category_type": "life_stage",
                        "description": "Open Deal",
                        "slug": null,
                        "woocommerce_cat_id": null,
                        "deleted_at": null,
                        "created_at": "2020-12-15 23:11:05",
                        "updated_at": "2020-12-15 23:11:05"
                    },
                    "lead_users": [
                        {
                            "id": 10,
                            "user_type": "user",
                            "surname": "Mr.",
                            "first_name": "WooCommerce",
                            "last_name": "User",
                            "username": "woocommerce_user",
                            "email": "woo@example.com",
                            "language": "en",
                            "contact_no": null,
                            "address": null,
                            "business_id": 1,
                            "max_sales_discount_percent": null,
                            "allow_login": 1,
                            "essentials_department_id": null,
                            "essentials_designation_id": null,
                            "status": "active",
                            "crm_contact_id": null,
                            "is_cmmsn_agnt": 0,
                            "cmmsn_percent": "0.00",
                            "selected_contacts": 0,
                            "dob": null,
                            "gender": null,
                            "marital_status": null,
                            "blood_group": null,
                            "contact_number": null,
                            "fb_link": null,
                            "twitter_link": null,
                            "social_media_1": null,
                            "social_media_2": null,
                            "permanent_address": null,
                            "current_address": null,
                            "guardian_name": null,
                            "custom_field_1": null,
                            "custom_field_2": null,
                            "custom_field_3": null,
                            "custom_field_4": null,
                            "bank_details": null,
                            "id_proof_name": null,
                            "id_proof_number": null,
                            "deleted_at": null,
                            "created_at": "2018-08-02 04:05:55",
                            "updated_at": "2018-08-02 04:05:55",
                            "pivot": {
                                "contact_id": 49,
                                "user_id": 10
                            }
                        }
                    ]
                },
                {
                    "contact_id": "CO0011",
                    "name": " Lead 4  ",
                    "supplier_business_name": null,
                    "email": null,
                    "mobile": "234567",
                    "tax_number": null,
                    "created_at": "2020-12-15 23:14:48",
                    "custom_field1": null,
                    "custom_field2": null,
                    "custom_field3": null,
                    "custom_field4": null,
                    "custom_field5": null,
                    "custom_field6": null,
                    "alternate_number": null,
                    "landline": null,
                    "dob": null,
                    "contact_status": "active",
                    "type": "lead",
                    "custom_field7": null,
                    "custom_field8": null,
                    "custom_field9": null,
                    "custom_field10": null,
                    "id": 50,
                    "business_id": 1,
                    "crm_source": "55",
                    "crm_life_stage": "62",
                    "address_line_1": null,
                    "address_line_2": null,
                    "city": null,
                    "state": null,
                    "country": null,
                    "zip_code": null,
                    "last_follow_up_id": 32,
                    "upcoming_follow_up_id": null,
                    "last_follow_up": "2021-01-08 16:06:00",
                    "upcoming_follow_up": null,
                    "last_follow_up_additional_info": "{\"call_durartion\":\"5 hour\"}",
                    "upcoming_follow_up_additional_info": null,
                    "source": {
                        "id": 55,
                        "name": "Facebook",
                        "business_id": 1,
                        "short_code": null,
                        "parent_id": 0,
                        "created_by": 1,
                        "category_type": "source",
                        "description": "Facebook",
                        "slug": null,
                        "woocommerce_cat_id": null,
                        "deleted_at": null,
                        "created_at": "2020-12-15 23:07:53",
                        "updated_at": "2020-12-15 23:07:53"
                    },
                    "life_stage": {
                        "id": 62,
                        "name": "New",
                        "business_id": 1,
                        "short_code": null,
                        "parent_id": 0,
                        "created_by": 1,
                        "category_type": "life_stage",
                        "description": "New",
                        "slug": null,
                        "woocommerce_cat_id": null,
                        "deleted_at": null,
                        "created_at": "2020-12-15 23:11:26",
                        "updated_at": "2020-12-15 23:11:26"
                    },
                    "lead_users": [
                        {
                            "id": 11,
                            "user_type": "user",
                            "surname": "Mr",
                            "first_name": "Admin Essential",
                            "last_name": null,
                            "username": "admin-essentials",
                            "email": "admin_essentials@example.com",
                            "language": "en",
                            "contact_no": null,
                            "address": null,
                            "business_id": 1,
                            "max_sales_discount_percent": null,
                            "allow_login": 1,
                            "essentials_department_id": null,
                            "essentials_designation_id": null,
                            "status": "active",
                            "crm_contact_id": null,
                            "is_cmmsn_agnt": 0,
                            "cmmsn_percent": "0.00",
                            "selected_contacts": 0,
                            "dob": null,
                            "gender": null,
                            "marital_status": null,
                            "blood_group": null,
                            "contact_number": null,
                            "fb_link": null,
                            "twitter_link": null,
                            "social_media_1": null,
                            "social_media_2": null,
                            "permanent_address": null,
                            "current_address": null,
                            "guardian_name": null,
                            "custom_field_1": null,
                            "custom_field_2": null,
                            "custom_field_3": null,
                            "custom_field_4": null,
                            "bank_details": null,
                            "id_proof_name": null,
                            "id_proof_number": null,
                            "deleted_at": null,
                            "created_at": "2018-01-04 02:15:19",
                            "updated_at": "2018-01-04 02:15:19",
                            "pivot": {
                                "contact_id": 50,
                                "user_id": 11
                            }
                        }
                    ]
                },
                {
                    "contact_id": "CO0015",
                    "name": " Lead kr  ",
                    "supplier_business_name": null,
                    "email": null,
                    "mobile": "9437638555",
                    "tax_number": null,
                    "created_at": "2021-01-07 18:31:08",
                    "custom_field1": null,
                    "custom_field2": null,
                    "custom_field3": null,
                    "custom_field4": null,
                    "custom_field5": null,
                    "custom_field6": null,
                    "alternate_number": null,
                    "landline": null,
                    "dob": "2021-01-07",
                    "contact_status": "active",
                    "type": "lead",
                    "custom_field7": null,
                    "custom_field8": null,
                    "custom_field9": null,
                    "custom_field10": null,
                    "id": 82,
                    "business_id": 1,
                    "crm_source": null,
                    "crm_life_stage": null,
                    "address_line_1": null,
                    "address_line_2": null,
                    "city": null,
                    "state": null,
                    "country": null,
                    "zip_code": null,
                    "last_follow_up_id": 36,
                    "upcoming_follow_up_id": null,
                    "last_follow_up": "2021-01-07 18:31:08",
                    "upcoming_follow_up": null,
                    "last_follow_up_additional_info": "{\"call duration\":\"1 hour\",\"call descr\":\"talked to him and all okay\"}",
                    "upcoming_follow_up_additional_info": null,
                    "source": null,
                    "life_stage": null,
                    "lead_users": [
                        {
                            "id": 11,
                            "user_type": "user",
                            "surname": "Mr",
                            "first_name": "Admin Essential",
                            "last_name": null,
                            "username": "admin-essentials",
                            "email": "admin_essentials@example.com",
                            "language": "en",
                            "contact_no": null,
                            "address": null,
                            "business_id": 1,
                            "max_sales_discount_percent": null,
                            "allow_login": 1,
                            "essentials_department_id": null,
                            "essentials_designation_id": null,
                            "status": "active",
                            "crm_contact_id": null,
                            "is_cmmsn_agnt": 0,
                            "cmmsn_percent": "0.00",
                            "selected_contacts": 0,
                            "dob": null,
                            "gender": null,
                            "marital_status": null,
                            "blood_group": null,
                            "contact_number": null,
                            "fb_link": null,
                            "twitter_link": null,
                            "social_media_1": null,
                            "social_media_2": null,
                            "permanent_address": null,
                            "current_address": null,
                            "guardian_name": null,
                            "custom_field_1": null,
                            "custom_field_2": null,
                            "custom_field_3": null,
                            "custom_field_4": null,
                            "bank_details": null,
                            "id_proof_name": null,
                            "id_proof_number": null,
                            "deleted_at": null,
                            "created_at": "2018-01-04 02:15:19",
                            "updated_at": "2018-01-04 02:15:19",
                            "pivot": {
                                "contact_id": 82,
                                "user_id": 11
                            }
                        }
                    ]
                }
            ],
            "links": {
                "first": "http://local.pos.com/connector/api/crm/leads?page=1",
                "last": "http://local.pos.com/connector/api/crm/leads?page=1",
                "prev": null,
                "next": null
            },
            "meta": {
                "current_page": 1,
                "from": 1,
                "last_page": 1,
                "path": "http://local.pos.com/connector/api/crm/leads",
                "per_page": "10",
                "to": 3,
                "total": 3
            }
        }
    */
    public function getLeads()
    {   
        $user = Auth::user();
        $query = $this->crmUtil->getLeadsListQuery($user->business_id);

        $can_access_all_leads = $user->can('crm.access_all_leads');
        $can_access_own_leads = $user->can('crm.access_own_leads');

        if (!empty(request()->input('assigned_to'))) {
            $assigned_to = explode(',', request()->input('assigned_to'));
            $query->where( function($query) use($assigned_to) {
                $query->whereHas('leadUsers', function($q) use($assigned_to) {
                    $q->whereIn('user_id', $assigned_to);
                });
            });
        }

        //If can access only own leads
        if (!$can_access_all_leads && $can_access_own_leads) {
            $query->where( function($q) use ($user) {
                $q->whereHas('leadUsers', function($qu) use ($user){
                    $qu->where('user_id', $user->id);
                });
            });
        }

        $search = request()->only(['name', 'biz_name', 'mobile_num', 'contact_id']);
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {

                if (!empty($search['name'])) {
                    $query->where('contacts.name', 'like', '%' . $search['name'] .'%');
                }
                
                if (!empty($search['biz_name'])) {
                    $query->orWhere('contacts.supplier_business_name', 'like', '%' . $search['biz_name'] .'%');
                }

                if (!empty($search['mobile_num'])) {
                    $query->orWhere('contacts.mobile', 'like', '%' . $search['mobile_num'] .'%')
                        ->orWhere('contacts.landline', 'like', '%' . $search['mobile_num'] .'%')
                        ->orWhere('contacts.alternate_number', 'like', '%' . $search['mobile_num'] .'%');
                }

                if (!empty($search['contact_id'])) {
                    $query->orWhere('contacts.contact_id', 'like', '%' . $search['contact_id'] .'%');
                }

            });
        }

        $order_by = request()->input('order_by');
        $order_by_dir = request()->input('direction'); 
        if (!empty($order_by) && !empty($order_by_dir)) {
            $query->orderBy($order_by, $order_by_dir);
        }

        $per_page = !empty(request()->input('per_page')) ? request()->input('per_page') : $this->perPage;
        
        if ($per_page != -1) {
            $leads = $query->paginate($per_page);
            $leads->appends(request()->query());
        } else{
            $leads = $query->get();
        }

        return CommonResource::collection($leads);
    }
}
