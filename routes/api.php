<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController
    ;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router){
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/emailverification', [AuthController::class, 'emailverification'])->name('emailverification');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/resendVerificationCode', [AuthController::class, 'resendVerificationCode'])->name('resendVerificationCode');
    Route::get('/refresh_token', [AuthController::class, 'refresh_token'])->name('refresh_token');
    Route::post('/passwordResetVerificationCode', [AuthController::class, 'passwordResetVerificationCode'])->name('passwordResetVerificationCode');
    Route::post('/resendPassword', [AuthController::class, 'resendPassword'])->name('resendPassword');
});

