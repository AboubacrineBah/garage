<?php

namespace Database\Seeders;

use App\Models\Assurance;
use App\Models\Client;
use App\Models\ComposantFormulation;
use App\Models\Devise;
use App\Models\FormulationPeinture;
use App\Models\InspectionCarrosserie;
use App\Models\LigneOrdre;
use App\Models\MouvementStock;
use App\Models\NotificationLog;
use App\Models\OrdreReparation;
use App\Models\PieceDetachee;
use App\Models\Role;
use App\Models\Sinistre;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FullDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Rôles déjà seedés par RoleSeeder, mais on s'assure
        $gerant = Role::firstOrCreate(['name' => 'gerant'], ['display_name' => 'Gérant', 'permissions' => ['*']]);

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@garage-moderne.local'],
            ['name' => 'Administrateur', 'password' => Hash::make('password'), 'phone' => '+22370000000', 'role_id' => $gerant->id, 'is_active' => true]
        );

        // Client 1
        $client1 = Client::firstOrCreate(
            ['telephone' => '76123456'],
            ['type' => 'particulier', 'nom' => 'Traoré', 'prenom' => 'Fatou', 'ville' => 'Bamako', 'credit_autorise' => 100000, 'created_by' => $admin->id]
        );
        $vehicule1 = Vehicule::firstOrCreate(
            ['immatriculation' => 'CD-789-EF'],
            ['client_id' => $client1->id, 'marque' => 'Renault', 'modele' => 'Clio', 'annee' => 2018, 'couleur' => 'Rouge', 'kilometrage_actuel' => 42000, 'type_carburant' => 'essence']
        );

        // Client 2
        $client2 = Client::firstOrCreate(
            ['telephone' => '70000000'],
            ['type' => 'particulier', 'nom' => 'Diallo', 'prenom' => 'Amadou', 'ville' => 'Bamako', 'created_by' => $admin->id]
        );
        $vehicule2 = Vehicule::firstOrCreate(
            ['immatriculation' => 'AA-123-BB'],
            ['client_id' => $client2->id, 'marque' => 'Toyota', 'modele' => 'Corolla', 'annee' => 2020, 'couleur' => 'Blanc', 'kilometrage_actuel' => 85000, 'type_carburant' => 'essence']
        );

        // Pièces
        $pieceFrein = PieceDetachee::firstOrCreate(
            ['reference' => 'PLA-001'],
            ['designation' => 'Plaquettes de frein avant', 'categorie' => 'freinage', 'marque' => 'Bosch', 'prix_achat' => 12000, 'prix_vente' => 18000, 'stock_actuel' => 12, 'stock_minimum' => 4, 'unite' => 'piece', 'emplacement' => 'Rayon A1', 'is_active' => true]
        );
        $piecePeinture = PieceDetachee::firstOrCreate(
            ['reference' => 'PEI-ROUGE'],
            ['designation' => 'Base peinture rouge', 'categorie' => 'peinture', 'marque' => 'PPG', 'prix_achat' => 25000, 'prix_vente' => 35000, 'stock_actuel' => 1500, 'stock_minimum' => 300, 'unite' => 'ml', 'emplacement' => 'Labo', 'is_active' => true]
        );
        $pieceVernis = PieceDetachee::firstOrCreate(
            ['reference' => 'VER-001'],
            ['designation' => 'Vernis brillant', 'categorie' => 'vernis', 'marque' => 'PPG', 'prix_achat' => 18000, 'prix_vente' => 26000, 'stock_actuel' => 800, 'stock_minimum' => 200, 'unite' => 'ml', 'emplacement' => 'Labo', 'is_active' => true]
        );

        // Mouvements stock
        MouvementStock::firstOrCreate(
            ['piece_id' => $pieceFrein->id, 'type' => 'entree', 'commentaire' => 'Stock initial'],
            ['quantite' => 12, 'user_id' => $admin->id]
        );

        // Inspection 360°
        InspectionCarrosserie::firstOrCreate(
            ['vehicule_id' => $vehicule1->id],
            ['user_id' => $admin->id, 'kilometrage' => 42000, 'code_teinte' => 'L90E', 'zones_choquees' => 'choc pare-choc avant gauche', 'date_inspection' => now()]
        );

        // Ordre 1 (devis, peinture)
        $ordre1 = OrdreReparation::firstOrCreate(
            ['numero' => 'OR-2026-00099'],
            [
                'client_id' => $client1->id, 'vehicule_id' => $vehicule1->id, 'statut' => 'devis',
                'date_entree' => now(), 'kilometrage_entree' => 42000,
                'description_travaux' => 'Remplacement plaquettes de frein + peinture pare-choc',
                'montant_ht' => 45000, 'montant_ttc' => 45000, 'reste_a_payer' => 45000,
                'devise_id' => Devise::where('code', 'XOF')->first()?->id,
                'created_by' => $admin->id,
            ]
        );
        LigneOrdre::firstOrCreate(
            ['ordre_id' => $ordre1->id, 'designation' => 'Plaquettes de frein avant'],
            ['type_ligne' => 'piece', 'piece_id' => $pieceFrein->id, 'quantite' => 1, 'prix_unitaire_ht' => 18000, 'remise_pct' => 0, 'montant_ht' => 18000, 'ordre_affichage' => 0]
        );
        LigneOrdre::firstOrCreate(
            ['ordre_id' => $ordre1->id, 'designation' => 'Main-d\'œuvre peinture'],
            ['type_ligne' => 'main_oeuvre', 'quantite' => 1, 'prix_unitaire_ht' => 27000, 'remise_pct' => 0, 'montant_ht' => 27000, 'ordre_affichage' => 1]
        );

        // Formulation peinture
        $formulation = FormulationPeinture::firstOrCreate(
            ['ordre_id' => $ordre1->id, 'nom' => 'Teinte rouge pare-choc'],
            ['vehicule_id' => $vehicule1->id, 'user_id' => $admin->id, 'code_teinte' => 'L90E', 'notes' => 'Formulation pour pare-choc avant']
        );
        ComposantFormulation::firstOrCreate(
            ['formulation_id' => $formulation->id, 'designation' => 'Base peinture rouge'],
            ['piece_id' => $piecePeinture->id, 'quantite' => 120.5, 'unite' => 'ml']
        );
        ComposantFormulation::firstOrCreate(
            ['formulation_id' => $formulation->id, 'designation' => 'Vernis brillant'],
            ['piece_id' => $pieceVernis->id, 'quantite' => 80, 'unite' => 'ml']
        );

        // Ordre 2 (assurance, sinistre)
        $ordre2 = OrdreReparation::firstOrCreate(
            ['numero' => 'OR-2026-00010'],
            [
                'client_id' => $client2->id, 'vehicule_id' => $vehicule2->id, 'statut' => 'facture',
                'date_entree' => now()->subDays(3), 'kilometrage_entree' => 85000,
                'description_travaux' => 'Réparation carrosserie suite accident + peinture',
                'montant_ht' => 350000, 'montant_ttc' => 350000, 'acompte' => 50000, 'reste_a_payer' => 300000,
                'assurance_nom' => 'NSIA Assurances', 'numero_sinistre' => 'SIN-2026-042',
                'franchise' => 50000, 'part_assurance' => 250000, 'part_client' => 100000,
                'mode_paiement' => 'assurance', 'created_by' => $admin->id,
                'devise_id' => Devise::where('code', 'XOF')->first()?->id,
            ]
        );
        $assurance = Assurance::where('code', 'NSIA')->first();
        if ($assurance) {
            Sinistre::firstOrCreate(
                ['ordre_id' => $ordre2->id],
                ['assurance_id' => $assurance->id, 'numero_sinistre' => 'SIN-2026-042', 'franchise' => 50000, 'part_assurance' => 250000, 'part_client' => 100000, 'statut' => 'ouvert']
            );
        }

        // Notification log
        NotificationLog::firstOrCreate(
            ['ordre_id' => $ordre1->id, 'type_notification' => 'devis_pret'],
            ['client_id' => $client1->id, 'canal' => 'whatsapp', 'destinataire' => $client1->telephone, 'message' => 'Devis prêt', 'statut' => 'envoye', 'sent_at' => now()]
        );
    }
}
