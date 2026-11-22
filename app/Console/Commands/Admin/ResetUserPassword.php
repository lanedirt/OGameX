<?php

namespace OGame\Console\Commands\Admin;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OGame\Models\User;

/**
 * Reset a user's password when requested or for testing purposes.
 */
#[Description('Reset a user\'s password.')]
#[Signature('ogamex:admin:reset-password {username-or-email} {--random}')]
class ResetUserPassword extends Command
{
    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $usernameOrEmail = $this->argument('username-or-email');
        $user = User::where('username', $usernameOrEmail)->orWhere('email', $usernameOrEmail)->first();

        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        // Generate password
        $password = $this->option('random') ? Str::random(12) : '12345678';

        // Update password
        $user->password = Hash::make($password);
        $user->save();

        $this->info('--------------------------------');
        $this->info('Password has been reset for user:');
        $this->info('- ID: ' . $user->id);
        $this->info('- Username: ' . $user->username);
        $this->info('- Email: ' . $user->email);
        $this->info('- New password: ' . $password);
        $this->info('--------------------------------');

        return 0;
    }
}
