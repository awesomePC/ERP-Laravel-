<?php

namespace Modules\Project\Entities;

use Illuminate\Database\Eloquent\Model;

class ProjectTaskMember extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pjt_project_task_members';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];
}
