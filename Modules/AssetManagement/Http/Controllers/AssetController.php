<?php

namespace Modules\AssetManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use Modules\AssetManagement\Entities\Asset;
use Illuminate\Support\Facades\DB;
use App\Utils\Util;
use App\Media;
use App\Category;
use App\BusinessLocation;
use Yajra\DataTables\Facades\DataTables;
use Modules\AssetManagement\Utils\AssetUtil;
use Modules\AssetManagement\Entities\AssetWarranty;
use Modules\AssetManagement\Entities\AssetTransaction;

class AssetController extends Controller
{   
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $commonUtil;
    protected $assetUtil;
    protected $purchaseTypes;

    /**
     * Constructor
     *
     */
    public function __construct(ModuleUtil $moduleUtil, Util $commonUtil, AssetUtil $assetUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->assetUtil = $assetUtil;

        $this->purchaseTypes = [
            'owned' => __('assetmanagement::lang.owned'),
            'rented' => __('assetmanagement::lang.rented'),
            'leased' => __('assetmanagement::lang.leased')
        ];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        $purchase_types = $this->purchaseTypes;

        if ($request->ajax()) {
            $assets = Asset::with(['media', 'warranties', 'maintenances'])
                        ->leftJoin('categories as CAT', 'assets.category_id',
                            '=', 'CAT.id')
                        ->leftJoin('business_locations as BL', 'assets.location_id',
                            '=', 'BL.id')
                        ->leftJoin('asset_transactions as AT', function ($join) {
                            $join->on('assets.id', '=', 'AT.asset_id')
                                ->where('transaction_type', 'allocate');
                        })
                        ->where('assets.business_id', $business_id)
                        ->select('asset_code', 'assets.name as asset', 'assets.quantity as quantity',
                        'model', 'purchase_date',
                        'unit_price', 'is_allocatable',
                        'CAT.name as category', 'BL.name as location',
                        'assets.id as id', DB::raw('SUM(COALESCE(AT.quantity, 0)) as allocated_qty'),
                        DB::raw('(SELECT SUM(COALESCE(AR.quantity, 0)) FROM asset_transactions AS AR WHERE(AR.asset_id=assets.id AND AR.transaction_type=\'revoke\')) as revoked_qty'),
                        'assets.description as description'
                        )
                        ->groupBy('id');

            if (!empty(request()->input('location_id'))) {
                $assets->where('assets.location_id', request()->input('location_id'));
            }
            if (!empty(request()->input('category_id'))) {
                $assets->where('assets.category_id', request()->input('category_id'));
            }
            if (!empty(request()->input('purchase_type'))) {
                $assets->where('assets.purchase_type', request()->input('purchase_type'));
            }
            if (!empty(request()->input('is_allocatable'))) {
                $assets->where('assets.is_allocatable', 1);
            }

            $now = \Carbon::now();
            return Datatables::of($assets)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                        '.__("messages.action").'
                                        <span class="caret"></span>
                                        <span class="sr-only">
                                        '.__("messages.action").'
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                    ';

                    if ($row->is_allocatable && (($row->allocated_qty - $row->revoked_qty) != $row->quantity)) {
                        $html .= '<li>
                                    <a data-href="' . action('\Modules\AssetManagement\Http\Controllers\AssetAllocationController@create', ['asset_id' => $row->id]) . '" class="cursor-pointer" id="allocate_asset">
                                        <i class="fas fa-plus-circle"></i>
                                    '.__("assetmanagement::lang.allocate_asset").'
                                    </a>
                                    </li>';
                    }

                    if(auth()->user()->can('asset.view_all_maintenance') || auth()->user()->can('asset.view_own_maintenance')) {
                        $html .= '<li>
                                        <a data-href="' . route('asset-maintenance.create') . '?asset_id=' . $row->id . '" class="cursor-pointer send_to_maintenance">
                                            <i class="fas fa-tools"></i>
                                        '.__("assetmanagement::lang.send_to_maintenance").'
                                        </a>
                                        </li>';
                    }

                    $html .= '
                            <li>
                                <a data-href="' . action('\Modules\AssetManagement\Http\Controllers\AssetController@edit', [$row->id]) . '" class="cursor-pointer edit_asset">
                                    <i class="fa fa-edit"></i>
                                    '.__("messages.edit").'
                                </a>
                            </li>
                            <li>
                                <a data-href="' . action('\Modules\AssetManagement\Http\Controllers\AssetController@destroy', [$row->id]) . '"  id="delete_asset" class="cursor-pointer">
                                    <i class="fas fa-trash"></i>
                                    '.__("messages.delete").'
                                </a>
                            </li>
                            </ul>';

                    $html .= '
                            </div>';

                    return $html;
                })
                ->editColumn('purchase_date', '
                    @if(!empty($purchase_date))
                        {{@format_date($purchase_date)}}
                    @endif
                ')
                ->editColumn('is_allocatable', function($row) {
                    if ($row->is_allocatable) {
                        return '<i class="fas fa-check-circle text-success"></i>';
                    } else {
                        return '<i class="fas fa-times-circle text-danger"></i>';
                    }
                })
                ->editColumn('quantity', '
                    @if(!empty($quantity))
                        {{@format_quantity($quantity)}}
                    @endif
                ')
                ->editColumn('allocated_qty', '
                    @if(!empty($allocated_qty))
                        {{@format_quantity($allocated_qty - $revoked_qty)}}
                    @endif
                ')
                ->editColumn('unit_price', function($row) {
                    return '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $row->unit_price . '">' . $row->unit_price . '</span>';
                })
                ->addColumn('image', function($row) {
                    $html = '';
                    if (!empty($row->media->first())) {
                        $url = $row->media->first()->display_url;
                        $html = '<a href="'.$url.'" target="_blank"><i class="fas fa-eye fa-lg"></i></a>';
                    }
                    return $html;
                })
                ->addColumn('warranty', function($row) use ($now) {
                    $warranty = null;

                    $html = '';
                    foreach ($row->warranties as $w) {
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
                ->editColumn('asset', function($row) {
                    $html = $row->asset;

                    foreach ($row->maintenances as $maintenance) {
                        if (in_array($maintenance->status, ['new', 'in_progress'])) {
                            $count = $row->maintenances->whereIn('status', ['new', 'in_progress'])->count();
                            $html .= '<br><span class="label bg-red">' . __('assetmanagement::lang.n_in_maintenance', ['n' => $count]) . '</span>';
                            break;
                        }
                    }

                    return $html;
                })
                ->removeColumn('id')
                ->removeColumn('maintenances')
                ->rawColumns(['action', 'is_allocatable', 'purchase_date',
                    'quantity', 'allocated_qty', 'unit_price', 'warranty_period', 'image', 'warranty', 'asset'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $asset_category = Category::forDropdown($business_id, 'asset');

        return view('assetmanagement::asset.index')
                ->with(compact('purchase_types', 'business_locations', 'asset_category'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {

            $asset_category = Category::forDropdown($business_id, 'asset');
            $business_locations = BusinessLocation::forDropdown($business_id);

            $purchase_types = $this->purchaseTypes;

            return view('assetmanagement::asset.create')
                ->with(compact('asset_category', 'business_locations', 'purchase_types'));
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

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('asset_code', 'name', 'quantity', 'model', 
                'serial_no', 'category_id', 'location_id', 'purchase_date', 
                'unit_price', 'depreciation','is_allocatable', 'description', 'purchase_type');

            $input['business_id'] = $business_id;
            $input['created_by'] = request()->session()->get('user.id');
            $input['is_allocatable'] = !empty($input['is_allocatable']) ? 1 : 0;

            DB::beginTransaction();

            if (empty($input['asset_code'])) {
                $ref_count = $this->commonUtil->setAndGetReferenceCount('asset_code', $business_id);
                $asset_settings = $this->assetUtil->getAssetSettings($business_id);

                $asset_code_prefix = $asset_settings['asset_code_prefix'] ?? null;
                $input['asset_code'] = $this->commonUtil->generateReferenceNumber('asset_code', $ref_count, null, $asset_code_prefix);
            }

            if (!empty($input['quantity'])) {
                $input['quantity'] = $this->commonUtil->num_uf($input['quantity']);
            }

            if (!empty($input['purchase_date'])) {
                $input['purchase_date'] = $this->commonUtil->uf_date($input['purchase_date']);
            }

            if (!empty($input['unit_price'])) {
                $input['unit_price'] = $this->commonUtil->num_uf($input['unit_price']);
            }

            if (!empty($input['depreciation'])) {
                $input['depreciation'] = $this->commonUtil->num_uf($input['depreciation']);
            }

            $asset = Asset::create($input);

            //upload media
            if ($request->has('image')) {
                Media::uploadMedia($business_id, $asset, $request, 'image', true);
            }
            $warranties = [];
            if (!empty($request->input('start_dates'))) {
                $months = $request->input('months');
                foreach ($request->input('start_dates') as $key => $value) {
                    if (!empty($value) && !empty($months[$key])) {
                        $start_date = $this->commonUtil->uf_date($value);
                        $warranties[] = [
                            'start_date' => $start_date,
                            'end_date' => \Carbon::parse($start_date)->addMonths($months[$key])->format('Y-m-d'),
                            'additional_cost' => $this->commonUtil->num_uf($request->input('additional_cost')[$key]),
                            'additional_note' => $request->input('additional_note')[$key]

                        ];
                    }
                }
            }

            if (!empty($warranties)) {
                $asset->warranties()->createMany($warranties);
            }

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

        return redirect()
            ->action('\Modules\AssetManagement\Http\Controllers\AssetController@index')
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

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $asset = Asset::with(['warranties'])
                        ->where('business_id', $business_id)
                        ->findOrfail($id);

            $asset_category = Category::forDropdown($business_id, 'asset');
            $business_locations = BusinessLocation::forDropdown($business_id);

            $purchase_types = $this->purchaseTypes;

            return view('assetmanagement::asset.edit')
                ->with(compact('asset_category', 'business_locations', 'asset', 'purchase_types'));
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

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('name', 'quantity', 'model',
                        'category_id', 'location_id', 'purchase_date', 'unit_price', 'depreciation',
                        'is_allocatable', 'description', 'purchase_type', 'serial_no');
            $input['is_allocatable'] = !empty($input['is_allocatable']) ? 1 : 0;

            DB::beginTransaction();

            if (!empty($input['quantity'])) {
                $input['quantity'] = $this->commonUtil->num_uf($input['quantity']);
            }

            if (!empty($input['purchase_date'])) {
                $input['purchase_date'] = $this->commonUtil->uf_date($input['purchase_date']);
            }

            if (!empty($input['unit_price'])) {
                $input['unit_price'] = $this->commonUtil->num_uf($input['unit_price']);
            }

            if (!empty($input['depreciation'])) {
                $input['depreciation'] = $this->commonUtil->num_uf($input['depreciation']);
            }

            $asset = Asset::where('business_id', $business_id)
                        ->findOrfail($id);
                        
            $asset->update($input);

            //update existing warranties
            $edited_warranty_ids = [];
            if (!empty($request->input('edit_warranty'))) {
                foreach ($request->input('edit_warranty') as $key => $value) {
                    $edited_warranty_ids[] = $key;
                    $start_date = $this->commonUtil->uf_date($value['start_date']);
                    AssetWarranty::where('id', $key)
                                ->update([
                            'start_date' => $start_date,
                            'end_date' => \Carbon::parse($start_date)->addMonths($value['months'])->format('Y-m-d'),
                            'additional_cost' => $this->commonUtil->num_uf($value['additional_cost']),
                            'additional_note' => $value['additional_note']

                        ]);
                }
            }
            AssetWarranty::where('asset_id', $asset->id)
                        ->whereNotIn('id', $edited_warranty_ids)
                        ->delete();

            //add new warranties
            $warranties = [];
            if (!empty($request->input('start_dates'))) {
                $months = $request->input('months');
                foreach ($request->input('start_dates') as $key => $value) {
                    if (!empty($value) && !empty($months[$key])) {
                        $start_date = $this->commonUtil->uf_date($value);
                        $warranties[] = [
                            'start_date' => $start_date,
                            'end_date' => \Carbon::parse($start_date)->addMonths($months[$key])->format('Y-m-d'),
                            'additional_cost' => $this->commonUtil->num_uf($request->input('additional_cost')[$key]),
                            'additional_note' => $request->input('additional_note')[$key]

                        ];
                    }
                }
            }

            if (!empty($warranties)) {
                $asset->warranties()->createMany($warranties);
            }

            //upload media
            if ($request->has('image')) {
                Media::uploadMedia($business_id, $asset, $request, 'image', true);
            }

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

        return redirect()
            ->action('\Modules\AssetManagement\Http\Controllers\AssetController@index')
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

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $asset = Asset::where('business_id', $business_id)
                            ->findOrfail($id);

                $asset->delete();
                $asset->media()->delete();

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

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');

        $allocated_assets = AssetTransaction::where('receiver', auth()->user()->id)
                                            ->select(
                                                DB::raw("SUM(quantity) as total_quantity_allocated"),
                                                DB::raw('(SELECT SUM(quantity) FROM asset_transactions as AT WHERE AT.parent_id=asset_transactions.id AND AT.transaction_type="revoke") as total_revoked_quantity')
                                            )->first();
        
        $total_assets_allocated = $allocated_assets->total_quantity_allocated - $allocated_assets->total_revoked_quantity;

        $asset_allocation_by_category = AssetTransaction::where('asset_transactions.receiver', 
                                                    auth()->user()->id)
                            ->leftJoin('assets as a', 'a.id',
                            '=', 'asset_transactions.asset_id')
                            ->leftJoin('categories as cat', 'a.category_id',
                            '=', 'cat.id')
                                            ->select(
                                                DB::raw("SUM(COALESCE(asset_transactions.quantity, 0) - (SELECT SUM(quantity) FROM asset_transactions as AT WHERE AT.parent_id=asset_transactions.id AND AT.transaction_type='revoke')) as total_quantity_allocated"),
                                                'cat.name as category'
                                            )->groupBy('cat.id')->get();

        $is_admin = $this->commonUtil->is_admin(auth()->user());

        $total_assets = 0;
        $assets_by_category = null;
        $total_assets_allocated_for_all_users = 0;
        $expiring_assets = null;

        if ($is_admin) {
            $total_assets = Asset::where('business_id', $business_id)
                                ->select(DB::raw('SUM(quantity) as total_quantity'))
                                ->first()->total_quantity;

            $assets_by_category = Asset::where('assets.business_id', $business_id)
                                    ->leftJoin('categories as cat', 'assets.category_id'
                                        ,'=', 'cat.id')
                                ->select(
                                        DB::raw('SUM(quantity) as total_quantity'),
                                        'cat.name as category'
                                    )
                                ->groupBy('cat.id')
                                ->get();

            $expiring_assets =  Asset::where('assets.business_id', $business_id)
                                    ->leftjoin('asset_warranties as aw', 'aw.asset_id', '=', 'assets.id')
                                    ->where( function($q) {
                                        $q->whereRaw("CURDATE() BETWEEN start_date AND end_date")
                                            ->whereRaw("DATEDIFF(end_date, CURDATE()) <= 30")
                                            ->whereRaw("DATEDIFF(end_date, CURDATE()) > 0");
                                    })
                                    ->orWhereNull('aw.end_date')
                                    ->select('assets.name', 'asset_code', 'end_date')
                                    ->get(); 

            $allocated_assets_for_all_users = AssetTransaction::where('business_id', $business_id)
                                            ->select(
                                                DB::raw("SUM(IF(transaction_type='allocate', quantity, 0)) as total_quantity_allocated"),
                                                DB::raw("SUM(IF(transaction_type='revoke', quantity, 0)) as total_revoked_quantity")
                                            )->first();
        
            $total_assets_allocated_for_all_users = $allocated_assets_for_all_users->total_quantity_allocated - $allocated_assets_for_all_users->total_revoked_quantity;           
        }
        
        return view('assetmanagement::asset.dashboard')
                ->with(compact('total_assets_allocated', 'asset_allocation_by_category', 
                    'is_admin', 'total_assets', 'assets_by_category', 'expiring_assets', 'total_assets_allocated_for_all_users'));

    }
}
