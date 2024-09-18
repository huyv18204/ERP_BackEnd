<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Line;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $description = $request->query('description');

        $departments = Department::query();

        if ($name) {
            $departments->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $departments->where('phone', 'like', '%' . $description . '%');
        }

        $departments = $departments->orderByDesc("id")->get();
        return response()->json($departments);
    }

    public function store(Request $request)
    {
        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $currentDay = date('d');
        $currentMonth = date('m');
        $employeePrev = Department::withTrashed()->orderByDesc('id')->first();

        if ($employeePrev) {
            $departmentCode = "DP" . $currentDay . $currentMonth . "-" . str_pad((int)$employeePrev->id + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $departmentCode = "DP" . $currentDay . $currentMonth . "-01";
        }

        $department = Department::query()->create([
            'department_code' => $departmentCode,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if (!$department) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($department);
    }

    public function destroy(Request $request, $id)
    {
        $department = Department::query()->find($id);
        if (!$department) {

            return response()->json(["message" => "Department does not exist"]);
        }
        $department->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {

        $department = Department::query()->find($id);
        if (!$department) {
            return response()->json(["message" => "Department does not exist"]);
        }

        if (empty($request->name)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $department = $department->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$department) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }


}
