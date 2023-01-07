<?php
namespace Modules\Crm\Utils;

use App\Utils\Util;
use Modules\Crm\Entities\CrmContact;
use Modules\Crm\Entities\Schedule;
use Carbon\Carbon;
use DB;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Transaction;
use App\Business;

class CrmUtil extends Util
{
	public function addFollowUp($input, $user)
	{	

        $input['notify_via'] = [
                'sms' => !empty($input['notify_via']['sms']) ? 1: 0,
                'mail' => !empty($input['notify_via']['mail']) ? 1: 0
            ];

        $input['notify_type'] = !empty($input['notify_type']) ? $input['notify_type'] : 'hour';
		$input['schedule_type'] = !empty($input['schedule_type']) ? $input['schedule_type'] : 'email';
        $input['allow_notification'] = !empty($input['allow_notification']) ? 1 : 0;
        $input['business_id'] = $user->business_id;
        $input['created_by'] = $user->id;
        
        $assigned_user = $input['user_id'];
        unset($input['user_id']);

        $schedule = Schedule::create($input);
        $schedule->users()->sync($assigned_user);

        return $schedule;
	}

    public function addRecursiveFollowUp($input, $user)
    {   

        $input['notify_via'] = [
                'sms' => !empty($input['notify_via']['sms']) ? 1: 0,
                'mail' => !empty($input['notify_via']['mail']) ? 1: 0
            ];

        $input['notify_type'] = !empty($input['notify_type']) ? $input['notify_type'] : 'hour';
        $input['schedule_type'] = !empty($input['schedule_type']) ? $input['schedule_type'] : 'email';
        $input['allow_notification'] = !empty($input['allow_notification']) ? 1 : 0;
        $input['business_id'] = $user->business_id;
        $input['created_by'] = $user->id;
        $input['is_recursive'] = 1;
        $assigned_user = $input['user_id'];
        unset($input['user_id']);

        $schedule = Schedule::create($input);
        $schedule->users()->sync($assigned_user);

        return $schedule;
    }

    public function addAdvanceFollowUp($input, $user)
    {
        $follow_ups = $input['follow_ups'];
        unset($input['follow_ups']);

        if (array_key_exists('invoices', $input)) {
            unset($input['invoices']);
        }

        $replacable_inputs['in_days'] = !empty($input['in_days']) ? $input['in_days']: null;
        $replacable_inputs['title'] = $input['title'];
        $replacable_inputs['description'] = $input['description'];

        if (array_key_exists('in_days', $input)) {
            unset($input['in_days']);
        }
        
        foreach ($follow_ups as $key => $value) {
            $input['contact_id'] = $key;
            $input['user_id'] = $value['user_id'];
            $invoices = !empty($value['invoices']) ? $value['invoices'] : [];

            $replaced_tag_input = $this->replaceAdvFollowUpTags($input['contact_id'], $invoices, $replacable_inputs);

            $input['title'] = $replaced_tag_input['title'];
            $input['description'] = $replaced_tag_input['description'];

            $follow_up = $this->addFollowUp($input, $user);

            if (!empty($value['invoices'])) {
                $follow_up->invoices()->sync($value['invoices']);
            }
        }
    }

	public function updateFollowUp($follow_up_id, $input, $user)
	{
		$input['notify_via'] = [
            'sms' => !empty($input['notify_via']['sms']) ? 1: 0,
            'mail' => !empty($input['notify_via']['mail']) ? 1: 0
        ];

        $input['notify_type'] = !empty($input['notify_type']) ? $input['notify_type'] : 'hour';
		$input['schedule_type'] = !empty($input['schedule_type']) ? $input['schedule_type'] : 'email';
        $input['allow_notification'] = !empty($input['allow_notification']) ? 1 : 0;
        
        $assigned_user = $input['user_id'];
        unset($input['user_id']);

        $query = Schedule::where('business_id', $user->business_id);

        if (!$user->can('crm.access_all_schedule') && $user->can('crm.access_own_schedule')) {
            $query->where( function($qry) use($user) {
                $qry->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->orWhere('created_by', $user->id);
            });
        }

        $schedule = $query->findOrFail($follow_up_id);

        $schedule->update($input);

        $schedule->users()->sync($assigned_user);

        return $schedule;
	}

	public function getFollowUpForGivenDate($user, $start_date, $end_date = null)
	{
		$query = Schedule::with(['customer'])->where('business_id', $user->business_id);

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(start_datetime)'), [$start_date, $end_date]);
        } elseif (!empty($start_date)) {
        	$query->where(DB::raw('date(start_datetime)'), $start_date);
        }

        if (!$user->can('crm.access_all_schedule') && $user->can('crm.access_own_schedule')) {
            $query->where( function($qry) use ($user) {
                $qry->whereHas('users', function($q) use ($user){
                    $q->where('user_id', $user->id);
                })->orWhere('created_by', $user->id);
            });
        }

