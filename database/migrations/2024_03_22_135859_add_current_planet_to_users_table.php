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
        if (!Schema::hasColumn('users', 'planet_current')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('planet_current', false, true)->nullable();
                $table->foreign('planet_current')->references('id')->on('planets');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'planet_current')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['planet_current']);
                $table->dropColumn('planet_current');
            });
        }
    }
};
