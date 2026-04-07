@extends('ingame.layouts.main')

@section('content')

<style>
    /* Override any padding from .buddylistContent / .content parent */
    #buttonz > .content > #fleet-timing-wrap {
        padding: 0 !important;
        margin: 0 !important;
    }
    #fleet-timing-wrap {
        box-sizing: border-box;
        width: 100%;
        max-width: 100%;
        overflow: hidden;
        padding: 0 !important;
        margin: 0 !important;
    }
    /* All top-level children share the same horizontal padding so header and table align */
    #fleet-timing-wrap > *,
    #fleet-timing-wrap > form {
        padding-left: 6px;
        padding-right: 6px;
        box-sizing: border-box;
    }
    #fleet-timing-wrap .ft-scroll {
        width: 100%;
        padding-left: 0;
        padding-right: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    #fleet-timing-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        table-layout: fixed;
    }
    #fleet-timing-wrap table th,
    #fleet-timing-wrap table td {
        padding: 4px 3px;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    /* Player column: allow wrap on long names */
    #fleet-timing-wrap table th:nth-child(2),
    #fleet-timing-wrap table td:nth-child(2) {
        white-space: normal;
        word-break: break-word;
    }
    /* Actions column: allow wrap for the two stacked forms */
    #fleet-timing-wrap table td:last-child {
        overflow: visible;
        white-space: normal;
    }
    #fleet-timing-wrap .ft-actions {
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: flex-start;
    }
    #fleet-timing-wrap .ft-actions form {
        display: flex;
        align-items: center;
        gap: 3px;
        flex-wrap: nowrap;
    }
    #fleet-timing-wrap .ft-actions input[type="number"] {
        width: 40px;
        background: #0d1a26;
        border: 1px solid #2a4a6a;
        color: #8ec8f0;
        padding: 1px 2px;
        font-size: 11px;
        text-align: center;
    }
    #fleet-timing-wrap .ft-actions input[type="submit"],
    #fleet-timing-wrap .ft-actions button {
        font-size: 10px;
        padding: 2px 5px;
        white-space: nowrap;
    }
    #fleet-timing-wrap .ft-header-bar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 12px;
        margin-bottom: 10px;
    }
    #fleet-timing-wrap .ft-filter-bar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 8px 0;
        margin-bottom: 8px;
    }
    #fleet-timing-wrap .ft-global-bar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 8px 0;
        margin-bottom: 8px;
        border-bottom: 1px solid #2a4a6a;
    }
    .mission-badge {
        border-radius: 3px;
        padding: 1px 5px;
        font-size: 11px;
        white-space: nowrap;
    }
</style>

