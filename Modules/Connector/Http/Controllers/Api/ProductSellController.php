<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Connector\Transformers\NewProductResource;
use Illuminate\Support\Facades\Auth;
use App\Product;
use Modules\Connector\Transformers\NewSellResource;
use App\Utils\Util;
use App\Transaction;
use App\Contact;
use Modules\Connector\Transformers\NewContactResource;

/**
 * @group New end points
 * @authenticated
 *
 * APIs for sell list, product list and contact list
 */
class ProductSellController extends ApiController
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(
        Util $commonUtil
    ) {
        $this->commonUtil = $commonUtil;
        parent::__construct();
    }


    /**
     * New List products
     * @queryParam order_by Values: product_name or newest
     * @queryParam order_direction Values: asc or desc
     * @queryParam location_custom_field_1 Custom field 1 of the location
     * @queryParam category_id comma separated ids of one or multiple category
     * @queryParam sub_category_id comma separated ids of one or multiple sub-category
     * @queryParam location_id Example: 1
     * @queryParam not_for_sell (1, 0) 
     * @queryParam send_lot_detail Send lot details in each variation location details(1, 0) 
     * @queryParam name Search term for product name
     * @queryParam sku Search term for product sku
     * @queryParam product_ids comma separated ids of products Example: 1,2
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     * @response {
        "data": [
            {
                "id": 2,
                "name": "Levis Men's Slimmy Fit Jeans",
                "business_id": 1,
                "type": "variable",
                "exemption_type_id": null,
                "enable_stock": 1,
                "sku": "AS0002",
                "product_custom_field1": null,
                "product_custom_field2": null,
                "product_custom_field3": null,
                "product_custom_field4": null,
                "woocommerce_media_id": null,
                "product_description": null,
                "is_inactive": 0,
                "repair_model_id": null,
                "not_for_selling": 0,
                "ecom_shipping_class_id": null,
                "ecom_active_in_store": 1,
                "woocommerce_product_id": 627,
                "woocommerce_disable_sync": 0,
                "image_url": "http://local.pos.com/uploads/img/1528727964_levis_jeans.jpg",
                "product_variations": [
                    {
                        "id": 2,
                        "variation_template_id": 5,
                        "name": "Waist Size",
                        "product_id": 2,
                        "is_dummy": 0,
                        "variations": [
                            {
                                "id": 2,
                                "name": "28",
                                "product_id": 2,
                                "sub_sku": "AS0002-1",
                                "product_variation_id": 2,
                                "woocommerce_variation_id": 658,
                                "default_purchase_price": "70.0000",
                                "dpp_inc_tax": "77.0000",
                                "default_sell_price": "70.0000",
                                "sell_price_inc_tax": "77.0000",
                                "combo_variations": null,
                                "variation_location_details": [
                                    {
                                        "id": 1,
                                        "product_id": 2,
                                        "product_variation_id": 2,
                                        "variation_id": 2,
                                        "location_id": 1,
                                        "qty_available": "71.0000"
                                    }
                                ],
                                "media": [],
                                "discounts": []
                            },
                            {
                                "id": 3,
                                "name": "30",
                                "product_id": 2,
                                "sub_sku": "AS0002-2",
                                "product_variation_id": 2,
                                "woocommerce_variation_id": 659,
                                "default_purchase_price": "70.0000",
                                "dpp_inc_tax": "77.0000",
                                "default_sell_price": "70.0000",
                                "sell_price_inc_tax": "77.0000",
                                "combo_variations": null,
                                "variation_location_details": [
                                    {
                                        "id": 2,
                                        "product_id": 2,
                                        "product_variation_id": 2,
                                        "variation_id": 3,
                                        "location_id": 1,
                                        "qty_available": "89.0000"
                                    }
                                ],
                                "media": [],
                                "discounts": []
                            },
                            {
                                "id": 4,
                                "name": "32",
                                "product_id": 2,
                                "sub_sku": "AS0002-3",
                                "product_variation_id": 2,
                                "woocommerce_variation_id": 660,
                                "default_purchase_price": "70.0000",
                                "dpp_inc_tax": "77.0000",
                                "default_sell_price": "70.0000",
                                "sell_price_inc_tax": "77.0000",
                                "combo_variations": null,
                                "variation_location_details": [
                                    {
                                        "id": 3,
                                        "product_id": 2,
                                        "product_variation_id": 2,
                                        "variation_id": 4,
                                        "location_id": 1,
                                        "qty_available": "127.0000"
                                    },
                                    {
                                        "id": 1371,
                                        "product_id": 2,
                                        "product_variation_id": 2,
                                        "variation_id": 4,
                                        "location_id": 7,
                                        "qty_available": "-1.0000"
                                    }
                                ],
                                "media": [],
                                "discounts": []
                            },
                            {
                                "id": 5,
                                "name": "34",
                                "product_id": 2,
                                "sub_sku": "AS0002-4",
                                "product_variation_id": 2,
                                "woocommerce_variation_id": 661,
                                "default_purchase_price": "72.0000",
                                "dpp_inc_tax": "79.2000",
                                "default_sell_price": "72.0000",
                                "sell_price_inc_tax": "79.2000",
                                "combo_variations": null,
                                "variation_location_details": [
                                    {
                                        "id": 4,
                                        "product_id": 2,
                                        "product_variation_id": 2,
                                        "variation_id": 5,
                                        "location_id": 1,
                                        "qty_available": "128.0000"
                                    }
                                ],
                                "media": [],
                                "discounts": []
                            },
                            {
                                "id": 6,
                                "name": "36",
                                "product_id": 2,
                                "sub_sku": "AS0002-5",
                                "product_variation_id": 2,
                                "woocommerce_variation_id": 662,
                                "default_purchase_price": "72.0000",
                                "dpp_inc_tax": "79.2000",
                                "default_sell_price": "72.0000",
                                "sell_price_inc_tax": "79.2000",
                                "combo_variations": null,
                                "variation_location_details": [
                                    {
                                        "id": 5,
                                        "product_id": 2,
                                        "product_variation_id": 2,
                                        "variation_id": 6,
                                        "location_id": 1,
                                        "qty_available": "99.0000"
                                    }
                                ],
                                "media": [],
                                "discounts": []
                            }
                        ]
                    }
                ],
                "brand": {
                    "id": 1,
                    "business_id": 1,
                    "name": "Levis",
                    "description": null,
                    "created_by": 1,
                    "use_for_repair": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:19:47",
                    "updated_at": "2018-01-03 21:19:47"
                },
                "unit": {
                    "id": 1,
                    "business_id": 1,
                    "actual_name": "Pieces",
                    "short_name": "Pc(s)",
                    "allow_decimal": 0,
                    "base_unit_id": null,
                    "base_unit_multiplier": null
                },
                "category": {
                    "id": 1,
                    "name": "Men's",
                    "business_id": 1,
                    "short_code": "sfefef",
                    "parent_id": 0,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null
                },
                "sub_category": {
                    "id": 4,
                    "name": "Jeans",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null
                },
                "product_tax": {
                    "id": 1,
                    "business_id": 1,
                    "name": "VAT@10%",
                    "amount": 10,
                    "is_tax_group": 0,
                    "for_tax_group": 0,
                    "created_by": 1,
                    "woocommerce_tax_rate_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:40:07",
                    "updated_at": "2018-01-04 02:40:07"
                },
                "product_locations": [
                    {
                        "id": 1,
                        "business_id": 1,
                        "name": "Location 1",
                        "custom_field1": "gdgdgd88",
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null
                    }
                ]
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/new_product?per_page=1&page=1",
            "last": "http://local.pos.com/connector/api/new_product?per_page=1&page=1088",
            "prev": null,
            "next": "http://local.pos.com/connector/api/new_product?per_page=1&page=2"
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 1088,
            "path": "http://local.pos.com/connector/api/new_product",
            "per_page": "1",
            "to": 1,
            "total": 1088
        }
}
     */
    public function newProduct()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $filters = request()->only(['category_id', 'location_id', 'sub_category_id', 'product_custom_field_1', 'location_custom_field_1', 'per_page']);
        $filters['not_for_sell'] = request()->input('not_for_sell') == 1 ? true : false;

        $filters['product_ids'] = !empty(request()->input('product_ids')) ? explode(',', request()->input('product_ids')) : null;

        $search = request()->only(['sku', 'name']);

        //order
        $order_by = null;
        $order_direction = null;

        if(!empty(request()->input('order_by'))){
            $order_by = in_array(request()->input('order_by'), ['product_name', 'newest']) ? request()->input('order_by') : null;
            $order_direction = in_array(request()->input('order_direction'), ['asc', 'desc']) ? request()->input('order_direction') : 'asc';
        }

        $products = $this->__getProducts($business_id, $filters, $search, true, $order_by, $order_direction); 

        return NewProductResource::collection($products);
    }

     /**
     * Function to query product
     * @return Response
     */
    private function __getProducts($business_id, $filters = [], $search = [], $pagination = false, $order_by = null, $order_direction = null)
    {
        $query = Product::select('products.*')->where('products.business_id', $business_id);

        $with = ['product_variations.variations.variation_location_details', 'brand', 'unit', 'category', 'sub_category', 'product_tax', 'product_variations.variations.media', 'product_locations'];

        if (!empty($filters['category_id'])) {
            $category_ids = explode(',', $filters['category_id']);
            $query->whereIn('category_id', $category_ids);
        }

        if (!empty($filters['sub_category_id'])) {
            $sub_category_id = explode(',', $filters['sub_category_id']);
            $query->whereIn('sub_category_id', $sub_category_id);
        }

        if (!empty($filters['product_custom_field_1'])) {
            $query->where('product_custom_field1', $filters['product_custom_field_1']);
        }

        if (!empty($filters['not_for_sell'])) {
            $query->where('not_for_selling', 1);
        }

        if (!empty($filters['location_custom_field_1'])) {
            $query->leftjoin('product_locations as pl', 'pl.product_id', '=', 'products.id')
                ->leftjoin('business_locations as bl', 'pl.location_id', '=', 'bl.id')
                ->where('bl.custom_field1', $filters['location_custom_field_1']);
        }

        if (!empty($filters['location_id'])) {
            $location_id = $filters['location_id'];
            $query->whereHas('product_locations', function($q) use($location_id) {
                $q->where('product_locations.location_id', $location_id);
            });

            $with['product_variations.variations.variation_location_details'] = function($q) use($location_id) {
                $q->where('location_id', $location_id);
            };

            $with['product_locations'] = function($q) use($location_id) {
                $q->where('product_locations.location_id', $location_id);
            };
        }

        if (!empty($filters['product_ids'])) {
            $query->whereIn('id', $filters['product_ids']);
        }

        $query->groupBy('products.id');

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {

                if (!empty($search['name'])) {
                    $query->where('products.name', 'like', '%' . $search['name'] .'%');
                }
                
                if (!empty($search['sku'])) {
                    $sku = $search['sku'];
                    $query->orWhere('sku', 'like', '%' . $sku .'%');
                    $query->orWhereHas('variations', function($q) use($sku) {
                        $q->where('variations.sub_sku', 'like', '%' . $sku .'%');
                    });
                }
            });
        }

        //Order by
        if(!empty($order_by)){
            if($order_by == 'product_name'){
                $query->orderBy('products.name', $order_direction);
            }

            if($order_by == 'newest'){
                $query->orderBy('products.id', $order_direction);
            }
        }

        $query->with($with);

        $perPage = !empty($filters['per_page']) ? $filters['per_page'] : $this->perPage;
        if ($pagination && $perPage != -1) {
            $products = $query->paginate($perPage);
            $products->appends(request()->query());
        } else{
            $products = $query->get();
        }

        return $products;
    }

    /**
     * New List sells
     * @queryParam location_id id of the location Example: 1      
     * @queryParam contact_id id of the customer
     * @queryParam payment_status Comma separated values of payment statuses. Available values due, partial, paid, overdue Example: due,partial
     * @queryParam start_date format:Y-m-d Example: 2018-06-25
     * @queryParam end_date format:Y-m-d Example: 2018-06-25
     * @queryParam user_id id of the user who created the sale
     * @queryParam service_staff_id id of the service staff assigned with the sale
     * @queryParam shipping_status Shipping Status of the sale ('ordered', 'packed', 'shipped', 'delivered', 'cancelled') Example: ordered
     * @queryParam source Source of the sale
     * @queryParam customer_group_id id of the customer group
     * @queryParam product_name name of the product
     * @queryParam product_sku sku of the product or variation
     * @queryParam product_custom_field_1 custome field 1 of the product
     * @queryParam location_custom_field_1 custome field 1 of the location
     * @queryParam location_invoice_scheme_prefix Invoice scheme prefix of the location
     * @queryParam product_category_id category id of the product
     * @queryParam product_sub_category_id Sub category id of the product
     * @queryParam sell_ids comma separated ids of the sells Example: 55,64
     * @queryParam only_subscriptions Filter only subcription invoices (1, 0)
     * @queryParam send_purchase_details Get purchase details of each sell line (1, 0)
     * @queryParam order_by_date Sort sell list by date ('asc', 'desc') Example: desc
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     *
     * @response {
        "data": [
            {
                "id": 6,
                "business_id": 1,
                "location_id": 1,
                "res_table_id": null,
                "type": "sell",
                "status": "final",
                "is_quotation": 0,
                "payment_status": "paid",
                "contact_id": 4,
                "customer_group_id": null,
                "invoice_no": "AS0001",
                "ref_no": "",
                "source": null,
                "unique_hash": null,
                "hash_control": null,
                "subscription_no": null,
                "subscription_repeat_on": null,
                "transaction_date": "2018-04-10 13:23:21",
                "total_before_tax": "770.0000",
                "tax_id": null,
                "tax_amount": "0.0000",
                "discount_type": "percentage",
                "discount_amount": "0.0000",
                "rp_redeemed": 0,
                "rp_redeemed_amount": "0.0000",
                "shipping_details": null,
                "shipping_address": null,
                "shipping_status": null,
                "delivered_to": null,
                "additional_notes": null,
                "staff_note": null,
                "round_off_amount": "0.0000",
                "final_total": "770.0000",
                "expense_sub_category_id": null,
                "is_direct_sale": 0,
                "is_suspend": 0,
                "total_amount_recovered": null,
                "crm_is_order_request": 0,
                "mfg_production_cost": "0.0000",
                "mfg_production_cost_type": "percentage",
                "mfg_is_final": 0,
                "is_created_from_api": 0,
                "essentials_duration": "0.00",
                "essentials_duration_unit": null,
                "essentials_amount_per_unit_duration": "0.0000",
                "essentials_allowances": null,
                "essentials_deductions": null,
                "repair_completed_on": null,
                "repair_warranty_id": null,
                "repair_brand_id": null,
                "repair_status_id": null,
                "repair_model_id": null,
                "repair_job_sheet_id": null,
                "repair_defects": null,
                "repair_serial_no": null,
                "repair_checklist": null,
                "repair_security_pwd": null,
                "repair_security_pattern": null,
                "repair_due_date": null,
                "repair_device_id": null,
                "repair_updates_notif": 0,
                "invoice_token": "00f79f7fd292225e8c303a6bf8626d06",
                "pjt_project_id": null,
                "pjt_title": null,
                "woocommerce_order_id": null,
                "selling_price_group_id": null,
                "created_at": "2018-01-06 07:06:11",
                "updated_at": "2021-10-23 11:42:13",
                "location_name": "Location 1",
                "location_custom_field1": "gdgdgd88",
                "location_invoice_scheme_prefix": "AS",
                "table_name": null,
                "table_description": null,
                "contact": "Harry",
                "customer_group_name": "grp 2",
                "sell_lines": [
                    {
                        "id": 1,
                        "transaction_id": 6,
                        "product_id": 2,
                        "quantity": 10,
                        "quantity_returned": "0.0000",
                        "unit_price_before_discount": "70.0000",
                        "unit_price": "70.0000",
                        "line_discount_type": null,
                        "line_discount_amount": "0.0000",
                        "unit_price_inc_tax": "77.0000",
                        "item_tax": "7.0000",
                        "tax_id": 1,
                        "discount_id": null,
                        "lot_no_line_id": null,
                        "sell_line_note": null,
                        "woocommerce_line_items_id": null,
                        "children_type": "",
                        "created_at": "2018-01-06 07:06:11",
                        "updated_at": "2018-01-06 07:06:11",
                        "product_name": "Levis Men's Slimmy Fit Jeans",
                        "product_custom_field_1": null,
                        "product_type": "variable",
                        "product_sku": "AS0002",
                        "category": {
                            "id": 1,
                            "name": "Men's",
                            "business_id": 1,
                            "short_code": "sfefef",
                            "parent_id": 0,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null
                        },
                        "sub_category": {
                            "id": 4,
                            "name": "Jeans",
                            "business_id": 1,
                            "short_code": null,
                            "parent_id": 1,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null
                        },
                        "product_variations": {
                            "id": 3,
                            "name": "30",
                            "product_id": 2,
                            "sub_sku": "AS0002-2",
                            "product_variation_id": 2,
                            "default_purchase_price": "70.0000",
                            "dpp_inc_tax": "77.0000",
                            "default_sell_price": "70.0000",
                            "sell_price_inc_tax": "77.0000"
                        }
                    }
                ],
                "payment_lines": [
                    {
                        "id": 1,
                        "transaction_id": 6,
                        "business_id": null,
                        "is_return": 0,
                        "amount": "770.0000",
                        "method": "cash",
                        "card_type": "visa",
                        "paid_on": "2018-01-09 17:30:35",
                        "payment_ref_no": null
                    }
                ],
                "invoice_url": "http://local.pos.com/invoice/00f79f7fd292225e8c303a6bf8626d06",
                "payment_link": ""
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/new_sell?per_page=1&page=1",
            "last": "http://local.pos.com/connector/api/new_sell?per_page=1&page=213",
            "prev": null,
            "next": "http://local.pos.com/connector/api/new_sell?per_page=1&page=2"
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 213,
            "path": "http://local.pos.com/connector/api/new_sell",
            "per_page": "1",
            "to": 1,
            "total": 213
        }
    }
     */
    public function newSell()
    {
        $user = Auth::user();
        $business_id = $user->business_id;
        $is_admin = $this->commonUtil->is_admin($user, $business_id);

        if ( !$is_admin && !auth()->user()->hasAnyPermission(['sell.view', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']) ) {
            abort(403, 'Unauthorized action.');
        }

        $filters = request()->only(['location_id', 'contact_id', 'payment_status', 'start_date', 'end_date', 'user_id', 'service_staff_id', 'only_subscriptions', 'per_page', 'shipping_status', 'order_by_date', 'source', 'customer_group_id', 'product_name', 'product_sku', 'product_custom_field_1', 'location_custom_field_1', 'location_invoice_scheme_prefix', 'product_category_id', 'product_sub_category_id']);

        $with = ['sell_lines', 'payment_lines', 'sell_lines.product', 'sell_lines.product.category', 'sell_lines.product.sub_category', 'sell_lines.variations'];
        $query = Transaction::where('transactions.business_id', $business_id)
                        ->where('transactions.type', 'sell')
                        ->leftjoin('business_locations as bl', 'bl.id', '=', 'transactions.location_id')
                        ->leftjoin('invoice_schemes as isc', 'isc.id', '=', 'bl.invoice_scheme_id')
                        ->leftjoin('res_tables as tab', 'tab.id', '=', 'transactions.res_table_id')
                        ->leftjoin('contacts as c', 'c.id', '=', 'transactions.contact_id')
                        ->leftjoin('customer_groups as cg', 'c.customer_group_id', '=', 'cg.id');

        if (!empty(request()->input('sell_ids'))) {
            $sell_ids = explode(',', request()->input('sell_ids'));
            $query->whereIn('transactions.id', $sell_ids);
        }
        
        if (!empty(request()->input('send_purchase_details')) && request()->input('send_purchase_details') == 1) {
            $with[] = 'sell_lines.sell_line_purchase_lines';
            $with[] = 'sell_lines.sell_line_purchase_lines.purchase_line';
        }

        $query->with($with);

        $permitted_locations = $user->permitted_locations($business_id);
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!$user->can('direct_sell.view')) {
            $query->where( function($q) use ($user){
                if ($user->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                    $q->where('transactions.created_by', $user->id);
                }

                //if user is commission agent display only assigned sells
                if ($user->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                    $q->orWhere('transactions.commission_agent', $user->id);
                }
            });
        }

        if (!empty($filters['location_id'])) {
            $query->where('transactions.location_id', $filters['location_id']);
        }

        if (!empty($filters['contact_id'])) {
            $query->where('transactions.contact_id', $filters['contact_id']);
        }

        if (!empty($filters['customer_group_id'])) {
            $query->where('c.customer_group_id', $filters['customer_group_id']);
        }

        if (!empty($filters['location_custom_field_1'])) {
            $query->where('bl.custom_field1', $filters['location_custom_field_1']);
        }

        if (!empty($filters['location_invoice_scheme_prefix'])) {
            $location_invoice_scheme_prefix = $filters['location_invoice_scheme_prefix'];
            $query->where('isc.prefix', 'like', $location_invoice_scheme_prefix);
        }

        if (!empty($filters['product_name']) || !empty($filters['product_custom_field_1'])
            || !empty($filters['product_category_id']) || !empty($filters['product_sub_category_id'])) {
            $product_name = $filters['product_name'] ?? null;
            $product_custom_field1 = $filters['product_custom_field_1'] ?? '';
            $product_category_id = $filters['product_category_id'] ?? '';
            $product_sub_category_id = $filters['product_sub_category_id'] ?? '';

            $query->whereHas('sell_lines.product', function($q) use ($product_name, $product_custom_field1, $product_category_id, $product_sub_category_id){
                if (!empty($product_name)) {
                    $q->where('name', 'like', "%{$product_name}%");
                }

                if (!empty($product_custom_field1)) {
                    $q->where('product_custom_field1', $product_custom_field1);
                }

                if (!empty($product_category_id)) {
                    $q->where('category_id', $product_category_id);
                }

                if (!empty($product_sub_category_id)) {
                    $q->where('sub_category_id', $product_sub_category_id);
                }
                
            });
        }

        if (!empty($filters['product_sku'])) {
            $product_sku = $filters['product_sku'];
            $query->whereHas('sell_lines.product.variations', function($q) use ($product_sku){
                $q->where('sub_sku', 'like',  "{$product_sku}%");
            });
        }

        $payment_status = [];
        if (!empty($filters['payment_status'])) {
            $payment_status = explode(',', $filters['payment_status']);
        }

        if (!$is_admin) {
            $payment_status_arr = [];
            if (auth()->user()->can('view_paid_sells_only')) {
                $payment_status_arr[] = 'paid';
            }

            if (auth()->user()->can('view_due_sells_only')) {
                $payment_status_arr[] = 'due';
            }

            if (auth()->user()->can('view_partial_sells_only')) {
                $payment_status_arr[] = 'partial';
            }

            if (empty($payment_status_arr)) {
                if (auth()->user()->can('view_overdue_sells_only')) {
                    $sells->OverDue();
                }
            } else {
                if (auth()->user()->can('view_overdue_sells_only')) {
                    $sells->where( function($q) use($payment_status_arr){
                        $q->whereIn('transactions.payment_status', $payment_status_arr)
                        ->orWhere( function($qr) {
                            $qr->OverDue();
                        });

                    });
                } else {
                    $sells->whereIn('transactions.payment_status', $payment_status_arr);
                }
            }
        }

        if (!empty($payment_status)) {
            $query->where( function($q) use($payment_status) {
                $is_overdue = false;
                if (in_array('overdue', $payment_status)) {
                    $is_overdue = true;
                    $key = array_search('overdue', $payment_status);
                    unset($payment_status[$key]);
                }

                if (!empty($payment_status)) {
                    $q->whereIn('transactions.payment_status', $payment_status);
                }

                if ($is_overdue) {
                    $q->orWhere( function($qr) {
                        $qr->whereIn('transactions.payment_status', ['due', 'partial'])
                            ->whereNotNull('transactions.pay_term_number')
                            ->whereNotNull('transactions.pay_term_type')
                            ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
                    });
                }

            });
            
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('transactions.transaction_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('transactions.transaction_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['order_by_date'])) {
            $order_by_date = in_array($filters['order_by_date'], ['asc', 'desc']) ? $filters['order_by_date'] : 'desc';
            $query->orderBy('transactions.transaction_date', $order_by_date);
        }

        if (!empty($filters['user_id'])) {
            $query->where('transactions.created_by', $filters['user_id']);
        }
        
        if (!empty($filters['service_staff_id'])) {
            $query->where('transactions.res_waiter_id', $filters['service_staff_id']);
        }

        if (!empty($filters['shipping_status'])) {
            $query->where('transactions.shipping_status', $filters['shipping_status']);
        }

        if (!empty($filters['only_subscriptions']) && $filters['only_subscriptions'] == 1) {
            $query->where(function ($q) {
                $q->whereNotNull('transactions.recur_parent_id')
                    ->orWhere('transactions.is_recurring', 1);
            });
        }

        if (!empty($filters['source'])) {
            //only exception for woocommerce
            if ($filters['source'] == 'woocommerce') {
                $query->whereNotNull('transactions.woocommerce_order_id');
            } else {
                $query->where('transactions.source', $filters['source']);
            }
        }
        $query->select('transactions.*', 
            'bl.name as location_name', 
            'bl.custom_field1 as location_custom_field1',
            'isc.prefix as location_invoice_scheme_prefix',
            'tab.name as table_name',
            'tab.description as table_description',
            'c.name as contact',
            'cg.name as customer_group_name'
        )
            ->groupBy('transactions.id');

        $perPage = !empty($filters['per_page']) ? $filters['per_page'] : $this->perPage;
        if ($perPage == -1) {
            $sells = $query->get();
        } else {
            $sells = $query->paginate($perPage);
            $sells->appends(request()->query());
        }

        return NewSellResource::collection($sells);
        
    }

    /**
     * New List contact
     *
     * @queryParam type required Type of contact (supplier, customer)
     * @queryParam customer_group_id id of the customer group
     * @queryParam custom_field_1 Custom field 1 of the contact
     * @queryParam contact_ids comma separated ids of contacts Example: 2,3
     * @queryParam name Search term for contact name
     * @queryParam biz_name Search term for contact's business name
     * @queryParam mobile_num Search term for contact's mobile number
     * @queryParam contact_id Search term for contact's contact_id. Ex(CO0005)
     * @queryParam order_by Column name to sort the result, Column: name, supplier_business_name
     * @queryParam direction Direction to sort the result, Direction: desc, asc 
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     * @response {
            "data": [
                {
                    "id": 18,
                    "business_id": 1,
                    "type": "customer",
                    "name": "Mr. Rohit Kumar Agrawalla",
                    "prefix": "Mr.",
                    "first_name": "Rohit",
                    "middle_name": "Kumar",
                    "last_name": "Agrawalla",
                    "email": null,
                    "contact_status": "active",
                    "mobile": "8596859647",
                    "credit_limit": null,
                    "converted_by": null,
                    "converted_on": null,
                    "balance": "40.0000",
                    "total_rp": 0,
                    "total_rp_used": 0,
                    "total_rp_expired": 0,
                    "customer_group_id": 1,
                    "crm_source": null,
                    "crm_life_stage": null,
                    "custom_field1": null,
                    "custom_field2": null,
                    "custom_field3": null,
                    "custom_field4": null,
                    "custom_field5": null,
                    "custom_field6": null,
                    "custom_field7": null,
                    "custom_field8": null,
                    "custom_field9": null,
                    "custom_field10": null,
                    "remember_token": null,
                    "password": null
                }
            ],
            "links": {
                "first": "http://local.pos.com/connector/api/new_contactapi?customer_group_id=1&page=1",
                "last": "http://local.pos.com/connector/api/new_contactapi?customer_group_id=1&page=1",
                "prev": null,
                "next": null
            },
            "meta": {
                "current_page": 1,
                "from": 1,
                "last_page": 1,
                "path": "http://local.pos.com/connector/api/new_contactapi",
                "per_page": 10,
                "to": 1,
                "total": 1
            }
        }
     */
    public function newContactApi(Request $request)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $type = request()->get('type', null);

        $query = Contact::where('business_id', $business_id);
        
        if($type == 'supplier'){
            $query->onlySuppliers();
        }

        if($type == 'customer'){
            $query->onlyCustomers();
        }

        if (!empty(request()->input('custom_field_1'))) {
            $query->where('custom_field1', request()->input('custom_field_1'));
        }

        if (!empty(request()->input('customer_group_id'))) {
            $query->where('customer_group_id', request()->input('customer_group_id'));
        }
        if (!empty(request()->input('contact_ids'))) {
            $contact_ids = explode(',', request()->input('contact_ids'));
            $query->whereIn('id', $contact_ids);
        }
        

        $search = request()->only(['name', 'biz_name', 'mobile_num', 'contact_id']);
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {

                if (!empty($search['name'])) {
                    $query->where('contacts.name', 'like', '%' . $search['name'] .'%');
                }
                
                if (!empty($search['biz_name'])) {
                    $query->orWhere('contacts.supplier_business_name', 'like', '%' . $search['biz_name'] .'%');
                }

                if (!empty($search['mobile_num'])) {
                    $query->orWhere('contacts.mobile', 'like', '%' . $search['mobile_num'] .'%')
                        ->orWhere('contacts.landline', 'like', '%' . $search['mobile_num'] .'%')
                        ->orWhere('contacts.alternate_number', 'like', '%' . $search['mobile_num'] .'%');
                }

                if (!empty($search['contact_id'])) {
                    $query->orWhere('contacts.contact_id', 'like', '%' . $search['contact_id'] .'%');
                }

            });
        }

        $order_by = request()->input('order_by');
        $order_by_dir = request()->input('direction'); 
        if (!empty($order_by) && !empty($order_by_dir)) {
            $query->orderBy($order_by, $order_by_dir);
        }
        
        $per_page = !empty(request()->input('per_page')) ? request()->input('per_page') : $this->perPage;

        if ($per_page != -1) {
            $contacts = $query->paginate($per_page);
            $contacts->appends(request()->query());
            
        } else{
            $contacts = $query->get();
        }

        return NewContactResource::collection($contacts);
    }

}
