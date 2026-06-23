<?php

namespace OGame\Console\Commands\Scheduler;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Exception;
use Illuminate\Console\Command;
use OGame\Services\DarkMatterService;

#[Description('Process Dark Matter regeneration for all eligible users')]
#[Signature('ogamex:scheduler:darkmatter-regenerate')]
class DarkMatterRegenerateCommand extends Command
{
    /**
     * DarkMatterRegenerateCommand constructor.
     *
     * @param DarkMatterService $darkMatterService
     */
    public function __construct(
        private DarkMatterService $darkMatterService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Processing Dark Matter regeneration...');

        try {
            $this->darkMatterService->processAllRegeneration();
            $this->info('Dark Matter regeneration completed successfully.');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Failed to process Dark Matter regeneration: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
