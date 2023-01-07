<?php

namespace Modules\Essentials\Http\Controllers;

use App\Transaction;
use App\User;
use App\Utils\ModuleUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Notifications\PayrollNotification;
use Modules\Essentials\Utils\EssentialsUtil;
use Yajra\DataTables\Facades\DataTables;
use App\Category;
use App\Utils\Util;
use Modules\Essentials\Entities\EssentialsLeave;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\View;
use Modules\Essentials\Entities\PayrollGroup;
use App\TransactionPayment;
use App\Events\TransactionPaymentAdded;
use App\Utils\BusinessUtil;
use Modules\Essentials\Entities\EssentialsAllowanceAndDeduction;
use App\BusinessLocation;
use Modules\Essentials\Entities\EssentialsUserSalesTarget;

class PayrollController extends Controller

{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $essentialsUtil;
    protected $commonUtil;
    protected $transactionUtil;
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, EssentialsUtil $essentialsUtil, Util $commonUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->essentialsUtil = $essentialsUtil;
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $can_view_all_payroll = auth()->user()->can('essentials.view_all_payroll');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $payrolls = $this->essentialsUtil->getPayrollQuery($business_id);

            if ($can_view_all_payroll) {
                if (!empty(request()->input('user_id'))) {
                    $payrolls->where('transactions.expense_for', request()->input('user_id'));
                }

                if (!empty(request()->input('designation_id'))) {
                    $payrolls->where('dsgn.id', request()->input('designation_id'));
                }

                if (!empty(request()->input('department_id'))) {
                    $payrolls->where('dept.id', request()->input('department_id'));
                }
            }

            if (!$can_view_all_payroll) {
                $payrolls->where('transactions.expense_for', auth()->user()->id);
            }

            if (!empty(request()->input('location_id'))) {
                $payrolls->where('u.location_id', request()->input('location_id'));
            }

            if (!empty(request()->month_year)) {
                $month_year_arr = explode('/', request()->month_year);
                if (count($month_year_arr) == 2) {
                    $month = $month_year_arr[0];
                    $year = $month_year_arr[1];

                    $payrolls->whereDate('transaction_date', $year . '-' .$month . '-01');
                }
            }

            return Datatables::of($payrolls)
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
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Essentials\Http\Controllers\PayrollController@show', [$row->id]) . '" data-container=".view_modal" class="btn-modal"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';

                        if (empty($row->payroll_group_id)) {

                            if (auth()->user()->can('essentials.update_payroll')) {
                                $html .= '<li><a href="' . action('\Modules\Essentials\Http\Controllers\PayrollController@edit', [$row->id]) . '"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';
                            }

                            if (auth()->user()->can('essentials.delete_payroll')) {
                                $html .= '<li><a href="' . action('\Modules\Essentials\Http\Controllers\PayrollController@destroy', [$row->id]) . '" class="delete-payroll"><i class="fa fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</a></li>';
                            }
                        }

                        // $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) . '" class="view_payment_modal"><i class="fa fa-money"></i> ' . __("purchase.view_payments") . '</a></li>';

