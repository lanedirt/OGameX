<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('bans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('reason');
            $table->dateTime('banned_until')->nullable();
            $table->boolean('canceled')->default(false);
            $table->dateTime('canceled_at')->nullable();
            $table->timestamps();
        });

        // Migrate existing ban data from users table.
        DB::table('users')
            ->whereNotNull('ban_reason')
            ->get()
            ->each(function ($user) {
                DB::table('bans')->insert([
                    'user_id'      => $user->id,
                    'reason'       => $user->ban_reason,
                    'banned_until' => $user->banned_until,
                    'canceled'     => false,
                    'canceled_at'  => null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ban_reason', 'banned_until']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('ban_reason')->nullable()->after('register_ip');
            $table->dateTime('banned_until')->nullable()->after('ban_reason');
        });

        // Restore active bans back to the users table.
        DB::table('bans')
            ->where('canceled', false)
            ->where(function ($q) {
                $q->whereNull('banned_until')
                    ->orWhere('banned_until', '>', now());
            })
            ->get()
            ->each(function ($ban) {
                DB::table('users')
                    ->where('id', $ban->user_id)
                    ->update([
                        'ban_reason'   => $ban->reason,
                        'banned_until' => $ban->banned_until,
                    ]);
            });

        Schema::dropIfExists('bans');
    }
};
