<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'essentials_shifts';

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
        'holidays' => 'array',
    ];

    public function user_shifts($value='')
    {
        return $this->hasMany(\Modules\Essentials\Entities\EssentialsUserShift::class, 'essentials_shift_id');
    }

    public static function getGivenShiftInfo($business_id, $shift_id)
    {
        $shift = Shift::where('business_id', $business_id)
                    ->find($shift_id);
                    
        return $shift;
    }
}
