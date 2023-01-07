<?php

namespace Modules\Essentials\Http\Controllers;

use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsAllowanceAndDeduction;
use Modules\Essentials\Utils\EssentialsUtil;
use Yajra\DataTables\Facades\DataTables;

class EssentialsAllowanceAndDeductionController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $essentialsUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, EssentialsUtil $essentialsUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->essentialsUtil = $essentialsUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('essentials.add_allowance_and_deduction') && !auth()->user()->can('essentials.view_allowance_and_deduction')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $allowances = EssentialsAllowanceAndDeduction::where('business_id', $business_id)
                ->with('employees');
            return Datatables::of($allowances)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if (auth()->user()->can('essentials.add_allowance_and_deduction')) {
                            $html .= '<button data-href="' . action('\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController@edit', [$row->id]) . '" data-container="#add_allowance_deduction_modal" class="btn-modal btn btn-primary btn-xs"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</button>';

                            $html .= '&nbsp; <button data-href="' . action('\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController@destroy', [$row->id]) . '" class="delete-allowance btn btn-danger btn-xs"><i class="fa fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</button>';
                        }

                        return $html;
                    }
                )
                ->editColumn('applicable_date', function ($row) {
                    return $this->essentialsUtil->format_date($row->applicable_date);
                })
                ->editColumn('type', '{{__("essentials::lang." . $type)}}')
                ->editColumn('amount', '<span class="display_currency" data-currency_symbol="false">{{$amount}}</span> @if($amount_type =="percent") % @endif')
                ->editColumn('employees', function ($row) {
                    $employees = [];
                    foreach ($row->employees as $employee) {
                        $employees[] = $employee->user_full_name;
                    }
                    return implode(', ', $employees);
                })
                ->rawColumns(['action', 'amount'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) || !auth()->user()->can('essentials.add_allowance_and_deduction')) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::forDropdown($business_id, false);

        return view('essentials::allowance_deduction.create')->with(compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) || !auth()->user()->can('essentials.add_allowance_and_deduction')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['description', 'type', 'amount', 'amount_type', 'applicable_date']);
            $input['business_id'] = $business_id;
            $input['amount'] = $this->moduleUtil->num_uf($input['amount']);
            $input['applicable_date'] = !empty($input['applicable_date']) ? $this->essentialsUtil->uf_date($input['applicable_date']) : null;
            $allowance = EssentialsAllowanceAndDeduction::create($input);
            $allowance->employees()->sync($request->input('employees'));

            $output = ['success' => true,
                        'msg' => __("lang_v1.added_success")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                        'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                    ];
        }

        return $output;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) || !auth()->user()->can('essentials.add_allowance_and_deduction')) {
            abort(403, 'Unauthorized action.');
        }

        return view('essentials::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) || !auth()->user()->can('essentials.add_allowance_and_deduction')) {
            abort(403, 'Unauthorized action.');
        }

        $allowance = EssentialsAllowanceAndDeduction::where('business_id', $business_id)
                    ->with('employees')
                    ->findOrFail($id);
        $users = User::forDropdown($business_id, false);

        $selected_users = [];
        foreach ($allowance->employees as $employee) {
            $selected_users[] = $employee->id;
        }

        $applicable_date = !empty($allowance->applicable_date) ? $this->essentialsUtil->format_date($allowance->applicable_date) : null;

        return view('essentials::allowance_deduction.edit')
                ->with(compact('allowance', 'users', 'selected_users', 'applicable_date'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) || !auth()->user()->can('essentials.add_allowance_and_deduction')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['description', 'type', 'amount', 'amount_type', 'applicable_date']);
            $input['amount'] = $this->moduleUtil->num_uf($input['amount']);
            $input['applicable_date'] = !empty($input['applicable_date']) ? $this->essentialsUtil->uf_date($input['applicable_date']) : null;
            $allowance = EssentialsAllowanceAndDeduction::findOrFail($id);
            $allowance->update($input);

            $allowance->employees()->sync($request->input('employees'));

            $output = ['success' => true,
                        'msg' => __("lang_v1.updated_success")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                        'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                    ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) || !auth()->user()->can('essentials.add_allowance_and_deduction')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                EssentialsAllowanceAndDeduction::where('business_id', $business_id)
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
}
