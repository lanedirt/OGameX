<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop first in case a previous failed attempt left a broken table.
        Schema::dropIfExists('officers');

        Schema::create('officers', function (Blueprint $table) {
            $table->increments('id');
            // user_id must be unsigned int to match users.id (which uses increments/int)
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('commander_until')->nullable();
            $table->dateTime('admiral_until')->nullable();
            $table->dateTime('engineer_until')->nullable();
            $table->dateTime('geologist_until')->nullable();
            $table->dateTime('technocrat_until')->nullable();
            $table->dateTime('all_officers_until')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officers');
    }
};
