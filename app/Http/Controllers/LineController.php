<?php

namespace App\Http\Controllers;

use App\Models\Line;
use Illuminate\Http\Request;

class LineController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $description = $request->query('description');

        $lines = Line::query();

        if ($name) {
            $lines->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $lines->where('description', 'like', '%' . $description . '%');
        }

        $lines = $lines->orderByDesc("id")->get();
        return response()->json($lines);
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $currentDay = date('d');
        $currentMonth = date('m');
        $linePrev = Line::withTrashed()->orderByDesc('id')->first();
        if ($linePrev) {
            $lineCode = "LN" . $currentDay . $currentMonth . "-" . str_pad((int)$linePrev->id + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $lineCode = "LN" . $currentDay . $currentMonth . "-01";
        }
        $line = Line::query()->create([
            'line_code' => $lineCode,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if (!$line) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($line);
    }

    public function destroy(Request $request, $id)

    {
        $line = Line::query()->find($id);
        if (!$line) {
            return response()->json(["message" => "Line does not exist"]);
        }
        $line->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {
        $line = Line::query()->find($id);
        if (!$line) {
            return response()->json(["message" => "Line does not exist"]);
        }

        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $response = $line->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$response) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
