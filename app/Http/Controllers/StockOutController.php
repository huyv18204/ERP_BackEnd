<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\StockOut;
use App\Models\StockOutItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->query('code');
        $start_date = $request->query('start_create_date');
        $end_date = $request->query('end_create_date');
        $stockOuts = StockOut::query()->with('stock_out_items.material');

        if ($start_date) {
            $stockOuts->where('out_date', '>=', $start_date);
        }

        if ($end_date) {
            $stockOuts->where('out_date', '<=', $end_date);
        }

        if ($code) {
            $stockOuts->where('code', $code);
        }

        $stockOuts = $stockOuts->orderByDesc('id')->get();
        return response()->json($stockOuts);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $stockOut = StockOut::query()->create([
                'code' => CodeGenerator::generateCode('stock_outs', "ST"),
                'out_date' => Carbon::now()->setTimezone('Asia/Ho_Chi_Minh'),
            ]);
            if ($stockOut) {
                if (!empty($request->stockOutItems)) {
                    $stockOutItems = $request->stockOutItems;
                    $arrItems = [];
                    foreach ($stockOutItems as $index => $item) {
                        if ($index == 0 && (empty($item['material_id']) || empty($item['quantity']))) {
                            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                        }
                        if (!empty($item['material_id']) && !empty($item['quantity'])) {
                            $stockOutItem = StockOutItem::query()->create([
                                "stock_out_id" => $stockOut->id,
                                'material_id' => $item['material_id'],
                                'quantity' => $item['quantity'],
                            ]);
                            $stockOutItem['key'] = $index + 1;
                            $arrItems[] = $stockOutItem;
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

    public function destroy($id)
    {
        $stockOut = StockOut::query()->find($id);
        if (!$stockOut) {
            return response()->json([
                "type" => "error",
                "message" => "Warehouse Entry does not exits"
            ]);
        }
        StockOutItem::query()->where('stock_out_id', $stockOut->id)->delete();
        $stockOut->delete();
        return response()->json([
            "type" => "success",
            "message" => "Delete success"
        ]);
    }
}