                        if (empty($row->payroll_group_id) && $row->payment_status != "paid" && auth()->user()->can('essentials.create_payroll')) {
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fa fa-money"></i> ' . __("purchase.add_payment") . '</a></li>';
                        }


                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->addColumn('transaction_date', function ($row) {
                    $transaction_date = \Carbon::parse($row->transaction_date);
                    
                    return $transaction_date->format('F Y');
                })
                ->editColumn('final_total', '<span class="display_currency" data-currency_symbol="true">{{$final_total}}</span>')
                ->filterColumn('user', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status-label no-print" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}
                        </span></a>
                        <span class="print_section">{{__(\'lang_v1.\' . $payment_status)}}</span>
                        '
                )
                ->removeColumn('id')
                ->rawColumns(['action', 'final_total', 'payment_status'])
                ->make(true);
        }

        $employees = [];
        if (auth()->user()->can('essentials.create_payroll')) {
            $employees = $this->__getEmployeesByLocation($business_id);
        }
        $departments = Category::forDropdown($business_id, 'hrm_department');
        $designations = Category::forDropdown($business_id, 'hrm_designation');
        $locations = BusinessLocation::forDropdown($business_id, true, false, true, false);

        return view('essentials::payroll.index')->with(compact('employees', 'departments', 'designations', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.create_payroll')) {
            abort(403, 'Unauthorized action.');
        }

        $employee_ids = request()->input('employee_ids');
        $month_year_arr = explode('/', request()->input('month_year'));
        $location_id = request()->get('primary_work_location');
        $month = $month_year_arr[0];
        $year = $month_year_arr[1];

        $transaction_date = $year . '-' . $month . '-01';

        //check if payrolls exists for the month year
        $payrolls = Transaction::where('business_id', $business_id)
                    ->whereIn('expense_for', $employee_ids)
                    ->whereDate('transaction_date', $transaction_date)
                    ->get();

        $add_payroll_for = array_diff($employee_ids, $payrolls->pluck('expense_for')->toArray());

        if (!empty($add_payroll_for)) {

            $location = BusinessLocation::where('business_id', $business_id)
                            ->find($location_id);

            //initialize required data
            $start_date = $transaction_date;
            $end_date = \Carbon::parse($start_date)->lastOfMonth();
            $month_name = $end_date->format('F');

            $employees = User::where('business_id', $business_id)
                            ->find($add_payroll_for);

            $payrolls = [];
            foreach ($employees as $employee) {

                //get employee info
                $payrolls[$employee->id]['name'] = $employee->user_full_name;
                $payrolls[$employee->id]['essentials_salary'] = $employee->essentials_salary;
                $payrolls[$employee->id]['essentials_pay_period'] = $employee->essentials_pay_period;
                $payrolls[$employee->id]['total_leaves'] = $this->essentialsUtil->getTotalLeavesForGivenDateOfAnEmployee($business_id, $employee->id, $start_date, $end_date->format('Y-m-d'));
                $payrolls[$employee->id]['total_days_worked'] = $this->essentialsUtil->getTotalDaysWorkedForGivenDateOfAnEmployee($business_id, $employee->id, $start_date, $end_date);

                //get total work duration of employee(attendance)
                $payrolls[$employee->id]['total_work_duration'] = $this->essentialsUtil->getTotalWorkDuration('hour', $employee->id, $business_id, $start_date, $end_date->format('Y-m-d'));

                //get total earned commission for employee
                $business_details = $this->businessUtil->getDetails($business_id);
                $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

                $commsn_calculation_type = empty($pos_settings['cmmsn_calculation_type']) || $pos_settings['cmmsn_calculation_type'] == 'invoice_value' ? 'invoice_value' : $pos_settings['cmmsn_calculation_type'];

                $total_commission = 0;
                if ($commsn_calculation_type == 'payment_received') {
                    $payment_details = $this->transactionUtil->getTotalPaymentWithCommission($business_id, $start_date, $end_date, null, $employee->id);
                    //Get Commision
                    $total_commission = $employee->cmmsn_percent * $payment_details['total_payment_with_commission'] / 100;
                } else {
                    $sell_details = $this->transactionUtil->getTotalSellCommission($business_id, $start_date, $end_date, null, $employee->id);
                    $total_commission = $employee->cmmsn_percent * $sell_details['total_sales_with_commission'] / 100;
                }
                
                if ($total_commission > 0) {
                    $payrolls[$employee->id]['allowances']['allowance_names'][] = __('essentials::lang.sale_commission');
                    $payrolls[$employee->id]['allowances']['allowance_amounts'][] = $total_commission;
                    $payrolls[$employee->id]['allowances']['allowance_types'][] = 'fixed';
                    $payrolls[$employee->id]['allowances']['allowance_percents'][] = 0;
                }
                $settings = $this->essentialsUtil->getEssentialsSettings();
                //get total sales added by the employee 
                $sale_totals = $this->transactionUtil->getUserTotalSales($business_id, $employee->id, $start_date, $end_date);

                $total_sales = !empty($settings['calculate_sales_target_commission_without_tax']) && $settings['calculate_sales_target_commission_without_tax'] == 1 ? $sale_totals['total_sales_without_tax'] : $sale_totals['total_sales'];

                //get sales target if exists
                $sales_target = EssentialsUserSalesTarget::where('user_id', $employee->id)
                                                    ->where('target_start', '<=', $total_sales)
                                                    ->where('target_end', '>=', $total_sales)
                                                    ->first();

                $total_sales_target_commission_percent = !empty($sales_target) ? $sales_target->commission_percent : 0;

                $total_sales_target_commission = $this->transactionUtil->calc_percentage($total_sales, $total_sales_target_commission_percent);

                if ($total_sales_target_commission > 0) {
                    $payrolls[$employee->id]['allowances']['allowance_names'][] = __('essentials::lang.sales_target_commission');
                    $payrolls[$employee->id]['allowances']['allowance_amounts'][] = $total_sales_target_commission;
                    $payrolls[$employee->id]['allowances']['allowance_types'][] = 'fixed';
                    $payrolls[$employee->id]['allowances']['allowance_percents'][] = 0;
                }

                //get earnings & deductions of employee
                $allowances_and_deductions = $this->essentialsUtil->getEmployeeAllowancesAndDeductions($business_id, $employee->id, $start_date, $end_date);
                foreach ($allowances_and_deductions as $ad) {
                    if ($ad->type == 'allowance') {
                        $payrolls[$employee->id]['allowances']['allowance_names'][] = $ad->description;
                        $payrolls[$employee->id]['allowances']['allowance_amounts'][] = $ad->amount_type == 'fixed' ?$ad->amount : 0;
                        $payrolls[$employee->id]['allowances']['allowance_types'][] = $ad->amount_type;
                        $payrolls[$employee->id]['allowances']['allowance_percents'][] = $ad->amount_type == 'percent' ? $ad->amount : 0;
                    } else {
                        $payrolls[$employee->id]['deductions']['deduction_names'][] = $ad->description;
                        $payrolls[$employee->id]['deductions']['deduction_amounts'][] = $ad->amount_type == 'fixed' ? $ad->amount : 0;
                        $payrolls[$employee->id]['deductions']['deduction_types'][] = $ad->amount_type;
                        $payrolls[$employee->id]['deductions']['deduction_percents'][] = $ad->amount_type == 'percent' ? $ad->amount : 0;
                    }
                }
            }

            $action = 'create';

            return view('essentials::payroll.create')
                    ->with(compact('month_name', 'transaction_date', 'year', 'payrolls', 'action', 'location'));
        } else {
            return redirect()->action('\Modules\Essentials\Http\Controllers\PayrollController@index')
                ->with('status',
                    [
                        'success' => true,
                        'msg' => __("essentials::lang.payroll_already_added_for_given_user")
                    ]
                );
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.create_payroll')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $transaction_date = $request->input('transaction_date');
            $payrolls = $request->input('payrolls');
            $notify_employee = !empty($request->input('notify_employee')) ? 1 : 0;
            $payroll_group['business_id'] = $business_id;
            $payroll_group['name'] = $request->input('payroll_group_name');
            $payroll_group['status'] = $request->input('payroll_group_status');
            $payroll_group['gross_total'] = $this->transactionUtil->num_uf($request->input('total_gross_amount'));
            $payroll_group['location_id'] = $request->input('location_id');
            $payroll_group['created_by'] = auth()->user()->id;
            
            DB::beginTransaction();

            $payroll_group = PayrollGroup::create($payroll_group);
            $transaction_ids = [];
            foreach ($payrolls as $key => $payroll) {
                $payroll['transaction_date'] = $transaction_date;
                $payroll['business_id'] = $business_id;
                $payroll['created_by'] = auth()->user()->id;
                $payroll['type'] = 'payroll';
                $payroll['payment_status'] = 'due';
                $payroll['status'] = 'final';
                $payroll['total_before_tax'] = $payroll['final_total'];
                $payroll['essentials_amount_per_unit_duration'] = $this->moduleUtil->num_uf($payroll['essentials_amount_per_unit_duration']);

                $allowances_and_deductions = $this->getAllowanceAndDeductionJson($payroll);
                $payroll['essentials_allowances'] = $allowances_and_deductions['essentials_allowances'];
                $payroll['essentials_deductions'] = $allowances_and_deductions['essentials_deductions'];

                //Update reference count
                $ref_count = $this->moduleUtil->setAndGetReferenceCount('payroll');

                    //Generate reference number
                if (empty($payroll['ref_no'])) {
                    $settings = request()->session()->get('business.essentials_settings');
                    $settings = !empty($settings) ? json_decode($settings, true) : [];
                    $prefix = !empty($settings['payroll_ref_no_prefix']) ? $settings['payroll_ref_no_prefix'] : '';
                    $payroll['ref_no'] = $this->moduleUtil->generateReferenceNumber('payroll', $ref_count, null, $prefix);
                }
                unset($payroll['allowance_names'], $payroll['allowance_types'], $payroll['allowance_percent'], $payroll['allowance_amounts'], $payroll['deduction_names'], $payroll['deduction_types'], $payroll['deduction_percent'], $payroll['deduction_amounts'], $payroll['total']);

                $transaction = Transaction::create($payroll);
                $transaction_ids[] = $transaction->id;

                if ($notify_employee && $payroll_group->status == 'final') {
                    $transaction->action = 'created';
                    $transaction->transaction_for->notify(new PayrollNotification($transaction));
                }
            }

            $payroll_group->payrollGroupTransactions()->sync($transaction_ids);

            DB::commit();

            $output = ['success' => true,
                'msg' => __("lang_v1.added_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->action('\Modules\Essentials\Http\Controllers\PayrollController@index')->with('status', $output);
    }

    private function getAllowanceAndDeductionJson($payroll)
    {   
        $allowance_names = $payroll['allowance_names'];
        $allowance_types = $payroll['allowance_types'];
        $allowance_percents = $payroll['allowance_percent'];
        $allowance_names_array = [];
        $allowance_percent_array = [];
        $allowance_amounts = [];

        foreach ($payroll['allowance_amounts'] as $key => $value) {
            if (!empty($allowance_names[$key])) {
                $allowance_amounts[] = $this->moduleUtil->num_uf($value);
                $allowance_names_array[] = $allowance_names[$key];
                $allowance_percent_array[] = !empty($allowance_percents[$key]) ? $this->moduleUtil->num_uf($allowance_percents[$key]) : 0;
            }
        }

        $deduction_names = $payroll['deduction_names'];
        $deduction_types = $payroll['deduction_types'];
        $deduction_percents = $payroll['deduction_percent'];
        $deduction_names_array = [];
        $deduction_percents_array = [];
        $deduction_amounts = [];
        foreach ($payroll['deduction_amounts'] as $key => $value) {
            if (!empty($deduction_names[$key])) {
                $deduction_names_array[] = $deduction_names[$key];
                $deduction_amounts[] = $this->moduleUtil->num_uf($value);
                $deduction_percents_array[] = !empty($deduction_percents[$key]) ? $this->moduleUtil->num_uf($deduction_percents[$key]) : 0;
            }
        }

        $output['essentials_allowances'] = json_encode([
                'allowance_names' => $allowance_names_array,
                'allowance_amounts' => $allowance_amounts,
                'allowance_types' => $allowance_types,
                'allowance_percents' => $allowance_percent_array
            ]);
        $output['essentials_deductions'] = json_encode([
                'deduction_names' => $deduction_names_array,
                'deduction_amounts' => $deduction_amounts,
                'deduction_types' => $deduction_types,
                'deduction_percents' => $deduction_percents_array
            ]);

        return $output;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $query = Transaction::where('business_id', $business_id)
                        ->with(['transaction_for', 'payment_lines']);

        if (!auth()->user()->can('essentials.view_all_payroll')) {
            $query->where('expense_for', auth()->user()->id);
        }
        $payroll = $query->findOrFail($id);

        $transaction_date = \Carbon::parse($payroll->transaction_date);

        $department = Category::where('category_type', 'hrm_department')
                        ->find($payroll->transaction_for->essentials_department_id);

        $designation = Category::where('category_type', 'hrm_designation')
                        ->find($payroll->transaction_for->essentials_designation_id);

        $location = BusinessLocation::where('business_id', $business_id)
                        ->find($payroll->transaction_for->location_id);

        $month_name = $transaction_date->format('F');
        $year = $transaction_date->format('Y');
        $allowances = !empty($payroll->essentials_allowances) ? json_decode($payroll->essentials_allowances, true) : [];
        $deductions = !empty($payroll->essentials_deductions) ? json_decode($payroll->essentials_deductions, true) : [];
        $bank_details = json_decode($payroll->transaction_for->bank_details, true);
        $payment_types = $this->moduleUtil->payment_types();
        $final_total_in_words = $this->commonUtil->numToIndianFormat($payroll->final_total);

        $start_of_month = \Carbon::parse($payroll->transaction_date);
        $end_of_month = \Carbon::parse($payroll->transaction_date)->endOfMonth();
        
        $leaves = EssentialsLeave::where('business_id', $business_id)
                        ->where('user_id', $payroll->transaction_for->id)
                        ->whereDate('start_date', '>=', $start_of_month)
                        ->whereDate('end_date', '<=', $end_of_month)
                        ->get();

        $total_leaves = 0;
        $days_in_a_month = \Carbon::parse($start_of_month)->daysInMonth;
        foreach ($leaves as $key => $leave) {
            $start_date = \Carbon::parse($leave->start_date);
            $end_date = \Carbon::parse($leave->end_date);

            $diff = $start_date->diffInDays($end_date);
            $diff += 1;
            $total_leaves += $diff;
        }

        $total_work_duration = $this->essentialsUtil->getTotalWorkDuration('hour', $payroll->transaction_for->id, $business_id, $start_of_month->format('Y-m-d'), $end_of_month->format('Y-m-d'));

        return view('essentials::payroll.show')
        ->with(compact('payroll', 'month_name', 'allowances', 'deductions', 'year', 'payment_types', 'bank_details', 'designation', 'department', 'final_total_in_words', 'total_leaves', 'days_in_a_month', 'total_work_duration', 'location'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || !$this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.update_payroll')) {
            abort(403, 'Unauthorized action.');
        }

        $payroll = Transaction::where('business_id', $business_id)
                                ->with(['transaction_for'])
                                ->where('type', 'payroll')
                                ->findOrFail($id);

        $transaction_date = \Carbon::parse($payroll->transaction_date);
        $month_name = $transaction_date->format('F');
        $year = $transaction_date->format('Y');
        $allowances = !empty($payroll->essentials_allowances) ? json_decode($payroll->essentials_allowances, true) : [];
        $deductions = !empty($payroll->essentials_deductions) ? json_decode($payroll->essentials_deductions, true) : [];

        return view('essentials::payroll.edit')->with(compact('payroll', 'month_name', 'allowances', 'deductions', 'year'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.update_payroll')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['essentials_duration', 'essentials_amount_per_unit_duration', 'final_total', 'essentials_duration_unit']);

            $input['essentials_amount_per_unit_duration'] = $this->moduleUtil->num_uf($input['essentials_amount_per_unit_duration']);
            $input['total_before_tax'] = $input['final_total'];

            //get pay componentes
            $payroll['allowance_names'] = $request->input('allowance_names');
            $payroll['allowance_types'] = $request->input('allowance_types');
            $payroll['allowance_percent'] = $request->input('allowance_percent');
            $payroll['allowance_amounts'] = $request->input('allowance_amounts');
            $payroll['deduction_names'] = $request->input('deduction_names');
            $payroll['deduction_types'] = $request->input('deduction_types');
            $payroll['deduction_percent'] = $request->input('deduction_percent');
            $payroll['deduction_amounts'] = $request->input('deduction_amounts');
            $payroll['final_total'] = $request->input('final_total');

            $allowances_and_deductions = $this->getAllowanceAndDeductionJson($payroll);
            $input['essentials_allowances'] = $allowances_and_deductions['essentials_allowances'];
            $input['essentials_deductions'] = $allowances_and_deductions['essentials_deductions'];

            DB::beginTransaction();
            $payroll = Transaction::where('business_id', $business_id)
                                ->where('type', 'payroll')
                                ->findOrFail($id);

            $payroll->update($input);

            $payroll->action = 'updated';
            $payroll->transaction_for->notify(new PayrollNotification($payroll));

            $output = ['success' => true,
                            'msg' => __("lang_v1.updated_success")
                        ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->action('\Modules\Essentials\Http\Controllers\PayrollController@index')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.delete_payroll')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $payroll = Transaction::where('business_id', $business_id)
                                ->where('type', 'payroll')
                                ->where('id', $id)
                                ->delete();

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
    }

    public function getAllowanceAndDeductionRow(Request $request)
    {
        if ($request->ajax()) {
            $employee = $request->input('employee_id');
            $type = $request->input('type');

            $ad_row = View::make('essentials::payroll.allowance_and_deduction_row')
                        ->with(compact('type', 'employee'))
                        ->render();

            return $ad_row;
        }
    }

    public function payrollGroupDatatable(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!(auth()->user()->can('superadmin') || $is_admin|| $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $payroll_groups = PayrollGroup::where('essentials_payroll_groups.business_id', $business_id)
                                ->join('users as u', 'u.id', '=', 'essentials_payroll_groups.created_by')
                                ->leftJoin('business_locations as BL', 'essentials_payroll_groups.location_id', '=', 'BL.id')
                                ->select('essentials_payroll_groups.id as id', 'essentials_payroll_groups.name as name', 'essentials_payroll_groups.status as status', 'essentials_payroll_groups.created_at as created_at',
                                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as added_by"), 'essentials_payroll_groups.payment_status as payment_status', 'essentials_payroll_groups.gross_total as gross_total',
                                    'BL.name as location_name'
                                );

            return Datatables::of($payroll_groups)
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
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        $html .= '<li>
                                    <a href="' . action('\Modules\Essentials\Http\Controllers\PayrollController@viewPayrollGroup', [$row->id]) . '" target="_blank">
                                            <i class="fa fa-eye" aria-hidden="true"></i> '
                                            . __("messages.view") .
                                    '</a>
                                </li>';
                        if (auth()->user()->can('essentials.update_payroll')) {
                            $html .= '<li>
                                        <a href="' . action('\Modules\Essentials\Http\Controllers\PayrollController@getEditPayrollGroup', [$row->id]) . '" target="_blank">
                                                <i class="fas fa-edit" aria-hidden="true"></i> '
                                                . __("messages.edit") .
                                        '</a>
                                    </li>';
                        }

                        if ($row->status == 'final' && $row->payment_status != 'paid') {
                            $html .= '<li>
                                    <a href="' . action('\Modules\Essentials\Http\Controllers\PayrollController@addPayment', [$row->id]) . '" target="_blank">
                                            <i class="fas fa-money-check" aria-hidden="true"></i> '
                                            . __("purchase.add_payment") .
                                    '</a>
                                </li>';
                        }

                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->editColumn('status', '
                    @lang("sale.".$status)
                ')
                ->editColumn('created_at', '
                    {{@format_datetime($created_at)}}
                ')
                ->editColumn('gross_total', '
                    @format_currency($gross_total)
                ')
                ->editColumn('location_name', '
                    @if(!empty($location_name))
                        {{$location_name}}
                    @else
                        {{__("report.all_locations")}}
                    @endif
                ')
                ->editColumn(
                    'payment_status',
                    '<span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}
                        </span>
                        '
                )
                ->filterColumn('added_by', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'added_by', 'created_at', 'status', 'gross_total', 'payment_status', 'location_name'])
                ->make(true);
        }
    }

    public function viewPayrollGroup($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!(auth()->user()->can('superadmin') || $is_admin || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        $payroll_group = PayrollGroup::where('business_id', $business_id)
                            ->with(['payrollGroupTransactions', 'payrollGroupTransactions.transaction_for', 'businessLocation', 'business'])
                            ->findOrFail($id);

        $payrolls = [];
        $month_name = null;
        $year = null;
        foreach ($payroll_group->payrollGroupTransactions as $transaction) {

            //payroll info
            if (empty($month_name) && empty($year)) {
                $transaction_date = \Carbon::parse($transaction->transaction_date);
                $month_name = $transaction_date->format('F');
                $year = $transaction_date->format('Y');
            }

            //transaction info
            $payrolls[$transaction->expense_for]['transaction_id'] = $transaction->id;
            $payrolls[$transaction->expense_for]['final_total'] = $transaction->final_total;
            $payrolls[$transaction->expense_for]['payment_status'] = $transaction->payment_status;
            
            //get employee info
            $payrolls[$transaction->expense_for]['employee'] = $transaction->transaction_for->user_full_name;
            $payrolls[$transaction->expense_for]['bank_details'] = json_decode($transaction->transaction_for->bank_details, true);
        }

        return view('essentials::payroll.view_payroll_group')
            ->with(compact('payroll_group', 'month_name', 'year', 'payrolls'));
    }

    public function getEditPayrollGroup($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || auth()->user()->can('essentials.update_payroll') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        $payroll_group = PayrollGroup::where('business_id', $business_id)
                            ->with(['payrollGroupTransactions', 'payrollGroupTransactions.transaction_for', 'businessLocation'])
                            ->findOrFail($id);

        //payroll location
        $location = $payroll_group->businessLocation;

        $payrolls = [];
        $transaction_date = null;
        $month_name = null;
        $year = null;
        foreach ($payroll_group->payrollGroupTransactions as $transaction) {

            //payroll info
            if (empty($transaction_date) && empty($month_name) && empty($year)) {
                $transaction_date = \Carbon::parse($transaction->transaction_date);
                $month_name = $transaction_date->format('F');
                $year = $transaction_date->format('Y');
                $start_date = \Carbon::parse($transaction->transaction_date);
                $end_date = \Carbon::parse($start_date)->lastOfMonth();
            }
            //transaction info
            $payrolls[$transaction->expense_for]['transaction_id'] = $transaction->id;

            //get employee info
            $payrolls[$transaction->expense_for]['name'] = $transaction->transaction_for->user_full_name;
            $payrolls[$transaction->expense_for]['staff_note'] = $transaction->staff_note;
            $payrolls[$transaction->expense_for]['essentials_amount_per_unit_duration'] = $transaction->essentials_amount_per_unit_duration;
            $payrolls[$transaction->expense_for]['essentials_duration'] = $transaction->essentials_duration;
            $payrolls[$transaction->expense_for]['essentials_duration_unit'] = $transaction->essentials_duration_unit;
            $payrolls[$transaction->expense_for]['total_leaves'] = $this->essentialsUtil->getTotalLeavesForGivenDateOfAnEmployee($business_id, $transaction->expense_for, $start_date->format('Y-m-d'), $end_date->format('Y-m-d'));
            $payrolls[$transaction->expense_for]['total_days_worked'] = $this->essentialsUtil->getTotalDaysWorkedForGivenDateOfAnEmployee($business_id, $transaction->expense_for, $start_date, $end_date);
            
            //get total work duration of employee(attendance)
            $payrolls[$transaction->expense_for]['total_work_duration'] = $this->essentialsUtil->getTotalWorkDuration('hour', $transaction->expense_for, $business_id, $start_date->format('Y-m-d'), $end_date->format('Y-m-d'));

            //get earnings employee
            $allowances = !empty($transaction->essentials_allowances) ? json_decode($transaction->essentials_allowances, true) : [];

            if (empty($allowances['allowance_names']) && empty($allowances['allowance_amounts'])) {
                $allowances['allowance_names'][] = '';
                $allowances['allowance_amounts'][] = 0;
                $allowances['allowance_types'][] = 'fixed';
                $allowances['allowance_percents'][] = '';
            }
            $payrolls[$transaction->expense_for]['allowances'] = $allowances;

            //get deductions of employee
            $deductions = !empty($transaction->essentials_deductions) ? json_decode($transaction->essentials_deductions, true) : [];

            if (empty($deductions['deduction_names']) && empty($deductions['deduction_amounts'])) {
                $deductions['deduction_names'][] = '';
                $deductions['deduction_amounts'][] = 0;
                $deductions['deduction_types'][] = 'fixed';
                $deductions['deduction_percents'][] = '';
            }

            $payrolls[$transaction->expense_for]['deductions'] = $deductions;
        }

        $action = 'edit';
        return view('essentials::payroll.create')
            ->with(compact('month_name', 'transaction_date', 'year', 'payrolls', 'payroll_group', 'action', 'location'));
    }

    public function getUpdatePayrollGroup(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || auth()->user()->can('essentials.update_payroll') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $transaction_date = $request->input('transaction_date');
            $payrolls = $request->input('payrolls');
            $notify_employee = !empty($request->input('notify_employee')) ? 1 : 0;

            $payroll_group_id = $request->input('payroll_group_id');
            $pg_input['name'] = $request->input('payroll_group_name');
            $pg_input['status'] = $request->input('payroll_group_status');
            $pg_input['gross_total'] = $this->transactionUtil->num_uf($request->input('total_gross_amount'));

            DB::beginTransaction();
                $payroll_group = PayrollGroup::where('business_id', $business_id)
                                    ->findOrFail($payroll_group_id);

                $payroll_group->update($pg_input);

                foreach ($payrolls as $key => $payroll) {

                    $transaction_id = $payroll['transaction_id'];

                    $payroll['total_before_tax'] = $payroll['final_total'];
                    $payroll['essentials_amount_per_unit_duration'] = $this->moduleUtil->num_uf($payroll['essentials_amount_per_unit_duration']);

                    $allowances_and_deductions = $this->getAllowanceAndDeductionJson($payroll);
                    $payroll['essentials_allowances'] = $allowances_and_deductions['essentials_allowances'];
                    $payroll['essentials_deductions'] = $allowances_and_deductions['essentials_deductions'];

                    unset($payroll['allowance_names'], $payroll['allowance_types'], $payroll['allowance_percent'], $payroll['allowance_amounts'], $payroll['deduction_names'], $payroll['deduction_types'], $payroll['deduction_percent'], $payroll['deduction_amounts'], $payroll['total'], $payroll['transaction_id']);

                    $payroll_trans = Transaction::where('business_id', $business_id)
                                        ->where('type', 'payroll')
                                        ->find($transaction_id);

                    if (!empty($payroll_trans)) {
                        $payroll_trans->update($payroll);

                        if ($notify_employee && $payroll_group->status == 'final') {
                            $payroll_trans->action = 'updated';
                            $payroll_trans->transaction_for->notify(new PayrollNotification($payroll_trans));
                        }
                    }
                }
            DB::commit();
            $output = ['success' => true,
                'msg' => __("lang_v1.updated_success")
            ];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return redirect()->action('\Modules\Essentials\Http\Controllers\PayrollController@index')->with('status', $output);
    }

    public function addPayment($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!(auth()->user()->can('superadmin') || $is_admin || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $payroll_group = PayrollGroup::where('business_id', $business_id)
                            ->with(['payrollGroupTransactions', 'payrollGroupTransactions.transaction_for', 'businessLocation', 'business'])
                            ->findOrFail($id);

        $payrolls = [];
        $month_name = null;
        $year = null;
        foreach ($payroll_group->payrollGroupTransactions as $transaction) {

            //payroll info
            if (empty($month_name) && empty($year)) {
                $transaction_date = \Carbon::parse($transaction->transaction_date);
                $month_name = $transaction_date->format('F');
                $year = $transaction_date->format('Y');
            }

            //transaction info
            $payrolls[$transaction->expense_for]['transaction_id'] = $transaction->id;
            $payrolls[$transaction->expense_for]['final_total'] = $transaction->final_total;
            $payrolls[$transaction->expense_for]['payment_status'] = $transaction->payment_status;
            $payrolls[$transaction->expense_for]['paid_on'] = \Carbon::now();

            //get employee info
            $payrolls[$transaction->expense_for]['employee'] = $transaction->transaction_for->user_full_name;
            $payrolls[$transaction->expense_for]['employee_id'] = $transaction->transaction_for->id;
            $payrolls[$transaction->expense_for]['bank_details'] = json_decode($transaction->transaction_for->bank_details, true);
        }

        $payment_types = $this->transactionUtil->payment_types();
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);
        return view('essentials::payroll.pay_payroll_group')
            ->with(compact('payroll_group', 'month_name', 'year', 'payrolls', 'payment_types', 'accounts'));
    }

    public function postAddPayment(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!(auth()->user()->can('superadmin') || $is_admin || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $payments = $request->input('payments');
            $payroll_group_id = $request->input('payroll_group_id');
            
            foreach ($payments as $employee_id => $payment) {
                $transaction = Transaction::where('business_id', $business_id)->findOrFail($payment['transaction_id']);
                $transaction_before = $transaction->replicate();
                if ($transaction->payment_status != 'paid' && !empty($payment['final_total']) && !empty($payment['method'])) {
                    $input['method'] = $payment['method'];
                    $input['card_number'] = $payment['card_number'];
                    $input['card_holder_name'] = $payment['card_holder_name'];
                    $input['card_transaction_number'] = $payment['card_transaction_number'];
                    $input['card_type'] = $payment['card_type'];
                    $input['card_month'] = $payment['card_month'];
                    $input['card_year'] = $payment['card_year'];
                    $input['card_security'] = $payment['card_security'];
                    $input['cheque_number'] = $payment['cheque_number'];
                    $input['bank_account_number'] = $payment['bank_account_number'];
                    $input['business_id'] = $business_id;
                    $input['paid_on'] = $this->transactionUtil->uf_date($payment['paid_on'], true);
                    $input['transaction_id'] = $payment['transaction_id'];
                    $input['amount'] = $this->transactionUtil->num_uf($payment['final_total']);
                    $input['created_by'] = auth()->user()->id;

                    if ($input['method'] == 'custom_pay_1') {
                        $input['transaction_no'] = $payment['transaction_no_1'];
                    } elseif ($input['method'] == 'custom_pay_2') {
                        $input['transaction_no'] = $payment['transaction_no_2'];
                    } elseif ($input['method'] == 'custom_pay_3') {
                        $input['transaction_no'] = $payment['transaction_no_3'];
                    }

                    if (!empty($payment['account_id']) && $input['method'] != 'advance') {
                        $input['account_id'] = $payment['account_id'];
                    }

                    DB::beginTransaction();
                        $ref_count = $this->transactionUtil->setAndGetReferenceCount('purchase_payment');
                        // Generate reference number
                        $input['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber('purchase_payment', $ref_count);

                        $tp = TransactionPayment::create($input);
                        $input['transaction_type'] = $transaction->type;
                        event(new TransactionPaymentAdded($tp, $input));

                        //update payment status
                        $payment_status = $this->transactionUtil->updatePaymentStatus($input['transaction_id']);
                        $transaction->payment_status = $payment_status;
                        $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);
                    DB::commit();

                    //unset transaction type after insert data
                    unset($input['transaction_type']);
                }
            }

            $this->_updatePayrollGroupPaymentStatus($payroll_group_id, $business_id);

            $output = ['success' => true,
                'msg' => __('purchase.payment_added_success')
            ];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return redirect()->action('\Modules\Essentials\Http\Controllers\PayrollController@index')->with('status', $output);
    }

    protected function _updatePayrollGroupPaymentStatus($payroll_group_id, $business_id)
    {
        $payroll_group = PayrollGroup::where('business_id', $business_id)
                            ->with(['payrollGroupTransactions'])
                            ->findOrFail($payroll_group_id);

        $total_transaction = count($payroll_group->payrollGroupTransactions);
        $total_paid = $payroll_group->payrollGroupTransactions->where('payment_status', 'paid')->count();
        $total_due = $payroll_group->payrollGroupTransactions->where('payment_status', '!=', 'paid')->count();

        if ($total_transaction == $total_paid) {
            $payment_status = 'paid';
        } elseif ($total_transaction == $total_due) {
            $payment_status = 'due';
        } else {
            $payment_status = 'partial';
        }
        
        $payroll_group->payment_status = $payment_status;
        $payroll_group->save();
    }

    /**
     * List payrolls & pay components
     * of an user
     *
     * @return Response
     */
    public function getMyPayrolls(Request $request)
    {   
        $business_id = request()->session()->get('user.business_id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {

            $payrolls = $this->essentialsUtil->getPayrollQuery($business_id);

            $payrolls->where('transactions.expense_for', auth()->user()->id);

            return Datatables::of($payrolls)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<a href="#" data-href="' . action('\Modules\Essentials\Http\Controllers\PayrollController@show', [$row->id]) . '" data-container=".view_modal" class="btn-modal btn-info btn btn-sm">
                            <i class="fa fa-eye" aria-hidden="true"></i> '
                            . __("messages.view") .
                            '</a>';
                        return $html;
                    }
                )
                ->addColumn('transaction_date', function ($row) {
                    $transaction_date = \Carbon::parse($row->transaction_date);
                    
                    return $transaction_date->format('F Y');
                })
                ->editColumn('final_total', '<span class="display_currency" data-currency_symbol="true">{{$final_total}}</span>')
                ->editColumn(
                    'payment_status',
                    '<span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}
                        </span>'
                )
                ->removeColumn('id')
                ->rawColumns(['action', 'final_total', 'payment_status'])
                ->make(true);
        }

        $pay_components = EssentialsAllowanceAndDeduction::join('essentials_user_allowance_and_deductions as EUAD', 'essentials_allowances_and_deductions.id', '=', 'EUAD.allowance_deduction_id')
                ->where('essentials_allowances_and_deductions.business_id', $business_id)
                ->where('EUAD.user_id', auth()->user()->id)
                ->get();

        return view('essentials::payroll.partials.user_payrolls')
            ->with(compact('pay_components'));
    }

    public function getEmployeesBasedOnLocation(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $location_id = $request->get('location_id');

            $employees = $this->__getEmployeesByLocation($business_id, $location_id);

            //dynamically generate dropdown
            $employees_html = \View::make('essentials::payroll.partials.employee_dropdown')
                                ->with(compact('employees'))
                                ->render();
            $output = [
                'success' => true,
                'msg' => __("lang_v1.success"),
                'employees_html' => $employees_html
            ];
        } catch (Exception $e) {
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    private function __getEmployeesByLocation($business_id, $location_id = null)
    {
        $query = User::where('business_id', $business_id)
                    ->user();

        if (!empty($location_id)) {
            $query->where('location_id', $location_id);
        } else {
            $query->whereNull('location_id');
        }
                    
        $users = $query->select('id', DB::raw("CONCAT(COALESCE(surname, ''),' ',COALESCE(first_name, ''),' ',COALESCE(last_name,'')) as full_name"))->get();

        $employees = $users->pluck('full_name', 'id')->toArray();

        return $employees;
    }
}
