<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vacation mode flag - prevents attacks and halts production
            $table->boolean('vacation_mode')->default(false)->after('dark_matter_last_regen');
            // Timestamp when vacation mode was activated
            $table->timestamp('vacation_mode_activated_at')->nullable()->after('vacation_mode');
            // Timestamp when vacation mode can be deactivated (minimum 48 hours after activation)
            $table->timestamp('vacation_mode_until')->nullable()->after('vacation_mode_activated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['vacation_mode', 'vacation_mode_activated_at', 'vacation_mode_until']);
        });
    }
};
