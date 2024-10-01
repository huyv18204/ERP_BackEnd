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
        $code = $request->query('code');

        $query = StockProduct::with(['product']);

        if ($product_id) {
            $query->where('product_id', $product_id);
        }

        if ($code) {
            $query->whereHas('product', function ($query) use ($code) {
                $query->where('code', $code);
            });
        }

        $stock = $query->get();
        return response()->json($stock);

    }
}
