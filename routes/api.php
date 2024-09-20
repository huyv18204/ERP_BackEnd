<?php

use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NgTypeController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductItemController;
use App\Http\Controllers\ProductNgController;
use App\Http\Controllers\ProductProcessController;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\SaleOrderItemController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
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

    Route::prefix("suppliers")->group(function () {
        Route::get("/", [SupplierController::class, 'index']);
        Route::post("/", [SupplierController::class, 'store']);
        Route::put("/{id}", [SupplierController::class, 'update']);
        Route::delete("/{id}", [SupplierController::class, 'destroy']);
    });

    Route::prefix("warehouses")->group(function () {
        Route::get("/", [WarehouseController::class, 'index']);
        Route::post("/", [WarehouseController::class, 'store']);
        Route::put("/{id}", [WarehouseController::class, 'update']);
        Route::delete("/{id}", [WarehouseController::class, 'destroy']);
    });

    Route::prefix("products")->group(function () {
        Route::get("/", [ProductController::class, 'index']);
        Route::post("/", [ProductController::class, 'store']);
        Route::put("/{id}", [ProductController::class, 'update']);
        Route::delete("/{id}", [ProductController::class, 'destroy']);
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

    Route::prefix("sizes")->group(function () {
        Route::get("/", [SizeController::class, 'index']);
        Route::post("/", [SizeController::class, 'store']);
        Route::put("/{id}", [SizeController::class, 'update']);
        Route::delete("/{id}", [SizeController::class, 'destroy']);
    });

    Route::prefix("colors")->group(function () {
        Route::get("/", [ColorController::class, 'index']);
        Route::post("/", [ColorController::class, 'store']);
        Route::put("/{id}", [ColorController::class, 'update']);
        Route::delete("/{id}", [ColorController::class, 'destroy']);
    });

    Route::prefix("processes")->group(function () {
        Route::get("/", [ProcessController::class, 'index']);
        Route::post("/", [ProcessController::class, 'store']);
        Route::put("/{id}", [ProcessController::class, 'update']);
        Route::delete("/{id}", [ProcessController::class, 'destroy']);
    });


    Route::prefix("ng-types")->group(function () {
        Route::get("/", [NgTypeController::class, 'index']);
        Route::post("/", [NgTypeController::class, 'store']);
        Route::put("/{id}", [NgTypeController::class, 'update']);
        Route::delete("/{id}", [NgTypeController::class, 'destroy']);
    });


    Route::prefix("menus")->group(function () {
        Route::get("/menu-tree", [MenuController::class, 'getMenuTree']);
        Route::get("/menu-root", [MenuController::class, 'getMenuRoot']);
        Route::get("/", [MenuController::class, 'index']);
        Route::post("/", [MenuController::class, 'store']);
        Route::put("/{id}", [MenuController::class, 'update']);
        Route::delete("/{id}", [MenuController::class, 'destroy']);
    });

    Route::prefix("sale-orders")->group(function () {
        Route::get("/", [SaleOrderController::class, 'index']);
        Route::post("/", [SaleOrderController::class, 'store']);
        Route::put("/{id}", [SaleOrderController::class, 'update']);
        Route::delete("/{id}", [SaleOrderController::class, 'destroy']);
    });

    Route::prefix("sale-order-items")->group(function () {
        Route::get("/", [SaleOrderItemController::class, 'index']);
        Route::post("/", [SaleOrderItemController::class, 'store']);
        Route::put("/{id}", [SaleOrderItemController::class, 'update']);
        Route::delete("/{id}", [SaleOrderItemController::class, 'destroy']);
    });

    Route::prefix("product-items")->group(function () {
        Route::get("/", [ProductItemController::class, 'index']);
        Route::get("/{id}", [ProductItemController::class, 'show']);
        Route::post("/", [ProductItemController::class, 'store']);
        Route::put("/{id}", [ProductItemController::class, 'update']);
        Route::delete("/{id}", [ProductItemController::class, 'destroy']);
    });

    Route::prefix("product-processes")->group(function () {
        Route::get("/", [ProductProcessController::class, 'index']);
        Route::get("/{id}", [ProductProcessController::class, 'show']);
        Route::post("/", [ProductProcessController::class, 'store']);
        Route::put("/{id}", [ProductProcessController::class, 'update']);
        Route::delete("/{id}", [ProductProcessController::class, 'destroy']);
    });

    Route::prefix("product-ngs")->group(function () {
        Route::get("/", [ProductNgController::class, 'index']);
        Route::get("/{id}", [ProductNgController::class, 'show']);
        Route::post("/", [ProductNgController::class, 'store']);
        Route::put("/{id}", [ProductNgController::class, 'update']);
        Route::delete("/{id}", [ProductNgController::class, 'destroy']);
    });

});


