<?php

namespace App\Http\Controllers;

use App\Models\ProductProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductProcessController extends Controller
{
    public function show($id)
    {
        $productProcess = ProductProcess::query()->where('sale_order_item_id', $id)->get();
        $productProcess = $productProcess->map(function ($item) {
            $item['key'] = "key" . $item->id;
            return $item;
        });
        return response()->json(["data" => $productProcess]);


    }

    public function update(Request $request, $id)
    {
        if (!$request->process_id || !$request->std_workTime) {
            return response()->json([
                "message" => "Please fill in all fields",
                "type" => "error"
            ]);
        }

        $productProcess = ProductProcess::query()->where('id', $id)->update([
            "process_id" => $request->process_id,
            "std_workTime" => $request->std_workTime,
            "description" => $request->description,
        ]);

        if (!$productProcess) {
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
        foreach ($request->product_process as $index => $item) {
            if ($index == 0 && (empty($item['process_id']) || empty($item['std_workTime']))) {
                return response()->json([
                    'type' => "error",
                    "message" => "Please fill in all fields"
                ]);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($request->product_process as $item) {
                if (!empty($item['process_id']) && !empty($item['std_workTime'])) {
                    ProductProcess::query()->create([
                        "sale_order_item_id" => $request->sale_order_item_id,
                        "process_id" => $item['process_id'],
                        "std_workTime" => $item['std_workTime'],
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
        $productProcess = ProductProcess::query()->find($id);
        if (!$productProcess) {
            return response()->json([
                "message" => "Item does not exist",
                "type" => "error"
            ]);
        }

        $productProcess->delete();
        return response()->json([
            "message" => "Delete success",
            "type" => "success"
        ]);
    }
}
