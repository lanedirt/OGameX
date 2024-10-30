<?php

use OGame\Console\Commands\GenerateHighscores;
use OGame\Console\Commands\GenerateHighscoreRanks;

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
