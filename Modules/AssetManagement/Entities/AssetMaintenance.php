<?php

namespace Modules\AssetManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    /**
     * user added asset.
     */
    public function asset()
    {
        return $this->belongsTo(\Modules\AssetManagement\Entities\Asset::class, 'asset_id');
    }

    /**
     * user added asset maintence.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * user assigned asset maintence.
     */
    public function assignedTo()
    {
        return $this->belongsTo('App\User', 'assigned_to');
    }
}
