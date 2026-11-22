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
        Schema::table('unit_queues', function (Blueprint $table) {
            $table->tinyInteger('dm_halved')->default(0)->after('processed');
            $table->tinyInteger('dm_completed')->default(0)->after('dm_halved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_queues', function (Blueprint $table) {
            $table->dropColumn(['dm_halved', 'dm_completed']);
        });
    }
};
