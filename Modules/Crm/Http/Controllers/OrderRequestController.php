<?php

namespace Modules\Crm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\BusinessLocation;
use App\Contact;
use App\Transaction;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Utils\Util;
use App\User;
use App\Utils\ProductUtil;
use App\Utils\ContactUtil;
use App\TaxRate;
use Illuminate\Support\Facades\DB;
use App\Media;
use Modules\Crm\Utils\CrmUtil;
use Yajra\DataTables\Facades\DataTables;

class OrderRequestController extends Controller
{
    protected $transactionUtil;
    protected $businessUtil;
    protected $commonUtil;
    protected $productUtil;
    protected $contactUtil;
    protected $crmUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, BusinessUtil $businessUtil, Util $commonUtil, ProductUtil $productUtil, ContactUtil $contactUtil, CrmUtil $crmUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->commonUtil = $commonUtil;
        $this->contactUtil = $contactUtil;
        $this->crmUtil = $crmUtil;
        $this->order_statuses = [
            'ordered' => [
                'label' => __('lang_v1.ordered'),
                'class' => 'bg-info'
            ],
            'partial' => [
                'label' => __('lang_v1.partial'),
                'class' => 'bg-yellow'
            ],
            'completed' => [
                'label' => __('restaurant.completed'),
                'class' => 'bg-green'
            ]
        ];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        // if (!auth()->user()->can('so.view_own') && !auth()->user()->can('so.view_all') && !auth()->user()->can('so.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');
        $customer = Contact::where('business_id', auth()->user()->business_id)
                            ->findOrFail(auth()->user()->crm_contact_id);   

