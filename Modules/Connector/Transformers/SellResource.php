<?php

namespace Modules\Connector\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Utils\Util;

class SellResource extends Resource
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
        }

        $commonUtil = new Util;
        $array['invoice_url'] = $commonUtil->getInvoiceUrl($array['id'], $array['business_id']);
        $array['payment_link'] = $commonUtil->getInvoicePaymentLink($array['id'], $array['business_id']);

        return $array;
    }
}
