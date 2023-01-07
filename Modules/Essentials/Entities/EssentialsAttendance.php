<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class EssentialsAttendance extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(\Modules\Essentials\Entities\Shift::class, 'essentials_shift_id');
    }
}
