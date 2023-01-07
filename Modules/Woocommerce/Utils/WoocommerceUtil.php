<?php
namespace Modules\Woocommerce\Utils;

use App\Business;
use App\Category;
use App\Contact;
use App\Exceptions\PurchaseSellMismatch;
use App\Product;
use App\TaxRate;
use App\Transaction;
use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

use App\Utils\Util;
use App\Utils\ContactUtil;

use App\VariationLocationDetails;
use App\VariationTemplate;
use Automattic\WooCommerce\Client;

use DB;
use Modules\Woocommerce\Entities\WoocommerceSyncLog;

use Modules\Woocommerce\Exceptions\WooCommerceError;

class WoocommerceUtil extends Util
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }


    public function get_api_settings($business_id)
    {
        $business = Business::find($business_id);
        $woocommerce_api_settings = json_decode($business->woocommerce_api_settings);
        return $woocommerce_api_settings;
    }

    private function add_to_skipped_orders($business, $order_id)
    {
        $business = !is_object($business) ? Business::find($business) : $business;
        $skipped_orders = !empty($business->woocommerce_skipped_orders) ? json_decode($business->woocommerce_skipped_orders, true) : [];
        if (!in_array($order_id, $skipped_orders)) {
            $skipped_orders[] = $order_id;
        }

        $business->woocommerce_skipped_orders = json_encode($skipped_orders);
        $business->save();
    }

    private function remove_from_skipped_orders($business, $order_id)
    {
        $business = !is_object($business) ? Business::find($business) : $business;
        $skipped_orders = !empty($business->woocommerce_skipped_orders) ? json_decode($business->woocommerce_skipped_orders, true) : [];
        if (in_array($order_id, $skipped_orders)) {
            $skipped_orders = array_diff($skipped_orders, [$order_id]);
        }

        $business->woocommerce_skipped_orders = json_encode($skipped_orders);
        $business->save();
    }

    /**
     * Creates Automattic\WooCommerce\Client object
     * @param int $business_id
     * @return obj
     */
    public function woo_client($business_id)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);
        if (empty($woocommerce_api_settings)) {
            throw new WooCommerceError(__("woocommerce::lang.unable_to_connect"));
        }

        $woocommerce = new Client(
            $woocommerce_api_settings->woocommerce_app_url,
            $woocommerce_api_settings->woocommerce_consumer_key,
            $woocommerce_api_settings->woocommerce_consumer_secret,
            [
                'wp_api' => true,
                'version' => 'wc/v2',
                'timeout' => 10000,
                'verify_ssl' => false
            ]
        );

        return $woocommerce;
    }

    public function syncCat($business_id, $data, $type, $new_categories = [])
    {

        //woocommerce api client object
        $woocommerce = $this->woo_client($business_id);
        $count = 0;
        foreach (array_chunk($data, 99) as $chunked_array) {
            $sync_data = [];
            $sync_data[$type] = $chunked_array;
            //Batch update categories

            $response = $woocommerce->post('products/categories/batch', $sync_data);

            //update woocommerce_cat_id
            if (!empty($response->create)) {
                foreach ($response->create as $key => $value) {
                    $new_category = $new_categories[$count];
                    if ($value->id != 0) {
                        $new_category->woocommerce_cat_id = $value->id;
                    } else {
                        if (!empty($value->error->data->resource_id)) {
                            $new_category->woocommerce_cat_id = $value->error->data->resource_id;
                        }
                    }
                    $new_category->save();
                    $count++;
                }
            }
        }
    }

    /**
     * Synchronizes pos categories with Woocommerce categories
     * @param int $business_id
     * @return Void
     */
    public function syncCategories($business_id, $user_id)
    {
        $last_synced = $this->getLastSync($business_id, 'categories', false);

        //Update parent categories
        $query = Category::where('business_id', $business_id)
                        ->where('category_type', 'product')
                        ->where('parent_id', 0);

        //Limit query to last sync
        if (!empty($last_synced)) {
            $query->where('updated_at', '>', $last_synced);
        }

        $categories = $query->get();

        $category_data = [];
        $new_categories = [];
        $created_data = [];
        $updated_data = [];
        foreach ($categories as $category) {
            if (empty($category->woocommerce_cat_id)) {
                $category_data['create'][] = [
                    'name' => $category->name
                ];
                $new_categories[] = $category;
                $created_data[] = $category->name;
            } else {
                $category_data['update'][] = [
                    'id' => $category->woocommerce_cat_id,
                    'name' => $category->name
                ];
                $updated_data[] = $category->name;
            }
        }

        if (!empty($category_data['create'])) {
            $this->syncCat($business_id, $category_data['create'], 'create', $new_categories);
        }
        if (!empty($category_data['update'])) {
            $this->syncCat($business_id, $category_data['update'], 'update', $new_categories);
        }

        //Sync child categories
        $query2 = Category::where('business_id', $business_id)
                        ->where('category_type', 'product')
                        ->where('parent_id', '!=', 0);
        //Limit query to last sync
        if (!empty($last_synced)) {
            $query2->where('updated_at', '>', $last_synced);
        }

        $child_categories = $query2->get();

        $cat_id_woocommerce_id = Category::where('business_id', $business_id)
                                    ->where('parent_id', 0)
                                    ->where('category_type', 'product')
                                    ->pluck('woocommerce_cat_id', 'id')
                                    ->toArray();

        $category_data = [];
        $new_categories = [];
        foreach ($child_categories as $category) {
            if (empty($cat_id_woocommerce_id[$category->parent_id])) {
                continue;
            }

            if (empty($category->woocommerce_cat_id)) {
                $category_data['create'][] = [
                    'name' => $category->name,
                    'parent' => $cat_id_woocommerce_id[$category->parent_id]
                ];
                $new_categories[] = $category;
                $created_data[] = $category->name;
            } else {
                $category_data['update'][] = [
                    'id' => $category->woocommerce_cat_id,
                    'name' => $category->name,
                    'parent' => $cat_id_woocommerce_id[$category->parent_id]
                ];
                $updated_data[] = $category->name;
            }
        }

        if (!empty($category_data['create'])) {
            $this->syncCat($business_id, $category_data['create'], 'create', $new_categories);
        }
        if (!empty($category_data['update'])) {
            $this->syncCat($business_id, $category_data['update'], 'update', $new_categories);
        }

        //Create log
        if (!empty($created_data)) {
            $this->createSyncLog($business_id, $user_id, 'categories', 'created', $created_data);
        }
        if (!empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'categories', 'updated', $updated_data);
        }
        if (empty($created_data) && empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'categories');
        }
    }

    /**
     * Synchronizes pos products with Woocommerce products
     * @param int $business_id
     * @return Void
     */
    public function syncProducts($business_id, $user_id, $sync_type, $limit = 100, $page = 0)
    {
        //$limit is zero for console command
        if ($page == 0 || $limit == 0) {
            //Sync Categories
            $this->syncCategories($business_id, $user_id);

            //Sync variation attributes
            $this->syncVariationAttributes($business_id);

            if ($limit > 0) {
                request()->session()->forget('last_product_synced');
            }
        }

        $last_synced = !empty(session('last_product_synced')) ? session('last_product_synced') : $this->getLastSync($business_id, 'all_products', false);
        //store last_synced if page is 0
        if ($page == 0) {
            session(['last_product_synced' => $last_synced]);
        }
        
        $woocommerce_api_settings = $this->get_api_settings($business_id);
        $created_data = [];
        $updated_data = [];

        $business_location_id = $woocommerce_api_settings->location_id;
        $offset = $page * $limit;
        $query = Product::where('business_id', $business_id)
                        ->whereIn('type', ['single', 'variable'])
                        ->where('woocommerce_disable_sync', 0)
                        ->with(['variations', 'category', 'sub_category',
                            'variations.variation_location_details',
                            'variations.product_variation',
                            'variations.product_variation.variation_template']);

        if ($limit > 0) {
            $query->limit($limit)
                ->offset($offset);
        }
                        
        if ($sync_type == 'new') {
            $query->whereNull('woocommerce_product_id');
        }

        //Select products only from selected location
        if (!empty($business_location_id)) {
            $query->ForLocation($business_location_id);
        }

        $all_products = $query->get();
        $product_data = [];
        $new_products = [];
        $updated_products = [];

        if (count($all_products) == 0) {
            request()->session()->forget('last_product_synced');
        }
        
        foreach ($all_products as $product) {
            //Skip product if last updated is less than last sync
            $last_updated = $product->updated_at;
            //check last stock updated
            $last_stock_updated = $this->getLastStockUpdated($business_location_id, $product->id);

            if (!empty($last_stock_updated)) {
                $last_updated = strtotime($last_stock_updated) > strtotime($last_updated) ?
                        $last_stock_updated : $last_updated;
            }
            if (!empty($product->woocommerce_product_id) && !empty($last_synced) && strtotime($last_updated) < strtotime($last_synced)) {
                continue;
            }

            //Set common data
            $array = [
                'type' => $product->type == 'single' ? 'simple' : 'variable',
                'sku' => $product->sku
            ];

            $manage_stock = false;
            if ($product->enable_stock == 1 && $product->type == 'single') {
                $manage_stock = true;
            }

            //Get details from first variation for single product only
            $first_variation = $product->variations->first();
            if (empty($first_variation)) {
                continue;
            }
            $price = $woocommerce_api_settings->product_tax_type == 'exc' ? $first_variation->default_sell_price : $first_variation->sell_price_inc_tax;

            if (!empty($woocommerce_api_settings->default_selling_price_group)) {
                $group_prices = $this->productUtil->getVariationGroupPrice($first_variation->id, $woocommerce_api_settings->default_selling_price_group, $product->tax_id);

                $price = $woocommerce_api_settings->product_tax_type == 'exc' ? $group_prices['price_exc_tax'] : $group_prices['price_inc_tax'];
            }

            //Set product stock
            $qty_available = 0;
            if ($manage_stock) {
                $variation_location_details = $first_variation->variation_location_details;
                foreach ($variation_location_details as $vld) {
                    if ($vld->location_id == $business_location_id) {
                        $qty_available = $vld->qty_available;
                    }
                }
            }

            //Set product category
            $product_cat = [];
            if (!empty($product->category)) {
                $product_cat[] = ['id' => $product->category->woocommerce_cat_id];
            }
            if (!empty($product->sub_category)) {
                $product_cat[] = ['id' => $product->sub_category->woocommerce_cat_id];
            }

            //set attributes for variable products
            if ($product->type == 'variable') {
                $variation_attr_data = [];

                foreach ($product->variations as $variation) {
                    if (!empty($variation->product_variation->variation_template->woocommerce_attr_id)) {
                        $woocommerce_attr_id = $variation->product_variation->variation_template->woocommerce_attr_id;
                        $variation_attr_data[$woocommerce_attr_id][] = $variation->name;
                    }
                }

                foreach ($variation_attr_data as $key => $value) {
                    $array['attributes'][] = [
                        'id' => $key,
                        'variation' => true,
                        'visible'   => true,
                        'options' => $value
                    ];
                }
            }

            $sync_description_as = !empty($woocommerce_api_settings->sync_description_as) ? $woocommerce_api_settings->sync_description_as : 'long';

            if (empty($product->woocommerce_product_id)) {
                $array['tax_class'] = !empty($woocommerce_api_settings->default_tax_class) ?
                $woocommerce_api_settings->default_tax_class : 'standard';

                //assign category
                if (in_array('category', $woocommerce_api_settings->product_fields_for_create)) {
                    if (!empty($product_cat)) {
                        $array['categories'] = $product_cat;
                    }
                }

                if (in_array('weight', $woocommerce_api_settings->product_fields_for_create)) {
                    $array['weight'] = $this->formatDecimalPoint($product->weight);
                }

                //sync product description
                if (in_array('description', $woocommerce_api_settings->product_fields_for_create)) {
                    if ($sync_description_as == 'long') {
                        $array['description'] = $product->product_description;
                    } elseif ($sync_description_as == 'short') {
                        $array['short_description'] = $product->product_description;
                    } else {
                        $array['description'] = $product->product_description;
                        $array['short_description'] = $product->product_description;
                    }
                }

                //Set product image url
                //If media id is set use media id else use image src
                if (!empty($product->image) && in_array('image', $woocommerce_api_settings->product_fields_for_create)) {
                    if ($this->isValidImage($product->image_path)) {
                        $array['images'] = !empty($product->woocommerce_media_id) ? [['id' => $product->woocommerce_media_id]] : [['src' => $product->image_url]];
                    }
                }

                //assign quantity and price if single product
                if ($product->type == 'single') {
                    $array['manage_stock'] = $manage_stock;
                    if (in_array('quantity', $woocommerce_api_settings->product_fields_for_create)) {
                        $array['stock_quantity'] = $this->formatDecimalPoint($qty_available, 'quantity');
                    } else {
                        //set manage stock and in_stock if quantity disabled
                        if (isset($woocommerce_api_settings->manage_stock_for_create)) {
                            if ($woocommerce_api_settings->manage_stock_for_create == 'true') {
                                $array['manage_stock'] = true;
                            } else if ($woocommerce_api_settings->manage_stock_for_create == 'false') {
                                $array['manage_stock'] = false;
                            } else {
                                unset($array['manage_stock']);
                            }
                        }
                        if (isset($woocommerce_api_settings->in_stock_for_create)) {
                            if ($woocommerce_api_settings->in_stock_for_create == 'true') {
                                $array['in_stock'] = true;
                            } else if ($woocommerce_api_settings->in_stock_for_create == 'false') {
                                $array['in_stock'] = false;
                            }
                        }
                    }
                    
                    $array['regular_price'] = $this->formatDecimalPoint($price);
                }

                //assign name
                $array['name'] = $product->name;

                $product_data['create'][] = $array;
                $new_products[] = $product;

                $created_data[] = $product->sku;
            } else {
                $array['id'] = $product->woocommerce_product_id;
                //assign category
                if (in_array('category', $woocommerce_api_settings->product_fields_for_update)) {
                    if (!empty($product_cat)) {
                        $array['categories'] = $product_cat;
                    }
                }

                if (in_array('weight', $woocommerce_api_settings->product_fields_for_update)) {
                    $array['weight'] = $this->formatDecimalPoint($product->weight);
                }

                //sync product description
                if (in_array('description', $woocommerce_api_settings->product_fields_for_update)) {
                    if ($sync_description_as == 'long') {
                        $array['description'] = $product->product_description;
                    } elseif ($sync_description_as == 'short') {
                        $array['short_description'] = $product->product_description;
                    } else {
                        $array['description'] = $product->product_description;
                        $array['short_description'] = $product->product_description;
                    }
                }

                //If media id is set use media id else use image src
                if (!empty($product->image) && in_array('image', $woocommerce_api_settings->product_fields_for_update)) {
                    if ($this->isValidImage($product->image_path)) {
                        $array['images'] = !empty($product->woocommerce_media_id) ? [['id' => $product->woocommerce_media_id]] : [['src' => $product->image_url]];
                    }
                }

                if ($product->type == 'single') {
                    //assign quantity
                    $array['manage_stock'] = $manage_stock;
                    if (in_array('quantity', $woocommerce_api_settings->product_fields_for_update)) {
                        $array['stock_quantity'] = $this->formatDecimalPoint($qty_available, 'quantity');
                    } else {
                        //set manage stock and in_stock if quantity disabled
                        if (isset($woocommerce_api_settings->manage_stock_for_update)) {
                            if ($woocommerce_api_settings->manage_stock_for_update == 'true') {
                                $array['manage_stock'] = true;
                            } else if ($woocommerce_api_settings->manage_stock_for_update == 'false') {
                                $array['manage_stock'] = false;
                            } else {
                                unset($array['manage_stock']);
                            }
                        }
                        if (isset($woocommerce_api_settings->in_stock_for_update)) {
                            if ($woocommerce_api_settings->in_stock_for_update == 'true') {
                                $array['in_stock'] = true;
                            } else if ($woocommerce_api_settings->in_stock_for_update == 'false') {
                                $array['in_stock'] = false;
                            }
                        }
                    }
                    //assign price
                    if (in_array('price', $woocommerce_api_settings->product_fields_for_update)) {
                        $array['regular_price'] = $this->formatDecimalPoint($price);
                    }
                }

                //assign name
                if (in_array('name', $woocommerce_api_settings->product_fields_for_update)) {
                    $array['name'] = $product->name;
                }

                $product_data['update'][] = $array;
                $updated_data[] = $product->sku;
                $updated_products[] = $product;
            }
        }

        $create_response = [];
        $update_response = [];

        if (!empty($product_data['create'])) {
            $create_response = $this->syncProd($business_id, $product_data['create'], 'create', $new_products);
        }
        if (!empty($product_data['update'])) {
            $update_response = $this->syncProd($business_id, $product_data['update'], 'update', $updated_products);
        }
        $new_woocommerce_product_ids = array_merge($create_response, $update_response);

        //Create log
        if (!empty($created_data)) {
            if ($sync_type == 'new') {
                $this->createSyncLog($business_id, $user_id, 'new_products', 'created', $created_data);
            } else {
                $this->createSyncLog($business_id, $user_id, 'all_products', 'created', $created_data);
            }
        }
        if (!empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'all_products', 'updated', $updated_data);
        }

        //Sync variable product variations
        $this->syncProductVariations($business_id, $sync_type, $new_woocommerce_product_ids);

        if (empty($created_data) && empty($updated_data)) {
            if ($sync_type == 'new') {
                $this->createSyncLog($business_id, $user_id, 'new_products');
            } else {
                $this->createSyncLog($business_id, $user_id, 'all_products');
            }
        }

        return $all_products;
    }

    public function syncProd($business_id, $data, $type, $new_products)
    {
        //woocommerce api client object
        $woocommerce = $this->woo_client($business_id);

        $new_woocommerce_product_ids = [];
        $count = 0;
        foreach (array_chunk($data, 99) as $chunked_array) {
            $sync_data = [];
            $sync_data[$type] = $chunked_array;
            $response = $woocommerce->post('products/batch', $sync_data);
            if (!empty($response->create)) {
                foreach ($response->create as $key => $value) {
                    $new_product = $new_products[$count];
                    if ($value->id != 0) {
                        $new_product->woocommerce_product_id = $value->id;
                        //Sync woocommerce media id
                        $new_product->woocommerce_media_id = !empty($value->images[0]->id) ? $value->images[0]->id : null;
                    } else {
                        if (!empty($value->error->data->resource_id)) {
                            $new_product->woocommerce_product_id = $value->error->data->resource_id;
                        }
                    }
                    $new_product->save();

                    $new_woocommerce_product_ids[] = $new_product->woocommerce_product_id;
                    $count ++;
                }
            }

            if (!empty($response->update)) {
                foreach ($response->update as $key => $value) {
                    $updated_product = $new_products[$count];
                    if ($value->id != 0) {
                        //Sync woocommerce media id
                        $updated_product->woocommerce_media_id = !empty($value->images[0]->id) ? $value->images[0]->id : null;
                        $updated_product->save();
                    }
                    $new_woocommerce_product_ids[] = $updated_product->woocommerce_product_id;
                    $count ++;
                }
            }
        }

        return $new_woocommerce_product_ids;
    }

    /**
     * Synchronizes pos variation templates with Woocommerce product attributes
     * @param int $business_id
     * @return Void
     */
    public function syncVariationAttributes($business_id)
    {
        $woocommerce = $this->woo_client($business_id);
        $query = VariationTemplate::where('business_id', $business_id);

        $attributes = $query->get();
        $data = [];
        $new_attrs = [];
        foreach ($attributes as $attr) {
            if (empty($attr->woocommerce_attr_id)) {
                $data['create'][] = ['name' => $attr->name];
                $new_attrs[] = $attr;
            } else {
                $data['update'][] = [
                    'name' => $attr->name,
                    'id' => $attr->woocommerce_attr_id
                ];
            }
        }

        if (!empty($data)) {
            $response = $woocommerce->post('products/attributes/batch', $data);

            //update woocommerce_attr_id
            if (!empty($response->create)) {
                foreach ($response->create as $key => $value) {
                    $new_attr = $new_attrs[$key];
                    if ($value->id != 0) {
                        $new_attr->woocommerce_attr_id = $value->id;
                    } else {
                        $all_attrs = $woocommerce->get('products/attributes');
                        foreach ($all_attrs as $attr) {
                            if (strtolower($attr->name) == strtolower($new_attr->name)) {
                                $new_attr->woocommerce_attr_id = $attr->id;
                            }
                        }
                    }
                    $new_attr->save();
                }
            }
        }
    }

    /**
     * Synchronizes pos products variations with Woocommerce product variations
     * @param int $business_id
     * @param string $sync_type
     * @param array $new_woocommerce_product_ids (woocommerce product id of newly created products to sync)
     * @return Void
     */
    public function syncProductVariations($business_id, $sync_type = 'all', $new_woocommerce_product_ids = [])
    {
        //woocommerce api client object
        $woocommerce = $this->woo_client($business_id);
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        $query = Product::where('business_id', $business_id)
                        ->where('type', 'variable')
                        ->where('woocommerce_disable_sync', 0)
                        ->with(['variations',
                            'variations.variation_location_details',
                            'variations.product_variation',
                            'variations.product_variation.variation_template']);

        $query->whereIn('woocommerce_product_id', $new_woocommerce_product_ids);

        $variable_products = $query->get();
        $business_location_id = $woocommerce_api_settings->location_id;
        foreach ($variable_products as $product) {

            //Skip product if last updated is less than last sync
            $last_updated = $product->updated_at;

            $last_stock_updated = $this->getLastStockUpdated($business_location_id, $product->id);

            if (!empty($last_stock_updated)) {
                $last_updated = strtotime($last_stock_updated) > strtotime($last_updated) ?
                        $last_stock_updated : $last_updated;
            }
            if (!empty($last_synced) && strtotime($last_updated) < strtotime($last_synced)) {
                continue;
            }

            $variations = $product->variations;

            $variation_data = [];
            $new_variations = [];
            $updated_variations = [];
            foreach ($variations as $variation) {
                $variation_arr = [
                    'sku' => $variation->sub_sku
                ];

                $manage_stock = false;
                if ($product->enable_stock == 1) {
                    $manage_stock = true;
                }

                if (!empty($variation->product_variation->variation_template->woocommerce_attr_id)) {
                    $variation_arr['attributes'][] = [
                        'id' => $variation->product_variation->variation_template->woocommerce_attr_id,
                        'option' => $variation->name
                    ];
                }

                $price = $woocommerce_api_settings->product_tax_type == 'exc' ? $variation->default_sell_price : $variation->sell_price_inc_tax;

                if (!empty($woocommerce_api_settings->default_selling_price_group)) {
                    $group_prices = $this->productUtil->getVariationGroupPrice($variation->id, $woocommerce_api_settings->default_selling_price_group, $product->tax_id);

                    $price = $woocommerce_api_settings->product_tax_type == 'exc' ? $group_prices['price_exc_tax'] : $group_prices['price_inc_tax'];
                }

                //Set product stock
                $qty_available = 0;
                if ($product->enable_stock == 1) {
                    $variation_location_details = $variation->variation_location_details;
                    foreach ($variation_location_details as $vld) {
                        if ($vld->location_id == $business_location_id) {
                            $qty_available = $vld->qty_available;
                        }
                    }
                }

                if (empty($variation->woocommerce_variation_id)) {
                    $variation_arr['manage_stock'] = $manage_stock;
                    if (in_array('quantity', $woocommerce_api_settings->product_fields_for_create)) {
                        $variation_arr['stock_quantity'] = $this->formatDecimalPoint($qty_available, 'quantity');
                    } else {
                        //set manage stock and in_stock if quantity disabled
                        if (isset($woocommerce_api_settings->manage_stock_for_create)) {
                            if ($woocommerce_api_settings->manage_stock_for_create == 'true') {
                                $variation_arr['manage_stock'] = true;
                            } else if ($woocommerce_api_settings->manage_stock_for_create == 'false') {
                                $variation_arr['manage_stock'] = false;
                            } else {
                                unset($variation_arr['manage_stock']);
                            }
                        }
                        if (isset($woocommerce_api_settings->in_stock_for_create)) {
                            if ($woocommerce_api_settings->in_stock_for_create == 'true') {
                                $variation_arr['in_stock'] = true;
                            } else if ($woocommerce_api_settings->in_stock_for_create == 'false') {
                                $variation_arr['in_stock'] = false;
                            }
                        }
                    }

                    //Set variation images
                    //If media id is set use media id else use image src
                    if (!empty($variation->media) && count($variation->media) > 0 && in_array('image', $woocommerce_api_settings->product_fields_for_create)) {
                        $url = $variation->media->first()->display_url;
                        $path = $variation->media->first()->display_path;
                        $woocommerce_media_id = $variation->media->first()->woocommerce_media_id;
                        if ($this->isValidImage($path)) {
                            $variation_arr['image'] = !empty($woocommerce_media_id) ? ['id' => $woocommerce_media_id] : ['src' => $url];
                        }
                    }

                    $variation_arr['regular_price'] = $this->formatDecimalPoint($price);
                    $new_variations[] = $variation;

                    $variation_data['create'][] = $variation_arr;
                } else {
                    $variation_arr['id'] = $variation->woocommerce_variation_id;
                    $variation_arr['manage_stock'] = $manage_stock;
                    if (in_array('quantity', $woocommerce_api_settings->product_fields_for_update)) {
                        $variation_arr['stock_quantity'] = $this->formatDecimalPoint($qty_available, 'quantity');
                    } else {
                        //set manage stock and in_stock if quantity disabled
                        if (isset($woocommerce_api_settings->manage_stock_for_update)) {
                            if ($woocommerce_api_settings->manage_stock_for_update == 'true') {
                                $variation_arr['manage_stock'] = true;
                            } else if ($woocommerce_api_settings->manage_stock_for_update == 'false') {
                                $variation_arr['manage_stock'] = false;
                            } else {
                                unset($variation_arr['manage_stock']);
                            }
                        }
                        if (isset($woocommerce_api_settings->in_stock_for_update)) {
                            if ($woocommerce_api_settings->in_stock_for_update == 'true') {
                                $variation_arr['in_stock'] = true;
                            } else if ($woocommerce_api_settings->in_stock_for_update == 'false') {
                                $variation_arr['in_stock'] = false;
                            }
                        }
                    }

                    //Set variation images
                    //If media id is set use media id else use image src
                    if (!empty($variation->media) && count($variation->media) > 0 && in_array('image', $woocommerce_api_settings->product_fields_for_update)) {
                        $url = $variation->media->first()->display_url;
                        $path = $variation->media->first()->display_path;
                        $woocommerce_media_id = $variation->media->first()->woocommerce_media_id;
                        if ($this->isValidImage($path)) {
                            $variation_arr['image'] = !empty($woocommerce_media_id) ? ['id' => $woocommerce_media_id] : ['src' => $url];
                        }
                    }
                    
                    //assign price
                    if (in_array('price', $woocommerce_api_settings->product_fields_for_update)) {
                        $variation_arr['regular_price'] = $this->formatDecimalPoint($price);
                    }

                    $variation_data['update'][] = $variation_arr;
                    $updated_variations[] = $variation;
                }
            }
           
            if (!empty($variation_data)) {
                $response = $woocommerce->post('products/' . $product->woocommerce_product_id . '/variations/batch', $variation_data);

                //update woocommerce_variation_id
                if (!empty($response->create)) {
                    foreach ($response->create as $key => $value) {
                        $new_variation = $new_variations[$key];
                        if ($value->id != 0) {
                            $new_variation->woocommerce_variation_id = $value->id;
                            $media = $new_variation->media->first();
                            if (!empty($media)) {
                                $media->woocommerce_media_id = !empty($value->image->id) ? $value->image->id : null;
                                $media->save();
                            }
                        } else {
                            if (!empty($value->error->data->resource_id)) {
                                $new_variation->woocommerce_variation_id = $value->error->data->resource_id;
                            }
                        }
                        $new_variation->save();
                    }
                }

                //Update media id if changed from woocommerce site
                if (!empty($response->update)) {
                    foreach ($response->update as $key => $value) {
                        $updated_variation = $updated_variations[$key];
                        if ($value->id != 0) {
                            $media = $updated_variation->media->first();
                            if (!empty($media)) {
                                $media->woocommerce_media_id = !empty($value->image->id) ? $value->image->id : null;
                                $media->save();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Synchronizes Woocommers Orders with POS sales
     * @param int $business_id
     * @param int $user_id
     * @return void
     */
    public function syncOrders($business_id, $user_id)
    {
        $last_synced = $this->getLastSync($business_id, 'orders', false);
        $orders = $this->getAllResponse($business_id, 'orders');
        
        $woocommerce_sells = Transaction::where('business_id', $business_id)
                                ->whereNotNull('woocommerce_order_id')
                                ->with('sell_lines', 'sell_lines.product', 'payment_lines')
                                ->get();

        $new_orders = [];
        $updated_orders = [];

        $woocommerce_api_settings = $this->get_api_settings($business_id);
        $business = Business::find($business_id);

        $skipped_orders = !empty($business->woocommerce_skipped_orders) ? json_decode($business->woocommerce_skipped_orders, true) : [];
        
        $business_data = [
            'id' => $business_id,
            'accounting_method' => $business->accounting_method,
            'location_id' => $woocommerce_api_settings->location_id,
            'pos_settings' => json_decode($business->pos_settings, true),
            'business' => $business
        ];

        $created_data = [];
        $updated_data = [];
        $create_error_data = [];
        $update_error_data = [];

        foreach ($orders as $order) {
            //Only consider orders modified after last sync
            if ((!empty($last_synced) && strtotime($order->date_modified) <= strtotime($last_synced) && !in_array($order->id, $skipped_orders)) || in_array($order->status, ['auto-draft'])) {
                continue;
            }
            //Search if order already exists
            $sell = $woocommerce_sells->filter(function ($item) use ($order) {
                return $item->woocommerce_order_id == $order->id;
            })->first();

            $order_number = $order->number;
            $sell_status = $this->woocommerceOrderStatusToPosSellStatus($order->status, $business_id);

            if ($sell_status == 'draft') {
                $order_number .= " (" . __('sale.draft') . ")";
            }
            if (empty($sell)) {
                $created = $this->createNewSaleFromOrder($business_id, $user_id, $order, $business_data);
                $created_data[] = $order_number;

                if ($created !== true) {
                    $create_error_data[] = $created;
                }
            } else {
                $updated = $this->updateSaleFromOrder($business_id, $user_id, $order, $sell, $business_data);
                $updated_data[] = $order_number;

                if ($updated !== true) {
                    $update_error_data[] = $updated;
                }
            }
        }

        //Create log
        if (!empty($created_data)) {
            $this->createSyncLog($business_id, $user_id, 'orders', 'created', $created_data, $create_error_data);
        }
        if (!empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'orders', 'updated', $updated_data, $update_error_data);
        }

        if (empty($created_data) && empty($updated_data)) {
            $error_data = $create_error_data + $update_error_data;
            $this->createSyncLog($business_id, $user_id, 'orders', null, [], $error_data);
        }
    }

    /**
     * Creates new sales in POSfrom woocommerce order list
     * @param id $business_id
     * @param id $user_id
     * @param obj $order
     * @param array $business_data
     */
    public function createNewSaleFromOrder($business_id, $user_id, $order, $business_data)
    {
        $input = $this->formatOrderToSale($business_id, $user_id, $order);

        if (!empty($input['has_error'])) {
            return $input['has_error'];
        }

        $invoice_total = [
            'total_before_tax' => $order->total,
            'tax' => 0,
        ];

        DB::beginTransaction();

        $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id, false);
        $transaction->woocommerce_order_id = $order->id;
        $transaction->save();

        //Create sell lines
        $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id'], false, null, ['woocommerce_line_items_id' => 'line_item_id'], false);

        $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment'], $business_id, $user_id, false);

        if ($input['status'] == 'final') {
            //update product stock
            foreach ($input['products'] as $product) {
                if ($product['enable_stock']) {
                    $this->productUtil->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $input['location_id'],
                        $product['quantity']
                    );
                }
            }

            //Update payment status
            $transaction->payment_status = 'paid';
            $transaction->save();

            try {
                $this->transactionUtil->mapPurchaseSell($business_data, $transaction->sell_lines, 'purchase');
            } catch (PurchaseSellMismatch $e) {
                DB::rollBack();

                $this->add_to_skipped_orders($business_data['business'], $order->id);
                return [
                    'error_type' => 'order_insuficient_product_qty',
                    'order_number' => $order->number,
                    'msg' => $e->getMessage()
                ];
            }
        }

        $this->remove_from_skipped_orders($business_data['business'], $order->id);

        DB::commit();

        return true;
    }

    /**
     * Formats Woocommerce order response to pos sale request
     * @param id $business_id
     * @param id $user_id
     * @param obj $order
     * @param obj $sell = null
     */
    public function formatOrderToSale($business_id, $user_id, $order, $sell = null)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        //Create sell line data
        $product_lines = [];

        //For updating sell lines
        $sell_lines = [];
        if (!empty($sell)) {
            $sell_lines = $sell->sell_lines;
        }

        foreach ($order->line_items as $product_line) {
            $product = Product::where('business_id', $business_id)
                            ->where('woocommerce_product_id', $product_line->product_id)
                            ->with(['variations'])
                            ->first();

            $unit_price = $product_line->total / $product_line->quantity;
            $line_tax = !empty($product_line->total_tax) ? $product_line->total_tax : 0;
            $unit_line_tax = $line_tax / $product_line->quantity;
            $unit_price_inc_tax = $unit_price + $unit_line_tax;
            if (!empty($product)) {

                //Set sale line variation;If single product then first variation
                //else search for woocommerce_variation_id in all the variations
                if ($product->type == 'single') {
                    $variation = $product->variations->first();
                } else {
                    foreach ($product->variations as $v) {
                        if ($v->woocommerce_variation_id == $product_line->variation_id) {
                            $variation = $v;
                        }
                    }
                }

                if (empty($variation)) {
                    return ['has_error' =>
                            [
                                'error_type' => 'order_product_not_found',
                                'order_number' => $order->number,
                                'product' => $product_line->name . ' SKU:' . $product_line->sku
                            ]
                        ];
                    exit;
                }

                //Check if line tax exists append to sale line data
                $tax_id = null;
                if (!empty($product_line->taxes)) {
                    foreach ($product_line->taxes as $tax) {
                        $pos_tax = TaxRate::where('business_id', $business_id)
                        ->where('woocommerce_tax_rate_id', $tax->id)
                        ->first();

                        if (!empty($pos_tax)) {
                            $tax_id = $pos_tax->id;
                            break;
                        }
                    }
                }

                $product_data = [
                    'product_id' => $product->id,
                    'unit_price' => $unit_price,
                    'unit_price_inc_tax' => $unit_price_inc_tax,
                    'variation_id' => $variation->id,
                    'quantity' => $product_line->quantity,
                    'enable_stock' => $product->enable_stock,
                    'item_tax' => $line_tax,
                    'tax_id' => $tax_id,
                    'line_item_id' => $product_line->id
                ];
                
                //append transaction_sell_lines_id if update
                if (!empty($sell_lines)) {
                    foreach ($sell_lines as $sell_line) {
                        if ($sell_line->woocommerce_line_items_id ==
                            $product_line->id) {
                            $product_data['transaction_sell_lines_id'] = $sell_line->id;
                        }
                    }
                }

                $product_lines[] = $product_data;
            } else {
                return ['has_error' =>
                        [
                            'error_type' => 'order_product_not_found',
                            'order_number' => $order->number,
                            'product' => $product_line->name . ' SKU:' . $product_line->sku
                        ]
                    ];
                exit;
            }
        }

        //Get customer details
        $order_customer_id = $order->customer_id;

        $customer_details = [];

        //If Customer empty skip get guest customer details from billing address
        if (empty($order_customer_id)) {
            $f_name = !empty($order->billing->first_name) ? $order->billing->first_name : '';
            $l_name = !empty($order->billing->last_name) ? $order->billing->last_name : '';
            $customer_details = [
                    'first_name' => $f_name,
                    'last_name' => $l_name,
                    'email' => !empty($order->billing->email) ? $order->billing->email : null,
                    'name' => $f_name . ' ' . $l_name,
                    'mobile' => $order->billing->phone,
                    'address_line_1' => !empty($order->billing->address_1) ? $order->billing->address_1 : null,
                    'address_line_2' => !empty($order->billing->address_2) ? $order->billing->address_2 : null,
                    'city' => !empty($order->billing->city) ? $order->billing->city : null,
                    'state' => !empty($order->billing->state) ? $order->billing->state : null,
                    'country' => !empty($order->billing->country) ? $order->billing->country : null,
                    'zip_code' => !empty($order->billing->postcode) ? $order->billing->postcode : null
                ];
        } else {
            //woocommerce api client object
            $woocommerce = $this->woo_client($business_id);
            $order_customer = $woocommerce->get('customers/' . $order_customer_id);

            $customer_details = [
                    'first_name' => $order_customer->first_name,
                    'last_name' => $order_customer->last_name,
                    'email' => $order_customer->email,
                    'name' => $order_customer->first_name . ' ' . $order_customer->last_name,
                    'mobile' => $order_customer->billing->phone,
                    'city' => $order_customer->billing->city,
                    'state' => $order_customer->billing->state,
                    'country' => $order_customer->billing->country,
                    'address_line_1' => $order_customer->billing->address_1,
                    'address_line_2' => $order_customer->billing->address_2,
                    'zip_code' => $order_customer->billing->postcode
                ];
        }
        
        if (!empty($customer_details['email'])) {
            $customer = Contact::where('business_id', $business_id)
                            ->where('email', $customer_details['email'])
                            ->OnlyCustomers()
                            ->first();
        }
        
        if (empty($order_customer_id) && empty($customer_details['email'])) {
            $contactUtil = new ContactUtil;
            $customer = $contactUtil->getWalkInCustomer($business_id, false);
        }

        //If customer not found create new
        if (empty($customer)) {
            $ref_count = $this->transactionUtil->setAndGetReferenceCount('contacts', $business_id);
            $contact_id = $this->transactionUtil->generateReferenceNumber('contacts', $ref_count, $business_id);

            $customer_data = [
                'business_id' => $business_id,
                'type' => 'customer',
                'first_name' => $customer_details['first_name'],
                'last_name' => $customer_details['last_name'],
                'name' => $customer_details['name'],
                'email' => $customer_details['email'],
                'contact_id' => $contact_id,
                'mobile' => $customer_details['mobile'],
                'city' => $customer_details['city'],
                'state' => $customer_details['state'],
                'country' => $customer_details['country'],
                'created_by' => $user_id,
                'address_line_1' => $customer_details['address_line_1'],
                'address_line_2' => $customer_details['address_line_2'],
                'zip_code' => $customer_details['zip_code']
            ];

            //if name is blank make email address as name
            if (empty(trim($customer_data['name']))) {
                $customer_data['first_name'] = $customer_details['email'];
                $customer_data['name'] = $customer_details['email'];
            }
            $customer = Contact::create($customer_data);
        }

        $sell_status = $this->woocommerceOrderStatusToPosSellStatus($order->status, $business_id);
        $shipping_status = $this->woocommerceOrderStatusToPosShippingStatus($order->status, $business_id);
        $shipping_address = [];
        if (!empty($order->shipping->first_name)) {
            $shipping_address[] = $order->shipping->first_name . ' ' . $order->shipping->last_name;
        }
        if (!empty($order->shipping->company)) {
            $shipping_address[] = $order->shipping->company;
        }
        if (!empty($order->shipping->address_1)) {
            $shipping_address[] = $order->shipping->address_1;
        }
        if (!empty($order->shipping->address_2)) {
            $shipping_address[] = $order->shipping->address_2;
        }
        if (!empty($order->shipping->city)) {
            $shipping_address[] = $order->shipping->city;
        }
        if (!empty($order->shipping->state)) {
            $shipping_address[] = $order->shipping->state;
        }
        if (!empty($order->shipping->country)) {
            $shipping_address[] = $order->shipping->country;
        }
        if (!empty($order->shipping->postcode)) {
            $shipping_address[] = $order->shipping->postcode;
        }
        $addresses['shipping_address'] = [
            'shipping_name' => $order->shipping->first_name . ' ' . $order->shipping->last_name,
            'company' => $order->shipping->company,
            'shipping_address_line_1' => $order->shipping->address_1,
            'shipping_address_line_2' => $order->shipping->address_2,
            'shipping_city' => $order->shipping->city,
            'shipping_state' => $order->shipping->state,
            'shipping_country' => $order->shipping->country,
            'shipping_zip_code' => $order->shipping->postcode
        ];
        $addresses['billing_address'] = [
            'billing_name' => $order->billing->first_name . ' ' . $order->billing->last_name,
            'company' => $order->billing->company,
            'billing_address_line_1' => $order->billing->address_1,
            'billing_address_line_2' => $order->billing->address_2,
            'billing_city' => $order->billing->city,
            'billing_state' => $order->billing->state,
            'billing_country' => $order->billing->country,
            'billing_zip_code' => $order->billing->postcode
        ];

        $shipping_lines_array = [];
        if (!empty($order->shipping_lines)) {
            foreach ($order->shipping_lines as $shipping_lines) {
                $shipping_lines_array[] = $shipping_lines->method_title;
            }
        }

        $new_sell_data = [
            'business_id' => $business_id,
            'location_id' => $woocommerce_api_settings->location_id,
            'contact_id' => $customer->id,
            'discount_type' => 'fixed',
            'discount_amount' => $order->discount_total,
            'shipping_charges' => $order->shipping_total,
            'final_total' => $order->total,
            'created_by' => $user_id,
            'status' => $sell_status == 'quotation' ? 'draft' : $sell_status,
            'is_quotation' => $sell_status == 'quotation' ? 1 : 0,
            'sub_status' => $sell_status == 'quotation' ? 'quotation' : null,
            'payment_status' => 'paid',
            'additional_notes' => '',
            'transaction_date' => $order->date_created,
            'customer_group_id' => $customer->customer_group_id,
            'tax_rate_id' => null,
            'sale_note' => null,
            'commission_agent' => null,
            'invoice_no' => $order->number,
            'order_addresses' => json_encode($addresses),
            'shipping_charges' => !empty($order->shipping_total) ? $order->shipping_total : 0,
            'shipping_details' => !empty($shipping_lines_array) ? implode(', ', $shipping_lines_array) : '',
            'shipping_status' => $shipping_status,
            'shipping_address' => implode(', ', $shipping_address)
        ];

        $payment = [
            'amount' => $order->total,
            'method' => 'cash',
            'card_transaction_number' => '',
            'card_number' => '',
            'card_type' => '',
            'card_holder_name' => '',
            'card_month' => '',
            'card_security' => '',
            'cheque_number' =>'',
            'bank_account_number' => '',
            'note' => $order->payment_method_title,
            'paid_on' => $order->date_paid
        ];

        if (!empty($sell) && count($sell->payment_lines) > 0) {
            $payment['payment_id'] = $sell->payment_lines->first()->id;
        }

        $new_sell_data['products'] = $product_lines;
        $new_sell_data['payment'] = [$payment];

        return $new_sell_data;
    }

    /**
     * Updates existing sale
     * @param id $business_id
     * @param id $user_id
     * @param obj $order
     * @param obj $sell
     * @param array $business_data
     */
    public function updateSaleFromOrder($business_id, $user_id, $order, $sell, $business_data)
    {
        $input = $this->formatOrderToSale($business_id, $user_id, $order, $sell);

        if (!empty($input['has_error'])) {
            return $input['has_error'];
        }

        $invoice_total = [
            'total_before_tax' => $order->total,
            'tax' => 0,
        ];

        $status_before = $sell->status;

        DB::beginTransaction();
        $transaction = $this->transactionUtil->updateSellTransaction($sell, $business_id, $input, $invoice_total, $user_id, false, false);

        //Update Sell lines
        $deleted_lines = $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id'], true, $status_before, [], false);

        $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment'], null, null, false);

        //Update payment status
        $transaction->payment_status = 'paid';
        $transaction->save();

        //Update product stock
        $this->productUtil->adjustProductStockForInvoice($status_before, $transaction, $input, false);

        try {
            $this->transactionUtil->adjustMappingPurchaseSell($status_before, $transaction, $business_data, $deleted_lines);
        } catch (PurchaseSellMismatch $e) {
            DB::rollBack();
            return [
                'error_type' => 'order_insuficient_product_qty',
                'order_number' => $order->number,
                'msg' => $e->getMessage()
            ];
        }

        DB::commit();

        return true;
    }

    /**
     * Creates sync log in the database
     * @param id $business_id
     * @param id $user_id
     * @param string $type
     * @param array $errors = null
     */
    public function createSyncLog($business_id, $user_id, $type, $operation = null, $data = [], $errors = null)
    {
        WoocommerceSyncLog::create([
            'business_id' => $business_id,
            'sync_type' => $type,
            'created_by' => $user_id,
            'operation_type' => $operation,
            'data' => !empty($data) ? json_encode($data) : null,
            'details' => !empty($errors) ? json_encode($errors) : null
        ]);
    }

    /**
     * Retrives last synced date from the database
     * @param id $business_id
     * @param string $type
     * @param bool $for_humans = true
     */
    public function getLastSync($business_id, $type, $for_humans = true)
    {
        $last_sync = WoocommerceSyncLog::where('business_id', $business_id)
                            ->where('sync_type', $type)
                            ->max('created_at');

        //If last reset present make last sync to null
        $last_reset = WoocommerceSyncLog::where('business_id', $business_id)
                            ->where('sync_type', $type)
                            ->where('operation_type', 'reset')
                            ->max('created_at');
        if (!empty($last_reset) && !empty($last_sync) && $last_reset >= $last_sync) {
            $last_sync = null;
        }

        if (!empty($last_sync) && $for_humans) {
            $last_sync = \Carbon::createFromFormat('Y-m-d H:i:s', $last_sync)->diffForHumans();
        }
        return $last_sync;
    }

    public function woocommerceOrderStatusToPosSellStatus($status, $business_id)
    {
        $default_status_array = [
            'pending' => 'draft',
            'processing' => 'final',
            'on-hold' => 'draft',
            'completed' => 'final',
            'cancelled' => 'draft',
            'refunded' => 'draft',
            'failed' => 'draft',
            'shipped' => 'final'
        ];

        $api_settings = $this->get_api_settings($business_id);

        $status_settings = $api_settings->order_statuses ?? null;

        $sale_status = !empty($status_settings) ? $status_settings->$status : null;
        $sale_status = empty($sale_status) && array_key_exists($status, $default_status_array) ? $default_status_array[$status] : $sale_status;
        $sale_status = empty($sale_status) ? 'final' : $sale_status;


        return $sale_status;
    }

    public function woocommerceOrderStatusToPosShippingStatus($status, $business_id)
    {
        $api_settings = $this->get_api_settings($business_id);

        $status_settings = $api_settings->shipping_statuses ?? null;

        $shipping_status = !empty($status_settings) ? $status_settings->$status : null;

        return $shipping_status;
    }

    /**
     * Splits response to list of 100 and merges all
     * @param int $business_id
     * @param string $endpoint
     * @param array $params = []
     *
     * @return array
     */
    public function getAllResponse($business_id, $endpoint, $params = [])
    {

        //woocommerce api client object
        $woocommerce = $this->woo_client($business_id);

        $page = 1;
        $list = [];
        $all_list = [];
        $params['per_page'] = 100;

        do {
            $params['page'] = $page;
            try {
                $list = $woocommerce->get($endpoint, $params);
            } catch (\Exception $e) {
                return [];
            }
            $all_list = array_merge($all_list, $list);
            $page++;
        } while (count($list) > 0);

        return $all_list;
    }

    /**
     * Retrives all tax rates from woocommerce api
     * @param id $business_id
     *
     * @param obj $tax_rates
     */
    public function getTaxRates($business_id)
    {
        $tax_rates = $this->getAllResponse($business_id, 'taxes');
        return $tax_rates;
    }

    public function getLastStockUpdated($location_id, $product_id)
    {
        $last_updated = VariationLocationDetails::where('location_id', $location_id)
                                    ->where('product_id', $product_id)
                                    ->max('updated_at');

        return $last_updated;
    }

    private function formatDecimalPoint($number, $type = 'currency') {

        $precision = 4;
        $currency_precision = config('constants.currency_precision');
        $quantity_precision = config('constants.quantity_precision');

        if ($type == 'currency' && !empty($currency_precision)) {
            $precision = $currency_precision;
        }
        if ($type == 'quantity' && !empty($quantity_precision)) {
            $precision = $quantity_precision;
        }

        return number_format((float) $number, $precision, ".", "");
    }

    public function isValidImage($path)
    {
        $valid_extenstions = ['jpg', 'jpeg', 'png', 'gif'];

        return !empty($path) && file_exists($path) && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $valid_extenstions);
    }
}
