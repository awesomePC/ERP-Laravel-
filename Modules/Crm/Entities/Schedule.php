<?php

namespace Modules\Crm\Entities;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_schedules';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'notify_via' => 'array',
        'followup_additional_info' => 'array'
    ];
    
    /**
    * The member that belongs to the schedule.
    */
    public function users()
    {
        return $this->belongsToMany('App\User', 'crm_schedule_users', 'schedule_id', 'user_id');
    }

    /**
    * Invoices assigned to the follow up.
    */
    public function invoices()
    {
        return $this->belongsToMany('App\Transaction', 'crm_followup_invoices', 'follow_up_id', 'transaction_id');
    }

    /**
     * user who created a schedule.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function customer()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }

    public function scheduleLog()
    {
        return $this->hasMany(\Modules\Crm\Entities\ScheduleLog::class);
    }

    /**
     * Return the status for schedule.
     */
    public static function statusDropdown()
    {
        $status = [
                'scheduled' => __('crm::lang.scheduled'),
                'open' => __('crm::lang.open'),
                'cancelled' => __('crm::lang.canceled'),
                'completed' =>  __('crm::lang.completed')
            ];

        return $status;
    }

    public static function followUpTypeDropdown()
    {
        return [
            'call' => __('crm::lang.call'),
            'sms' => __('crm::lang.sms'), 
            'meeting' => __('crm::lang.meeting'),
            'email' => __('business.email')
        ];
    }

    public static function followUpNotifyTypeDropdown()
    {
        return [
                'minute' => __('crm::lang.minute'),
                'hour' => __('crm::lang.hour'),
                'day' => __('lang_v1.day')
            ];
    }

    public static function followUpNotifyViaDropdown()
    {
        return ['sms' => __('crm::lang.sms'),'mail' => __('business.email')];
    }
}
