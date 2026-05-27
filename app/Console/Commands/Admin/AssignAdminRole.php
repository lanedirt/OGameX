<?php

namespace OGame\Console\Commands\Admin;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OGame\Models\User;

#[Description('Assign the admin role to a specified username')]
#[Signature('ogamex:admin:assign-role {username}')]
class AssignAdminRole extends Command
{
    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $username = $this->argument('username');

        $user = User::where('username', $username)->first();

        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        // Assuming you are using the Spatie Permission package
        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
            $this->info('Admin role assigned to user ' . $username);
        } else {
            $this->info('User ' . $username . ' already has the admin role.');
        }

        return 0;
    }
}
