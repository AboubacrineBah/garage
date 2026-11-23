<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historique des mouvements de stock.
     */
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piece_id')->constrained('pieces_detachees')->cascadeOnDelete();
            $table->enum('type', ['entree', 'sortie', 'ajustement', 'inventaire']);
            $table->decimal('quantite', 10, 2);                // Toujours positive
            $table->foreignId('ordre_id')->nullable()->constrained('ordres_reparation')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
