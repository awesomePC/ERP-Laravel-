<?php

namespace Modules\Connector\Transformers;

use Illuminate\Http\Resources\Json\Resource;

use App\Utils\Util;

class BusinessLocationResource extends Resource
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
        $default_payment_accounts = !empty($array['default_payment_accounts']) ? json_decode($array['default_payment_accounts'], true) : [];
        //Add label of each payment methods from business custom labels.
        $util = new Util();
        $all_payment_methods = $util->payment_types($array['id']);
        $payment_methods = [];
        foreach ($default_payment_accounts as $key => $value) {
            if(isset($all_payment_methods[$key]) && $value['is_enabled'] == 1){
                $payment_methods[] = [
                    'name' => $key,
                    'label' => $all_payment_methods[$key],
                    'account_id' => isset($value['account']) ? $value['account'] : null
                ];
            }
        }

        unset($array['default_payment_accounts']);
        $array['payment_methods'] = $payment_methods;

        return $array;
    }
}
