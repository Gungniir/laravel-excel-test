<?php

use App\Http\Controllers\RowsController;
use App\Http\Controllers\PingController;
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

Route::middleware('auth.basic')->group(function () {
    Route::get('/ping', [PingController::class, 'ping']);

    Route::get('/excel', [RowsController::class, 'index']);
    Route::post('/excel', [RowsController::class, 'store']);
});
