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
        Schema::create('unit_queues', function (Blueprint $table) {
            // Building queue specific
            $table->increments('id');
            $table->integer('planet_id', false, true);
            $table->foreign('planet_id')->references('id')->on('planets');
            $table->integer('object_id');
            $table->integer('object_amount');
            $table->integer('time_duration')->default(0);
            $table->integer('time_start')->default(0);
            $table->integer('time_end')->default(0);
            $table->integer('time_progress')->default(0);
            $table->integer('object_amount_progress')->default(0);
            $table->integer('metal')->default(0);
            $table->integer('crystal')->default(0);
            $table->integer('deuterium')->default(0);
            $table->tinyInteger('processed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_queues');
    }
};
