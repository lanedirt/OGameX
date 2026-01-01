<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use OGame\Services\DarkMatterService;
use Exception;

class DarkMatterRegenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:darkmatter-regenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Dark Matter regeneration for all eligible users';

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
