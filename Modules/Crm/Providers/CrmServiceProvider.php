<?php

namespace Modules\Crm\Providers;

use App\Utils\ModuleUtil;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Utils\Util;
use Illuminate\Routing\Router;

class CrmServiceProvider extends ServiceProvider
{

    /**
     * The filters base class name.
     *
     * @var array
     */
    protected $middleware = [
        'Crm' => [
            'CheckContactLogin' => 'CheckContactLogin',
            'ContactSidebarMenu' => 'ContactSidebarMenu',
        ],
    ];

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
        $this->registerScheduleCommands();
        $this->registerMiddleware($this->app['router']);

        View::composer(
            ['crm::layouts.nav'],
            function ($view) {
                $commonUtil = new Util();
                $is_admin = $commonUtil->is_admin(auth()->user(), auth()->user()->business_id);
                $view->with('__is_admin', $is_admin);
            }
        );
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
     * Register the filters.
     *
     * @param  Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = "Modules\\{$module}\\Http\\Middleware\\{$middleware}";

                $router->aliasMiddleware($name, $class);
            }
        }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('crm.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php',
            'crm'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/crm');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/crm';
        }, \Config::get('view.paths')), [$sourcePath]), 'crm');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/crm');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'crm');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'crm');
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
            \Modules\Crm\Console\SendScheduleNotification::class,
            \Modules\Crm\Console\CreateRecursiveFollowup::class
        ]);
    }

    public function registerScheduleCommands()
    {
        $env = config('app.env');
        $module_util = new ModuleUtil();
        $is_installed = $module_util->isModuleInstalled(config('crm.name'));
        
        if ($env === 'live' && $is_installed) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('pos:sendScheduleNotification')->everyMinute();
                $schedule->command('pos:createRecursiveFollowup')->daily();
            });
        }
    }
}
