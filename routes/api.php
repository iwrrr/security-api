<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SatkerController;
use App\Http\Controllers\API\ActivityController;

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

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::controller(SatkerController::class)->group(function () {
    Route::post('/satker', 'store');
    Route::get('/satker', 'index');
    Route::get('/satker/{id}', 'detail');
    Route::post('/satker/{id}', 'update');
    Route::delete('/satker/{id}', 'destroy');
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'getUser');
        Route::post('/user/change-password', 'changePassword');
        Route::post('/user/forgot-password', 'forgotPassword');
    });

    Route::controller(ActivityController::class)->group(function () {
        Route::post('/activity/comment', 'comment');
        Route::post('/activity/like', 'like');
        Route::post('/activity/store', 'store');
        Route::get('/activity', 'index');
        Route::get('/activity/{id}', 'detail');
        Route::post('/activity/{id}', 'update');
        Route::delete('/activity/{id}', 'destroy');
    });

    Route::controller(NewsController::class)->group(function () {
        Route::post('/news/store', 'store');
        Route::get('/news', 'index');
        Route::get('/news/{id}', 'detail');
        Route::post('/news/{id}', 'update');
        Route::delete('/news/{id}', 'destroy');
    });
});

require __DIR__ . '/auth.php';
