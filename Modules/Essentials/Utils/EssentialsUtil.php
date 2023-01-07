<?php
namespace Modules\Essentials\Utils;

use App\Utils\Util;
use DB;
use Modules\Essentials\Entities\EssentialsAllowanceAndDeduction;
use Modules\Essentials\Entities\EssentialsAttendance;
use Modules\Essentials\Entities\EssentialsUserShift;
use Modules\Essentials\Entities\Shift;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Client;
use Modules\Essentials\Entities\EssentialsLeave;
use App\Transaction;

class EssentialsUtil extends Util
{
    /**
     * Function to calculate total work duration of a user for a period of time
     * @param  string $unit
     * @param  integer $user_id
     * @param  integer $business_id
     * @param  integer $start_date = null
     * @param  integer $end_date = null
     */


    public function getTotalWorkDuration(
        $unit,
        $user_id,
        $business_id,
        $start_date = null,
        $end_date = null
    ) {
        $total_work_duration = 0;
        if ($unit == 'hour') {
            $query = EssentialsAttendance::where('business_id', $business_id)
                                        ->where('user_id', $user_id)
                                        ->whereNotNull('clock_out_time');

            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('clock_in_time', '>=', $start_date)
                            ->whereDate('clock_in_time', '<=', $end_date);
            }

            $minutes_sum = $query->select(DB::raw('SUM(TIMESTAMPDIFF(MINUTE, clock_in_time, clock_out_time)) as total_minutes'))->first();
            $total_work_duration = !empty($minutes_sum->total_minutes) ? $minutes_sum->total_minutes / 60 : 0;
        }

