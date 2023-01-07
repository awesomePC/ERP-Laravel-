<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class EssentialsHoliday extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }
}
