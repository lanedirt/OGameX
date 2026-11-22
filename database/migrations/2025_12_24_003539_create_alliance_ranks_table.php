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
        Schema::create('alliance_ranks', function (Blueprint $table) {
            $table->id();
            // Foreign key to the alliance
            $table->unsignedBigInteger('alliance_id');
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('cascade');
            // Rank name
            $table->string('rank_name', 30);
            // Permissions stored as JSON
            $table->json('permissions');
            // Sort order (lower numbers appear first)
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Add indexes for performance
            $table->index(['alliance_id', 'sort_order']); // For ordering ranks within alliance
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
