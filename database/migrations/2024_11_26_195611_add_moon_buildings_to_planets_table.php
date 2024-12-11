<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            $table->integer('lunar_base')->default(0)->after('space_dock');
            $table->integer('sensor_phalanx')->default(0)->after('lunar_base');
            $table->integer('jump_gate')->default(0)->after('sensor_phalanx');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            $table->dropColumn('lunar_base');
            $table->dropColumn('sensor_phalanx');
            $table->dropColumn('jump_gate');
        });
    }
};
