<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $phone = $request->query('phone');
        $address = $request->query('address');
        $department_id = $request->query('department_id');
        $work_date = $request->query('work_date');

        $employees = Employee::query()->with('department');

        if ($name) {
            $employees->where('name', 'like', '%' . $name . '%');
        }
        if ($phone) {
            $employees->where('phone', $phone);
        }
        if ($address) {
            $employees->where('address', 'like', '%' . $address . '%');
        }

        if ($department_id) {
            $employees->where('department_id', $department_id);
        }

        if ($work_date) {
            $employees->where('work_date', $work_date);
        }

        $employees = $employees->orderByDesc("id")->get();
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        if (empty($request->name) || empty($request->department_id)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $currentDay = date('d');
        $currentMonth = date('m');
        $prevCode = "EP" . $currentDay . $currentMonth;

        $stt = DB::table('employees')->where("employee_code", "LIKE", $prevCode . "%")->orderByDesc('id')->first();
        if ($stt) {
            $parts = explode('-', $stt->employee_code);
            $lastPart = (int)end($parts) + 1;
            $employee_code = $prevCode . '-' . str_pad($lastPart, 3, '0', STR_PAD_LEFT);
        } else {
            $employee_code = $prevCode . '-' . "001";
        }

        $employee = Employee::query()->create([
            'employee_code' => $employee_code,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'department_id' => $request->department_id,
            'work_date' => $request->work_date,
        ]);
        if (!$employee) {
            return response()->json(["message" => "Create fails"]);
        }
        $employee->load('department');
        return response()->json($employee);
    }

    public function destroy(Request $request, $id)
    {
        $employee = Employee::query()->where('id', $id)->first();
        if (!$employee) {

            return response()->json(["message" => "Employee does not exist"]);
        }
        $employee->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {
        if (empty($request->name) || empty($request->department_id)) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $employee = Employee::query()->where('id', $id)->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'department_id' => $request->department_id,
            'work_date' => $request->work_date,
        ]);

        if (!$employee) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
