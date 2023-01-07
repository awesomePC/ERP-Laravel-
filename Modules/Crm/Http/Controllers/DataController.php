<?php

namespace Modules\Crm\Http\Controllers;

use App\Business;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Modules\Crm\Utils\CrmUtil;
use Illuminate\Routing\Controller;
use Menu;
use Modules\Crm\Entities\Schedule;
use DB;

class DataController extends Controller
{

    /**
     * Parses notification message from database.
     * @return array
     */
    public function parse_notification($notification)
    {
        $commonUtil = new Util();

        $notification_datas = [];
        if ($notification->type == 'Modules\Crm\Notifications\ScheduleNotification') {
            $data = $notification->data;
            $schedule = Schedule::with('createdBy')
                        ->where('business_id', $data['business_id'])
                        ->find($data['schedule_id']);

            if (!empty($schedule)) {
                $business = Business::find($data['business_id']);
                $startdatetime = $commonUtil->format_date($schedule->start_datetime, true, $business);
                $msg = __(
                    'crm::lang.schedule_notification',
                    [
                    'created_by' => $schedule->createdBy->user_full_name,
                    'title' => $schedule->title,
                    'startdatetime' => $startdatetime
                    ]
                );

                $notification_datas = [
                    'msg' => $msg,
                    'icon_class' => 'fas fa fa-calendar-check bg-green',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans()
                ];
            }
        }

        return $notification_datas;
    }

