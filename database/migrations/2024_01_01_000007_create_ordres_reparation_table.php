<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table principale des ordres de réparation (devis / factures / travaux).
     */
    public function up(): void
    {
        Schema::create('ordres_reparation', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->unique()->index();     // OR-2026-00045
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('vehicule_id')->constrained('vehicules')->restrictOnDelete();
            $table->enum('statut', [
                'brouillon', 'devis', 'accepte', 'en_cours',
                'termine', 'facture', 'annule', 'livre',
            ])->default('brouillon')->index();
            $table->dateTime('date_entree');
            $table->dateTime('date_sortie_prevue')->nullable();
            $table->dateTime('date_sortie_reelle')->nullable();
            $table->unsignedInteger('kilometrage_entree')->default(0);
            $table->text('description_travaux')->nullable();    // Symptômes / travaux demandés
            $table->text('diagnostic')->nullable();
            $table->json('mecaniciens_ids')->nullable();        // Liste des user_id assignés
            $table->decimal('montant_ht', 15, 0)->default(0);   // En XOF
            $table->decimal('montant_tva', 15, 0)->default(0);
            $table->decimal('montant_ttc', 15, 0)->default(0);
            $table->foreignId('devise_id')->nullable()->constrained('devises')->nullOnDelete();
            $table->decimal('taux_applique', 12, 6)->nullable();
            $table->decimal('acompte', 15, 0)->default(0);
            $table->decimal('reste_a_payer', 15, 0)->default(0);
            $table->string('mode_paiement', 50)->nullable();    // especes, orange_money, wave, moov, virement
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes_internes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordres_reparation');
    }
};
