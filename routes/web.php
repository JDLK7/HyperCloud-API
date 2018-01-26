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

Route::get('/', function () {
    return File::get(public_path('index.html'));
});

Route::get('user/verify/{verification_code}', 'RegisterController@verifyUser');

/**
 * Redirección para las llamadas que ocasionan 
 * conflicto con el router de Angular;
 */
Route::get('{all}', function () {
    return File::get(public_path('index.html'));
})->where('all', '(.*)');

Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
Route::post('password/reset', 'Auth\PasswordController@reset');