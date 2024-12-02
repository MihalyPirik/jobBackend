<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/check-email/{email}', [AuthController::class, 'checkEmail']);
  Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/users', [UserController::class, 'index']);
  Route::get('/user', [UserController::class, 'showUser']);
  Route::put('/user', [UserController::class, 'update']);
  Route::delete('/user', [UserController::class, 'destroy']);
});
