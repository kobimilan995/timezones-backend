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
Route::middleware('jwt.guest')->group(function () {
    Route::post('/register', 'JwtAuth\AuthController@register');
    Route::post('/login', 'JwtAuth\AuthController@login');
});

Route::middleware('jwt.auth')->group(function() {
    Route::delete('/logout', 'JwtAuth\AuthController@logout');
    Route::get('/test', function() {
        return 'TEST';
    });
});

Route::prefix('admin')->middleware('jwt.auth', 'jwt.admin')->group(function() {
    Route::get('/users', 'api\UsersController@index');
    Route::post('/users', 'api\UsersController@store');
    Route::get('/users/{user_id}', 'api\UsersController@show');
    Route::put('/users/{user_id}', 'api\UsersController@update');
    Route::delete('/users/{user_id}', 'api\UsersController@destroy');
});

Route::prefix('time_zones')->middleware('jwt.auth')->group(function() {
    Route::post('/', 'api\TimeZonesController@store');
    Route::get('/', 'api\TimeZonesController@index');
});


Route::get('/gmdate', function() {
    date_default_timezone_set('UTC');
    return [
        'gmdate' => gmdate('m/d/Y, H:i:s')
    ];
});
