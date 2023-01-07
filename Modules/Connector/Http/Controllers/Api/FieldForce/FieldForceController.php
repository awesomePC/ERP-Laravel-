<?php

namespace Modules\Connector\Http\Controllers\Api\FieldForce;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\FieldForce\Entities\FieldForce;
use App\Media;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use App\Utils\ModuleUtil;
use Modules\Connector\Http\Controllers\Api\ApiController;

/**
 * @group Field Force
 * @authenticated
 *
 * APIs for managing field forces
 */
class FieldForceController extends ApiController
{

    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param CommonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * List visits
     * @queryParam contact_id id of the contact        
     * @queryParam assigned_to id of the assigned user
     * @queryParam status status of the visit (assigned, finished)
     * @queryParam start_date Start date filter for visit on format:Y-m-d Example: 2018-06-25
     * @queryParam end_date End date filter for visit on format:Y-m-d Example: 2018-06-25
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:15
     * @queryParam order_by_date Sort visit by visit on date ('asc', 'desc') Example: desc
     * 
     *  @response {
        "data": [
            {
                "id": 7,
                "visit_id": "2022/0161",
                "business_id": 1,
                "contact_id": null,
                "visit_to": "from api",
                "visit_address": "asdddd",
                "assigned_to": 9,
                "visited_address": null,
                "status": "assigned",
                "visit_on": "2022-01-16 17:23:00",
                "visited_on": null,
                "visit_for": "test from api new",
                "comments": null,
                "reason_to_not_meet_contact": null,
                "created_at": "2022-01-08 12:01:02",
                "updated_at": "2022-01-08 12:01:02",
                "contact": null,
                "user": {
                    "id": 9,
                    "user_type": "user",
                    "surname": "Mr.",
                    "first_name": "Super",
                    "last_name": "Admin",
                    "username": "superadmin",
                    "email": "superadmin@example.com",
                    "language": "en",
                    "contact_no": null,
                    "address": null,
                    "business_id": 1,
                    "max_sales_discount_percent": null,
                    "allow_login": 1,
                    "essentials_department_id": null,
                    "essentials_designation_id": null,
                    "essentials_salary": null,
                    "essentials_pay_period": "month",
                    "essentials_pay_cycle": null,
                    "status": "active",
                    "crm_contact_id": null,
                    "is_cmmsn_agnt": 0,
                    "cmmsn_percent": "5.00",
                    "selected_contacts": 0,
                    "dob": null,
                    "gender": null,
                    "marital_status": null,
                    "blood_group": null,
                    "contact_number": null,
                    "alt_number": null,
                    "family_number": null,
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
                    "bank_details": "{\"account_holder_name\":null,\"account_number\":null,\"bank_name\":null,\"bank_code\":null,\"branch\":null,\"tax_payer_id\":null}",
                    "id_proof_name": null,
                    "id_proof_number": null,
                    "location_id": null,
                    "crm_department": null,
                    "crm_designation": null,
                    "deleted_at": null,
                    "created_at": "2018-08-02 04:05:55",
                    "updated_at": "2021-12-15 18:16:51"
                }
            },
            {
                "id": 8,
                "visit_id": "2022/0017",
                "business_id": 1,
                "contact_id": 1,
                "visit_to": null,
                "visit_address": null,
                "assigned_to": 2,
                "visited_address": null,
                "status": "assigned",
                "visit_on": "2022-01-08 12:14:00",
                "visited_on": null,
                "visit_for": "efer 3er3",
                "comments": null,
                "reason_to_not_meet_contact": null,
                "created_at": "2022-01-08 12:15:04",
                "updated_at": "2022-01-08 12:15:04",
                "contact": {
                    "id": 1,
                    "business_id": 1,
                    "type": "customer",
                    "supplier_business_name": "test  biz",
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
                    "converted_by": null,
                    "converted_on": null,
                    "balance": "3728.0500",
                    "total_rp": 0,
                    "total_rp_used": 0,
                    "total_rp_expired": 0,
                    "is_default": 1,
                    "shipping_address": null,
                    "shipping_custom_field_details": null,
                    "is_export": 0,
                    "export_custom_field_1": null,
                    "export_custom_field_2": null,
                    "export_custom_field_3": null,
                    "export_custom_field_4": null,
                    "export_custom_field_5": null,
                    "export_custom_field_6": null,
                    "position": null,
                    "customer_group_id": null,
                    "crm_source": null,
                    "crm_life_stage": null,
                    "custom_field1": "cu f 1",
                    "custom_field2": "cf 2",
                    "custom_field3": "cf 3",
                    "custom_field4": null,
                    "custom_field5": null,
                    "custom_field6": null,
                    "custom_field7": null,
                    "custom_field8": null,
                    "custom_field9": null,
                    "custom_field10": null,
                    "deleted_at": null,
                    "created_at": "2018-01-03 20:45:20",
                    "updated_at": "2021-12-13 18:57:15",
                    "remember_token": null,
                    "password": null
                },
                "user": {
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
                    "essentials_salary": null,
                    "essentials_pay_period": "month",
                    "essentials_pay_cycle": null,
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
                    "alt_number": null,
                    "family_number": null,
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
                    "bank_details": "{\"account_holder_name\":null,\"account_number\":null,\"bank_name\":null,\"bank_code\":null,\"branch\":null,\"tax_payer_id\":null}",
                    "id_proof_name": null,
                    "id_proof_number": null,
                    "location_id": null,
                    "crm_department": null,
                    "crm_designation": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:20:58",
                    "updated_at": "2021-11-29 17:42:42"
                }
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/field-force?page=1",
            "last": "http://local.pos.com/connector/api/field-force?page=1",
            "prev": null,
            "next": null
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 1,
            "path": "http://local.pos.com/connector/api/field-force",
            "per_page": 15,
            "to": 2,
            "total": 2
        }
    }
     */
    public function index()
    {
        if (!$this->moduleUtil->isModuleInstalled('FieldForce')) {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();

        $business_id = $user->business_id;

        $filters = request()->only(['contact_id', 'assigned_to', 'status', 'start_date', 'end_date', 'per_page', 'order_by_date']);
        
        $query = FieldForce::with(['contact', 'user'])->where('business_id', $business_id);

        if (!auth()->user()->can('visit.view_all') && auth()->user()->can('visit.view_own')) {
            $query->where('field_forces.assigned_to', $user->id);
        }

        if (!empty($filters['contact_id'])) {
            $query->where('contact_id', $filters['contact_id']);
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('visit_on', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('visit_on', '<=', $filters['end_date']);
        }

        if (!empty($filters['order_by_date'])) {
            $query->orderBy('visit_on', $filters['order_by_date']);
        }

        $perPage = !empty($filters['per_page']) ? $filters['per_page'] : $this->perPage;
        if ($perPage == -1) {
            $visits = $query->get();
        } else {
            $visits = $query->paginate($perPage);
            $visits->appends(request()->query());
        }

        return CommonResource::collection($visits);
    }

    
    /**
     * Create Visit
     * 
     * @bodyParam contact_id integer id of the contact
     * @bodyParam visit_to string Name of the visiting person or company if contact_id is not given
     * @bodyParam visit_address string Address of the visiting person or company if contact_id is not given
     * @bodyParam assigned_to integer required id of the assigned user
     * @bodyParam visit_on format:Y-m-d H:i:s Example: 2021-12-28 17:23:00
     * @bodyParam visit_for string Purpose of visiting
     *
     * @response {
            "data": {
                "contact_id": "6",
                "assigned_to": "9",
                "visit_on": "2022-01-15 17:23:00",
                "visit_for": "",
                "visit_id": "2021/0031",
                "status": "assigned",
                "business_id": 1,
                "updated_at": "2021-12-30 11:00:47",
                "created_at": "2021-12-30 11:00:47",
                "id": 3
            }
        }
     */
    public function store(Request $request)
    {
        if (!$this->moduleUtil->isModuleInstalled('FieldForce')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $user = Auth::user();

            if (!$user->can('visit.create')) {
                abort(403, 'Unauthorized action.');
            }

            $business_id = $user->business_id;

            $input = $request->only('contact_id', 'assigned_to', 'visit_on', 'visit_for');

            if (empty($input['visit_on'])) {
                $input['visit_on'] = \Carbon::now()->format('Y-m-d H:i:s');
            }

            if (empty($input['contact_id'])) {
                $input['visit_to'] = $request->input('visit_to');
                $input['visit_address'] = $request->input('visit_address');
            }

            $ref_count = $this->moduleUtil->setAndGetReferenceCount('field_force', $business_id);
            //Generate reference number
            $input['visit_id'] = $this->moduleUtil->generateReferenceNumber('field_force', $ref_count. $business_id);

            $input['status'] = 'assigned';
            $input['business_id'] = $business_id;

            $visit = FieldForce::create($input);

            return new CommonResource($visit);
            
        } catch (\Exception $e) {
           \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return $this->otherExceptions($e); 
        }
    }

    /**
     * Update Visit status
     * 
     * @urlParam id required id of the visit to be updated Example: 17
     * @bodyParam status string Current status of the visit (assigned, finished, met_contact, did_not_meet_contact) Example: finished
     * @bodyParam reason_to_not_meet_contact string Reason if status is did_not_meet_contact
     * @bodyParam visited_on format:Y-m-d H:i:s Example: 2021-12-28 17:23:00
     * @bodyParam visited_address string Full address of the contact Example: Radhanath Mullick Ln, Tiretta Bazaar, Bow Bazaar, Kolkata, West Bengal, 700 073, India
     * @bodyParam latitude decimal Lattitude of the user location if full address is not given Example: 41.40338
     * @bodyParam longitude decimal Longitude of the user location if full address is not given Example: 2.17403
     * @bodyParam comments string Extra comments
     * @bodyParam photo file Upload Photo as a file of the visit if any or base64 encoded image
     *
     * @response {
        "data": {
            "id": 10,
            "business_id": 1,
            "contact_id": 6,
            "assigned_to": 9,
            "visited_address": "New address",
            "status": "finished",
            "visit_on": "2021-12-28 17:23:00",
            "visit_for": "assigned from api",
            "comments": "Users comment",
            "created_at": "2021-12-28 17:35:13",
            "updated_at": "2021-12-28 18:06:03"
        }
    }
    */
    public function updateStatus(Request $request, $id)
    {
        if (!$this->moduleUtil->isModuleInstalled('FieldForce')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user = Auth::user();
            $business_id = $user->business_id;

            $input = $request->only('status', 'visited_on', 'visited_address', 'comments');

            $visit = FieldForce::find($id);

            if ($user->id != $visit->assigned_to) {
                abort(403, 'Unauthorized action.');
            }

            if (!empty($input['visited_on'])) {
                $visit->visited_on = $input['visited_on'];
            }

            if (!empty($input['status'])) {
                $visit->status = $input['status'];
            }

            if (!empty($input['visited_address'])) {
                $visit->visited_address = $input['visited_address'];
            }

            if (!empty($request->input('latitude')) && !empty($request->input('longitude'))) {
                $response = $this->moduleUtil->getLocationFromCoordinates($request->input('latitude'), $request->input('longitude'));
                if (!empty($response['results'][0]['formatted_address'])) {
                    $visit->visited_address = $response['results'][0]['formatted_address'];
                } elseif (!empty($response['formatted_address'])) {
                    $visit->visited_address = $response['formatted_address'];
                }
            }

            if (!empty($input['comments'])) {
                $visit->comments = $input['comments'];
            }

            if (!empty($request->input('reason_to_not_meet_contact')) && $visit->status == 'did_not_meet_contact') {
                $visit->reason_to_not_meet_contact = $request->input('reason_to_not_meet_contact');
            }

            $visit->save();

            Media::uploadMedia($business_id, $visit, $request, 'photo', true);

            return new CommonResource($visit);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return $this->otherExceptions($e);
        }
    }
}
