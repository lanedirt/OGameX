<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value');
            $table->timestamps();
        });

        // Insert an initial record that should always exist in the settings table.
        DB::table('settings')->insert([
            'key' => 'game_name',
            'value' => 'OGameX',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
