<?php

namespace Modules\Crm\Console;

use App\Business;
use App\User;
use App\Utils\NotificationUtil;

use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Crm\Entities\Schedule;
use Modules\Crm\Notifications\ScheduleNotification;
use Notification;

class SendScheduleNotification extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'pos:sendScheduleNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Schedule Notification to User And Customer if notification is allowed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $commonUtil;
    protected $notificationUtil;
    public function __construct(Util $commonUtil, NotificationUtil $notificationUtil)
    {
        parent::__construct();
        $this->commonUtil = $commonUtil;
        $this->notificationUtil = $notificationUtil;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $schedules = Schedule::with('users', 'createdBy')
                        ->where('allow_notification', 1)
                        ->where('start_datetime', '>=', Carbon::now())
                        ->get();

        foreach ($schedules as $key => $schedule) {
            $notifiy_before = 0;
            $current_datetime = Carbon::now();
            $start_datetime = Carbon::parse($schedule->start_datetime);
            if ($schedule->notify_type == 'minute') {
                $notifiy_before = $current_datetime->diffInMinutes($start_datetime);
            } elseif ($schedule->notify_type == 'hour') {
                $notifiy_before = $current_datetime->diffInHours($start_datetime);
            } elseif ($schedule->notify_type == 'day') {
                $notifiy_before = $start_datetime->diffInDays($current_datetime);
            }

            if ($notifiy_before == $schedule->notify_before) {
                //get notifiable users
                $business_id = $schedule->business_id;
                $contact_id = $schedule->contact_id;
                $users = User::where('business_id', $business_id)
                            ->where('contact_id', $contact_id)
                            ->get();

                $notifiable_users = $users->merge($schedule->users);

                //format schedule start date time in business format
                $business = Business::find($business_id);
                $startdatetime = $this->commonUtil->format_date($start_datetime, true, $business);

                //Used for broadcast notification
                $schedule['start_datetime'] = $startdatetime;
                $schedule['broadcast_title'] = __('crm.schedule');
                $schedule['body'] = __(
                    'crm::lang.schedule_notification',
                    [
                    'created_by' => $schedule->createdBy->user_full_name,
                    'title' => $schedule->title,
                    'startdatetime' => $startdatetime
                    ]
                );
                $schedule['link'] = '';

                //get delivery channel for notification
                $delivery_channel = ['database'];
                if ($schedule->notify_via['mail']) {
                    $delivery_channel[] = 'mail';
                }
                
                //send notifiction
                Notification::send($notifiable_users, new ScheduleNotification($schedule, $delivery_channel));
                if ($schedule->notify_via['sms']) {
                    //TODO: add numbers, add tags, replace tags, send sms
                    // $this->notificationUtil->sendSms($schedule);
                }
            }
        }
    }
}
