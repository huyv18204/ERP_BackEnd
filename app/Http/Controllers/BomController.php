<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\Bom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BomController extends Controller
{

    public function index(Request $request)
    {
        $product_id = $request->query('product_id');
        if (!$product_id) {
            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
        }
        $BOM = Bom::query()->where('product_id', $product_id)->get();
        return response()->json($BOM);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if (!empty($request->materials)) {
                $materials = $request->materials;
                $arrItems = [];
                foreach ($materials as $index => $item) {
                    if ($index == 0 && (empty($item['material_id']) || empty($item['quantity']) && empty($request->product_id))) {
                        return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                    }

                    if(!empty($item['material_id']) || !empty($item['quantity'])){
                        $BOM = Bom::query()->create([
                            'material_id' => $item['material_id'],
                            'product_id' => $request->product_id,
                            'quantity' => $item['quantity'],
                        ]);
                        $BOM['key'] = $index + 1;
                        $arrItems[] = $BOM;
                    }
                }
            }
            DB::commit();
            return response()->json([
                "data" => $arrItems,
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
        $BOM = Bom::query()->find($id);
        if (!$BOM) {
            return response()->json([
                "type" => "error",
                'message' => "BOM does not exits"
            ]);
        }

        if (empty($request->material_id) || empty($request->quantity)) {
            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
        }
        $response = $BOM->update([
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
//
//    public function update(Request $request, $id)
//    {
//
//        return response()->json($request->all());
//    }
    public function destroy($id)
    {
        $BOM = Bom::query()->find($id);
        if (!$BOM) {
            return response()->json([
                "type" => "error",
                'message' => "BOM does not exits"
            ]);
        }
        $BOM->delete();
        return response()->json([
            "type" => "success",
            'message' => "Delete Successfully"
        ]);
    }
}
