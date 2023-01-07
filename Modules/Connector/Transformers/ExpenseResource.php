<?php

namespace Modules\Connector\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ExpenseResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $all_data = parent::toArray($request);

        $array = [
            'id' => $all_data['id'],
            'business_id' => $all_data['business_id'],
            'location_id' => $all_data['location_id'],
            'payment_status' => $all_data['payment_status'],
            'ref_no' => $all_data['ref_no'],
            'transaction_date' => $all_data['transaction_date'],
            'total_before_tax' => $all_data['total_before_tax'],
            'tax_id' => !empty($all_data['tax_id']) ? $all_data['tax_id'] : null,
            'tax_amount' => !empty($all_data['tax_amount']) ? $all_data['tax_amount'] : 0,
            'final_total' => $all_data['final_total'],
            'expense_category_id' => !empty($all_data['expense_category_id']) ? $all_data['expense_category_id'] : null,
            'document' => !empty($all_data['document']) ? $all_data['document'] : null ,
            'created_by' => $all_data['created_by'],
            'created_at' => $all_data['created_at'],
            'updated_at' => $all_data['updated_at'],
            'expense_for' => !empty($all_data['transaction_for']) ? $all_data['transaction_for'] : [],
        ];

        if ($all_data['type'] == 'expense') {
            $recur_data = [
                'is_recurring' => !empty($all_data['is_recurring']) ? $all_data['is_recurring'] : 0,
                'recur_interval' => !empty($all_data['recur_interval']) ? $all_data['recur_interval'] : null ,
                'recur_interval_type' => !empty($all_data['recur_interval_type']) ? $all_data['recur_interval_type'] : null ,
                'recur_repetitions' => !empty($all_data['recur_repetitions']) ? $all_data['recur_repetitions'] : null,
                'recur_stopped_on' => !empty($all_data['recur_stopped_on']) ? $all_data['recur_stopped_on'] : null,
                'recur_parent_id' => !empty($all_data['recur_parent_id']) ? $all_data['recur_parent_id'] : null
            ];

            $array = array_merge($array, $recur_data);
        }
        return $array;
    }
}
