<?php

namespace Modules\Repair\Http\Controllers;

use App\Barcode;
use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\Media;
use App\NotificationTemplate;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionSellLine;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Warranty;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\Repair\Entities\DeviceModel;
use Modules\Repair\Entities\RepairStatus;
use Modules\Repair\Utils\RepairUtil;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class RepairController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $repairUtil;
    protected $commonUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, RepairUtil $repairUtil, Util $commonUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->repairUtil = $repairUtil;
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('repair.view') || auth()->user()->can('repair.view_own')) ))) {
            abort(403, 'Unauthorized action.');
        }

        $is_admin = $this->commonUtil->is_admin(auth()->user(), $business_id);

        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin(
                    'repair_statuses AS rs',
                    'transactions.repair_status_id',
                    '=',
                    'rs.id'
                )
                ->leftJoin(
                    'users as ss',
                    'ss.id',
                    '=',
                    'transactions.res_waiter_id'
                )
                ->leftJoin(
                    'warranties as rw',
                    'rw.id',
                    '=',
                    'transactions.repair_warranty_id'
                )
                ->leftJoin(
                    'transactions AS SR',
                    'transactions.id',
                    '=',
                    'SR.return_parent_id'
                )
                ->leftJoin(
                    'repair_device_models as rdm',
                    'rdm.id',
                    '=',
                    'transactions.repair_model_id'
                )
                ->leftJoin(
                    'brands AS b',
                    'transactions.repair_brand_id',
                    '=',
                    'b.id'
                )
                ->join(
                    'users',
                    'transactions.created_by',
                    '=',
                    'users.id'
                )
                ->leftJoin(
                    'repair_job_sheets AS rjs',
                    'transactions.repair_job_sheet_id',
                    '=',
                    'rjs.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->where('transactions.sub_type', 'repair')
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.is_direct_sale',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'transactions.tax_amount',
                    'transactions.discount_amount',
                    'transactions.discount_type',
                    'transactions.total_before_tax',
                    'transactions.repair_status_id',
                    'rs.name as repair_status',
                    'rs.color as status_color',
                    'rs.is_completed_status',
                    'transactions.repair_serial_no',
                    DB::raw('SUM(IF(tp.is_return = 1,-1*tp.amount,tp.amount)) as total_paid'),
                    'bl.name as business_location',
                    DB::raw('CONCAT(COALESCE(ss.first_name, ""), COALESCE(ss.last_name, "")) as service_staff'),
                    'transactions.repair_completed_on',
                    'rw.name as warranty_name',
                    'rw.duration',
                    'rw.duration_type',
                    'transactions.repair_due_date',
                    DB::raw('COUNT(SR.id) as return_exists'),
                    DB::raw('(SELECT SUM(tp1.amount) FROM transaction_payments AS tp1 WHERE
                        tp1.transaction_id=SR.id ) as return_paid'),
                    DB::raw('COALESCE(SR.final_total, 0) as amount_return'),
                    'SR.id as return_transaction_id',
                    'rdm.name as device_model',
                    'b.name as brand',
                    'transactions.repair_updates_notif',
                    DB::raw("CONCAT(COALESCE(users.surname, ''),' ',COALESCE(users.first_name, ''),' ',COALESCE(users.last_name,'')) as added_by"),
                    'rjs.job_sheet_no as job_sheet_no',
                    'rjs.id as job_sheet_id'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!auth()->user()->can('repair.view') && auth()->user()->can('repair.view_own')) {
                $sells->where( function($q) {
                    $q->where('transactions.created_by', auth()->user()->id)
                        ->orWhere('transactions.res_waiter_id', auth()->user()->id);
                });
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            if (!empty(request()->input('payment_status'))) {
                $sells->where('transactions.payment_status', request()->input('payment_status'));
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (!empty(request()->service_staff_id)) {
                $sells->where('transactions.res_waiter_id', request()->service_staff_id);
            }
            if (!empty(request()->repair_status_id)) {
                $sells->where('transactions.repair_status_id', request()->repair_status_id);
            }

            //filter out mark as completed status
            $sells->where('rs.is_completed_status', request()->get('is_completed_status'));

            $sells->groupBy('transactions.id');

            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                                        __("messages.actions") .
                                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">' ;

                        if (auth()->user()->can("repair.view") || auth()->user()->can("direct_sell.access")) {
                            $html .= '<li><a href="#" data-href="' . action("\Modules\Repair\Http\Controllers\RepairController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                        }

                        if (auth()->user()->can("repair.update")) {
                            $html .= '<li><a target="_blank" href="' . action('SellPosController@edit', [$row->id]) . '?sub_type=repair"><i class="fas fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }

                        if (auth()->user()->can("repair.delete")) {
                            $html .= '<li><a href="' . action('SellPosController@destroy', [$row->id]) . '" class="delete-sale"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        if (auth()->user()->can("repair.view") || auth()->user()->can("direct_sell.access")) {
                            $html .= '<li>
                                        <a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '">
                                            <i class="fa fa-print" aria-hidden="true"></i> '
                                            . __("messages.print")
                                        . '</a>
                                    </li>
                                    <li>
                                        <a href="#" class="print-invoice" data-href="' . route('repair.customerCopy', [$row->id]) . '">
                                            <i class="fa fa-print" aria-hidden="true"></i> '
                                            . __("repair::lang.print_customer_copy")
                                        . '</a>
                                    </li>';
                        }
                        $html .= '<li class="divider"></li>';

                        if (auth()->user()->can("repair.create")) {
                            $html .= '<li><a href="' . action('SellReturnController@add', [$row->id]) . '"><i class="fas fa-undo"></i> ' . __("lang_v1.sell_return") . '</a></li>';
                        }

                        if (auth()->user()->can("repair_status.update")) {
                            $html .= '<li><a data-href="' . action("\Modules\Repair\Http\Controllers\RepairController@editRepairStatus", [$row->id]) . '" class="edit_repair_status"><i class="fa fa-edit"></i> ' . __("repair::lang.change_status") . '</a></li>';
                        }

                        if ($row->payment_status != "paid" && (auth()->user()->can("repair.create") || auth()->user()->can("direct_sell.access"))) {
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fas fa-money-bill-alt"></i> ' . __("purchase.add_payment") . '</a></li>';
                        }

                        $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) . '" class="view_payment_modal"><i class="fas fa-money-bill-alt"></i> ' . __("purchase.view_payments") . '</a></li>';

                        if (auth()->user()->can("send_notification")) {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "new_sale"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>' . __("lang_v1.new_sale_notification") . '</a></li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn(
                    'tax_amount',
                    '<span class="display_currency total-tax" data-currency_symbol="true" data-orig-value="{{$tax_amount}}">{{$tax_amount}}</span>'
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="display_currency total_before_tax" data-currency_symbol="true" data-orig-value="{{$total_before_tax}}">{{$total_before_tax}}</span>'
                )
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = !empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (!empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->total_before_tax * ($discount / 100);
                        }

                        return '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $discount . '">' . $discount . '</span>';
                    }
                )
                ->editColumn('repair_due_date', '
                        @if(!empty($repair_due_date))
                            {{@format_datetime($repair_due_date)}}
                        @endif
                ')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status-label no-print" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}
                        </span></a>
                        <span class="print_section">{{__(\'lang_v1.\' . $payment_status)}}</span>
                        '
                )
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining =  $row->final_total - $row->total_paid;
                    $total_remaining_html = '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $total_remaining . '">' . $total_remaining . '</span>';

                    return $total_remaining_html;
                })
                 ->editColumn('invoice_no', function ($row) {
                     $invoice_no = $row->invoice_no;

                     if (!empty($row->return_exists)) {
                         $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') .'"><i class="fas fa-undo"></i></small>';
                     }

                     return $invoice_no;
                 })
                 ->editColumn(
                     'repair_status',
                     function ($row) {
                         $html = '<a data-href="' . action("\Modules\Repair\Http\Controllers\RepairController@editRepairStatus", [$row->id]) . '" class="edit_repair_status" data-orig-value="'.$row->repair_status.'" data-status-name="'.$row->repair_status.'">
                                <span class="label " style="background-color:'.$row->status_color.';" >
                                    ' .$row->repair_status .'
                                </span>
                            </a>
                        ';

                         if ($row->repair_updates_notif) {
                             $tooltip = __("repair::lang.sms_sent");
                             $html .= '<br><i class="fas fa-check-double text-success"
                                data-toggle="tooltip" title="'.$tooltip.'"></i>';
                         }
                         return $html;
                     }
                 )
                 ->addColumn('return_due', function ($row) {
                     $return_due_html = '';
                     if (!empty($row->return_exists)) {
                         $return_due = $row->amount_return - $row->return_paid;
                         $return_due_html .= '<a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="display_currency sell_return_due" data-currency_symbol="true" data-orig-value="' . $return_due . '">' . $return_due . '</span></a>';
                     }

                     return $return_due_html;
                 })
                 ->editColumn('warranty_name', function ($row) {
                     $warranty = '';
                     $warranty_expires_in = $this->repairUtil->repairWarrantyExpiresIn($row);

                     if (!empty($warranty_expires_in)) {
                         $warranty = __('repair::lang.warranty') . ': ' . $row->warranty_name . '<br>';

                         $warranty .= '<small class="help-block">' . __('repair::lang.expires_in') . ': ' . $warranty_expires_in . '</small>';
                     }
                     return $warranty;
                 })
                 ->editColumn('job_sheet_no', function($row) {
                    $html = $row->job_sheet_no;
                    if (auth()->user()->can("job_sheet.view_assigned") || auth()->user()->can("job_sheet.view_all") || auth()->user()->can("job_sheet.create")) {
                            $html = '<a href="' . action('\Modules\Repair\Http\Controllers\JobSheetController@show', ['id' => $row->job_sheet_id]) . '" class="cursor-pointer" target="_blank">
                                    '.$row->job_sheet_no.'
                                </a>';
                    }

                    return $html;
                 })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('\Modules\Repair\Http\Controllers\RepairController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }]);

            $rawColumns = ['final_total', 'repair_due_date', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'repair_status', 'warranty_name', 'return_due', 'job_sheet_no'];
                
            return $datatable->rawColumns($rawColumns)
                      ->make(true);
        }
        
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $service_staffs = $this->transactionUtil->serviceStaffDropdown($business_id);
        $repair_status_dropdown = RepairStatus::forDropdown($business_id);
        $sales_representative = User::forDropdown($business_id, false, false, true);
        $user_role_as_service_staff = auth()->user()->roles()
                                    ->where('is_service_staff', 1)
                                    ->get()
                                    ->toArray();
        $is_service_staff = false;
        if (!empty($user_role_as_service_staff) && !$is_admin) {
            $is_service_staff = true;
        }

        return view('repair::repair.index')->with(compact('business_locations', 'customers', 'service_staffs', 'repair_status_dropdown', 'sales_representative', 'is_service_staff'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.create')))) {
            abort(403, 'Unauthorized action.');
        }

        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('HomeController@index'));
        } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        
        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $default_location = null;
        if (count($business_locations) == 1) {
            foreach ($business_locations as $id => $name) {
                $default_location = $id;
            }
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }

        $customer_groups = CustomerGroup::forDropdown($business_id);

        $default_datetime = $this->businessUtil->format_date('now', true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $repair_statuses = RepairStatus::getRepairSatuses($business_id);
        $warranties = Warranty::forDropdown($business_id);

        $brands = Brands::forDropdown($business_id);

        $service_staff = [];
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staff = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $checklist = Business::where('id', $business_id)->value('repair_checklist');
        $checklist = !empty($checklist) ? json_decode($checklist, true) : [];

        $repair_settings = $this->repairUtil->getRepairSettings($business_id);

        return view('repair::repair.create')
                ->with(compact(
                    'business_details',
                    'taxes',
                    'walk_in_customer',
                    'business_locations',
                    'bl_attributes',
                    'default_location',
                    'commission_agent',
                    'customer_groups',
                    'default_datetime',
                    'pos_settings',
                    'repair_statuses',
                    'types',
                    'brands',
                    'service_staff',
                    'checklist',
                    'warranties',
                    'repair_settings'
            ));
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.view')))) {
            abort(403, 'Unauthorized action.');
        }

        $taxes = TaxRate::where('business_id', $business_id)
                            ->pluck('name', 'id');

        $sell = Transaction::where('transactions.business_id', $business_id)
                    ->where('transactions.id', $id)
                    ->leftJoin(
                        'repair_statuses AS rs',
                        'transactions.repair_status_id',
                        '=',
                        'rs.id'
                    )
                    ->leftJoin(
                        'users AS service_staff',
                        'transactions.res_waiter_id',
                        '=',
                        'service_staff.id'
                    )
                    ->leftJoin(
                        'brands AS b',
                        'transactions.repair_brand_id',
                        '=',
                        'b.id'
                    )
                    ->leftJoin(
                        'warranties as rw',
                        'rw.id',
                        '=',
                        'transactions.repair_warranty_id'
                    )
                    ->leftJoin(
                        'repair_device_models as rdm',
                        'rdm.id',
                        '=',
                        'transactions.repair_model_id'
                    )
                    ->leftJoin(
                        'categories as device',
                        'device.id',
                        '=',
                        'transactions.repair_device_id'
                    )
                    ->with(['contact', 'sell_lines' => function ($q) {
                        $q->whereNull('parent_sell_line_id');
                    },'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'media'])
                    ->select(
                        'transactions.*',
                        'rs.name as repair_status',
                        'rs.color as repair_status_color',
                        DB::raw('CONCAT( COALESCE(service_staff.first_name, ""), " ", COALESCE(service_staff.last_name, "") ) as service_staff'),
                        'b.name as manufacturer',
                        'rw.name as warranty_name',
                        'rw.duration',
                        'rw.duration_type',
                        'rdm.name as repair_model',
                        'device.name as repair_device'
                    )
                    ->first();

        foreach ($sell->sell_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
        }

        $payment_types = $this->transactionUtil->payment_types();

        $warranty_expires_in = $this->repairUtil->repairWarrantyExpiresIn($sell);

        $order_taxes = [];
        if (!empty($sell->tax)) {
            if ($sell->tax->is_tax_group) {
                $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
            } else {
                $order_taxes[$sell->tax->name] = $sell->tax_amount;
            }
        }

        $activities = Activity::forSubject($sell)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;

        $checklists = [];
        if (!empty($sell->repair_model_id)) {
            $device_model = DeviceModel::where('business_id', $business_id)
                            ->find($sell->repair_model_id);
            
            if (!empty($device_model) && !empty($device_model->repair_checklist)) {
                $checklists = explode('|', $device_model->repair_checklist);
            }
        }

        return view('repair::repair.show')
            ->with(compact('taxes', 'sell', 'payment_types', 'order_taxes', 'activities', 'warranty_expires_in', 'is_warranty_enabled', 'checklists'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.update')))) {
            abort(403, 'Unauthorized action.');
        }

        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', ['success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])]);
        }

        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', ['success' => 0,
                    'msg' => __('lang_v1.return_exist')]);
        }
        
        $business_id = request()->session()->get('user.business_id');
        
        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $transaction = Transaction::where('business_id', $business_id)
                            ->where('type', 'sell')
                            ->where('sub_type', 'repair')
                            ->findorfail($id);

        $location_id = $transaction->location_id;
        $location_printer_type = BusinessLocation::find($location_id)->receipt_printer_type;

        $sell_details = TransactionSellLine::
                        join(
                            'products AS p',
                            'transaction_sell_lines.product_id',
                            '=',
                            'p.id'
                        )
                        ->join(
                            'variations AS variations',
                            'transaction_sell_lines.variation_id',
                            '=',
                            'variations.id'
                        )
                        ->join(
                            'product_variations AS pv',
                            'variations.product_variation_id',
                            '=',
                            'pv.id'
                        )
                        ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
                            $join->on('variations.id', '=', 'vld.variation_id')
                                ->where('vld.location_id', '=', $location_id);
                        })
                        ->leftjoin('units', 'units.id', '=', 'p.unit_id')
                        ->where('transaction_sell_lines.transaction_id', $id)
                        ->select(
                            DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                            'p.id as product_id',
                            'p.enable_stock',
                            'p.name as product_actual_name',
                            'pv.name as product_variation_name',
                            'pv.is_dummy as is_dummy',
                            'variations.name as variation_name',
                            'variations.sub_sku',
                            'p.barcode_type',
                            'p.enable_sr_no',
                            'variations.id as variation_id',
                            'units.short_name as unit',
                            'units.allow_decimal as unit_allow_decimal',
                            'transaction_sell_lines.tax_id as tax_id',
                            'transaction_sell_lines.item_tax as item_tax',
                            'transaction_sell_lines.unit_price as default_sell_price',
                            'transaction_sell_lines.unit_price_inc_tax as sell_price_inc_tax',
                            'transaction_sell_lines.unit_price_before_discount as unit_price_before_discount',
                            'transaction_sell_lines.id as transaction_sell_lines_id',
                            'transaction_sell_lines.quantity as quantity_ordered',
                            'transaction_sell_lines.sell_line_note as sell_line_note',
                            'transaction_sell_lines.lot_no_line_id',
                            'transaction_sell_lines.line_discount_type',
                            'transaction_sell_lines.line_discount_amount',
                            'units.id as unit_id',
                            'transaction_sell_lines.sub_unit_id',
                            DB::raw('vld.qty_available + transaction_sell_lines.quantity AS qty_available')
                        )
                        ->get();
        if (!empty($sell_details)) {
            foreach ($sell_details as $key => $value) {
                if ($transaction->status != 'final') {
                    $actual_qty_avlbl = $value->qty_available - $value->quantity_ordered;
                    $sell_details[$key]->qty_available = $actual_qty_avlbl;
                    $value->qty_available = $actual_qty_avlbl;
                }
                    
                $sell_details[$key]->formatted_qty_available = $this->transactionUtil->num_f($value->qty_available, false, null, true);
                $lot_numbers = [];
                if (request()->session()->get('business.enable_lot_number') == 1) {
                    $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($value->variation_id, $business_id, $location_id);
                    foreach ($lot_number_obj as $lot_number) {
                        //If lot number is selected added ordered quantity to lot quantity available
                        if ($value->lot_no_line_id == $lot_number->purchase_line_id) {
                            $lot_number->qty_available += $value->quantity_ordered;
                        }

                        $lot_number->qty_formated = $this->transactionUtil->num_f($lot_number->qty_available);
                        $lot_numbers[] = $lot_number;
                    }
                }
                $sell_details[$key]->lot_numbers = $lot_numbers;

                if (!empty($value->sub_unit_id)) {
                    $value = $this->productUtil->changeSellLineUnit($business_id, $value);
                    $sell_details[$key] = $value;
                }

                $sell_details[$key]->formatted_qty_available = $this->transactionUtil->num_f($value->qty_available, false, null, true);
            }
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        //Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $transaction->transaction_date = $this->transactionUtil->format_date($transaction->transaction_date, true);

        $transaction->repair_completed_on = !empty($transaction->repair_completed_on) ? $this->transactionUtil->format_date($transaction->repair_completed_on, true) : null;

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $repair_statuses = RepairStatus::getRepairSatuses($business_id);

        $warranties = Warranty::forDropdown($business_id);

        $brands = Brands::forDropdown($business_id);

        $waiters = [];
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $waiters = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $checklist = Business::where('id', $business_id)->value('repair_checklist');
        $checklist = !empty($checklist) ? json_decode($checklist, true) : [];

        $redeem_details = [];
        if (request()->session()->get('business.enable_rp') == 1) {
            $redeem_details = $this->transactionUtil->getRewardRedeemDetails($business_id, $transaction->contact_id);

            $redeem_details['points'] += $transaction->rp_redeemed;
            $redeem_details['points'] -= $transaction->rp_earned;
        }

        return view('repair::repair.edit')
            ->with(compact(
                'business_details',
                'taxes',
                'sell_details',
                'transaction',
                'commission_agent',
                'types',
                'customer_groups',
                'price_groups',
                'pos_settings',
                'repair_statuses',
                'brands',
                'waiters',
                'checklist',
                'warranties',
                'redeem_details'
            ));
    }

    public function editRepairStatus($repair_id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair_status.update')))) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {
            $transaction = Transaction::where('business_id', $business_id)->findOrFail($repair_id);

            $repair_status_dropdown = RepairStatus::forDropdown($business_id, true);
            $status_template_tags = $this->repairUtil->getRepairStatusTemplateTags();

            return view('repair::repair.partials.edit_repair_status_modal')
                ->with(compact('transaction', 'repair_status_dropdown', 'status_template_tags'));
        }
    }

    public function updateRepairStatus(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair_status.update')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['repair_id', 'repair_status_id_modal', 'update_note']);

                $transaction = Transaction::where('business_id', $business_id)->findOrFail($input['repair_id']);
                $transaction->repair_status_id = $input['repair_status_id_modal'];
                $transaction->save();

                $status = RepairStatus::where('business_id', $business_id)->findOrFail($input['repair_status_id_modal']);

                //Send repair updates
                if (!empty($request->input('send_sms'))) {
                    $sms_body = $request->input('sms_body');
                    $response = $this->repairUtil->sendRepairUpdateNotification($sms_body, $transaction);
                }

                //update if notification is sent or not
                if (!empty($response) && $response->getStatusCode() == 200) {
                    $transaction->repair_updates_notif = 1;
                } else {
                    $transaction->repair_updates_notif = 0;
                }
                $transaction->save();

                activity()
                ->performedOn($transaction)
                ->withProperties(['update_note' => $input['update_note'], 'updated_status' => $status->name  ])
                ->log('status_changed');

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
    }

    public function deleteMedia($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.update')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            Media::deleteMedia($business_id, $id);

            $output = ['success' => true,
                        'msg' => __("lang_v1.deleted_success")
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
     * Prints barcode for the repair
     * @return Response
     */
    public function printLabel($transaction_id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                                    ->with(['contact'])
                                    ->findorfail($transaction_id);

            $repair_settings = $this->repairUtil->getRepairSettings($business_id);

            //barcode types
            $default_barcode_type = $this->moduleUtil->barcode_default();

            $barcode_type = !empty($repair_settings['barcode_type']) ? $repair_settings['barcode_type'] : $default_barcode_type;

            $barcode_details = Barcode::find($repair_settings['barcode_id']);
            
            $business_name = request()->session()->get('business.name');

            $product_details = [];
            $total_qty = 0;
            $product_details[] = ['details' => $transaction, 'qty' => 1];
            $total_qty = 1;

            $page_height = null;
            if ($barcode_details->is_continuous) {
                $rows = ceil($total_qty/$barcode_details->stickers_in_one_row) + 0.4;
                $barcode_details->paper_height = $barcode_details->top_margin + ($rows*$barcode_details->height) + ($rows*$barcode_details->row_distance);
            }
            
            return view('repair::repair.partials.preview_label')
                ->with(compact('product_details', 'business_name', 'barcode_details', 'page_height', 'barcode_type'));
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['html' => '',
                    'success' => false,
                    'msg' =>  __('lang_v1.barcode_label_error')
                ];
        }

        return $output;
    }

    /**
     * Prints the customer copy
     * @return Response
     */
    public function printCustomerCopy(Request $request, $transaction_id)
    {
        if (request()->ajax()) {
            try {
                $output = [
                        'success' => 0,
                        'msg' => trans("messages.something_went_wrong")
                    ];

                $business_id = $request->session()->get('user.business_id');
            
                $transaction = Transaction::where('business_id', $business_id)
                                ->where('id', $transaction_id)
                                ->with(['location'])
                                ->first();

                if (empty($transaction)) {
                    return $output;
                }

                $receipt = $this->_receiptContent($business_id, $transaction->location_id, $transaction_id);

                if (!empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = [
                    'success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    protected function _receiptContent($business_id, $location_id, $transaction_id)
    {
        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_details->invoice_layout_id);

        $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, 'browser');

        $currency_details = [
            'symbol' => $business_details->currency_symbol,
            'thousand_separator' => $business_details->thousand_separator,
            'decimal_separator' => $business_details->decimal_separator,
        ];

        $receipt_details->currency = $currency_details;
        
        $output['html_content'] = view('repair::repair.receipts.classic', compact('receipt_details'))->render();

        return $output;
    }
}
