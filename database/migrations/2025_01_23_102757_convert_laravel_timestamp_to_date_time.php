<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * List of tables that use Laravel's default timestamps
     *
     * @var array<string>
     */
    private array $tables = [
        'battle_reports',
        'building_queues',
        'debris_fields',
        'espionage_reports',
        'fleet_missions',
        'highscores',
        'messages',
        'notes',
        'planets',
        'research_queues',
        'settings',
        'users',
        'users_tech',
        'unit_queues',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->datetime('created_at')->nullable()->change();
                $table->datetime('updated_at')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->timestamp('created_at')->nullable()->change();
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
    }
};
