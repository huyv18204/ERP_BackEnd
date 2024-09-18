<?php

namespace App\Http\Controllers;

use App\Models\NgType;
use Illuminate\Http\Request;

class NgTypeController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $description = $request->query('description');

        $ngs = NgType::query();

        if ($name) {
            $ngs->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $ngs->where('description', 'like', '%' . $description . '%');
        }

        $ngs = $ngs->orderByDesc("id")->get();
        return response()->json($ngs);
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $currentDay = date('d');
        $currentMonth = date('m');
        $NgTypePrev = NgType::withTrashed()->orderByDesc('id')->first();

        if ($NgTypePrev) {
            $NgTypeCode = "NgType" . $currentDay . $currentMonth . "-" . str_pad((int)$NgTypePrev->id + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $NgTypeCode = "NgType" . $currentDay . $currentMonth . "-01";
        }
        $ng = NgType::query()->create([
            'ng_code' => $NgTypeCode,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if (!$ng) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($ng);
    }

    public function destroy(Request $request, $id)

    {
        $ng = NgType::query()->find($id);
        if (!$ng) {
            return response()->json(["message" => "NgType does not exist"]);
        }
        $ng->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {
        $ng = NgType::query()->find($id);
        if (!$ng) {
            return response()->json(["message" => "NgType does not exist"]);
        }

        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $response = $ng->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$response) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
