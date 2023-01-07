<?php

namespace Modules\Connector\Http\Controllers\Api\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Connector\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\DB;
use App\Contact;
use App\User;

/**
 * @group CRM
 * @authenticated
 *
 * APIs for managing follow up
 */
class CallLogsController extends ApiController
{   
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
    * Save Call Logs
    *
    * @bodyParam call_logs.*.mobile_number string required Mobile number of the customer or user
    * @bodyParam call_logs.*.mobile_name string Name of the contact saved in the mobile
    * @bodyParam call_logs.*.call_type string Call type (call, sms) Example:call
    * @bodyParam call_logs.*.start_time string Start datetime of the call in "Y-m-d H:i:s" format
    * @bodyParam call_logs.*.end_time string End datetime of the call in "Y-m-d H:i:s" format
    * @bodyParam call_logs.*.duration string Duration of the call in seconds
    */
    public function saveCallLogs(Request $request)
    {
        if (!$this->moduleUtil->isModuleInstalled('Crm')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $call_logs = $request->input('call_logs');

            $user = Auth::user();

            $business_id = $user->business_id;
            $call_log_data = [];

            $now = \Carbon::now()->toDateTimeString();
            foreach ($call_logs as $call_log) {

                $number = $call_log['mobile_number'];

                $number_details = $this->getNumberDetails($number);

                $number_without_country_code = $number_details['national_number'];
                $contact = $this->searchContact($business_id, $number_without_country_code);

                $data = [
                    'business_id' => $business_id,
                    'created_by' => $user->id,
                    'mobile_number' => $number,
                    'call_type' => $call_log['call_type'] ?? null,
                    'duration' => $call_log['duration']  ?? null,
                    'mobile_name' => $call_log['mobile_name']  ?? null,
                    'contact_id' => !empty($contact) ? $contact->id : null,
                    'user_id' => null,
                    "created_at" =>  $now,
                    "updated_at" => $now
                ];

                //If contact not found search in users table
                if (empty($contact)) {
                    $contact_user = $this->searchUser($business_id, $number_without_country_code);

                    if (!empty($contact_user)) {
                        $data['user_id'] = $contact_user->id;
                        if (!empty($contact_user->crm_contact_id)) {
                            $data['contact_id'] = $contact_user->crm_contact_id;
                        }
                    }
                }

                $start_time = !empty($call_log['start_time']) ? \Carbon::parse($call_log['start_time']) : null;
                $end_time = !empty($call_log['end_time']) ? \Carbon::parse($call_log['end_time']) : null;

                if (empty($start_time) && !empty($end_time)) {
                    $start_time = $end_time->subSeconds($data['duration']);
                }

                if (empty($end_time) && !empty($start_time)) {
                    $end_time = $start_time->addSeconds($data['duration']);
                }

                if (empty($data['duration']) && !empty($start_time) && !empty($end_time)) {
                    $data['duration'] = $start_time->diffInSeconds($end_time);
                }
                $data['start_time'] = $start_time->toDateTimeString();
                $data['end_time'] = $end_time->toDateTimeString();

                $is_call_log_exist = \Modules\Crm\Entities\CrmCallLog::where('call_type', $data['call_type'])
                                        ->where('mobile_number', $data['mobile_number'])
                                        ->where('start_time', $data['start_time'])
                                        ->exists();

                if (!$is_call_log_exist) {
                    $call_log_data[] = $data;
                }
            }

            DB::beginTransaction(); 

            if (!empty($call_log_data)) {
                \Modules\Crm\Entities\CrmCallLog::insert($call_log_data);
            }

            DB::commit();

            $output['success'] = true;

            return $output;

        } catch (\Exception $e) {
            DB::rollback();
            return  $this->otherExceptions($e);
        }
    }

    public function searchUser($business_id, $number)
    {
        $users = User::where('business_id', $business_id)
                    ->where( function($q) use($number) {
                        $q->where('contact_number', 'like', "%{$number}")
                            ->orWhere('alt_number', 'like', "%{$number}")
                            ->orWhere('family_number', 'like', "%{$number}");
                    })
                    ->get();

        //get user with exact match
        $matched_user = null;
        foreach ($users as $user) {
            $contact_number_details = $this->getNumberDetails($user->contact_number);
            if ($contact_number_details['national_number'] == $number) {
                $matched_user = $user;
                break;
            }

            $alt_number_details = $this->getNumberDetails($user->alt_number);
            if ($alt_number_details['national_number'] == $number) {
                $matched_user = $user;
                break;
            }

            $family_number_number_details = $this->getNumberDetails($user->family_number);
            if ($family_number_number_details['national_number'] == $number) {
                $matched_user = $user;
                break;
            }
        }

        return $matched_user;
    }

    private function searchContact($business_id, $number)
    {
        //get contacts with matches
        $contacts = Contact::where('business_id', $business_id)
                                ->where( function($q) use($number) {
                                    $q->where('mobile', 'like', "%{$number}")
                                        ->orWhere('landline', 'like', "%{$number}")
                                        ->orWhere('alternate_number', 'like', "%{$number}");
                                })
                                ->get();

        //get contact with exact match
        $matched_contact = null;
        foreach ($contacts as $contact) {
            $mobile_details = $this->getNumberDetails($contact->mobile);
            if ($mobile_details['national_number'] == $number) {
                $matched_contact = $contact;
                break;
            }

            $landline_details = $this->getNumberDetails($contact->landline);
            if ($landline_details['national_number'] == $number) {
                $matched_contact = $contact;
                break;
            }

            $alternate_number_details = $this->getNumberDetails($contact->alternate_number);
            if ($alternate_number_details['national_number'] == $number) {
                $matched_contact = $contact;
                break;
            }
        }

        return $matched_contact;
    }

    private function getNumberDetails($number)
    {
        $first_character = substr($number,0, 1);

        $number_details = [
            'national_number' => $number,
            'country_code' => ''
        ];

        //check if number starts with 0
        if ($first_character === '0') {

            $number_details['national_number'] = ltrim($number, '0');

        } elseif ($first_character === '+') { //check if number starts with 0
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $number_obj = $phoneUtil->parse($number);
            $number_details['national_number'] = $number_obj->getNationalNumber();
            $number_details['country_code'] = $number_obj->getCountryCode();
        }

        return $number_details;
    }
}