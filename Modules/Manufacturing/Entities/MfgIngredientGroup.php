<?php

namespace Modules\Manufacturing\Entities;

use Illuminate\Database\Eloquent\Model;

class MfgIngredientGroup extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
