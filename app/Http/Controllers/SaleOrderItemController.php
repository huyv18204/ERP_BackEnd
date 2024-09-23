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

class SaleOrderItemController extends Controller
{
    public function index()
    {
        $saleOrderItem = SaleOrderItem::query()->get();
        return response()->json($saleOrderItem);
    }

    public function show($id)
    {
        $saleOrderItem = SaleOrderItem::query()->where('sale_order_id', $id)->get();
        $saleOrderItem = $saleOrderItem->map(function ($item) {
            $item['key'] = "key" . $item->id;
            return $item;
        });
        return response()->json(["data" => $saleOrderItem]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $saleOrder = SaleOrder::query()->find($request->id);
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
                                'sale_order_id' => $request->id,
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


    public function update(Request $request, $id)
    {
        $saleOrderItem = SaleOrderItem::query()->find($id);
        if (!$saleOrderItem) {
            return response()->json([
                "type" => "error",
                'message' => "BOM does not exits"
            ]);
        }

        if (empty($request->product_id) || empty($request->delivery_date)) {
            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
        }
        $response = $saleOrderItem->update([
            "product_id" => $request->product_id,
            "delivery_date" => $request->delivery_date,
            "description" => $request->description,
        ]);

        if (!$response) {
            return response()->json([
                "type" => "error",
                'message' => "Update fails"
            ]);
        }

        return response()->json([
            "type" => "success",
            'message' => "Update successfully"
        ]);
    }

    public function destroy($id)
    {
        $saleOrderItems = SaleOrderItem::query()->find($id);
        if (!$saleOrderItems) {
            return response()->json([
                "type" => "error",
                "message" => "Sale order does not exits"
            ]);
        }
        ProductItem::query()->where("sale_order_item_id", $saleOrderItems->id)->delete();
        ProductProcess::query()->where("sale_order_item_id", $saleOrderItems->id)->delete();
        ProductNg::query()->where("sale_order_item_id", $saleOrderItems->id)->delete();
        $saleOrderItems->delete();
        return response()->json([
            "type" => "success",
            "message" => "Delete success"
        ]);
    }
}
