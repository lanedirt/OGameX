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
     * Friendly labels for known scheduler commands.
     *
     * @var array<string, string>
     */
    private const COMMAND_LABELS = [
        'ogamex:scheduler:generate-highscores' => 'Generate player highscores',
        'ogamex:scheduler:generate-alliance-highscores' => 'Generate alliance highscores',
        'ogamex:scheduler:generate-highscore-ranks' => 'Generate highscore ranks',
        'ogamex:scheduler:reset-debris-fields' => 'Reset empty debris fields',
        'ogamex:scheduler:cleanup-wreckfields' => 'Clean up wreck fields',
        'ogamex:scheduler:delete-old-messages' => 'Delete old messages',
        'ogamex:scheduler:darkmatter-regenerate' => 'Dark Matter regeneration',
    ];

    /**
     * Human-readable schedule summaries for common cron expressions.
     *
     * @var array<string, string>
     */
    private const SCHEDULE_LABELS = [
        '*/5 * * * *' => 'Every 5 minutes',
        '0 * * * *' => 'Hourly',
        '0 1 * * 1' => 'Weekly (Monday 01:00)',
    ];

    /**
     * Shows scheduled cron tasks and allows manual runs.
     */
    public function index(): View
    {
        $tasks = [];

        foreach ($this->resolveSchedule()->events() as $event) {
            $command = $this->extractCommandName($event->command ?? '');
            $tasks[] = [
                'expression' => self::SCHEDULE_LABELS[$event->expression] ?? $event->expression,
                'description' => self::COMMAND_LABELS[$command] ?? ($event->description ?: $command),
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
     * Resolve the application schedule, loading console schedule definitions
     * when running in an HTTP context (where routes/console.php is not auto-loaded).
     */
    private function resolveSchedule(): Schedule
    {
        $schedule = app(Schedule::class);

        if (count($schedule->events()) === 0) {
            // Schedule:: definitions live in routes/console.php and are only
            // registered automatically during Artisan bootstrap.
            require base_path('routes/console.php');
        }

        return $schedule;
    }

    /**
     * Extract a readable command identifier from a scheduled event command string.
     */
    private function extractCommandName(string $command): string
    {
        // Typical format: '/usr/bin/php' 'artisan' ogamex:scheduler:generate-highscores
        // or: php artisan ogamex:scheduler:generate-highscores
        if (preg_match("/artisan['\"]?\s+['\"]?([^\s'\"]+)/", $command, $matches)) {
            return $matches[1];
        }

        // Some Laravel versions store the signature/FQCN directly.
        if (str_starts_with($command, 'ogamex:')) {
            return $command;
        }

        return $command;
    }
}
