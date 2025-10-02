@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="resourcesettingscomponent">
    <div id="inhalt">
        <div id="planet" class="shortHeader">
            <h2>@lang('Resource settings') - {{ $planet_name }}</h2>
        </div>
        <div class="contentRS">
            <div class="headerRS"><a href="{{ route('resources.index') }}" class="close_details close_ressources"></a>
            </div>
            <div class="mainRS">
                <form method="POST" action="{{ route('resources.settingsUpdate') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="saveSettings" value="1">
                    <input type="hidden" name="token" value="1e31c04875d85148ce663b4eb30d328c">
                    <table cellpadding="0" cellspacing="0" class="list listOfResourceSettingsPerPlanet"
                           style="margin-top:0px;">
                        <tbody>
                        <tr>
                            <td colspan="7" id="factor">
                                <div class="secondcol">
                                    <div style="width:376px; margin: 0px auto;">
                                        <span class="factorkey">@lang('Production factor'): {{ $production_factor }}%</span>
                                        <span class="factorbutton">
                                        <input class="btn_blue" type="submit" value="Recalculate">
                                    </span>
                                        <br class="clearfloat">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2"></th>
                            <th>@lang('Metal')</th>
                            <th>@lang('Crystal')</th>
                            <th>@lang('Deuterium')</th>
                            <th>@lang('Energy')</th>
                            <th></th>
                        </tr>
                        <tr class="alt">
                            <td colspan="2" class="label">@lang('Basic Income')</td>
                            <td class="undermark textRight">
                                <span class="tooltipCustom"
                                      title="{{ $basic_income->metal->get() }}">{{ $basic_income->metal->getFormattedLong() }}</span>
                            </td>
                            <td class="undermark textRight">
                                <span class="tooltipCustom"
                                      title="{{ $basic_income->crystal->get() }}">{{ $basic_income->crystal->getFormattedLong() }}</span>
                            </td>
                            <td class="normalmark textRight">
                                <span class="tooltipCustom"
                                      title="{{ $basic_income->deuterium->get() }}">{{ $basic_income->deuterium->getFormattedLong() }}</span>
                            </td>
                            <td class="normalmark textRight">
                                <span class="tooltipCustom"
                                      title="{{ $basic_income->energy->get() }}">{{ $basic_income->energy->getFormattedLong() }}</span>
                            </td>
                            <td></td>
                        </tr>
                        @foreach ($building_resource_rows as $count => $row)
                            <tr class="{{ $loop->iteration % 2 == 0 ? 'alt' : '' }}">
                                <td class="label">
                                    {{ $row['title'] }} (@lang('Level') {{ $row['level'] }})
                                </td>
                                <td>
                                </td>
                                <td class="{{ $row['production']->metal->get() > 0 ? 'undermark' : ($row['production']->metal->get() < 0 ? 'overmark' : 'normalmark') }}">
                                <span class="tooltipCustom " title="{{ $row['production']->metal->getFormattedFull() }}">
                                    {{ $row['production']->metal->getFormattedLong() }}
                                </span>
                                </td>
                                <td class="{{ $row['production']->crystal->get() > 0 ? 'undermark' : ($row['production']->crystal->get() < 0 ? 'overmark' : 'normalmark') }}">
                                <span class="tooltipCustom " title="{{ $row['production']->crystal->getFormattedFull() }}">
                                    {{ $row['production']->crystal->getFormattedLong() }}
                                </span>
                                </td>
                                <td class="{{ $row['production']->deuterium->get() > 0 ? 'undermark' : ($row['production']->deuterium->get() < 0 ? 'overmark' : 'normalmark') }}">
                                <span class="tooltipCustom "
                                      title="{{ $row['production']->deuterium->getFormattedFull() }}">
                                    {{ $row['production']->deuterium->getFormattedLong() }}
                                </span>
                                </td>
                                <td class="{{ ($row['production']->energy->get() * -1) > 0 ? 'undermark' : (($row['production']->energy->get() * -1) < 0 ? 'overmark' : 'normalmark') }}">
                                <span class="tooltipCustom "
                                      title="{{ \OGame\Facades\AppUtil::formatNumberLong($row['actual_energy_use'] * -1) }}/{{ \OGame\Facades\AppUtil::formatNumberLong($row['production']->energy->get() * -1) }}">
                                    {{ \OGame\Facades\AppUtil::formatNumberLong($row['actual_energy_use'] * -1) }}/{{ \OGame\Facades\AppUtil::formatNumberLong($row['production']->energy->get() * -1) }}
                                </span>
                                </td>
                                <td>
                                    <select name="last{{ $row['id'] }}" size="1" class="overmark">
                                        <option class="undermark"
                                                value="10" {{ $row['percentage'] == 10 ? 'selected' : '' }}>100%
                                        </option>
                                        <option class="undermark"
                                                value="9" {{ $row['percentage'] == 9 ? 'selected' : '' }}>90%
                                        </option>
                                        <option class="undermark"
                                                value="8" {{ $row['percentage'] == 8 ? 'selected' : '' }}>80%
                                        </option>
                                        <option class="undermark"
                                                value="7" {{ $row['percentage'] == 7 ? 'selected' : '' }}>70%
                                        </option>
                                        <option class="middlemark"
                                                value="6" {{ $row['percentage'] == 6 ? 'selected' : '' }}>60%
                                        </option>
                                        <option class="middlemark"
                                                value="5" {{ $row['percentage'] == 5 ? 'selected' : '' }}>50%
                                        </option>
                                        <option class="middlemark"
                                                value="4" {{ $row['percentage'] == 4 ? 'selected' : '' }}>40%
                                        </option>
                                        <option class="overmark"
                                                value="3" {{ $row['percentage'] == 3 ? 'selected' : '' }}>30%
                                        </option>
                                        <option class="overmark"
                                                value="2" {{ $row['percentage'] == 2 ? 'selected' : '' }}>20%
                                        </option>
                                        <option class="overmark"
                                                value="1" {{ $row['percentage'] == 1 ? 'selected' : '' }}>10%
                                        </option>
                                        <option class="overmark"
                                                value="0" {{ $row['percentage'] == 0 ? 'selected' : '' }}="">0%</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                        @foreach ($building_energy_rows as $count => $row)
                            <tr class="{{ $loop->iteration % 2 == 0 ? '' : 'alt' }}">
                                <td class="label">
                                    {{ $row['title'] }}
                                    ({{ ($row['type'] === \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $row['type'] === \OGame\GameObjects\Models\Enums\GameObjectType::Defense) ? __('Number:') : __('Level')}} {{ $row['level'] }})
                                </td>
                                <td>
                                </td>
                                <td class="{{ $row['production']->metal->get() > 0 ? 'undermark' : ($row['production']->metal->get() < 0 ? 'overmark' : 'normalmark') }}">
                                <span class="tooltipCustom " title="{{ $row['production']->metal->getFormattedFull() }}">
                                    {{ $row['production']->metal->getFormatted() }}
                                </span>
                                </td>
                                <td class="{{ $row['production']->crystal->get() > 0 ? 'undermark' : ($row['production']->crystal->get() < 0 ? 'overmark' : 'normalmark') }}">
                                <span class="tooltipCustom " title="{{ $row['production']->crystal->getFormattedFull() }}">
                                    {{ $row['production']->crystal->getFormatted() }}
                                </span>
                                </td>
                                <td class="{{ $row['production']->deuterium->get() > 0 ? 'undermark' : ($row['production']->deuterium->get() < 0 ? 'overmark' : 'normalmark') }}">
                                <span class="tooltipCustom "
                                      title="{{ $row['production']->deuterium->getFormattedFull() }}">
                                    {{ $row['production']->deuterium->getFormattedLong() }}
                                </span>
                                </td>
                                <td class="{{ ($row['production']->energy->get()) > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom " title=" {{ $row['production']->energy->getFormattedFull() }}">
                                    {{ $row['production']->energy->getFormattedLong() }}
                                </span>
                                </td>
                                <td>
                                    <select name="last{{ $row['id'] }}" size="1" class="overmark">
                                        <option class="undermark"
                                                value="10" {{ $row['percentage'] == 10 ? 'selected' : '' }}>100%
                                        </option>
                                        <option class="undermark"
                                                value="9" {{ $row['percentage'] == 9 ? 'selected' : '' }}>90%
                                        </option>
                                        <option class="undermark"
                                                value="8" {{ $row['percentage'] == 8 ? 'selected' : '' }}>80%
                                        </option>
                                        <option class="undermark"
                                                value="7" {{ $row['percentage'] == 7 ? 'selected' : '' }}>70%
                                        </option>
                                        <option class="middlemark"
                                                value="6" {{ $row['percentage'] == 6 ? 'selected' : '' }}>60%
                                        </option>
                                        <option class="middlemark"
                                                value="5" {{ $row['percentage'] == 5 ? 'selected' : '' }}>50%
                                        </option>
                                        <option class="middlemark"
                                                value="4" {{ $row['percentage'] == 4 ? 'selected' : '' }}>40%
                                        </option>
                                        <option class="overmark"
                                                value="3" {{ $row['percentage'] == 3 ? 'selected' : '' }}>30%
                                        </option>
                                        <option class="overmark"
                                                value="2" {{ $row['percentage'] == 2 ? 'selected' : '' }}>20%
                                        </option>
                                        <option class="overmark"
                                                value="1" {{ $row['percentage'] == 1 ? 'selected' : '' }}>10%
                                        </option>
                                        <option class="overmark"
                                                value="0" {{ $row['percentage'] == 0 ? 'selected' : '' }}="">0%</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="">
                            <td class="label">
                                @lang('Plasma Technology') (@lang('Level') {{ $plasma_technology_level }})
                            </td>
                            <td>
                            </td>
                            <td class="{{ $production_total->plasma_technology->metal->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom " title="{{ $production_total->plasma_technology->metal->getFormattedFull() }}">
                                    {{ $production_total->plasma_technology->metal->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->plasma_technology->crystal->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom " title="{{ $production_total->plasma_technology->crystal->getFormattedFull() }}">
                                    {{ $production_total->plasma_technology->crystal->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->plasma_technology->deuterium->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom " title="{{ $production_total->plasma_technology->deuterium->getFormattedFull() }}">
                                    {{ $production_total->plasma_technology->deuterium->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->plasma_technology->energy->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom " title="{{ $production_total->plasma_technology->energy->getFormattedFull() }}">
                                    {{ $production_total->plasma_technology->energy->getFormattedLong() }}
                                </span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="alt">
                            <td class="label">
                                @lang('Items')
                            </td>
                            <td>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom " title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom " title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom " title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom " title="0">
                                    0
                                </span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="">
                            <td class="label">
                                @lang('Geologist')
                            </td>
                            <td>
                                <div class="tooltipCustom smallOfficer geologe {{ $officers['geologist'] ? '' : 'grayscale' }}" title="+10% @lang('mine production')">
                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="25" height="25">
                                </div>
                            </td>
                            <td class="{{ $production_total->geologist->metal->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['geologist'] ? '' : 'disabled' }}" title="{{ $production_total->geologist->metal->getFormattedFull() }}">
                                    {{ $production_total->geologist->metal->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->geologist->crystal->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['geologist'] ? '' : 'disabled' }}" title="{{ $production_total->geologist->crystal->getFormattedFull() }}">
                                    {{ $production_total->geologist->crystal->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->geologist->deuterium->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['geologist'] ? '' : 'disabled' }}" title="{{ $production_total->geologist->deuterium->getFormattedFull() }}">
                                    {{ $production_total->geologist->deuterium->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->geologist->energy->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['geologist'] ? '' : 'disabled' }}" title="{{ $production_total->geologist->energy->getFormattedFull() }}">
                                    {{ $production_total->geologist->energy->getFormattedLong() }}
                                </span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="alt">
                            <td class="label">
                                @lang('Engineer')
                            </td>
                            <td>
                                <div class="tooltipCustom smallOfficer engineer {{ $officers['engineer'] ? '' : 'grayscale' }}"
                                     title="+10% @lang('energy production')">
                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="25" height="25">
                                </div>
                            </td>
                            <td class="{{ $production_total->engineer->metal->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['engineer'] ? '' : 'disabled' }}" title="{{ $production_total->engineer->metal->getFormattedFull() }}">
                                    {{ $production_total->engineer->metal->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->engineer->crystal ->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['engineer'] ? '' : 'disabled' }}" title="{{ $production_total->engineer->crystal->getFormattedFull() }}">
                                    {{ $production_total->engineer->crystal->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->engineer->deuterium->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['engineer'] ? '' : 'disabled' }}" title="{{ $production_total->engineer->deuterium->getFormattedFull() }}">
                                    {{ $production_total->engineer->deuterium->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->engineer->energy->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['engineer'] ? '' : 'disabled' }}" title="{{ $production_total->engineer->energy->getFormattedFull() }}">
                                    {{ $production_total->engineer->energy->getFormattedLong() }}
                                </span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="">
                            <td class="label">
                                @lang('Commanding Staff')
                            </td>
                            <td>
                                <div class="tooltipCustom smallOfficer stab {{ $officers['commanding_staff'] ? '' : 'grayscale' }}"
                                     title="+2% mine production<br>+2% energy production">
                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="25" height="25">
                                </div>
                            </td>
                            <td class="{{ $production_total->commanding_staff->metal->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['commanding_staff'] ? '' : 'disabled' }}" title="{{ $production_total->commanding_staff->metal->getFormattedFull() }}">
                                    {{ $production_total->commanding_staff->metal->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->commanding_staff->crystal->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['commanding_staff'] ? '' : 'disabled' }}" title="{{ $production_total->commanding_staff->crystal->getFormattedFull() }}">
                                    {{ $production_total->commanding_staff->crystal->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->commanding_staff->deuterium->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['commanding_staff'] ? '' : 'disabled' }}" title="{{ $production_total->commanding_staff->deuterium->getFormattedFull() }}">
                                    {{ $production_total->commanding_staff->deuterium->getFormattedLong() }}
                                </span>
                            </td>
                            <td class="{{ $production_total->commanding_staff->energy->get() > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom {{ $officers['commanding_staff'] ? '' : 'disabled' }}" title="{{ $production_total->commanding_staff->energy->getFormattedFull() }}">
                                    {{ $production_total->commanding_staff->energy->getFormattedLong() }}
                                </span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="">
                            <td colspan="2" class="label">@lang('Storage capacity')</td>
                            <td class="{{ $metal >= $metal_storage ? 'overmark' : 'normalmark' }} left2">
                            <span class="tooltipCustom" title="{{ $metal_storage_formatted }}">
                                {{ $metal_storage_formatted }}
                            </span>
                            </td>
                            <td class="{{ $crystal >= $crystal_storage ? 'overmark' : 'normalmark' }} left2">
                            <span class="tooltipCustom" title="{{ $crystal_storage_formatted }}">
                                {{ $crystal_storage_formatted }}
                            </span>
                            </td>
                            <td class="{{ $deuterium >= $deuterium_storage ? 'overmark' : 'normalmark' }} left2">
                            <span class="tooltipCustom" title="{{ $deuterium_storage_formatted }}">
                                {{ $deuterium_storage_formatted }}
                            </span>
                            </td>
                            <td>-</td>
                            <td></td>
                        </tr>
                        <tr class="summary alt">
                            <td colspan="2" class="label"><em>@lang('Total per hour:')</em></td>
                            <td class="undermark">
                            <span class="tooltipCustom" title="{{ $production_total->total->metal->getFormattedFull() }}">
                                {{ $production_total->total->metal->getFormattedLong() }}
                            </span>
                            </td>
                            <td class="undermark">
                            <span class="tooltipCustom" title="{{ $production_total->total->crystal->getFormattedFull() }}">
                                {{ $production_total->total->crystal->getFormattedLong() }}
                            </span>
                            </td>
                            <td class="undermark">
                            <span class="tooltipCustom" title="{{ $production_total->total->deuterium->getFormattedFull() }}">
                                {{ $production_total->total->deuterium->getFormattedLong() }}
                            </span>
                            </td>
                            <td class="{{ ($production_total->total->energy->getFormatted() > 0) ? 'undermark' : 'overmark' }}">
                            <span class="tooltipCustom" title="{{ $production_total->total->energy->getFormattedFull() }}">
                                {{ $production_total->total->energy->getFormattedLong() }}
                            </span>
                            </td>
                            <td></td>
                        </tr>
                        <tr class="">
                            <td colspan="2" class="label"><em>@lang('Total per day'):</em></td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ \OGame\Facades\AppUtil::formatNumber($production_total->total->metal->get() * 24) }}">
                                    {{ \OGame\Facades\AppUtil::formatNumberLong($production_total->total->metal->get() * 24) }}
                                </span>
                            </td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ \OGame\Facades\AppUtil::formatNumber($production_total->total->crystal->get() * 24) }}">
                                    {{ \OGame\Facades\AppUtil::formatNumberLong($production_total->total->crystal->get() * 24) }}
                                </span>
                            </td>
                            <td class="{{ $production_total->total->deuterium->get() > 0 ? 'undermark' : 'overmark' }}">
                                <span class="tooltipCustom" title="{{ \OGame\Facades\AppUtil::formatNumber($production_total->total->deuterium->get() * 24) }}">
                                    {{ \OGame\Facades\AppUtil::formatNumberLong($production_total->total->deuterium->get() * 24) }}
                                </span>
                            </td>
                            <td class="{{ ($production_total->total->energy->get() > 0) ? 'undermark' : 'overmark' }}">
                                <span class="tooltipCustom" title="{{ $production_total->total->energy->getFormattedFull() }}">
                                    {{ $production_total->total->energy->getFormattedLong() }}
                                </span>
                            </td>
                            <td></td>
                        </tr>
                        <tr class="alt">
                            <td colspan="2" class="label"><em>@lang('Total per week'):</em></td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ \OGame\Facades\AppUtil::formatNumber($production_total->total->metal->get() * 168) }}">
                                    {{ \OGame\Facades\AppUtil::formatNumberLong($production_total->total->metal->get() * 168) }}
                                </span>
                            </td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ \OGame\Facades\AppUtil::formatNumber($production_total->total->crystal->get() * 168) }}">
                                    {{ \OGame\Facades\AppUtil::formatNumberLong($production_total->total->crystal->get() * 168) }}
                                </span>
                            </td>
                            <td class="{{ $production_total->total->deuterium->get() > 0 ? 'undermark' : 'overmark' }}">
                                <span class="tooltipCustom" title="{{ \OGame\Facades\AppUtil::formatNumber($production_total->total->deuterium->get() * 168) }}">
                                    {{ \OGame\Facades\AppUtil::formatNumberLong($production_total->total->deuterium->get() * 168) }}
                                </span>
                            </td>
                            <td class="{{ ($production_total->total->energy->getFormatted() > 0) ? 'undermark' : 'overmark' }}">
                                <span class="tooltipCustom" title="{{ $production_total->total->energy->getFormattedFull() }}">
                                    {{ $production_total->total->energy->getFormattedLong() }}
                                </span>
                            </td>
                            <td></td>
                        </tr>

                        </tbody>
                    </table>
                </form>
            </div>
            <div class="footerRS"></div>
        </div>
        <br class="clearfloat">
    </div>
    </div>

    <script type="text/javascript">
        function initResourceSettings() {
            $('.mainRS tr:gt(0)').hover(function () {
                $(this).addClass('hover');
            }, function () {
                $(this).removeClass('hover');
            });
        }

        $(function () {
            initResourceSettings();
        });
    </script>
    <div id="eventboxContent">

        <div id="eventListWrap">
            <div id="eventHeader">
                <a class="close_details eventToggle" href="javascript:void(0);">
                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="16" width="16">
                </a>
                <h2>Events</h2>
            </div>
            <table id="eventContent">
                <tbody>
                </tbody>
            </table>
            <div id="eventFooter"></div>
        </div>
    </div>

@endsection
