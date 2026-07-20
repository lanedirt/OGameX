@extends('ingame.layouts.main')

@section('content')

    @if (session('success'))
        <script>fadeBox('{{ session('success') }}', false);</script>
    @endif
    @if (session('error'))
        <script>fadeBox('{{ session('error') }}', true);</script>
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
                        @lang('Scheduled server tasks. You can run allowed tasks manually for testing or recovery.')
                    </p>

                    <div class="group bborder" style="display: block; overflow-x: auto;">
                        <table class="defaultTable" style="width: 100%;">
                            <thead>
                            <tr>
                                <th>@lang('Command')</th>
                                <th>@lang('Schedule')</th>
                                <th>@lang('Next run')</th>
                                <th>@lang('Overlap protection')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($tasks as $task)
                                <tr>
                                    <td>
                                        <strong>{{ $task['description'] }}</strong><br>
                                        <span style="color: #999;">{{ $task['command'] }}</span>
                                    </td>
                                    <td><code>{{ $task['expression'] }}</code></td>
                                    <td>{{ $task['next_due'] }}</td>
                                    <td>{{ $task['without_overlapping'] ? __('Yes') : __('No') }}</td>
                                    <td>
                                        @if ($task['runnable'] && !empty($task['command']))
                                            <form method="post" action="{{ route('admin.crontasks.run') }}" style="display: inline;">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="command" value="{{ $task['command'] }}">
                                                <input type="submit" class="btn_blue" value="@lang('Run now')"
                                                       onclick="return confirm('@lang('Run this scheduled task now?')');">
                                            </form>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5">@lang('No scheduled tasks found.')</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
