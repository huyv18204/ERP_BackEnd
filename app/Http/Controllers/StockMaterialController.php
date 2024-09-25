<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\StockMaterial;
use App\Models\WarehouseEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMaterialController extends Controller
{
    public function index(Request $request)
    {
        $material_id = $request->query('material_id');
        $code = $request->query('code');

        $query = StockMaterial::with(['material']);

        if ($material_id) {
            $query->where('material_id', $material_id);
        }

        if ($code) {
            $query->whereHas('material', function ($query) use ($code) {
                $query->where('code', $code);
            });
        }

        $stock = $query->get();
        return response()->json($stock);

    }


    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'warehouseEntryDetail' => 'required|array',
            'warehouseEntryDetail.*.material_code' => 'required',
            'warehouseEntryDetail.*.quantity' => 'required|numeric|min:0',
            'warehouseEntryDetail.*.unit_price' => 'required|numeric|min:0',
            'warehouseEntryDetail.*.name' => 'required',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->warehouseEntryDetail as $item) {
                $material = Material::query()->where('code', $item['material_code'])->where('supplier_id', $request->supplier_id)->first();

                if ($material) {
                    $stock = StockMaterial::query()
                        ->where('material_id', $material->id)
                        ->first();

                    if ($stock) {
                        $stock->update([
                            'quantity' => $stock->quantity + $item['quantity']
                        ]);
                    }
                } else {
                    $responseMaterial = Material::query()->create([
                        "code" => $item['material_code'],
                        "cost" => $item['unit_price'],
                        "name" => $item['name'],
                        "supplier_id" => $request->supplier_id,
                    ]);

                    if ($responseMaterial) {
                        StockMaterial::query()->create([
                            'material_id' => $responseMaterial->id,
                            'quantity' => $item['quantity']
                        ]);
                    }
                }

                WarehouseEntry::query()->where('id', $item['warehouse_entry_id'])->update([
                    "status" => true
                ]);
            }

            DB::commit();

            return response()->json(['type' => "success", 'message' => 'Warehouse entry details successfully stored']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['type' => "error", 'message' => 'Failed to store warehouse entry details', 'error' => $e->getMessage()], 500);
        }
    }
}
