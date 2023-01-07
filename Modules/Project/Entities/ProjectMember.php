<?php

namespace Modules\Project\Entities;

use App\User;
use DB;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pjt_project_members';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];

    public static function projectMembersDropdown($project_id, $user_id = null)
    {
        $user_ids = ProjectMember::where('project_id', $project_id)
                        ->pluck('user_id');

        $project_members = User::whereIn('id', $user_ids)
                    ->select('id', DB::raw("CONCAT(COALESCE(surname, ''),' ',COALESCE(first_name, ''),' ',COALESCE(last_name,'')) as full_name"));

        //filter by assigned member
        if (!empty($user_id)) {
            $project_members->where('id', $user_id);
        }

        $project_members = $project_members->pluck('full_name', 'id');

        return $project_members;
    }
}
