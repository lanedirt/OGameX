<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use OGame\Models\User;

class RemoveAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:remove-admin-role {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the admin role from a specified username';

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
