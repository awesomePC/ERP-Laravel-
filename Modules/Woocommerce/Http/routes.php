<?php

Route::post(
    '/webhook/order-created/{business_id}',
    'Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderCreated'
);
Route::post(
    '/webhook/order-updated/{business_id}',
    'Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderUpdated'
);
Route::post(
    '/webhook/order-deleted/{business_id}',
    'Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderDeleted'
);
Route::post(
    '/webhook/order-restored/{business_id}',
    'Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderRestored'
);

Route::group(['middleware' => ['web', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu'], 'prefix' => 'woocommerce', 'namespace' => 'Modules\Woocommerce\Http\Controllers'], function () {
    Route::get('/install', 'InstallController@index');
    Route::get('/install/update', 'InstallController@update');
    Route::get('/install/uninstall', 'InstallController@uninstall');
    
    Route::get('/', 'WoocommerceController@index');
    Route::get('/api-settings', 'WoocommerceController@apiSettings');
    Route::post('/update-api-settings', 'WoocommerceController@updateSettings');
    Route::get('/sync-categories', 'WoocommerceController@syncCategories');
    Route::get('/sync-products', 'WoocommerceController@syncProducts');
    Route::get('/sync-log', 'WoocommerceController@getSyncLog');
    Route::get('/sync-orders', 'WoocommerceController@syncOrders');
    Route::post('/map-taxrates', 'WoocommerceController@mapTaxRates');
    Route::get('/view-sync-log', 'WoocommerceController@viewSyncLog');
    Route::get('/get-log-details/{id}', 'WoocommerceController@getLogDetails');
    Route::get('/reset-categories', 'WoocommerceController@resetCategories');
    Route::get('/reset-products', 'WoocommerceController@resetProducts');
});
