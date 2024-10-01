<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\Material;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequisitionItemController extends Controller
{
    public function show($id)
    {
        $PRItems = PurchaseRequisitionItem::query()->where('purchase_requisition_id', $id)->get();
        $PRItems = $PRItems->map(function ($item) {
            $item['key'] = "key" . $item->id;
            return $item;
        });
        return response()->json(["data" => $PRItems]);
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();


            if (!empty($request->PRs)) {
                $PRs = $request->PRs;
                foreach ($PRs as $index => $item) {
                    if ($index == 0 && (empty($item['material_id']) || empty($item['quantity']))) {
                        return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                    }

                    if (!empty($item['material_id']) || !empty($item['quantity'])) {
                        $material = Material::query()->find($item['material_id']);
                        PurchaseRequisitionItem::query()->create([
                            'material_id' => $item['material_id'],
                            'purchase_requisition_id' => $request->id,
                            'quantity' => $item['quantity'],
                            'material_code' => $material->code
                        ]);
                    }

                }
                $PR = PurchaseRequisition::query()->find($request->id);
                $arrItems = PurchaseRequisitionItem::query()->where('purchase_requisition_id', $PR->id)->get();
            }
            DB::commit();
            return response()->json([
                "data" => [
                    "PR" => $PR,
                    "PRItem" => $arrItems
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
        $PRItem = PurchaseRequisitionItem::query()->find($id);
        if (!$PRItem) {
            return response()->json([
                "type" => "error",
                'message' => "Record does not exits"
            ]);
        }

        if (empty($request->material_id) || empty($request->quantity)) {
            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
        }
        $material = Material::query()->find($request->material_id);
        $response = $PRItem->update([
            "material_id" => $request->material_id,
            "material_code" => $material->code,
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
        $PRItem = PurchaseRequisitionItem::query()->find($id);
        if (!$PRItem) {
            return response()->json([
                "type" => "error",
                "message" => "PR Item does not exits"
            ]);
        }
        $PRItem->delete();
        return response()->json([
            "type" => "success",
            "message" => "Delete success"
        ]);
    }
}
