<?php

namespace Modules\Superadmin\Http\Controllers;

use \Notification;
use App\System;
use App\Utils\Util;
use Illuminate\Routing\Controller;
use Menu;
use Modules\Superadmin\Notifications\NewBusinessNotification;
use Modules\Superadmin\Notifications\NewBusinessWelcomNotification;

class DataController extends Controller
{
    /**
     * Parses notification message from database.
     * @return array
     */
    public function parse_notification($notification)
    {
        $notification_data = [];
        if ($notification->type ==
            'Modules\Superadmin\Notifications\SendSubscriptionExpiryAlert') {
            $data = $notification->data;
            $msg = __('superadmin::lang.subscription_expiry_alert', ['days_left' => $data['days_left'], 'app_name' => config('app.name')]);

            $notification_data = [
                'msg' => $msg,
                'icon_class' => "fas fa-exclamation-triangle bg-yellow",
                'link' =>  action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index'),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans()
            ];
        } else if($notification->type ==
            'Modules\Superadmin\Notifications\SuperadminCommunicator') {
            $msg = __('superadmin::lang.new_message_from_superadmin');

            $notification_data = [
                'msg' => $msg,
                'icon_class' => "fas fa-exclamation-triangle bg-yellow",
                'link' =>  action("HomeController@showNotification", [$notification->id]),
                'show_popup' => true,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans()
            ];
        }

        return $notification_data;
    }

    /**
     * Function to be called after a new business is created.
     * @return null
     */
    public function after_business_created($data)
    {
        try {
            //Send new registration notification to superadmin
            $is_notif_enabled =
            System::getProperty('enable_new_business_registration_notification');

            $common_util = new Util();
            
            if (!$common_util->IsMailConfigured()) {
                return null;
            }

            $email = System::getProperty('email');
            $business = $data['business'];
            
            if (!empty($email) && $is_notif_enabled == 1) {
                Notification::route('mail', $email)
                ->notify(new NewBusinessNotification($business));
            }

            //Send welcome email to business owner
            $welcome_email_settings = System::getProperties(['enable_welcome_email', 'welcome_email_subject', 'welcome_email_body'], true);
            
            if (isset($welcome_email_settings['enable_welcome_email']) && $welcome_email_settings['enable_welcome_email'] == 1 && !empty($welcome_email_settings['welcome_email_subject']) && !empty($welcome_email_settings['welcome_email_body'])) {
                $subject = $this->removeTags($welcome_email_settings['welcome_email_subject'], $business);
                $body = $this->removeTags($welcome_email_settings['welcome_email_body'], $business);

                $welcome_email_data = [
                    'subject' => $subject,
                    'body' => $body
                ];

                Notification::route('mail', $business->owner->email)
                ->notify(new NewBusinessWelcomNotification($welcome_email_data));
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }

        return null;
    }

    private function removeTags($string, $business)
    {
        $string = str_replace('{business_name}', $business->name, $string);
        $string = str_replace('{owner_name}', $business->owner->user_full_name, $string);

        return $string;
    }

    /**
     * Adds Superadmin menus
     * @return null
     */
    public function modifyAdminMenu()
    {
        if (auth()->user()->can('superadmin')) {
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu) {
                    $menu->url(action('\Modules\Superadmin\Http\Controllers\SuperadminController@index'), __('superadmin::lang.superadmin'), ['icon' => 'fa fas fa-users-cog', 'active' => request()->segment(1) == 'superadmin'])->order(1);
                }
            );       
        }

        if (auth()->user()->can('superadmin.access_package_subscriptions') && auth()->user()->can('business_settings.access')) {
            $menu = Menu::instance('admin-sidebar-menu');
            $menu->whereTitle(__('business.settings'), function ($sub) {
                $sub->url(
                    action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index'),
                    __('superadmin::lang.subscription'),
                    ['icon' => 'fa fas fa-sync', 'active' => request()->segment(1) == 'subscription']
                );
            });
        }
    }

    /**
     * Defines user permissions for the module.
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'superadmin.access_package_subscriptions',
                'label' => __('superadmin::lang.access_package_subscriptions'),
                'default' => false
            ]
        ];
    }
}
