<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DeviseSeeder::class,
            AdminUserSeeder::class,
            ParametreSeeder::class,
            AssuranceSeeder::class,
        ]);
    }
}
