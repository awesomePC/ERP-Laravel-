<?php

namespace Modules\Project\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pjt_projects';

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
        'settings' => 'array',
    ];
    
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    
    /**
     * The member that belongs to the project.
     */
    public function members()
    {
        return $this->belongsToMany('App\User', 'pjt_project_members', 'project_id', 'user_id');
    }

    /**
     * user who created a project.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
    /**
     * Return the project lead
     */
    public function lead()
    {
        return $this->belongsTo('App\User', 'lead_id');
    }

    /**
     * Return the customer for the project.
     */
    public function customer()
    {
        return $this->belongsTo('App\Contact', 'contact_id');
    }

    /**
     * Get the project task for the project.
     */
    public function tasks()
    {
        return $this->hasMany('Modules\Project\Entities\ProjectTask');
    }
    
    /**
     * Get all of the projects's notes & documents.
     */
    public function documentsAndnote()
    {
        return $this->morphMany('App\DocumentAndNote', 'notable');
    }

    /**
     * Get the project time logs.
     */
    public function timeLogs()
    {
        return $this->hasMany('Modules\Project\Entities\ProjectTimeLog');
    }

    /**
     * Get the project categories.
     */
    public function categories()
    {
        return $this->morphToMany('App\Category', 'categorizable');
    }

    /**
     * Return the status for project.
     */
    public static function statusDropdown()
    {
        $status = [
                'not_started' => __('project::lang.not_started'),
                'in_progress' =>  __('project::lang.in_progress'),
                'on_hold' => __('project::lang.on_hold'),
                'cancelled' => __('project::lang.cancelled'),
                'completed' => __('project::lang.completed')
            ];

        return $status;
    }

    /**
     * Return the project list for dropdown.
     */
    public static function projectDropdown($business_id, $user_id = null)
    {
        $projects = Project::where('business_id', $business_id);

        // if user_id not empty get assigned project for filter
        if (!empty($user_id)) {
            $projects->whereHas('members', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        $projects = $projects->pluck('name', 'id');

        return $projects;
    }
}
