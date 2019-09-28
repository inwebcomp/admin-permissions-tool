<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::group(['prefix' => 'tool/permissions-tool'], function () {
    Route::get('', 'PermissionsController@index');
    Route::get('roles', 'PermissionsController@roles');
    Route::get('user/roles', 'PermissionsController@userRoles');
    Route::post('roles', 'PermissionsController@storeRole');
    Route::delete('roles/{role}', 'PermissionsController@removeRole');
    Route::post('roles/{resource}/{resourceId}', 'PermissionsController@assignRoleToUser');
    Route::delete('roles/{resource}/{resourceId}', 'PermissionsController@removeRoleFromUser');
    Route::post('{role}/all', 'PermissionsController@updateAllForRole');
    Route::post('{role}/{resource}/all', 'PermissionsController@updateAll');
    Route::post('{role}/{resource}/{action}', 'PermissionsController@update');
});