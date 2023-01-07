<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class EssentialsTodoComment extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function added_by()
    {
        return $this->belongsTo(\App\User::class, 'comment_by');
    }

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public function task()
    {
        return $this->belongsTo(\Modules\Essentials\Entities\ToDo::class, 'task_id');
    }
}
