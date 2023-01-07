<?php

namespace Modules\Connector\Http\Controllers;

use App\System;
use Composer\Semver\Comparator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallController extends Controller
{
    public function __construct()
    {
        $this->module_name = 'connector';
        $this->appVersion = config('connector.module_version');
    }

    /**
     * Install
     *
     * @return Response
     */
    public function index()
    {   
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        //clear cache & config file
        config(['app.debug' => true]);
        Artisan::call('config:clear');

        //Check if Connector is installed or not.
        $is_installed = System::getProperty($this->module_name . '_version');
        if (empty($is_installed)) {
            DB::statement('SET default_storage_engine=INNODB;');
            Artisan::call('module:migrate', ['module' => "Connector"]);
            Artisan::call('module:publish', ['module' => "Connector"]);
            System::addProperty($this->module_name . '_version', $this->appVersion);

            Artisan::call('passport:install --force');
            Artisan::call('apidoc:generate');
        }

        $output = ['success' => 1,
                    'msg' => 'Connector module installed succesfully'
                ];

        return redirect()
            ->action('\App\Http\Controllers\Install\ModulesController@index')
            ->with('status', $output);
    }

    /**
     * Uninstall
     * @return Response
     */
    public function uninstall()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            System::removeProperty($this->module_name . '_version');

            $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
        } catch (\Exception $e) {
            $output = ['success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * update module
     * @return Response
     */
    public function update()
    {
        //Check if connector_version is same as appVersion then 404
        //If appVersion > connector_version - run update script.
        //Else there is some problem.
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');

            $connector_version = System::getProperty($this->module_name . '_version');

            if (Comparator::greaterThan($this->appVersion, $connector_version)) {
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', '512M');
                
                //clear cache & config file
                config(['app.debug' => true]);
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
                
                DB::statement('SET default_storage_engine=INNODB;');
                Artisan::call('module:migrate', ['module' => "Connector"]);
                Artisan::call('module:publish', ['module' => "Connector"]);
                System::setProperty($this->module_name . '_version', $this->appVersion);

                Artisan::call('apidoc:generate');
            } else {
                abort(404);
            }

            DB::commit();
            
            $output = ['success' => 1,
                    'msg' => 'Connector module updated Succesfully to version ' . $this->appVersion . ' !!'
                ];

            return redirect()->back()->with(['status' => $output]);
        } catch (Exception $e) {
            DB::rollBack();
            die($e->getMessage());
        }
    }
}
