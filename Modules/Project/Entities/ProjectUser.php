<?php

namespace Modules\Project\Entities;

use App\User;

class ProjectUser extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The project that belong to the user.
     */
    public function projects()
    {
        return $this->belongsToMany('Modules\Project\Entities\Project', 'pjt_project_members', 'user_id', 'project_id');
    }
}
