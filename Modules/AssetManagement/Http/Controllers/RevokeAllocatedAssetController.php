<?php

namespace Modules\AssetManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Modules\AssetManagement\Utils\AssetUtil;
use Modules\AssetManagement\Entities\AssetTransaction;
use DB;
use Yajra\DataTables\Facades\DataTables;

class RevokeAllocatedAssetController extends Controller
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
    public function index()
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $asset_allocated = AssetTransaction::join('asset_transactions as PT',
                                'asset_transactions.parent_id', '=', 'PT.id')
                                ->join('assets', 'PT.asset_id', '=', 'assets.id')
                                ->join('users as receiver', 'PT.receiver', '=', 'receiver.id')
                                ->join('users as revoked_by', 'asset_transactions.created_by', '=', 'revoked_by.id')
                                ->leftJoin('categories as CAT', 'assets.category_id',
                                    '=', 'CAT.id')
                                ->where('asset_transactions.business_id', $business_id)
                                ->where('asset_transactions.transaction_type', 'revoke')
                                ->select('asset_transactions.ref_no as ref_no',
                                'asset_transactions.quantity as quantity',
                                'asset_transactions.transaction_datetime as revoked_at', 'asset_transactions.id as id',
                                'assets.name as asset', 'assets.model as model',
                                'CAT.name as category', DB::raw("CONCAT(COALESCE(receiver.surname, ''),' ',COALESCE(receiver.first_name, ''),' ',COALESCE(receiver.last_name,'')) as revoked_for"),
                                DB::raw("CONCAT(COALESCE(revoked_by.surname, ''),' ',COALESCE(revoked_by.first_name, ''),' ',COALESCE(revoked_by.last_name,'')) as revoked_by_name"),
                                'PT.ref_no as allocation_code', 'asset_transactions.reason as reason');

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
                                    ';

                    $html .= '<ul class="dropdown-menu dropdown-menu-left" role="menu">
                                <li>
                                    <a data-href="' . action('\Modules\AssetManagement\Http\Controllers\RevokeAllocatedAssetController@destroy', [$row->id]) . '"  id="delete_revoked_asset" class="cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                        '.__("messages.delete").'
                                    </a>
                                </li>
                            </ul>';

                    $html .= '
                            </div>';

                    return $html;
                })
                ->editColumn('revoked_at', '
                    @if(!empty($revoked_at))
                        {{@format_datetime($revoked_at)}}
                    @endif
                ')
                ->editColumn('quantity', '
                    @if(!empty($quantity))
                        {{@format_quantity($quantity)}}
                    @endif
                ')
                ->removeColumn('id')
                ->rawColumns(['action', 'revoked_at', 'quantity'])
                ->make(true);
        }

        return view('assetmanagement::asset_revocation.index');
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
            $allocated_id = $request->get('id');
            $allocated_asset = AssetTransaction::where('business_id', $business_id)
                                    ->findOrFail($allocated_id);

            $total_revoked_asset = $this->_getRevokedQtyOfAllocatedAsset($allocated_asset);

            return view('assetmanagement::asset_revocation.create')
                ->with(compact('allocated_asset', 'total_revoked_asset'));
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
            $input = $request->only('ref_no', 'parent_id', 'asset_id', 'quantity', 'transaction_datetime', 'reason');
            $input['transaction_type'] = 'revoke';
            $input['business_id'] = $business_id;
            $input['created_by'] = request()->session()->get('user.id');

            DB::beginTransaction();

            if (empty($input['ref_no'])) {
                $ref_count = $this->commonUtil->setAndGetReferenceCount('revoke_code', $business_id);
                $asset_settings = $this->assetUtil->getAssetSettings($business_id);

                $revoke_code_prefix = $asset_settings['revoke_code_prefix'] ?? null;
                $input['ref_no'] = $this->commonUtil->generateReferenceNumber('revoke_code', $ref_count, null, $revoke_code_prefix);
            }

            if (!empty($input['transaction_datetime'])) {
                $input['transaction_datetime'] = $this->commonUtil->uf_date($input['transaction_datetime'], true);
            }

            if (!empty($input['quantity'])) {
                $input['quantity'] = $this->commonUtil->num_uf($input['quantity']);
            }

            AssetTransaction::create($input);

            DB::commit();

            return redirect()
                ->action('\Modules\AssetManagement\Http\Controllers\RevokeAllocatedAssetController@index')
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
        return view('assetmanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
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
                $asset_revoked = AssetTransaction::where('business_id', $business_id)
                                    ->findOrfail($id);

                $asset_revoked->delete();

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
     * Get total revoked qty
     * of an allocated asset
     *
     * @return int
     */
    protected function _getRevokedQtyOfAllocatedAsset($allocated_asset)
    {
        $asset_transaction = AssetTransaction::
                                where('business_id', $allocated_asset->business_id)
                                ->where('parent_id', $allocated_asset->id)
                                ->select(DB::raw('SUM(COALESCE(quantity, 0)) as quantity'))
                                ->first();

        return $asset_transaction->quantity;
    }
}
