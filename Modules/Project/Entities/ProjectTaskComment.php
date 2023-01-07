<?php

namespace Modules\Project\Entities;

use Illuminate\Database\Eloquent\Model;

class ProjectTaskComment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pjt_project_task_comments';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public function commentedBy()
    {
        return $this->belongsTo(\App\User::class, 'commented_by');
    }
}
