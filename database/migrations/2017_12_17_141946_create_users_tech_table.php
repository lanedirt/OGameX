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
        Schema::create('users_tech', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('energy_technology')->default(0);
            $table->integer('laser_technology')->default(0);
            $table->integer('ion_technology')->default(0);
            $table->integer('hyperspace_technology')->default(0);
            $table->integer('plasma_technology')->default(0);
            $table->integer('combustion_drive')->default(0);
            $table->integer('impulse_drive')->default(0);
            $table->integer('hyperspace_drive')->default(0);
            $table->integer('espionage_technology')->default(0);
            $table->integer('computer_technology')->default(0);
            $table->integer('astrophysics')->default(0);
            $table->integer('intergalactic_research_network')->default(0);
            $table->integer('graviton_technology')->default(0);
            $table->integer('weapon_technology')->default(0);
            $table->integer('shielding_technology')->default(0);
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
        Schema::dropIfExists('users_tech');
    }
};
