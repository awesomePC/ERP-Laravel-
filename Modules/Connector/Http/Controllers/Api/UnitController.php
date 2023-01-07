<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;

use App\Unit;

/**
 * @group Unit management
 * @authenticated
 *
 * APIs for managing units
 */
class UnitController extends ApiController
{
    /**
     * List units
     * @response {
        "data": [
            {
                "id": 1,
                "business_id": 1,
                "actual_name": "Pieces",
                "short_name": "Pc(s)",
                "allow_decimal": 0,
                "base_unit_id": null,
                "base_unit_multiplier": null,
                "created_by": 1,
                "deleted_at": null,
                "created_at": "2018-01-03 15:15:20",
                "updated_at": "2018-01-03 15:15:20",
                "base_unit": null
            },
            {
                "id": 2,
                "business_id": 1,
                "actual_name": "Packets",
                "short_name": "packets",
                "allow_decimal": 0,
                "base_unit_id": null,
                "base_unit_multiplier": null,
                "created_by": 1,
                "deleted_at": null,
                "created_at": "2018-01-06 01:07:01",
                "updated_at": "2018-01-06 01:08:36",
                "base_unit": null
            },
            {
                "id": 15,
                "business_id": 1,
                "actual_name": "Dozen",
                "short_name": "dz",
                "allow_decimal": 0,
                "base_unit_id": 1,
                "base_unit_multiplier": "12.0000",
                "created_by": 9,
                "deleted_at": null,
                "created_at": "2020-07-20 13:11:09",
                "updated_at": "2020-07-20 13:11:09",
                "base_unit": {
                    "id": 1,
                    "business_id": 1,
                    "actual_name": "Pieces",
                    "short_name": "Pc(s)",
                    "allow_decimal": 0,
                    "base_unit_id": null,
                    "base_unit_multiplier": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 15:15:20",
                    "updated_at": "2018-01-03 15:15:20"
                }
            }
        ]
    }
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        
        $units = Unit::where('business_id', $business_id)
                    ->with(['base_unit'])
                    ->get();

        return CommonResource::collection($units);
    }

    /**
     * Get the specified unit
     *
     * @urlParam unit required comma separated ids of the units Example: 1
     * @response {
        "data": [
            {
                "id": 1,
                "business_id": 1,
                "actual_name": "Pieces",
                "short_name": "Pc(s)",
                "allow_decimal": 0,
                "base_unit_id": null,
                "base_unit_multiplier": null,
                "created_by": 1,
                "deleted_at": null,
                "created_at": "2018-01-03 15:15:20",
                "updated_at": "2018-01-03 15:15:20",
                "base_unit": null
            }
        ]
    }
     */
    public function show($unit_ids)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $unit_ids = explode(',', $unit_ids);

        $units = Unit::where('business_id', $business_id)
                        ->whereIn('id', $unit_ids)
                        ->with(['base_unit'])
                        ->get();

        return CommonResource::collection($units);
    }
}
