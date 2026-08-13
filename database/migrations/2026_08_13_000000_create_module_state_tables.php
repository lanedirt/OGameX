<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * These tables are intentionally generic and namespaced by module_alias.
     * Installing a module must not add a column to planets or users_tech.
     */
    public function up(): void
    {
        Schema::create('module_object_states', function (Blueprint $table) {
            $table->id();
            $table->string('module_alias', 64);
            $table->string('scope', 16); // planet or player
            $table->unsignedBigInteger('owner_id');
            $table->unsignedInteger('object_id');
            $table->string('machine_name', 128);
            $table->unsignedBigInteger('amount')->default(0);
            $table->timestamps();

            $table->unique(['module_alias', 'scope', 'owner_id', 'machine_name'], 'module_object_state_identity');
            $table->index(['scope', 'owner_id']);
            $table->index('object_id');
        });

        Schema::create('module_states', function (Blueprint $table) {
            $table->id();
            $table->string('module_alias', 64);
            $table->string('scope', 16); // server, planet, or player
            $table->unsignedBigInteger('owner_id')->default(0);
            $table->string('key', 128);
            $table->json('value');
            $table->timestamps();

            $table->unique(['module_alias', 'scope', 'owner_id', 'key'], 'module_state_identity');
            $table->index(['scope', 'owner_id']);
        });

        Schema::create('module_queue_items', function (Blueprint $table) {
            $table->id();
            $table->string('module_alias', 64);
            $table->string('scope', 16); // planet today; extensible to player/server
            $table->unsignedBigInteger('owner_id');
            $table->string('queue', 128);
            $table->json('payload');
            $table->timestamp('available_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['module_alias', 'scope', 'owner_id', 'queue', 'available_at'], 'module_queue_due');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_queue_items');
        Schema::dropIfExists('module_states');
        Schema::dropIfExists('module_object_states');
    }
};
