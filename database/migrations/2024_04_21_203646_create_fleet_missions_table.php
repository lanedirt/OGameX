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
        Schema::create('fleet_missions', function (Blueprint $table) {
            $table->id();

            // Foreign key to the user that this message belongs to.
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('planet_id_from', false, true);
            $table->foreign('planet_id_from')->references('id')->on('planets');

            // Planet to is optional as it can be a colonization mission or an expedition which are not planets.
            $table->integer('planet_id_to', false, true)->nullable();
            $table->foreign('planet_id_to')->references('id')->on('planets');

            $table->integer('galaxy_to')->nullable();
            $table->integer('system_to')->nullable();
            $table->integer('position_to')->nullable();

            $table->integer('mission_type');
            $table->integer('time_departure')->default(0);
            $table->integer('time_arrival')->default(0);

            // Resources
            $table->integer('metal')->default(0);
            $table->integer('crystal')->default(0);
            $table->integer('deuterium')->default(0);

            // Flyable ships (units)
            $table->integer('light_fighter')->default(0);
            $table->integer('heavy_fighter')->default(0);
            $table->integer('cruiser')->default(0);
            $table->integer('battle_ship')->default(0);
            $table->integer('battlecruiser')->default(0);
            $table->integer('bomber')->default(0);
            $table->integer('destroyer')->default(0);
            $table->integer('deathstar')->default(0);

            $table->integer('small_cargo')->default(0);
            $table->integer('large_cargo')->default(0);
            $table->integer('colony_ship')->default(0);
            $table->integer('recycler')->default(0);
            $table->integer('espionage_probe')->default(0);

            // Status
            $table->tinyInteger('processed')->default(0);
            $table->tinyInteger('canceled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleet_missions');
    }
};