    /**
     * Adds Crm menus
     * @return null
     */
    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        
        $is_crm_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'crm_module');

        $commonUtil = new Util();
        $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

        if ($is_crm_enabled) {
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu) use ($is_admin) {
                    $menu->url(action('\Modules\Crm\Http\Controllers\CrmDashboardController@index'), __('crm::lang.crm'), ['icon' => 'fas fa fa-broadcast-tower', 'active' => request()->segment(1) == 'crm' || request()->get('type') == 'life_stage' || request()->get('type') == 'source'])->order(86);
                }
            );

            //TODO: uncomment this query in 3-4 months and comment below queries.
            // $crm_settings = Business::where('id', auth()->user()->business_id)
            //                     ->value('crm_settings');
            // $crm_settings = !empty($crm_settings) ? json_decode($crm_settings, true) : [];

            $business = Business::find(auth()->user()->business_id);
            $crm_settings = !empty($business->crm_settings) ? json_decode($business->crm_settings, true) : [];

            if (!empty($crm_settings['enable_order_request'])) {
                $menu = Menu::instance('admin-sidebar-menu');
                $menu->whereTitle(__('sale.sale'), function ($sub) {
                    $sub->url(
                        action('\Modules\Crm\Http\Controllers\OrderRequestController@listOrderRequests'),
                        __('crm::lang.order_request'),
                        ['icon' => 'fa fas fa-sync', 'active' => request()->segment(2) == 'order-request']
                    );
                });
            }
        }
    }

    /**
     * Superadmin package permissions
     * @return array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'crm_module',
                'label' => __('crm::lang.crm_module'),
                'default' => false
            ]
        ];
    }

    /**
     * Returns Tab path with required extra data.
     * for contact view
     * @return array
     */
    public function get_contact_view_tabs()
    {
        $module_util = new ModuleUtil();
        $business_id = request()->session()->get('user.business_id');
        $is_crm_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'crm_module');

        if ($is_crm_enabled) {
            //for multiple tab just add another array of tab details and if js is in common file just include once in any array
            return  [
                [
                    'tab_menu_path' => 'crm::contact_login.partial.tab_menu',
                    'tab_content_path' => 'crm::contact_login.partial.tab_content',
                    'tab_data' => [],
                    'module_js_path' => 'crm::contact_login.contact_login_js'
                ],
            ];
        } else {
            return [];
        }
    }

    /**
    * Function to add essential module taxonomies
    * @return array
    */
    public function addTaxonomies()
    {
        $module_util = new ModuleUtil();
        $business_id = request()->session()->get('user.business_id');

        $output = [
                'source' => [],
                'life_stage' => []
            ];
        if (!(auth()->user()->can('superadmin') || $module_util->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            return $output;
        }

        if (auth()->user()->can('crm.access_sources')) {
            $output['source'] = [
                'taxonomy_label' =>  __('crm::lang.source'),
                'heading' => __('crm::lang.sources'),
                'sub_heading' => __('crm::lang.manage_source'),
                'enable_taxonomy_code' => false,
                'enable_sub_taxonomy' => false,
                'heading_tooltip' => __('crm::lang.source_are_used_when_lead_is_added'),
                'navbar' => 'crm::layouts.nav'
            ];
        }
        if (auth()->user()->can('crm.access_life_stage')) {
            $output['life_stage'] = [
                'taxonomy_label' =>  __('crm::lang.life_stage'),
                'heading' => __('crm::lang.life_stage'),
                'sub_heading' => __('crm::lang.manage_life_stage'),
                'enable_taxonomy_code' => false,
                'enable_sub_taxonomy' => false,
                'heading_tooltip' => __('crm::lang.lifestage_of_leads'),
                'navbar' => 'crm::layouts.nav'
            ];
        }

        return $output;
    }

    /**
     * Defines user permissions for the module.
     * @return array
     */
    public function user_permissions()
    {
        $permissions = [
            [
                'value' => 'crm.access_all_schedule',
                'label' => __('crm::lang.access_all_schedule'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'schedule_view'
            ],
            [
                'value' => 'crm.access_own_schedule',
                'label' => __('crm::lang.access_own_schedule'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'schedule_view'
            ],
            [
                'value' => 'crm.access_all_leads',
                'label' => __('crm::lang.access_all_leads'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'leads_view'
            ],
            [
                'value' => 'crm.access_own_leads',
                'label' => __('crm::lang.access_own_leads'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'leads_view'
            ],
            [
                'value' => 'crm.access_all_campaigns',
                'label' => __('crm::lang.access_all_campaigns'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'campaigns_view'
            ],
            [
                'value' => 'crm.access_own_campaigns',
                'label' => __('crm::lang.access_own_campaigns'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'campaigns_view'
            ],
            [
                'value' => 'crm.access_contact_login',
                'label' => __('crm::lang.access_contact_login'),
                'default' => false
            ],
            [
                'value' => 'crm.access_sources',
                'label' => __('crm::lang.access_sources'),
                'default' => false
            ],
            [
                'value' => 'crm.access_life_stage',
                'label' => __('crm::lang.access_life_stage'),
                'default' => false
            ],
            [
                'value' => 'crm.access_proposal',
                'label' => __('crm::lang.access_proposal'),
                'default' => false
            ]
        ];

        if (config('constants.enable_crm_call_log')) {
            $permissions[] = [
                'value' => 'crm.view_all_call_log',
                'label' => __('crm::lang.view_all_call_log'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'call_log_view'
            ];
            $permissions[] = [
                'value' => 'crm.view_own_call_log',
                'label' => __('crm::lang.view_own_call_log'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'call_log_view'
            ];
        }

        return $permissions;
    }

    /**
     * Fetches all calender events for the module
     *
     * @param  array $data
     *
     * @return array
     */
    public function calendarEvents($data)
    {
        if (!in_array('schedule', $data['events'])) {
            return [];
        }

        $query = Schedule::where('business_id', $data['business_id'])
                        ->whereBetween(DB::raw('date(start_datetime)'), [$data['start_date'], $data['end_date']]);

        if (!empty($data['user_id'])) {
            $query->where( function($qry) use ($data) {
                $qry->whereHas('users', function($q) use ($data){
                    $q->where('user_id', $data['user_id']);
                })->orWhere('created_by', $data['user_id']);
            });
        }

        $schedules = $query->get();

        $events = [];
        foreach ($schedules as $schedule) {
            $events[] = [
                'title' => $schedule->title,
                'start' => $schedule->start_datetime,
                'end' => $schedule->end_datetime,
                'url' => action('\Modules\Crm\Http\Controllers\ScheduleController@index'),
                'backgroundColor' => '#FEBE10',
                'borderColor'     => '#FEBE10',
                'event_type' => 'schedule',
                'allDay' => false,
            ];
        }

        return $events;
    }

    /**
     * List of calendar event types
     * 
     * @return array
     */
    public function eventTypes()
    {
        return [
            'schedule' => [ 
                'label' => __('crm::lang.follow_ups'),
                'color' => '#FEBE10',
            ]
        ];
    }

    public function contact_form_part()
    {
        $path = 'crm::contact_login.partial.contact_form_part';
        
        return  [
            'template_path' => $path,
            'template_data' => []
        ];
    }

    public function after_contact_saved($data)
    {
        $contact = $data['contact'];
        $input = $data['input'];

        if(!empty($input['contact_persons'])) {
            $crmUtil = new CrmUtil;

            foreach ($input['contact_persons'] as $contact_person_data) {

                if (!empty($contact_person_data['username']) && !empty($contact_person_data['password']) && !empty($contact_person_data['confirm_password'])) {
                    $contact_person_data['crm_contact_id'] = $contact->id;
                    $contact_person_data['business_id'] = request()->session()->get('user.business_id');
                    $contact_person_data['status'] = !empty($contact_person_data['is_active']) ? 'active' : 'inactive';
                    unset($contact_person_data['confirm_password']);

                    if (isset($contact_person_data['is_active'])) {
                        unset($contact_person_data['is_active']);
                    }

                    $crmUtil->creatContactPerson($contact_person_data);
                }
            }
        }
    }

    /**
     * Returns addtional js, css, html and files which 
     * will be included in the app layout
     * 
     * @return array
     */
    public function get_additional_script()
    {
        $additional_js = '
            <script type="text/javascript">
                $(document).on("contactFormvalidationAdded", "#contact_add_form", function(e) {

                    $("#contact_add_form .input-icheck").iCheck({
                        checkboxClass: "icheckbox_square-blue"
                    });

                    $(".allow_login").on("ifChecked", function(event){
                      $("#" + $(this).attr("data-loginDiv")).removeClass("hide");
                    });
                    $(".allow_login").on("ifUnchecked", function(event){
                      $("#" + $(this).attr("data-loginDiv")).addClass("hide");
                    });

                    if($("#username0").length) {
                        $( "#username0" ).rules( "add", {
                            minlength: 5,
                            remote: {
                                url: "/business/register/check-username",
                                type: "post",
                                data: {
                                    username: function() {
                                        return $( "#username0" ).val();
                                    }
                                }
                            },
                            messages: {
                                remote: "Invalid username or User already exist"
                            }
                        });
                    }

                    if($("#username1").length) {
                        $( "#username1" ).rules( "add", {
                            minlength: 5,
                            remote: {
                                url: "/business/register/check-username",
                                type: "post",
                                data: {
                                    username: function() {
                                        return $( "#username1" ).val();
                                    }
                                }
                            },
                            messages: {
                                remote: "Invalid username or User already exist"
                            }
                        });
                    }

                    if($("#username2").length) {
                        $( "#username2" ).rules( "add", {
                            minlength: 5,
                            remote: {
                                url: "/business/register/check-username",
                                type: "post",
                                data: {
                                    username: function() {
                                        return $( "#username2" ).val();
                                    }
                                }
                            },
                            messages: {
                                remote: "Invalid username or User already exist"
                            }
                        });
                    }
                });
            </script>
        ';
        $additional_css = '';
        $additional_html = '';
        $additional_views = [];

        return [
            'additional_js' => $additional_js,
            'additional_css' => $additional_css,
            'additional_html' => $additional_html,
            'additional_views' => $additional_views
        ];
    }
}
