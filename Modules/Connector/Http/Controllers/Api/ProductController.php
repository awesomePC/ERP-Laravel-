<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\ProductResource;
use Modules\Connector\Transformers\VariationResource;
use Modules\Connector\Transformers\CommonResource;
use App\Product;
use App\Variation;
use App\SellingPriceGroup;

/**
 * @group Product management
 * @authenticated
 *
 * APIs for managing products
 */
class ProductController extends ApiController
{
    /**
     * List products
     * @queryParam order_by Values: product_name or newest
     * @queryParam order_direction Values: asc or desc
     * @queryParam brand_id comma separated ids of one or multiple brands
     * @queryParam category_id comma separated ids of one or multiple category
     * @queryParam sub_category_id comma separated ids of one or multiple sub-category
     * @queryParam location_id Example: 1
     * @queryParam selling_price_group (1, 0) 
     * @queryParam send_lot_detail Send lot details in each variation location details(1, 0) 
     * @queryParam name Search term for product name
     * @queryParam sku Search term for product sku
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     * @response {
        "data": [
            {
                "id": 1,
                "name": "Men's Reverse Fleece Crew",
                "business_id": 1,
                "type": "single",
                "sub_unit_ids": null,
                "enable_stock": 1,
                "alert_quantity": "5.0000",
                "sku": "AS0001",
                "barcode_type": "C128",
                "expiry_period": null,
                "expiry_period_type": null,
                "enable_sr_no": 0,
                "weight": null,
                "product_custom_field1": null,
                "product_custom_field2": null,
                "product_custom_field3": null,
                "product_custom_field4": null,
                "image": null,
                "woocommerce_media_id": null,
                "product_description": null,
                "created_by": 1,
                "warranty_id": null,
                "is_inactive": 0,
                "repair_model_id": null,
                "not_for_selling": 0,
                "ecom_shipping_class_id": null,
                "ecom_active_in_store": 1,
                "woocommerce_product_id": 356,
                "woocommerce_disable_sync": 0,
                "image_url": "http://local.pos.com/img/default.png",
                "product_variations": [
                    {
                        "id": 1,
                        "variation_template_id": null,
                        "name": "DUMMY",
                        "product_id": 1,
                        "is_dummy": 1,
                        "created_at": "2018-01-03 21:29:08",
                        "updated_at": "2018-01-03 21:29:08",
                        "variations": [
                            {
                                "id": 1,
                                "name": "DUMMY",
                                "product_id": 1,
                                "sub_sku": "AS0001",
                                "product_variation_id": 1,
                                "woocommerce_variation_id": null,
                                "variation_value_id": null,
                                "default_purchase_price": "130.0000",
                                "dpp_inc_tax": "143.0000",
                                "profit_percent": "0.0000",
                                "default_sell_price": "130.0000",
                                "sell_price_inc_tax": "143.0000",
                                "created_at": "2018-01-03 21:29:08",
                                "updated_at": "2020-06-09 00:23:22",
                                "deleted_at": null,
                                "combo_variations": null,
                                "variation_location_details": [
                                    {
                                        "id": 56,
                                        "product_id": 1,
                                        "product_variation_id": 1,
                                        "variation_id": 1,
                                        "location_id": 1,
                                        "qty_available": "20.0000",
                                        "created_at": "2020-06-08 23:46:40",
                                        "updated_at": "2020-06-08 23:46:40"
                                    }
                                ],
                                "media": [
                                    {
                                        "id": 1,
                                        "business_id": 1,
                                        "file_name": "1591686466_978227300_nn.jpeg",
                                        "description": null,
                                        "uploaded_by": 9,
                                        "model_type": "App\\Variation",
                                        "woocommerce_media_id": null,
                                        "model_id": 1,
                                        "created_at": "2020-06-09 00:07:46",
                                        "updated_at": "2020-06-09 00:07:46",
                                        "display_name": "nn.jpeg",
                                        "display_url": "http://local.pos.com/uploads/media/1591686466_978227300_nn.jpeg"
                                    }
                                ],
                                "discounts": [
                                    {
                                        "id": 2,
                                        "name": "FLAT 10%",
                                        "business_id": 1,
                                        "brand_id": null,
                                        "category_id": null,
                                        "location_id": 1,
                                        "priority": 2,
                                        "discount_type": "fixed",
                                        "discount_amount": "5.0000",
                                        "starts_at": "2021-09-01 11:45:00",
                                        "ends_at": "2021-09-30 11:45:00",
                                        "is_active": 1,
                                        "spg": null,
                                        "applicable_in_cg": 1,
                                        "created_at": "2021-09-01 11:46:00",
                                        "updated_at": "2021-09-01 12:12:55",
                                        "formated_starts_at": " 11:45",
                                        "formated_ends_at": " 11:45"
                                    }
                                ],
                                "selling_price_group": [
                                    {
                                        "id": 2,
                                        "variation_id": 1,
                                        "price_group_id": 1,
                                        "price_inc_tax": "140.0000",
                                        "created_at": "2020-06-09 00:23:31",
                                        "updated_at": "2020-06-09 00:23:31"
                                    }
                                ]
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
                    "base_unit_multiplier": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 15:15:20",
                    "updated_at": "2018-01-03 15:15:20"
                },
                "category": {
                    "id": 1,
                    "name": "Men's",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 0,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:06:34",
                    "updated_at": "2018-01-03 21:06:34"
                },
                "sub_category": {
                    "id": 5,
                    "name": "Shirts",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 1,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:08:18",
                    "updated_at": "2018-01-03 21:08:18"
                },
                "product_tax": {
                    "id": 1,
                    "business_id": 1,
                    "name": "VAT@10%",
                    "amount": 10,
                    "is_tax_group": 0,
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
                    "location_id": null,
                    "name": "Awesome Shop",
                    "landmark": "Linking Street",
                    "country": "USA",
                    "state": "Arizona",
                    "city": "Phoenix",
                    "zip_code": "85001",
                    "invoice_scheme_id": 1,
                    "invoice_layout_id": 1,
                    "selling_price_group_id": null,
                    "print_receipt_on_invoice": 1,
                    "receipt_printer_type": "browser",
                    "printer_id": null,
                    "mobile": null,
                    "alternate_number": null,
                    "email": null,
                    "website": null,
                    "featured_products": [
                        "5",
                        "71"
                    ],
                    "is_active": 1,
                    "default_payment_accounts": "{\"cash\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"card\":{\"is_enabled\":\"1\",\"account\":\"3\"},\"cheque\":{\"is_enabled\":\"1\",\"account\":\"2\"},\"bank_transfer\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"other\":{\"is_enabled\":\"1\",\"account\":\"3\"},\"custom_pay_1\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"custom_pay_2\":{\"is_enabled\":\"1\",\"account\":\"2\"},\"custom_pay_3\":{\"is_enabled\":\"1\",\"account\":\"3\"}}",
                    "custom_field1": null,
                    "custom_field2": null,
                    "custom_field3": null,
                    "custom_field4": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:20",
                    "updated_at": "2020-06-09 01:07:05",
                    "pivot": {
                        "product_id": 2,
                        "location_id": 1
                    }
                }]
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/product?page=1",
            "last": "http://local.pos.com/connector/api/product?page=32",
            "prev": null,
            "next": "http://local.pos.com/connector/api/product?page=2"
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "path": "http://local.pos.com/connector/api/product",
            "per_page": 10,
            "to": 10
        }
    }
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $filters = request()->only(['brand_id', 'category_id', 'location_id', 'sub_category_id', 'per_page']);
        $filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;

        $search = request()->only(['sku', 'name']);

        //order
        $order_by = null;
        $order_direction = null;

        if(!empty(request()->input('order_by'))){
            $order_by = in_array(request()->input('order_by'), ['product_name', 'newest']) ? request()->input('order_by') : null;
            $order_direction = in_array(request()->input('order_direction'), ['asc', 'desc']) ? request()->input('order_direction') : 'asc';
        }
        
        $products = $this->__getProducts($business_id, $filters, $search, true, $order_by, $order_direction); 

        return ProductResource::collection($products);
    }

    /**
     * Get the specified product
     * @urlParam product required comma separated ids of products Example: 1
     * @queryParam selling_price_group (1, 0) 
     * @queryParam send_lot_detail Send lot details in each variation location details(1, 0) 
     * @response {
            "data": [
                {
                    "id": 1,
                    "name": "Men's Reverse Fleece Crew",
                    "business_id": 1,
                    "type": "single",
                    "sub_unit_ids": null,
                    "enable_stock": 1,
                    "alert_quantity": "5.0000",
                    "sku": "AS0001",
                    "barcode_type": "C128",
                    "expiry_period": null,
                    "expiry_period_type": null,
                    "enable_sr_no": 0,
                    "weight": null,
                    "product_custom_field1": null,
                    "product_custom_field2": null,
                    "product_custom_field3": null,
                    "product_custom_field4": null,
                    "image": null,
                    "woocommerce_media_id": null,
                    "product_description": null,
                    "created_by": 1,
                    "warranty_id": null,
                    "is_inactive": 0,
                    "repair_model_id": null,
                    "not_for_selling": 0,
                    "ecom_shipping_class_id": null,
                    "ecom_active_in_store": 1,
                    "woocommerce_product_id": 356,
                    "woocommerce_disable_sync": 0,
                    "image_url": "http://local.pos.com/img/default.png",
                    "product_variations": [
                        {
                            "id": 1,
                            "variation_template_id": null,
                            "name": "DUMMY",
                            "product_id": 1,
                            "is_dummy": 1,
                            "created_at": "2018-01-03 21:29:08",
                            "updated_at": "2018-01-03 21:29:08",
                            "variations": [
                                {
                                    "id": 1,
                                    "name": "DUMMY",
                                    "product_id": 1,
                                    "sub_sku": "AS0001",
                                    "product_variation_id": 1,
                                    "woocommerce_variation_id": null,
                                    "variation_value_id": null,
                                    "default_purchase_price": "130.0000",
                                    "dpp_inc_tax": "143.0000",
                                    "profit_percent": "0.0000",
                                    "default_sell_price": "130.0000",
                                    "sell_price_inc_tax": "143.0000",
                                    "created_at": "2018-01-03 21:29:08",
                                    "updated_at": "2020-06-09 00:23:22",
                                    "deleted_at": null,
                                    "combo_variations": null,
                                    "variation_location_details": [
                                        {
                                            "id": 56,
                                            "product_id": 1,
                                            "product_variation_id": 1,
                                            "variation_id": 1,
                                            "location_id": 1,
                                            "qty_available": "20.0000",
                                            "created_at": "2020-06-08 23:46:40",
                                            "updated_at": "2020-06-08 23:46:40"
                                        }
                                    ],
                                    "media": [
                                        {
                                            "id": 1,
                                            "business_id": 1,
                                            "file_name": "1591686466_978227300_nn.jpeg",
                                            "description": null,
                                            "uploaded_by": 9,
                                            "model_type": "App\\Variation",
                                            "woocommerce_media_id": null,
                                            "model_id": 1,
                                            "created_at": "2020-06-09 00:07:46",
                                            "updated_at": "2020-06-09 00:07:46",
                                            "display_name": "nn.jpeg",
                                            "display_url": "http://local.pos.com/uploads/media/1591686466_978227300_nn.jpeg"
                                        }
                                    ],
                                    "discounts": [
                                        {
                                            "id": 2,
                                            "name": "FLAT 10%",
                                            "business_id": 1,
                                            "brand_id": null,
                                            "category_id": null,
                                            "location_id": 1,
                                            "priority": 2,
                                            "discount_type": "fixed",
                                            "discount_amount": "5.0000",
                                            "starts_at": "2021-09-01 11:45:00",
                                            "ends_at": "2021-09-30 11:45:00",
                                            "is_active": 1,
                                            "spg": null,
                                            "applicable_in_cg": 1,
                                            "created_at": "2021-09-01 11:46:00",
                                            "updated_at": "2021-09-01 12:12:55",
                                            "formated_starts_at": " 11:45",
                                            "formated_ends_at": " 11:45"
                                        }
                                    ],
                                    "selling_price_group": [
                                        {
                                            "id": 2,
                                            "variation_id": 1,
                                            "price_group_id": 1,
                                            "price_inc_tax": "140.0000",
                                            "created_at": "2020-06-09 00:23:31",
                                            "updated_at": "2020-06-09 00:23:31"
                                        }
                                    ]
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
                        "base_unit_multiplier": null,
                        "created_by": 1,
                        "deleted_at": null,
                        "created_at": "2018-01-03 15:15:20",
                        "updated_at": "2018-01-03 15:15:20"
                    },
                    "category": {
                        "id": 1,
                        "name": "Men's",
                        "business_id": 1,
                        "short_code": null,
                        "parent_id": 0,
                        "created_by": 1,
                        "category_type": "product",
                        "description": null,
                        "slug": null,
                        "woocommerce_cat_id": null,
                        "deleted_at": null,
                        "created_at": "2018-01-03 21:06:34",
                        "updated_at": "2018-01-03 21:06:34"
                    },
                    "sub_category": {
                        "id": 5,
                        "name": "Shirts",
                        "business_id": 1,
                        "short_code": null,
                        "parent_id": 1,
                        "created_by": 1,
                        "category_type": "product",
                        "description": null,
                        "slug": null,
                        "woocommerce_cat_id": null,
                        "deleted_at": null,
                        "created_at": "2018-01-03 21:08:18",
                        "updated_at": "2018-01-03 21:08:18"
                    },
                    "product_tax": {
                        "id": 1,
                        "business_id": 1,
                        "name": "VAT@10%",
                        "amount": 10,
                        "is_tax_group": 0,
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
                        "location_id": null,
                        "name": "Awesome Shop",
                        "landmark": "Linking Street",
                        "country": "USA",
                        "state": "Arizona",
                        "city": "Phoenix",
                        "zip_code": "85001",
                        "invoice_scheme_id": 1,
                        "invoice_layout_id": 1,
                        "selling_price_group_id": null,
                        "print_receipt_on_invoice": 1,
                        "receipt_printer_type": "browser",
                        "printer_id": null,
                        "mobile": null,
                        "alternate_number": null,
                        "email": null,
                        "website": null,
                        "featured_products": [
                            "5",
                            "71"
                        ],
                        "is_active": 1,
                        "default_payment_accounts": "{\"cash\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"card\":{\"is_enabled\":\"1\",\"account\":\"3\"},\"cheque\":{\"is_enabled\":\"1\",\"account\":\"2\"},\"bank_transfer\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"other\":{\"is_enabled\":\"1\",\"account\":\"3\"},\"custom_pay_1\":{\"is_enabled\":\"1\",\"account\":\"1\"},\"custom_pay_2\":{\"is_enabled\":\"1\",\"account\":\"2\"},\"custom_pay_3\":{\"is_enabled\":\"1\",\"account\":\"3\"}}",
                        "custom_field1": null,
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null,
                        "deleted_at": null,
                        "created_at": "2018-01-04 02:15:20",
                        "updated_at": "2020-06-09 01:07:05",
                        "pivot": {
                            "product_id": 2,
                            "location_id": 1
                        }
                    }]
                }
            ]
        }
     */
    public function show($product_ids)
    {
        $user = Auth::user();

        // if (!$user->can('api.access')) {
        //     return $this->respondUnauthorized();
        // }

        $business_id = $user->business_id;
        $filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;

        $filters['product_ids'] = explode(',', $product_ids);

        $products = $this->__getProducts($business_id, $filters);

        return ProductResource::collection($products);
    }

