<?php

namespace Modules\AssetManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\AssetManagement\Entities\Asset;
use Modules\AssetManagement\Entities\AssetMaintenance;
use Modules\AssetManagement\Utils\AssetUtil;
use App\Utils\Util;
use App\Utils\ModuleUtil;
use App\Media;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\User;

class AssetMaitenanceController extends Controller
{
    
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $commonUtil;
    protected $assetUtil;

    /**
     * Constructor
     *
     */
    public function __construct(ModuleUtil $moduleUtil, Util $commonUtil, AssetUtil $assetUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->assetUtil = $assetUtil;

        $this->maintenanceStatuses = $this->assetUtil->maintenanceStatuses();

        $this->maintenancePriorities = $this->assetUtil->maintenancePriorities();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!((auth()->user()->can('asset.view_all_maintenance') && auth()->user()->can('asset.view_own_maintenance')) || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $query = AssetMaintenance::with(['asset', 'asset.warranties'])
                        ->where('asset_maintenances.business_id', $business_id)
                        ->leftJoin('users as u', 'u.id', '=', 'asset_maintenances.assigned_to')
                        ->leftJoin('users as u1', 'u1.id', '=', 'asset_maintenances.created_by');

            if (!auth()->user()->can('asset.view_all_maintenance') && auth()->user()->can('asset.view_own_maintenance')) {
                $query->where(function($q){
                    $q->where('asset_maintenances.created_by', auth()->user()->id)
                    ->orWhere('asset_maintenances.assigned_to', auth()->user()->id);
                });
            }

            if (!empty(request()->input('status'))) {
                $query->where('asset_maintenances.status', request()->input('status'));
            }

            if (!empty(request()->input('priority'))) {
                $query->where('asset_maintenances.priority', request()->input('priority'));
            }

            if (!empty(request()->input('assigned_to'))) {
                $query->where('asset_maintenances.assigned_to', request()->input('assigned_to'));
            }

            $asset_maintenances = $query->select([
                        'asset_maintenances.asset_id',
                        'asset_maintenances.maitenance_id',
                        'asset_maintenances.status',
                        'asset_maintenances.priority',
                        'asset_maintenances.id',
                        'asset_maintenances.details',
                        'asset_maintenances.created_at',
                        'u.id as assigned_user_id',
                        DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as assigned_to_user"),
                        DB::raw("CONCAT(COALESCE(u1.surname, ''), ' ', COALESCE(u1.first_name, ''), ' ', COALESCE(u1.last_name, '')) as created_by_user")
                    ]);

            $now = \Carbon::now();
            return Datatables::of($asset_maintenances)
                ->addColumn('asset_name', function($row){
                    return $row->asset->name ?? '';
                })
                ->addColumn('warranty', function($row) use ($now) {
                    $warranty = null;

                    $html = '';
                    foreach ($row->asset->warranties as $w) {
                        $start_date = \Carbon::parse($w->start_date);
                        $end_date = \Carbon::parse($w->end_date);
                        if ($now->between($start_date, $end_date)) {
                            $warranty = $w;

                            $html = '<span class="label bg-green">' . __('assetmanagement::lang.in_warranty') . '</span><br>';

                            $html .= '<small>' . $this->commonUtil->format_date($w->start_date) . ' ~ ' . $this->commonUtil->format_date($w->end_date) . '</br>'; 
                            $html .= '(' . $now->diffInDays($end_date, false) . ' ' . __('assetmanagement::lang.days_left') . ') </small>';

                            break;
                        }
                    }

                    if (empty($warranty)) {
                        $html = '<span class="label bg-red">' . __('assetmanagement::lang.not_in_warranty') . '</span>';
                    }

                    return $html;
                })
                ->editColumn('assigned_to_user', '@if(empty($assigned_user_id))<small class="label bg-lightgray text-danger">@lang("assetmanagement::lang.unassigned")</small> @else {{$assigned_to_user}} @endif')
                ->filterColumn('assigned_to_user', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('created_by_user', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u1.surname, ''), ' ', COALESCE(u1.first_name, ''), ' ', COALESCE(u1.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->editColumn('status', function($row){
                    $statuses = $this->maintenanceStatuses;
                    $html = '';

                    if (!empty($statuses[$row->status])) {
                       $html = '<span class="label ' . $statuses[$row->status]['class'] . '" >' . $statuses[$row->status]['label'] . '</span>';
                    }

                    return $html;
                })
                ->editColumn('priority', function($row){
                    $priorities = $this->maintenancePriorities;
                    $html = '';

                    if (!empty($priorities[$row->priority])) {
                       $html = '<span class="label ' . $priorities[$row->priority]['class'] . '" >' . $priorities[$row->priority]['label'] . '</span>';
                    }

                    return $html;
                })
                ->editColumn('created_at', function($row){
                    $datetime = $this->commonUtil->format_date($row->created_at, true);
                    $datetime .= '<br><small class="text-muted">' . \Carbon::parse($row->created_at)->diffForHumans() . '</small>';

                    return $datetime;
                })
                ->addColumn('action', function($row){
                    $html = '<button type="button" class="btn btn-primary btn-xs edit_maintenance" data-href="' . action('\Modules\AssetManagement\Http\Controllers\AssetMaitenanceController@edit', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __('messages.edit') . '</button>';

                    $html .= ' <button type="button" data-href="' . action('\Modules\AssetManagement\Http\Controllers\AssetMaitenanceController@destroy', [$row->id]) . '"  id="delete_asset_maintenance" class="btn btn-danger btn-xs">
                                    <i class="fas fa-trash"></i>
                                    '.__("messages.delete").'
                                </button>';

                    return $html;


                })
                ->removeColumn('id')
                ->rawColumns(['status', 'priority', 'action', 'created_at', 
                    'assigned_to_user', 'warranty'])
                ->make(true);
        }

        $statuses = [];
        foreach ($this->maintenanceStatuses as $key => $value) {
            $statuses[$key] = $value['label'];
        }

        $priorities = [];
        foreach ($this->maintenancePriorities as $key => $value) {
            $priorities[$key] = $value['label'];
        }

        $users = User::forDropdown($business_id, false);

        return view('assetmanagement::asset_maintenance.index')->with(compact('statuses', 'priorities', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!((auth()->user()->can('asset.view_all_maintenance') && auth()->user()->can('asset.view_own_maintenance')) || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $asset_id = request()->input('asset_id');

            $asset = Asset::with(['warranties'])
                        ->where('business_id', $business_id)
                        ->findOrfail($asset_id);

            $statuses = [];
            foreach ($this->maintenanceStatuses as $key => $value) {
                $statuses[$key] = $value['label'];
            }

            $priorities = [];
            foreach ($this->maintenancePriorities as $key => $value) {
                $priorities[$key] = $value['label'];
            }

            return view('assetmanagement::asset_maintenance.create')
                    ->with(compact('asset', 'statuses', 'priorities'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!((auth()->user()->can('asset.view_all_maintenance') && auth()->user()->can('asset.view_own_maintenance')) || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('status', 'priority', 'asset_id', 'maintenance_note');

            $ref_count = $this->commonUtil->setAndGetReferenceCount('asset_maintenance', $business_id);
            $asset_settings = $this->assetUtil->getAssetSettings($business_id);

            DB::beginTransaction();
            $asset_maintenance_prefix = $asset_settings['asset_maintenance_prefix'] ?? null;
            $input['maitenance_id'] = $this->commonUtil->generateReferenceNumber('asset_maintenance', $ref_count, null, $asset_maintenance_prefix);
            $input['business_id'] = $business_id;
            $input['created_by'] = auth()->user()->id;

            $asset_maintenance = AssetMaintenance::create($input);

            //upload attachments
            if ($request->has('attachments')) {
                Media::uploadMedia($business_id, $asset_maintenance, $request, 'attachments');
            }

            //send notification
            $this->assetUtil->sendAssetSentForMaintenanceNotification($asset_maintenance->id);

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
            ];
        }

        return redirect()
            ->action('\Modules\AssetManagement\Http\Controllers\AssetMaitenanceController@index')
            ->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('assetmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!((auth()->user()->can('asset.view_all_maintenance') && auth()->user()->can('asset.view_own_maintenance')) || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $maintenance = AssetMaintenance::where('business_id', $business_id)
                        ->with(['media'])
                        ->findOrfail($id);

            $statuses = [];
            foreach ($this->maintenanceStatuses as $key => $value) {
                $statuses[$key] = $value['label'];
            }

            $priorities = [];
            foreach ($this->maintenancePriorities as $key => $value) {
                $priorities[$key] = $value['label'];
            }

            $users = User::forDropdown($business_id, false);

            return view('assetmanagement::asset_maintenance.edit')
                    ->with(compact('maintenance', 'statuses', 'priorities', 'users'));
        }
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
        if (!((auth()->user()->can('asset.view_all_maintenance') && auth()->user()->can('asset.view_own_maintenance')) || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('status', 'priority', 'details', 'assigned_to');

            $asset_maintenance = AssetMaintenance::find($id);

            $previous_assigned_to = $asset_maintenance->assigned_to;

            DB::beginTransaction();

            $asset_maintenance->update($input);

            //upload attachments
            if ($request->has('attachments')) {
                Media::uploadMedia($business_id, $asset_maintenance, $request, 'attachments');
            }

            //if assigned user changed send notification
            if (!empty($input['assigned_to']) && 
                $previous_assigned_to !== $input['assigned_to']) {
                $this->assetUtil->sendAssetAssignedForMaintenanceNotification($asset_maintenance->id);
            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()
            ->action('\Modules\AssetManagement\Http\Controllers\AssetMaitenanceController@index')
            ->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!((auth()->user()->can('asset.view_all_maintenance') && auth()->user()->can('asset.view_own_maintenance')) || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $asset_maintenance = AssetMaintenance::where('business_id', $business_id)
                            ->findOrfail($id);

                $asset_maintenance->delete();
                $asset_maintenance->media()->delete();

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
    }
}
