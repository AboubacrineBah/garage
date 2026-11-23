<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulations_peinture', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordre_id')->nullable()->constrained('ordres_reparation')->nullOnDelete();
            $table->foreignId('vehicule_id')->nullable()->constrained('vehicules')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code_teinte', 40)->nullable();
            $table->string('nom', 120);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('composants_formulation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formulation_id')->constrained('formulations_peinture')->cascadeOnDelete();
            $table->foreignId('piece_id')->nullable()->constrained('pieces_detachees')->nullOnDelete();
            $table->string('designation', 160);
            $table->decimal('quantite', 10, 3);
            $table->string('unite', 10)->default('g');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('composants_formulation');
        Schema::dropIfExists('formulations_peinture');
    }
};
