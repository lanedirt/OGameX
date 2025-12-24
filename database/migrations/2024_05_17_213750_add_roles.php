<?php

use Illuminate\Database\Migrations\Migration;
use OGame\Models\User;
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

        // Assign "admin" role to the first non-Legor user and rename it to "Admin".
        // Skip Legor if it's the first user, as it already has admin role.
        $firstUser = User::where('username', '!=', 'Legor')->orderBy('id')->first();
        if ($firstUser) {
            $firstUser->assignRole('admin');
            $firstUser->username = 'Admin';
            $firstUser->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove admin role from the first non-Legor user
        $firstUser = User::where('username', '!=', 'Legor')->orderBy('id')->first();
        if ($firstUser) {
            $firstUser->removeRole('admin');
        }

        Role::where('name', 'admin')->delete();
        Role::where('name', 'moderator')->delete();
        Role::where('name', 'player')->delete();
    }
};
