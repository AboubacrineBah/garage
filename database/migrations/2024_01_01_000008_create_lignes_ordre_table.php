<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lignes de détail d'un ordre de réparation (pièces, main-d'œuvre, forfaits).
     */
    public function up(): void
    {
        Schema::create('lignes_ordre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordre_id')->constrained('ordres_reparation')->cascadeOnDelete();
            $table->enum('type_ligne', ['piece', 'main_oeuvre', 'forfait', 'autre'])->default('piece');
            $table->foreignId('piece_id')->nullable()->constrained('pieces_detachees')->nullOnDelete();
            $table->string('designation');
            $table->decimal('quantite', 10, 2)->default(1);
            $table->decimal('prix_unitaire_ht', 15, 0)->default(0); // En XOF
            $table->decimal('remise_pct', 5, 2)->default(0);
            $table->decimal('montant_ht', 15, 0)->default(0);
            $table->smallInteger('ordre_affichage')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_ordre');
    }
};
