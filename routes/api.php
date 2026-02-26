<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\DepartmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::controller(AuthController::class)->prefix("auth")->group(function () {
    Route::post('login', 'login');
});

// Authenticated User Only

Route::middleware(['auth.sanctum'])->group(function () {

    // Admin Only Routes
    Route::middleware('admin.only')->group(function () {
        Route::controller(AuthController::class)->prefix('register')->group(function () {
            Route::post('user', 'registerEmployees');
        });

        Route::controller(DepartmentController::class)->prefix('dept')->group(function () {
            Route::post('create', 'createDepartment');
        });


    });

});