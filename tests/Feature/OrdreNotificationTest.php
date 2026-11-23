<?php

namespace Tests\Feature;

use App\Contracts\NotificationChannelInterface;
use App\Models\Client;
use App\Models\Devise;
use App\Models\OrdreReparation;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicule;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Test fonctionnel : clôture d'un ordre + journalisation de notification.
 * (Les vrais appels API sont mockés via un canal fake.)
 */
class OrdreNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'gerant',
            'display_name' => 'Gérant',
            'permissions' => ['*'],
        ]);

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.local',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        Devise::create([
            'code' => 'XOF', 'nom' => 'Franc CFA', 'symbole' => 'F CFA',
            'taux_vers_xof' => 1, 'decimales' => 0, 'is_default' => true, 'is_active' => true,
        ]);
    }

    public function test_cloture_ordre_cree_notification_log(): void
    {
        $client = Client::create([
            'type' => 'particulier',
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'telephone' => '70000000',
        ]);

        $vehicule = Vehicule::create([
            'client_id' => $client->id,
            'immatriculation' => 'AA-123-BB',
            'marque' => 'Toyota',
            'modele' => 'Corolla',
            'kilometrage_actuel' => 85000,
            'type_carburant' => 'essence',
        ]);

        $ordre = OrdreReparation::create([
            'numero' => 'OR-2026-00001',
            'client_id' => $client->id,
            'vehicule_id' => $vehicule->id,
            'statut' => 'en_cours',
            'date_entree' => now()->subDay(),
            'kilometrage_entree' => 85000,
            'description_travaux' => 'Vidange + freins',
            'montant_ht' => 75000,
            'montant_ttc' => 75000,
            'reste_a_payer' => 75000,
            'created_by' => $this->admin->id,
        ]);

        // Appel direct du service avec un canal mock
        $notifier = app(NotificationService::class);

        // Canal fake qui réussit toujours
        $fakeChannel = new class implements NotificationChannelInterface
        {
            public function send(string $to, string $message, array $options = []): array
            {
                return ['success' => true, 'message_id' => 'fake-123'];
            }

            public function getName(): string
            {
                return 'whatsapp';
            }

            public function isAvailable(): bool
            {
                return true;
            }
        };

        $notifier->registerChannel($fakeChannel);

        $log = $notifier->notifierVehiculeTermine($ordre->load('client', 'vehicule'), 'whatsapp');

        $this->assertNotNull($log);
        $this->assertEquals('whatsapp', $log->canal);
        $this->assertEquals('vehicule_termine', $log->type_notification);
        $this->assertEquals('envoye', $log->statut);
        $this->assertEquals($ordre->id, $log->ordre_id);
        $this->assertDatabaseHas('notifications_logs', [
            'ordre_id' => $ordre->id,
            'canal' => 'whatsapp',
            'type_notification' => 'vehicule_termine',
            'statut' => 'envoye',
        ]);
    }

    public function test_notification_sans_telephone_retourne_null(): void
    {
        $client = Client::create([
            'type' => 'particulier',
            'nom' => 'Sans Tel',
            'telephone' => '', // vide
        ]);

        $vehicule = Vehicule::create([
            'client_id' => $client->id,
            'immatriculation' => 'BB-456-CC',
            'marque' => 'Peugeot',
            'modele' => '208',
            'type_carburant' => 'essence',
        ]);

        $ordre = OrdreReparation::create([
            'numero' => 'OR-2026-00002',
            'client_id' => $client->id,
            'vehicule_id' => $vehicule->id,
            'statut' => 'termine',
            'date_entree' => now(),
            'kilometrage_entree' => 10000,
            'description_travaux' => 'Test',
            'created_by' => $this->admin->id,
        ]);

        $notifier = app(NotificationService::class);
        $log = $notifier->notifierVehiculeTermine($ordre->load('client', 'vehicule'), 'whatsapp');

        $this->assertNull($log);
    }
}
