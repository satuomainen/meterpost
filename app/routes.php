<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'DashboardController@showDashboard');

Route::resource('dataseries', 'DataseriesController');

Route::get('dataseries/{id}/reading/csv', 'ReadingController@getDataseriesAsCsv');
Route::get('dataseries/{id}/reading/averages', 'ReadingController@getDataseriesAverages');
Route::resource('dataseries.reading', 'ReadingController');

Route::post('series/{id}/add', 'LegacyController@storeReading');
