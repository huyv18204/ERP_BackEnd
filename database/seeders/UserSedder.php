<?php

namespace Database\Seeders;

use App\Helpers\CodeGenerator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSedder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            'department_code' => "DP-001",
           'name' => "Dev"
        ]);

        DB::table('users')->insert([
            'department_id' => 1,
            'name' => "Huy",
            'code' => CodeGenerator::generateCode("users", 'EP'),
            'email' => "vqh8124@gmail.com",
            'password' => Hash::make('password'),
        ]);
    }
}
