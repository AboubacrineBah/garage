<?php

namespace Database\Seeders;

use App\Models\Assurance;
use Illuminate\Database\Seeder;

class AssuranceSeeder extends Seeder
{
    public function run(): void
    {
        $assurances = [
            ['nom' => 'NSIA Assurances', 'code' => 'NSIA', 'telephone' => '+223 20 23 00 00', 'email' => 'contact@nsia.local'],
            ['nom' => 'AXA Assurances Mali', 'code' => 'AXA', 'telephone' => '+223 20 21 00 00', 'email' => 'contact@axa.local'],
            ['nom' => 'Saham Assurance', 'code' => 'SAHAM', 'telephone' => '+223 20 22 00 00', 'email' => 'contact@saham.local'],
            ['nom' => 'Allianz Mali', 'code' => 'ALLIANZ', 'telephone' => '+223 20 24 00 00', 'email' => 'contact@allianz.local'],
        ];

        foreach ($assurances as $a) {
            Assurance::updateOrCreate(['code' => $a['code']], array_merge($a, ['is_active' => true]));
        }
    }
}