    /**
     * Function to query product
     * @return Response
     */
    private function __getProducts($business_id, $filters = [], $search = [], $pagination = false, $order_by = null, $order_direction = null)
    {
        $query = Product::where('business_id', $business_id);

        $with = ['product_variations.variations.variation_location_details', 'brand', 'unit', 'category', 'sub_category', 'product_tax', 'product_variations.variations.media', 'product_locations'];

        if (!empty($filters['category_id'])) {
            $category_ids = explode(',', $filters['category_id']);
            $query->whereIn('category_id', $category_ids);
        }

        if (!empty($filters['sub_category_id'])) {
            $sub_category_id = explode(',', $filters['sub_category_id']);
            $query->whereIn('sub_category_id', $sub_category_id);
        }

        if (!empty($filters['brand_id'])) {
            $brand_ids = explode(',', $filters['brand_id']);
            $query->whereIn('brand_id', $brand_ids);
        }

        if (!empty($filters['selling_price_group']) && $filters['selling_price_group'] == true) {
            $with[] = 'product_variations.variations.group_prices';
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
     * List Variations
     * @urlParam id comma separated ids of variations Example: 2
     * @queryParam product_id Filter by comma separated products ids
     * @queryParam location_id Example: 1
     * @queryParam brand_id
     * @queryParam category_id
     * @queryParam sub_category_id
     * @queryParam not_for_selling Values: 0 or 1
     * @queryParam name Search term for product name
     * @queryParam sku Search term for product sku
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:10
     * @response {
        "data": [
            {
                "variation_id": 1,
                "variation_name": "",
                "sub_sku": "AS0001",
                "product_id": 1,
                "product_name": "Men's Reverse Fleece Crew",
                "sku": "AS0001",
                "type": "single",
                "business_id": 1,
                "barcode_type": "C128",
                "expiry_period": null,
                "expiry_period_type": null,
                "enable_sr_no": 0,
                "weight": null,
                "product_custom_field1": null,
                "product_custom_field2": null,
                "product_custom_field3": null,
                "product_custom_field4": null,
                "product_image": "1528728059_fleece_crew.jpg",
                "product_description": null,
                "warranty_id": null,
                "brand_id": 1,
                "brand_name": "Levis",
                "unit_id": 1,
                "enable_stock": 1,
                "not_for_selling": 0,
                "unit_name": "Pc(s)",
                "unit_allow_decimal": 0,
                "category_id": 1,
                "category": "Men's",
                "sub_category_id": 5,
                "sub_category": "Shirts",
                "tax_id": 1,
                "tax_type": "exclusive",
                "tax_name": "VAT@10%",
                "tax_amount": 10,
                "product_variation_id": 1,
                "default_purchase_price": "130.0000",
                "dpp_inc_tax": "143.0000",
                "profit_percent": "0.0000",
                "default_sell_price": "130.0000",
                "sell_price_inc_tax": "143.0000",
                "product_variation_name": "",
                "variation_location_details": [],
                "media": [],
                "selling_price_group": [],
                "product_image_url": "http://local.pos.com/uploads/img/1528728059_fleece_crew.jpg",
                "product_locations": [
                    {
                        "id": 1,
                        "business_id": 1,
                        "location_id": null,
                        "name": "Awesome Shop",
                        "landmark": "Linking Street",
                        "country": "USA",
                        "state": "Arizona",
                        "city": "Phoenix",
                        "zip_code": "85001",
                        "invoice_scheme_id": 1,
                        "invoice_layout_id": 1,
                        "selling_price_group_id": null,
                        "print_receipt_on_invoice": 1,
                        "receipt_printer_type": "browser",
                        "printer_id": null,
                        "mobile": null,
                        "alternate_number": null,
                        "email": null,
                        "website": null,
                        "featured_products": null,
                        "is_active": 1,
                        "default_payment_accounts": "",
                        "custom_field1": null,
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null,
                        "deleted_at": null,
                        "created_at": "2018-01-04 02:15:20",
                        "updated_at": "2019-12-11 04:53:39",
                        "pivot": {
                            "product_id": 1,
                            "location_id": 1
                        }
                    }
                ]
            },
            {
                "variation_id": 2,
                "variation_name": "28",
                "sub_sku": "AS0002-1",
                "product_id": 2,
                "product_name": "Levis Men's Slimmy Fit Jeans",
                "sku": "AS0002",
                "type": "variable",
                "business_id": 1,
                "barcode_type": "C128",
                "expiry_period": null,
                "expiry_period_type": null,
                "enable_sr_no": 0,
                "weight": null,
                "product_custom_field1": null,
                "product_custom_field2": null,
                "product_custom_field3": null,
                "product_custom_field4": null,
                "product_image": "1528727964_levis_jeans.jpg",
                "product_description": null,
                "warranty_id": null,
                "brand_id": 1,
                "brand_name": "Levis",
                "unit_id": 1,
                "enable_stock": 1,
                "not_for_selling": 0,
                "unit_name": "Pc(s)",
                "unit_allow_decimal": 0,
                "category_id": 1,
                "category": "Men's",
                "sub_category_id": 4,
                "sub_category": "Jeans",
                "tax_id": 1,
                "tax_type": "exclusive",
                "tax_name": "VAT@10%",
                "tax_amount": 10,
                "product_variation_id": 2,
                "default_purchase_price": "70.0000",
                "dpp_inc_tax": "77.0000",
                "profit_percent": "0.0000",
                "default_sell_price": "70.0000",
                "sell_price_inc_tax": "77.0000",
                "product_variation_name": "Waist Size",
                "variation_location_details": [
                    {
                        "id": 1,
                        "product_id": 2,
                        "product_variation_id": 2,
                        "variation_id": 2,
                        "location_id": 1,
                        "qty_available": "50.0000",
                        "created_at": "2018-01-06 06:57:11",
                        "updated_at": "2020-08-04 04:11:27"
                    }
                ],
                "media": [
                    {
                        "id": 1,
                        "business_id": 1,
                        "file_name": "1596701997_743693452_test.jpg",
                        "description": null,
                        "uploaded_by": 9,
                        "model_type": "App\\Variation",
                        "woocommerce_media_id": null,
                        "model_id": 2,
                        "created_at": "2020-08-06 13:49:57",
                        "updated_at": "2020-08-06 13:49:57",
                        "display_name": "test.jpg",
                        "display_url": "http://local.pos.com/uploads/media/1596701997_743693452_test.jpg"
                    }
                ],
                "selling_price_group": [],
                "product_image_url": "http://local.pos.com/uploads/img/1528727964_levis_jeans.jpg",
                "product_locations": [
                    {
                        "id": 1,
                        "business_id": 1,
                        "location_id": null,
                        "name": "Awesome Shop",
                        "landmark": "Linking Street",
                        "country": "USA",
                        "state": "Arizona",
                        "city": "Phoenix",
                        "zip_code": "85001",
                        "invoice_scheme_id": 1,
                        "invoice_layout_id": 1,
                        "selling_price_group_id": null,
                        "print_receipt_on_invoice": 1,
                        "receipt_printer_type": "browser",
                        "printer_id": null,
                        "mobile": null,
                        "alternate_number": null,
                        "email": null,
                        "website": null,
                        "featured_products": null,
                        "is_active": 1,
                        "default_payment_accounts": "",
                        "custom_field1": null,
                        "custom_field2": null,
                        "custom_field3": null,
                        "custom_field4": null,
                        "deleted_at": null,
                        "created_at": "2018-01-04 02:15:20",
                        "updated_at": "2019-12-11 04:53:39",
                        "pivot": {
                            "product_id": 2,
                            "location_id": 1
                        }
                    }
                ],
                "discounts": [
                    {
                        "id": 2,
                        "name": "FLAT 10%",
                        "business_id": 1,
                        "brand_id": null,
                        "category_id": null,
                        "location_id": 1,
                        "priority": 2,
                        "discount_type": "fixed",
                        "discount_amount": "5.0000",
                        "starts_at": "2021-09-01 11:45:00",
                        "ends_at": "2021-09-30 11:45:00",
                        "is_active": 1,
                        "spg": null,
                        "applicable_in_cg": 1,
                        "created_at": "2021-09-01 11:46:00",
                        "updated_at": "2021-09-01 12:12:55",
                        "formated_starts_at": " 11:45",
                        "formated_ends_at": " 11:45"
                    }
                ]
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/variation?page=1",
            "last": null,
            "prev": null,
            "next": "http://local.pos.com/connector/api/variation?page=2"
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "path": "http://local.pos.com/connector/api/variation",
            "per_page": "2",
            "to": 2
        }
    }
     */
    public function listVariations($variation_ids = null)
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
                ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
                ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('tax_rates as tr', 'p.tax', '=', 'tr.id')
                ->leftjoin('brands', function ($join) {
                    $join->on('p.brand_id', '=', 'brands.id')
                        ->whereNull('brands.deleted_at');
                })
                ->leftjoin('categories as c', 'p.category_id', '=', 'c.id')
                ->leftjoin('categories as sc', 'p.sub_category_id', '=', 'sc.id')
                ->where('p.business_id', $business_id)
                ->select(
                    'variations.id',
                    'variations.name as variation_name',
                    'variations.sub_sku',
                    'p.id as product_id',
                    'p.name as product_name',
                    'p.sku',
                    'p.type as type',
                    'p.business_id', 
                    'p.barcode_type',
                    'p.expiry_period',
                    'p.expiry_period_type',
                    'p.enable_sr_no',
                    'p.weight',
                    'p.product_custom_field1',
                    'p.product_custom_field2',
                    'p.product_custom_field3',
                    'p.product_custom_field4',
                    'p.image as product_image',
                    'p.product_description',
                    'p.warranty_id',
                    'p.brand_id',
                    'brands.name as brand_name',
                    'p.unit_id',
                    'p.enable_stock',
                    'p.not_for_selling',
                    'units.short_name as unit_name',
                    'units.allow_decimal as unit_allow_decimal',
                    'p.category_id',
                    'c.name as category',
                    'p.sub_category_id',
                    'sc.name as sub_category',
                    'p.tax as tax_id',
                    'p.tax_type',
                    'tr.name as tax_name',
                    'tr.amount as tax_amount',
                    'variations.product_variation_id',
                    'variations.default_purchase_price',
                    'variations.dpp_inc_tax',
                    'variations.profit_percent',
                    'variations.default_sell_price',
                    'variations.sell_price_inc_tax',
                    'pv.id as product_variation_id',
                    'pv.name as product_variation_name'
                );

        $with = [
                    'variation_location_details', 
                    'media', 
                    'group_prices',
                    'product',
                    'product.product_locations'
                ];

        if (!empty(request()->input('category_id'))) {
            $query->where('category_id', request()->input('category_id'));
        }

        if (!empty(request()->input('sub_category_id'))) {
            $query->where('p.sub_category_id', request()->input('sub_category_id'));
        }

        if (!empty(request()->input('brand_id'))) {
            $query->where('p.brand_id', request()->input('brand_id'));
        }

        if (request()->has('not_for_selling')) {
            $not_for_selling = request()->input('not_for_selling') == 1 ? 1 : 0;
            $query->where('p.not_for_selling', $not_for_selling);
        }
        $filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;

        if (!empty(request()->input('location_id'))) {
            $location_id = request()->input('location_id');
            $query->whereHas('product.product_locations', function($q) use($location_id) {
                $q->where('product_locations.location_id', $location_id);
            });

            $with['variation_location_details'] = function($q) use($location_id) {
                $q->where('location_id', $location_id);
            };

            $with['product.product_locations'] = function($q) use($location_id) {
                $q->where('product_locations.location_id', $location_id);
            };
        }

        $search = request()->only(['sku', 'name']);

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {

                if (!empty($search['name'])) {
                    $query->where('p.name', 'like', '%' . $search['name'] .'%');
                }
                
                if (!empty($search['sku'])) {
                    $sku = $search['sku'];
                    $query->orWhere('p.sku', 'like', '%' . $sku .'%')
                        ->where('variations.sub_sku', 'like', '%' . $sku .'%');
                }
            });
        }

        //filter by variations ids
        if (!empty($variation_ids)) {
            $variation_ids = explode(',', $variation_ids);
            $query->whereIn('variations.id', $variation_ids);
        }

        //filter by product ids
        if (!empty(request()->input('product_id'))) {
            $product_ids = explode(',', request()->input('product_id'));
            $query->whereIn('p.id', $product_ids);
        }

        $query->with($with);

        $perPage = !empty(request()->input('per_page')) ? request()->input('per_page') : $this->perPage;
        if ($perPage == -1) {
            $variations = $query->get();
        } else {
            //paginate
            $variations = $query->paginate($perPage);
            $variations->appends(request()->query());
        }

        return VariationResource::collection($variations);
    }

    /**
     * List Selling Price Group
     *
     * @response {
        "data": [
            {
                "id": 1,
                "name": "Retail",
                "description": null,
                "business_id": 1,
                "is_active": 1,
                "deleted_at": null,
                "created_at": "2020-10-21 04:30:06",
                "updated_at": "2020-11-16 18:23:15"
            },
            {
                "id": 2,
                "name": "Wholesale",
                "description": null,
                "business_id": 1,
                "is_active": 1,
                "deleted_at": null,
                "created_at": "2020-10-21 04:30:21",
                "updated_at": "2020-11-16 18:23:00"
            }
        ]
    }
     */
    public function getSellingPriceGroup()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                                        ->get();

        return CommonResource::collection($price_groups);
    }
}
