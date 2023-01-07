<?php

namespace Modules\Crm\Entities;

use Illuminate\Database\Eloquent\Model;

class ScheduleLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_schedule_logs';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the post that owns the comment.
     */
    public function schedule()
    {
        return $this->belongsTo('Modules\Crm\Entities\Schedule', 'schedule_id');
    }

    /**
     * user who created a schedule log.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}
