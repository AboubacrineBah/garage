<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Devise;
use App\Models\FormulationPeinture;
use App\Models\InspectionCarrosserie;
use App\Models\OrdreReparation;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PersistenceModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'gerant', 'display_name' => 'Gérant', 'permissions' => ['*']]);
        $this->admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.local',
            'password' => Hash::make('password'), 'role_id' => $role->id, 'is_active' => true,
        ]);

        Devise::create([
            'code' => 'XOF', 'nom' => 'Franc CFA', 'symbole' => 'F CFA',
            'taux_vers_xof' => 1, 'decimales' => 0, 'is_default' => true, 'is_active' => true,
        ]);
    }

    public function test_facturation_cree_sinistre_et_assurance(): void
    {
        $client = Client::create(['type' => 'particulier', 'nom' => 'Diallo', 'prenom' => 'A', 'telephone' => '70000000']);
        $vehicule = Vehicule::create(['client_id' => $client->id, 'immatriculation' => 'AA-1-BB', 'marque' => 'Toyota', 'modele' => 'Yaris', 'type_carburant' => 'essence']);
        $ordre = OrdreReparation::create([
            'numero' => 'OR-2026-00010', 'client_id' => $client->id, 'vehicule_id' => $vehicule->id,
            'statut' => 'devis', 'date_entree' => now(), 'kilometrage_entree' => 1000,
            'description_travaux' => 'Test', 'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('ordres.update-facturation', $ordre), [
            'assurance_nom' => 'NSIA Assurances',
            'numero_sinistre' => 'SIN-123',
            'franchise' => 50000,
            'part_assurance' => 200000,
            'part_client' => 50000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assurances', ['nom' => 'NSIA Assurances']);
        $this->assertDatabaseHas('sinistres', [
            'ordre_id' => $ordre->id, 'numero_sinistre' => 'SIN-123',
        ]);
    }

    public function test_inspection_carrosserie_peut_etre_creee(): void
    {
        $vehicule = Vehicule::create(['client_id' => Client::create(['type' => 'particulier', 'nom' => 'X', 'telephone' => '70000001'])->id, 'immatriculation' => 'BB-2-CC', 'marque' => 'Peugeot', 'modele' => '208', 'type_carburant' => 'essence']);

        $inspection = InspectionCarrosserie::create([
            'vehicule_id' => $vehicule->id, 'user_id' => $this->admin->id,
            'kilometrage' => 5000, 'code_teinte' => 'L90E',
            'zones_choquees' => 'choc avant', 'date_inspection' => now(),
        ]);

        $this->assertDatabaseHas('inspections_carrosserie', ['id' => $inspection->id, 'code_teinte' => 'L90E']);
    }

    public function test_formulation_peinture_avec_composants(): void
    {
        $client = Client::create(['type' => 'particulier', 'nom' => 'Y', 'telephone' => '70000002']);
        $vehicule = Vehicule::create(['client_id' => $client->id, 'immatriculation' => 'CC-3-DD', 'marque' => 'Renault', 'modele' => 'Clio', 'type_carburant' => 'essence']);
        $ordre = OrdreReparation::create([
            'numero' => 'OR-2026-00011', 'client_id' => $client->id, 'vehicule_id' => $vehicule->id,
            'statut' => 'devis', 'date_entree' => now(), 'kilometrage_entree' => 1000,
            'description_travaux' => 'Peinture', 'created_by' => $this->admin->id,
        ]);

        $formulation = FormulationPeinture::create([
            'ordre_id' => $ordre->id, 'vehicule_id' => $vehicule->id,
            'user_id' => $this->admin->id, 'code_teinte' => 'L90E', 'nom' => 'Teinte rouge',
        ]);
        $formulation->composants()->create(['designation' => 'Base rouge', 'quantite' => 120.5, 'unite' => 'g']);

        $this->assertDatabaseHas('formulations_peinture', ['id' => $formulation->id]);
        $this->assertEquals(1, $formulation->composants()->count());
    }
}
