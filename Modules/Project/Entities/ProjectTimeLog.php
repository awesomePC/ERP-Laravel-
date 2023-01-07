<?php

namespace Modules\Project\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectTimeLog extends Model
{
    use LogsActivity;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pjt_project_time_logs';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];

    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;

    /**
     * Get the task for time log.
     */
    public function task()
    {
        return $this->belongsTo('Modules\Project\Entities\ProjectTask', 'project_task_id');
    }

    /**
     * Get the user for time log.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Get the user who added time log.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Return the project for a time log.
     */
    public function project()
    {
        return $this->belongsTo('Modules\Project\Entities\Project', 'project_id');
    }
}
