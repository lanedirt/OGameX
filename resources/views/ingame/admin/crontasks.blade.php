@extends('ingame.layouts.main')

@section('content')

    @if (session('success'))
        <script>fadeBox(@json(session('success')), false);</script>
    @endif
    @if (session('error'))
        <script>fadeBox(@json(session('error')), true);</script>
    @endif
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
                        <div class="group bborder" style="display: block; overflow-x: auto;">
                            <table class="defaultTable" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th>@lang('Command')</th>
                                    <th>@lang('Schedule')</th>
                                    <th>@lang('Next Run')</th>
                                    <th>@lang('Overlap Protection')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($tasks as $task)
                                    <tr>
                                        <td>
                                            <strong>{{ $task['description'] }}</strong><br>
                                            <span style="font-family: monospace; font-size: 11px; color: #7a8a9a;">{{ $task['command'] }}</span>
                                        </td>
                                        <td>{{ $task['expression'] }}</td>
                                        <td>{{ $task['next_due'] }}</td>
                                        <td>{{ $task['without_overlapping'] ? __('On') : __('Off') }}</td>
                                        <td>
                                            @if ($task['runnable'] && !empty($task['command']))
                                                <form method="post" action="{{ route('admin.crontasks.run') }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="command" value="{{ $task['command'] }}">
                                                    <input type="submit" class="btn_blue" value="@lang('Run now')"
                                                           onclick="return confirm(@json(__('Run this scheduled task now?')));">
                                                </form>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
