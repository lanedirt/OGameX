@extends('ingame.layouts.main')

@section('content')

    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>@lang('Activity logs')</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>@lang('Activity logs')</h2>
            </div>
            <div class="content">
                <div class="buddylistContent" style="margin-bottom: 60px;">
                    <p class="box_highlight textCenter no_buddies">
                        @lang('Recent constructions, fleet missions, and research activity.')
                    </p>

                    <p class="box_highlight textCenter no_buddies">
                        <a class="btn_blue {{ $tab === 'fleets' ? 'active' : '' }}" href="{{ route('admin.activitylogs.index', ['tab' => 'fleets']) }}">@lang('Fleets')</a>
                        <a class="btn_blue {{ $tab === 'buildings' ? 'active' : '' }}" href="{{ route('admin.activitylogs.index', ['tab' => 'buildings']) }}">@lang('Buildings')</a>
                        <a class="btn_blue {{ $tab === 'units' ? 'active' : '' }}" href="{{ route('admin.activitylogs.index', ['tab' => 'units']) }}">@lang('Shipyard')</a>
                        <a class="btn_blue {{ $tab === 'research' ? 'active' : '' }}" href="{{ route('admin.activitylogs.index', ['tab' => 'research']) }}">@lang('Research')</a>
                    </p>

                    @if ($tab === 'fleets' && $fleets)
                        <div class="group bborder" style="display: block; overflow-x: auto;">
                            <table class="defaultTable" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>@lang('Player')</th>
                                    <th>@lang('Mission')</th>
                                    <th>@lang('From')</th>
                                    <th>@lang('To')</th>
                                    <th>@lang('Departure')</th>
                                    <th>@lang('Arrival')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($fleets as $mission)
                                    <tr>
                                        <td>{{ $mission->id }}</td>
                                        <td>{{ $users[$mission->user_id] ?? ('#'.$mission->user_id) }}</td>
                                        <td>{{ $missionTypeLabels[$mission->mission_type] ?? $mission->mission_type }}</td>
                                        <td>{{ $mission->galaxy_from }}:{{ $mission->system_from }}:{{ $mission->position_from }}</td>
                                        <td>{{ $mission->galaxy_to }}:{{ $mission->system_to }}:{{ $mission->position_to }}</td>
                                        <td>{{ $mission->time_departure ? date('Y-m-d H:i:s', $mission->time_departure) : '-' }}</td>
                                        <td>{{ $mission->time_arrival ? date('Y-m-d H:i:s', $mission->time_arrival) : '-' }}</td>
                                        <td>
                                            @if ($mission->canceled)
                                                @lang('Canceled')
                                            @elseif ($mission->processed)
                                                @lang('Processed')
                                            @else
                                                @lang('Active')
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8">@lang('No fleet missions found.')</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="textCenter" style="margin-top: 10px;">{{ $fleets->links() }}</div>
                        </div>
                    @endif

                    @if ($tab === 'buildings' && $buildings)
                        <div class="group bborder" style="display: block; overflow-x: auto;">
                            <table class="defaultTable" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>@lang('Player')</th>
                                    <th>@lang('Planet')</th>
                                    <th>@lang('Building')</th>
                                    <th>@lang('Target level')</th>
                                    <th>@lang('End')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($buildings as $queue)
                                    @php $planet = $planets[$queue->planet_id] ?? null; @endphp
                                    <tr>
                                        <td>{{ $queue->id }}</td>
                                        <td>{{ $planet ? ($users[$planet->user_id] ?? ('#'.$planet->user_id)) : '-' }}</td>
                                        <td>
                                            @if ($planet)
                                                {{ $planet->name }} [{{ $planet->galaxy }}:{{ $planet->system }}:{{ $planet->planet }}]
                                            @else
                                                #{{ $queue->planet_id }}
                                            @endif
                                        </td>
                                        <td>{{ $objectNames[$queue->object_id] ?? ('#'.$queue->object_id) }}</td>
                                        <td>{{ $queue->object_level_target }}</td>
                                        <td>{{ $queue->time_end ? date('Y-m-d H:i:s', $queue->time_end) : '-' }}</td>
                                        <td>
                                            @if ($queue->canceled)
                                                @lang('Canceled')
                                            @elseif ($queue->processed)
                                                @lang('Processed')
                                            @elseif ($queue->building)
                                                @lang('Building')
                                            @else
                                                @lang('Queued')
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7">@lang('No building queues found.')</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="textCenter" style="margin-top: 10px;">{{ $buildings->links() }}</div>
                        </div>
                    @endif

                    @if ($tab === 'units' && $units)
                        <div class="group bborder" style="display: block; overflow-x: auto;">
                            <table class="defaultTable" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>@lang('Player')</th>
                                    <th>@lang('Planet')</th>
                                    <th>@lang('Unit')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('End')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($units as $queue)
                                    @php $planet = $planets[$queue->planet_id] ?? null; @endphp
                                    <tr>
                                        <td>{{ $queue->id }}</td>
                                        <td>{{ $planet ? ($users[$planet->user_id] ?? ('#'.$planet->user_id)) : '-' }}</td>
                                        <td>
                                            @if ($planet)
                                                {{ $planet->name }} [{{ $planet->galaxy }}:{{ $planet->system }}:{{ $planet->planet }}]
                                            @else
                                                #{{ $queue->planet_id }}
                                            @endif
                                        </td>
                                        <td>{{ $objectNames[$queue->object_id] ?? ('#'.$queue->object_id) }}</td>
                                        <td>{{ $queue->object_amount }}</td>
                                        <td>{{ $queue->time_end ? date('Y-m-d H:i:s', $queue->time_end) : '-' }}</td>
                                        <td>
                                            @if ($queue->processed)
                                                @lang('Processed')
                                            @else
                                                @lang('Active')
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7">@lang('No shipyard queues found.')</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="textCenter" style="margin-top: 10px;">{{ $units->links() }}</div>
                        </div>
                    @endif

                    @if ($tab === 'research' && $research)
                        <div class="group bborder" style="display: block; overflow-x: auto;">
                            <table class="defaultTable" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>@lang('Player')</th>
                                    <th>@lang('Planet')</th>
                                    <th>@lang('Research')</th>
                                    <th>@lang('Target level')</th>
                                    <th>@lang('End')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($research as $queue)
                                    @php $planet = $planets[$queue->planet_id] ?? null; @endphp
                                    <tr>
                                        <td>{{ $queue->id }}</td>
                                        <td>{{ $planet ? ($users[$planet->user_id] ?? ('#'.$planet->user_id)) : '-' }}</td>
                                        <td>
                                            @if ($planet)
                                                {{ $planet->name }} [{{ $planet->galaxy }}:{{ $planet->system }}:{{ $planet->planet }}]
                                            @else
                                                #{{ $queue->planet_id }}
                                            @endif
                                        </td>
                                        <td>{{ $objectNames[$queue->object_id] ?? ('#'.$queue->object_id) }}</td>
                                        <td>{{ $queue->object_level_target }}</td>
                                        <td>{{ $queue->time_end ? date('Y-m-d H:i:s', $queue->time_end) : '-' }}</td>
                                        <td>
                                            @if ($queue->canceled)
                                                @lang('Canceled')
                                            @elseif ($queue->processed)
                                                @lang('Processed')
                                            @elseif ($queue->building)
                                                @lang('Researching')
                                            @else
                                                @lang('Queued')
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7">@lang('No research queues found.')</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="textCenter" style="margin-top: 10px;">{{ $research->links() }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
