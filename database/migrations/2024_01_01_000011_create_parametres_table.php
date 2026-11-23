<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table clé/valeur pour les paramètres de l'atelier
     * (coordonnées, templates messages, seuils, etc.).
     */
    public function up(): void
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique();           // ex: atelier.nom, whatsapp.template.devis_pret
            $table->text('valeur')->nullable();
            $table->string('type', 20)->default('string'); // string, text, int, bool, json
            $table->string('groupe', 50)->default('general'); // general, atelier, notifications, facturation
            $table->string('libelle')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres');
    }
};
