<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class EssentialsMessage extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
    * Get sender.
    */
    public function sender()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
