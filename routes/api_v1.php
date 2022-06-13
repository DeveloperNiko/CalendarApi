<?php

use App\Http\Controllers\Api\v1\CalendarController;
use App\Http\Middleware\AuthenticateAPI;
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

Route::prefix('/calendar')->middleware(AuthenticateAPI::class)->name('calendar')->group(function () {
    Route::get('/', [CalendarController::class, 'list'])->name('list');
    Route::post('/', [CalendarController::class, 'create'])->name('create');
    Route::patch('/{id}', [CalendarController::class, 'update'])->name('update');
    Route::delete('/{id}', [CalendarController::class, 'destroy'])->name('destroy');
    Route::post('/create_token', [CalendarController::class, 'createToken'])->name('createToken');
});
