<?php

namespace App\Http\Controllers;

use App\Models\StockMaterial;
use App\Models\StockProduct;
use Illuminate\Http\Request;

class StockProductController extends Controller
{
    public function index(Request $request)
    {
        $product_id = $request->query('product_id');
        $color_id = $request->query('color_id');
        $size_id = $request->query('size_id');
        $code = $request->query('code');

        $query = StockProduct::with(['product','color','size']);

        if ($product_id) {
            $query->where('product_id', $product_id);
        }

        if ($color_id) {
            $query->where('color_id', $color_id);
        }

        if ($size_id) {
            $query->where('size_id', $size_id);
        }

        if ($code) {
            $query->whereHas('material', function ($query) use ($code) {
                $query->where('code', $code);
            });
        }

        $stock = $query->get();
        return response()->json($stock);

    }
}
