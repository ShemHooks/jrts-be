<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\DepartmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Authentication 
Route::controller(AuthController::class)->prefix("auth")->group(function () {
    Route::post('login', 'login');
});


// User Management
Route::middleware(['auth:sanctum', 'admin.only'])
    ->post('/register/user', [AuthController::class, 'register']);





