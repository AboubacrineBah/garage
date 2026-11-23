<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $roleGerant = Role::where('name', 'gerant')->first();

        User::updateOrCreate(
            ['email' => 'admin@garage-moderne.local'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'), // À changer en production !
                'phone' => '+22370000000',
                'role_id' => $roleGerant?->id,
                'is_active' => true,
            ]
        );
    }
}
