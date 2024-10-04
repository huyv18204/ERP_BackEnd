<?php

namespace App\Http\Controllers;

use App\Enums\SaleOrderStatus;
use App\Helpers\CodeGenerator;
use App\Models\Product;
use App\Models\ProductNg;
use App\Models\ProductProcess;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\StockProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleOrderController extends Controller
{

    public function index(Request $request)
    {
        $customer_id = $request->query('customer_id');
        $start_date = $request->query('start_order_date');
        $end_date = $request->query('end_order_date');
        $code = $request->query('code');
        $saleOrders = SaleOrder::with(['customer', 'sale_order_items' => function ($query) {
            $query->with(['product']);
        }]);


        if ($customer_id) {
            $saleOrders->where('customer_id', $customer_id);
        }

        if ($code) {
            $saleOrders->where('code', $code);
        }
        if ($start_date) {
            $saleOrders->where('order_date', '>=', $start_date);
        }

        if ($end_date) {
            $saleOrders->where('order_date', '<=', $end_date);
        }

        $saleOrders = $saleOrders->get();
        return response()->json($saleOrders);
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra các trường cần thiết trước khi thực hiện các truy vấn
            if (empty($request->customer_id)) {
                return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
            }

            // Tạo đơn hàng mới
            $saleOrder = SaleOrder::query()->create([
                'code' => CodeGenerator::generateCode('sale_orders', "SO"),
                'order_date' => Carbon::now()->setTimezone('Asia/Ho_Chi_Minh'),
                'customer_id' => $request->customer_id
            ]);

            if ($saleOrder && !empty($request->saleOrderItem)) {
                $saleOrderItems = $request->saleOrderItem;
                $arrItems = [];
                $totalAmount = 0;
                $totalPrice = 0;

                foreach ($saleOrderItems as $index => $item) {
                    // Kiểm tra các trường bắt buộc cho mỗi sale order item
                    if (empty($item['product_id']) || empty($item['delivery_date']) || empty($item['quantity'])) {
                        return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                    }

                    // Tìm sản phẩm và tạo sale order item
                    $product = Product::query()->find($item['product_id']);
                    if ($product) {
                        $saleOrderItem = SaleOrderItem::query()->create([
                            'code' => CodeGenerator::generateCode('sale_order_items', "SI"),
                            'sale_order_id' => $saleOrder->id,
                            'product_id' => $item['product_id'],
                            'unit_price' => $product->unit_price,
                            'delivery_date' => $item['delivery_date'],
                            'description' => $item['description'],
                            'quantity' => $item['quantity'],
                            'total_price' => $product->unit_price * $item['quantity']
                        ]);

                        $saleOrderItem['key'] = $index + 1;
                        $arrItems[] = $saleOrderItem;

                        // Tính tổng số lượng và tổng giá trị của đơn hàng
                        $totalAmount += $item['quantity'];
                        $totalPrice += $product->unit_price * $item['quantity'];
                    }
                }

                // Cập nhật lại total_amount và total_price của SaleOrder sau khi tạo các SaleOrderItem
                $saleOrder->update([
                    'total_amount' => $totalAmount,
                    'total_price' => $totalPrice,
                ]);
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


    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $saleOrder = SaleOrder::query()->find($id);
            if (!$saleOrder) {
                return response()->json([
                    "type" => "error",
                    "message" => "Sale order does not exist"
                ]);
            }

            $saleOrder->update([
                "status" => "Cancelled"
            ]);
            $saleOrderItems = $saleOrder->sale_order_items;
            foreach ($saleOrderItems as $saleOrderItem) {
                ProductProcess::query()->where("sale_order_item_id", $saleOrderItem->id)->delete();
                ProductNg::query()->where("sale_order_item_id", $saleOrderItem->id)->delete();
            }

            SaleOrderItem::query()->where('sale_order_id', $saleOrder->id)->delete();

            $saleOrder->delete();

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
                new \Illuminate\Validation\Rules\Enum(SaleOrderStatus::class)
            ],
        ]);

        $saleOrder = SaleOrder::query()->find($id);
        if (!$saleOrder) {
            return response()->json([
                "type" => "error",
                "message" => "Sale order does not exits"
            ]);
        }
        $response = $saleOrder->update([
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

