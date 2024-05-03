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
        Schema::create('planets', function (Blueprint $table) {
            // Planet specific
            $table->increments('id');
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name');
            $table->integer('galaxy');
            $table->integer('system');
            $table->integer('planet');
            $table->integer('planet_type');
            $table->integer('destroyed');
            $table->integer('diameter');
            $table->integer('field_current');
            $table->integer('field_max');
            $table->integer('temp_min');
            $table->integer('temp_max');
            $table->integer('metal')->default(0);
            $table->integer('metal_production')->default(0);
            $table->integer('metal_max')->default(0);
            $table->integer('crystal')->default(0);
            $table->integer('crystal_production')->default(0);
            $table->integer('crystal_max')->default(0);
            $table->integer('deuterium')->default(0);
            $table->integer('deuterium_production')->default(0);
            $table->integer('deuterium_max')->default(0);
            $table->integer('energy_used')->default(0);
            $table->integer('energy_max')->default(0);
            $table->integer('time_last_update')->default(0);

            // Buildings
            $table->integer('metal_mine')->default(0);
            $table->integer('metal_mine_percent')->default(0);
            $table->integer('crystal_mine')->default(0);
            $table->integer('crystal_mine_percent')->default(0);
            $table->integer('deuterium_synthesizer')->default(0);
            $table->integer('deuterium_synthesizer_percent')->default(0);
            $table->integer('solar_plant')->default(0);
            $table->integer('solar_plant_percent')->default(0);
            $table->integer('fusion_plant')->default(0);
            $table->integer('fusion_plant_percent')->default(0);
            $table->integer('robot_factory')->default(0);
            $table->integer('nano_factory')->default(0);
            $table->integer('shipyard')->default(0);
            $table->integer('metal_store')->default(0);
            $table->integer('crystal_store')->default(0);
            $table->integer('deuterium_store')->default(0);
            $table->integer('research_lab')->default(0);
            $table->integer('terraformer')->default(0);
            $table->integer('alliance_depot')->default(0);
            $table->integer('missile_silo')->default(0);
            $table->integer('space_dock')->default(0);

            // Ships
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
            $table->integer('solar_satellite')->default(0);

            // Defense
            $table->integer('rocket_launcher')->default(0);
            $table->integer('light_laser')->default(0);
            $table->integer('heavy_laser')->default(0);
            $table->integer('gauss_cannon')->default(0);
            $table->integer('ion_cannon')->default(0);
            $table->integer('plasma_turret')->default(0);
            $table->integer('small_shield_dome')->default(0);
            $table->integer('large_shield_dome')->default(0);
            $table->integer('anti_ballistic_missile')->default(0);
            $table->integer('interplanetary_missile')->default(0);
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
        Schema::dropIfExists('planets');
    }
};
