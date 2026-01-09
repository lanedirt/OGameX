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
            // Foreign key to the alliance the user belongs to (nullable)
            $table->unsignedBigInteger('alliance_id')->nullable()->after('planet_current');
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('set null');

            // Add index for querying users by alliance
            $table->index('alliance_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['alliance_id']);
            $table->dropIndex(['alliance_id']);
            $table->dropColumn('alliance_id');
        });
    }
};
