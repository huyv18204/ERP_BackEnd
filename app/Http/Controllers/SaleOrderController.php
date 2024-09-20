<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\ProductItem;
use App\Models\ProductNg;
use App\Models\ProductProcess;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleOrderController extends Controller
{

//    public function index(Request $request)
//    {
//        $customer_id = $request->query('customer_id');
//        $start_date = $request->query('start_order_date');
//        $end_date = $request->query('end_order_date');
//        $saleOrders = SaleOrder::with(['customer','sale_order_items.product_items' => function ($query) {
//            $query->selectRaw('sale_order_item_id, SUM(quantity) as total')->groupBy('sale_order_item_id');
//        }])
//            ->withSum(['sale_order_items as total_quantity' => function ($query) {
//                $query->join('product_items', 'product_items.sale_order_item_id', '=', 'sale_order_items.id')
//                    ->selectRaw('SUM(product_items.quantity)');
//            }], 'product_items.quantity');
//
//
//        if ($customer_id) {
//            $saleOrders->where('customer_id', $customer_id);
//        }
//        if ($start_date) {
//            $saleOrders->where('order_date', '>=', $start_date);
//        }
//
//        if ($end_date) {
//            $saleOrders->where('order_date', '<=', $end_date);
//        }
//
//        $saleOrders = $saleOrders->orderByDesc('id')->get();
//        return response()->json($saleOrders);
//    }


    public function index(Request $request)
    {
        $customer_id = $request->query('customer_id');
        $start_date = $request->query('start_order_date');
        $end_date = $request->query('end_order_date');
        $saleOrders = SaleOrder::with(['customer', 'sale_order_items' => function ($query) {
            $query->with(['product', 'product_items']);
        }]);


        if ($customer_id) {
            $saleOrders->where('customer_id', $customer_id);
        }
        if ($start_date) {
            $saleOrders->where('order_date', '>=', $start_date);
        }

        if ($end_date) {
            $saleOrders->where('order_date', '<=', $end_date);
        }

        $saleOrders = $saleOrders->orderByDesc('id')->get();
        return response()->json($saleOrders);
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if (empty($request->customer_id) || empty($request->order_date)) {
                return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
            }
            $saleOrder = SaleOrder::query()->create([
                'code' => CodeGenerator::generateCode('sale_orders', "SO"),
                'order_date' => $request->order_date,
                'customer_id' => $request->customer_id
            ]);
            if ($saleOrder) {
                if (!empty($request->saleOrderItem)) {
                    $saleOrderItems = $request->saleOrderItem;
                    $arrItems = [];
                    foreach ($saleOrderItems as $index => $item) {
                        if ($index == 0 && (empty($item['product_id']))) {
                            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                        }
                        if (!empty($item['product_id'])) {
                            $saleOrderItem = SaleOrderItem::query()->create([
                                'code' => CodeGenerator::generateCode('sale_order_items', "SI"),
                                'sale_order_id' => $saleOrder->id,
                                'product_id' => $item['product_id'],
                                'delivery_date' => $item['delivery_date'],
                                'description' => $item['description'],
                            ]);
                            $saleOrderItem['key'] = $index + 1;
                            $arrItems[] = $saleOrderItem;
                        }

                    }
                }
            }

            DB::commit();
//            $data = json_encode($arrItems,JSON_PRETTY_PRINT);
            return response()->json([
                "data" => [
                    "sale_order" => $saleOrder,
                    "sale_order_item" => $arrItems
                ],
                "type" => "success",
                'message' => "Add new success"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        $saleOrder = SaleOrder::query()->find($id);
        if (!$saleOrder) {
            return response()->json([
                "type" => "error",
                "message" => "Sale order does not exits"
            ]);
        }
        $saleOrderItems = SaleOrderItem::query()->where('sale_order_id', $saleOrder->id)->get();

        foreach ($saleOrderItems as $saleOrderItem) {
            ProductItem::query()->where("sale_order_item_id", $saleOrderItem['id'])->delete();
            ProductProcess::query()->where("sale_order_item_id", $saleOrderItem['id'])->delete();
            ProductNg::query()->where("sale_order_item_id", $saleOrderItem['id'])->delete();
        }

        SaleOrderItem::query()->where('sale_order_id', $saleOrder->id)->delete();
        $saleOrder->delete();
        return response()->json([
            "type" => "success",
            "message" => "Delete success"
        ]);
    }

}

