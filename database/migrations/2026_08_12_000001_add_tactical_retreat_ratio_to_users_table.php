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
        Schema::table('users', function (Blueprint $table) {
            // Defender preference: 0 = never, 5 = 5:1, 3 = 3:1 (Admiral).
            $table->unsignedTinyInteger('tactical_retreat_ratio')->default(5)->after('dark_matter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tactical_retreat_ratio');
        });
    }
};
