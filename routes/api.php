<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NgTypeController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\ProductNgController;
use App\Http\Controllers\ProductProcessController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderItemController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\PurchaseRequisitionItemController;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\SaleOrderItemController;
use App\Http\Controllers\StockMaterialController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\StockOutItemController;
use App\Http\Controllers\StockProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseEntryController;
use App\Http\Controllers\WarehouseEntryDetailController;
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


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/profile', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::middleware(['jwt.auth'])->group(function () {
    Route::prefix("v1")->group(function () {
        Route::prefix("employees")->group(function () {
            Route::get("/", [EmployeeController::class, 'index']);
            Route::post("/", [EmployeeController::class, 'store']);
            Route::put("/{id}", [EmployeeController::class, 'update']);
            Route::delete("/{id}", [EmployeeController::class, 'destroy']);
        });

        Route::prefix("users")->group(function () {
            Route::get("/", [UserController::class, 'index']);
            Route::post("/", [UserController::class, 'store']);
            Route::put("/{id}", [UserController::class, 'update']);
            Route::delete("/{id}", [UserController::class, 'destroy']);
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

        Route::prefix("products")->group(function () {
            Route::get("/", [ProductController::class, 'index']);
            Route::post("/", [ProductController::class, 'store']);
            Route::put("/{id}", [ProductController::class, 'update']);
            Route::delete("/{id}", [ProductController::class, 'destroy']);
        });

        Route::prefix("materials")->group(function () {
            Route::get("/", [MaterialController::class, 'index']);
            Route::post("/", [MaterialController::class, 'store']);
            Route::put("/{id}", [MaterialController::class, 'update']);
            Route::delete("/{id}", [MaterialController::class, 'destroy']);
        });

        Route::prefix("boms")->group(function () {
            Route::get("/", [BomController::class, 'index']);
            Route::post("/", [BomController::class, 'store']);
            Route::put("/{id}", [BomController::class, 'update']);
            Route::delete("/{id}", [BomController::class, 'destroy']);
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
            Route::put("/{id}/status", [SaleOrderController::class, 'updateStatus']);
            Route::delete("/{id}", [SaleOrderController::class, 'destroy']);
        });


        Route::prefix("stock-materials")->group(function () {
            Route::get("/", [StockMaterialController::class, 'index']);
            Route::post("/", [StockMaterialController::class, 'store']);
            Route::put("/{id}", [StockMaterialController::class, 'update']);
            Route::delete("/{id}", [StockMaterialController::class, 'destroy']);
        });

        Route::prefix("stock-products")->group(function () {
            Route::get("/", [StockProductController::class, 'index']);
            Route::post("/", [StockProductController::class, 'store']);
            Route::put("/{id}", [StockProductController::class, 'update']);
            Route::delete("/{id}", [StockProductController::class, 'destroy']);
        });

        Route::prefix("purchase-requisitions")->group(function () {
            Route::get("/", [PurchaseRequisitionController::class, 'index']);
            Route::post("/", [PurchaseRequisitionController::class, 'store']);
            Route::put("/{id}", [PurchaseRequisitionController::class, 'update']);
            Route::delete("/{id}", [PurchaseRequisitionController::class, 'destroy']);
        });

        Route::prefix("purchase-orders")->group(function () {
            Route::get("/", [PurchaseOrderController::class, 'index']);
            Route::post("/", [PurchaseOrderController::class, 'store']);
            Route::put("/{id}", [PurchaseOrderController::class, 'update']);
            Route::delete("/{id}", [PurchaseOrderController::class, 'destroy']);
            Route::put("/{id}/status", [PurchaseOrderController::class, 'updateStatus']);
        });


        Route::prefix("purchase-order-items")->group(function () {
            Route::get("/", [PurchaseOrderItemController::class, 'index']);
            Route::post("/", [PurchaseOrderItemController::class, 'store']);
            Route::put("/{id}", [PurchaseOrderItemController::class, 'update']);
            Route::delete("/{id}", [PurchaseOrderItemController::class, 'destroy']);
        });

        Route::prefix("purchase-requisition-items")->group(function () {
            Route::get("/", [PurchaseRequisitionItemController::class, 'index']);
            Route::post("/", [PurchaseRequisitionItemController::class, 'store']);
            Route::put("/{id}", [PurchaseRequisitionItemController::class, 'update']);
            Route::delete("/{id}", [PurchaseRequisitionItemController::class, 'destroy']);
            Route::get("/{id}", [PurchaseRequisitionItemController::class, 'show']);
        });

        Route::prefix("warehouse-entries")->group(function () {
            Route::get("/", [WarehouseEntryController::class, 'index']);
            Route::post("/", [WarehouseEntryController::class, 'store']);
            Route::put("/{id}", [WarehouseEntryController::class, 'update']);
            Route::delete("/{id}", [WarehouseEntryController::class, 'destroy']);
        });

        Route::prefix("stock-outs")->group(function () {
            Route::get("/", [StockOutController::class, 'index']);
            Route::post("/", [StockOutController::class, 'store']);
            Route::put("/{id}", [StockOutController::class, 'update']);
            Route::delete("/{id}", [StockOutController::class, 'destroy']);
        });

        Route::prefix("stock-out-items")->group(function () {
            Route::get("/", [StockOutItemController::class, 'index']);
            Route::post("/", [StockOutItemController::class, 'store']);
            Route::put("/{id}", [StockOutItemController::class, 'update']);
            Route::delete("/{id}", [StockOutItemController::class, 'destroy']);
            Route::get("/{id}", [StockOutItemController::class, 'show']);
        });

        Route::prefix("warehouse-entry-details")->group(function () {
            Route::get("/", [WarehouseEntryDetailController::class, 'index']);
            Route::post("/", [WarehouseEntryDetailController::class, 'store']);
            Route::put("/{id}", [WarehouseEntryDetailController::class, 'update']);
            Route::delete("/{id}", [WarehouseEntryDetailController::class, 'destroy']);
            Route::get("/{id}", [WarehouseEntryDetailController::class, 'show']);
        });

        Route::prefix("sale-order-items")->group(function () {
            Route::get("/", [SaleOrderItemController::class, 'index']);
            Route::get("/{id}", [SaleOrderItemController::class, 'show']);
            Route::post("/", [SaleOrderItemController::class, 'store']);
            Route::put("/{id}", [SaleOrderItemController::class, 'update']);
            Route::delete("/{id}", [SaleOrderItemController::class, 'destroy']);
        });

        Route::prefix("production-orders")->group(function () {
            Route::get("/", [ProductionOrderController::class, 'index']);
            Route::get("/{id}", [ProductionOrderController::class, 'show']);
            Route::post("/", [ProductionOrderController::class, 'store']);
            Route::put("/{id}", [ProductionOrderController::class, 'update']);
            Route::delete("/{id}", [ProductionOrderController::class, 'destroy']);
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
});






