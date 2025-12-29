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
        Schema::table('messages', function (Blueprint $table) {
            // Foreign key to the alliance that sent the message (nullable)
            $table->unsignedBigInteger('sender_alliance_id')->nullable()->after('sender_user_id');
            $table->foreign('sender_alliance_id')->references('id')->on('alliances')->onDelete('cascade');

            // Add index for querying messages by alliance
            $table->index('sender_alliance_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['sender_alliance_id']);
            $table->dropIndex(['sender_alliance_id']);
            $table->dropColumn('sender_alliance_id');
        });
    }
};
