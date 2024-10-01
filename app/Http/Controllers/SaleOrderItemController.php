<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\Product;
use App\Models\ProductNg;
use App\Models\ProductProcess;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleOrderItemController extends Controller
{
    public function index()
    {
        $saleOrderItem = SaleOrderItem::query()->get();
        return response()->json($saleOrderItem);
    }

    public function show($id)
    {
        $saleOrderItem = SaleOrderItem::query()->where('sale_order_id', $id)->get();
        $saleOrderItem = $saleOrderItem->map(function ($item) {
            $item['key'] = "key" . $item->id;
            return $item;
        });
        return response()->json(["data" => $saleOrderItem]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $saleOrder = SaleOrder::query()->find($request->id);
            if ($saleOrder) {
                if (!empty($request->saleOrderItem)) {
                    $saleOrderItems = $request->saleOrderItem;

                    // Validate trước khi xử lý
                    foreach ($saleOrderItems as $item) {
                        if (empty($item['product_id']) || empty($item['delivery_date']) || empty($item['quantity'])) {
                            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                        }
                    }

                    $productIds = array_column($saleOrderItems, 'product_id');
                    $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');
                    $arrItems = [];
                    $totalQuantity = 0;
                    $totalPrice = 0;

                    foreach ($saleOrderItems as $index => $item) {
                        $product = $products[$item['product_id']] ?? null;

                        if ($product) {
                            $saleOrderItem = SaleOrderItem::query()->create([
                                'code' => CodeGenerator::generateCode('sale_order_items', "SI"),
                                'sale_order_id' => $request->id,
                                'product_id' => $item['product_id'],
                                'delivery_date' => $item['delivery_date'],
                                'description' => $item['description'],
                                'unit_price' => $product->unit_price,
                                'quantity' => $item['quantity'],
                                'total_price' => $product->unit_price * $item['quantity']
                            ]);
                            $saleOrderItem['key'] = $index + 1;
                            $arrItems[] = $saleOrderItem;

                            // Tính tổng quantity và total_price
                            $totalQuantity += $saleOrderItem->quantity;
                            $totalPrice += $saleOrderItem->quantity * $saleOrderItem->unit_price;
                        }
                    }

                    // Cập nhật SaleOrder một lần sau khi xử lý tất cả các items
                    $saleOrder->update([
                        'total_amount' => $saleOrder->total_amount + $totalQuantity,
                        'total_price' => $saleOrder->total_price + $totalPrice,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                "data" => [
                    "sale_order" => $saleOrder,
                    "sale_order_item" => $arrItems
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
        try {
            // Kiểm tra dữ liệu đầu vào trước khi tìm SaleOrderItem
            if (empty($request->product_id) || empty($request->delivery_date) || empty($request->quantity)) {
                return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
            }

            $saleOrderItem = SaleOrderItem::query()->find($id);
            if (!$saleOrderItem) {
                return response()->json([
                    "type" => "error",
                    'message' => "Sale order item does not exist"
                ]);
            }

            $saleOrder = SaleOrder::query()->find($saleOrderItem->sale_order_id);
            if ($saleOrder) {

                $totalQuantity = $saleOrder->sale_order_items->sum('quantity') - $saleOrderItem->quantity + $request->quantity;
                $totalPrice = $saleOrder->sale_order_items->sum(function ($item) use ($saleOrderItem, $request) {
                    return $item->id === $saleOrderItem->id
                        ? $request->quantity * $saleOrderItem->unit_price
                        : $item->total_price;
                });

                $saleOrder->update([
                    'total_amount' => $totalQuantity,
                    'total_price' => $totalPrice,
                ]);
            }

            // Cập nhật SaleOrderItem
            $response = $saleOrderItem->update([
                "quantity" => $request->quantity,
                "delivery_date" => $request->delivery_date,
                "description" => $request->description,
                "total_price" => $saleOrderItem->unit_price * $request->quantity
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
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $saleOrderItem = SaleOrderItem::query()->find($id);
            if (!$saleOrderItem) {
                return response()->json([
                    "type" => "error",
                    "message" => "Sale order item does not exist"
                ]);
            }

            // Xóa các bảng liên quan đến sale order item
            ProductProcess::query()->where("sale_order_item_id", $saleOrderItem->id)->delete();
            ProductNg::query()->where("sale_order_item_id", $saleOrderItem->id)->delete();

            $saleOrder = SaleOrder::query()->find($saleOrderItem->sale_order_id);
            if ($saleOrder) {
                // Tính lại tổng quantity và total price từ các SaleOrderItem còn lại
                $totalQuantity = $saleOrder->sale_order_items->sum('quantity') - $saleOrderItem->quantity;
                $totalPrice = $saleOrder->sale_order_items->sum('total_price') - $saleOrderItem->total_price;

                // Cập nhật SaleOrder
                $saleOrder->update([
                    'total_amount' => $totalQuantity,
                    'total_price' => $totalPrice,
                ]);
            }

            // Xóa SaleOrderItem
            $saleOrderItem->delete();

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
