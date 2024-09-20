<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $unit_price = $request->query('unit_price');
        $description = $request->query('description');

        $products = Product::query();

        if ($name) {
            $products->where('name', 'like', '%' . $name . '%');
        }
        if ($unit_price) {
            $products->where('unit_price', $unit_price);
        }
        if ($description) {
            $products->where('description', 'like', '%' . $description . '%');
        }

        $products = $products->orderByDesc("id")->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        if (empty($request->name) || empty($request->unit_price)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $code = CodeGenerator::generateCode('products', 'SP');
        $product = Product::query()->create([
            'code' => $code,
            'name' => $request->name,
            'description' => $request->description,
            'unit_price' => $request->unit_price,
        ]);
        if (!$product) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($product);
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::query()->find($id);
        if (!$product) {

            return response()->json(["message" => "Product does not exist"]);
        }
        $product->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {

        $product = Product::query()->find($id);
        if (!$product) {
            return response()->json(["message" => "Product does not exist"]);
        }

        if (empty($request->name) || empty($request->unit_price)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }

        $product = $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'unit_price' => $request->unit_price,
        ]);

        if (!$product) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
