<?php

Route::group(['middleware' => ['web', 'authh', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu'], 'prefix' => 'manufacturing', 'namespace' => 'Modules\Manufacturing\Http\Controllers'], function () {
    Route::get('/install', 'InstallController@index');
    Route::post('/install', 'InstallController@install');
    Route::get('/install/update', 'InstallController@update');
    Route::get('/install/uninstall', 'InstallController@uninstall');

    Route::get('/is-recipe-exist/{variation_id}', 'RecipeController@isRecipeExist');
    Route::get('/ingredient-group-form', 'RecipeController@getIngredientGroupForm');
    Route::get('/get-recipe-details', 'RecipeController@getRecipeDetails');
    Route::get('/get-ingredient-row/{variation_id}', 'RecipeController@getIngredientRow');
    Route::get('/add-ingredient', 'RecipeController@addIngredients');
    Route::resource('/recipe', 'RecipeController', ['except' => ['edit', 'update']]);
    Route::resource('/production', 'ProductionController');
    Route::resource('/settings', 'SettingsController', ['only' => ['index', 'store']]);

    Route::get('/report', 'ProductionController@getManufacturingReport');

    Route::post('/update-product-prices', 'RecipeController@updateRecipeProductPrices');
});
