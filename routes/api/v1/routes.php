<?php

use App\Http\Controllers\Accounts\AuthController;
use App\Http\Controllers\Accounts\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    dd('smile');
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::post('/admin',[UserController::class,'createAdmin']);
Route::post('/worker',[UserController::class,'createWorker'])->middleware('auth:sanctum');
