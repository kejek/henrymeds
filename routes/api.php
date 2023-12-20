<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ScheduleController;
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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [LoginController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('logout', [LoginController::class, 'logout']);
        Route::get('user', [LoginController::class, 'user']);
    });
});

//Private Schedule Routes to add/update schedule
Route::middleware('auth:sanctum')->group(function () {
    //schedules
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/schedules/{uuid}', [ScheduleController::class, 'show']);
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::put('/schedules/{uuid}', [ScheduleController::class, 'update']);
    Route::delete('/schedules/{uuid}', [ScheduleController::class, 'destroy']);

    //reservations
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/{uuid}', [ReservationController::class, 'show']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{uuid}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{uuid}', [ReservationController::class, 'destroy']);
});
