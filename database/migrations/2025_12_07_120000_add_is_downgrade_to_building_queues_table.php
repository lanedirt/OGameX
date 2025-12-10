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
        Schema::table('building_queues', function (Blueprint $table) {
            $table->boolean('is_downgrade')->default(false)->after('object_level_target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_queues', function (Blueprint $table) {
            $table->dropColumn('is_downgrade');
        });
    }
};

