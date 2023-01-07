<?php

namespace Modules\Connector\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Utils\Util;

class NewContactResource extends Resource
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

        return array_diff_key($array, array_flip($this->__excludeFields()));

        return $array;
    }


            
    private function __excludeFields(){
        return [
            'supplier_business_name',
            'contact_id',
            "tax_number",
            "city",
            "state",
            "country",
            "address_line_1",
            "address_line_2",
            "zip_code",
            "dob",
            "landline",
            "alternate_number",
            "pay_term_number",
            "pay_term_type",
            "created_by",
            "is_default",
            "shipping_address",
            "shipping_custom_field_details",
            "is_export",
            "export_custom_field_1",
            "export_custom_field_2",
            "export_custom_field_3",
            "export_custom_field_4",
            "export_custom_field_5",
            "export_custom_field_6",
            "position",
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

}
