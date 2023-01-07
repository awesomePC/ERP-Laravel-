<?php

namespace Modules\Essentials\Providers;

use App\Utils\ModuleUtil;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\View;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Modules\Essentials\Entities\EssentialsAttendance;

class EssentialsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        view::composer(['essentials::layouts.partials.header_part',
            'report.profit_loss'], function ($view) {
            $module_util = new ModuleUtil();

            if (auth()->user()->can('superadmin')) {
                $__is_essentials_enabled = $module_util->isModuleInstalled('Essentials');
            } else {
                $business_id = session()->get('user.business_id');
                $__is_essentials_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'essentials_module');
            }

            $view->with(compact('__is_essentials_enabled'));
        });

        view::composer(['essentials::layouts.partials.header_part'], function ($view) {

            $is_employee_allowed = false;
            $clock_in = null;

            $module_util = new ModuleUtil();
            if($module_util->isModuleInstalled('Essentials')){
                $business_id = session()->get('user.business_id');
                
                //Check if employee are allowed or not to enter own attendance.
                $is_employee_allowed = auth()->user()->can('essentials.allow_users_for_attendance_from_web');

                //Check if clocked in or not.
                $clock_in = EssentialsAttendance::where('essentials_attendances.business_id', $business_id)
                                ->leftjoin('essentials_shifts as es', 'es.id', '=', 'essentials_attendances.essentials_shift_id')
                                ->where('user_id', auth()->user()->id)
                                ->whereNull('clock_out_time')
                                ->select([
                                    'clock_in_time', 'es.name as shift_name', 'es.start_time', 'es.end_time'
                                ])
                                ->first();
            }

            $view->with(compact('is_employee_allowed', 'clock_in'));
        });

        view::composer(['essentials::attendance.clock_in_clock_out_modal',
            'essentials::attendance.create'], function ($view) {
            $util = new \App\Utils\Util();
            $ip_address = $util->getUserIpAddr();
            
            $settings = session()->get('business.essentials_settings');
            $settings = !empty($settings) ? json_decode($settings, true) : [];
            $is_location_required = !empty($settings['is_location_required']) ? true : false;

            $view->with(compact('ip_address', 'is_location_required'));
        });

        $this->registerScheduleCommands();
    }

    public function registerScheduleCommands()
    {
        $env = config('app.env');
        //schedule command for auto clock out user
        if ($env === 'live') {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('pos:autoClockOutUser')->everyThirtyMinutes();
            });
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('essentials.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php',
            'essentials'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/essentials');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/essentials';
        }, \Config::get('view.paths')), [$sourcePath]), 'essentials');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/essentials');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'essentials');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'essentials');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            \Modules\Essentials\Console\AutoClockOutUser::class
        ]);
    }
}
