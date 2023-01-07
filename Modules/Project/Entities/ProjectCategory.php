<?php

namespace Modules\Project\Entities;

use App\Category;

class ProjectCategory extends Category
{


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorizables';


    /**
     * Get all of the category that are assigned to project.
     */
    public function project()
    {
        return $this->morphedByMany('Modules\Project\Entities\Project', 'categorizable');
    }
}
