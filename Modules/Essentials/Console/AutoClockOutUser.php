<?php

namespace Modules\Essentials\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Essentials\Entities\EssentialsAttendance;
use Carbon\Carbon;

class AutoClockOutUser extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'pos:autoClockOutUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto clock out user for a given time';

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
        $attendances = EssentialsAttendance::join('essentials_shifts as es', 'essentials_attendances.essentials_shift_id', 'es.id')
            ->where('es.is_allowed_auto_clockout', 1)
            ->whereNull('essentials_attendances.clock_out_time')
            ->whereBetween('es.auto_clockout_time', [Carbon::now()->toTimeString(), Carbon::now()->addMinutes(30)->toTimeString()])
            ->update(['clock_out_time' => Carbon::now()->toDateTimeString()]);
    }
}
