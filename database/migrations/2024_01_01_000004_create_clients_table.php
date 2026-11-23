<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table des clients (particuliers et entreprises).
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['particulier', 'entreprise'])->default('particulier');
            $table->string('nom');                             // Nom ou raison sociale
            $table->string('prenom', 100)->nullable();
            $table->string('telephone', 30)->index();
            $table->string('telephone_2', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('numero_contribuable', 50)->nullable(); // Pour entreprises
            $table->decimal('credit_autorise', 15, 0)->default(0); // Plafond en XOF
            $table->decimal('solde_credit', 15, 0)->default(0);    // Encours actuel
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
