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
            $table->unsignedBigInteger('jump_gate_cooldown')->nullable()->after('jump_gate');
            $table->unsignedInteger('default_jump_gate_target_id')->nullable()->after('jump_gate_cooldown');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            $table->dropColumn('jump_gate_cooldown');
            $table->dropColumn('default_jump_gate_target_id');
        });
    }
};
