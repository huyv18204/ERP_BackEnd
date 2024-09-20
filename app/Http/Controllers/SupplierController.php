<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $phone = $request->query('phone');
        $address = $request->query('address');

        $suppliers = Supplier::query();

        if ($name) {
            $suppliers->where('name', 'like', '%' . $name . '%');
        }
        if ($phone) {
            $suppliers->where('phone', $phone);
        }
        if ($address) {
            $suppliers->where('address', 'like', '%' . $address . '%');
        }

        $suppliers = $suppliers->orderByDesc("id")->get();
        return response()->json($suppliers);
    }

    public function store(Request $request)
    {
        if (empty($request->name) || empty($request->phone)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $code = CodeGenerator::generateCode('suppliers', 'SP');
        $supplier = Supplier::query()->create([
            'code' => $code,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);
        if (!$supplier) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($supplier);
    }

    public function destroy(Request $request, $id)
    {
        $supplier = Supplier::query()->find($id);
        if (!$supplier) {

            return response()->json(["message" => "Supplier does not exist"]);
        }
        $supplier->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {

        $supplier = Supplier::query()->find($id);
        if (!$supplier) {
            return response()->json(["message" => "Supplier does not exist"]);
        }

        if (empty($request->name) || empty($request->phone)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }

        $supplier = $supplier->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        if (!$supplier) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
