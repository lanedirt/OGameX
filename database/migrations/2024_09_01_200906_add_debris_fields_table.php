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
        Schema::create('debris_fields', function (Blueprint $table) {
            // Planet specific
            $table->increments('id');
            $table->integer('galaxy');
            $table->integer('system');
            $table->integer('planet');
            $table->integer('metal')->default(0);
            $table->integer('crystal')->default(0);
            $table->integer('deuterium')->default(0);
            $table->timestamps();

            // Add unique constraint
            $table->unique(['galaxy', 'system', 'planet']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debris_fields');
    }
};
