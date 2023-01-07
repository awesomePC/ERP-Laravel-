<?php

namespace Modules\Connector\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Utils\ProductUtil;
use App\PurchaseLine;

class ProductResource extends Resource
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

        $array['brand'] = $this->brand;
        $array['unit'] = $this->unit;
        $array['category'] = $this->category;
        $array['sub_category'] = $this->sub_category;
        $array['product_tax'] = $this->product_tax;

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
                    $selling_price_group = $pl['selling_price_group_id'];
                    $location_discount = $productUtil->getProductDiscount($this, $array['business_id'], $pl['id'], false, $selling_price_group, $v['id']);
                    if (!empty($location_discount)) {
                        $discounts[] = $location_discount;
                    }
                }

                $array['product_variations'][$key]['variations'][$k]['discounts'] = $discounts;
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
        ];
    }
}
