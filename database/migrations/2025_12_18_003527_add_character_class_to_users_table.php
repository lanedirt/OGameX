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
        Schema::table('users', function (Blueprint $table) {
            // Character class (1=Collector, 2=General, 3=Discoverer, null=None)
            $table->tinyInteger('character_class')->nullable()->after('vacation_mode_duration');
            // Track if user has used their free class selection
            $table->boolean('character_class_free_used')->default(false)->after('character_class');
            // Track when character class was selected/changed
            $table->timestamp('character_class_changed_at')->nullable()->after('character_class_free_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['character_class', 'character_class_free_used', 'character_class_changed_at']);
        });
    }
};
