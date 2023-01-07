<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use App\Utils\ModuleUtil;
use Illuminate\Support\Arr;
use \Module;

/**
 * @group Superadmin
 * @authenticated
 *
 * APIs for superadmin module
 */
class SuperadminController extends ApiController
{

    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * If SaaS installed get active subscription details, else return the enabled modules details in package_details
     * @response {
        "data": {
            "id": 1,
            "business_id": 1,
            "package_id": 3,
            "start_date": "2020-09-05 00:00:00",
            "trial_end_date": "2020-09-15",
            "end_date": "2020-10-05 00:00:00",
            "package_price": "599.9900",
            "package_details": {
                "location_count": 0,
                "user_count": 0,
                "product_count": 0,
                "invoice_count": 0,
                "name": "Unlimited",
                "woocommerce_module": 1,
                "essentials_module": 1
            },
            "created_id": 1,
            "paid_via": "stripe",
            "payment_transaction_id": "ch_1CuLdQAhokBpT93LVZNg2At6",
            "status": "approved",
            "deleted_at": null,
            "created_at": "2018-08-01 07:49:09",
            "updated_at": "2018-08-01 07:49:09",
            "locations_created": 1,
            "users_created": 6,
            "products_created": 2,
            "invoices_created": 1
        }
    }
     */
    public function getActiveSubscription()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $output = [];

        if ($this->moduleUtil->isSuperadminInstalled()) {
            $subscription = \Modules\Superadmin\Entities\Subscription::active_subscription($business_id);

            if (!empty($subscription)) {
                $resource_count = $this->moduleUtil->getResourceCount($business_id, $subscription);
                $output = array_merge($subscription->toArray(), $resource_count);
            }

        } else {

            //If not installed return the installed modules details.
            $modules = Arr::pluck(Module::toCollection()->toArray(), 'name');
            $output = [
                'package_details' => []
            ];

            foreach ($modules as $module) {
                if($this->moduleUtil->isModuleInstalled($module)){
                    $output['package_details'][strtolower($module) . '_module'] = 1;
                }
            }
        }

        return new CommonResource($output);
    }


    /**
     * Get Superadmin Package List
     * @response {
        "data": [
            {
                "id": 1,
                "name": "Starter - Free",
                "description": "Give it a test drive...",
                "location_count": 0,
                "user_count": 0,
                "product_count": 0,
                "bookings": 0,
                "kitchen": 0,
                "order_screen": 0,
                "tables": 0,
                "invoice_count": 0,
                "interval": "months",
                "interval_count": 1,
                "trial_days": 10,
                "price": "0.0000",
                "custom_permissions": {
                    "assetmanagement_module": "1",
                    "connector_module": "1",
                    "crm_module": "1",
                    "essentials_module": "1",
                    "manufacturing_module": "1",
                    "productcatalogue_module": "1",
                    "project_module": "1",
                    "repair_module": "1",
                    "woocommerce_module": "1"
                },
                "created_by": 1,
                "sort_order": 0,
                "is_active": 1,
                "is_private": 0,
                "is_one_time": 0,
                "enable_custom_link": 0,
                "custom_link": "",
                "custom_link_text": "",
                "deleted_at": null,
                "created_at": "2020-10-09 16:38:02",
                "updated_at": "2020-11-11 12:19:17"
            },
            {
                "id": 2,
                "name": "Regular",
                "description": "For Small Shops",
                "location_count": 0,
                "user_count": 0,
                "product_count": 0,
                "bookings": 0,
                "kitchen": 0,
                "order_screen": 0,
                "tables": 0,
                "invoice_count": 0,
                "interval": "months",
                "interval_count": 1,
                "trial_days": 10,
                "price": "199.9900",
                "custom_permissions": {
                    "repair_module": "1"
                },
                "created_by": 1,
                "sort_order": 1,
                "is_active": 1,
                "is_private": 0,
                "is_one_time": 0,
                "enable_custom_link": 0,
                "custom_link": null,
                "custom_link_text": null,
                "deleted_at": null,
                "created_at": "2020-10-09 16:38:02",
                "updated_at": "2020-10-09 16:38:02"
            },
            {
                "id": 3,
                "name": "Unlimited",
                "description": "For Large Business",
                "location_count": 0,
                "user_count": 0,
                "product_count": 0,
                "bookings": 0,
                "kitchen": 0,
                "order_screen": 0,
                "tables": 0,
                "invoice_count": 0,
                "interval": "months",
                "interval_count": 1,
                "trial_days": 10,
                "price": "599.9900",
                "custom_permissions": {
                    "assetmanagement_module": "1",
                    "connector_module": "1",
                    "crm_module": "1",
                    "essentials_module": "1",
                    "manufacturing_module": "1",
                    "productcatalogue_module": "1",
                    "project_module": "1",
                    "repair_module": "1",
                    "woocommerce_module": "1"
                },
                "created_by": 1,
                "sort_order": 1,
                "is_active": 1,
                "is_private": 0,
                "is_one_time": 0,
                "enable_custom_link": 0,
                "custom_link": "",
                "custom_link_text": "",
                "deleted_at": null,
                "created_at": "2020-10-09 16:38:02",
                "updated_at": "2020-11-02 12:09:19"
            }
        ]
    }
     */
    public function getPackages()
    {
        if (!$this->moduleUtil->isSuperadminInstalled()) {
            abort(403, 'Unauthorized action.');
        }

        $packages = \Modules\Superadmin\Entities\Package::
                        orderby('sort_order', 'asc')->get();
        
        return new CommonResource($packages);
    }
}
