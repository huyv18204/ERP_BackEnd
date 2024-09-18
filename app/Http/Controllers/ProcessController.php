<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $description = $request->query('description');

        $processes = Process::query();

        if ($name) {
            $processes->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $processes->where('description', 'like', '%' . $description . '%');
        }

        $processes = $processes->orderByDesc("id")->get();
        return response()->json($processes);
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $currentDay = date('d');
        $currentMonth = date('m');
        $processPrev = Process::withTrashed()->orderByDesc('id')->first();

        if ($processPrev) {
            $processCode = "PC" . $currentDay . $currentMonth . "-" . str_pad((int)$processPrev->id + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $processCode = "PC" . $currentDay . $currentMonth . "-01";
        }
        $process = Process::query()->create([
            'process_code' => $processCode,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if (!$process) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($process);
    }

    public function destroy(Request $request, $id)

    {
        $process = Process::query()->find($id);
        if (!$process) {
            return response()->json(["message" => "Process does not exist"]);
        }
        $process->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {
        $process = Process::query()->find($id);
        if (!$process) {
            return response()->json(["message" => "Process does not exist"]);
        }

        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $response = $process->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$response) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