<div id="resourcesettingscomponent" class="maincontent">
    <div id="planet" class="shortHeader">
        <h2>Fleet Timing Control</h2>
    </div>

    @if (session('success'))
        <div style="background:#1a3a1a;border:1px solid #4caf50;color:#4caf50;padding:8px 14px;margin-bottom:10px;border-radius:4px;font-size:12px;">
            {{ session('success') }}
        </div>
    @endif

    <div id="buttonz">
        <div class="header">
            <h2>Fleet Timing Control</h2>
        </div>

        <div class="content">
            <div class="buddylistContent" id="fleet-timing-wrap">

                {{-- ── HEADER BAR ─────────────────────────────────────────── --}}
                <div class="ft-header-bar">
                    <span>Active missions: <strong id="mission-count">{{ $missions->count() }}</strong></span>
                    <span>|</span>
                    <span>Server time: <strong id="server-clock" style="font-family:monospace;"></strong></span>
                    <span>|</span>
                    <button onclick="location.reload()" class="btn_blue" style="font-size:11px;padding:2px 8px;">Refresh</button>
                </div>

                {{-- ── FILTER BAR ─────────────────────────────────────────── --}}
                <form method="GET" action="{{ route('admin.fleettiming.index') }}" id="filter-form">
                    <datalist id="player-datalist">
                        @foreach ($allUsers as $uid => $uname)
                            <option value="{{ $uname }} (#{{ $uid }})"></option>
                        @endforeach
                    </datalist>
                    <input type="hidden" name="user_id" id="player-user-id" value="{{ $filterUserId ?? '' }}">
                    <div class="ft-filter-bar">
                        <label class="styled textBeefy" style="white-space:nowrap;">
                            Filter by player <span style="color:#aaa;font-weight:normal;">({{ $allUsers->count() }} active)</span>:
                        </label>
                        <input type="text" id="player-search"
                               list="player-datalist"
                               placeholder="Search..."
                               autocomplete="off"
                               value="{{ $filterUserId && isset($allUsers[$filterUserId]) ? $allUsers[$filterUserId].' (#'.$filterUserId.')' : '' }}"
                               style="background:#0d1a26;border:1px solid #2a4a6a;color:#8ec8f0;padding:3px 6px;font-size:11px;width:180px;">
                        <input type="submit" class="btn_blue" value="Go" style="font-size:11px;padding:3px 8px;">
                        @if ($filterUserId)
                            <a href="{{ route('admin.fleettiming.index') }}" class="btn_blue" style="padding:3px 8px;font-size:11px;">Clear</a>
                        @endif
                    </div>
                </form>

                {{-- ── GLOBAL ACTION ───────────────────────────────────────── --}}
                <form method="POST" action="{{ route('admin.fleettiming.fast-forward-all') }}"
                      onsubmit="return confirm('Fast-forward ALL active missions to arrive NOW?')">
                    @csrf
                    @if ($filterUserId)
                        <input type="hidden" name="user_id" value="{{ $filterUserId }}">
                    @endif
                    <div class="ft-global-bar">
                        <span class="textBeefy" style="color:#f90;white-space:nowrap;">Global action:</span>
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
                    <div class="ft-scroll">
                        <table id="fleet-timing-table">
                            <colgroup>
                                <col style="width:5%">    {{-- ID --}}
                                <col style="width:13%">   {{-- Player --}}
                                <col style="width:10%">   {{-- Type --}}
                                <col style="width:7%">    {{-- From --}}
                                <col style="width:7%">    {{-- To --}}
                                <col style="width:6%">    {{-- Hold --}}
                                <col style="width:9%">    {{-- Departure --}}
                                <col style="width:10%">   {{-- Arrival --}}
                                <col style="width:9%">    {{-- Time Left --}}
                                <col style="width:24%">   {{-- Actions --}}
                            </colgroup>
                            <thead>
                                <tr style="background:#1e3040;color:#8ec8f0;border-bottom:1px solid #2a4a6a;">
                                    <th style="text-align:left;">ID</th>
                                    <th style="text-align:left;">Player</th>
                                    <th style="text-align:left;">Type</th>
                                    <th style="text-align:center;">From</th>
                                    <th style="text-align:center;">To</th>
                                    <th style="text-align:center;">Hold (s)</th>
                                    <th style="text-align:center;">Departure</th>
                                    <th style="text-align:center;">Arrival</th>
                                    <th style="text-align:center;">Time Left</th>
                                    <th style="text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($missions as $mission)
                                    @php
                                        $username    = $users[$mission->user_id] ?? "User #{$mission->user_id}";
                                        $typeLabel   = $missionTypeLabels[$mission->mission_type] ?? "Type {$mission->mission_type}";
                                        $timeLeft    = $mission->time_arrival - $now;
                                        $holdingDisp = $mission->time_holding ? $mission->time_holding : '—';
                                        $rowBg       = $loop->even ? '#12202e' : '#0f1c28';
                                        $isAttack    = in_array($mission->mission_type, [1, 2]);
                                        $isExped     = $mission->mission_type === 15;
                                        $badgeBg     = $isAttack ? '#4a1010' : ($isExped ? '#1a3a1a' : '#1a2a3a');
                                        $badgeColor  = $isAttack ? '#f77'    : ($isExped ? '#8f8'    : '#8ec8f0');
                                    @endphp
                                    <tr style="background:{{ $rowBg }};border-bottom:1px solid #1a3040;"
                                        data-arrival="{{ $mission->time_arrival }}">
                                        <td style="color:#8ec8f0;">{{ $mission->id }}</td>
                                        <td>
                                            <strong>{{ $username }}</strong>
                                            <span style="color:#666;font-size:10px;">#{{ $mission->user_id }}</span>
                                        </td>
                                        <td>
                                            <span class="mission-badge"
                                                  style="background:{{ $badgeBg }};color:{{ $badgeColor }};">
                                                {{ $typeLabel }}
                                            </span>
                                        </td>
                                        <td style="text-align:center;font-family:monospace;font-size:11px;">
                                            {{ $mission->galaxy_from }}:{{ $mission->system_from }}:{{ $mission->position_from }}
                                        </td>
                                        <td style="text-align:center;font-family:monospace;font-size:11px;">
                                            {{ $mission->galaxy_to }}:{{ $mission->system_to }}:{{ $mission->position_to }}
                                        </td>
                                        <td style="text-align:center;color:#aaa;font-size:11px;">{{ $holdingDisp }}</td>
                                        <td style="text-align:center;color:#888;font-size:11px;">
                                            {{ date('H:i:s', $mission->time_departure) }}
                                        </td>
                                        <td style="text-align:center;font-size:11px;">
                                            {{ date('H:i:s', $mission->time_arrival) }}<br>
                                            <span style="font-size:10px;color:#666;">{{ date('d/m', $mission->time_arrival) }}</span>
                                        </td>
                                        <td style="text-align:center;">
                                            <span class="countdown"
                                                  data-arrival="{{ $mission->time_arrival }}"
                                                  style="font-family:monospace;font-size:12px;
                                                         color:{{ $timeLeft <= 0 ? '#f44' : ($timeLeft <= 60 ? '#f00' : ($timeLeft <= 300 ? '#f90' : '#6cf')) }};
                                                         font-weight:{{ $timeLeft <= 60 ? 'bold' : 'normal' }};">
                                                {{ $timeLeft > 0 ? gmdate('H:i:s', $timeLeft) : 'ARRIVED' }}
                                            </span>
                                            @if ($mission->time_holding)
                                                <br><span style="font-size:10px;color:#f90;">+{{ gmdate('H:i:s', $mission->time_holding) }} hold</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="ft-actions">
                                                {{-- Arrive Now --}}
                                                <form method="POST" action="{{ route('admin.fleettiming.fast-forward') }}">
                                                    @csrf
                                                    <input type="hidden" name="mission_id" value="{{ $mission->id }}">
                                                    <input type="submit" class="btn_red" value="Now"
                                                           onclick="return confirm('Arrive mission #{{ $mission->id }} immediately?')">
                                                </form>
                                                {{-- Reduce Time --}}
                                                <form method="POST" action="{{ route('admin.fleettiming.reduce') }}">
                                                    @csrf
                                                    <input type="hidden" name="mission_id" value="{{ $mission->id }}">
                                                    <span style="font-size:11px;color:#aaa;">−</span>
                                                    <input type="number" name="minutes" value="60" min="1" max="10000">
                                                    <span style="font-size:11px;color:#aaa;">min</span>
                                                    <input type="submit" class="btn_blue" value="Reduce">
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>{{-- ft-scroll --}}
                @endif

            </div>{{-- fleet-timing-wrap --}}
        </div>{{-- content --}}
    </div>{{-- buttonz --}}
