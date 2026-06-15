<?php

namespace OGame\Console\Commands\Admin;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OGame\Models\User;

#[Description('Remove the admin role from a specified username')]
#[Signature('ogamex:admin:remove-role {username}')]
class RemoveAdminRole extends Command
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
        if ($user->hasRole('admin')) {
            $user->removeRole('admin');
            $this->info('Admin role removed from user ' . $username);
        } else {
            $this->info('User ' . $username . ' does not have the admin role.');
        }

        return 0;
    }
}
