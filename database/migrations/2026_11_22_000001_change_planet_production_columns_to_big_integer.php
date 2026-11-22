<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * Changes production columns from INT to BIGINT. INT is limited to 2,147,483,647
     * which is exceeded on servers with high economy speed, causing an
     * "out of range value" error when the planet is saved.
     */
    public function up(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            $table->bigInteger('metal_production')->default(0)->change();
            $table->bigInteger('crystal_production')->default(0)->change();
            $table->bigInteger('deuterium_production')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            $table->integer('metal_production')->default(0)->change();
            $table->integer('crystal_production')->default(0)->change();
            $table->integer('deuterium_production')->default(0)->change();
        });
    }
};
