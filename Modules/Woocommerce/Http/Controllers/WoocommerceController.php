<?php

namespace Modules\Woocommerce\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Media;
use App\Product;
use App\SellingPriceGroup;
use App\System;
use App\TaxRate;
use App\Utils\ModuleUtil;
use App\Variation;
use App\VariationTemplate;
use DB;
use Illuminate\Http\Request;

use Illuminate\Http\Response;

use Illuminate\Routing\Controller;
use Modules\Woocommerce\Entities\WoocommerceSyncLog;

use Modules\Woocommerce\Utils\WoocommerceUtil;

use Yajra\DataTables\Facades\DataTables;

class WoocommerceController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $woocommerceUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param WoocommerceUtil $woocommerceUtil
     * @return void
     */
    public function __construct(WoocommerceUtil $woocommerceUtil, ModuleUtil $moduleUtil)
    {
        $this->woocommerceUtil = $woocommerceUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        try {
            $business_id = request()->session()->get('business.id');

            if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
                abort(403, 'Unauthorized action.');
            }
                
            $tax_rates = TaxRate::where('business_id', $business_id)
                            ->get();
                            
            $woocommerce_tax_rates = ['' => __('messages.please_select')];

            $woocommerce_api_settings = $this->woocommerceUtil->get_api_settings($business_id);

            $alerts = [];

            $not_synced_cat_count = Category::where('business_id', $business_id)
                                        ->whereNull('woocommerce_cat_id')
                                        ->where('category_type', 'product')
                                        ->count();

            if (!empty($not_synced_cat_count)) {
                $alerts['not_synced_cat'] = $not_synced_cat_count == 1 ? __('woocommerce::lang.one_cat_not_synced_alert') : __('woocommerce::lang.cat_not_sync_alert', ['count' => $not_synced_cat_count]);
            }

            $cat_last_sync = $this->woocommerceUtil->getLastSync($business_id, 'categories', false);
            if (!empty($cat_last_sync)) {
                $updated_cat_count = Category::where('business_id', $business_id)
                                        ->whereNotNull('woocommerce_cat_id')
                                        ->where('updated_at', '>', $cat_last_sync)
                                        ->count();
            }
            
            if (!empty($updated_cat_count)) {
                $alerts['updated_cat'] = $updated_cat_count == 1 ? __('woocommerce::lang.one_cat_updated_alert') : __('woocommerce::lang.cat_updated_alert', ['count' => $updated_cat_count]);
            }

            $products_last_synced = $this->woocommerceUtil->getLastSync($business_id, 'all_products', false);
            $query = Product::where('business_id', $business_id)
                                        ->whereIn('type', ['single', 'variable'])
                                        ->whereNull('woocommerce_product_id')
                                        ->where('woocommerce_disable_sync', 0);

            if (!empty($woocommerce_api_settings->location_id)) {
                $query->ForLocation($woocommerce_api_settings->location_id);
            }
            $not_synced_product_count = $query->count();

            if (!empty($not_synced_product_count)) {
                $alerts['not_synced_product'] = $not_synced_product_count == 1 ? __('woocommerce::lang.one_product_not_sync_alert') : __('woocommerce::lang.product_not_sync_alert', ['count' => $not_synced_product_count]);
            }
            if (!empty($products_last_synced)) {
                $updated_product_count = Product::where('business_id', $business_id)
                                        ->whereNotNull('woocommerce_product_id')
                                        ->where('woocommerce_disable_sync', 0)
                                        ->whereIn('type', ['single', 'variable'])
                                        ->where('updated_at', '>', $products_last_synced)
                                        ->count();
            }

            if (!empty($updated_product_count)) {
                $alerts['not_updated_product'] = $updated_product_count == 1 ? __('woocommerce::lang.one_product_updated_alert') : __('woocommerce::lang.product_updated_alert', ['count' => $updated_product_count]);
            }

            $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
            if (empty($notAllowed)) {
                $response = $this->woocommerceUtil->getTaxRates($business_id);
                if (!empty($response)) {
                    foreach ($response as $r) {
                        $woocommerce_tax_rates[$r->id] = $r->name;
                    }
                }
            }
        } catch (\Exception $e) {
            $alerts['connection_failed'] = 'Unable to connect with WooCommerce, Check API settings';
        }
        

        return view('woocommerce::woocommerce.index')
                ->with(compact('tax_rates', 'woocommerce_tax_rates', 'alerts'));
    }

    /**
     * Displays form to update woocommerce api settings.
     * @return Response
     */
    public function apiSettings()
    {
        $business_id = request()->session()->get('business.id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.access_woocommerce_api_settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $default_settings = [
            'woocommerce_app_url' => '',
            'woocommerce_consumer_key' => '',
            'woocommerce_consumer_secret' => '',
            'location_id' => null,
            'default_tax_class' => '',
            'product_tax_type' => 'inc',
            'default_selling_price_group' => '',
            'product_fields_for_create' => ['category', 'quantity'],
            'product_fields_for_update' => ['name', 'price', 'category', 'quantity'],
        ];

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                        ->pluck('name', 'id')->prepend(__('lang_v1.default'), '');

        $business = Business::find($business_id);

        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            $business = null;
        }

        if (!empty($business->woocommerce_api_settings)) {
            $default_settings = json_decode($business->woocommerce_api_settings, true);
            if (empty($default_settings['product_fields_for_create'])) {
                $default_settings['product_fields_for_create'] = [];
            }

            if (empty($default_settings['product_fields_for_update'])) {
                $default_settings['product_fields_for_update'] = [];
            }
        }

        $locations = BusinessLocation::forDropdown($business_id);
        $module_version = System::getProperty('woocommerce_version');

        $cron_job_command = $this->moduleUtil->getCronJobCommand();

        $shipping_statuses = $this->moduleUtil->shipping_statuses();

        return view('woocommerce::woocommerce.api_settings')
                ->with(compact('default_settings', 'locations', 'price_groups', 'module_version', 'cron_job_command', 'business', 'shipping_statuses'));
    }

    /**
     * Updates woocommerce api settings.
     * @return Response
     */
    public function updateSettings(Request $request)
    {
        $business_id = request()->session()->get('business.id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.access_woocommerce_api_settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $input = $request->except('_token');

            $input['product_fields_for_create'] = !empty($input['product_fields_for_create']) ? $input['product_fields_for_create'] : [];
            $input['product_fields_for_update'] = !empty($input['product_fields_for_update']) ? $input['product_fields_for_update'] : [];
            $input['order_statuses'] = !empty($input['order_statuses']) ? $input['order_statuses'] : [];
            $input['shipping_statuses'] = !empty($input['shipping_statuses']) ? $input['shipping_statuses'] : [];

            $business = Business::find($business_id);
            $business->woocommerce_api_settings = json_encode($input);
            $business->woocommerce_wh_oc_secret = $input['woocommerce_wh_oc_secret'];
            $business->woocommerce_wh_ou_secret = $input['woocommerce_wh_ou_secret'];
            $business->woocommerce_wh_od_secret = $input['woocommerce_wh_od_secret'];
            $business->woocommerce_wh_or_secret = $input['woocommerce_wh_or_secret'];
            $business->save();

            $output = ['success' => 1,
                            'msg' => trans("lang_v1.updated_succesfully")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Synchronizes pos categories with Woocommerce categories
     * @return Response
     */
    public function syncCategories()
    {
        $business_id = request()->session()->get('business.id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.syc_categories')))) {
            abort(403, 'Unauthorized action.');
        }

        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            DB::beginTransaction();
            $user_id = request()->session()->get('user.id');
            
            $this->woocommerceUtil->syncCategories($business_id, $user_id);

            DB::commit();

            $output = ['success' => 1,
                            'msg' => __("woocommerce::lang.synced_successfully")
                        ];
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                            'msg' => $e->getMessage(),
                        ];
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }
        }

        return $output;
    }

    /**
     * Synchronizes pos products with Woocommerce products
     * @return Response
     */
    public function syncProducts()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_products')))) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        try {
            $user_id = request()->session()->get('user.id');
            $sync_type = request()->input('type');

            DB::beginTransaction();

            $offset = request()->input('offset');
            $limit = 100;
            $all_products = $this->woocommerceUtil->syncProducts($business_id, $user_id, $sync_type, $limit, $offset);
            $total_products = count($all_products);
            
            DB::commit();
            $msg = $total_products > 0 ?  __("woocommerce::lang.n_products_synced_successfully", ['count' => $total_products]) :  __("woocommerce::lang.synced_successfully");
            $output = ['success' => 1,
                            'msg' => $msg,
                            'total_products' => $total_products
                        ];
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                            'msg' => $e->getMessage(),
                        ];
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }
        }
        
        return $output;
    }

    /**
     * Synchronizes Woocommers Orders with POS sales
     * @return Response
     */
    public function syncOrders()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_orders')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $user_id = request()->session()->get('user.id');
           
            $this->woocommerceUtil->syncOrders($business_id, $user_id);

            DB::commit();

            $output = ['success' => 1,
                            'msg' => __("woocommerce::lang.synced_successfully")
                        ];
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                            'msg' => $e->getMessage(),
                        ];
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong"),
                        ];
            }
        }

        return $output;
    }

    /**
     * Retrives sync log
     * @return Response
     */
    public function getSyncLog()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $last_sync = [
                'categories' => $this->woocommerceUtil->getLastSync($business_id, 'categories'),
                'new_products' => $this->woocommerceUtil->getLastSync($business_id, 'new_products'),
                'all_products' => $this->woocommerceUtil->getLastSync($business_id, 'all_products'),
                'orders' => $this->woocommerceUtil->getLastSync($business_id, 'orders')

            ];
            return $last_sync;
        }
    }

    /**
     * Maps POS tax_rates with Woocommerce tax rates.
     * @return Response
     */
    public function mapTaxRates(Request $request)
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.map_tax_rates')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->except('_token');
            foreach ($input['taxes'] as $key => $value) {
                $value = !empty($value) ? $value : null;
                TaxRate::where('business_id', $business_id)
                        ->where('id', $key)
                        ->update(['woocommerce_tax_rate_id' => $value]);
            }

            $output = ['success' => 1,
                            'msg' => __("lang_v1.updated_succesfully")
                        ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                        'msg' => __("messages.something_went_wrong"),
                    ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function viewSyncLog()
    {
        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $logs = WoocommerceSyncLog::where('woocommerce_sync_logs.business_id', $business_id)
                    ->leftjoin('users as U', 'U.id', '=', 'woocommerce_sync_logs.created_by')
                    ->select([
                        'woocommerce_sync_logs.created_at',
                        'sync_type', 'operation_type',
                        DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                        'woocommerce_sync_logs.data',
                        'woocommerce_sync_logs.details as log_details',
                        'woocommerce_sync_logs.id as DT_RowId'
                    ]);
            $sync_type = [];
            if (auth()->user()->can('woocommerce.syc_categories')) {
                $sync_type[] = 'categories';
            }
            if (auth()->user()->can('woocommerce.sync_products')) {
                $sync_type[] = 'all_products';
                $sync_type[] = 'new_products';
            }

            if (auth()->user()->can('woocommerce.sync_orders')) {
                $sync_type[] = 'orders';
            }
            if (!auth()->user()->can('superadmin')) {
                $logs->whereIn('sync_type', $sync_type);
            }

            return Datatables::of($logs)
                ->editColumn('created_at', function ($row) {
                    $created_at = $this->woocommerceUtil->format_date($row->created_at, true);
                    $for_humans = \Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->diffForHumans();
                    return $created_at . '<br><small>' . $for_humans . '</small>';
                })
                ->editColumn('sync_type', function ($row) {
                    $array = [
                        'categories' => __('category.categories'),
                        'all_products' => __('sale.products'),
                        'new_products' => __('sale.products'),
                        'orders' => __('woocommerce::lang.orders'),
                    ];
                    return $array[$row->sync_type];
                })
                ->editColumn('operation_type', function ($row) {
                    $array = [
                        'created' => __('woocommerce::lang.created'),
                        'updated' => __('woocommerce::lang.updated'),
                        'reset' => __('woocommerce::lang.reset'),
                        'deleted' => __('lang_v1.deleted'),
                        'restored' => __('woocommerce::lang.order_restored')
                    ];
                    return array_key_exists($row->operation_type, $array) ? $array[$row->operation_type] : '';
                })
                ->editColumn('data', function ($row) {
                    if (!empty($row->data)) {
                        $data = json_decode($row->data, true);
                        return implode(', ', $data) . '<br><small>' . count($data) . ' ' . __('woocommerce::lang.records') . '</small>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('log_details', function ($row) {
                    $details = '';
                    if (!empty($row->log_details)) {
                        $details = $row->log_details;
                    }
                    return $details;
                })
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['created_at', 'data'])
                ->make(true);
        }

        return view('woocommerce::woocommerce.sync_log');
    }

    /**
     * Retrives details of a sync log.
     * @param int $id
     * @return Response
     */
    public function getLogDetails($id)
    {
        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $log = WoocommerceSyncLog::where('business_id', $business_id)
                                            ->find($id);
            $log_details = json_decode($log->details);
            
            return view('woocommerce::woocommerce.partials.log_details')
                    ->with(compact('log_details'));
        }
    }

    /**
     * Resets synced categories
     * @return json
     */
    public function resetCategories()
    {
        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                Category::where('business_id', $business_id)
                        ->update(['woocommerce_cat_id' => null]);
                $user_id = request()->session()->get('user.id');
                $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'categories', 'reset', null);

                $output = ['success' => 1,
                            'msg' => __("woocommerce::lang.cat_reset_success"),
                        ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong"),
                        ];
            }

            return $output;
        }
    }

    /**
     * Resets synced products
     * @return json
     */
    public function resetProducts()
    {
        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                //Update products table
                Product::where('business_id', $business_id)
                        ->update(['woocommerce_product_id' => null, 'woocommerce_media_id' => null]);

                $product_ids = Product::where('business_id', $business_id)
                                    ->pluck('id');

                $product_ids = !empty($product_ids) ? $product_ids : [];
                //Update variations table
                Variation::whereIn('product_id', $product_ids)
                        ->update([
                            'woocommerce_variation_id' => null
                        ]);

                //Update variation templates
                VariationTemplate::where('business_id', $business_id)
                                ->update([
                                    'woocommerce_attr_id' => null
                                ]);

                Media::where('business_id', $business_id)
                        ->update(['woocommerce_media_id' => null]);

                $user_id = request()->session()->get('user.id');
                $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'all_products', 'reset', null);

                $output = ['success' => 1,
                            'msg' => __("woocommerce::lang.prod_reset_success"),
                        ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = ['success' => 0,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage(),
                        ];
            }

            return $output;
        }
    }
}
