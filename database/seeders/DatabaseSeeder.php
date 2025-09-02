<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Product::create(['name' => 'Laptop', 'price' => 1200]);
        Product::create(['name' => 'Phone', 'price' => 800]);
        Product::create(['name' => 'Tablet', 'price' => 400]);
    }
}
