<?php

namespace App\Http\Controllers;

use App\Enums\SaleOrderStatus;
use App\Helpers\CodeGenerator;
use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->query('code');
        $start_date = $request->query('start_create_date');
        $end_date = $request->query('end_create_date');
        $PR = PurchaseRequisition::query()->with('purchase_requisition_items');

        if ($start_date) {
            $PR->where('create_date', '>=', $start_date);
        }
        if ($end_date) {
            $PR->where('create_date', '<=', $end_date);
        }

        if ($code) {
            $PR->where('code', $code);
        }

        $PR = $PR->orderByDesc('id')->get();
        return response()->json($PR);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction(); // Bắt đầu giao dịch

            if (!empty($request->PRs)) {
                // Tạo Purchase Requisition
                $PR = PurchaseRequisition::query()->create([
                    "notes" => $request->notes,
                    "create_date" => Carbon::now()->setTimezone('Asia/Ho_Chi_Minh'),
                    "code" => CodeGenerator::generateCode('purchase_requisitions', "PU")
                ]);

                // Kiểm tra xem Purchase Requisition có được tạo thành công hay không
                if (!$PR || !$PR->id) {
                    return response()->json(["type" => "error", "message" => "Failed to create Purchase Requisition"]);
                }

                $PRs = $request->PRs;
                $arrItems = [];

                foreach ($PRs as $index => $item) {

                    if (empty($item['material_id']) || empty($item['quantity'])) {
                        return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                    }


                    $material = Material::query()->find($item['material_id']);
                    if (!$material) {
                        return response()->json(["type" => "error", "message" => "Material not found"]);
                    }
                    $quantity = (int)$item['quantity'];
                    if ($quantity <= 0) {
                        return response()->json(["type" => "error", "message" => "Quantity must be greater than 0"]);
                    }

                    $PRItem = PurchaseRequisitionItem::query()->create([
                        'material_id' => $item['material_id'],
                        'purchase_requisition_id' => $PR->id,
                        'quantity' => $quantity,
                        'material_code' => $material->code
                    ]);

                    $PRItem['key'] = $index + 1;
                    $arrItems[] = $PRItem;
                }
            }

            DB::commit(); // Commit giao dịch
            return response()->json([
                "data" => [
                    "PR" => $PR,
                    "PRItem" => $arrItems
                ],
                "type" => "success",
                'message' => "Add new success"
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback nếu có lỗi
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $PR = PurchaseRequisition::query()->find($id);
            if (!$PR) {
                return response()->json([
                    "type" => "error",
                    "message" => "Sale order does not exist"
                ]);
            }
            $PR->update([
                "status" => "Rejected"
            ]);
            PurchaseRequisitionItem::query()->where("purchase_requisition_id", $PR->id)->delete();
            $PR->delete();
            DB::commit();
            return response()->json([
                "type" => "success",
                "message" => "Delete success"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
