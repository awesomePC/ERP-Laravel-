<?php

namespace Modules\Connector\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class TypesOfServiceResource extends Resource
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

        $array['location_price_group'] = (object) $this->location_price_group;
        return $array;
    }
}
