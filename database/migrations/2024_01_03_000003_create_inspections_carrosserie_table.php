<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections_carrosserie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->nullable()->constrained('vehicules')->nullOnDelete();
            $table->foreignId('ordre_id')->nullable()->constrained('ordres_reparation')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('kilometrage')->nullable();
            $table->string('code_teinte', 40)->nullable();
            $table->text('zones_choquees')->nullable();
            $table->string('photo_avant')->nullable();
            $table->string('photo_arriere')->nullable();
            $table->string('photo_gauche')->nullable();
            $table->string('photo_droite')->nullable();
            $table->timestamp('date_inspection')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections_carrosserie');
    }
};
