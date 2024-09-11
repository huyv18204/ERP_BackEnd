<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\MenuController;
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

    Route::prefix("customers")->group(function () {
        Route::get("/", [CustomerController::class, 'index']);
        Route::post("/", [CustomerController::class, 'store']);
        Route::put("/{id}", [CustomerController::class, 'update']);
        Route::delete("/{id}", [CustomerController::class, 'destroy']);
    });

    Route::prefix("departments")->group(function () {
        Route::get("/", [DepartmentController::class, 'index']);
        Route::post("/", [DepartmentController::class, 'store']);
        Route::put("/{id}", [DepartmentController::class, 'update']);
        Route::delete("/{id}", [DepartmentController::class, 'destroy']);
    });

    Route::prefix("lines")->group(function () {
        Route::get("/", [LineController::class, 'index']);
        Route::post("/", [LineController::class, 'store']);
        Route::put("/{id}", [LineController::class, 'update']);
        Route::delete("/{id}", [LineController::class, 'destroy']);
    });

    Route::prefix("factories")->group(function () {
        Route::get("/", [FactoryController::class, 'index']);
        Route::post("/", [FactoryController::class, 'store']);
        Route::put("/{id}", [FactoryController::class, 'update']);
        Route::delete("/{id}", [FactoryController::class, 'destroy']);
    });


    Route::prefix("menus")->group(function () {
        Route::get("/menu-tree", [MenuController::class, 'getMenuTree']);
        Route::get("/menu-root", [MenuController::class, 'getMenuRoot']);
        Route::get("/", [MenuController::class, 'index']);
        Route::post("/", [MenuController::class, 'store']);
        Route::put("/{id}", [MenuController::class, 'update']);
        Route::delete("/{id}", [MenuController::class, 'destroy']);
    });

});


