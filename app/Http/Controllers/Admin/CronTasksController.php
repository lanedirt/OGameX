<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use Throwable;

class CronTasksController extends OGameController
{
    /**
     * Allowed artisan command signatures that can be run from the admin UI.
     *
     * @var list<string>
     */
    private const ALLOWED_COMMANDS = [
        'ogamex:scheduler:generate-highscores',
        'ogamex:scheduler:generate-alliance-highscores',
        'ogamex:scheduler:generate-highscore-ranks',
        'ogamex:scheduler:reset-debris-fields',
        'ogamex:scheduler:cleanup-wreckfields',
        'ogamex:scheduler:delete-old-messages',
        'ogamex:scheduler:darkmatter-regenerate',
    ];

    /**
     * Shows scheduled cron tasks and allows manual runs.
     */
    public function index(Schedule $schedule): View
    {
        $tasks = [];

        foreach ($schedule->events() as $event) {
            $command = $this->extractCommandName($event->command ?? '');
            $tasks[] = [
                'expression' => $event->expression,
                'description' => $event->description ?: $command,
                'command' => $command,
                'next_due' => $event->nextRunDate()->format('Y-m-d H:i:s'),
                'without_overlapping' => (bool)$event->withoutOverlapping,
                'runnable' => in_array($command, self::ALLOWED_COMMANDS, true),
            ];
        }

        return view('ingame.admin.crontasks', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Manually run a scheduled command.
     */
    public function run(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'command' => 'required|string|max:255',
        ]);

        $command = $validated['command'];

        if (!in_array($command, self::ALLOWED_COMMANDS, true)) {
            return redirect()->route('admin.crontasks.index')
                ->with('error', __('This command cannot be run from the admin panel.'));
        }

        try {
            Artisan::call($command);
            $output = trim(Artisan::output());
            $message = __('Command ran successfully: :command', ['command' => $command]);
            if ($output !== '') {
                $message .= ' — ' . Str::limit($output, 200);
            }

            return redirect()->route('admin.crontasks.index')->with('success', $message);
        } catch (Throwable $e) {
            return redirect()->route('admin.crontasks.index')
                ->with('error', __('Failed to run command: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Extract a readable command identifier from a scheduled event command string.
     */
    private function extractCommandName(string $command): string
    {
        // Typical format: '/usr/bin/php artisan ogamex:scheduler:generate-highscores'
        if (preg_match("/artisan\s+([^\s']+)/", $command, $matches)) {
            return $matches[1];
        }

        // Some Laravel versions store the signature/FQCN directly.
        if (str_starts_with($command, 'ogamex:')) {
            return $command;
        }

        return $command;
    }
}
