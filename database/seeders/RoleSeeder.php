<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::query()->firstOrCreate(
            ['slug' => Role::SLUG_ADMIN],
            ['name' => 'Administrator']
        );
        Role::query()->firstOrCreate(
            ['slug' => Role::SLUG_CASHIER],
            ['name' => 'Cashier']
        );
    }
}
