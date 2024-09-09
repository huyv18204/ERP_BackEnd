<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix("v1")->group(function () {
    Route::prefix("employees")->group(function () {
        Route::get("/", [EmployeeController::class, 'index']);
        Route::post("/", [EmployeeController::class, 'store']);
        Route::put("/{id}", [EmployeeController::class, 'update']);
        Route::delete("/{id}", [EmployeeController::class, 'destroy']);
    });

    Route::prefix("departments")->group(function () {
        Route::get("/", [DepartmentController::class, 'index']);
//        Route::post("/", [DepartmentController::class, 'store']);
//        Route::put("/id", [DepartmentController::class, 'update']);
//        Route::delete("/id", [DepartmentController::class, 'destroy']);
    });

});


