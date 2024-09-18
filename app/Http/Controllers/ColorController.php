<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $description = $request->query('description');

        $colors = Color::query();

        if ($name) {
            $colors->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $colors->where('description', 'like', '%' . $description . '%');
        }

        $colors = $colors->orderByDesc("id")->get();
        return response()->json($colors);
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $currentDay = date('d');
        $currentMonth = date('m');
        $colorPrev = Color::withTrashed()->orderByDesc('id')->first();

        if ($colorPrev) {
            $colorCode = "Cl" . $currentDay . $currentMonth . "-" . str_pad((int)$colorPrev->id + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $colorCode = "CL" . $currentDay . $currentMonth . "-01";
        }

        $color = Color::query()->create([
            'color_code' => $colorCode,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if (!$color) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($color);
    }

    public function destroy(Request $request, $id)

    {
        $color = Color::query()->find($id);
        if (!$color) {
            return response()->json(["message" => "Color does not exist"]);
        }
        $color->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {
        $color = Color::query()->find($id);
        if (!$color) {
            return response()->json(["message" => "Color does not exist"]);
        }

        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $response = $color->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$response) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
