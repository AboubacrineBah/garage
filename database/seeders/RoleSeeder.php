<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'gerant',
                'display_name' => 'Gérant',
                'permissions' => ['*'], // Accès total
                'description' => 'Accès complet à toutes les fonctionnalités',
            ],
            [
                'name' => 'receptionniste',
                'display_name' => 'Réceptionniste',
                'permissions' => [
                    'clients.view', 'clients.create', 'clients.edit',
                    'vehicules.view', 'vehicules.create', 'vehicules.edit',
                    'ordres.view', 'ordres.create', 'ordres.edit',
                    'notifications.send', 'stock.view',
                ],
                'description' => 'Accueil clients, création d\'ordres et devis',
            ],
            [
                'name' => 'chef_atelier',
                'display_name' => 'Chef d\'atelier',
                'permissions' => [
                    'ordres.view', 'ordres.edit', 'ordres.assign',
                    'stock.view', 'stock.sortie',
                    'vehicules.view',
                ],
                'description' => 'Gestion technique des ordres et attribution des mécaniciens',
            ],
            [
                'name' => 'mecanicien',
                'display_name' => 'Mécanicien',
                'permissions' => [
                    'ordres.view_assigned', 'ordres.update_status',
                    'vehicules.view',
                ],
                'description' => 'Consultation et mise à jour des ordres assignés',
            ],
            [
                'name' => 'peintre',
                'display_name' => 'Peintre',
                'permissions' => [
                    'ordres.view_assigned', 'ordres.update_status',
                    'stock.view', 'stock.sortie',
                    'vehicules.view',
                ],
                'description' => 'Suivi cabine peinture, préparation et finition',
            ],
            [
                'name' => 'comptable',
                'display_name' => 'Comptable',
                'permissions' => [
                    'ordres.view', 'ordres.facturer',
                    'clients.view', 'clients.credit',
                    'rapports.view', 'paiements.manage',
                ],
                'description' => 'Facturation, encaissements et rapports financiers',
            ],
        ];

        foreach ($roles as $data) {
            Role::updateOrCreate(['name' => $data['name']], $data);
        }
    }
}
