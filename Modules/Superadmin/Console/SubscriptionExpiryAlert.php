<?php

namespace Modules\Superadmin\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\System;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Notifications\SendSubscriptionExpiryAlert;

class SubscriptionExpiryAlert extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'pos:sendSubscriptionExpiryAlert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends package expiry alerts to all the subscribers before a specified time';

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
        $min_alert_days = System::where('key', 'package_expiry_alert_days')->value('value');

        //Get all subscription which will expire in $min_alert_days
        $now = \Carbon::today()->toDateString();
        $expiring_subscriptions = Subscription::approved()
                                ->with(['business', 'business.owner'])
                                ->whereRaw("DATEDIFF(end_date,'$now') = $min_alert_days")
                                ->whereDate('start_date', '<=', $now)
                                ->get();

        $next_alert_days = $min_alert_days + 1;
        foreach ($expiring_subscriptions as $subscription) {

            //Check if next subscription available
            $next_sub_start_date = \Carbon::today()->addDays($next_alert_days)->toDateString();
            $next_subscription = Subscription::where('business_id', $subscription->business_id)
                                    ->whereDate('start_date', '=', $next_sub_start_date)
                                    ->approved()
                                    ->first();
            //If next subscription is empty send alert to business owner
            if (empty($next_subscription)) {
                $subscription->business->owner->notify(new SendSubscriptionExpiryAlert($subscription));
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    // protected function getArguments()
    // {
    //     return [
    //         ['example', InputArgument::REQUIRED, 'An example argument.'],
    //     ];
    // }

    /**
     * Get the console command options.
     *
     * @return array
     */
    // protected function getOptions()
    // {
    //     return [
    //         ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
    //     ];
    // }
}
