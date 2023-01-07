<?php

namespace Modules\AssetManagement\Providers;

use App\Utils\ModuleUtil;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AssetManagementServiceProvider extends ServiceProvider
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

        //TODO:Remove sidebar
        view::composer(['assetmanagement::layouts.nav'
            ], function ($view) {
                if (auth()->user()->can('superadmin')) {
                    $__is_asset_enabled = true;
                } else {
                    $business_id = session()->get('user.business_id');
                    $module_util = new ModuleUtil();
                    $__is_asset_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'assetmanagement_module');
                }

                $view->with(compact('__is_asset_enabled'));
            });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('assetmanagement.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php',
            'assetmanagement'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/assetmanagement');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/assetmanagement';
        }, \Config::get('view.paths')), [$sourcePath]), 'assetmanagement');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/assetmanagement');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'assetmanagement');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'assetmanagement');
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
}
