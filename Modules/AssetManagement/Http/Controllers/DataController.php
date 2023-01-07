<?php

namespace Modules\AssetManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use Menu;

class DataController extends Controller
{   
    public function superadmin_package()
    {
        return [
            [
                'name' => 'assetmanagement_module',
                'label' => __('assetmanagement::lang.asset_management'),
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
                'value' => 'asset.view',
                'label' => __('assetmanagement::lang.view_asset'),
                'default' => false
            ],
            [
                'value' => 'asset.view_all_maintenance',
                'label' => __('assetmanagement::lang.view_all_maintenance'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'view_maintenance'
            ],
            [
                'value' => 'asset.view_own_maintenance',
                'label' => __('assetmanagement::lang.view_own_maintenance'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'view_maintenance',
            ],
        ];
    }

    /**
    * Function to add module taxonomies
    * @return array
    */
    public function addTaxonomies()
    {
        $business_id = request()->session()->get('user.business_id');

        $module_util = new ModuleUtil();
        if (!(auth()->user()->can('superadmin') || $module_util->hasThePermissionInSubscription($business_id, 'assetmanagement_module'))) {
            return ['asset' => []];
        }
        
        return [
            'asset' => [
                'taxonomy_label' =>  __('assetmanagement::lang.asset_category'),
                'heading' => __('assetmanagement::lang.asset_categories'),
                'sub_heading' => __('assetmanagement::lang.manage_asset_categories'),
                'enable_taxonomy_code' => false,
                'enable_sub_taxonomy' => false,
                'navbar' => 'assetmanagement::layouts.nav'
            ]
        ];
    }

    /**
     * Adds Repair menus
     * @return null
     */
    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $is_asset_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'assetmanagement_module');

        $background_color = '';
        if (config('app.env') == 'demo') {
            $background_color = '#2e97bf !important';
        }

        if ($is_asset_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('asset.view') || auth()->user()->can('asset.view_own_maintenance') || auth()->user()->can('asset.view_all_maintenance') )) {
            Menu::modify('admin-sidebar-menu', function ($menu) use ($background_color) {
                $menu->url(
                            action('\Modules\AssetManagement\Http\Controllers\AssetController@dashboard'),
                            __('assetmanagement::lang.asset_management'),
                            ['icon' => 'fas fa fa-boxes', 'active' => request()->segment(1) == 'asset', 'style' => 'background-color:'.$background_color]
                        )
                ->order(87);
            });
        }
    }

    /**
     * Parses notification message from database.
     * @return array
     */
    public function parse_notification($notification)
    {
        $notification_data = [];
        if ($notification->type ==
            'Modules\AssetManagement\Notifications\AssetSentForMaintenance' || $notification->type ==
            'Modules\AssetManagement\Notifications\AssetAssignedForMaintenance') {
            
            $notification_data = [
                'msg' => $notification->data['msg'],
                'icon_class' => 'fas fa-tools bg-green',
                'link' => action('\Modules\AssetManagement\Http\Controllers\AssetMaitenanceController@index'),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans()
            ];
        }

        return $notification_data;
    }
}
