<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $description = $request->query('description');

        $warehouses = Warehouse::query();

        if ($name) {
            $warehouses->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $warehouses->where('description', 'like', '%' . $description . '%');
        }

        $warehouses = $warehouses->orderByDesc("id")->get();
        return response()->json($warehouses);
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $code = CodeGenerator::generateCode('warehouses', 'WH');
        $warehouse = Warehouse::query()->create([
            'code' => $code,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if (!$warehouse) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($warehouse);
    }

    public function destroy(Request $request, $id)
    {
        $warehouse = Warehouse::query()->find($id);
        if (!$warehouse) {

            return response()->json(["message" => "Warehouse does not exist"]);
        }
        $warehouse->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {

        $warehouse = Warehouse::query()->find($id);
        if (!$warehouse) {
            return response()->json(["message" => "Warehouse does not exist"]);
        }

        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }

        $warehouse = $warehouse->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$warehouse) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
