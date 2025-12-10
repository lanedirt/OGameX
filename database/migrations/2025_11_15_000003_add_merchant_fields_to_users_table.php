<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Track bonus merchant calls from expeditions
            if (!Schema::hasColumn('users', 'merchant_expedition_bonuses')) {
                $table->integer('merchant_expedition_bonuses')->default(0)->after('espionage_probes_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('merchant_expedition_bonuses');
        });
    }
};
