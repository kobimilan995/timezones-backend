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

Route::middleware('jwt.auth', 'jwt.admin')->group(function() {
    Route::get('/admin/test', function() {
        return 'TEST';
    });
});


Route::get('/gmdate', function() {
    date_default_timezone_set('UTC');
    return [
        'gmdate' => gmdate('m/d/Y, H:i:s')
    ];
});
