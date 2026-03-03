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

    Route::middleware(['auth:sanctum', 'admin.only'])
        ->post('/register/user', 'register');
});


// Department Management
Route::controller(DepartmentController::class)->prefix("department")->group(function () {

    Route::middleware(['auth:sanctum', 'admin.only'])->get('/retrieve', 'index');

    Route::middleware(['auth:sanctum', 'admin.only'])->post('/create', 'createDepartment');

    Route::middleware(['auth:sanctum', 'admin.only'])->post('/create/sub', 'createSubDepartment');

    Route::middleware(['auth:sanctum', 'admin.only'])->post('archive/${id}', 'archiveDepartment');

    Route::middleware(['auth:sanctum', 'admin.only'])->post('unarchive/${id}', 'unarchiveDepartment');

    Route::middleware(['auth:sanctum', 'admin.only'])->delete('deleteDepartment/${id}', 'deleteDepartment');


});





