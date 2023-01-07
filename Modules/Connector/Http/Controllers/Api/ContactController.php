<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use App\Contact;
use App\Utils\ContactUtil;
use Illuminate\Support\Facades\DB;
use App\Utils\TransactionUtil;

/**
 * @group Contact management
 * @authenticated
 *
 * APIs for managing contacts
 */
class ContactController extends ApiController
{
    protected $contactUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ContactUtil $contactUtil
     * @return void
     */
    public function __construct(
        ContactUtil $contactUtil,
        TransactionUtil $transactionUtil
    ) {
        $this->contactUtil = $contactUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * List contact
     *
     * @queryParam type required Type of contact (supplier, customer)
     * @queryParam name Search term for contact name
     * @queryParam biz_name Search term for contact's business name
     * @queryParam mobile_num Search term for contact's mobile number
     * @queryParam contact_id Search term for contact's contact_id. Ex(CO0005)
     * @queryParam order_by Column name to sort the result, Column: name, supplier_business_name
     * @queryParam direction Direction to sort the result, Direction: desc, asc 
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     * @response {
            "data": [
                {
                    "id": 2,
                    "business_id": 1,
                    "type": "supplier",
                    "supplier_business_name": "Alpha Clothings",
                    "name": "Michael",
                    "prefix": null,
                    "first_name": "Michael",
                    "middle_name": null,
                    "last_name": null,
                    "email": null,
                    "contact_id": "CO0001",
                    "contact_status": "active",
                    "tax_number": "4590091535",
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
                    "pay_term_number": 15,
                    "pay_term_type": "days",
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
                    "created_at": "2018-01-03 20:59:38",
                    "updated_at": "2018-06-11 22:21:03",
                    "remember_token": null,
                    "password": null
                },
                {
                    "id": 3,
                    "business_id": 1,
                    "type": "supplier",
                    "supplier_business_name": "Manhattan Clothing Ltd.",
                    "name": "Philip",
                    "prefix": null,
                    "first_name": "Philip",
                    "middle_name": null,
                    "last_name": null,
                    "email": null,
                    "contact_id": "CO0003",
                    "contact_status": "active",
                    "tax_number": "54869310093",
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
                    "pay_term_number": 15,
                    "pay_term_type": "days",
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
                    "created_at": "2018-01-03 21:00:55",
                    "updated_at": "2018-06-11 22:21:36",
                    "remember_token": null,
                    "password": null
                },
                {
                    "id": 5,
                    "business_id": 1,
                    "type": "supplier",
                    "supplier_business_name": "Digital Ocean",
                    "name": "Mike McCubbin",
                    "prefix": null,
                    "first_name": "Mike McCubbin",
                    "middle_name": null,
                    "last_name": null,
                    "email": null,
                    "contact_id": "CN0004",
                    "contact_status": "active",
                    "tax_number": "52965489001",
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
                    "pay_term_number": 30,
                    "pay_term_type": "days",
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
                    "created_at": "2018-01-06 06:53:22",
                    "updated_at": "2018-06-11 22:21:47",
                    "remember_token": null,
                    "password": null
                },
                {
                    "id": 6,
                    "business_id": 1,
                    "type": "supplier",
                    "supplier_business_name": "Univer Suppliers",
                    "name": "Jackson Hill",
                    "prefix": null,
                    "first_name": "Jackson Hill",
                    "middle_name": null,
                    "last_name": null,
                    "email": null,
                    "contact_id": "CO0002",
                    "contact_status": "active",
                    "tax_number": "5459000655",
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
                    "pay_term_number": 45,
                    "pay_term_type": "days",
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
                    "created_at": "2018-01-06 06:55:09",
                    "updated_at": "2018-06-11 22:21:18",
                    "remember_token": null,
                    "password": null
                }
            ],
            "links": {
                "first": "http://local.pos.com/connector/api/contactapi?page=1",
                "last": "http://local.pos.com/connector/api/contactapi?page=1",
                "prev": null,
                "next": null
            },
            "meta": {
                "current_page": 1,
                "from": 1,
                "last_page": 1,
                "path": "http://local.pos.com/connector/api/contactapi",
                "per_page": "10",
                "to": 4,
                "total": 4
            }
        }
    */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $type = request()->get('type', null);

        $query = Contact::where('business_id', $business_id);
        
        if($type == 'supplier'){
            $query->onlySuppliers();
        }

        if($type == 'customer'){
            $query->onlyCustomers();
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
            $contacts = $query->paginate($per_page);
            $contacts->appends(request()->query());
            
        } else{
            $contacts = $query->get();
        }

        return CommonResource::collection($contacts);
    }

