<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
 
 /**
 * The attributes that aren't mass assignable.
 *
 * @var array
 */
    protected $guarded = ['id'];
 
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'essentials_reminders';


    /**
     * Fetches all reminders for the calendar
     *
     * @param  array $data
     *
     * @return array
     */
    public static function getReminders($data)
    {
        $reminders = Reminder::where('business_id', $data['business_id'])
                   ->where('user_id', $data['user_id'])
                   ->get();

        $events = [];
        foreach ($reminders as $reminder) {
            $reminder_date = $reminder->date;
            // $time = $this->commonUtil->format_time($reminder->time);

            if ($reminder->repeat == "every_day" || $reminder->repeat == "every_month" || $reminder->repeat == "every_week") {
                while ($reminder_date <= $data['end_date']) {
                    $date = $reminder_date.' '. $reminder->time;
                    $end_time = !empty($reminder->end_time) ? $reminder->end_time : $reminder->time;
                    $end_date = $reminder_date.' '. $end_time;
                    $events[] = [
                          'title' => $reminder->name,
                          'start' => $date,
                          'end' => $end_date,
                          'name' => $reminder->name,
                          // 'time' => $time,
                          'repeat' => $reminder->repeat,
                          'url' => action('\Modules\Essentials\Http\Controllers\ReminderController@show', [$reminder->id]),
                          'allDay' => false,
                          'event_url' => action('\Modules\Essentials\Http\Controllers\ReminderController@index'),
                          'backgroundColor' => '#ff851b',
                    		'borderColor' => '#ff851b',
                    		'event_type' => 'reminder',
                        ];

                    $dt = strtotime($reminder_date);

                    if ($reminder->repeat == "every_day") {
                        $reminder_date= date("Y-m-d", strtotime("+1 day", $dt));
                    } elseif ($reminder->repeat == "every_month") {
                        $reminder_date= date("Y-m-d", strtotime("+1 month", $dt));
                    } elseif ($reminder->repeat == "every_week") {
                        $reminder_date= date("Y-m-d", strtotime("+1 weeks", $dt));
                    }
                }
            } elseif ($reminder->repeat == "one_time") {
                $date = $reminder_date . ' '. $reminder->time;
                $end_time = !empty($reminder->end_time) ? $reminder->end_time : $reminder->time;
                $end_date = $reminder_date.' '. $end_time;
                $events[] = [
                        'title' => $reminder->name,
                        'start' => $date,
                        'end' => $end_date,
                        'name' => $reminder->name,
                        // 'time' => $time,
                        'repeat' => $reminder->repeat,
                        'url' => action('\Modules\Essentials\Http\Controllers\ReminderController@show', [$reminder->id]),
                        'allDay' => false,
                        'event_url' => action('\Modules\Essentials\Http\Controllers\ReminderController@index'),
                        'backgroundColor' => '#ff851b',
                    	'borderColor' => '#ff851b',
                    	'event_type' => 'reminder',
                    ];
            }
        }

        return $events;
    }
}
