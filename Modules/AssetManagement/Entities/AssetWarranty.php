<?php

namespace Modules\AssetManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class AssetWarranty extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    function getMonthsAttribute() {
        return \Carbon::parse($this->start_date)->diffInMonths($this->end_date);
    }
}
