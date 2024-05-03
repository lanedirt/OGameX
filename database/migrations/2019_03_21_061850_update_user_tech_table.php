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
        if (!Schema::hasColumn('users_tech', 'armor_technology')) {
            Schema::table('users_tech', function (Blueprint $table) {
                $table->integer('armor_technology')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('users_tech', 'armor_technology')) {
            Schema::table('users_tech', function (Blueprint $table) {
                $table->dropColumn('armor_technology');
            });
        }
    }
};
