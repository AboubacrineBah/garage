<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordres_reparation', function (Blueprint $table) {
            if (! Schema::hasColumn('ordres_reparation', 'assurance_nom')) {
                $table->string('assurance_nom', 120)->nullable()->after('mode_paiement');
            }
            if (! Schema::hasColumn('ordres_reparation', 'numero_sinistre')) {
                $table->string('numero_sinistre', 80)->nullable()->after('assurance_nom');
            }
            if (! Schema::hasColumn('ordres_reparation', 'franchise')) {
                $table->decimal('franchise', 12, 0)->default(0)->after('numero_sinistre');
            }
            if (! Schema::hasColumn('ordres_reparation', 'part_assurance')) {
                $table->decimal('part_assurance', 12, 0)->default(0)->after('franchise');
            }
            if (! Schema::hasColumn('ordres_reparation', 'part_client')) {
                $table->decimal('part_client', 12, 0)->default(0)->after('part_assurance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ordres_reparation', function (Blueprint $table) {
            $cols = ['assurance_nom', 'numero_sinistre', 'franchise', 'part_assurance', 'part_client'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('ordres_reparation', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
