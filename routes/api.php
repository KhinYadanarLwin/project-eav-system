<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimesheetController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('projects', [ProjectController::class, 'index']);
    Route::post('projects/store', [ProjectController::class, 'store']);
    Route::get('projects/{project}/show', [ProjectController::class, 'show']);
    Route::put('projects/{project}/update', [ProjectController::class, 'update']);
    Route::delete('projects/delete/{project}', [ProjectController::class, 'destroy']);

    Route::get('timesheets', [TimesheetController::class, 'index']);
    Route::post('timesheets/store', [TimesheetController::class, 'store']);
    Route::get('timesheets/{timesheet}/show', [TimesheetController::class, 'show']);
    Route::put('timesheets/{timesheet}/update', [TimesheetController::class, 'update']);
    Route::delete('timesheets/delete/{timesheet}', [TimesheetController::class, 'destroy']);

    Route::get('attributes', [AttributeController::class, 'index']);
    Route::post('attributes/store', [AttributeController::class, 'store']);
    Route::get('attributes/{attribute}/show', [AttributeController::class, 'show']);
    Route::put('attributes/{attribute}/update', [AttributeController::class, 'update']);
    Route::delete('attributes/delete/{attribute}', [AttributeController::class, 'destroy']);
});
