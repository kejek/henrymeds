<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ScheduleController;
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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [LoginController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [LoginController::class, 'logout']);
      Route::get('user', [LoginController::class, 'user']);
    });
});


//Public Schedule Routes to see what is available
Route::get('/schedules/{id}', [ScheduleController::class, 'index']);

//Private Schedule Routes to add/update schedule
Route::middleware('auth:sanctum')->group(function(){
    //schedules
    Route::get('/schedules/{id}', [ScheduleController::class, 'index']);
    Route::post('/provider/schedule/add', [ScheduleController::class, 'addSchedule']);

    //reservations
    Route::get('/reservations/available/{id}', [ReservationController::class, 'index']);
    Route::post('/reservations/add/{id}', [ReservationController::class, 'addReservation']);
});
