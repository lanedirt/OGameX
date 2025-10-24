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
        Schema::create('alliance_members', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('alliance_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('rank_id')->nullable();
            $table->text('application_text')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('alliance_id')
                ->references('id')
                ->on('alliances')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('rank_id')
                ->references('id')
                ->on('alliance_ranks')
                ->onDelete('set null');

            // Unique constraint - a user can only be in one alliance
            $table->unique('user_id');

            // Indexes
            $table->index('alliance_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alliance_members');
    }
};
