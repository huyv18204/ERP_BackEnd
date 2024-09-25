<?php

namespace App\Http\Controllers;

use App\Enums\SaleOrderStatus;
use App\Helpers\CodeGenerator;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\ProductNg;
use App\Models\ProductProcess;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\Stock;
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
            $query->with(['product', 'product_items']);
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

        $saleOrders = $saleOrders->orderByDesc('id')->get();
        return response()->json($saleOrders);
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if (empty($request->customer_id)) {
                return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
            }
            $saleOrder = SaleOrder::query()->create([
                'code' => CodeGenerator::generateCode('sale_orders', "SO"),
                'order_date' => Carbon::now()->setTimezone('Asia/Ho_Chi_Minh'),
                'customer_id' => $request->customer_id
            ]);
            if ($saleOrder) {
                if (!empty($request->saleOrderItem)) {
                    $saleOrderItems = $request->saleOrderItem;
                    $arrItems = [];
                    foreach ($saleOrderItems as $index => $item) {
                        if ($index == 0 && (empty($item['product_id']) || empty($item['delivery_date']))) {
                            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
                        }

                        if (!empty($item['product_id']) && !empty($item['delivery_date'])) {
                            $product = Product::query()->find($item['product_id']);
                            $saleOrderItem = SaleOrderItem::query()->create([
                                'code' => CodeGenerator::generateCode('sale_order_items', "SI"),
                                'sale_order_id' => $saleOrder->id,
                                'product_id' => $item['product_id'],
                                'unit_price' => $product->unit_price,
                                'delivery_date' => $item['delivery_date'],
                                'description' => $item['description'],
                            ]);
                            $saleOrderItem['key'] = $index + 1;
                            $arrItems[] = $saleOrderItem;
                        }

                    }
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


    public function destroy($id)
    {
        $saleOrder = SaleOrder::query()->find($id);
        if (!$saleOrder) {
            return response()->json([
                "type" => "error",
                "message" => "Sale order does not exits"
            ]);
        }
        $saleOrder->update([
            "status" => "Cancelled"
        ]);
        $saleOrderItems = SaleOrderItem::query()->where('sale_order_id', $saleOrder->id)->get();

        foreach ($saleOrderItems as $saleOrderItem) {
            ProductItem::query()->where("sale_order_item_id", $saleOrderItem['id'])->delete();
            ProductProcess::query()->where("sale_order_item_id", $saleOrderItem['id'])->delete();
            ProductNg::query()->where("sale_order_item_id", $saleOrderItem['id'])->delete();
        }

        SaleOrderItem::query()->where('sale_order_id', $saleOrder->id)->delete();
        $saleOrder->delete();
        return response()->json([
            "type" => "success",
            "message" => "Delete success"
        ]);
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

//        $saleOrderItems = SaleOrderItem::query()->where('sale_order_item',$id)->get();
//        foreach ($saleOrderItems as $saleOrderItem){
//            $productItems = ProductItem::query()->where('sale_order_item_id', $saleOrderItem['id'])->get();
//
//            foreach ($productItems as $productItem){
//                $stockProduct = Stock::query()->where('product_id')
//            }
//        }

        return response()->json([
            "type" => "success",
            "message" => "Update status success",
            "data" => $request->all()
        ]);
    }

}

