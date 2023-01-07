<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromArray;

class ProductsExport implements FromArray
{
    public function array(): array
    {
        $business_id = request()->session()->get('user.business_id');

        $products = Product::where('business_id', $business_id)
                    ->with(['brand', 'unit', 'category', 'sub_category', 'product_variations', 'product_variations.variations', 'product_tax', 'rack_details', 'product_locations'])
                    ->select('products.*')
                    ->get();

        //set headers
        $products_array = [['NAME', 'BRAND', 'UNIT', 'CATEGORY', 'SUB-CATEGORY', 'SKU (Leave blank to auto generate sku)', 'BARCODE TYPE', 'MANAGE STOCK (1=yes 0=No)', 'ALERT QUANTITY', 'EXPIRES IN', 'EXPIRY PERIOD UNIT (months/days)', 'APPLICABLE TAX', 'Selling Price Tax Type (inclusive or exclusive)', 'PRODUCT TYPE (single or variable)', 'VARIATION NAME (Keep blank if product type is single)', 'VARIATION VALUES (| seperated values & blank if product type if single)', 'VARIATION SKUs (| seperated values & blank if product type if single)', 'PURCHASE PRICE (Including tax)', 'PURCHASE PRICE (Excluding tax)', 'PROFIT MARGIN', 'SELLING PRICE', 'OPENING STOCK', 'OPENING STOCK LOCATION', 'EXPIRY DATE', 'ENABLE IMEI OR SERIAL NUMBER(1=yes 0=No)', 'WEIGHT', 'RACK', 'ROW', 'POSITION', 'IMAGE', 'PRODUCT DESCRIPTION', 'CUSTOM FIELD 1', 'CUSTOM FIELD 2', 'CUSTOM FIELD 3', 'CUSTOM FIELD 4', 'NOT FOR SELLING(1=yes 0=No)', 'PRODUCT LOCATIONS']];
        foreach ($products as $product) {
            $product_variation = $product->product_variations->first();

            $product_variation_name = $product->type == 'variable' ? $product_variation->name : '';
            $variation_values = $product->type == 'variable' ? implode('|', $product_variation->variations->pluck('name')->toArray()) : '';
            $variation_skus = implode('|', $product_variation->variations->pluck('sub_sku')->toArray());
            $purchase_prices = implode('|', $product_variation->variations->pluck('dpp_inc_tax')->toArray());
            $purchase_prices_ex_tax = implode('|', $product_variation->variations->pluck('default_purchase_price')->toArray());
            $profit_percents = implode('|', $product_variation->variations->pluck('profit_percent')->toArray());
            $selling_prices = $product->tax_type == 'inclusive' ? implode('|', $product_variation->variations->pluck('sell_price_inc_tax')->toArray()) : implode('|', $product_variation->variations->pluck('default_sell_price')->toArray());
            $locations = implode(',', $product->product_locations->pluck('name')->toArray());

            $rack_details = [];
            $row_details = [];
            $position_details = [];
            foreach ($product->product_locations as $l) {
                foreach ($product->rack_details as $rd) {
                    if ($rd->location_id == $l->id) {
                        $rack_details[] = $rd->rack;
                        $row_details[] = $rd->row;
                        $position_details[] = $rd->position;
                    }
                }
            }

            $product_arr = [
                $product->name,
                $product->brand->name ?? '',
                $product->unit->short_name ?? '',
                $product->category->name ?? '',
                $product->sub_category->name ?? '',
                $product->sku,
                $product->barcode_type,
                $product->enable_stock,
                $product->alert_quantity,
                $product->expiry_period,
                $product->expiry_period_type,
                $product->product_tax->name ?? '',
                $product->tax_type,
                $product->type,
                $product_variation_name,
                $variation_values,
                $variation_skus,
                $purchase_prices,
                $purchase_prices_ex_tax,
                $profit_percents,
                $selling_prices,
                '',
                '',
                '',
                $product->enable_sr_no,
                $product->weight,
                implode('|', $rack_details),
                implode('|', $row_details),
                implode('|', $position_details),
                $product->image_url,
                $product->product_description,
                $product->product_custom_field1,
                $product->product_custom_field2,
                $product->product_custom_field3,
                $product->product_custom_field4,
                $product->not_for_selling,
                $locations
            ];

            $products_array[] = $product_arr;
        }

        return $products_array;
    }
}
