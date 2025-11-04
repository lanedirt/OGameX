<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'expired' status to the enum
        DB::statement("ALTER TABLE acs_invitations MODIFY COLUMN status ENUM('pending', 'joined', 'declined', 'expired') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'expired' status from the enum (but keep any existing expired records as 'declined')
        DB::statement("UPDATE acs_invitations SET status = 'declined' WHERE status = 'expired'");
        DB::statement("ALTER TABLE acs_invitations MODIFY COLUMN status ENUM('pending', 'joined', 'declined') NOT NULL DEFAULT 'pending'");
    }
};
