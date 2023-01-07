<?php

namespace Modules\Repair\Entities;

use Illuminate\Database\Eloquent\Model;

class DeviceModel extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'repair_device_models';

    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'repair_checklist' => 'array',
    ];

    /**
     * user who added a model.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * get device for model
     */
    public function Device()
    {
        return $this->belongsTo('App\Category', 'device_id');
    }

    /**
     * get Brand for model
     */
    public function Brand()
    {
        return $this->belongsTo('App\Brands', 'brand_id');
    }

    public static function forDropdown($business_id)
    {
        $device_models = DeviceModel::where('business_id', $business_id)
                            ->pluck('name', 'id');

        return $device_models;
    }
}