        if (request()->ajax()) {
            $sells = $this->transactionUtil->getListSells($business_id, 'sales_order');

            $sells->where('transactions.contact_id', $customer->id)
                ->where('transactions.crm_is_order_request', 1)
                ->where('transactions.created_by', auth()->user()->id)
                ->groupBy('transactions.id');

            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (!empty(request()->input('status'))) {
                $sells->where('transactions.status', request()->input('status'));
            }

            $datatable = Datatables::of($sells)
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="final-total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn('total_items', '{{@format_quantity($total_items)}}')
                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where( function($q) use($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                
                ->editColumn('status', function($row){
                    $status = '<span class="label ' . $this->order_statuses[$row->status]['class'] . '" >' . $this->order_statuses[$row->status]['label'] . '</span>';

                    return $status;
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('so_qty_remaining', '{{@format_quantity($so_qty_remaining)}}')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return  action('SellController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }]);

            $rawColumns = ['final_total', 'invoice_no','conatct_name', 'status'];
                
            return $datatable->rawColumns($rawColumns)
                      ->make(true);
        }

        $order_statuses = [];
        foreach ($this->order_statuses as $key => $value) {
            $order_statuses[$key] = $value['label'];
        } 

        $business_locations = BusinessLocation::forDropdown($business_id, false, false, true, false);

        return view('crm::order_request.index')
            ->with(compact('business_locations', 'customer', 'order_statuses'));

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $contact = Contact::where('business_id', auth()->user()->business_id)
                            ->findOrFail(auth()->user()->crm_contact_id);

        $business_id = request()->session()->get('user.business_id');
        
        $business_details = $this->businessUtil->getDetails($business_id);

        $business_locations = BusinessLocation::forDropdown($business_id, false, true, true, false);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $default_location = null;
        foreach ($business_locations as $id => $name) {
            $default_location = BusinessLocation::findOrFail($id);
            break;
        }

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        return view('crm::order_request.create')
            ->with(compact(
                'business_details',
                'business_locations',
                'bl_attributes',
                'default_location',
                'pos_settings',
                'contact'
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $is_direct_sale = true;

        try {
            $input = $request->except('_token');

            $input['status'] = 'ordered';
            $input['type'] = 'sales_order';
            $input['discount_amount'] = 0;

            $contact = Contact::where('business_id', auth()->user()->business_id)
                            ->findOrFail(auth()->user()->crm_contact_id);

            if (!empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');
        
                $user_id = $request->session()->get('user.id');
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], null);

                DB::beginTransaction();

                $input['transaction_date'] =  \Carbon::now();
                $input['is_direct_sale'] = 1;

                //Customer group details
                $contact_id = $contact->id;
                $cg = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;

                //set selling price group id
                $price_group_id = $request->has('price_group') ? $request->input('price_group') : null;

                //If default price group for the location exists
                $price_group_id = $price_group_id == 0 && $request->has('default_price_group') ? $request->input('default_price_group') : $price_group_id;

                $input['selling_price_group_id'] = $price_group_id;

                $crm_settings = $this->crmUtil->getCrmSettings($business_id);
                $order_request_prefix = $crm_settings['order_request_prefix'] ?? null;

                $ref_count = $this->productUtil->setAndGetReferenceCount('crm_order_request');

                $input['invoice_no'] = $this->productUtil->generateReferenceNumber('crm_order_request', $ref_count, $business_id, $order_request_prefix);

                $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id);

                $transaction->crm_is_order_request = 1;
                $transaction->save();
                
                $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id']);

                $this->transactionUtil->activityLog($transaction, 'added');

                DB::commit();

                $output = ['success' => 1, 'msg' => __('lang_v1.added_success') ];
            } else {
                $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $msg = trans("messages.something_went_wrong");

            $output = ['success' => 0,
                            'msg' => $msg
                        ];
        }

        return redirect()
                ->action('\Modules\Crm\Http\Controllers\OrderRequestController@index')
                ->with('status', $output);

    }

    public function getProductRow($variation_id, $location_id)
    {
        $output = [];

        try {
            $row_count = request()->get('product_row');
            $row_count = $row_count + 1;
            $quantity = request()->get('quantity', 1);

            $is_direct_sell = true;

            $output = $this->getSellLineRow($variation_id, $location_id, $quantity, $row_count, $is_direct_sell);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage();
        }

        return $output;
    }

    private function getSellLineRow($variation_id, $location_id, $quantity, $row_count, $is_direct_sell)
    {
        $business_id = request()->session()->get('user.business_id');
        $business_details = $this->businessUtil->getDetails($business_id);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id, false);

        if (!isset($product->quantity_ordered)) {
            $product->quantity_ordered = $quantity;
        }

        $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit_id, false, $product->product_id);

        //Get customer group and change the price accordingly
        $customer_id = request()->get('customer_id', null);
        $cg = $this->contactUtil->getCustomerGroup($business_id, $customer_id);
        $percent = (empty($cg) || empty($cg->amount) || $cg->price_calculation_type != 'percentage') ? 0 : $cg->amount;
        $product->default_sell_price = $product->default_sell_price + ($percent * $product->default_sell_price / 100);
        $product->sell_price_inc_tax = $product->sell_price_inc_tax + ($percent * $product->sell_price_inc_tax / 100);

        $price_group = request()->input('price_group');
        if (!empty($price_group)) {
            $variation_group_prices = $this->productUtil->getVariationGroupPrice($variation_id, $price_group, $product->tax_id);
            
            if (!empty($variation_group_prices['price_inc_tax'])) {
                $product->sell_price_inc_tax = $variation_group_prices['price_inc_tax'];
                $product->default_sell_price = $variation_group_prices['price_exc_tax'];
            }
        }

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);

        $output['success'] = true;
        $output['enable_sr_no'] = $product->enable_sr_no;

        $is_cg = !empty($cg->id) ? true : false;
        
        $discount = $this->productUtil->getProductDiscount($product, $business_id, $location_id, $is_cg, $price_group, $variation_id);
        

        $output['html_content'] =  view('crm::order_request.product_row')
                    ->with(compact('product', 'row_count', 'pos_settings', 'sub_units', 'discount', 'quantity', 'is_direct_sell', 'tax_dropdown'))
                    ->render();

        return $output;
    }

    public function listOrderRequests()
    {
        $business_id = request()->session()->get('user.business_id');
        $crm_settings = $this->crmUtil->getCrmSettings($business_id);

        $order_statuses = [];
        foreach ($this->order_statuses as $key => $value) {
            $order_statuses[$key] = $value['label'];
        } 

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);    

        return view('crm::order_request.all_list')
            ->with(compact('business_locations', 'order_statuses', 'customers'));
    }

}
