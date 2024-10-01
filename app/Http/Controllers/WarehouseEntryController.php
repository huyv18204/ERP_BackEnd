<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\WarehouseEntry;
use App\Models\WarehouseEntryDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseEntryController extends Controller
{
    public function index(Request $request)
    {
        $supplier_id = $request->query('supplier_id');
        $code = $request->query('code');
        $start_date = $request->query('start_create_date');
        $end_date = $request->query('end_create_date');
        $WHEntry = WarehouseEntry::query()->with('supplier', 'warehouse_entry_details');

        if ($supplier_id) {
            $WHEntry->where('supplier_id', $supplier_id);
        }
        if ($start_date) {
            $WHEntry->where('entry_date', '>=', $start_date);
        }

        if ($end_date) {
            $WHEntry->where('entry_date', '<=', $end_date);
        }

        if ($code) {
            $WHEntry->where('code', $code);
        }

        $WHEntry = $WHEntry->orderByDesc('id')->get();
        return response()->json($WHEntry);
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if (empty($request->supplier_id)) {
                return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
            }
            $total_amount = 0;
            $total_price = 0;
            if (!empty($request->warehouseEntryDetail)) {

                $WHEntryDetails = $request->warehouseEntryDetail;

                foreach ($WHEntryDetails as $index => $item) {
                    if (!empty($item['material_code']) && !empty($item['quantity']) && !empty($item['unit_price']) && !empty($item['name'])) {
                        $total_price += $item['quantity'] * $item['unit_price'];
                        $total_amount += $item['quantity'];
                    }
                }
            }
            $WHEntry = WarehouseEntry::query()->create([
                'code' => CodeGenerator::generateCode('warehouse_entries', "WE"),
                'entry_date' => Carbon::now()->setTimezone('Asia/Ho_Chi_Minh'),
                'supplier_id' => $request->supplier_id,
                "total_price" => $total_price,
                "total_amount" => $total_amount,

            ]);
            if ($WHEntry) {
                if (!empty($request->warehouseEntryDetail)) {
                    $WHEntryDetails = $request->warehouseEntryDetail;
                    $arrItems = [];
                    foreach ($WHEntryDetails as $index => $item) {
                        if ($index == 0 && (empty($item['material_code'] || empty($item['quantity']) || empty($item['unit_price'])) || empty($item['name']))) {
                            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                        }
                        if (!empty($item['material_code']) && !empty($item['quantity']) && !empty($item['unit_price']) && !empty($item['name'])) {
                            $WHEntryDetail = WarehouseEntryDetail::query()->create([
                                "warehouse_entry_id" => $WHEntry->id,
                                'total_price' => ($item['quantity'] * $item['unit_price']),
                                'name' => $item['name'],
                                'material_code' => $item['material_code'],
                                'quantity' => $item['quantity'],
                                'unit_price' => $item['unit_price'],
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

    public function destroy($id)
    {
        $WHEntry = WarehouseEntry::query()->find($id);
        if (!$WHEntry) {
            return response()->json([
                "type" => "error",
                "message" => "Warehouse Entry does not exits"
            ]);
        }
        WarehouseEntryDetail::query()->where('warehouse_entry_id', $WHEntry->id)->delete();
        $WHEntry->delete();
        return response()->json([
            "type" => "success",
            "message" => "Delete success"
        ]);
    }
}
