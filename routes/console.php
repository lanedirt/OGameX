<?php

use OGame\Console\Commands\DarkMatterRegenerateCommand;
use OGame\Console\Commands\GenerateHighscores;
use OGame\Console\Commands\GenerateHighscoreRanks;
use OGame\Console\Commands\ResetDebrisFields;
use OGame\Console\Commands\CleanupWreckFields;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Schedule::command(GenerateHighscores::class)->everyFiveMinutes();
Schedule::command(GenerateHighscoreRanks::class)->everyFiveMinutes();

// Reset empty debris fields weekly on Monday at 1:00 AM
Schedule::command(ResetDebrisFields::class)->weeklyOn(1, '1:00');

// Clean up wreck fields hourly
Schedule::command(CleanupWreckFields::class)->hourly()->withoutOverlapping();

// Process Dark Matter regeneration every 5 minutes
Schedule::command(DarkMatterRegenerateCommand::class)->everyFiveMinutes()->withoutOverlapping();
