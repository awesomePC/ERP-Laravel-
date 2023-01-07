<?php
Route::group(['middleware' => ['web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu'], 'prefix' => 'asset', 'namespace' => 'Modules\AssetManagement\Http\Controllers'], function () {

    Route::get('install', 'InstallController@index');
    Route::post('install', 'InstallController@install');
    Route::get('install/uninstall', 'InstallController@uninstall');
    Route::get('install/update', 'InstallController@update');
    
    Route::resource('assets', 'AssetController');
    Route::resource('allocation', 'AssetAllocationController');
    Route::resource('revocation', 'RevokeAllocatedAssetController');
    Route::resource('settings', 'AssetSettingsController');
    Route::get('dashboard', 'AssetController@dashboard');

    Route::resource('asset-maintenance', 'AssetMaitenanceController');
   
});
