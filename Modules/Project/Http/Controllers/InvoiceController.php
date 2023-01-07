<?php

namespace Modules\Project\Http\Controllers;

use App\Contact;
use App\InvoiceScheme;
use App\TaxRate;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Project\Entities\InvoiceLine;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectTransaction;
use Yajra\DataTables\Facades\DataTables;
use App\BusinessLocation;
class InvoiceController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param CommonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $project_id = request()->get('project_id');

            $transactions = ProjectTransaction::where('business_id', $business_id)
                    ->where('pjt_project_id', $project_id)
                    ->with('contact')
                    ->select('invoice_no', 'transaction_date', 'contact_id', 'pjt_title', 'payment_status', 'final_total', 'status', 'id', 'pjt_project_id');

            return Datatables::of($transactions)
                        ->addColumn('action', function ($row) {
                            $html = '<div class="btn-group">
                                <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                    '.__("messages.action").'
                                    <span class="caret"></span>
                                    <span class="sr-only">
                                    '.__("messages.action").'
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                            if ($row->payment_status != "paid") {
                                $html .= '<li>
                                    <a href="' . action('TransactionPaymentController@addPayment', ['id' => $row->id]) . '" class="add_payment_modal">
                                        <i class="fas fa-credit-card"></i>
                                        '.__("purchase.add_payment").'
                                    </a>
                                </li>';
                            }

                            $html .= '
                                    <li>
                                        <a href="' . action('TransactionPaymentController@show', [$row->id]) . '" class="view_payment_modal">
                                            <i class="fas fa-money-check"></i> ' . __("purchase.view_payments") . '
                                        </a>
                                    </li>
                                    <li>
                                        <a data-href="' . action('\Modules\Project\Http\Controllers\InvoiceController@show', ['id' => $row->id, 'project_id' => $row->pjt_project_id]) . '" class="cursor-pointer view_a_project_invoice">
                                            <i class="fa fa-eye"></i>
                                            '.__("messages.view").'
                                        </a>
                                    </li>
                                    <li>
                                        <a href="' . action('\Modules\Project\Http\Controllers\InvoiceController@edit', ['id' => $row->id, 'project_id' => $row->pjt_project_id]) . '" class="cursor-pointer edit_a_invoice">
                                            <i class="fa fa-edit"></i>
                                            '.__("messages.edit").'
                                        </a>
                                    </li>
                                    <li>
                                        <a data-href="' . action('\Modules\Project\Http\Controllers\InvoiceController@destroy', ['id' => $row->id, 'project_id' => $row->pjt_project_id]) . '" class="cursor-pointer delete_a_invoice">
                                            <i class="fas fa-trash"></i>
                                            '.__("messages.delete").'
                                        </a>
                                    </li>';
                            $html .= '</ul>
                                    </div>';

                            return $html;
                        })
                        ->editColumn('transaction_date', '
                                {{@format_date($transaction_date)}}
                        ')
                        ->editColumn('contact_id', function ($row) {
                            return $row->contact->name;
                        })
                        ->editColumn('invoice_no', '
                            <a data-href="{{action("\Modules\Project\Http\Controllers\InvoiceController@show", ["id" => $id, "project_id" => $pjt_project_id])}}" class="cursor-pointer view_a_project_invoice text-black">
                                {{$invoice_no}}
                            </a>
                        ')
                        ->editColumn('pjt_title', '
                            <a data-href="{{action("\Modules\Project\Http\Controllers\InvoiceController@show", ["id" => $id, "project_id" => $pjt_project_id])}}" class="cursor-pointer view_a_project_invoice text-black">
                                {{$pjt_title}}
                            </a>
                        ')
                        ->editColumn(
                            'payment_status',
                            '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}">
                                    <span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}
                                    </span>
                            </a>'
                        )
                        ->editColumn('final_total', function ($row) {
                            $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . $row->final_total . '">' . $row->final_total . '</span>';

                            return $html;
                        })
                        ->editColumn('status', function ($row) {
                            return __('sale.'.$row->status);
                        })
                        ->removeColumn('id')
                        ->rawColumns(['action', 'invoice_no', 'transaction_date', 'contact_id', 'pjt_title', 'payment_status', 'final_total', 'status'])
                        ->make(true);
        }

        return view('project::invoice.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $project_id = request()->get('project_id');

        $project = Project::where('business_id', $business_id)
                        ->findOrFail($project_id);

        $customers = Contact::customersDropdown($business_id, false);
        $invoice_schemes = InvoiceScheme::forDropdown($business_id);
        $default_scheme = InvoiceScheme::getDefault($business_id);
        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];
        $business_locations = BusinessLocation::forDropdown($business_id);
        $statuses = ProjectTransaction::invoiceStatuses();
        $discount_types = ProjectTransaction::discountTypes();

        return view('project::invoice.create')
            ->with(compact('customers', 'invoice_schemes', 'default_scheme', 'project', 'statuses', 'discount_types', 'taxes', 'tax_attributes', 'business_locations'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $input = $request->only('pjt_project_id', 'pjt_title', 'contact_id', 'pay_term_number', 'pay_term_type', 'status', 'discount_type', 'staff_note', 'additional_notes', 'location_id');

            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['transaction_date'] = $this->commonUtil->uf_date($request->input('transaction_date'));
            $input['invoice_no'] = $this->transactionUtil->getInvoiceNumber($input['business_id'], $input['status'], null, $request->get('invoice_scheme_id'));
            $input['discount_amount'] = $this->commonUtil->num_uf($request->get('discount_amount'));
            $input['total_before_tax'] = $this->commonUtil->num_uf($request->get('total_before_tax'));
            $input['final_total'] = $this->commonUtil->num_uf($request->get('final_total'));
            $input['type'] = 'sell';
            $input['sub_type'] = 'project_invoice';
            $input['payment_status'] = 'due';

            // invoice lines
            $tasks = $request->input('task');
            $rates = $request->input('rate');
            $quantities = $request->input('quantity');
            $tax_rate_ids = $request->input('tax_rate_id');
            $totals = $request->input('total');
            $descriptions = $request->input('description');

            $invoice_lines = [];
            foreach ($tasks as $key => $value) {
                $rate = $rates[$key];
                $quantity = $quantities[$key];
                $total = $totals[$key];
                $description = $descriptions[$key];
                $tax_rate_id = $tax_rate_ids[$key];
                if (isset($value) && isset($rate) && isset($quantity) && isset($total)) {
                    $invoice_lines[] = [
                        'task' => $value,
                        'rate' => $this->commonUtil->num_uf($rate),
                        'tax_rate_id' => $tax_rate_id,
                        'quantity' => $this->commonUtil->num_uf($quantity),
                        'total' => $this->commonUtil->num_uf($total),
                        'description' => $description
                    ];
                }
            }
            
            $transaction = ProjectTransaction::create($input);

            $transaction->invoiceLines()->createMany($invoice_lines);

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect()->action(
            '\Modules\Project\Http\Controllers\ProjectController@show',
            ['id' => $input['pjt_project_id']]
            )->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $project_id = request()->get('project_id');

            $transaction = ProjectTransaction::where('business_id', $business_id)
                    ->where('pjt_project_id', $project_id)
                    ->with('contact', 'invoiceLines', 'invoiceLines.tax', 'project', 'payment_lines')
                    ->findOrFail($id);
                                
            return view('project::invoice.show')
                ->with(compact('transaction'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        $project_id = request()->get('project_id');

        $transaction = ProjectTransaction::with('invoiceLines')
                            ->where('business_id', $business_id)
                            ->where('pjt_project_id', $project_id)
                            ->findOrFail($id);
        $project = Project::where('business_id', $business_id)
                        ->findOrFail($project_id);
        $customers = Contact::customersDropdown($business_id, false);
        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];
        $statuses = ProjectTransaction::invoiceStatuses();
        $discount_types = ProjectTransaction::discountTypes();
        $business_locations = BusinessLocation::forDropdown($business_id);
        return view('project::invoice.edit')
            ->with(compact('project', 'customers', 'statuses', 'discount_types', 'transaction', 'taxes', 'tax_attributes', 'business_locations'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $input = $request->only('pjt_title', 'contact_id', 'pay_term_number', 'pay_term_type', 'status', 'discount_type', 'staff_note', 'additional_notes', 'location_id');
            $input['transaction_date'] = $this->commonUtil->uf_date($request->input('transaction_date'));
            $input['discount_amount'] = $this->commonUtil->num_uf($request->get('discount_amount'));
            $input['total_before_tax'] = $this->commonUtil->num_uf($request->get('total_before_tax'));
            $input['final_total'] = $this->commonUtil->num_uf($request->get('final_total'));

            $project_id = $request->get('pjt_project_id');
            $business_id = request()->session()->get('user.business_id');

            $transaction = ProjectTransaction::where('business_id', $business_id)
                    ->where('pjt_project_id', $project_id)
                    ->findOrFail($id);

            $transaction->update($input);

            // update existing invoice line
            $invoice_line_ids = $request->input('invoice_line_id');
            $existing_task = $request->input('existing_task');
            $existing_rate = $request->input('existing_rate');
            $existing_tax_rate_ids = $request->input('existing_tax_rate_id');
            $existing_quantity = $request->input('existing_quantity');
            $existing_total = $request->input('existing_total');
            $existing_description = $request->input('existing_description');

            $existing_line_id = [];
            foreach ($invoice_line_ids as $key => $invoice_line_id) {
                $existing_line_id[] = $invoice_line_id;
                $invoice_line = InvoiceLine::where('transaction_id', $id)
                                    ->findOrFail($invoice_line_id);
                $invoice_line->task = $existing_task[$key];
                $invoice_line->tax_rate_id = $existing_tax_rate_ids[$key];
                $invoice_line->description = $existing_description[$key];
                $invoice_line->rate = $this->commonUtil->num_uf($existing_rate[$key]);
                $invoice_line->quantity = $this->commonUtil->num_uf($existing_quantity[$key]);
                $invoice_line->total = $this->commonUtil->num_uf($existing_total[$key]);
                $invoice_line->save();
            }

            // remove line which is not present in existing line id
            InvoiceLine::where('transaction_id', $id)
                        ->whereNotIn('id', $existing_line_id)
                        ->delete();

            //add new invoice line
            $tasks = $request->input('task');
            $rates = $request->input('rate');
            $tax_rate_ids = $request->input('tax_rate_id');
            $quantities = $request->input('quantity');
            $totals = $request->input('total');
            $descriptions = $request->input('description');
            
            $invoice_lines = [];
            foreach ($tasks as $key => $value) {
                $rate = $rates[$key];
                $quantity = $quantities[$key];
                $total = $totals[$key];
                $description = $descriptions[$key];
                $tax_rate_id = $tax_rate_ids[$key];

                if (isset($value) && isset($rate) && isset($quantity) && isset($total)) {
                    $invoice_lines[] = [
                        'task' => $value,
                        'rate' => $this->commonUtil->num_uf($rate),
                        'tax_rate_id' => $tax_rate_id,
                        'quantity' => $this->commonUtil->num_uf($quantity),
                        'total' => $this->commonUtil->num_uf($total),
                        'description' => $description,
                    ];
                }
            }

            $transaction->invoiceLines()->createMany($invoice_lines);

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect()->action(
            '\Modules\Project\Http\Controllers\ProjectController@show',
            ['id' => $project_id]
            )->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');
                $project_id = request()->get('project_id');

                $transaction = ProjectTransaction::where('business_id', $business_id)
                    ->where('pjt_project_id', $project_id)
                    ->findOrFail($id);

                $transaction->delete();
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
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
     * get Project Invoice Tax Report
     * used in tax report view
     *
     * @return Response
     */
    public function getProjectInvoiceTaxReport(Request $request)
    {
        if (!auth()->user()->can('tax_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $taxes = TaxRate::forBusiness($business_id);

            $transactions = ProjectTransaction::leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')
                ->leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.sub_type', 'project_invoice')
                ->where('transactions.status', 'final')
                ->with(['invoiceLines' => function($q){
                    $q->whereNotNull('pjt_invoice_lines.tax_rate_id');
                }])
                ->select('c.name as contact_name', 
                        'c.tax_number',
                        'transactions.ref_no',
                        'transactions.invoice_no',
                        'transactions.transaction_date',
                        'transactions.total_before_tax',
                        'transactions.tax_id',
                        'transactions.tax_amount',
                        'transactions.id',
                        'transactions.type',
                        'transactions.discount_type',
                        'transactions.discount_amount'
                    );

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $transactions->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $datatable = Datatables::of($transactions);
            $raw_cols = ['total_before_tax', 'discount_amount'];
            foreach ($taxes as $tax) {
                $col = 'tax_' . $tax['id'];
                $raw_cols[] = $col;
                $datatable->addColumn($col, function($row) use($tax, $col) {
                    $tax_amount = 0;
                    foreach ($row->invoiceLines as $invoiceLine) {
                        if ($invoiceLine->tax_rate_id == $tax['id']) {
                            $row_amount = $invoiceLine->rate * $invoiceLine->quantity;
                            $item_tax = $this->transactionUtil->calc_percentage($row_amount, $tax['amount']);
                            $tax_amount += $item_tax * $invoiceLine->quantity;
                        }
                    }

                    if ($tax_amount > 0) {
                        return '<span class="display_currency ' . $col . '" data-currency_symbol="true" data-orig-value="' . $tax_amount . '">' . $tax_amount . '</span>';
                    } else {
                        return '';
                    }
                });
            }

            $datatable->editColumn(
                'total_before_tax',
                '<span class="display_currency total_before_tax" data-currency_symbol="true" data-orig-value="{{$total_before_tax}}">{{$total_before_tax}}</span>'
            )->editColumn('discount_amount', '@if($discount_amount != 0)<span class="display_currency" data-currency_symbol="true">{{$discount_amount}}</span>@if($discount_type == "percentage")% @endif @endif')
            ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}');

            return $datatable->rawColumns($raw_cols)
                ->make(true);
        }
    }
}
