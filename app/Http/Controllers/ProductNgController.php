<?php

namespace App\Http\Controllers;

use App\Models\ProductNg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductNgController extends Controller
{
    public function show($id)
    {
        $productNG = ProductNg::query()->where('sale_order_item_id', $id)->get();
        $productNG = $productNG->map(function ($item) {
            $item['key'] = "key" . $item->id;
            return $item;
        });
        return response()->json(["data" => $productNG]);


    }

    public function update(Request $request, $id)
    {
        if (!$request->ng_type_id || !$request->std_workTime) {
            return response()->json([
                "message" => "Please fill in all fields",
                "type" => "error"
            ]);
        }

        $productNG = ProductNg::query()->where('id', $id)->update([
            "ng_type_id" => $request->ng_type_id,
            "description" => $request->description,
        ]);

        if (!$productNG) {
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
        foreach ($request->product_ng as $index => $item) {
            if ($index == 0 && (empty($item['ng_type_id']))) {
                return response()->json([
                    'type' => "error",
                    "message" => "Please fill in all fields"
                ]);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($request->product_ng as $item) {
                if (!empty($item['ng_type_id'])) {
                    ProductNg::query()->create([
                        "sale_order_item_id" => $request->sale_order_item_id,
                        "ng_type_id" => $item['ng_type_id'],
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
        $productNG = ProductNg::query()->find($id);
        if (!$productNG) {
            return response()->json([
                "message" => "Item does not exist",
                "type" => "error"
            ]);
        }

        $productNG->delete();
        return response()->json([
            "message" => "Delete success",
            "type" => "success"
        ]);
    }
}
