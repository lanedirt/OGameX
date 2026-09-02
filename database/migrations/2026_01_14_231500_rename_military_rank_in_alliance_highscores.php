<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     * Renames military_rank to military_built_rank in alliance_highscores table
     * to match the player highscores structure with military subcategories.
     */
    public function up(): void
    {
        Schema::table('alliance_highscores', function (Blueprint $table) {
            $table->renameColumn('military_rank', 'military_built_rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alliance_highscores', function (Blueprint $table) {
            $table->renameColumn('military_built_rank', 'military_rank');
        });
    }
};
