<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First drop the existing foreign key
        Schema::table('battle_reports', function (Blueprint $table) {
            if (Schema::hasColumn('battle_reports', 'planet_user_id')) {
                $foreignKeys = Schema::getConnection()
                    ->getSchemaBuilder()
                    ->getForeignKeys('battle_reports');

                foreach ($foreignKeys as $foreignKey) {
                    if (in_array('planet_user_id', $foreignKey['columns'])) {
                        $table->dropForeign(['planet_user_id']);
                        break;
                    }
                }
            }
        });

        // Then modify the column to be nullable and match users.id type
        Schema::table('battle_reports', function (Blueprint $table) {
            $table->unsignedInteger('planet_user_id')->nullable()->change();
        });

        // Finally add the new foreign key constraint
        Schema::table('battle_reports', function (Blueprint $table) {
            $table->foreign('planet_user_id', 'battle_reports_planet_user_id_foreign')
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
        // First drop the existing foreign key
        Schema::table('battle_reports', function (Blueprint $table) {
            if (Schema::hasColumn('battle_reports', 'planet_user_id')) {
                $foreignKeys = Schema::getConnection()
                    ->getSchemaBuilder()
                    ->getForeignKeys('battle_reports');

                foreach ($foreignKeys as $foreignKey) {
                    if (in_array('planet_user_id', $foreignKey['columns'])) {
                        $table->dropForeign('battle_reports_planet_user_id_foreign');
                        break;
                    }
                }
            }
        });

        // Then modify the column to be non-nullable and match users.id type
        Schema::table('battle_reports', function (Blueprint $table) {
            $table->unsignedInteger('planet_user_id')->nullable(false)->change();
        });

        // Finally add the new foreign key constraint
        Schema::table('battle_reports', function (Blueprint $table) {
            $table->foreign('planet_user_id', 'battle_reports_planet_user_id_foreign')
                ->references('id')
                ->on('users');
        });
    }
};
