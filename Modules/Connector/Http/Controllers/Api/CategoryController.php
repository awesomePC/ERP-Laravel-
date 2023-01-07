<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;

use App\Category;

/**
 * @group Taxonomy management
 * @authenticated
 *
 * APIs for managing taxonomies
 */
class CategoryController extends ApiController
{
    /**
     * List taxonomy
     * @queryParam type Type of taxonomy (product, device, hrm_department)
     *
     * @response {
            "data": [
                {
                    "id": 1,
                    "name": "Men's",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 0,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:06:34",
                    "updated_at": "2018-01-03 21:06:34",
                    "sub_categories": [
                        {
                            "id": 4,
                            "name": "Jeans",
                            "business_id": 1,
                            "short_code": null,
                            "parent_id": 1,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null,
                            "deleted_at": null,
                            "created_at": "2018-01-03 21:07:34",
                            "updated_at": "2018-01-03 21:07:34"
                        },
                        {
                            "id": 5,
                            "name": "Shirts",
                            "business_id": 1,
                            "short_code": null,
                            "parent_id": 1,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null,
                            "deleted_at": null,
                            "created_at": "2018-01-03 21:08:18",
                            "updated_at": "2018-01-03 21:08:18"
                        }
                    ]
                },
                {
                    "id": 21,
                    "name": "Food & Grocery",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 0,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-06 05:31:35",
                    "updated_at": "2018-01-06 05:31:35",
                    "sub_categories": []
                }
            ]
        }
     *
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        
        $query = Category::where('business_id', $business_id)
                        ->onlyParent()
                        ->with('sub_categories');

        if (!empty(request()->input('type'))) {
            $query->where('category_type', request()->input('type'));
        }

        $categories = $query->get();

        return CommonResource::collection($categories);
    }

    /**
     * Get the specified taxonomy
     *
     * @urlParam taxonomy required comma separated ids of product categories Example: 1

     * @response {
            "data": [
                {
                    "id": 1,
                    "name": "Men's",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 0,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:06:34",
                    "updated_at": "2018-01-03 21:06:34",
                    "sub_categories": [
                        {
                            "id": 4,
                            "name": "Jeans",
                            "business_id": 1,
                            "short_code": null,
                            "parent_id": 1,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null,
                            "deleted_at": null,
                            "created_at": "2018-01-03 21:07:34",
                            "updated_at": "2018-01-03 21:07:34"
                        },
                        {
                            "id": 5,
                            "name": "Shirts",
                            "business_id": 1,
                            "short_code": null,
                            "parent_id": 1,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null,
                            "deleted_at": null,
                            "created_at": "2018-01-03 21:08:18",
                            "updated_at": "2018-01-03 21:08:18"
                        }
                    ]
                }
            ]
        }
     */
    public function show($category_ids)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $category_ids = explode(',', $category_ids);

        $categories = Category::where('business_id', $business_id)
                        ->whereIn('id', $category_ids)
                        ->with('sub_categories')
                        ->get();

        return CommonResource::collection($categories);
    }
}
