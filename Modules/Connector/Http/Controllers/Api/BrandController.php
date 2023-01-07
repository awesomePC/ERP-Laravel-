<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;

use App\Brands;

/**
 * @group Brand management
 * @authenticated
 *
 * APIs for managing brands
 */
class BrandController extends ApiController
{
    /**
     * List brands
     * @response {
            "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "name": "Levis",
                    "description": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:19:47",
                    "updated_at": "2018-01-03 21:19:47"
                },
                {
                    "id": 2,
                    "business_id": 1,
                    "name": "Espirit",
                    "description": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:19:58",
                    "updated_at": "2018-01-03 21:19:58"
                }
            ]
        }
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        
        $brands = Brands::where('business_id', $business_id)
                        ->get();

        return CommonResource::collection($brands);
    }

    /**
     * Get the specified brand
     *
     * @urlParam brand required comma separated ids of the brands Example: 1
     * @response {
            "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "name": "Levis",
                    "description": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:19:47",
                    "updated_at": "2018-01-03 21:19:47"
                }
            ]
        }
     */
    public function show($brand_ids)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $brand_ids = explode(',', $brand_ids);

        $brands = Brands::where('business_id', $business_id)
                        ->whereIn('id', $brand_ids)
                        ->get();

        return CommonResource::collection($brands);
    }
}
