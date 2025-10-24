<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alliance_ranks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('alliance_id');
            $table->string('name', 64);
            $table->boolean('can_invite')->default(false);
            $table->boolean('can_kick')->default(false);
            $table->boolean('can_see_applications')->default(false);
            $table->boolean('can_accept_applications')->default(false);
            $table->boolean('can_edit_alliance')->default(false);
            $table->boolean('can_manage_ranks')->default(false);
            $table->boolean('can_send_circular_message')->default(false);
            $table->boolean('can_view_member_list')->default(true);
            $table->boolean('can_use_alliance_depot')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            // Foreign key
            $table->foreign('alliance_id')
                ->references('id')
                ->on('alliances')
                ->onDelete('cascade');

            // Indexes
            $table->index(['alliance_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alliance_ranks');
    }
};
