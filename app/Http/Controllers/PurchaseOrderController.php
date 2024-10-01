<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseRequisitionStatus;
use App\Helpers\CodeGenerator;
use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequisition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{

    public function index(Request $request)
    {
        $supplier_id = $request->query('supplier_id');
        $start_date = $request->query('start_order_date');
        $end_date = $request->query('end_order_date');
        $code = $request->query('code');
        $POrders = PurchaseOrder::with(['supplier', 'purchase_order_items' => function ($query) {
            $query->with(['material']);
        }]);


        if ($supplier_id) {
            $POrders->where('supplier_id', $supplier_id);
        }

        if ($code) {
            $POrders->where('code', $code);
        }
        if ($start_date) {
            $POrders->where('order_date', '>=', $start_date);
        }

        if ($end_date) {
            $POrders->where('order_date', '<=', $end_date);
        }

        $POrders = $POrders->orderByDesc('id')->get();
        return response()->json($POrders);
    }
    public function store(Request $request)
    {
        $request->validate([
            'PRsId' => 'required|array',
            'PDOrders' => 'required|array',
            'PDOrders.*.material_id' => 'required',
            'PDOrders.*.quantity' => 'required|numeric|min:0',
            'PDOrders.*.supplier_id' => 'required|numeric|min:0',
        ]);
        try {
            DB::beginTransaction();
            $ordersBySupplier = [];
            foreach ($request->PDOrders as $item) {
                $ordersBySupplier[$item['supplier_id']][] = $item;
            }

            foreach ($ordersBySupplier as $supplierId => $items) {

                $POrder = PurchaseOrder::query()->create([
                    'code' => CodeGenerator::generateCode('purchase_orders', "PO"),
                    'order_date' => Carbon::now()->setTimezone("Asia/Ho_Chi_Minh"),
                    'supplier_id' => $supplierId,
                ]);

                if (!$POrder) {
                    return response()->json(['type' => "error", 'message' => 'Store failed']);
                }
                $total_price = 0;
                $total_amount = 0;
                foreach ($items as $item) {
                    $material = Material::query()->find($item['material_id']);

                    if (!$material) {
                        DB::rollBack();
                        return response()->json(['type' => 'error', 'message' => 'Material not found'], 404);
                    }
                    $response = PurchaseOrderItem::query()->create([
                        'material_id' => $item['material_id'],
                        'quantity' => $item['quantity'],
                        'purchase_order_id' => $POrder->id,
                        'unit_price' => $material->cost,
                        'total_price' => $material->cost * $item['quantity'],
                    ]);

                    $total_amount += $item['quantity'];
                    $total_price += $material->cost * $item['quantity'];
                }

                $POrder->update([
                    'total_amount' => $total_amount,
                    'total_price' => $total_price
                ]);
            }

            foreach ($request->PRsId as $id){
                PurchaseRequisition::query()->where('id', $id)->update([
                   'status' => "Approved"
                ]);
            }


            DB::commit();
            return response()->json(['type' => "success", 'message' => 'Create purchase successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['type' => "error", 'message' => 'Failed to store warehouse entry details', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $POrder = PurchaseOrder::query()->find($id);
            if (!$POrder) {
                return response()->json([
                    "type" => "error",
                    "message" => "Sale order does not exist"
                ]);
            }
            $POrder->update([
                "status" => "Rejected"
            ]);
            PurchaseOrderItem::query()->where('purchase_order_id', $POrder->id)->delete();
            $POrder->delete();

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
    public function updateStatus(Request $request, $id)
    {

        $validatedData = $request->validate([
            'status' => [
                'required',
                new \Illuminate\Validation\Rules\Enum(PurchaseRequisitionStatus::class)
            ],
        ]);

        $POrder = PurchaseOrder::query()->find($id);
        if (!$POrder) {
            return response()->json([
                "type" => "error",
                "message" => "Purchase order does not exits"
            ]);
        }
        $response = $POrder->update([
            "status" => $validatedData['status']
        ]);

        if (!$response) {
            return response()->json([
                "type" => "error",
                "message" => "Update failed"
            ]);
        }

        return response()->json([
            "type" => "success",
            "message" => "Update status success",
            "data" => $request->all()
        ]);
    }


}
