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
        Schema::create('alliances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag', 8)->unique();
            $table->string('name', 64);
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('external_url')->nullable();
            $table->text('internal_text')->nullable();
            $table->text('application_text')->nullable();
            $table->unsignedInteger('founder_id');
            $table->boolean('open_for_applications')->default(true);
            $table->timestamps();

            // Foreign key
            $table->foreign('founder_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes
            $table->index('tag');
            $table->index('name');
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
