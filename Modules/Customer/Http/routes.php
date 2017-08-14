<?php

Route::group(['middleware' => 'web', 'prefix' => 'customer', 'namespace' => 'Modules\Customer\Http\Controllers'], function()
{
    Route::get('/', 'CustomerController@index');
    Route::resource('/manage-customer', 'CustomerController');
    /* Check duplicate email route */
    Route::get('check-email/{id?}', 'CustomerController@checkEmail');
});
