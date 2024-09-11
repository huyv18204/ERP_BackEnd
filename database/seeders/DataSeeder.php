<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentDay = date('d');
        $currentMonth = date('m');

        for ($i = 0; $i < 10; $i++) {
            DB::table('departments')->insert([
                'department_code' => "DP" . $currentDay . $currentMonth . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'name' => "Develop" . $i + 1,
                'description' => "Description " . $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $currentDay = date('d');
        $currentMonth = date('m');
        $prevCode = "EP" . $currentDay . $currentMonth;
        foreach (range(1, 10) as $index) {
            $stt = DB::table('employees')->where("employee_code", "LIKE", $prevCode . "%")->orderByDesc('id')->first();
            if ($stt) {
                $parts = explode('-', $stt->employee_code); // Tách phần cuối cùng của mã nhân viên
                $lastPart = (int)end($parts) + 1; // Tăng phần số lên 1
                $employee_code = $prevCode . '-' . str_pad($lastPart, 3, '0', STR_PAD_LEFT); // Đảm bảo luôn có 3 chữ số
            } else {
                $employee_code = $prevCode . '-' . "001"; // Nếu không có mã nào trước đó, bắt đầu từ 001
            }
            DB::table('employees')->insert([
                'employee_code' => $employee_code,
                'name' => fake()->name,
                'address' => fake()->address,
                'phone' => fake()->phoneNumber,
                'work_date' => "2022-11-09",
                'department_id' => rand(1, 10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (range(1, 10) as $index) {
            $stt = DB::table('customers')->where("customer_code", "LIKE", $prevCode . "%")->orderByDesc('id')->first();
            if ($stt) {
                $parts = explode('-', $stt->customer_code); // Tách phần cuối cùng của mã nhân viên
                $lastPart = (int)end($parts) + 1; // Tăng phần số lên 1
                $customer_code = $prevCode . '-' . str_pad($lastPart, 2, '0', STR_PAD_LEFT);
            } else {
                $customer_code = $prevCode . '-' . "01";
            }
            DB::table('customers')->insert([
                'customer_code' => $customer_code,
                'name' => fake()->name,
                'address' => fake()->address,
                'phone' => fake()->phoneNumber,
                'fax' => "011122223",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            DB::table('lines')->insert([
                'line_code' => "LN" . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'name' => "Line " . $i + 1,
                'description' => "Description " . $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            DB::table('factories')->insert([
                'factory_code' => "FA" . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'name' => "Factory " . $i + 1,
                'description' => "Description " . $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        for ($i = 1; $i <= 5; $i++) {
            DB::table('menus')->insert([
                'label' => fake()->word(),
                'parent' => null,
                'icon' => fake()->randomElement(['PieChartOutlined', 'DesktopOutlined', 'UserOutlined', 'TeamOutlined', 'FileOutlined']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        for ($i = 1; $i <= 10; $i++) {
            $parent = fake()->numberBetween(1, 5);
            DB::table('menus')->insert([
                'label' => fake()->word(),
                'parent' => $parent,
                'icon' => null,
                'url' => "/erp-system/employees",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
