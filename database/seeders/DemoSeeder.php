<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\OrdreReparation;
use App\Models\PieceDetachee;
use App\Models\Vehicule;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $client = Client::create([
            'type' => 'particulier', 'nom' => 'Traoré', 'prenom' => 'Fatou',
            'telephone' => '76123456', 'ville' => 'Bamako',
        ]);

        $vehicule = Vehicule::create([
            'client_id' => $client->id, 'immatriculation' => 'CD-789-EF',
            'marque' => 'Renault', 'modele' => 'Clio', 'annee' => 2018,
            'kilometrage_actuel' => 42000, 'type_carburant' => 'essence',
            'couleur' => 'Rouge', 'photo_path' => null,
        ]);

        OrdreReparation::create([
            'numero' => 'OR-2026-00099', 'client_id' => $client->id, 'vehicule_id' => $vehicule->id,
            'statut' => 'devis', 'date_entree' => now(), 'kilometrage_entree' => 42000,
            'description_travaux' => 'Remplacement plaquettes de frein + peinture pare-choc',
            'montant_ht' => 45000, 'montant_ttc' => 45000, 'reste_a_payer' => 45000,
            'created_by' => 1,
        ]);

        PieceDetachee::create([
            'reference' => 'PLA-001', 'designation' => 'Plaquettes de frein avant',
            'categorie' => 'freinage', 'marque' => 'Bosch',
            'prix_achat' => 12000, 'prix_vente' => 18000,
            'stock_actuel' => 12, 'stock_minimum' => 4, 'unite' => 'piece',
            'emplacement' => 'Rayon A1', 'is_active' => true,
        ]);

        PieceDetachee::create([
            'reference' => 'PEI-ROUGE', 'designation' => 'Base peinture rouge',
            'categorie' => 'peinture', 'marque' => 'PPG',
            'prix_achat' => 25000, 'prix_vente' => 35000,
            'stock_actuel' => 1500, 'stock_minimum' => 300, 'unite' => 'ml',
            'emplacement' => 'Labo', 'is_active' => true,
        ]);
    }
}
