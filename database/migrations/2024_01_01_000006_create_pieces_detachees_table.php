<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table du stock de pièces détachées.
     */
    public function up(): void
    {
        Schema::create('pieces_detachees', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 50)->unique()->index();
            $table->string('designation');
            $table->string('categorie', 80)->nullable()->index(); // filtre, huile, freinage...
            $table->string('marque', 80)->nullable();
            $table->decimal('prix_achat', 15, 0)->default(0);     // En XOF
            $table->decimal('prix_vente', 15, 0)->default(0);     // En XOF
            $table->decimal('stock_actuel', 10, 2)->default(0);
            $table->decimal('stock_minimum', 10, 2)->default(0);  // Seuil d'alerte
            $table->string('unite', 20)->default('piece');        // piece, litre, kg...
            $table->string('emplacement', 50)->nullable();
            $table->string('fournisseur_principal', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_detachees');
    }
};
