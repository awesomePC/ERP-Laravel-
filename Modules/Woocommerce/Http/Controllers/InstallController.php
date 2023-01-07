<?php

namespace Modules\Woocommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Composer\Semver\Comparator;

use App\System;

class InstallController extends Controller
{
    public function __construct()
    {
        $this->module_name = 'woocommerce';
        $this->appVersion = config('woocommerce.module_version');
    }

    /**
     * Install
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->installSettings();
        
        //Check if installed or not.
        $is_installed = System::getProperty($this->module_name . '_version');
        if (empty($is_installed)) {
            DB::statement('SET default_storage_engine=INNODB;');
            Artisan::call('module:migrate', ['module' => "Woocommerce"]);
            System::addProperty($this->module_name . '_version', $this->appVersion);
        }

        $output = ['success' => 1,
                    'msg' => 'Woocommerce module installed succesfully'
                ];
        return redirect()
            ->action('HomeController@index')
            ->with('status', $output);
    }

    /**
     * Initialize all install functions
     *
     */
    private function installSettings()
    {
        config(['app.debug' => true]);
        Artisan::call('config:clear');
    }

    //Updating
    public function update()
    {
        //Check if woocommerce_version is same as appVersion then 404
        //If appVersion > woocommerce_version - run update script.
        //Else there is some problem.
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');
            
            $woocommerce_version = System::getProperty($this->module_name . '_version');
            
            if (Comparator::greaterThan($this->appVersion, $woocommerce_version)) {
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', '512M');
                $this->installSettings();
                
                DB::statement('SET default_storage_engine=INNODB;');
                Artisan::call('module:migrate', ['module' => "Woocommerce"]);

                System::setProperty($this->module_name . '_version', $this->appVersion);
            } else {
                abort(404);
            }

            DB::commit();
            
            $output = ['success' => 1,
                        'msg' => 'Woocommerce module updated Succesfully to version ' . $this->appVersion . ' !!'
                    ];
            return redirect()
                ->action('HomeController@index')
                ->with('status', $output);
        } catch (Exception $e) {
            DB::rollBack();
            die($e->getMessage());
        }
    }

    /**
     * Uninstall
     * @return Response
     */
    public function uninstall(){
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
}
