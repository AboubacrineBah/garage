<?php

namespace Database\Seeders;

use App\Models\Devise;
use Illuminate\Database\Seeder;

class DeviseSeeder extends Seeder
{
    public function run(): void
    {
        $devises = [
            [
                'code' => 'XOF',
                'nom' => 'Franc CFA BCEAO',
                'symbole' => 'F CFA',
                'taux_vers_xof' => 1,
                'decimales' => 0,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'code' => 'XAF',
                'nom' => 'Franc CFA BEAC',
                'symbole' => 'F CFA',
                'taux_vers_xof' => 1,
                'decimales' => 0,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'EUR',
                'nom' => 'Euro',
                'symbole' => '€',
                'taux_vers_xof' => 655.957,
                'decimales' => 2,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'USD',
                'nom' => 'Dollar US',
                'symbole' => '$',
                'taux_vers_xof' => 600.00, // Taux indicatif, à mettre à jour
                'decimales' => 2,
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($devises as $data) {
            Devise::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}
