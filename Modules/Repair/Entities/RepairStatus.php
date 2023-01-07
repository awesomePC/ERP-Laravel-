<?php

namespace Modules\Repair\Entities;

use Illuminate\Database\Eloquent\Model;

class RepairStatus extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function forDropdown($business_id, $include_attributes = false)
    {
        $query = RepairStatus::where('business_id', $business_id)
                    ->orderBy('sort_order', 'asc')
                    ->get();

        $statuses = $query->pluck('name', 'id');

        //Add sms, email template as attribute
        $template_attr = null;
        if ($include_attributes) {
            $template_attr = collect($query)->mapWithKeys(function($status){
                    return [$status->id => [
                            'data-sms_template' => $status->sms_template ?? '',
                            'data-email_subject' => $status->email_subject ?? '',
                            'data-email_body' => $status->email_body ?? '',
                            'data-is_completed_status' => $status->is_completed_status
                        ]
                    ];
            })->all();
        }

        $output = ['statuses' => $statuses, 'template' => $template_attr];

        return $output;
    }

    public static function getRepairSatuses($business_id)
    {
        $list = RepairStatus::where('business_id', $business_id)
                        ->orderBy('sort_order', 'asc')
                        ->get();

        return $list;
    }
}
