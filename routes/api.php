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

Route::post('login', 'LoginController@login');
Route::post('register', 'RegisterController@register');

Route::get('suscriptions', 'SuscriptionController@index');

Route::get('/files/{shareableLink}', 'SharedFileController@index')
    ->where('shareableLink', '[A-Za-z0-9]+');

Route::post('/files/{shareableLink}/download', 'SharedFileController@download');

Route::group(['middleware' => ['jwt.auth']], function () {

    Route::get('logout', 'LoginController@logout');

    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user'    => \App\User::where('id', Auth::id())
                ->with('account')->first(),
        ]);
    });
    Route::get('/user/notifications', 'NotificationController@index');
    Route::patch('/user/notifications/{notification}', 'NotificationController@update');
    
    Route::get('users/{user}/files', 'FileUserController@index');
    Route::put('users/{user}', 'UserController@update');

    Route::group(['middleware' => ['user.ownership']], function () {
        Route::patch('users/{user}/files/{file}', 'FileUserController@update');
        Route::get('users/{user}/folders/{folder}', 'FileUserController@show');
        Route::get('users/{user}/folders/{folder}/folders', 'FileUserController@listFolders');
        Route::post('users/{user}/folders/{folder}/folders', 'FileUserController@createFolder');
        Route::post('users/{user}/folders/{folder}/archives', 'FileUserController@uploadArchive');
    });

    Route::group(['middleware' => ['user.many.ownership']], function () {
        Route::delete('users/{user}/files', 'FileUserController@delete');
        Route::post('users/{user}/files/download', 'FileUserController@download');
        Route::post('users/{user}/files/share', 'FileUserController@share');
    });

    Route::group(['middleware' => ['user.admin']], function () {
        Route::get('/users', 'UserController@index');
        Route::get('/metrics', 'StatisticsController@metrics');
        Route::delete('/users/{user}', 'UserController@destroy');
        Route::patch('/users/{user}/grant-admin-privileges', 'UserController@grantAdminPrivileges');
        Route::post('/suscriptions', 'SuscriptionController@create');
        Route::delete('/suscriptions/{suscription}', 'SuscriptionController@destroy');
    });
    
    Route::group(['middleware' => ['group.membership']], function () {
        Route::get('groups/{group}/files', 'FileGroupController@index');
        
        Route::group(['middleware' => ['group.ownership']], function () {
            Route::get('groups/{group}/folders/{folder}', 'FileGroupController@show');
            Route::post('groups/{group}/folders/{folder}/folders', 'FileGroupController@createFolder');
            Route::post('groups/{group}/folders/{folder}/archives', 'FileController@uploadArchive');
        });
        
        Route::group(['middleware' => ['group.many.ownership']], function () {
            Route::delete('groups/{group}/files', 'FileGroupController@delete');
            Route::post('groups/{group}/files/download', 'FileGroupController@download');
        });
    });
});
