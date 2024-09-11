<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use Illuminate\Http\Request;

class FactoryController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $description = $request->query('description');

        $factories = Factory::query();

        if ($name) {
            $factories->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $factories->where('phone', 'like', '%' . $description . '%');
        }

        $factories = $factories->orderByDesc("id")->get();
        return response()->json($factories);
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $currentDay = date('d');
        $currentMonth = date('m');
        $factoryPrev = Factory::withTrashed()->orderByDesc('id')->first();
        $factory = Factory::query()->create([
            'factory_code' => "FA" . $currentDay . $currentMonth . "-" . str_pad((int)$factoryPrev->id + 1, 2, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if (!$factory) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($factory);
    }

    public function destroy(Request $request, $id)

    {
        $factory = Factory::query()->find($id);
        if (!$factory) {
            return response()->json(["message" => "Factory does not exist"]);
        }
        $factory->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {
        $factory = Factory::query()->find($id);
        if (!$factory) {
            return response()->json(["message" => "Factory does not exist"]);
        }

        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $response = $factory->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$response) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
