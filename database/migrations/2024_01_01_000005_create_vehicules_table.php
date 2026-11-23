<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table des véhicules des clients.
     */
    public function up(): void
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('immatriculation', 20)->unique()->index();
            $table->string('marque', 80);
            $table->string('modele', 80);
            $table->year('annee')->nullable();
            $table->string('couleur', 50)->nullable();
            $table->string('numero_chassis', 50)->nullable();
            $table->unsignedInteger('kilometrage_actuel')->default(0);
            $table->enum('type_carburant', ['essence', 'diesel', 'hybride', 'electrique', 'gpl'])->default('essence');
            $table->date('date_mise_circulation')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};
