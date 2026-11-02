@extends('ingame.layouts.main')

@section('content')

    <div id="npcempirescomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>{{ __('t_ingame.admin.npc_page_title') }}</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>{{ __('t_ingame.admin.npc_page_title') }}</h2>
            </div>
            <div class="content">
                <p class="box_highlight textCenter no_buddies">
                    {{ __('t_ingame.admin.npc_page_desc') }}<br>
                    {{ $empires_enabled ? __('t_ingame.admin.npc_state_active') : __('t_ingame.admin.npc_state_frozen') }}
                    — {{ $attacks_enabled ? __('t_ingame.admin.npc_state_attacks_on') : __('t_ingame.admin.npc_state_attacks_off') }}
                    ({{ __('t_ingame.admin.npc_page_count', ['current' => $npc_count, 'max' => $npc_max_count]) }})
                </p>

                <div class="group bborder" style="display: block;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #0d0d1a; color: #aaa; font-size: 11px;">
                                <th style="padding: 4px 6px; text-align: left;">ID</th>
                                <th style="padding: 4px 6px; text-align: left;">Username</th>
                                <th style="padding: 4px 6px; text-align: left;">Planets</th>
                                <th style="padding: 4px 6px; text-align: left;">{{ __('t_ingame.admin.npc_trait_dev') }}</th>
                                <th style="padding: 4px 6px; text-align: left;">{{ __('t_ingame.admin.npc_trait_aggression') }}</th>
                                <th style="padding: 4px 6px; text-align: left;">{{ __('t_ingame.admin.npc_trait_expansion') }}</th>
                                <th style="padding: 4px 6px; text-align: left;">{{ __('t_ingame.admin.npc_last_decision') }}</th>
                                <th style="padding: 4px 6px; text-align: left;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($empires as $empire)
                                <tr style="border-top: 1px solid #22224a;">
                                    <td style="padding: 4px 6px;">{{ $empire['user_id'] }}</td>
                                    <td style="padding: 4px 6px;">{{ $empire['username'] }}</td>
                                    <td style="padding: 4px 6px;">
                                        @foreach ($empire['planets'] as $planet)
                                            {{ $planet['coords'] }} ({{ $planet['name'] }})<br>
                                        @endforeach
                                    </td>
                                    <td style="padding: 4px 6px;">{{ $empire['development_speed'] }}</td>
                                    <td style="padding: 4px 6px;">{{ $empire['aggression'] }}</td>
                                    <td style="padding: 4px 6px;">{{ $empire['expansion_rate'] }}</td>
                                    <td style="padding: 4px 6px;">{{ $empire['last_decision'] }}</td>
                                    <td style="padding: 4px 6px;">
                                        <form action="{{ route('admin.npc-empires.remove') }}" method="post"
                                              onsubmit="return confirm('{{ __('t_ingame.admin.npc_remove_confirm') }}');">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="user_id" value="{{ $empire['user_id'] }}">
                                            <button type="submit" class="btn_blue" style="font-size: 10px; padding: 2px 8px;">{{ __('t_ingame.admin.npc_remove_btn') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr style="border-top: 1px solid #22224a;">
                                    <td colspan="8" style="padding: 8px 6px; text-align: center;">{{ __('t_ingame.admin.npc_page_empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <form action="{{ route('admin.npc-empires.spawn') }}" method="post" class="group bborder" style="display: block; padding: 10px;">
                    {{ csrf_field() }}
                    <div class="fieldwrapper">
                        <label class="styled textBeefy">{{ __('t_ingame.admin.npc_spawn_label') }}</label>
                        <div class="thefield">
                            <input type="number" min="1" max="10" value="1" size="3" name="count" class="textInput w50 textCenter textBeefy">
                            <input type="submit" class="btn_blue" value="{{ __('t_ingame.admin.npc_spawn_btn') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
