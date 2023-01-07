<?php

namespace Modules\AssetManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use App\User;
use Modules\AssetManagement\Entities\Asset;
use Modules\AssetManagement\Entities\AssetTransaction;
use DB;
use Yajra\DataTables\Facades\DataTables;
use Modules\AssetManagement\Utils\AssetUtil;

class AssetAllocationController extends Controller
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

        if ($request->ajax()) {
            $asset_allocated = AssetTransaction::join('assets',
                                'asset_transactions.asset_id', '=', 'assets.id')
                                ->join('users as receiver', 'asset_transactions.receiver', '=', 'receiver.id')
                                ->join('users as provider', 'asset_transactions.created_by', '=', 'provider.id')
                                ->leftJoin('categories as CAT', 'assets.category_id',
                                    '=', 'CAT.id')
                                ->leftJoin('asset_transactions as PT',
                                'asset_transactions.id', '=', 'PT.parent_id')
                                ->where('asset_transactions.business_id', $business_id)
                                ->where('asset_transactions.transaction_type', 'allocate')
                                ->select('asset_transactions.ref_no as ref_no',
                                'asset_transactions.quantity as quantity',
                                'asset_transactions.transaction_datetime as allocated_at', 'asset_transactions.id as id',
                                'assets.name as asset', 'assets.model as model',
                                'CAT.name as category', DB::raw("CONCAT(COALESCE(receiver.surname, ''),' ',COALESCE(receiver.first_name, ''),' ',COALESCE(receiver.last_name,'')) as receiver_name"),
                                DB::raw("CONCAT(COALESCE(provider.surname, ''),' ',COALESCE(provider.first_name, ''),' ',COALESCE(provider.last_name,'')) as provider_name"),
                                DB::raw('SUM(COALESCE(PT.quantity, 0)) as revoked_quantity'),
                                'asset_transactions.reason as reason',
                                'asset_transactions.allocated_upto'
                                )
                                ->groupBy('asset_transactions.id');

            return Datatables::of($asset_allocated)
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

                    if ($row->revoked_quantity != $row->quantity) {
                        $html .= '<li>
                                    <a data-href="' . action('\Modules\AssetManagement\Http\Controllers\AssetAllocationController@edit', [$row->id]) . '" class="cursor-pointer edit_allocated_asset">
                                        <i class="fa fa-edit"></i>
                                            '.__("messages.edit").'
                                        </a>
                                    </li>';
                    }

                    $html .= '<li>
                                <a data-href="' . action('\Modules\AssetManagement\Http\Controllers\AssetAllocationController@destroy', [$row->id]) . '"  id="delete_allocated_asset" class="cursor-pointer">
                                    <i class="fas fa-trash"></i>
                                    '.__("messages.delete").'
                                </a>
                            </li>';

                    if ($row->revoked_quantity != $row->quantity) {
                        $html .= '<li>
                                <a data-href="' . action('\Modules\AssetManagement\Http\Controllers\RevokeAllocatedAssetController@create', ['id' => $row->id]) . '" class="cursor-pointer revoke_allocated_asset">
                                    <i class="fas fa-history"></i>
                                    '.__("assetmanagement::lang.revoke").'
                                </a>
                            </li>';
                    }

                    $html .= '</ul>
                            </div>';

                    return $html;
                })
                ->editColumn('allocated_at', '
                    @if(!empty($allocated_at))
                        {{@format_datetime($allocated_at)}}
                    @endif
                ')
                ->editColumn('allocated_upto', '
                    @if(!empty($allocated_upto))
                        {{@format_date($allocated_upto)}}
                    @endif
                ')
                ->editColumn('quantity', '
                    @if(!empty($quantity))
                        {{@format_quantity($quantity)}}
                    @endif
                ')
                ->editColumn('revoked_quantity', '
                    @if(!empty($revoked_quantity))
                        {{@format_quantity($revoked_quantity)}}
                    @endif
                ')
                ->removeColumn('id')
                ->rawColumns(['action', 'allocated_at', 'quantity', 'revoked_quantity'])
                ->make(true);
        }

        return view('assetmanagement::asset_allocation.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $users = User::forDropdown($business_id, false);
            $assets = Asset::forDropdown($business_id, true, false);
            $asset_id = request()->get('asset_id', null);

            return view('assetmanagement::asset_allocation.create')
                ->with(compact('users', 'assets', 'asset_id'));
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
            $input = $request->only('ref_no', 'asset_id', 'quantity',
                        'receiver', 'transaction_datetime', 'reason', 'allocated_upto');
            $input['transaction_type'] = 'allocate';
            $input['business_id'] = $business_id;
            $input['created_by'] = request()->session()->get('user.id');

            DB::beginTransaction();

            if (empty($input['ref_no'])) {
                $ref_count = $this->commonUtil->setAndGetReferenceCount('allocation_code', $business_id);
                $asset_settings = $this->assetUtil->getAssetSettings($business_id);

                $allocation_code_prefix = $asset_settings['allocation_code_prefix'] ?? null;
                $input['ref_no'] = $this->commonUtil->generateReferenceNumber('allocation_code', $ref_count, null, $allocation_code_prefix);
            }

            if (!empty($input['transaction_datetime'])) {
                $input['transaction_datetime'] = $this->commonUtil->uf_date($input['transaction_datetime'], true);
            }

            if (!empty($input['allocated_upto'])) {
                $input['allocated_upto'] = $this->commonUtil->uf_date($input['allocated_upto']);
            }

            if (!empty($input['quantity'])) {
                $input['quantity'] = $this->commonUtil->num_uf($input['quantity']);
            }

            AssetTransaction::create($input);

            DB::commit();

            return redirect()
                ->action('\Modules\AssetManagement\Http\Controllers\AssetAllocationController@index')
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
        } catch (Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return redirect()->back()
                ->with('status', ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ]);
        }
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
            $asset_allocated = AssetTransaction::with('asset', 'revokeTransaction')
                                ->where('business_id', $business_id)
                                ->findOrfail($id);

            $users = User::forDropdown($business_id, false);
            $assets = Asset::forDropdown($business_id, true, false);
            $total_available_asset = $this->_getAvailableQtyOfAsset($asset_allocated);
            
            return view('assetmanagement::asset_allocation.edit')
                ->with(compact('users', 'assets', 'asset_allocated',
                'total_available_asset'));
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
            $input = $request->only('asset_id', 'quantity', 'receiver',
                    'transaction_datetime', 'reason', 'allocated_upto');
            
            DB::beginTransaction();

            if (!empty($input['transaction_datetime'])) {
                $input['transaction_datetime'] = $this->commonUtil->uf_date($input['transaction_datetime'], true);
            }

            if (!empty($input['allocated_upto'])) {
                $input['allocated_upto'] = $this->commonUtil->uf_date($input['allocated_upto']);
            }

            if (!empty($input['quantity'])) {
                $input['quantity'] = $this->commonUtil->num_uf($input['quantity']);
            }

            $a_trans = AssetTransaction::where('business_id', $business_id)
                            ->findOrfail($id);
                            
            $a_trans->update($input);

            DB::commit();

            return redirect()
                ->action('\Modules\AssetManagement\Http\Controllers\AssetAllocationController@index')
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
        } catch (Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return redirect()->back()
                ->with('status', ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ]);
        }
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
                $asset_allocated = AssetTransaction::where('business_id', $business_id)
                                        ->findOrfail($id);

                $asset_allocated->delete();

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

    /**
     * Get total available qty
     * of an asset
     *
     * @return int
     */
    protected function _getAvailableQtyOfAsset($asset_allocated)
    {
        $asset = Asset::leftJoin('asset_transactions as AT', function ($join) {
                            $join->on('assets.id', '=', 'AT.asset_id')
                                ->where('transaction_type', 'allocate');
                        })
                        ->where('assets.business_id', $asset_allocated->business_id)
                        ->where('assets.id', $asset_allocated->asset_id)
                        ->select('assets.id as id', DB::raw('SUM(COALESCE(AT.quantity, 0)) as allocated_qty'),
                        DB::raw('(SELECT SUM(COALESCE(AR.quantity, 0)) FROM asset_transactions AS AR WHERE(AR.asset_id=assets.id AND AR.transaction_type=\'revoke\')) as revoked_qty')
                        )
                        ->first();

        $available_qty = $asset->allocated_qty - $asset->revoked_qty;

        return $available_qty;
    }
}
