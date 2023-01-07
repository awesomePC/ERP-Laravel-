<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;

use App\TaxRate;

/**
 * @group Tax management
 * @authenticated
 *
 * APIs for managing taxes
 */
class TaxController extends ApiController
{
    /**
     * List taxes
     *
     * @response {
    "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "name": "VAT@10%",
                    "amount": 10,
                    "is_tax_group": 0,
                    "created_by": 1,
                    "woocommerce_tax_rate_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:40:07",
                    "updated_at": "2018-01-04 02:40:07"
                },
                {
                    "id": 2,
                    "business_id": 1,
                    "name": "CGST@10%",
                    "amount": 10,
                    "is_tax_group": 0,
                    "created_by": 1,
                    "woocommerce_tax_rate_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:40:55",
                    "updated_at": "2018-01-04 02:40:55"
                },
                {
                    "id": 3,
                    "business_id": 1,
                    "name": "SGST@8%",
                    "amount": 8,
                    "is_tax_group": 0,
                    "created_by": 1,
                    "woocommerce_tax_rate_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:41:13",
                    "updated_at": "2018-01-04 02:41:13"
                },
                {
                    "id": 4,
                    "business_id": 1,
                    "name": "GST@18%",
                    "amount": 18,
                    "is_tax_group": 1,
                    "created_by": 1,
                    "woocommerce_tax_rate_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:42:19",
                    "updated_at": "2018-01-04 02:42:19"
                }
            ]
        }
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        
        $taxes = TaxRate::where('business_id', $business_id)
                        ->get();

        return CommonResource::collection($taxes);
    }

    /**
     * Get the specified tax
     * @urlParam tax required comma separated ids of required taxes Example: 1
     *
     * @response {
            "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "name": "VAT@10%",
                    "amount": 10,
                    "is_tax_group": 0,
                    "created_by": 1,
                    "woocommerce_tax_rate_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:40:07",
                    "updated_at": "2018-01-04 02:40:07"
                }
            ]
        }   
    */
    public function show($tax_ids)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $tax_ids = explode(',', $tax_ids);

        $taxes = TaxRate::where('business_id', $business_id)
                        ->whereIn('id', $tax_ids)
                        ->get();

        return CommonResource::collection($taxes);
    }
}
