<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table des devises supportées (XOF par défaut, XAF, EUR, USD).
     */
    public function up(): void
    {
        Schema::create('devises', function (Blueprint $table) {
            $table->id();
            $table->char('code', 3)->unique();                 // XOF, XAF, EUR, USD
            $table->string('nom', 50);                         // Franc CFA BCEAO, Euro...
            $table->string('symbole', 10);                     // F CFA, €, $
            $table->decimal('taux_vers_xof', 12, 6)->default(1); // 1 unité = X XOF
            $table->tinyInteger('decimales')->default(0);      // 0 pour XOF/XAF, 2 pour EUR/USD
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devises');
    }
};
