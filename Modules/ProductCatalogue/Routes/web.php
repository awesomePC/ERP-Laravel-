<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => '\Modules\ProductCatalogue\Http\Controllers'], function () {
	Route::get('/catalogue/{business_id}/{location_id}', 'ProductCatalogueController@index');
	Route::get('/show-catalogue/{business_id}/{product_id}', 'ProductCatalogueController@show');
});

Route::group(['middleware' => ['web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu'], 'namespace' => '\Modules\ProductCatalogue\Http\Controllers', 'prefix' => 'product-catalogue'], function () {
    Route::get('catalogue-qr', 'ProductCatalogueController@generateQr');

    Route::get('install', 'InstallController@index');
    Route::post('install', 'InstallController@install');
    Route::get('install/uninstall', 'InstallController@uninstall');
    Route::get('install/update', 'InstallController@update');
});
