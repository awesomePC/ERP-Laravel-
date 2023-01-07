<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Transaction;
use App\Utils\ModuleUtil;
use DB;
use Illuminate\Routing\Controller;
use Menu;
use Modules\Manufacturing\Utils\ManufacturingUtil;

class DataController extends Controller
{
    /**
     * Defines module as a superadmin package.
     * @return Array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'manufacturing_module',
                'label' => __('manufacturing::lang.manufacturing_module'),
                'default' => false
            ]
        ];
    }

    /**
     * Defines user permissions for the module.
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'manufacturing.access_recipe',
                'label' => __('manufacturing::lang.access_recipe'),
                'default' => false
            ],
            [
                'value' => 'manufacturing.add_recipe',
                'label' => __('manufacturing::lang.add_recipe'),
                'default' => false
            ],
            [
                'value' => 'manufacturing.edit_recipe',
                'label' => __('manufacturing::lang.edit_recipe'),
                'default' => false
            ],
            [
                'value' => 'manufacturing.access_production',
                'label' => __('manufacturing::lang.access_production'),
                'default' => false
            ]
        ];
    }

    /**
     * Adds Manufacturing menus
     * @return null
     */
    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $is_mfg_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'manufacturing_module', 'superadmin_package');

        if ($is_mfg_enabled && (auth()->user()->can('manufacturing.access_recipe') || auth()->user()->can('manufacturing.access_production'))) {
            Menu::modify('admin-sidebar-menu', function ($menu) {
                $menu->url(
                        action('\Modules\Manufacturing\Http\Controllers\RecipeController@index'),
                        __('manufacturing::lang.manufacturing'),
                        ['icon' => 'fa fas fa-industry', 'style' => config('app.env') == 'demo' ? 'background-color: #ff851b;' : '', 'active' => request()->segment(1) == 'manufacturing']
                    )
                ->order(21);
            });
        }
    }

    public function profitLossReportData($data)
    {
        $business_id = $data['business_id'];
        $location_id = !empty($data['location_id']) ? $data['location_id'] : null;
        $start_date = !empty($data['start_date']) ? $data['start_date'] : null;
        $end_date = !empty($data['end_date']) ? $data['end_date'] : null;
        $user_id = !empty($data['user_id']) ? $data['user_id'] : null;

        $mfgUtil = new ManufacturingUtil();

        $production_totals = $mfgUtil->getProductionTotals($business_id, $location_id, $start_date, $end_date, $user_id);

        $report_data = [
            //left side data
            [
                [
                    'value' => $production_totals['total_production_cost'],
                    'label' => __('manufacturing::lang.total_production_cost'),
                    'add_to_net_profit' => true
                ]
            ],

            //right side data
            []
        ];

        return $report_data;
    }
}
