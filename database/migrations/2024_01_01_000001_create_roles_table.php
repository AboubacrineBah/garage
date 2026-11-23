<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table des rôles utilisateurs (Gérant, Réceptionniste, Chef d'atelier, Mécanicien, Comptable)
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();          // gérant, réceptionniste, chef_atelier, mécanicien, comptable
            $table->string('display_name', 100);           // Libellé affiché
            $table->json('permissions')->nullable();       // Permissions (ou utiliser spatie/laravel-permission)
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
