<?php

Route::group(['middleware' => ['web', 'authh', 'SetSessionData', 'auth', 'language', 'timezone', 'ContactSidebarMenu', 'CheckContactLogin'], 'prefix' => 'contact', 'namespace' => 'Modules\Crm\Http\Controllers'], function () {
    Route::resource('contact-dashboard', 'DashboardController');
    Route::get('contact-profile', 'ManageProfileController@getProfile');
    Route::post('contact-password-update', 'ManageProfileController@updatePassword');
    Route::post('contact-profile-update', 'ManageProfileController@updateProfile');
    Route::get('contact-purchases', 'PurchaseController@getPurchaseList');
    Route::get('contact-sells', 'SellController@getSellList');
    Route::get('contact-ledger', 'LedgerController@index');
    Route::get('contact-get-ledger', 'LedgerController@getLedger');
    Route::resource('bookings', 'ContactBookingController');
    Route::resource('order-request', 'OrderRequestController');
    Route::get('products/list', '\App\Http\Controllers\ProductController@getProducts');
    Route::get('order-request/get_product_row/{variation_id}/{location_id}', 'OrderRequestController@getProductRow');
});

Route::group(['middleware' => ['web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'], 'namespace' => 'Modules\Crm\Http\Controllers', 'prefix' => 'crm'], function () {
    Route::get('all-contacts-login', 'ContactLoginController@allContactsLoginList');
    Route::resource('contact-login', 'ContactLoginController')->except(['show']);
    Route::resource('follow-ups', 'ScheduleController');
    Route::get('todays-follow-ups', 'ScheduleController@getTodaysSchedule');
    Route::get('lead-follow-ups', 'ScheduleController@getLeadSchedule');
    Route::get('get-invoices', 'ScheduleController@getInvoicesForFollowUp');
    Route::get('get-followup-groups', 'ScheduleController@getFollowUpGroups');

    Route::resource('follow-up-log', 'ScheduleLogController');
    
    Route::get('install', 'InstallController@index');
    Route::post('install', 'InstallController@install');
    Route::get('install/uninstall', 'InstallController@uninstall');
    Route::get('install/update', 'InstallController@update');

    Route::resource('leads', 'LeadController');
    Route::get('lead/{id}/convert', 'LeadController@convertToCustomer');
    Route::get('lead/{id}/post-life-stage', 'LeadController@postLifeStage');

    Route::get('{id}/send-campaign-notification', 'CampaignController@sendNotification');
    Route::resource('campaigns', 'CampaignController');
    Route::get('dashboard', 'CrmDashboardController@index');

    Route::get('reports', 'ReportController@index');
    Route::get('follow-ups-by-user', 'ReportController@followUpsByUser');
    Route::get('follow-ups-by-contact', 'ReportController@followUpsContact');
    Route::get('lead-to-customer-report', 'ReportController@leadToCustomerConversion');
    Route::get('lead-to-customer-details/{user_id}', 'ReportController@showLeadToCustomerConversionDetails');
    Route::get('call-log', 'CallLogController@index',['only' => ['index']]);
    Route::post('mass-delete-call-log', 'CallLogController@massDestroy');

    Route::get('edit-proposal-template', 'ProposalTemplateController@getEdit');
    Route::post('update-proposal-template', 'ProposalTemplateController@postEdit');
    Route::get('view-proposal-template', 'ProposalTemplateController@getView');
    Route::get('send-proposal', 'ProposalTemplateController@send');
    Route::delete('delete-proposal-media/{id}', 'ProposalTemplateController@deleteProposalMedia');
    Route::resource('proposal-template', 'ProposalTemplateController')->except(['show', 'edit', 'update', 'destroy']);
    Route::resource('proposals', 'ProposalController')->except(['create', 'edit', 'update', 'destroy']);
    Route::get('settings', 'CrmSettingsController@index');
    Route::post('update-settings', 'CrmSettingsController@updateSettings');
    Route::get('order-request', 'OrderRequestController@listOrderRequests');
});
