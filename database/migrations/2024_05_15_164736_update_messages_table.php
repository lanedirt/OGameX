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
        // Delete all existing messages in the table.
        DB::table('messages')->truncate();

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->string('key')->after('user_id');
            $table->text('subject')->nullable()->change();
            $table->text('body')->nullable()->change();
            $table->text('params')->nullable()->after('body');

            // Add indexes for performance:
            $table->index(['user_id', 'key', 'created_at']); // For message listing
            $table->index(['user_id', 'key', 'viewed']); // For unread messages count
        });

        Schema::table('messages', function (Blueprint $table) {
            // Drop indexes:
            $table->dropIndex(['user_id', 'type', 'viewed']);
            $table->dropIndex(['user_id', 'type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->tinyInteger('type')->after('user_id');
            $table->dropColumn('key');
            $table->text('subject')->change();
            $table->text('body')->change();
            $table->dropColumn('params');

            // Add indexes for performance:
            $table->index(['user_id', 'type', 'created_at']); // For message listing
            $table->index(['user_id', 'type', 'viewed']); // For unread messages count
        });

        Schema::table('messages', function (Blueprint $table) {
            // Drop indexes:
            $table->dropIndex(['user_id', 'key', 'created_at']);
            $table->dropIndex(['user_id', 'key', 'viewed']);
        });
    }
};
