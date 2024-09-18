<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $description = $request->query('description');

        $sizes = Size::query();

        if ($name) {
            $sizes->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $sizes->where('description', 'like', '%' . $description . '%');
        }

        $sizes = $sizes->orderByDesc("id")->get();
        return response()->json($sizes);
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $currentDay = date('d');
        $currentMonth = date('m');
        $sizePrev = Size::withTrashed()->orderByDesc('id')->first();
        if ($sizePrev) {
            $sizeCode = "SZ" . $currentDay . $currentMonth . "-" . str_pad((int)$sizePrev->id + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $sizeCode = "SZ" . $currentDay . $currentMonth . "-01";
        }

        $size = Size::query()->create([
            'size_code' => $sizeCode,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if (!$size) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($size);
    }

    public function destroy(Request $request, $id)

    {
        $size = Size::query()->find($id);
        if (!$size) {
            return response()->json(["message" => "Size does not exist"]);
        }
        $size->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {
        $size = Size::query()->find($id);
        if (!$size) {
            return response()->json(["message" => "Size does not exist"]);
        }

        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $response = $size->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$response) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
