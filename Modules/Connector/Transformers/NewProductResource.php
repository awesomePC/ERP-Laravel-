<?php

namespace Modules\Connector\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Utils\ProductUtil;
use App\PurchaseLine;

class NewProductResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);
        $array['brand'] = $array['brand'];
        $array['unit'] = $array['unit'];
        $array['category'] = $array['category'];
        $array['sub_category'] = $array['sub_category'];
        $array['product_tax'] = $array['product_tax'];

        $send_lot_detail = !empty(request()->input('send_lot_detail')) && request()->input('send_lot_detail') == 1 ? true : false;

        $productUtil = new ProductUtil;
        foreach ($array['product_variations'] as $key => $value) {
            foreach ($value['variations'] as $k => $v) {

                //set lot details in each variation_location_details
                if ($send_lot_detail && !empty($v['variation_location_details'])) {
                    foreach ($v['variation_location_details'] as $u => $w) {
                        $lot_details = [];
                        $purchase_lines = PurchaseLine::where('variation_id', $w['variation_id'])
                                                    ->leftJoin('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
                                                    ->where('t.location_id', $w['location_id'])
                                                    ->where('t.status', 'received')
                                                    ->get();

                        foreach ($purchase_lines as $pl) {
                            if ($pl->quantity_remaining > 0) {
                                $lot_details[] = [
                                    'lot_number' => $pl->lot_number,
                                    'qty_available' => $pl->quantity_remaining,
                                    'default_purchase_price' => $pl->purchase_price,
                                    'dpp_inc_tax' => $pl->purchase_price_inc_tax
                                ];
                            }
                        }

                        $array['product_variations'][$key]['variations'][$k]['variation_location_details'][$u]['lot_details'] = $lot_details;
                    }
                }

               if (isset($v['group_prices'])) {
                    $array['product_variations'][$key]['variations'][$k]['selling_price_group'] = $v['group_prices'];
                    unset($array['product_variations'][$key]['variations'][$k]['group_prices']);
                }
                //get discounts for each location
                $discounts = [];
                foreach($array['product_locations'] as $pl){
                    $selling_price_group = $pl['selling_price_group_id'] ?? null;
                    $location_discount = $productUtil->getProductDiscount($this, $array['business_id'], $pl['id'], false, $selling_price_group, $v['id']);
                    if (!empty($location_discount)) {
                        $discounts[] = $location_discount;
                    }
                }

                $array['product_variations'][$key]['variations'][$k]['discounts'] = $discounts;
            }

            //unset not required fields
            foreach ($array['product_variations'] as $key => $value) {
                foreach ($value['variations'] as $k => $v) {
                    foreach ($v['variation_location_details'] as $u => $w) {
                        $v['variation_location_details'][$u] = array_diff_key($w, array_flip($this->__excludeLocationDetailsFields()));
                    }

                    $value['variations'][$k] = array_diff_key($v, array_flip($this->__excludeVariationFields()));
                }

                $array['product_variations'][$key] = array_diff_key($value, array_flip($this->__excludeProductVariationFields()));
            }

            if (!empty($array['unit'])) {
               $array['unit'] = array_diff_key($array['unit'], array_flip($this->__excludeUnitFields()));
            }
            
            if (!empty($array['category'])) {
                $array['category'] = array_diff_key($array['category'], array_flip($this->__excludeUnitFields()));
            }

            if (!empty($array['sub_category'])) {
                $array['sub_category'] = array_diff_key($array['sub_category'], array_flip($this->__excludeUnitFields()));
            }

            foreach ($array['product_locations'] as $key => $value) {
                $product_location = [
                    'id' => $value['id'],
                    'business_id' => $value['business_id'],
                    'name' => $value['name'],
                    'custom_field1' => $value['custom_field1'],
                    'custom_field2' => $value['custom_field2'],
                    'custom_field3' => $value['custom_field3'],
                    'custom_field4' => $value['custom_field4'],
                ];
                $array['product_locations'][$key] = $product_location;
            }


        }
        
        return array_diff_key($array, array_flip($this->__excludeFields()));
    }

    private function __excludeFields(){
        return [
            'created_at',
            'updated_at',
            'brand_id',
            'unit_id',
            'category_id',
            'sub_category_id',
            'tax',
            'tax_type',
            'sub_unit_ids',
            'alert_quantity',
            'barcode_type',
            'expiry_period',
            'expiry_period_type',
            'enable_sr_no',
            'weight',
            'image',
            'created_by',
            'warranty_id',
        ];
    }

    private function __excludeProductVariationFields(){
        return [
            'created_at',
            'updated_at',
        ];
    }

    private function __excludeVariationFields(){
        return [
            'variation_value_id',
            'profit_percent',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    private function __excludeLocationDetailsFields(){
        return [
            'created_at',
            'updated_at',
        ];
    }

    private function __excludeUnitFields(){
        return [
            'created_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    private function __excludeCategoryFields(){
        return [
            'category_type',
            'created_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }
}