<?php

namespace OGame\Console\Commands\Scheduler;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use Throwable;

#[Description('Advance all NPC empires: resources, queues, missions and AI decisions')]
#[Signature('ogamex:scheduler:npc-empires')]
class ProcessNpcEmpires extends Command
{
    /**
     * ProcessNpcEmpires constructor.
     *
     * @param NpcEmpireManager $npcEmpireManager
     */
    public function __construct(
        private NpcEmpireManager $npcEmpireManager
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * Individual empire failures are caught inside processAll() so that one
     * broken NPC does not stall the whole batch.
     */
    public function handle(): int
    {
        try {
            $this->npcEmpireManager->processAll();
            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed to process NPC empires: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
