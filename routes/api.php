<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\DepartmentController;
use App\Http\Controllers\api\UserManagement;
use App\Http\Controllers\api\DashboardController;
use App\Http\Controllers\api\ProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Authentication 
Route::controller(AuthController::class)->prefix("auth")->group(function () {
    Route::post('login', 'login');
    Route::post('/register', 'clientRegistration');

    Route::middleware(['auth:sanctum', 'admin.only'])
        ->post('/register/employee', 'employeeRegistration');
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

Route::controller(UserManagement::class)->prefix('user')->group(function () {

    Route::middleware(['auth:sanctum'])->get('retrieve', 'index');

    Route::middleware(['auth:sanctum', 'admin.only'])->post('archive/${id}', 'archiveAccount');

    Route::middleware(['auth:sanctum', 'admin.only'])->post('unarchive/${id}', 'unArchiveAccount');

    Route::middleware(['auth:sanctum', 'admin.only'])->delete('delete/${id}', 'deleteUserAccount');

});


Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
    Route::get('admin', 'admin');
});

Route::controller(ProfileController::class)->prefix('profile')->group(function () {
    Route::middleware(['auth:sanctum'])->get('retrieve', 'userProfile');
});

