<?php

namespace Modules\Connector\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Utils\Util;

class NewSellResource extends Resource
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

        foreach ($array['sell_lines'] as $key => $value) {
            //check if mapping exists
            if (isset($value['sell_line_purchase_lines'])) {
                $purchase_lines = [];
                foreach ($value['sell_line_purchase_lines'] as $sell_line_purchase_line) {
                    //check mapped purchase line
                    if (isset($sell_line_purchase_line['purchase_line'])) {

                        //get purchase details of the sell line
                        $purchase_lines[] = [
                            'purchase_price' => $sell_line_purchase_line['purchase_line']['purchase_price'],
                            'pp_inc_tax' => $sell_line_purchase_line['purchase_line']['purchase_price_inc_tax'],
                            'lot_number' => $sell_line_purchase_line['purchase_line']['lot_number']
                        ];
                    }
                }
                //unset mapping and set purchase details
                unset($array['sell_lines'][$key]['sell_line_purchase_lines']);
                $array['sell_lines'][$key]['purchase_price'] = $purchase_lines;
            }

            $product = $value['product'];
            $value['product_name'] = $product['name'];
            $value['product_custom_field_1'] = $product['product_custom_field1'];
            $value['product_type'] = $product['type'];
            $value['product_sku'] = $product['sku'];
            $value['category'] = !empty($product['category']) ? array_diff_key($product['category'], array_flip($this->__excludeCommonFields())) : null;
            $value['sub_category'] = !empty($product['sub_category']) ? array_diff_key($product['sub_category'], array_flip($this->__excludeCommonFields())) : null;

            $variation = $value['variations'];
            $value['product_variations'] = [
                'id' => $variation['id'],
                'name' => $variation['name'],
                "product_id" => $variation['product_id'],
                "sub_sku" => $variation['sub_sku'],
                "product_variation_id" => $variation['product_variation_id'],
                "default_purchase_price" => $variation['default_purchase_price'],
                "dpp_inc_tax" => $variation['dpp_inc_tax'],
                "default_sell_price" => $variation['default_sell_price'],
                "sell_price_inc_tax" => $variation['sell_price_inc_tax']
            ];

            $array['sell_lines'][$key] = array_diff_key($value, array_flip($this->__excludeSellLineFields()));
        }

        if (!empty($array['payment_lines'])) {
            foreach ($array['payment_lines'] as $key => $value) {
                $array['payment_lines'][$key] = array_diff_key($value, array_flip($this->__excludePaymentLineFields()));
            }
        }
        

        $commonUtil = new Util;
        $array['invoice_url'] = $commonUtil->getInvoiceUrl($array['id'], $array['business_id']);
        $array['payment_link'] = $commonUtil->getInvoicePaymentLink($array['id'], $array['business_id']);

        return array_diff_key($array, array_flip($this->__excludeSellFields()));

        return $array;
    }


            
    private function __excludeSellFields(){
        return [
            'res_waiter_id',
            'res_order_status',
            'sub_type',
            'sub_status',
            'adjustment_type',
            'shipping_charges',
            'shipping_custom_field_1',
            'shipping_custom_field_2',
            'shipping_custom_field_3',
            'shipping_custom_field_4',
            'shipping_custom_field_5',
            'is_export',
            'export_custom_fields_info',
            'additional_expense_key_1',
            'additional_expense_value_1',
            "additional_expense_key_2",
            "additional_expense_value_2",
            "additional_expense_key_3",
            "additional_expense_value_3",
            "additional_expense_key_4",
            "additional_expense_value_4",
            "expense_category_id",
            "expense_for",
            "commission_agent",
            "document",
            'exchange_rate',
            "transfer_parent_id",
            "return_parent_id",
            "opening_stock_product_id",
            "created_by",
            "mfg_parent_production_purchase_id",
            "mfg_wasted_units",
            "prefer_payment_method",
            "prefer_payment_account",
            "sales_order_ids",
            "purchase_order_ids",
            "custom_field_1",
            "custom_field_2",
            "custom_field_3",
            "custom_field_4",
            "import_batch",
            "import_time",
            "types_of_service_id",
            "packing_charge",
            "packing_charge_type",
            "service_custom_field_1",
            "service_custom_field_2",
            "service_custom_field_3",
            "service_custom_field_4",
            "service_custom_field_5",
            "service_custom_field_6",
            "rp_earned",
            "order_addresses",
            "is_recurring",
            "recur_interval",
            "recur_interval_type",
            "recur_repetitions",
            "recur_stopped_on",
            "recur_parent_id",
            'pay_term_number',
            'pay_term_type',
        ];
    }

    private function __excludeSellLineFields(){
        return [
            'variation_id',
            'mfg_waste_percent',
            'mfg_ingredient_group_id',
            "so_line_id",
            "so_quantity_invoiced",
            "res_service_staff_id",
            "res_line_order_status",
            "parent_sell_line_id",
            "sub_unit_id",
            'product',
            'variations'
        ];
    }

    private function __excludeCommonFields(){
        return [
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    private function __excludePaymentLineFields(){
        return [
            "transaction_no",
            "card_transaction_number",
            "card_number",
            "card_holder_name",
            "card_month",
            "card_year",
            "card_security",
            "cheque_number",
            "bank_account_number",
            "created_by",
            "paid_through_link",
            "gateway",
            "is_advance",
            "payment_for",
            "parent_id",
            "note",
            "document",
            "account_id",
            "created_at",
            "updated_at"
        ];
    }
}
