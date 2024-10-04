<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\StockOutItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOutItemController extends Controller
{
    public function show($id)
    {
        $stockOutItems = StockOutItem::query()->where('stock_out_id', $id)->get();
        $stockOutItems = $stockOutItems->map(function ($item) {
            $item['key'] = "key" . $item->id;
            return $item;
        });
        return response()->json(["data" => $stockOutItems]);
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $stockOut = StockOut::query()->find($request->id);

            if ($stockOut) {
                if (!empty($request->stockOutItems)) {
                    $stockOutItems = $request->stockOutItems;
                    $arrItems = [];
                    foreach ($stockOutItems as $index => $item) {
                        if ($index == 0 && (empty($item['material_id'] || empty($item['quantity'])))) {
                            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                        }
                        if (!empty($item['material_id']) && !empty($item['quantity'])) {
                            $stockOutItems = StockOutItem::query()->create([
                                "stock_out_id" => $stockOut->id,
                                'material_id' => $item['material_id'],
                                'quantity' => $item['quantity'],
                            ]);
                            $stockOutItems['key'] = $index + 1;
                            $arrItems[] = $stockOutItems;
                        }

                    }
                }
            }

            DB::commit();
            return response()->json([
                "data" => [
                    "stock_out" => $stockOut,
                    "stock_out_item" => $arrItems
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
        $stockOutItems = StockOutItem::query()->find($id);
        if (!$stockOutItems) {
            return response()->json([
                "type" => "error",
                'message' => "Record does not exits"
            ]);
        }

        if (empty($request->material_id) || empty($request->quantity)) {
            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
        }
        $response = $stockOutItems->update([
            "material_id" => $request->material_id,
            "quantity" => $request->quantity,
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
        $stockOutItems = StockOutItem::query()->find($id);
        if (!$stockOutItems) {
            return response()->json([
                "type" => "error",
                "message" => "Sale order does not exits"
            ]);
        }
        $stockOutItems->delete();
        return response()->json([
            "type" => "success",
            "message" => "Delete success"
        ]);
    }
}
