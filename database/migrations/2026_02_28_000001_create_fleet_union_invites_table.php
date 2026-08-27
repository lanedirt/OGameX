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
        Schema::create('fleet_union_invites', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('fleet_union_id');
            $table->foreign('fleet_union_id')->references('id')->on('fleet_unions')->onDelete('cascade');

            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();

            $table->unique(['fleet_union_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleet_union_invites');
    }
};
