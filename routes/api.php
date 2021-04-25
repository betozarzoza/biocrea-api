<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::get('confirm/{confirm_token}', 'App\Http\Controllers\AuthController@confirmEmail');
    Route::get('testconfirm/{confirm_token}', 'App\Http\Controllers\AuthController@testConfirmMail');
    Route::post('signup', 'App\Http\Controllers\AuthController@signup');
    Route::post('login/facebook', 'App\Http\Controllers\AuthController@facebookLogin');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'App\Http\Controllers\AuthController@logout');
        Route::post('user', 'App\Http\Controllers\AuthController@user');
    });
});

Route::group([
  'prefix' => 'courses'
], function() {
    Route::get('all', 'App\Http\Controllers\CourseController@index');
    Route::get('list', 'App\Http\Controllers\CourseController@list');
    Route::post('search', 'App\Http\Controllers\CourseController@search');
    //  Route::post('mycourses', 'App\Http\Controllers\CourseController@myModules');
});

Route::group([
    'prefix' => 'user'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('courses', 'App\Http\Controllers\CourseController@myModules');
    });
});

Route::group([
    'prefix' => 'module'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('get', 'App\Http\Controllers\CourseController@module');
    });
});

Route::group([
  'prefix' => 'imageresizer',
], function () {
  Route::get('', 'App\Http\Controllers\ImageResizerController@index');
});

Route::group([
  'prefix' => 'reset'
], function() {
    Route::post('token', 'App\Http\Controllers\AuthController@createResetPasswordToken');
    Route::post('password', 'App\Http\Controllers\AuthController@resetPassword');
});

Route::group([
  'prefix' => 'password'
], function() {
    Route::post('email', 'App\Http\Controllers\ForgotPasswordController@sendResetLinkEmail');
    Route::post('reset', 'App\Http\Controllers\ForgotPasswordController@passwordReset');
});


Route::group([
    'prefix' => 'conekta'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('user', 'App\Http\Controllers\ConektaController@getConektaUser');
        Route::post('add/card', 'App\Http\Controllers\ConektaController@addCard');
        Route::post('checkout', 'App\Http\Controllers\ConektaController@checkout');
        Route::post('remove/card', 'App\Http\Controllers\ConektaController@deleteCard');
    });
});
