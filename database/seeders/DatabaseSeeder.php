<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ChartOfAccountsSeeder::class,
        ]);

        // Create dummy customers
        \App\Models\Customer::factory(3)->create();

        // Create dummy suppliers
        \App\Models\Supplier::factory(3)->create();
    }
}
