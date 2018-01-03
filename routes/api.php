<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('recover', 'AuthController@recover');

Route::group(['middleware' => ['jwt.auth']], function () {

    Route::get('logout', 'AuthController@logout');
    
    Route::get('users/{user}/files', 'FileController@listUserFiles');

    Route::group(['middleware' => ['user.ownership']], function () {
        Route::get('users/{user}/folders/{folder}', 'FileController@listUserFolder');
    });
    
    Route::group(['middleware' => ['group.membership']], function () {
        Route::get('groups/{group}/files', 'FileController@listGroupFiles');
        
        Route::group(['middleware' => ['group.ownership']], function () {
            Route::get('groups/{group}/folders/{folder}', 'FileController@listGroupFolder');
        });
        
        Route::group(['middleware' => ['group.many.ownership']], function () {
            Route::post('groups/{group}/files/download', 'FileController@download');
        });
    });

    Route::group(['middleware' => ['user.many.ownership']], function () {
        Route::post('users/{user}/files/download', 'FileController@download');
    });
});
