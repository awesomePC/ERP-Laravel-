<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\TypesOfServiceResource;
use App\TypesOfService;

/**
 * @group Types of service management
 * @authenticated
 *
 * APIs for managing Types of services
 */
class TypesOfServiceController extends ApiController
{
    /**
     * List types of service
     *
     * @response {
        "data": [
            {
                "id": 1,
                "name": "Home Delivery",
                "description": null,
                "business_id": 1,
                "location_price_group": {
                    "1": "0"
                },
                "packing_charge": "10.0000",
                "packing_charge_type": "fixed",
                "enable_custom_fields": 0,
                "created_at": "2020-06-04 22:41:13",
                "updated_at": "2020-06-04 22:41:13"
            }
        ]
    }
     * 
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        
        $types_of_service = TypesOfService::where('business_id', $business_id)
                                        ->get();

        return TypesOfServiceResource::collection($types_of_service);
    }

    /**
     * Get the specified types of service
     * @urlParam types_of_service required comma separated ids of required types of services Example: 1
     *
     * @response {
        "data": [
            {
                "id": 1,
                "name": "Home Delivery",
                "description": null,
                "business_id": 1,
                "location_price_group": {
                    "1": "0"
                },
                "packing_charge": "10.0000",
                "packing_charge_type": "fixed",
                "enable_custom_fields": 0,
                "created_at": "2020-06-04 22:41:13",
                "updated_at": "2020-06-04 22:41:13"
            }
        ]
    }
     */
    public function show($types_of_service_ids)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $types_of_service_ids = explode(',', $types_of_service_ids);

        $types_of_service = TypesOfService::where('business_id', $business_id)
                        ->whereIn('id', $types_of_service_ids)
                        ->get();

        return TypesOfServiceResource::collection($types_of_service);
    }
}
