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
    
    Route::get('users/{user}/files', 'FileUserController@index');

    Route::group(['middleware' => ['user.ownership']], function () {
        Route::get('users/{user}/folders/{folder}', 'FileUserController@show');
        Route::get('users/{user}/folders/{folder}/folders', 'FileUserController@listFolders');
        Route::post('users/{user}/folders/{folder}/folders', 'FileUserController@createFolder');
        Route::post('users/{user}/folders/{folder}/archives', 'FileUserController@uploadArchive');
    });

    Route::group(['middleware' => ['user.many.ownership']], function () {
        Route::post('users/{user}/files/download', 'FileUserController@download');
    });
    
    Route::group(['middleware' => ['group.membership']], function () {
        Route::get('groups/{group}/files', 'FileGroupController@index');
        
        Route::group(['middleware' => ['group.ownership']], function () {
            Route::get('groups/{group}/folders/{folder}', 'FileGroupController@show');
            Route::post('groups/{group}/folders/{folder}/folders', 'FileGroupController@createFolder');
            // Route::post('groups/{group}/folders/{folder}/archives', 'FileController@uploadArchive');
        });
        
        Route::group(['middleware' => ['group.many.ownership']], function () {
            Route::post('groups/{group}/files/download', 'FileGroupController@download');
        });
    });
});
