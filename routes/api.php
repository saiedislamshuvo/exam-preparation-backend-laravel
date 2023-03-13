<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MocktestController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ResultController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/mocktests', [MocktestController::class, 'public']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::resource('/account/mocktests', MocktestController::class)->except(['create', 'show', 'edit']);
    Route::resource('/account/questions', QuestionController::class)->except(['index', 'create', 'edit']);
    Route::resource('/account/results', ResultController::class)->except(['create', 'edit', 'update']);
});
