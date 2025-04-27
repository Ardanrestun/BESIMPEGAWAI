<?php

use App\Http\Controllers\Access\MenuController;
use App\Http\Controllers\Access\RoleController;
use App\Http\Controllers\Access\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Console\TestController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeeTaskController;
use App\Http\Controllers\Employee\TaskController;
use App\Models\Employee\EmployeeTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





Route::post('login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {

    Route::get('getIdEmployee', [EmployeeTaskController::class, 'getId']);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('role', RoleController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('menu', MenuController::class);
    Route::get('menurole', [AuthController::class, 'menuRole']);
    Route::get('useremployees', [UserController::class, 'getEmployees']);
    Route::apiResource('employee', EmployeeController::class);
    Route::apiResource('task', TaskController::class);
    Route::get('employee-task/{taskId}', [EmployeeTaskController::class, 'listByTask']);
    Route::post('employee-task', [EmployeeTaskController::class, 'assignEmployee']);
    Route::delete('employee-task/{id}', [EmployeeTaskController::class, 'removeEmployee']);
    Route::get('my-tasks', [EmployeeTaskController::class, 'myTasks']);
    Route::put('my-tasks/{id}', [EmployeeTaskController::class, 'updateMyTask']);
});

Route::get('/test', [TestController::class, 'batchTest']);
