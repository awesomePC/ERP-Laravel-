<?php

namespace Modules\AssetManagement\Utils;

use App\Utils\Util;
use App\Business;
use App\NotificationTemplate;
use Modules\AssetManagement\Entities\AssetMaintenance;
use App\User;
use Illuminate\Support\Facades\Notification;
use Modules\AssetManagement\Notifications\AssetSentForMaintenance;
use Modules\AssetManagement\Notifications\AssetAssignedForMaintenance;

class AssetUtil extends Util
{
    public function getAssetSettings($business_id)
    {
        $asset_settings = Business::where('id', $business_id)
                            ->value('asset_settings');

        $asset_settings = !empty($asset_settings) ? json_decode($asset_settings, true) : [];

        return $asset_settings;
    }

    /**
     * Sends notification when an asset is sent for maintenance
     */
    public function sendAssetSentForMaintenanceNotification($maintenance_id)
    {
        $maintenance = AssetMaintenance::with(['asset', 'assignedTo', 'createdBy'])->find($maintenance_id);

        $settings = $this->getAssetSettings($maintenance->business_id);

        if (empty($settings['send_for_maintenence_recipients'])) {
            return false;
        }

        $via = ['database'];
        if (!empty($settings['enable_asset_send_for_maintenance_email'])) {
            $via[] = 'mail';
        }

        $template = NotificationTemplate::where('business_id', $maintenance->business_id)
                                        ->where('template_for', 'send_for_maintenance')
                                        ->first();

        $email_data = [
            'subject' => !empty($template) ? $this->replaceEmailTags($template->subject, $maintenance) : __('assetmanagement::lang.asset_sent_for_maintenance', ['asset_code' => $maintenance->asset->asset_code]),
            'body' => !empty($template) ? $this->replaceEmailTags($template->email_body, $maintenance) : '',
            'via' => $via
        ];

        $recipients = User::whereIn('id', $settings['send_for_maintenence_recipients'])
                        ->get();

        Notification::send($recipients, new AssetSentForMaintenance($email_data));
    }

    /**
     * Sends notification when an asset is asigned for maintenance
     */
    public function sendAssetAssignedForMaintenanceNotification($maintenance_id)
    {
        $maintenance = AssetMaintenance::with(['asset', 'assignedTo', 'createdBy'])->find($maintenance_id);

        $settings = $this->getAssetSettings($maintenance->business_id);

        $via = ['database'];
        if (!empty($settings['enable_asset_assigned_for_maintenance_email'])) {
            $via[] = 'mail';
        }

        $template = NotificationTemplate::where('business_id', $maintenance->business_id)
                                        ->where('template_for', 'assigned_for_maintenance')
                                        ->first();

        $email_data = [
            'subject' => !empty($template) ? $this->replaceEmailTags($template->subject, $maintenance) : __('assetmanagement::lang.asset_assigned_for_maintenance', ['asset_code' => $maintenance->asset->asset_code]),
            'body' => !empty($template) ? $this->replaceEmailTags($template->email_body, $maintenance) : '',
            'via' => $via
        ];

        if (!empty($maintenance->assignedTo)) {
            $maintenance->assignedTo->notify(new AssetAssignedForMaintenance($email_data));
        }

        
    }

    /**
     * Replaces tags from a string
     */
    public function replaceEmailTags($string, $maintenance)
    {
        if (strpos($string, '{asset_code}') !== false) {
            $string = str_replace('{asset_code}', $maintenance->asset->asset_code, $string);
        }

        if (strpos($string, '{maintenance_id}') !== false) {
            $string = str_replace('{maintenance_id}', $maintenance->maitenance_id, $string);
        }

        if (strpos($string, '{status}') !== false) {
            $status = !empty($maintenance->status) ? $this->maintenanceStatuses()[$maintenance->status]['label'] : '';
            $string = str_replace('{status}', $status, $string);
        }

        if (strpos($string, '{priority}') !== false) {
            $priority = !empty($maintenance->priority) ? $this->maintenancePriorities()[$maintenance->priority]['label'] : '';
            $string = str_replace('{priority}', $priority, $string);
        }

        if (strpos($string, '{details}') !== false) {
            $string = str_replace('{details}', $maintenance->details, $string);
        }

        if (strpos($string, '{created_by}') !== false) {
            $string = str_replace('{created_by}', $maintenance->createdBy->user_full_name, $string);
        }

        return $string;
    }

    public function maintenanceStatuses()
    {
        return [
            'new' => [
                'label' => __('assetmanagement::lang.new'),
                'class' => 'bg-primary',
            ],
            'in_progress' => [
                'label' => __('assetmanagement::lang.in_progress'),
                'class' => 'bg-yellow',
            ],
            'completed' => [
                'label' => __('restaurant.completed'),
                'class' => 'bg-green',
            ],
            'cancelled' => [
                'label' =>__('restaurant.cancelled'),
                'class' => 'bg-danger'
            ],
        ];
    }

    public function maintenancePriorities()
    {
        return [
            'high' => [
                'label' => __('assetmanagement::lang.high'),
                'class' => 'bg-red',
            ],
            'medium' => [
                'label' => __('assetmanagement::lang.medium'),
                'class' => 'bg-yellow',
            ],
            'low' => [
                'label' =>__('assetmanagement::lang.low'),
                'class' => 'bg-green',
            ],
        ];
    }
}
