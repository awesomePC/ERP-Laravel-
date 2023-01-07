<?php

namespace Modules\Crm\Entities;

use Illuminate\Database\Eloquent\Model;

class Leaduser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_lead_users';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
