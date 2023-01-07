<?php

namespace Modules\AssetManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class AssetTransaction extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * get asset for transaction
     */
    public function asset()
    {
        return $this->belongsTo('Modules\AssetManagement\Entities\Asset', 'asset_id');
    }

    public function revokeTransaction()
    {
        return $this->hasMany('Modules\AssetManagement\Entities\AssetTransaction', 'parent_id');
    }
}
