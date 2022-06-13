<?php

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

Route::prefix('auth')->name('auth.')->group(function() {
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'authenticate'])->name('login');
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware(['auth:sanctum']);
});

Route::prefix('trip')->name('trip.')->middleware('auth:sanctum')->group(function() {
    Route::get('/list', [App\Http\Controllers\TripController::class, 'list'])->name('list');
    Route::get('/get/{id}', [App\Http\Controllers\TripController::class, 'get'])->name('get');
    Route::post('/create', [App\Http\Controllers\TripController::class, 'create'])->name('create');
    Route::put('/update/{id}', [App\Http\Controllers\TripController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\TripController::class, 'delete'])->name('delete');
});
