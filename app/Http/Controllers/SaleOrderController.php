<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleOrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if (empty($request->customer_id) || empty($request->order_date)) {
                return response()->json(["type" => "error","message" => "Please fill in required fields"]);
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
                        if ($index == 0 && (empty($item['product']) || empty($item['unit_price']))) {
                            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                        }
                        if (!empty($item['product']) && !empty($item['unit_price'])) {
                            $saleOrderItem = SaleOrderItem::query()->create([
                                'code' => CodeGenerator::generateCode('sale_order_items', "SI"),
                                'sale_order_id' => $saleOrder->id,
                                'unit_price' => $item['unit_price'],
                                'product' => $item['product'],
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

}
