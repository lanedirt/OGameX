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

        // Assign "admin" role to the first user and rename it to "Admin".
        $firstUser = User::first();
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
        // Remove roles from the first user
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->removeRole('admin');
        }

        Role::where('name', 'admin')->delete();
        Role::where('name', 'moderator')->delete();
        Role::where('name', 'player')->delete();
    }
};
