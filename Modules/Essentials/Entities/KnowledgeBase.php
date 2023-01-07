<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;


class KnowledgeBase extends Model
{
    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */

    protected $guarded = ['id'];

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'essentials_kb';

    /**
     * Get all the children of the knowledge base.
     */
    public function children()
    {
        return $this->hasMany(\Modules\Essentials\Entities\KnowledgeBase::class, 'parent_id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'essentials_kb_users','kb_id', 'user_id');
    }
}