</div>{{-- maincontent --}}

<script>
(function () {
    var serverNow   = {{ $now }};
    var clientStart = Math.floor(Date.now() / 1000);

    function srvNow() {
        return serverNow + (Math.floor(Date.now() / 1000) - clientStart);
    }
    function pad(n) { return String(n).padStart(2, '0'); }
    function hms(sec) {
        if (sec <= 0) return 'ARRIVED';
        return pad(Math.floor(sec / 3600)) + ':' + pad(Math.floor((sec % 3600) / 60)) + ':' + pad(sec % 60);
    }

    function tick() {
        var t = srvNow();
        // clock
        var cl = document.getElementById('server-clock');
        if (cl) {
            var d = new Date(t * 1000);
            cl.textContent = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
        }
        // countdowns
        document.querySelectorAll('.countdown[data-arrival]').forEach(function (el) {
            var diff = parseInt(el.dataset.arrival, 10) - t;
            el.textContent = hms(diff);
            el.style.color       = diff <= 0 ? '#f44' : (diff <= 60 ? '#f00' : (diff <= 300 ? '#f90' : '#6cf'));
            el.style.fontWeight  = diff <= 60 ? 'bold' : 'normal';
        });
    }

    setInterval(tick, 1000);
    tick();
})();

// ── Player datalist filter ───────────────────────────────────────────────────
// Build a map from display text -> user_id from the datalist options
(function () {
    var input      = document.getElementById('player-search');
    var hiddenId   = document.getElementById('player-user-id');
    var form       = document.getElementById('filter-form');
    var datalist   = document.getElementById('player-datalist');
    if (!input || !hiddenId || !datalist) return;

    // Build lookup: display text -> id  (text format: "Name (#N)")
    var map = {};
    Array.prototype.forEach.call(datalist.options, function (o) {
        var match = o.value.match(/#(\d+)\)$/);
        if (match) { map[o.value] = match[1]; }
    });

    input.addEventListener('change', function () {
        var uid = map[this.value] || '';
        hiddenId.value = uid;
        if (uid) { form.submit(); }
    });

    // Pressing Enter also submits if a valid player is selected
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            var uid = map[this.value] || '';
            hiddenId.value = uid;
            form.submit();
        }
    });
})();
</script>

@endsection
