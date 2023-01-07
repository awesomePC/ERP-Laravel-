<?php

namespace Modules\Crm\Entities;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_campaigns';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'contact_ids' => 'array',
        'additional_info' => 'array'
    ];

    /**
     * user who created a campaign.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public static function getTags()
    {
        return ['{contact_name}', '{campaign_name}', '{business_name}'];
    }
}
