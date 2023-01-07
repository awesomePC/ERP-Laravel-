<?php

namespace Modules\Crm\Entities;

use Illuminate\Database\Eloquent\Model;

class ScheduleUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_schedule_users';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * Get the schedule that owns the user.
     */
    public function schedule()
    {
        return $this->belongsTo('Modules\Crm\Entities\Schedule');
    }
}