        return number_format($total_work_duration, 2);
    }

    /**
     * Parses month and year from date
     * @param  string $month_year
     */
    public function getDateFromMonthYear($month_year)
    {
        $month_year_arr = explode('/', $month_year);
        $month = $month_year_arr[0];
        $year = $month_year_arr[1];

        $transaction_date = $year . '-' . $month . '-01';

        return $transaction_date;
    }

    /**
     * Retrieves all allowances and deductions of an employeee
     * @param  int $business_id
     * @param  int $user_id
     * @param  string $start_date = null
     * @param  string $end_date = null
     */
    public function getEmployeeAllowancesAndDeductions($business_id, $user_id, $start_date = null, $end_date = null)
    {
        $query = EssentialsAllowanceAndDeduction::join('essentials_user_allowance_and_deductions as euad', 'euad.allowance_deduction_id', '=', 'essentials_allowances_and_deductions.id')
                ->where('business_id', $business_id)
                ->where('euad.user_id', $user_id);

        //Filter if applicable one
        if (!empty($start_date) && !empty($end_date)) {
            $query->where(function ($q) use ($start_date, $end_date) {
                $q->whereNull('applicable_date')
                    ->orWhereBetween('applicable_date', [$start_date, $end_date]);
            });
        }
        $allowances_and_deductions = $query->get();

        return $allowances_and_deductions;
    }

    /**
     * Validates user clock in and returns available shift id
     */
    public function checkUserShift($user_id, $settings, $clock_in_time = null)
    {
        $shift_id = null;
        $shift_date = !empty($clock_in_time) ? \Carbon::parse($clock_in_time) : \Carbon::now();
        $shift_datetime = $shift_date->format('Y-m-d');
        $day_string = strtolower($shift_date->format('l'));
        $grace_before_checkin = !empty($settings['grace_before_checkin']) ? (int) $settings['grace_before_checkin'] : 0;
        $grace_after_checkin = !empty($settings['grace_after_checkin']) ? (int) $settings['grace_after_checkin'] : 0;
        $clock_in_start =  !empty($clock_in_time) ? \Carbon::parse($clock_in_time)->subMinutes($grace_before_checkin) : \Carbon::now()->subMinutes($grace_before_checkin);
        $clock_in_end = !empty($clock_in_time) ? \Carbon::parse($clock_in_time)->addMinutes($grace_after_checkin) : \Carbon::now()->addMinutes($grace_after_checkin);

        $user_shifts = EssentialsUserShift::join('essentials_shifts as s', 's.id', '=', 'essentials_user_shifts.essentials_shift_id')
                    ->where('user_id', $user_id)
                    ->where('start_date', '<=', $shift_datetime)
                    ->where(function ($q) use ($shift_datetime) {
                        $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', $shift_datetime);
                    })
                    ->select('essentials_user_shifts.*', 's.holidays', 's.start_time', 's.end_time', 's.type')
                    ->get();

        foreach ($user_shifts as $shift) {
            $holidays = json_decode($shift->holidays, true);
            //check if holiday
            if (is_array($holidays) && in_array($day_string, $holidays)) {
                continue;
            }

            //Check allocated shift time
            if ((!empty($shift->start_time) && \Carbon::parse($shift->start_time)->between($clock_in_start, $clock_in_end)) || $shift->type == 'flexible_shift') {
                return $shift->essentials_shift_id;
            }
        }

        return $shift_id;
    }

    /**
     * Validates user clock out
     */
    public function canClockOut($clock_in, $settings, $clock_out_time = null)
    {
        $shift = Shift::find($clock_in->essentials_shift_id);
        if (empty($shift->end_time)) {
            return true;
        }

        $grace_before_checkout = !empty($settings['grace_before_checkout']) ? (int) $settings['grace_before_checkout'] : 0;
        $grace_after_checkout = !empty($settings['grace_after_checkout']) ? (int) $settings['grace_after_checkout'] : 0;
        $clock_out_start =  empty($clock_out_time) ? \Carbon::now()->subMinutes($grace_before_checkout) : \Carbon::parse($clock_out_time)->subMinutes($grace_before_checkout);

        $clock_out_end = empty($clock_out_time) ? \Carbon::now()->addMinutes($grace_after_checkout) : \Carbon::parse($clock_out_time)->addMinutes($grace_after_checkout);

        if ((\Carbon::parse($shift->end_time)->between($clock_out_start, $clock_out_end)) || $shift->type == 'flexible_shift') {
            return true;
        } else {
            return false;
        }
    }

    public function clockin($data, $essentials_settings)
    {
        //Check user can clockin
        $clock_in_time = is_object($data['clock_in_time']) ? $data['clock_in_time']->toDateTimeString() : $data['clock_in_time'];

        $shift = $this->checkUserShift($data['user_id'], $essentials_settings, $clock_in_time);

        if (empty($shift)) {
            $available_shifts = $this->getAllAvailableShiftsForGivenUser($data['business_id'], $data['user_id']);

            $available_shifts_html = View::make('essentials::attendance.avail_shifts')
                                        ->with(compact('available_shifts'))
                                        ->render();

            $output = ['success' => false,
                    'msg' => __("essentials::lang.shift_not_allocated"),
                    'type' => 'clock_in',
                    'shift_details' => $available_shifts_html
                ];
            return $output;
        }

        $data['essentials_shift_id'] = $shift;

        //Check if already clocked in
        $count = EssentialsAttendance::where('business_id', $data['business_id'])
                                ->where('user_id', $data['user_id'])
                                ->whereNull('clock_out_time')
                                ->count();
        if ($count == 0) {
            EssentialsAttendance::create($data);

            $shift_info = Shift::getGivenShiftInfo($data['business_id'], $shift);
            $current_shift_html = View::make('essentials::attendance.current_shift')
                                    ->with(compact('shift_info'))
                                    ->render();

            $output = ['success' => true,
                    'msg' => __("essentials::lang.clock_in_success"),
                    'type' => 'clock_in',
                    'current_shift' => $current_shift_html
                ];
        } else {
            $output = ['success' => false,
                    'msg' => __("essentials::lang.already_clocked_in"),
                    'type' => 'clock_in'
                ];
        }

        return $output;
    }

    public function clockout($data, $essentials_settings)
    {

        //Get clock in
        $clock_in = EssentialsAttendance::where('business_id', $data['business_id'])
                                ->where('user_id', $data['user_id'])
                                ->whereNull('clock_out_time')
                                ->first();
        $clock_out_time = is_object($data['clock_out_time']) ? $data['clock_out_time']->toDateTimeString() : $data['clock_out_time'];

        if (!empty($clock_in)) {
            $can_clockout = $this->canClockOut($clock_in, $essentials_settings, $clock_out_time);
            if (!$can_clockout) {
                $output = ['success' => false,
                        'msg' => __("essentials::lang.shift_not_over"),
                        'type' => 'clock_out'
                    ];
                return $output;
            }

            $clock_in->clock_out_time = $data['clock_out_time'];
            $clock_in->clock_out_note = $data['clock_out_note'];
            $clock_in->clock_out_location = $data['clock_out_location'] ?? '';
            $clock_in->save();

            $output = ['success' => true,
                    'msg' => __("essentials::lang.clock_out_success"),
                    'type' => 'clock_out'
                ];
        } else {
            $output = ['success' => false,
                    'msg' => __("essentials::lang.not_clocked_in"),
                    'type' => 'clock_out'
                ];
        }

        return $output;
    }

    public function getAllAvailableShiftsForGivenUser($business_id, $user_id)
    {   
        $available_user_shifts = EssentialsUserShift::join('essentials_shifts as s', 's.id', '=', 
                                    'essentials_user_shifts.essentials_shift_id')
                                    ->where('user_id', $user_id)
                                    ->where('s.business_id', $business_id)
                                    ->whereDate('start_date', '<=', \Carbon::today())
                                    ->whereDate('end_date', '>=', \Carbon::today())
                                    ->select('essentials_user_shifts.start_date', 'essentials_user_shifts.end_date',
                                        's.name', 's.type', 's.start_time', 's.end_time', 's.holidays')
                                    ->get();

        return $available_user_shifts;
    }

    /**
     * get total leaves of and employee for given date
     *
     * @param  int $business_id
     * @param  int $employee_id
     * @param  string $start_date
     * @param  string $end_date
     */
    public function getTotalLeavesForGivenDateOfAnEmployee($business_id, $employee_id, $start_date, $end_date)
    {
        $leaves = EssentialsLeave::where('business_id', $business_id)
                        ->where('user_id', $employee_id)
                        ->whereDate('start_date', '>=', $start_date)
                        ->whereDate('end_date', '<=', $end_date)
                        ->get();

        $total_leaves = 0;
        foreach ($leaves as $key => $leave) {
            $start_date = \Carbon::parse($leave->start_date);
            $end_date = \Carbon::parse($leave->end_date);

            $diff = $start_date->diffInDays($end_date);
            $diff += 1;
            $total_leaves += $diff;
        }
        
        return $total_leaves;
    }

    public function getTotalDaysWorkedForGivenDateOfAnEmployee($business_id, $employee_id, $start_date, $end_date)
    {   
        $attendances = EssentialsAttendance::where('business_id', $business_id)
                        ->where('user_id', $employee_id)
                        ->whereNotNull('clock_out_time')
                        ->whereDate('clock_in_time', '>=', $start_date)
                        ->whereDate('clock_in_time', '<=', $end_date)
                        ->get()
                        ->groupBy(function($attendance, $key) {
                            return \Carbon::parse($attendance->clock_in_time)->format('Y-m-d');
                        });

        return count($attendances);
    }

    public function getPayrollQuery($business_id)
    {
        $payrolls = Transaction::where('transactions.business_id', $business_id)
                    ->where('type', 'payroll')
                    ->join('users as u', 'u.id', '=', 'transactions.expense_for')
                    ->leftJoin('categories as dept', 'u.essentials_department_id', '=', 'dept.id')
                    ->leftJoin('categories as dsgn', 'u.essentials_designation_id', '=', 'dsgn.id')
                    ->leftJoin('essentials_payroll_group_transactions as epgt', 'transactions.id', '=', 'epgt.transaction_id')
                    ->select([
                        'transactions.id',
                        DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                        'final_total',
                        'transaction_date',
                        'ref_no',
                        'transactions.payment_status',
                        'dept.name as department',
                        'dsgn.name as designation',
                        'epgt.payroll_group_id'
                    ]);

        return $payrolls;
    }

    public function getEssentialsSettings()
    {
        $settings = request()->session()->get('business.essentials_settings');
        $settings = !empty($settings) ? json_decode($settings, true) : [];

        return $settings;
    }
}
