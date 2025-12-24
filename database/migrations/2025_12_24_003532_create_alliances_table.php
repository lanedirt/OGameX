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
        Schema::create('alliances', function (Blueprint $table) {
            $table->id();
            // Alliance tag (3-8 characters, displayed in brackets)
            $table->string('alliance_tag', 8)->unique();
            // Alliance name (3-30 characters)
            $table->string('alliance_name', 30);
            // Foreign key to the founder user
            $table->integer('founder_user_id', false, true);
            $table->foreign('founder_user_id')->references('id')->on('users')->onDelete('cascade');
            // Alliance texts
            $table->text('internal_text')->nullable(); // Text shown to members
            $table->text('external_text')->nullable(); // Public alliance page text
            $table->text('application_text')->nullable(); // Text shown to applicants
            // Alliance logo URL (optional)
            $table->string('logo_url')->nullable();
            // Alliance homepage URL (optional)
            $table->string('homepage_url')->nullable();
            // Alliance settings
            $table->boolean('is_open')->default(true); // Whether alliance accepts applications
            // Custom rank names
            $table->string('founder_rank_name', 30)->default('Founder');
            $table->string('newcomer_rank_name', 30)->default('Newcomer');
            $table->timestamps();

            // Add indexes for performance
            $table->index('alliance_tag'); // For searching/displaying alliances
            $table->index('founder_user_id'); // For founder lookup
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alliances');
    }
};
