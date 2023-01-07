<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;

use App\Restaurant\ResTable;

/**
 * @group Table management
 * @authenticated
 *
 * APIs for managing tables
 */
class TableController extends ApiController
{
    /**
     * List tables
     * 
     * @queryParam location_id  int id of the location Example: 1
     *
     * @response {
        "data": [
            {
                "id": 5,
                "business_id": 1,
                "location_id": 1,
                "name": "Table 1",
                "description": null,
                "created_by": 9,
                "deleted_at": null,
                "created_at": "2020-06-04 22:36:37",
                "updated_at": "2020-06-04 22:36:37"
            }
        ]
    }
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        
        $query = ResTable::where('business_id', $business_id);

        if (!empty(request()->location_id)) {
            $query->where('location_id', request()->location_id);
        }
        $tables = $query->get();
        return CommonResource::collection($tables);
    }

    /**
     * Show the specified table
     * @urlParam table required comma separated ids of required tables Example: 5
     *
     * @response {
        "data": [
            {
                "id": 5,
                "business_id": 1,
                "location_id": 1,
                "name": "Table 1",
                "description": null,
                "created_by": 9,
                "deleted_at": null,
                "created_at": "2020-06-04 22:36:37",
                "updated_at": "2020-06-04 22:36:37"
            }
        ]
    }
     */
    public function show($table_ids)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $table_ids = explode(',', $table_ids);

        $tables = ResTable::where('business_id', $business_id)
                        ->whereIn('id', $table_ids)
                        ->get();

        return CommonResource::collection($tables);
    }
}
