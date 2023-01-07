<?php

Route::group(['middleware' => ['web', 'authh', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu'], 'prefix' => 'project', 'namespace' => 'Modules\Project\Http\Controllers'], function () {
    Route::put('project/{id}/post-status', 'ProjectController@postProjectStatus');
    Route::put('project-settings', 'ProjectController@postSettings');
    Route::resource('project', 'ProjectController');
    Route::resource('project-task', 'TaskController');
    Route::get('project-task-get-status', 'TaskController@getTaskStatus');
    Route::put('project-task/{id}/post-status', 'TaskController@postTaskStatus');
    Route::put('project-task/{id}/post-description', 'TaskController@postTaskDescription');
    Route::resource('project-task-comment', 'TaskCommentController');
    Route::post('post-media-dropzone-upload', 'TaskCommentController@postMedia');
    Route::resource('project-task-time-logs', 'ProjectTimeLogController');
    Route::resource('activities', 'ActivityController')->only(['index']);
    Route::get('project-invoice-tax-report', 'InvoiceController@getProjectInvoiceTaxReport');
    Route::resource('invoice', 'InvoiceController');
    Route::get('project-employee-timelog-reports', 'ReportController@getEmployeeTimeLogReport');
    Route::get('project-timelog-reports', 'ReportController@getProjectTimeLogReport');
    Route::get('project-reports', 'ReportController@index');

    Route::get('/install', 'InstallController@index');
    Route::post('/install', 'InstallController@install');
    Route::get('/install/uninstall', 'InstallController@uninstall');
    Route::get('/install/update', 'InstallController@update');
});
