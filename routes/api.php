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

Route::group(['middleware' => 'apikey'], function () {

    Route::group(['prefix' => 'v1'], function () {

        Route::post('/register', [App\Http\Controllers\API\RegisterController::class, 'index']);

        Route::post('/login', [App\Http\Controllers\API\LoginController::class, 'index']);

        Route::group(['middleware' => 'auth:api'], function () {

            Route::resource('loans', App\Http\Controllers\API\LoanController::class);

            Route::post('/re-payment', [App\Http\Controllers\API\RepaymentController::class, 'index']);

            Route::get('/logout', [App\Http\Controllers\API\LogoutController::class, 'index']);
        });
    });
});
