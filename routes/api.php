<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\UserNewsFeedController;
use App\Http\Controllers\Api\V1\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login')->name('login');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword')->name('password.reset');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', 'logout');
    });
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    Route::controller(ArticleController::class)->prefix('articles')->group(function () {
        Route::get('/', 'index');
        Route::get('/search', 'search');
        Route::get('/{article}', 'show');
    });

    Route::prefix('users')->group(function () {
        Route::controller(UserPreferenceController::class)->group(function () {
            Route::post('/preferences', 'addPreference');
            Route::get('/preferences', 'getPreferences');
        });

        Route::controller(UserNewsFeedController::class)->group(function () {
            Route::get('/news-feed', 'getNewsFeed');
        });
    });

});
