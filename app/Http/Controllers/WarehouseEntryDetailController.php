<?php

namespace App\Http\Controllers;

use App\Models\WarehouseEntry;
use App\Models\WarehouseEntryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseEntryDetailController extends Controller
{
    public function show($id)
    {
        $WHEntryDetail = WarehouseEntryDetail::query()->where('warehouse_entry_id', $id)->get();
        $WHEntryDetail = $WHEntryDetail->map(function ($item) {
            $item['key'] = "key" . $item->id;
            return $item;
        });
        return response()->json(["data" => $WHEntryDetail]);
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $WHEntry = WarehouseEntry::query()->find($request->id);


            $total_price = $WHEntry->total_price;
            $total_amount = $WHEntry->total_amount;

            if (!empty($request->warehouseEntryDetail)) {
                $WHEntryDetails = $request->warehouseEntryDetail;
                foreach ($WHEntryDetails as $index => $item) {
                    if (!empty($item['material_code']) && !empty($item['quantity']) && !empty($item['unit_price']) && !empty($item['name'])) {
                        $total_price += $item['quantity'] * $item['unit_price'];
                        $total_amount += $item['quantity'];
                    }
                }
            }
            if ($WHEntry) {
                $WHEntry->update(["total_price" => $total_price, "total_amount" => $total_amount]);
                if (!empty($request->warehouseEntryDetail)) {
                    $WHEntryDetail = $request->warehouseEntryDetail;
                    $arrItems = [];
                    foreach ($WHEntryDetail as $index => $item) {
                        if ($index == 0 && (empty($item['material_code'] || empty($item['quantity']) || empty($item['unit_price']) || empty($item['name'])))) {
                            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                        }
                        if (!empty($item['material_code']) && !empty($item['quantity']) && !empty($item['unit_price']) && !empty($item['name'])) {
                            $WHEntryDetail = WarehouseEntryDetail::query()->create([
                                "warehouse_entry_id" => $WHEntry->id,
                                'total_price' => $item['quantity'] * $item['unit_price'],
                                'material_code' => $item['material_code'],
                                'quantity' => $item['quantity'],
                                'unit_price' => $item['unit_price'],
                                'name' => $item['name'],
                            ]);
                            $WHEntryDetail['key'] = $index + 1;
                            $arrItems[] = $WHEntryDetail;
                        }

                    }
                }
            }

            DB::commit();
            return response()->json([
                "data" => [
                    "wh_entry" => $WHEntry,
                    "wh_entry_detail" => $arrItems
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
        $WHEntryDetail = WarehouseEntryDetail::query()->find($id);
        if (!$WHEntryDetail) {
            return response()->json([
                "type" => "error",
                'message' => "Record does not exits"
            ]);
        }

        $WHEntry = WarehouseEntry::query()->find($WHEntryDetail->warehouse_entry_id);

        $WHEntry->update([
            "total_price" => $WHEntry->total_price - $WHEntryDetail->total_price + ($request->quantity * $request->unit_price),
            "total_amount" => $WHEntry->total_amount - $WHEntryDetail->quantity + $request->quantity
        ]);

        if (empty($request->material_code) || empty($request->quantity) || empty($request->unit_price) || empty($request->name)) {
            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
        }
        $response = $WHEntryDetail->update([
            "total_price" => $request->quantity * $request->unit_price,
            "material_code" => $request->material_code,
            "quantity" => $request->quantity,
            "unit_price" => $request->unit_price,
            "name" => $request->name,
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
        $WHEntryDetail = WarehouseEntryDetail::query()->find($id);
        if (!$WHEntryDetail) {
            return response()->json([
                "type" => "error",
                "message" => "Sale order does not exits"
            ]);
        }

        $WHEntry = WarehouseEntry::query()->find($WHEntryDetail->warehouse_entry_id);

        $WHEntry->update([
            "total_price" => $WHEntry->total_price - $WHEntryDetail->total_price,
            "total_amount" => $WHEntry->total_amount - $WHEntryDetail->quantity
        ]);

        $WHEntryDetail->delete();
        return response()->json([
            "type" => "success",
            "message" => "Delete success"
        ]);
    }
}
