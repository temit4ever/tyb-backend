<?php

use App\Http\Controllers\AuthController;
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

Route::prefix('v1')->group(function () {
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
    Route::apiResource('sprints', \App\Http\Controllers\SprintController::class);
    Route::apiResource('tasks', \App\Http\Controllers\TaskController::class);
    Route::apiResource('tasks.dates', \App\Http\Controllers\TaskDateController::class)->only([
        'index',
        'store',
    ]);
    Route::apiResource('task-types', \App\Http\Controllers\TaskTypeController::class);

  Route::apiResource('login', AuthController::class);
  Route::apiResource('logout', AuthController::class);
});
