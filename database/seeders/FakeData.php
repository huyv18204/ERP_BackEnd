<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Factory;
use App\Models\Line;
use App\Models\Material;
use App\Models\Product;
use App\Models\StockMaterial;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakeData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Line::query()->create([
            'line_code' => "LN-001",
           'name' => "Line 1"
        ]);
        Factory::query()->create([
            'factory_code' => "FT-001",
            'name' => "Factory 1"
        ]);

        Product::query()->create([
            'code' => "PR-001",
            'name' => "Áo",
            'unit_price' => 100000
        ]);

        Customer::query()->create([
            'customer_code' => "CS-001",
            'name' => "Lê Thanh Hằng"
        ]);

        Supplier::query()->create([
            'code' => "SP-001",
            'name' => "Lê Thanh Huy"
        ]);

    }
}
