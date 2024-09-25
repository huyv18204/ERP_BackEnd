<?php

namespace App\Http\Controllers;

use App\Models\ProductItem;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductItemController extends Controller
{

    public function show($id)
    {
        $productItem = ProductItem::query()->where('sale_order_item_id', $id)->get();
        $productItem = $productItem->map(function ($item) {
            $item['key'] = "key" . $item->id;
            return $item;
        });
        return response()->json(["data" => $productItem]);


    }

    public function update(Request $request, $id)
    {
        if (!$request->size_id || !$request->color_id || !$request->quantity) {
            return response()->json([
                "message" => "Please fill in all fields",
                "type" => "error"
            ]);
        }

        $productItem = ProductItem::query()->find($id);

        if (!$productItem) {
            return response()->json([
                "message" => "Product item not found",
                "type" => "error"
            ]);
        }

        $saleOrderItem = SaleOrderItem::query()->find($productItem->sale_order_item_id);
        if (!$saleOrderItem) {
            return response()->json([
                "message" => "Sale order item not found",
                "type" => "error"
            ]);
        }

        // Tính toán số lượng và giá trị mới
        $totalAmountDifference = $request->quantity - $productItem->quantity;
        $totalPriceDifference = ($request->quantity * $saleOrderItem->unit_price) - ($productItem->quantity * $saleOrderItem->unit_price);

        // Cập nhật sale_order_item
        $saleOrderItem->update([
            'total_amount' => $saleOrderItem->total_amount + $totalAmountDifference,
            'total_price' => $saleOrderItem->total_price + $totalPriceDifference,
        ]);

        // Cập nhật sale_order
        $saleOrder = SaleOrder::query()->find($saleOrderItem->sale_order_id);
        if ($saleOrder) {
            $saleOrder->update([
                'total_amount' => $saleOrder->total_amount + $totalAmountDifference,
                'total_price' => $saleOrder->total_price + $totalPriceDifference,
            ]);
        }

        // Cập nhật ProductItem
        $updatedProductItem = ProductItem::query()->where('id', $id)->update([
            "size_id" => $request->size_id,
            "color_id" => $request->color_id,
            "quantity" => $request->quantity,
            "description" => $request->description,
        ]);

        if (!$updatedProductItem) {
            return response()->json([
                "message" => "Update failed",
                "type" => "error"
            ]);
        }

        return response()->json([
            "message" => "Update successfully",
            "type" => "success",
        ]);
    }


    public function store(Request $request)
    {
        foreach ($request->size_color_quantity as $item) {
            if (empty($item['size_id']) || empty($item['color_id']) || empty($item['quantity'])) {
                return response()->json([
                    'type' => "error",
                    "message" => "Please fill in all fields"
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $totalPrice = 0;

            foreach ($request->size_color_quantity as $item) {
                $productItem = ProductItem::query()->create([
                    "sale_order_item_id" => $request->sale_order_item_id,
                    "size_id" => $item['size_id'],
                    "color_id" => $item['color_id'],
                    "quantity" => $item['quantity'],
                    "description" => $item['description'] ?? ''
                ]);

                if ($productItem) {
                    $saleOrderItem = SaleOrderItem::query()->find($productItem->sale_order_item_id);
                    if ($saleOrderItem) {
                        $totalAmount += $productItem->quantity;
                        $totalPrice += $productItem->quantity * $saleOrderItem->unit_price;
                    }
                }
            }

            // Cập nhật sale_order_item
            $saleOrderItem->update([
                'total_amount' => $saleOrderItem->total_amount + $totalAmount,
                'total_price' => $saleOrderItem->total_price + $totalPrice,
            ]);

            // Cập nhật sale_order
            $saleOrder = SaleOrder::query()->find($saleOrderItem->sale_order_id);
            $saleOrder->update([
                'total_amount' => $saleOrder->total_amount + $totalAmount,
                'total_price' => $saleOrder->total_price + $totalPrice,
            ]);

            DB::commit();

            return response()->json([
                "message" => "Add successfully",
                "type" => "success"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'type' => "error",
                "message" => "Failed to add items: " . $e->getMessage()
            ]);
        }
    }


    public function destroy($id)
    {
        $productItem = ProductItem::query()->find($id);

        if (!$productItem) {
            return response()->json([
                "message" => "Item does not exist",
                "type" => "error"
            ]);
        }

        // Lấy thông tin SaleOrderItem trước khi xóa ProductItem
        $saleOrderItem = SaleOrderItem::query()->find($productItem->sale_order_item_id);
        if (!$saleOrderItem) {
            return response()->json([
                "message" => "Sale order item not found",
                "type" => "error"
            ]);
        }

        // Tính toán lại total_amount và total_price cho SaleOrderItem
        $totalAmountDifference = $productItem->quantity;
        $totalPriceDifference = $productItem->quantity * $saleOrderItem->unit_price;

        // Cập nhật SaleOrderItem
        $saleOrderItem->update([
            'total_amount' => $saleOrderItem->total_amount - $totalAmountDifference,
            'total_price' => $saleOrderItem->total_price - $totalPriceDifference,
        ]);

        // Cập nhật SaleOrder
        $saleOrder = SaleOrder::query()->find($saleOrderItem->sale_order_id);
        if ($saleOrder) {
            $saleOrder->update([
                'total_amount' => $saleOrder->total_amount - $totalAmountDifference,
                'total_price' => $saleOrder->total_price - $totalPriceDifference,
            ]);
        }

        // Xóa ProductItem
        $productItem->delete();

        return response()->json([
            "message" => "Delete success",
            "type" => "success"
        ]);
    }


}
