<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $phone = $request->query('phone');
        $address = $request->query('address');
        $department_id = $request->query('department_id');
        $work_date = $request->query('work_date');

        $employees = User::query()->with('department');

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
        if (empty($request->name) || empty($request->department_id) || empty($request->email) || empty($request->password) || empty($request->role)) {
            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
        }

        $employee = User::query()->create([
            'code' => CodeGenerator::generateCode('users', "EP"),
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'department_id' => $request->department_id,
            'work_date' => $request->work_date,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);
        if (!$employee) {
            return response()->json(["message" => "Create fails"]);
        }
        $employee->load('department');
        return response()->json($employee);
    }

    public function destroy(Request $request, $id)
    {
        $employee = User::query()->find($id);
        if (!$employee) {

            return response()->json(["message" => "Employee does not exist"]);
        }
        $employee->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {
        $employee = User::query()->find($id);
        if (!$employee) {
            return response()->json(["message" => "Employee does not exist"]);
        }

        if (empty($request->name) || empty($request->department_id) || empty($request->email) || empty($request->role)) {
            return response()->json(["type" => "error", "message" => "Please fill in required fields"]);
        }

        $employee = $employee->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'department_id' => $request->department_id,
            'work_date' => $request->work_date,
            'email' => $request->email,
//            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        if (!$employee) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
