<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;


use Spatie\Activitylog\Traits\LogsActivity;

class EssentialsLeave extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logAttributesToIgnore = [ 'created_at', 'updated_at'];

    protected static $logOnlyDirty = true;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function leave_type()
    {
        return $this->belongsTo(\Modules\Essentials\Entities\EssentialsLeaveType::class, 'essentials_leave_type_id');
    }

    public function changed_by_user()
    {
        return $this->belongsTo(\App\User::class, 'changed_by');
    }
}
