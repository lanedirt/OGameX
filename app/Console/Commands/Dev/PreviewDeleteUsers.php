<?php

namespace OGame\Console\Commands\Dev;

use Illuminate\Console\Command;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\User;

class PreviewDeleteUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:dev:delete-users
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all test users (test1-test10) and their associated data.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check if any test users exist
        $testUsernames = self::getTestUsernames();
        $existingCount = User::whereIn('username', $testUsernames)->count();

        if ($existingCount === 0) {
            $this->info('No preview test users found.');
            return self::SUCCESS;
        }

        $this->warn("Found {$existingCount} preview test users.");

        // Confirm deletion unless --force is used
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete all test users?')) {
                $this->info('Operation cancelled.');
                return self::SUCCESS;
            }
        }

        self::deleteTestUsers($this);

        $this->newLine();
        $this->info('Preview test users deleted successfully.');

        return self::SUCCESS;
    }

    /**
     * Get the list of test usernames.
     *
     * @return array<string>
     */
    public static function getTestUsernames(): array
    {
        $usernames = [];
        for ($i = 1; $i <= 10; $i++) {
            $usernames[] = 'test' . $i;
        }
        return $usernames;
    }

    /**
     * Delete all test users and their associated data.
     * This method can be called from other commands.
     *
     * @param Command|null $output Optional command instance for output
     */
    public static function deleteTestUsers(Command|null $output = null): void
    {
        $testUsernames = self::getTestUsernames();

        // Get user IDs before deleting
        $testUserIds = User::whereIn('username', $testUsernames)->pluck('id')->toArray();

        if (empty($testUserIds)) {
            $output?->info('No existing test users found.');
            return;
        }

        // Use PlayerService::delete() which handles all relations properly
        $playerServiceFactory = app(PlayerServiceFactory::class);

        foreach ($testUserIds as $userId) {
            $playerService = $playerServiceFactory->make($userId);
            $username = $playerService->getUsername(false);
            $playerService->delete();
            $output?->line("  Deleted user: {$username}");
        }
    }
}
