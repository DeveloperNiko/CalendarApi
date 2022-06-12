<?php

use App\Http\Controllers\Api\v1\CalendarController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('/v1/calendar')->name('triptypes.')->group(function () {
    Route::get('/', [CalendarController::class, 'show'])->name('show');
//    Route::get('/{id}', [CalendarController::class, 'details'])->name('show');
});
