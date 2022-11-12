<?php

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\UserController;
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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'getUser');
        Route::post('/user/change-password', 'changePassword');
    });

    Route::controller(ArticleController::class)->group(function () {
        Route::post('/article/store', 'store');
        Route::get('/article', 'getArticles');
        Route::get('/article/headline', 'getHeadlineArticles');
        Route::get('/article/{id}', 'detail');
        Route::post('/article/{id}/update', 'update');
        Route::delete('/article/{id}/delete', 'destroy');
    });
});

require __DIR__.'/auth.php';
