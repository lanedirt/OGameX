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
        Schema::table('highscores', function (Blueprint $table) {
            // Rename existing military column to military_built
            $table->renameColumn('military', 'military_built');

            // Add new military subcategory columns
            $table->bigInteger('military_destroyed')->default(0)->after('military_built');
            $table->bigInteger('military_lost')->default(0)->after('military_destroyed');

            // Rename existing military_rank to military_built_rank
            $table->renameColumn('military_rank', 'military_built_rank');

            // Add new military subcategory rank columns
            $table->bigInteger('military_destroyed_rank')->nullable()->default(null)->after('military_built_rank');
            $table->bigInteger('military_lost_rank')->nullable()->default(null)->after('military_destroyed_rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('highscores', function (Blueprint $table) {
            // Revert column names
            $table->renameColumn('military_built', 'military');
            $table->renameColumn('military_built_rank', 'military_rank');

            // Drop new columns
            $table->dropColumn(['military_destroyed', 'military_lost', 'military_destroyed_rank', 'military_lost_rank']);
        });
    }
};
