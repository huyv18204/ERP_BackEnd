<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\AllocatedMaterial;
use App\Models\AllocatedProduct;
use App\Models\Bom;
use App\Models\Material;
use App\Models\ProductionOrder;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\SaleOrder;
use App\Models\StockMaterial;
use App\Models\StockProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionOrderController extends Controller
{

    public function index(Request $request)
    {

        $line_id = $request->query('line_id');
        $factory_id = $request->query('factory_id');
        $product_id = $request->query('product_id');
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $quantity = $request->query('quantity');
        $description = $request->query('description');
        $PDOrders = ProductionOrder::query();


        if ($product_id) {
            $PDOrders->where('product_id', $product_id);
        }

        if ($line_id) {
            $PDOrders->where('line_id', $line_id);
        }

        if ($factory_id) {
            $PDOrders->where('factory_id', $factory_id);
        }

        if ($quantity) {
            $PDOrders->where('quantity', $quantity);
        }

        if ($description) {
            $PDOrders->where('description', 'like', "%" . $description . "%");
        }
        if ($start_date) {
            $PDOrders->where('start_date', '>=', $start_date);
        }

        if ($end_date) {
            $PDOrders->where('end_date', '<=', $end_date);
        }

        $PDOrders = $PDOrders->orderByDesc('id')->get();
        return response()->json($PDOrders);
    }

    public function store(Request $request)
    {
        DB::beginTransaction(); // Bắt đầu transaction

        try {
            if (!empty($request->PDOrders)) {
                $validatedData = $request->validate([
                    'PDOrders.*.product_id' => 'required|exists:products,id',
                    'PDOrders.*.sale_order_id' => 'required|exists:sale_orders,id',
                    'PDOrders.*.line_id' => 'required|exists:lines,id',
                    'PDOrders.*.factory_id' => 'required|exists:factories,id',
                    'PDOrders.*.quantity' => 'required|integer|min:1',
                    'PDOrders.*.start_date' => 'nullable|date',
                    'PDOrders.*.end_date' => 'nullable|date|after_or_equal:PDOrders.*.start_date',
                    'PDOrders.*.description' => 'nullable|string|max:255',
                ]);
                $PDOrders = [];

                foreach ($validatedData['PDOrders'] as $order) {
                    $stockProduct = StockProduct::query()->where('product_id', $order['product_id'])->first();

                    $requiredProductQuantity = $order['quantity'];
                    $allocatedQuantity = AllocatedProduct::query()->where('product_id', $order['product_id'])->where('status', 'Allocated')->sum('allocated_quantity');
                    if (($stockProduct->quantity ?? 0) - $allocatedQuantity < $requiredProductQuantity) {
                        $productionQuantity = $requiredProductQuantity - (($stockProduct->quantity ?? 0) - $allocatedQuantity);
                    }

                    if (($stockProduct->quantity ?? 0) - $allocatedQuantity > 0) {
                        AllocatedProduct::query()->create([
                            'product_id' => $order['product_id'],
                            'allocated_quantity' => (($stockProduct->quantity ?? 0)) - $allocatedQuantity,
                            'sale_order_id' => $order['sale_order_id'],

                        ]);
                    }
                    $response = ProductionOrder::query()->create([
                        'code' => CodeGenerator::generateCode('production_orders', 'WO'),
                        'product_id' => $order['product_id'],
                        'line_id' => $order['line_id'],
                        'factory_id' => $order['factory_id'],
                        'quantity' => $productionQuantity ?? 0,
                        'start_date' => $order['start_date'],
                        'end_date' => $order['end_date'],
                        'description' => $order['description'],
                    ]);


                    $BOMs = BOM::query()->where('product_id', $order['product_id'])->get();
                    $materialsToPurchase = [];

                    foreach ($BOMs as $BOM) {
                        $stockMaterial = StockMaterial::query()->where('material_id', $BOM['material_id'])->first();
                        $material = Material::query()->where('id', $BOM['material_id'])->first();
                        $requiredQuantity = $BOM['quantity'] * ($productionQuantity ?? 0);

                        $allocatedQuantity = AllocatedMaterial::query()->where('material_id', $material->id)->where('status', 'Allocated')->sum('allocated_quantity');
                        if ((($stockMaterial->quantity ?? 0) - $allocatedQuantity) < $requiredQuantity) {
                            $materialsToPurchase[] = [
                                'code' => $material->code,
                                'material_id' => $stockMaterial->material_id ?? 0,
                                'quantity' => $requiredQuantity - (($stockMaterial->quantity ?? 0) - $allocatedQuantity),
                            ];
                        }
                        if (($stockMaterial->quantity ?? 0) - $allocatedQuantity > 0) {
                            AllocatedMaterial::query()->create([
                                'material_id' => $material->id,
                                'allocated_quantity' => ($stockMaterial->quantity ?? 0) - $allocatedQuantity,
                                'production_order_id' => $response->id,

                            ]);
                        }
                    }

                    $code = CodeGenerator::generateCode('purchase_requisitions', 'PU');
                    if (!empty($materialsToPurchase)) {
                        $purchaseRequisition = PurchaseRequisition::query()->create([
                            'code' => $code,
                            'status' => 'Pending',
                            'create_date' => Carbon::now()->setTimezone('Asia/Ho_Chi_Minh')
                        ]);
                        foreach ($materialsToPurchase as $materialToPurchase) {
                            PurchaseRequisitionItem::query()->create([
                                'material_code' => $materialToPurchase["code"],
                                'purchase_requisition_id' => $purchaseRequisition->id,
                                'material_id' => $materialToPurchase['material_id'],
                                'quantity' => $materialToPurchase['quantity'],
                            ]);
                        }
                    }
                    $response['key'] = $response['id'];
                    $PDOrders[] = $response;
                }

                $saleOrderIds = array_column($validatedData['PDOrders'], 'sale_order_id');
                $uniqueSaleOrderIds = array_unique($saleOrderIds);
                SaleOrder::query()->whereIn('id', $uniqueSaleOrderIds)->update([
                    "status" => "Pending Production"
                ]);


                DB::commit(); // Cam kết transaction

                return response()->json([
                    'message' => 'PDOrders stored successfully!',
                    'type' => 'success',
                    'data' => $PDOrders
                ], 201);
            } else {
                $validatedData = $request->validate([
                    'product_id' => 'required|exists:products,id',
                    'line_id' => 'required|exists:lines,id',
                    'factory_id' => 'required|exists:factories,id',
                    'quantity' => 'required|integer|min:1',
                    'start_date' => 'nullable|date',
                    'end_date' => 'nullable|date|after_or_equal:start_date',
                    'description' => 'nullable|string|max:255',
                ]);

                $response = ProductionOrder::query()->create([
                    'code' => CodeGenerator::generateCode('production_orders', 'WO'),
                    'product_id' => $validatedData['product_id'],
                    'line_id' => $validatedData['line_id'],
                    'factory_id' => $validatedData['factory_id'],
                    'quantity' => $validatedData['quantity'],
                    'start_date' => $validatedData['start_date'],
                    'end_date' => $validatedData['end_date'],
                    'description' => $validatedData['description'],
                ]);

                $BOMs = BOM::query()->where('product_id', $request->product_id)->get();
                $materialsToPurchase = [];

                foreach ($BOMs as $BOM) {
                    $stockMaterial = StockMaterial::query()->where('material_id', $BOM['material_id'])->first();
                    $material = Material::query()->where('id', $BOM['material_id'])->first();
                    $requiredQuantity = $BOM['quantity'] * $validatedData['quantity'];
                    $allocatedQuantity = AllocatedMaterial::query()->where('material_id', $material->id)->where('status', 'Allocated')->sum('allocated_quantity');
                    if (($stockMaterial->quantity) - $allocatedQuantity < $requiredQuantity) {
                        $materialsToPurchase[] = [
                            'material_code' => $material->code,
                            'material_id' => $stockMaterial->material_id,
                            'quantity' => $requiredQuantity - ($stockMaterial->quantity - $allocatedQuantity),
                        ];

                        if (($stockMaterial->quantity) - $allocatedQuantity > 0) {
                            AllocatedMaterial::query()->create([
                                'material_id' => $material->id,
                                'allocated_quantity' => ($stockMaterial->quantity) - $allocatedQuantity,
                                'production_order_id' => $response->id,

                            ]);
                        }

                    }
                }

                if (!empty($materialsToPurchase)) {
                    $purchaseRequisition = PurchaseRequisition::query()->create([
                        'code' => CodeGenerator::generateCode('purchase_requisitions', 'PU'),
                        'status' => 'Pending',
                        'create_date' => Carbon::now()->setTimezone('Asia/Ho_Chi_Minh')
                    ]);
                    foreach ($materialsToPurchase as $materialToPurchase) {
                        PurchaseRequisitionItem::query()->create([
                            'material_code' => $materialToPurchase["material_code"],
                            'purchase_requisition_id' => $purchaseRequisition->id,
                            'material_id' => $materialToPurchase['material_id'],
                            'quantity' => $materialToPurchase['quantity'],
                        ]);
                    }
                }

                if (!$response) {
                    return response()->json([
                        'message' => 'PDOrders stored fails!',
                        'type' => 'error'
                    ]);
                }

                DB::commit(); // Cam kết transaction

                return response()->json([
                    'message' => 'PDOrders stored successfully!',
                    'type' => 'success',
                    'data' => $response
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback nếu có lỗi xảy ra
            return response()->json([
                'message' => 'Error occurred: ' . $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'line_id' => 'required|exists:lines,id',
            'factory_id' => 'required|exists:factories,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:255',
        ]);
        $response = ProductionOrder::query()->where('id', $id)->update([
            'line_id' => $validatedData['line_id'],
            'factory_id' => $validatedData['factory_id'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'description' => $validatedData['description'],
        ]);
        if (!$response) {
            return response()->json([
                'message' => 'PDOrders update fails!',
                'type' => 'error'
            ]);
        }
        $PDOrder = ProductionOrder::query()->find($id);
        return response()->json([
            'message' => 'PDOrders update successfully!',
            'type' => 'success',
            'data' => $PDOrder
        ], 200);
    }

    public function destroy($id)
    {
        $PDOrder = ProductionOrder::query()->find($id);
        if (!$PDOrder) {
            return response()->json([
                "type" => "error",
                'message' => "Production Order does not exits"
            ]);
        }
        $PDOrder->delete();
        return response()->json([
            "type" => "success",
            'message' => "Delete Successfully"
        ]);
    }

}
