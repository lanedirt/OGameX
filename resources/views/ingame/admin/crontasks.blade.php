@extends('ingame.layouts.main')

@section('content')

    @if (session('success'))
        <script>fadeBox(@json(session('success')), false);</script>
    @endif
    @if (session('error'))
        <script>fadeBox(@json(session('error')), true);</script>
    @endif

    <style>
        .cron-task-list {
            padding: 0 12px 20px;
        }
        .cron-task {
            display: block;
            margin: 0 0 14px;
            padding: 12px 14px;
            background: #121a22;
            border: 1px solid #2a3a4a;
        }
        .cron-task-title {
            color: #f1c891;
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 4px;
        }
        .cron-task-command {
            color: #7a8a9a;
            font-size: 11px;
            font-family: monospace;
            margin: 0 0 10px;
            word-break: break-all;
        }
        .cron-task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 18px;
            margin: 0 0 12px;
            color: #c5d0da;
            font-size: 11px;
        }
        .cron-task-meta span {
            white-space: nowrap;
        }
        .cron-task-meta strong {
            color: #8ec8f0;
            font-weight: normal;
        }
        .cron-task-actions {
            text-align: left;
        }
    </style>

    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>@lang('Cron tasks')</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>@lang('Cron tasks')</h2>
            </div>
            <div class="content">
                <div class="buddylistContent" style="margin-bottom: 60px;">
                    <p class="box_highlight textCenter no_buddies">
                        @lang('Scheduled server tasks. Run a task manually for testing or recovery.')
                    </p>

                    @if (empty($tasks))
                        <p class="box_highlight textCenter no_buddies">@lang('No scheduled tasks found.')</p>
                    @else
                        <div class="cron-task-list">
                            @foreach ($tasks as $task)
                                <div class="cron-task">
                                    <p class="cron-task-title">{{ $task['description'] }}</p>
                                    <p class="cron-task-command">{{ $task['command'] }}</p>
                                    <div class="cron-task-meta">
                                        <span>@lang('Schedule'): <strong>{{ $task['expression'] }}</strong></span>
                                        <span>@lang('Next run'): <strong>{{ $task['next_due'] }}</strong></span>
                                        @if ($task['without_overlapping'])
                                            <span>@lang('Overlap protection'): <strong>@lang('On')</strong></span>
                                        @endif
                                    </div>
                                    <div class="cron-task-actions">
                                        @if ($task['runnable'] && !empty($task['command']))
                                            <form method="post" action="{{ route('admin.crontasks.run') }}">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="command" value="{{ $task['command'] }}">
                                                <input type="submit" class="btn_blue" value="@lang('Run now')"
                                                       onclick="return confirm('@lang('Run this scheduled task now?')');">
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
