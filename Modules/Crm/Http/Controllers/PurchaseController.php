<?php

namespace Modules\Crm\Http\Controllers;

use App\Contact;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseList(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $contact_type = Contact::where('business_id', $business_id)
                            ->find(auth()->user()->crm_contact_id)
                            ->type;

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module') && in_array($contact_type, ['supplier', 'both']))) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $purchases = $this->transactionUtil->getListPurchases($business_id);

            //filter by payment status
            if (!empty($request->input('payment_status')) && $request->input('payment_status') != 'overdue') {
                $purchases->where('transactions.payment_status', $request->input('payment_status'));
            } elseif ($request->input('payment_status') == 'overdue') {
                $purchases->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            //filter by purchase status
            if (!empty($request->status)) {
                $purchases->where('transactions.status', $request->status);
            }

            //filter by date
            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end =  $request->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            //get purchase of logged in supplier/customer
            $purchases->where('contacts.id', auth()->user()->crm_contact_id);

            return Datatables::of($purchases)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                <li>
                                    <a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a>
                                </li>

                                <li>
                                    <a href="#" class="print-invoice" data-href="' . action('PurchaseController@printInvoice', [$row->id]) . '"><i class="fas fa-print" aria-hidden="true"></i>'. __("messages.print") .'</a>
                                </li>';

                    $html .=  '</ul>
                            </div>';
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn('ref_no', function ($row) {
                    return !empty($row->return_exists) ? $row->ref_no . ' <small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned') .'"><i class="fas fa-undo"></i></small>' : $row->ref_no;
                })
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'status',
                    '<a href="#">
                        <span class="label @transaction_status($status) status-label" data-status-name="{{__(\'lang_v1.\' . $status)}}" data-orig-value="{{$status}}">
                            {{__(\'lang_v1.\' . $status)}}
                        </span>
                    </a>'
                )
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);

                        if ($payment_status == 'partial') {
                            $bg = 'bg-aqua';
                        } elseif ($payment_status == 'due') {
                            $bg = 'bg-yellow';
                        } elseif ($payment_status == 'paid') {
                            $bg = 'bg-light-green';
                        } elseif ($payment_status == 'overdue' || $payment_status == 'partial-overdue') {
                            $bg = 'bg-red';
                        }

                        $html = '<a href="#" class="view_payment_modal payment-status-label" data-orig-value="'.$payment_status.'" data-status-name="'.__('lang_v1.' . $payment_status).'"><span class="label '.$bg.'">'.__('lang_v1.' . $payment_status).'
                        </span></a>';

                        return $html;
                    }
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    $due_html = '<strong>' . __('lang_v1.purchase') .':</strong> <span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';

                    if (!empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $due_html .= '<br><strong>' . __('lang_v1.purchase_return') .':</strong> <a href="#" class="no-print"><span class="display_currency purchase_return" data-currency_symbol="true" data-orig-value="' . $return_due . '">' . $return_due . '</span></a><span class="display_currency print_section" data-currency_symbol="true">' . $return_due . '</span>';
                    }
                    return $due_html;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action('PurchaseController@show', [$row->id]);
                    }])
                ->rawColumns(['action', 'ref_no', 'status', 'payment_status', 'final_total', 'payment_due'])
                ->make(true);
        }

        $orderStatuses = $this->productUtil->orderStatuses();
        return view('crm::purchase.index')
            ->with(compact('orderStatuses'));
    }
}
