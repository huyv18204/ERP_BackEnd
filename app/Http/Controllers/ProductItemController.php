<?php

namespace App\Http\Controllers;

use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductItemController extends Controller
{

    public function show($id)
    {
        $productItem = ProductItem::query()->where('sale_order_item_id', $id)->get();
        $productItem = $productItem->map(function ($item) {
            $item['key'] = "key" . $item->id;
            return $item;
        });
        return response()->json(["data" => $productItem]);


    }

    public function update(Request $request, $id)
    {
        if (!$request->size_id || !$request->color_id || !$request->quantity) {
            return response()->json([
                "message" => "Please fill in all fields",
                "type" => "error"
            ]);
        }

        $productItem = ProductItem::query()->where('id', $id)->update([
            "size_id" => $request->size_id,
            "color_id" => $request->color_id,
            "quantity" => $request->quantity,
            "description" => $request->description,
        ]);

        if (!$productItem) {
            return response()->json([
                "message" => "Update False",
                "type" => "error"
            ]);
        }

        return response()->json([
            "message" => "Update successfully",
            "type" => "success",
        ]);
    }

    public function store(Request $request)
    {
        foreach ($request->size_color_quantity as $index => $item) {
            if ($index == 0 && (empty($item['size_id']) || empty($item['color_id']) || empty($item['quantity']))) {
                return response()->json([
                    'type' => "error",
                    "message" => "Please fill in all fields"
                ]);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($request->size_color_quantity as $item) {
                if (!empty($item['size_id']) && !empty($item['color_id']) && !empty($item['quantity'])) {
                    ProductItem::query()->create([
                        "sale_order_item_id" => $request->sale_order_item_id,
                        "size_id" => $item['size_id'],
                        "color_id" => $item['color_id'],
                        "quantity" => $item['quantity'],
                        "description" => $item['description'] ?? ''
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                "message" => "Add successfully",
                "type" => "success"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'type' => "error",
                "message" => "Failed to add items: " . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $productItem = ProductItem::query()->find($id);
        if (!$productItem) {
            return response()->json([
                "message" => "Item does not exist",
                "type" => "error"
            ]);
        }

        $productItem->delete();
        return response()->json([
            "message" => "Delete success",
            "type" => "success"
        ]);
    }

}
