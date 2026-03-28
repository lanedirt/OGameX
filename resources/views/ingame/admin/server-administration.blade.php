@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <script>fadeBox('{{ session('status') }}', false);</script>
    @endif

    @if (session('error'))
        <script>fadeBox('{{ session('error') }}', true);</script>
    @endif

    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>@lang('Server Administration')</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>@lang('Server Administration')</h2>
            </div>
            <div class="content">
                <div class="buddylistContent" style="margin-bottom: 60px;">

                    {{-- ===== MASQUERADE AS USER ===== --}}
                    <p class="box_highlight textCenter no_buddies">@lang('Masquerade as User')</p>
                    <form action="{{ route('admin.developershortcuts.impersonate') }}" method="post" style="margin-bottom: 20px;">
                        {{ csrf_field() }}
                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy" for="masquerade_username">@lang('Username:')</label>
                                <div class="thefield">
                                    <input type="text"
                                           id="masquerade_username"
                                           name="username"
                                           class="textInput w150 textCenter textBeefy"
                                           placeholder="@lang('Enter username')">
                                </div>
                            </div>
                            <div class="fieldwrapper" style="text-align: center; margin-top: 10px;">
                                <input type="submit" class="btn_blue" value="@lang('Masquerade')">
                            </div>
                        </div>
                    </form>

                    {{-- ===== FLAGGED ACCOUNTS ===== --}}
                    <p class="box_highlight textCenter no_buddies">@lang('Flagged Accounts')</p>

                    @php
                        $nothingFlagged = $sharedIpGroups->isEmpty() && $botSuspects->isEmpty();
                    @endphp

                    @if ($nothingFlagged)
                        <div class="group bborder" style="display: block;">
                            <p style="text-align: center; padding: 10px;">No suspicious accounts detected.</p>
                        </div>
                    @else
                        <div style="max-height: 400px; overflow-y: auto; border: 1px solid #333; border-radius: 3px; margin-bottom: 10px;">

                            {{-- ── Shared IP groups ── --}}
                            @if ($sharedIpGroups->isNotEmpty())
                                <div style="padding: 6px 10px; background: #0d0d1a; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">
                                    Shared IP Groups
                                </div>
                                @foreach ($sharedIpGroups as $group)
                                    <div style="padding: 8px 10px; border-bottom: 1px solid #333;">
                                        <div style="padding: 4px 0; margin-bottom: 4px;">
                                            <strong>{{ $group['type'] }}:</strong>
                                            <code style="background: #1a1a2e; padding: 2px 6px; border-radius: 3px; margin-left: 6px;">{{ $group['ip'] }}</code>
                                            @if ($group['cross_missions']->isNotEmpty())
                                                <span style="color: #e74c3c; font-size: 10px; margin-left: 10px;">&#9888; Cross-account missions detected</span>
                                            @endif
                                        </div>

                                        <table style="width: 100%; border-collapse: collapse;">
                                            <thead>
                                                <tr style="background: #0d0d1a; color: #aaa; font-size: 11px;">
                                                    <th style="padding: 4px 6px; text-align: left;">ID</th>
                                                    <th style="padding: 4px 6px; text-align: left;">Username</th>
                                                    <th style="padding: 4px 6px; text-align: left;">Email</th>
                                                    <th style="padding: 4px 6px; text-align: left;">Registered</th>
                                                    <th style="padding: 4px 6px; text-align: left;">Last Active</th>
                                                    <th style="padding: 4px 6px; text-align: left;">Status</th>
                                                    <th style="padding: 4px 6px; text-align: left;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($group['users'] as $user)
                                                    <tr style="border-top: 1px solid #222;">
                                                        <td style="padding: 4px 6px;">{{ $user->id }}</td>
                                                        <td style="padding: 4px 6px;">
                                                            {{ $user->username }}
                                                            @if ($user->hasRole('admin'))
                                                                <span style="color: #f48406; font-size: 10px;">[ADMIN]</span>
                                                            @endif
                                                        </td>
                                                        <td style="padding: 4px 6px;">{{ $user->email }}</td>
                                                        <td style="padding: 4px 6px;">{{ $user->created_at?->format('Y-m-d') }}</td>
                                                        <td style="padding: 4px 6px;">{{ $user->time ? \Illuminate\Support\Carbon::createFromTimestamp((int)$user->time)->format('Y-m-d H:i') : '-' }}</td>
                                                        <td style="padding: 4px 6px;">
                                                            @if ($user->isBanned())
                                                                <span style="color: #e74c3c;">Banned</span>
                                                            @else
                                                                <span style="color: #2ecc71;">Active</span>
                                                            @endif
                                                        </td>
                                                        <td style="padding: 4px 6px;">
                                                            @if (!$user->hasRole('admin') && !$user->isBanned())
                                                                <form action="{{ route('admin.server-administration.ban') }}" method="post" style="display:inline;">
                                                                    {{ csrf_field() }}
                                                                    <input type="hidden" name="username" value="{{ $user->username }}">
                                                                    <input type="hidden" name="reason" value="Multi-account violation">
                                                                    <input type="hidden" name="duration" value="permanent">
                                                                    <input type="submit" class="btn_blue" value="Quick Ban" style="font-size: 10px; padding: 2px 6px;">
                                                                </form>
                                                            @elseif (!$user->hasRole('admin') && $user->isBanned())
                                                                <form action="{{ route('admin.server-administration.unban') }}" method="post" style="display:inline;">
                                                                    {{ csrf_field() }}
                                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                                    <input type="submit" class="btn_blue" value="Unban" style="font-size: 10px; padding: 2px 6px;">
                                                                </form>
                                                            @else
                                                                <span style="color: #666; font-size: 10px;">—</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        @if ($group['cross_missions']->isNotEmpty())
                                            <div style="margin-top: 8px;">
                                                <div style="font-size: 11px; color: #aaa; margin-bottom: 4px;">Fleet missions between these accounts (most recent 20):</div>
                                                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                                    <thead>
                                                        <tr style="background: #1a0a0a; color: #aaa;">
                                                            <th style="padding: 3px 6px; text-align: left;">Type</th>
                                                            <th style="padding: 3px 6px; text-align: left;">From</th>
                                                            <th style="padding: 3px 6px; text-align: left;">To</th>
                                                            <th style="padding: 3px 6px; text-align: left;">Resources</th>
                                                            <th style="padding: 3px 6px; text-align: left;">Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($group['cross_missions'] as $mission)
                                                            @php
                                                                $missionLabel = match($mission->mission_type) {
                                                                    1 => ['label' => 'Attack',    'color' => '#e74c3c'],
                                                                    3 => ['label' => 'Transport', 'color' => '#3498db'],
                                                                    6 => ['label' => 'Espionage', 'color' => '#f39c12'],
                                                                    default => ['label' => 'Unknown', 'color' => '#aaa'],
                                                                };
                                                                $resources = array_filter([
                                                                    $mission->metal     ? number_format($mission->metal)     . ' M' : null,
                                                                    $mission->crystal   ? number_format($mission->crystal)   . ' C' : null,
                                                                    $mission->deuterium ? number_format($mission->deuterium) . ' D' : null,
                                                                ]);
                                                            @endphp
                                                            <tr style="border-top: 1px solid #1a0a0a;">
                                                                <td style="padding: 3px 6px; color: {{ $missionLabel['color'] }};">{{ $missionLabel['label'] }}</td>
                                                                <td style="padding: 3px 6px;">{{ $mission->sender_username }}</td>
                                                                <td style="padding: 3px 6px;">{{ $mission->target_username }}</td>
                                                                <td style="padding: 3px 6px; color: #aaa;">{{ $resources ? implode(', ', $resources) : '—' }}</td>
                                                                <td style="padding: 3px 6px; color: #aaa;">{{ \Illuminate\Support\Carbon::createFromTimestamp($mission->time_departure)->format('Y-m-d H:i') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif

                            {{-- ── Unusual activity (bot-like behaviour) ── --}}
                            @if ($botSuspects->isNotEmpty())
                                <div style="padding: 6px 10px; background: #0d0d1a; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">
                                    Unusual Activity
                                </div>
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #0d0d1a; color: #aaa; font-size: 11px;">
                                            <th style="padding: 4px 6px; text-align: left;">Username</th>
                                            <th style="padding: 4px 6px; text-align: left;" title="18+ distinct active hours AND missions/slot/day above threshold">Round-the-clock</th>
                                            <th style="padding: 4px 6px; text-align: left;" title="Expedition re-dispatched within {{ $detectionSettings['expedition_gap_seconds'] }}s of return">Instant re-dispatch</th>
                                            <th style="padding: 4px 6px; text-align: left;" title="Fleet sent within {{ $detectionSettings['attack_reaction_seconds'] }}s of incoming attack">Instant fleet-save</th>
                                            <th style="padding: 4px 6px; text-align: left;">Status</th>
                                            <th style="padding: 4px 6px; text-align: left;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($botSuspects as $suspect)
                                            @php $user = $suspect['user']; $signals = $suspect['signals']; @endphp
                                            <tr style="border-top: 1px solid #222;">
                                                <td style="padding: 4px 6px;">
                                                    {{ $user->username }}
                                                    @if ($user->hasRole('admin'))
                                                        <span style="color: #f48406; font-size: 10px;">[ADMIN]</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 4px 6px;">
                                                    @if (isset($signals['round_the_clock']))
                                                        @php $rtc = $signals['round_the_clock']; @endphp
                                                        <span style="color: #e74c3c; font-weight: bold;">&#9888;</span>
                                                        <span style="font-size: 10px; color: #aaa;">
                                                            {{ $rtc['active_hours'] }}/24h &middot;
                                                            {{ $rtc['missions_per_slot_per_day'] }}/slot/day &middot;
                                                            {{ number_format($rtc['mission_count']) }} total
                                                        </span>
                                                    @else
                                                        <span style="color: #444; font-size: 10px;">—</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 4px 6px;">
                                                    @if (isset($signals['instant_expedition']))
                                                        <span style="color: #e74c3c; font-weight: bold;">&#9888;</span>
                                                        <span style="font-size: 10px; color: #aaa;">{{ $signals['instant_expedition']['occurrences'] }}×</span>
                                                    @else
                                                        <span style="color: #444; font-size: 10px;">—</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 4px 6px;">
                                                    @if (isset($signals['instant_attack_reaction']))
                                                        <span style="color: #e74c3c; font-weight: bold;">&#9888;</span>
                                                        <span style="font-size: 10px; color: #aaa;">{{ $signals['instant_attack_reaction']['occurrences'] }}×</span>
                                                    @else
                                                        <span style="color: #444; font-size: 10px;">—</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 4px 6px;">
                                                    @if ($user->isBanned())
                                                        <span style="color: #e74c3c;">Banned</span>
                                                    @else
                                                        <span style="color: #2ecc71;">Active</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 4px 6px;">
                                                    @if (!$user->hasRole('admin') && !$user->isBanned())
                                                        <form action="{{ route('admin.server-administration.ban') }}" method="post" style="display:inline;">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="username" value="{{ $user->username }}">
                                                            <input type="hidden" name="reason" value="Suspected botting">
                                                            <input type="hidden" name="duration" value="permanent">
                                                            <input type="submit" class="btn_blue" value="Quick Ban" style="font-size: 10px; padding: 2px 6px;">
                                                        </form>
                                                    @elseif (!$user->hasRole('admin') && $user->isBanned())
                                                        <form action="{{ route('admin.server-administration.unban') }}" method="post" style="display:inline;">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                            <input type="submit" class="btn_blue" value="Unban" style="font-size: 10px; padding: 2px 6px;">
                                                        </form>
                                                    @else
                                                        <span style="color: #666; font-size: 10px;">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                        </div>
                    @endif

                    {{-- ===== BOT DETECTION SETTINGS ===== --}}
                    <p class="box_highlight textCenter no_buddies" style="margin-top: 20px;">@lang('Bot Detection Settings')</p>
                    <form action="{{ route('admin.server-administration.detection-settings') }}" method="post">
                        {{ csrf_field() }}
                        <div class="group bborder" style="display: block;">

                            <div style="padding: 6px 10px; background: #0d0d1a; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                                Signal 1 — Round-the-clock activity
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled" for="bd_lookback_days" title="How many days back to scan for all signals">Lookback period (days):</label>
                                <div class="thefield">
                                    <input type="number" id="bd_lookback_days" name="bot_detection_lookback_days" class="textInput w80 textCenter" min="1" max="90" value="{{ $detectionSettings['lookback_days'] }}">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled" for="bd_active_hours" title="Minimum distinct hours of the day (0–23) that must have mission activity">Min. active hours/day:</label>
                                <div class="thefield">
                                    <input type="number" id="bd_active_hours" name="bot_detection_active_hours" class="textInput w80 textCenter" min="1" max="24" value="{{ $detectionSettings['active_hours'] }}">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled" for="bd_rate" title="Missions per fleet slot per day. Fleet slots = computer technology level + 1. Scales automatically with player progression.">Min. missions/slot/day:</label>
                                <div class="thefield">
                                    <input type="number" id="bd_rate" name="bot_detection_missions_per_slot_per_day" class="textInput w80 textCenter" min="1" value="{{ $detectionSettings['missions_per_slot_per_day'] }}">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled" for="bd_floor" title="Minimum total missions required before a player can be flagged by signal 1. Protects new players whose rate looks suspicious due to tiny sample size.">Min. total missions (floor):</label>
                                <div class="thefield">
                                    <input type="number" id="bd_floor" name="bot_detection_min_missions_floor" class="textInput w80 textCenter" min="0" value="{{ $detectionSettings['min_missions_floor'] }}">
                                </div>
                            </div>

                            <div style="padding: 6px 10px; background: #0d0d1a; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin: 12px 0 8px;">
                                Signal 2 — Instant expedition re-dispatch
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled" for="bd_exp_gap" title="Maximum seconds between an expedition returning and the next one departing from the same planet to count as an instant re-dispatch">Max. re-dispatch gap (seconds):</label>
                                <div class="thefield">
                                    <input type="number" id="bd_exp_gap" name="bot_detection_expedition_gap_seconds" class="textInput w80 textCenter" min="1" value="{{ $detectionSettings['expedition_gap_seconds'] }}">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled" for="bd_exp_min" title="How many instant re-dispatches must be observed before flagging">Min. occurrences to flag:</label>
                                <div class="thefield">
                                    <input type="number" id="bd_exp_min" name="bot_detection_expedition_min_occurrences" class="textInput w80 textCenter" min="1" value="{{ $detectionSettings['expedition_min_occurrences'] }}">
                                </div>
                            </div>

                            <div style="padding: 6px 10px; background: #0d0d1a; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin: 12px 0 8px;">
                                Signal 3 — Instant fleet-save after attack
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled" for="bd_atk_gap" title="Maximum seconds between an attack being dispatched and the defender sending a fleet">Max. reaction gap (seconds):</label>
                                <div class="thefield">
                                    <input type="number" id="bd_atk_gap" name="bot_detection_attack_reaction_seconds" class="textInput w80 textCenter" min="1" value="{{ $detectionSettings['attack_reaction_seconds'] }}">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled" for="bd_atk_min" title="How many instant reactions must be observed before flagging">Min. occurrences to flag:</label>
                                <div class="thefield">
                                    <input type="number" id="bd_atk_min" name="bot_detection_attack_reaction_min_occurrences" class="textInput w80 textCenter" min="1" value="{{ $detectionSettings['attack_reaction_min_occurrences'] }}">
                                </div>
                            </div>

                            <div class="fieldwrapper" style="text-align: center; margin-top: 10px;">
                                <input type="submit" class="btn_blue" value="Save Settings">
                            </div>
                        </div>
                    </form>

                    {{-- ===== BAN A PLAYER ===== --}}
                    <p class="box_highlight textCenter no_buddies" style="margin-top: 20px;">@lang('Ban a Player')</p>
                    <form action="{{ route('admin.server-administration.ban') }}" method="post">
                        {{ csrf_field() }}
                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy" for="ban_username">Username:</label>
                                <div class="thefield">
                                    <input type="text"
                                           id="ban_username"
                                           name="username"
                                           class="textInput w150 textCenter textBeefy"
                                           placeholder="Enter username"
                                           required>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy" for="ban_reason">Reason:</label>
                                <div class="thefield">
                                    <input type="text"
                                           id="ban_reason"
                                           name="reason"
                                           class="textInput w300 textBeefy"
                                           placeholder="Reason for ban"
                                           required>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy" for="ban_duration">Duration:</label>
                                <div class="thefield">
                                    <select id="ban_duration" name="duration" class="textInput textBeefy">
                                        <option value="86400">1 Day</option>
                                        <option value="259200">3 Days</option>
                                        <option value="604800">7 Days</option>
                                        <option value="2592000">30 Days</option>
                                        <option value="permanent">Permanent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="fieldwrapper" style="text-align: center; margin-top: 10px;">
                                <input type="submit" class="btn_blue" value="Ban Player">
                            </div>
                        </div>
                    </form>

                    {{-- ===== CURRENTLY BANNED USERS ===== --}}
                    <p class="box_highlight textCenter no_buddies" style="margin-top: 20px;">@lang('Currently Banned Players')</p>

                    @if ($bannedUsers->isEmpty())
                        <div class="group bborder" style="display: block;">
                            <p style="text-align: center; padding: 10px;">No players are currently banned.</p>
                        </div>
                    @else
                        <div class="group bborder" style="display: block;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #0d0d1a; color: #aaa; font-size: 11px;">
                                        <th style="padding: 5px 8px; text-align: left;">Username</th>
                                        <th style="padding: 5px 8px; text-align: left;">Reason</th>
                                        <th style="padding: 5px 8px; text-align: left;">Banned Until</th>
                                        <th style="padding: 5px 8px; text-align: left;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bannedUsers as $user)
                                        <tr style="border-top: 1px solid #222;">
                                            <td style="padding: 5px 8px;">{{ $user->username }}</td>
                                            <td style="padding: 5px 8px;">{{ $user->ban_reason }}</td>
                                            <td style="padding: 5px 8px;">
                                                {{ $user->banned_until ? $user->banned_until->format('Y-m-d H:i') . ' UTC' : 'Permanent' }}
                                            </td>
                                            <td style="padding: 5px 8px;">
                                                <form action="{{ route('admin.server-administration.unban') }}" method="post" style="display:inline;">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <input type="submit" class="btn_blue" value="Unban" style="font-size: 10px; padding: 2px 8px;">
                                                </form>
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
