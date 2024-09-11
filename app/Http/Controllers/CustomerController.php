<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->query('name');
        $phone = $request->query('phone');
        $address = $request->query('address');
        $fax = $request->query('fax');

        $customers = Customer::query();

        if ($name) {
            $customers->where('name', 'like', '%' . $name . '%');
        }
        if ($phone) {
            $customers->where('phone', $phone);
        }
        if ($address) {
            $customers->where('address', 'like', '%' . $address . '%');
        }

        if ($fax) {
            $customers->where('fax', $fax);
        }

        $customers = $customers->orderByDesc("id")->get();
        return response()->json($customers);
    }

    public function store(Request $request)
    {
        if (empty($request->name) || empty($request->phone || empty($request->fax))) {
            return response()->json(["message" => "Please fill in required fields"]);
        }
        $currentDay = date('d');
        $currentMonth = date('m');
        $prevCode = "CS" . $currentDay . $currentMonth;

        $stt = DB::table('customers')->where("customer_code", "LIKE", $prevCode . "%")->orderByDesc('id')->first();
        if ($stt) {
            $parts = explode('-', $stt->customer_code);
            $lastPart = (int)end($parts) + 1;
            $customer_code = $prevCode . '-' . str_pad($lastPart, 3, '0', STR_PAD_LEFT);
        } else {
            $customer_code = $prevCode . '-' . "001";
        }

        $customer = Customer::query()->create([
            'customer_code' => $customer_code,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'fax' => $request->fax,
        ]);
        if (!$customer) {
            return response()->json(["message" => "Create fails"]);
        }
        return response()->json($customer);
    }

    public function destroy(Request $request, $id)
    {
        $customer = Customer::query()->find($id);
        if (!$customer) {

            return response()->json(["message" => "Customer does not exist"]);
        }
        $customer->delete();
        return response()->json(["message" => "Delete successfully"]);
    }

    public function update(Request $request, $id)
    {

        $customer = Customer::query()->find($id);
        if (!$customer) {
            return response()->json(["message" => "Customer does not exist"]);
        }

        if (empty($request->name) || empty($request->phone || empty($request->fax))) {
            return response()->json(["message" => "Please fill in required fields"]);
        }

        $customer = $customer->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'fax' => $request->fax,
        ]);

        if (!$customer) {
            return response()->json(["message" => "Updated fails"]);
        }
        return response()->json(["message" => "Updated successfully"]);
    }
}