    /**
    * Create contact
    *
    * @bodyParam type string required Type of contact (supplier, customer, both, lead) Example: customer
    * @bodyParam supplier_business_name string required Required if type is supplier
    * @bodyParam prefix string Prefix for the name of the contact
    * @bodyParam first_name string required Name of the contact
    * @bodyParam middle_name string
    * @bodyParam last_name string
    * @bodyParam tax_number string Example:8787fefef
    * @bodyParam pay_term_number float Example: 3
    * @bodyParam pay_term_type string (months ,days) Example: months
    * @bodyParam mobile string required Example:4578691009
    * @bodyParam landline string Example:5487-8454-4145 
    * @bodyParam alternate_number string Example: 841847541222 
    * @bodyParam address_line_1 string
    * @bodyParam address_line_2 string
    * @bodyParam city string  
    * @bodyParam state string 
    * @bodyParam country string
    * @bodyParam zip_code string    
    * @bodyParam customer_group_id string  
    * @bodyParam contact_id string  
    * @bodyParam dob string Fromat: Y-m-d Example:2000-06-13
    * @bodyParam custom_field1 string  
    * @bodyParam custom_field2 string  
    * @bodyParam custom_field3 string  
    * @bodyParam custom_field4 string  
    * @bodyParam email string  
    * @bodyParam shipping_address string  
    * @bodyParam position string  
    * @bodyParam opening_balance float Example: 0.0000
    * @bodyParam source_id integer Id of the source. Applicable only if the type is lead 
    * @bodyParam life_stage_id integer Id of the Life stage. Applicable only if the type is lead
    * @bodyParam assigned_to array Ids of the users the lead is assigned to. Applicable only if the type is lead
    *
    * @response {
        "data": {
            "type": "customer",
            "name": "test customer",
            "tax_number": "75879BHF",
            "mobile": "7878825008",
            "business_id": 1,
            "created_by": 9,
            "credit_limit": null,
            "contact_id": "CO0007",
            "updated_at": "2020-06-04 21:59:21",
            "created_at": "2020-06-04 21:59:21",
            "id": 17
        }
    }
    */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required',
                'type' => 'required|in:customer,supplier,both,lead',
                'mobile' => 'required',
                'supplier_business_name' => 'required_if:type,supplier'
            ]);

            $input = $request->only(['type', 'supplier_business_name',
                'prefix', 'first_name', 'middle_name', 'last_name', 'tax_number', 'pay_term_number', 'pay_term_type', 'mobile', 'landline', 'alternate_number', 'city', 'state', 'country', 'address_line_1', 'address_line_2', 'customer_group_id', 'zip_code', 'contact_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email', 'shipping_address', 'position', 'dob']);
            $input['business_id'] = Auth::user()->business_id;
            $input['created_by'] = Auth::user()->id;
            $input['credit_limit'] = $request->input('credit_limit') != '' ? $request->input('credit_limit') : null;

            if (!empty($input['prefix'])) {
                $name_array[] = $input['prefix'];
            }
            $name_array[] = $input['first_name'];
            if (!empty($input['middle_name'])) {
                $name_array[] = $input['middle_name'];
            }
            if (!empty($input['last_name'])) {
                $name_array[] = $input['last_name'];
            }
            $input['name'] = implode(' ', $name_array);

            DB::beginTransaction();

            if ($input['type'] == 'lead') {

                $input['crm_source'] = $request->input('source_id') ?? null;
                $input['crm_life_stage'] = $request->input('life_stage_id') ?? null;

                $assigned_to = $request->input('assigned_to') ?? [];

                $lead = \Modules\Crm\Entities\CrmContact::createNewLead($input, $assigned_to);
                $output['data'] = $lead;
            } else {
                $output = $this->contactUtil->createNewContact($input);
            }


            DB::commit();

            return new CommonResource($output['data']);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            return $this->otherExceptions($e);
        }
    }
    
    /**
     * Get the specified contact
     * @urlParam contact required comma separated ids of contacts Example: 2
     * @response {
        "data": [
            {
                "id": 1,
                "business_id": 1,
                "type": "customer",
                "supplier_business_name": null,
                "name": " Walk-In Customer  ",
                "prefix": null,
                "first_name": "Walk-In Customer",
                "middle_name": null,
                "last_name": null,
                "email": "walkin@test.com",
                "contact_id": "CO0005",
                "contact_status": "active",
                "tax_number": null,
                "city": "Phoenix",
                "state": "Arizona",
                "country": "USA",
                "address_line_1": "Linking Street",
                "address_line_2": null,
                "zip_code": "85001",
                "dob": null,
                "mobile": "(378) 400-1234",
                "landline": null,
                "alternate_number": null,
                "pay_term_number": null,
                "pay_term_type": null,
                "credit_limit": "0.0000",
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
                "deleted_at": null,
                "created_at": "2018-01-03 20:45:20",
                "updated_at": "2020-08-10 10:26:45",
                "remember_token": null,
                "password": null,
                "customer_group": null,
                "opening_balance": "0.0000",
                "opening_balance_paid": "0.0000",
                "total_purchase": "0.0000",
                "purchase_paid": "0.0000",
                "total_purchase_return": "0.0000",
                "purchase_return_paid": "0.0000",
                "total_invoice": "2050.0000",
                "invoice_received": "1987.5000",
                "total_sell_return": "0.0000",
                "sell_return_paid": "0.0000",
                "purchase_due": 0,
                "sell_due": 62.5,
                "purchase_return_due": 0,
                "sell_return_due": 0
            }
        ]
    }
    */
    public function show($contact_ids)
    {
        $user = Auth::user();

        // if (!$user->can('api.access')) {
        //     return $this->respondUnauthorized();
        // }

        $business_id = $user->business_id;
        $contact_ids = explode(',', $contact_ids);

        $query = $this->contactUtil->getContactQuery($business_id, 'both', $contact_ids);
        $contacts = $query->get();

        foreach ($contacts as $key => $value) {
            $contacts[$key]->purchase_due = $value->total_purchase - $value->total_purchase_paid;
            $contacts[$key]->sell_due = $value->total_invoice - $value->invoice_received;
            $contacts[$key]->purchase_return_due = $value->total_purchase_return - $value->purchase_return_paid;
            $contacts[$key]->sell_return_due = $value->total_sell_return - $value->sell_return_paid;
        }

        return CommonResource::collection($contacts);
    }

    /**
    * Update contact
    *
    * @urlParam contact required id of the contact to be updated Example: 17
    * @bodyParam type string Type of contact (supplier, customer, both) Example:customer
    * @bodyParam supplier_business_name string required* Required if type is supplier
    * @bodyParam prefix string Prefix for the name of the contact
    * @bodyParam first_name string required Name of the contact
    * @bodyParam middle_name string
    * @bodyParam last_name string
    * @bodyParam tax_number string Example: 488744dwd
    * @bodyParam pay_term_number float Example: 3
    * @bodyParam pay_term_type string (months ,days)  Example:months
    * @bodyParam mobile string required Example:8795461009
    * @bodyParam landline string  Example:65484-848-848
    * @bodyParam alternate_number string  Example:9898795220
    * @bodyParam address_line_1 string
    * @bodyParam address_line_2 string
    * @bodyParam city string  
    * @bodyParam state string 
    * @bodyParam country string   
    * @bodyParam zip_code string    
    * @bodyParam customer_group_id string  
    * @bodyParam contact_id string  
    * @bodyParam dob string Fromat: Y-m-d Example:2000-06-13
    * @bodyParam custom_field1 string  
    * @bodyParam custom_field2 string  
    * @bodyParam custom_field3 string  
    * @bodyParam custom_field4 string  
    * @bodyParam email string  
    * @bodyParam shipping_address string  
    * @bodyParam position string  
    * @bodyParam opening_balance float Example:10.3
    * @bodyParam source_id integer Id of the source. Applicable only if the type is lead 
    * @bodyParam life_stage_id integer Id of the Life stage. Applicable only if the type is lead
    * @bodyParam assigned_to array Ids of the users the lead is assigned to. Applicable only if the type is lead
    *
    * @response {
        "data": {
            "id": 21,
            "business_id": 1,
            "type": "customer",
            "supplier_business_name": null,
            "name": "created from api",
            "prefix": null,
            "first_name": "created from api",
            "middle_name": null,
            "last_name": null,
            "email": null,
            "contact_id": "CO0009",
            "contact_status": "active",
            "tax_number": null,
            "city": null,
            "state": null,
            "country": null,
            "address_line_1": "test address",
            "address_line_2": null,
            "zip_code": "54878787",
            "dob": "2000-06-13",
            "mobile": "8754154872154",
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
            "crm_source": null,
            "crm_life_stage": null,
            "custom_field1": null,
            "custom_field2": null,
            "custom_field3": null,
            "custom_field4": null,
            "deleted_at": null,
            "created_at": "2020-08-10 10:41:42",
            "updated_at": "2020-08-10 10:41:42",
            "remember_token": null,
            "password": null
        }
    }
    */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->only(['type', 'supplier_business_name', 'prefix', 'first_name', 'middle_name', 'last_name', 'tax_number', 'pay_term_number', 'pay_term_type', 'mobile', 'address_line_1', 'address_line_2', 'zip_code', 'dob', 'alternate_number', 'city', 'state', 'country', 'landline', 'customer_group_id', 'contact_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email', 'shipping_address', 'position']);
            
            $business_id = Auth::user()->business_id;

            $updates = [];
            foreach ($input as $key => $value) {
                if ($request->has($key)) {
                    $updates[$key] = $value;
                }
            }

            if (!empty($input['prefix'])) {
                $name_array[] = $input['prefix'];
            }
            $name_array[] = $input['first_name'];
            if (!empty($input['middle_name'])) {
                $name_array[] = $input['middle_name'];
            }
            if (!empty($input['last_name'])) {
                $name_array[] = $input['last_name'];
            }
            $input['name'] = implode(' ', $name_array);
            
            DB::beginTransaction();

            if ($input['type'] == 'lead') {

                $input['crm_source'] = $request->input('source_id') ?? null;
                $input['crm_life_stage'] = $request->input('life_stage_id') ?? null;

                $assigned_to = $request->input('assigned_to') ?? [];

                $lead = \Modules\Crm\Entities\CrmContact::updateLead($id, $input, $assigned_to);

                $output['data'] = $lead;
            } else {
                $output = $this->contactUtil->updateContact($updates, $id, $business_id);
            }

            DB::commit();

            return new CommonResource($output['data']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return $this->otherExceptions($e);
        }
    }

    /**
    * Contact payment
    *
    * @bodyParam contact_id int required id of the contact Example: 17
    * @bodyParam amount float required amount of the payment Example: 453.1300
    * @bodyParam method string payment methods ('cash', 'card', 'cheque', 'bank_transfer', 'other', 'custom_pay_1', 'custom_pay_2', 'custom_pay_3') Example: cash
    * @bodyParam paid_on string transaction date format:Y-m-d H:i:s, Example: 2020-07-22 15:48:29
    * @bodyParam account_id int account id 
    * @bodyParam card_number string 
    * @bodyParam card_holder_name string 
    * @bodyParam card_transaction_number string 
    * @bodyParam card_type string 
    * @bodyParam card_month string 
    * @bodyParam card_year string 
    * @bodyParam card_security string 
    * @bodyParam transaction_no_1 string 
    * @bodyParam transaction_no_2 string 
    * @bodyParam transaction_no_3 string 
    * @bodyParam cheque_number string 
    * @bodyParam bank_account_number string
    * @bodyParam note string payment note
    *
    *@response {
        "data": {
            "amount": "20",
            "method": "cash",
            "paid_on": "2020-07-22 15:48:29",
            "created_by": 1,
            "payment_for": "19",
            "business_id": 1,
            "is_advance": 1,
            "payment_ref_no": "SP2020/0127",
            "document": null,
            "updated_at": "2020-07-22 15:48:29",
            "created_at": "2020-07-22 15:48:29",
            "id": 215
        }
    }
    */
    public function contactPay(Request $request)
    {
        try {
            DB::beginTransaction();

            $payment = $this->transactionUtil->payContact($request, false);

            DB::commit();
            return new CommonResource($payment);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return $this->otherExceptions($e);
        }
    }
}
