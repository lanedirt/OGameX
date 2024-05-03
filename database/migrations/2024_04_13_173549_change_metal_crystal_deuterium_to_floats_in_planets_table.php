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
        Schema::table('planets', function (Blueprint $table) {
            // Change columns to float
            $table->float('metal', 16)->change();
            $table->float('crystal', 16)->change();
            $table->float('deuterium', 16)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            // Revert columns back to integer
            $table->integer('metal')->change();
            $table->integer('crystal')->change();
            $table->integer('deuterium')->change();
        });
    }
};
