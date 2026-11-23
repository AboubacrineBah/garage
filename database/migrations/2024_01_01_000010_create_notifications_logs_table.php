<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Journal des notifications envoyées (WhatsApp, SMS, Email).
     */
    public function up(): void
    {
        Schema::create('notifications_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordre_id')->nullable()->constrained('ordres_reparation')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->enum('canal', ['whatsapp', 'sms', 'email']);
            $table->string('destinataire', 50);                // Numéro ou email
            $table->string('type_notification', 50);           // devis_pret, vehicule_termine, rappel_vidange...
            $table->text('message');
            $table->enum('statut', ['en_attente', 'envoye', 'echec', 'lu'])->default('en_attente');
            $table->json('provider_response')->nullable();     // Réponse brute de l'API
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_logs');
    }
};
