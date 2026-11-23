<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sinistres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordre_id')->constrained('ordres_reparation')->cascadeOnDelete();
            $table->foreignId('assurance_id')->nullable()->constrained('assurances')->nullOnDelete();
            $table->string('numero_sinistre', 80)->nullable();
            $table->decimal('franchise', 12, 0)->default(0);
            $table->decimal('part_assurance', 12, 0)->default(0);
            $table->decimal('part_client', 12, 0)->default(0);
            $table->string('statut', 40)->default('ouvert');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinistres');
    }
};
