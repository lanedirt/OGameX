@extends('ingame.layouts.main')

@section('content')

<div id="resourcesettingscomponent" class="maincontent">
    <div id="planet" class="shortHeader">
        <h2>Fleet Timing Control</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="background:#1a3a1a;border:1px solid #4caf50;color:#4caf50;padding:10px 16px;margin-bottom:12px;border-radius:4px;">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div id="buttonz">
        <div class="header">
            <h2>Fleet Timing Control
                <span style="font-size:12px;font-weight:normal;color:#aaa;margin-left:12px;">
                    Active missions: <strong id="mission-count">{{ $missions->count() }}</strong>
                    &nbsp;|&nbsp; Server time: <strong id="server-clock"></strong>
                    &nbsp;|&nbsp;
                    <button onclick="location.reload()" class="btn_blue" style="font-size:11px;padding:2px 8px;">↻ Refresh</button>
                </span>
            </h2>
        </div>

        <div class="content">
            <div class="buddylistContent">

                {{-- ── FILTER BAR ─────────────────────────────────────────── --}}
                <form method="GET" action="{{ route('admin.fleettiming.index') }}" style="margin-bottom:16px;">
                    <div class="group bborder" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <label class="styled textBeefy">Filter by player:</label>
                        <select name="user_id" class="w200" onchange="this.form.submit()">
                            <option value="">— All players —</option>
                            @foreach ($allUsers as $uid => $uname)
                                <option value="{{ $uid }}" {{ (string)$filterUserId === (string)$uid ? 'selected' : '' }}>
                                    {{ $uname }} (#{{ $uid }})
                                </option>
                            @endforeach
                        </select>
                        @if ($filterUserId)
                            <a href="{{ route('admin.fleettiming.index') }}" class="btn_blue" style="padding:3px 10px;font-size:12px;">✕ Clear</a>
                        @endif
                    </div>
                </form>

                {{-- ── GLOBAL ACTIONS ─────────────────────────────────────── --}}
                <form method="POST" action="{{ route('admin.fleettiming.fast-forward-all') }}"
                      onsubmit="return confirm('Fast-forward ALL active missions{{ $filterUserId ? ' for this player' : '' }} to arrive NOW?')">
                    @csrf
                    @if ($filterUserId)
                        <input type="hidden" name="user_id" value="{{ $filterUserId }}">
                    @endif
                    <div class="group bborder" style="display:flex;align-items:center;gap:10px;padding:10px 0;">
                        <span class="textBeefy" style="color:#f90;">⚡ Global action:</span>
                        <input type="submit" class="btn_red"
                               value="{{ $filterUserId ? 'Arrive ALL now (this player)' : 'Arrive ALL now (all players)' }}"
                               style="font-weight:bold;">
                        <span style="font-size:11px;color:#aaa;">Sets time_arrival = now and time_holding = 0 for all visible missions</span>
                    </div>
                </form>

                {{-- ── MISSIONS TABLE ─────────────────────────────────────── --}}
                @if ($missions->isEmpty())
                    <p class="box_highlight textCenter no_buddies" style="color:#aaa;">
                        No active fleet missions found.
                    </p>
                @else
                    <table id="fleet-timing-table"
                           style="width:100%;border-collapse:collapse;font-size:12px;margin-top:8px;">
                        <thead>
                            <tr style="background:#1e3040;color:#8ec8f0;border-bottom:1px solid #2a4a6a;">
                                <th style="padding:6px 8px;text-align:left;">ID</th>
                                <th style="padding:6px 8px;text-align:left;">Player</th>
                                <th style="padding:6px 8px;text-align:left;">Type</th>
                                <th style="padding:6px 8px;text-align:center;">From</th>
                                <th style="padding:6px 8px;text-align:center;">To</th>
                                <th style="padding:6px 8px;text-align:center;">Holding (s)</th>
                                <th style="padding:6px 8px;text-align:center;">Departure</th>
                                <th style="padding:6px 8px;text-align:center;">Arrival</th>
                                <th style="padding:6px 8px;text-align:center;min-width:90px;">Time Left</th>
                                <th style="padding:6px 8px;text-align:center;min-width:260px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($missions as $mission)
                                @php
                                    $username       = $users[$mission->user_id] ?? "User #{$mission->user_id}";
                                    $typeLabel      = $missionTypeLabels[$mission->mission_type] ?? "Type {$mission->mission_type}";
                                    $timeLeft       = $mission->time_arrival - $now;
                                    $holdingDisplay = $mission->time_holding ? $mission->time_holding : '—';
                                    $rowBg          = $loop->even ? '#12202e' : '#0f1c28';
                                    $urgentStyle    = $timeLeft <= 60 ? 'color:#f00;font-weight:bold;' : ($timeLeft <= 300 ? 'color:#f90;' : 'color:#6cf;');
                                @endphp
                                <tr style="background:{{ $rowBg }};border-bottom:1px solid #1a3040;"
                                    data-mission-id="{{ $mission->id }}"
                                    data-arrival="{{ $mission->time_arrival }}"
                                    data-holding="{{ $mission->time_holding ?? 0 }}">
                                    <td style="padding:5px 8px;color:#8ec8f0;">{{ $mission->id }}</td>
                                    <td style="padding:5px 8px;">
                                        <strong>{{ $username }}</strong>
                                        <span style="color:#666;font-size:10px;">#{{ $mission->user_id }}</span>
                                    </td>
                                    <td style="padding:5px 8px;">
                                        <span class="mission-type-badge mission-type-{{ $mission->mission_type }}"
                                              style="border-radius:3px;padding:1px 5px;font-size:11px;white-space:nowrap;
                                                     background:{{ $mission->mission_type === 1 || $mission->mission_type === 2 ? '#4a1010' : ($mission->mission_type === 15 ? '#1a3a1a' : '#1a2a3a') }};
                                                     color:{{ $mission->mission_type === 1 || $mission->mission_type === 2 ? '#f77' : ($mission->mission_type === 15 ? '#8f8' : '#8ec8f0') }};">
                                            {{ $typeLabel }}
                                        </span>
                                    </td>
                                    <td style="padding:5px 8px;text-align:center;font-family:monospace;">
                                        {{ $mission->galaxy_from }}:{{ $mission->system_from }}:{{ $mission->position_from }}
                                    </td>
                                    <td style="padding:5px 8px;text-align:center;font-family:monospace;">
                                        {{ $mission->galaxy_to }}:{{ $mission->system_to }}:{{ $mission->position_to }}
                                    </td>
                                    <td style="padding:5px 8px;text-align:center;color:#aaa;">
                                        {{ $holdingDisplay }}
                                    </td>
                                    <td style="padding:5px 8px;text-align:center;color:#888;font-size:11px;">
                                        {{ date('H:i:s', $mission->time_departure) }}
                                    </td>
                                    <td style="padding:5px 8px;text-align:center;font-size:11px;">
                                        {{ date('H:i:s d/m', $mission->time_arrival) }}
                                    </td>
                                    <td style="padding:5px 8px;text-align:center;">
                                        <span class="countdown" data-arrival="{{ $mission->time_arrival }}"
                                              style="{{ $urgentStyle }}font-family:monospace;font-size:13px;">
                                            {{ $timeLeft > 0 ? gmdate('H:i:s', $timeLeft) : 'ARRIVED' }}
                                        </span>
                                        @if ($mission->time_holding)
                                            <br><span style="font-size:10px;color:#f90;">+ {{ gmdate('H:i:s', $mission->time_holding) }} hold</span>
                                        @endif
                                    </td>
                                    <td style="padding:5px 8px;text-align:center;">
                                        {{-- Arrive Now --}}
                                        <form method="POST" action="{{ route('admin.fleettiming.fast-forward') }}"
                                              style="display:inline-block;margin:2px 0;">
                                            @csrf
                                            <input type="hidden" name="mission_id" value="{{ $mission->id }}">
                                            <input type="submit" class="btn_red" value="⚡ Arrive Now"
                                                   style="font-size:11px;padding:2px 6px;"
                                                   onclick="return confirm('Set mission #{{ $mission->id }} to arrive immediately?')">
                                        </form>
                                        {{-- Reduce Time --}}
                                        <form method="POST" action="{{ route('admin.fleettiming.reduce') }}"
                                              style="display:inline-flex;align-items:center;gap:3px;margin-top:4px;">
                                            @csrf
                                            <input type="hidden" name="mission_id" value="{{ $mission->id }}">
                                            <span style="font-size:11px;color:#aaa;">-</span>
                                            <input type="number" name="minutes" value="60" min="1" max="10000"
                                                   style="width:52px;background:#0d1a26;border:1px solid #2a4a6a;
                                                          color:#8ec8f0;padding:2px 4px;font-size:11px;text-align:center;">
                                            <span style="font-size:11px;color:#aaa;">min</span>
                                            <input type="submit" class="btn_blue" value="Reduce"
                                                   style="font-size:11px;padding:2px 6px;">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>{{-- buddylistContent --}}
        </div>{{-- content --}}
    </div>{{-- buttonz --}}
</div>{{-- maincontent --}}

<script>
(function () {
    // ── Server clock ────────────────────────────────────────────────
    var serverNow = {{ $now }};
    var clientStart = Math.floor(Date.now() / 1000);

    function currentServerTime() {
        return serverNow + (Math.floor(Date.now() / 1000) - clientStart);
    }

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function formatHMS(sec) {
        if (sec <= 0) return 'ARRIVED';
        var h = Math.floor(sec / 3600);
        var m = Math.floor((sec % 3600) / 60);
        var s = sec % 60;
        return pad(h) + ':' + pad(m) + ':' + pad(s);
    }

    function updateClock() {
        var t = currentServerTime();
        var d = new Date(t * 1000);
        var el = document.getElementById('server-clock');
        if (el) {
            el.textContent = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
        }
    }

    // ── Per-row countdowns ───────────────────────────────────────────
    function updateCountdowns() {
        var t = currentServerTime();
        document.querySelectorAll('.countdown[data-arrival]').forEach(function (el) {
            var arrival = parseInt(el.dataset.arrival, 10);
            var diff    = arrival - t;
            el.textContent = formatHMS(diff);
            // Update urgency colour
            el.style.color = diff <= 0 ? '#f44' : (diff <= 60 ? '#f00' : (diff <= 300 ? '#f90' : '#6cf'));
            el.style.fontWeight = diff <= 60 ? 'bold' : 'normal';
        });
    }

    // ── Auto-refresh page every 30 s to pull new missions ───────────
    var refreshIn = 30;
    var refreshEl = document.getElementById('mission-count');

    setInterval(function () {
        updateClock();
        updateCountdowns();
        refreshIn -= 1;
        if (refreshIn <= 0) {
            location.reload();
        }
    }, 1000);

    // Initial paint
    updateClock();
    updateCountdowns();
})();
</script>

@endsection
