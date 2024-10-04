<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Seed departments
        foreach (range(1, 10) as $index) {
            DB::table('departments')->insert([
                'department_code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'name' => $faker->word,
                'description' => $faker->sentence,
            ]);
        }

        // Seed lines
        foreach (range(1, 10) as $index) {
            DB::table('lines')->insert([
                'line_code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'name' => $faker->word,
                'description' => $faker->sentence,
            ]);
        }

        // Seed factories
        foreach (range(1, 10) as $index) {
            DB::table('factories')->insert([
                'factory_code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'name' => $faker->word,
                'description' => $faker->sentence,
            ]);
        }

        // Seed customers
        foreach (range(1, 10) as $index) {
            DB::table('customers')->insert([
                'customer_code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'name' => $faker->name,
                'phone' => $faker->phoneNumber,
                'fax' => $faker->phoneNumber,
                'address' => $faker->address,
            ]);
        }

        // Seed ng_types
        foreach (range(1, 10) as $index) {
            DB::table('ng_types')->insert([
                'ng_code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'name' => $faker->word,
                'description' => $faker->sentence,
            ]);
        }

        // Seed processes
        foreach (range(1, 10) as $index) {
            DB::table('processes')->insert([
                'process_code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'name' => $faker->word,
                'description' => $faker->sentence,
            ]);
        }

        // Seed sale_orders
        foreach (range(1, 10) as $index) {
            DB::table('sale_orders')->insert([
                'code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'order_date' => $faker->date,
                'customer_id' => rand(1, 10),
                'total_price' => rand(1000, 10000),
                'total_amount' => rand(1, 100),
                'status' => $faker->randomElement(['Pending', 'Pending Production', 'In Production', 'Completed', 'Cancelled', 'On Hold']),
            ]);
        }

        // Seed products
        foreach (range(1, 10) as $index) {
            DB::table('products')->insert([
                'code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'name' => $faker->word,
                'unit_price' => rand(100, 1000),
                'description' => $faker->sentence,
            ]);
        }

        // Seed sale_order_items
        foreach (range(1, 10) as $index) {
            DB::table('sale_order_items')->insert([
                'code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'sale_order_id' => rand(1, 10),
                'product_id' => rand(1, 10),
                'unit_price' => rand(100, 1000),
                'quantity' => rand(1, 50),
                'total_price' => rand(1000, 5000),
                'delivery_date' => $faker->date,
                'description' => $faker->sentence,
            ]);
        }

        // Seed product_processes
        foreach (range(1, 10) as $index) {
            DB::table('product_processes')->insert([
                'product_id' => rand(1, 10),
                'process_id' => rand(1, 10),
                'sequence' => rand(1, 5),
                'description' => $faker->sentence,
            ]);
        }

        // Seed product_ngs
        foreach (range(1, 10) as $index) {
            DB::table('product_ngs')->insert([
                'product_id' => rand(1, 10),
                'ng_type_id' => rand(1, 10),
                'description' => $faker->sentence,
            ]);
        }

        // Seed suppliers
        foreach (range(1, 10) as $index) {
            DB::table('suppliers')->insert([
                'supplier_code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'name' => $faker->company,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
            ]);
        }

        // Seed materials
        foreach (range(1, 10) as $index) {
            DB::table('materials')->insert([
                'material_code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'name' => $faker->word,
                'description' => $faker->sentence,
            ]);
        }

        // Seed boms (Bill of Materials)
        foreach (range(1, 10) as $index) {
            DB::table('boms')->insert([
                'product_id' => rand(1, 10),
                'material_id' => rand(1, 10),
                'quantity' => rand(1, 100),
            ]);
        }

        // Seed warehouse_entries
        foreach (range(1, 10) as $index) {
            DB::table('warehouse_entries')->insert([
                'entry_code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'date' => $faker->date,
                'supplier_id' => rand(1, 10),
                'status' => $faker->randomElement(['Pending', 'Completed', 'Cancelled']),
            ]);
        }

        // Seed warehouse_entry_details
        foreach (range(1, 10) as $index) {
            DB::table('warehouse_entry_details')->insert([
                'warehouse_entry_id' => rand(1, 10),
                'material_id' => rand(1, 10),
                'quantity' => rand(1, 100),
            ]);
        }

        // Seed purchase_requisitions
        foreach (range(1, 10) as $index) {
            DB::table('purchase_requisitions')->insert([
                'code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'date' => $faker->date,
                'status' => $faker->randomElement(['Pending', 'Approved', 'Rejected']),
            ]);
        }

        // Seed purchase_requisition_items
        foreach (range(1, 10) as $index) {
            DB::table('purchase_requisition_items')->insert([
                'purchase_requisition_id' => rand(1, 10),
                'material_id' => rand(1, 10),
                'quantity' => rand(1, 100),
            ]);
        }

        // Seed purchase_orders
        foreach (range(1, 10) as $index) {
            DB::table('purchase_orders')->insert([
                'code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'supplier_id' => rand(1, 10),
                'date' => $faker->date,
                'status' => $faker->randomElement(['Pending', 'Completed', 'Cancelled']),
            ]);
        }

        // Seed purchase_order_items
        foreach (range(1, 10) as $index) {
            DB::table('purchase_order_items')->insert([
                'purchase_order_id' => rand(1, 10),
                'material_id' => rand(1, 10),
                'quantity' => rand(1, 100),
                'unit_price' => rand(100, 1000),
            ]);
        }

        // Seed production_orders
        foreach (range(1, 10) as $index) {
            DB::table('production_orders')->insert([
                'code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'sale_order_id' => rand(1, 10),
                'product_id' => rand(1, 10),
                'quantity' => rand(1, 100),
                'status' => $faker->randomElement(['Pending', 'In Production', 'Completed', 'Cancelled']),
            ]);
        }

        // Seed stock_products
        foreach (range(1, 10) as $index) {
            DB::table('stock_products')->insert([
                'product_id' => rand(1, 10),
                'quantity' => rand(1, 100),
            ]);
        }

        // Seed stock_materials
        foreach (range(1, 10) as $index) {
            DB::table('stock_materials')->insert([
                'material_id' => rand(1, 10),
                'quantity' => rand(1, 100),
            ]);
        }

        // Seed stock_outs
        foreach (range(1, 10) as $index) {
            DB::table('stock_outs')->insert([
                'code' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'date' => $faker->date,
                'status' => $faker->randomElement(['Pending', 'Completed', 'Cancelled']),
            ]);
        }

        // Seed stock_out_items
        foreach (range(1, 10) as $index) {
            DB::table('stock_out_items')->insert([
                'stock_out_id' => rand(1, 10),
                'product_id' => rand(1, 10),
                'quantity' => rand(1, 50),
            ]);
        }
    }
}
