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
        // Drop the existing non-cascading FK
        Schema::table('espionage_reports', function (Blueprint $table) {
            if (Schema::hasColumn('espionage_reports', 'planet_user_id')) {
                $foreignKeys = Schema::getConnection()
                    ->getSchemaBuilder()
                    ->getForeignKeys('espionage_reports');

                foreach ($foreignKeys as $foreignKey) {
                    if (in_array('planet_user_id', $foreignKey['columns'])) {
                        $table->dropForeign(['planet_user_id']);
                        break;
                    }
                }
            }
        });

        // Make the column nullable
        Schema::table('espionage_reports', function (Blueprint $table) {
            $table->unsignedInteger('planet_user_id')->nullable()->change();
        });

        // Re-add the FK with ON DELETE SET NULL so reports are preserved when a player is deleted
        Schema::table('espionage_reports', function (Blueprint $table) {
            $table->foreign('planet_user_id', 'espionage_reports_planet_user_id_foreign')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('espionage_reports', function (Blueprint $table) {
            $foreignKeys = Schema::getConnection()
                ->getSchemaBuilder()
                ->getForeignKeys('espionage_reports');

            foreach ($foreignKeys as $foreignKey) {
                if (in_array('planet_user_id', $foreignKey['columns'])) {
                    $table->dropForeign('espionage_reports_planet_user_id_foreign');
                    break;
                }
            }
        });

        Schema::table('espionage_reports', function (Blueprint $table) {
            $table->unsignedInteger('planet_user_id')->nullable(false)->change();
        });

        Schema::table('espionage_reports', function (Blueprint $table) {
            $table->foreign('planet_user_id')
                ->references('id')
                ->on('users');
        });
    }
};
