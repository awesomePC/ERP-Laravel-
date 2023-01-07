<?php
Route::get('/repair-status', 'Modules\Repair\Http\Controllers\CustomerRepairStatusController@index')->name('repair-status');
Route::post('/post-repair-status', 'Modules\Repair\Http\Controllers\CustomerRepairStatusController@postRepairStatus')->name('post-repair-status');
Route::group(['middleware' => ['web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu'], 'prefix' => 'repair', 'namespace' => 'Modules\Repair\Http\Controllers'], function () {
    Route::get('edit-repair/{id}/status', 'RepairController@editRepairStatus');
    Route::post('update-repair-status', 'RepairController@updateRepairStatus');
    Route::get('delete-media/{id}', 'RepairController@deleteMedia');
    Route::get('print-label/{id}', 'RepairController@printLabel');
    Route::get('print-repair/{transaction_id}/customer-copy', 'RepairController@printCustomerCopy')->name('repair.customerCopy');
    Route::resource('/repair', 'RepairController')->except(['create', 'edit']);
    Route::resource('/status', 'RepairStatusController', ['except' => ['show']]);
    
    Route::resource('/repair-settings', 'RepairSettingsController', ['only' => ['index', 'store']]);

    Route::get('/install', 'InstallController@index');
    Route::post('/install', 'InstallController@install');
    Route::get('/install/uninstall', 'InstallController@uninstall');
    Route::get('/install/update', 'InstallController@update');

    Route::get('get-device-models', 'DeviceModelController@getDeviceModels');
    Route::get('models-repair-checklist', 'DeviceModelController@getRepairChecklists');
    Route::resource('device-models', 'DeviceModelController')->except(['show']);
    Route::resource('dashboard', 'DashboardController');

    Route::post('job-sheet-post-upload-docs', 'JobSheetController@postUploadDocs');
    Route::get('job-sheet/{id}/upload-docs', 'JobSheetController@getUploadDocs');
    Route::get('job-sheet/print/{id}', 'JobSheetController@print');
    Route::get('job-sheet/delete/{id}/image', 'JobSheetController@deleteJobSheetImage');
    Route::get('job-sheet/{id}/status', 'JobSheetController@editStatus');
    Route::put('job-sheet-update/{id}/status', 'JobSheetController@updateStatus');
    Route::get('job-sheet/add-parts/{id}', 'JobSheetController@addParts');
    Route::post('job-sheet/save-parts/{id}', 'JobSheetController@saveParts');
    Route::post('job-sheet/get-part-row', 'JobSheetController@jobsheetPartRow');
    Route::resource('job-sheet', 'JobSheetController');
});
