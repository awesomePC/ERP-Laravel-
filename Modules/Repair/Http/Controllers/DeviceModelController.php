<?php

namespace Modules\Repair\Http\Controllers;

use App\Brands;
use App\Category;
use App\Transaction;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Repair\Entities\DeviceModel;
use Yajra\DataTables\Facades\DataTables;
use Modules\Repair\Entities\JobSheet;

class DeviceModelController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $models = DeviceModel::with('Device', 'Brand')
                        ->where('business_id', $business_id)
                        ->select('*');

            if (!empty($request->get('brand_id'))) {
                $models->where('brand_id', $request->get('brand_id'));
            }

            if (!empty($request->get('device_id'))) {
                $models->where('device_id', $request->get('device_id'));
            }

            return Datatables::of($models)
                    ->addColumn('action', function ($row) {
                        $html = '<div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                        '.__("messages.action").'
                                        <span class="caret"></span>
                                        <span class="sr-only">
                                        '.__("messages.action").'
                                        </span>
                                    </button>
                                    ';

                        $html .= '<ul class="dropdown-menu dropdown-menu-left" role="menu">
                                <li>
                                    <a data-href="' . action('\Modules\Repair\Http\Controllers\DeviceModelController@edit', ['id' => $row->id]) . '" class="cursor-pointer edit_device_model">
                                        <i class="fa fa-edit"></i>
                                        '.__("messages.edit").'
                                    </a>
                                </li>
                                <li>
                                    <a data-href="' . action('\Modules\Repair\Http\Controllers\DeviceModelController@destroy', ['id' => $row->id]) . '"  id="delete_a_model" class="cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                        '.__("messages.delete").'
                                    </a>
                                </li>
                                </ul>';

                        $html .= '
                                </div>';

                        return $html;
                    })
                ->editColumn('repair_checklist', function ($row) {
                    $checklist = '';
                    if (!empty($row->repair_checklist)) {
                        $checklist = explode('|', $row->repair_checklist);
                    }

                    return $checklist;
                })
                ->editColumn('device_id', function ($row) {
                    return optional($row->Device)->name;
                })
                ->editColumn('brand_id', function ($row) {
                    return optional($row->Brand)->name;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'repair_checklist', 'device_id', 'brand_id'])
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

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        $brands = Brands::forDropdown($business_id, false, true);
        $devices = Category::forDropdown($business_id, 'device');

        return view('repair::device_model.create')
            ->with(compact('devices', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('name', 'brand_id', 'device_id', 'repair_checklist');
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->user()->id;

            DeviceModel::create($input);

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
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('repair::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        $model = DeviceModel::where('business_id', $business_id)
                    ->findOrFail($id);

        $brands = Brands::forDropdown($business_id, false, true);
        $devices = Category::forDropdown($business_id, 'device');

        return view('repair::device_model.edit')
            ->with(compact('devices', 'brands', 'model'));
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

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('name', 'brand_id', 'device_id', 'repair_checklist');

            $model = DeviceModel::where('business_id', $business_id)
                            ->findOrFail($id);

            $model->update($input);

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

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $model = DeviceModel::where('business_id', $business_id)
                            ->findOrFail($id);

                $model->delete();

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

    /**
     * get models for particular device
     * @param $request
     * @return Response
     */
    public function getDeviceModels(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $device_id = $request->get('device_id');
        $brand_id = $request->get('brand_id');

        $query = DeviceModel::where('business_id', $business_id)
                    ->where('device_id', $device_id);

        if (!empty($brand_id)) {
            $query->where('brand_id', $brand_id);
        }

        $models = $query->pluck('name', 'id');

        //dynamically generate dropdown
        $model_html = View::make('repair::device_model.partials.device_model_drodown')
                        ->with(compact('models'))
                        ->render();

        return $model_html;
    }

    /**
     * get repair checklist for particular models
     * @param $request
     * @return Response
     */
    public function getRepairChecklists(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $model_id = $request->get('model_id');
        $transaction_id = $request->get('transaction_id');
        $job_sheet_id = $request->get('job_sheet_id');

        $device_model = DeviceModel::where('business_id', $business_id)
                            ->find($model_id);

        $selected_checklist = [];
        //used while editing and creating invoivce
        if (!empty($transaction_id)) {
            $transaction = Transaction::where('business_id', $business_id)
                            ->where('type', 'sell')
                            ->find($transaction_id);

            $selected_checklist = !empty($transaction->repair_checklist) ? json_decode($transaction->repair_checklist, true) : [];
        }

        //used while adding/editing/creating job sheet and its invoivce
        if (!empty($job_sheet_id)) {
            $job_sheet = JobSheet::where('business_id', $business_id)
                            ->find($job_sheet_id);

            $selected_checklist = !empty($job_sheet->checklist) ? $job_sheet->checklist : [];
        }

        $checklists = [];
        if (!empty($device_model) && !empty($device_model->repair_checklist)) {
            $checklists = explode('|', $device_model->repair_checklist);
        }
        
        //dynamically generate dropdown
        $checklists_html = View::make('repair::repair.partials.checklists')
                            ->with(compact('checklists', 'selected_checklist'))
                            ->render();

        return $checklists_html;
    }
}