        return $query;
	}

    public function getLeadsListQuery($business_id)
    {
        $leads = CrmContact::with(['Source', 'lifeStage', 'leadUsers'])
                ->where('contacts.business_id', $business_id)
                ->where('contacts.type', 'lead')
                ->select('contacts.contact_id', 'name', 'supplier_business_name', 'email', 'mobile', 'tax_number', 'contacts.created_at', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'custom_field5', 'custom_field6', 'alternate_number', 'landline', 'dob', 'contact_status', 'type', 'custom_field7', 'custom_field8', 'custom_field9', 'custom_field10', 'contacts.id','contacts.business_id', 'crm_source', 'crm_life_stage', 'address_line_1', 'address_line_2', 'city', 'state', 'country', 'zip_code', DB::raw('(SELECT CS.id FROM crm_schedules AS CS WHERE CS.contact_id=contacts.id AND CS.start_datetime < "'. Carbon::today()->toDateTimeString(). '" ORDER BY CS.start_datetime DESC LIMIT 1) as last_follow_up_id'), DB::raw('(SELECT CS.id FROM crm_schedules AS CS WHERE CS.contact_id=contacts.id AND CS.start_datetime > "'. Carbon::today()->toDateTimeString().'" ORDER BY CS.start_datetime ASC LIMIT 1) as upcoming_follow_up_id'),
                    DB::raw('(SELECT CS.start_datetime FROM crm_schedules AS CS WHERE CS.contact_id=contacts.id AND CS.start_datetime < "'. Carbon::today()->toDateTimeString(). '" ORDER BY CS.start_datetime DESC LIMIT 1) as last_follow_up'),
                    DB::raw('(SELECT CS.start_datetime FROM crm_schedules AS CS WHERE CS.contact_id=contacts.id AND CS.start_datetime > "'. Carbon::today()->toDateTimeString().'" ORDER BY CS.start_datetime ASC LIMIT 1) as upcoming_follow_up'),
                    DB::raw('(SELECT CS.followup_additional_info FROM crm_schedules AS CS WHERE CS.contact_id=contacts.id AND CS.start_datetime < "'. Carbon::today()->toDateTimeString(). '" ORDER BY CS.start_datetime DESC LIMIT 1) as last_follow_up_additional_info'),
                    DB::raw('(SELECT CS.followup_additional_info FROM crm_schedules AS CS WHERE CS.contact_id=contacts.id AND CS.start_datetime > "'. Carbon::today()->toDateTimeString().'" ORDER BY CS.start_datetime ASC LIMIT 1) as upcoming_follow_up_additional_info'));

        return $leads;
    }

    public function creatContactPerson($input)
    {
        $input['password'] = Hash::make($input['password']);
        $input['user_type'] = 'user_customer';

        if (empty($input['allow_login'])) {
            $input['password'] = null;
            $input['username'] = null;
            $input['allow_login'] = 0;
        }
        
        // Create the user
        $user = User::create($input);
    }

    public function getAdvFollowupsTags()
    {
        return  [
            'invoice' => [
                '{invoice_numbers}', '{customer_business_name}', '{customer_name}'
            ],
            'trans_days' => [
                '{customer_business_name}', '{customer_name}', '{days}'
            ],
            'contact_name' => [
                '{customer_business_name}', '{customer_name}'
            ],
            'help_text' => __('lang_v1.available_tags')
        ];
    }

    public function replaceAdvFollowUpTags($contact_id, $invoices = [], $input)
    {   
        $contact = CrmContact::where('id', $contact_id)
                        ->first();

        $invoice_numbers  = '';
        if (!empty($invoices)) {
            $transactions = Transaction::find($invoices);
            $invoice_numbers  = implode(', ', $transactions->pluck('invoice_no')->toArray());
        }

        foreach ($input as $key => $value) {

            //replace invoice numbers
            if (strpos($value, '{invoice_numbers}') !== false) {
                $input[$key] = str_replace('{invoice_numbers}', $invoice_numbers, $input[$key]);
            }

            //replace customer biz name
            if (strpos($value, '{customer_business_name}') !== false) {
                $input[$key] = str_replace('{customer_business_name}', $contact->supplier_business_name, $input[$key]);
            }

            //replace customer name
            if (strpos($value, '{customer_name}') !== false) {
                $input[$key] = str_replace('{customer_name}', $contact->name, $input[$key]);
            }

            //replace days
            if (strpos($value, '{days}') !== false) {
                $input[$key] = str_replace('{days}', $input['in_days'], $input[$key]);
            }
        }

        return $input;
    }

    public function getCrmSettings($business_id)
    {
        $crm_settings = Business::where('id', $business_id)
                                ->value('crm_settings');

        $crm_settings = !empty($crm_settings) ? json_decode($crm_settings, true) : [];

        return $crm_settings;
    }
}