<?php

namespace Modules\Crm\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Crm\Entities\Schedule;
use App\Transaction;
use Modules\Crm\Utils\CrmUtil;
use App\Contact;
use DB;

class CreateRecursiveFollowup extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'pos:createRecursiveFollowup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates recursive follow ups based on order history and payment status of the customers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $recursive_followups = Schedule::where('is_recursive', 1)
                                    ->with(['users', 'createdBy'])
                                    ->get();

        try {
            DB::beginTransaction();
            foreach ($recursive_followups as $rf) {
                if ($rf->follow_up_by == 'payment_status') {
                    $this->createRecursiveFollowupByPaymentStatus($rf);
                } elseif ($rf->follow_up_by == 'orders') {
                    $this->createRecursiveFollowupByPaymentOrders($rf);
                }
            }
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }
    }

    public function createRecursiveFollowupByPaymentOrders($recursive_followup)
    {
        $days = $recursive_followup->recursion_days;

        $current_date = \Carbon::now()->format('Y-m-d');
        $days_diff = $recursive_followup->recursion_days;

        $customers = Contact::where('contacts.business_id', $recursive_followup->business_id)
                        ->OnlyCustomers()
                        ->leftJoin('transactions as t', 't.contact_id', '=', 'contacts.id')
                        ->havingRaw("DATEDIFF('{$current_date}', MAX(DATE(transaction_date))) = {$days_diff}")
                        ->orHavingRaw("(transaction_date IS NULL AND DATEDIFF('{$current_date}', DATE(contacts.created_at)) =  {$days_diff})")
                        ->select('contacts.*', 't.transaction_date')
                        ->groupBy('contacts.id')
                        ->get();

        $input = [
            'follow_up_by' => 'orders',
            'title' => $recursive_followup->title,
            'status' => $recursive_followup->status,
            'start_datetime' => \Carbon::now()->format('Y-m-d H:i:s'),
            'description' => $recursive_followup->description,
            'schedule_type' => $recursive_followup->schedule_type,
            'allow_notification' => $recursive_followup->allow_notification,
            'notify_via' => $recursive_followup->notify_via,
            'notify_before' => $recursive_followup->notify_before,
            'notify_type' => $recursive_followup->notify_type
        ];

        $follow_ups = [];
        $users = $recursive_followup->users->pluck('id');
        foreach ($customers as $customer) {
            $follow_ups[$customer->id] = [
                'user_id' => $users
            ];
        }
        $input['follow_ups'] = $follow_ups;

        if (!empty($follow_ups)) {
            $crmUtil = new CrmUtil();

            $crmUtil->addAdvanceFollowUp($input, $recursive_followup->createdBy);
        }

    }

    private function createRecursiveFollowupByPaymentStatus($recursive_followup)
    {
        if (empty($recursive_followup->follow_up_by_value)) {
            return false;
        }

        $current_date = \Carbon::now()->format('Y-m-d');
        $days_diff = $recursive_followup->recursion_days;

        $query = Transaction::where('business_id', $recursive_followup->business_id)
                            ->where('type', 'sell')
                            ->where('status', 'final')
                            ->whereRaw("DATEDIFF('$current_date', DATE(transaction_date)) = $days_diff");

        if ($recursive_followup->follow_up_by_value == 'all') {
            $query->whereIn('payment_status', ['due', 'partial']);
        } elseif ($recursive_followup->follow_up_by_value == 'due') {
            $query->where('payment_status', 'due');
        } elseif ($recursive_followup->follow_up_by_value == 'partial') {
            $query->where('payment_status', 'partial');
        } elseif ($recursive_followup->follow_up_by_value == 'overdue') {
            $query->overDue();
        }

        $transactions = $query->get();

        $input = [
            'follow_up_by' => 'payment_status',
            'title' => $recursive_followup->title,
            'status' => $recursive_followup->status,
            'start_datetime' => \Carbon::now()->format('Y-m-d H:i:s'),
            'description' => $recursive_followup->description,
            'schedule_type' => $recursive_followup->schedule_type,
            'allow_notification' => $recursive_followup->allow_notification,
            'notify_via' => $recursive_followup->notify_via,
            'notify_before' => $recursive_followup->notify_before,
            'notify_type' => $recursive_followup->notify_type
        ];

        $follow_ups = [];
        $users = $recursive_followup->users->pluck('id');
        foreach ($transactions as $transaction) {
            $follow_ups[$transaction->contact_id]['invoices'][] = $transaction->id;
        }
        foreach ($follow_ups as $key => $value) {
            $follow_ups[$key]['user_id'] = $users;
        }

        $input['follow_ups'] = $follow_ups;

        if (!empty($follow_ups)) {
            $crmUtil = new CrmUtil();

            $crmUtil->addAdvanceFollowUp($input, $recursive_followup->createdBy);
        }
    }
}
