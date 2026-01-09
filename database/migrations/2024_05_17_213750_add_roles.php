<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'moderator']);
        Role::create(['name' => 'player']);

        // Create Legor admin account using raw SQL to avoid model events
        // Only create if ID 1 doesn't exist and position 1:1:2 is available
        $legorExists = DB::table('users')->where('username', 'Legor')->exists();
        $planetExists = DB::table('planets')
            ->where('galaxy', 1)
            ->where('system', 1)
            ->where('planet', 2)
            ->exists();

        if (!$legorExists && !$planetExists) {
            // Get admin role ID
            $adminRole = Role::where('name', 'admin')->first();

            // Insert Legor user with ID 1
            $legorId = DB::table('users')->insertGetId([
                'id' => 1,
                'username' => 'Legor',
                'email' => 'legor@ogamex.local',
                'password' => Hash::make(Str::random(32)),
                'lang' => 'en',
                'time' => now()->timestamp,
                'planet_current' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert UserTech record
            DB::table('users_tech')->insert([
                'user_id' => $legorId,
            ]);

            // Assign admin role using raw insert
            DB::table('model_has_roles')->insert([
                'role_id' => $adminRole->id,
                'model_type' => 'OGame\\Models\\User',
                'model_id' => $legorId,
            ]);

            // Create planet at 1:1:2
            $planetId = DB::table('planets')->insertGetId([
                'user_id' => $legorId,
                'name' => 'Arakis',
                'galaxy' => 1,
                'system' => 1,
                'planet' => 2,
                'planet_type' => 1,
                'diameter' => 12800,
                'field_max' => 163,
                'field_current' => 0,
                'temp_min' => 30,
                'temp_max' => 70,
                'metal' => 500,
                'crystal' => 500,
                'deuterium' => 0,
                'time_last_update' => now()->timestamp,
                'destroyed' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update user's current planet
            DB::table('users')
                ->where('id', $legorId)
                ->update(['planet_current' => $planetId]);
        }

        // Assign "admin" role to the first non-Legor user and rename it to "Admin".
        // Skip Legor if it's the first user, as it already has admin role.
        $firstUserId = DB::table('users')
            ->where('username', '!=', 'Legor')
            ->orderBy('id')
            ->value('id');

        if ($firstUserId !== null) {
            $adminRole = Role::where('name', 'admin')->first();
            $adminRoleId = (int) $adminRole->id;

            // Assign admin role
            DB::table('model_has_roles')->insert([
                'role_id' => $adminRoleId,
                'model_type' => 'OGame\\Models\\User',
                'model_id' => $firstUserId,
            ]);

            // Rename to Admin
            DB::table('users')
                ->where('id', $firstUserId)
                ->update(['username' => 'Admin']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Legor account if it was created by this migration (ID 1 with username 'Legor')
        $legor = DB::table('users')->where('id', 1)->where('username', 'Legor')->first();
        if ($legor) {
            // Delete Legor's planets (including moon)
            DB::table('planets')->where('user_id', 1)->delete();
            // Delete debris field at Legor's planet location (1:1:2)
            DB::table('debris_fields')
                ->where('galaxy', 1)
                ->where('system', 1)
                ->where('planet', 2)
                ->delete();
            // Delete Legor's user tech
            DB::table('users_tech')->where('user_id', 1)->delete();
            // Delete Legor's role assignments
            DB::table('model_has_roles')->where('model_id', 1)->delete();
            // Delete Legor
            DB::table('users')->where('id', 1)->delete();
        }

        // Remove admin role from the first non-Legor user
        $firstUserId = DB::table('users')
            ->where('username', '!=', 'Legor')
            ->orderBy('id')
            ->value('id');

        if ($firstUserId !== null) {
            DB::table('model_has_roles')
                ->where('model_id', $firstUserId)
                ->where('model_type', 'OGame\\Models\\User')
                ->delete();
        }

        Role::where('name', 'admin')->delete();
        Role::where('name', 'moderator')->delete();
        Role::where('name', 'player')->delete();
    }
};
