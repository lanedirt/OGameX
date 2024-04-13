@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="inhalt">
        <div id="planet" class="shortHeader">
            <h2>Resource settings - {{ $planet_name }}</h2>
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
                                        <span class="factorkey">Production factor: {{ $production_factor }}%</span>
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
                            <th>Metal</th>
                            <th>Crystal</th>
                            <th>Deuterium</th>
                            <th>Energy</th>
                            <th></th>
                        </tr>
                        <tr class="alt">
                            <td colspan="2" class="label">Basic Income</td>
                            <td class="undermark textRight">
                                <span class="tooltipCustom"
                                      title="{{ $basic_income->metal->get() }}">{{ $basic_income->metal->getFormatted() }}</span>
                            </td>
                            <td class="undermark textRight">
                                <span class="tooltipCustom"
                                      title="{{ $basic_income->crystal->get() }}">{{ $basic_income->crystal->getFormatted() }}</span>
                            </td>
                            <td class="normalmark textRight">
                                <span class="tooltipCustom"
                                      title="{{ $basic_income->deuterium->get() }}">{{ $basic_income->deuterium->getFormatted() }}</span>
                            </td>
                            <td class="normalmark textRight">
                                <span class="tooltipCustom"
                                      title="{{ $basic_income->energy->get() }}">{{ $basic_income->energy->getFormatted() }}</span>
                            </td>
                            <td></td>
                        </tr>
                        @foreach ($building_resource_rows as $count => $row)
                            <tr class="{{ $loop->iteration % 2 == 0 ? 'alt' : '' }}">
                                <td class="label">
                                    {{ $row['title'] }} (Level {{ $row['level'] }})
                                </td>
                                <td>
                                </td>
                                <td class="{{ $row['production']->metal->get() > 0 ? 'overmark' : ($row['production']->metal->get() < 0 ? 'undermark' : 'normalmark') }}">
                                <span class="tooltipCustom " title="{{ $row['production']->metal->getFormatted() }}">
                                    {{ $row['production']->metal->getFormatted() }}
                                </span>
                                </td>
                                <td class="{{ $row['production']->crystal->get() > 0 ? 'overmark' : ($row['production']->crystal->get() < 0 ? 'undermark' : 'normalmark') }}">
                                <span class="tooltipCustom " title="{{ $row['production']->crystal->getFormatted() }}">
                                    {{ $row['production']->crystal->getFormatted() }}
                                </span>
                                </td>
                                <td class="{{ $row['production']->deuterium->get() > 0 ? 'overmark' : ($row['production']->deuterium->get() < 0 ? 'undermark' : 'normalmark') }}">
                                <span class="tooltipCustom "
                                      title="{{ $row['production']->deuterium->getFormatted() }}">
                                    {{ $row['production']->deuterium->getFormatted() }}
                                </span>
                                </td>
                                <td class="{{ ($row['production']->energy->get() * -1) > 0 ? 'undermark' : (($row['production']->energy->get() * -1) < 0 ? 'overmark' : 'normalmark') }}">
                                <span class="tooltipCustom "
                                      title="{{ number_format($row['actual_energy_use'] * -1, 0, ',', '.') }}/{{ number_format($row['production']->energy->get() * -1, 0, ',', '.') }}">
                                    {{ number_format($row['actual_energy_use'] * -1, 0, ',', '.') }}/{{ number_format($row['production']->energy->get() * -1, 0, ',', '.') }}
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
                                    {{ $row['title'] }} (Level {{ $row['level'] }})
                                </td>
                                <td>
                                </td>
                                <td class="{{ $row['production']->metal->get() > 0 ? 'overmark' : ($row['production']->metal->get() < 0 ? 'undermark' : 'normalmark') }}">
                                <span class="tooltipCustom " title="{{ $row['production']->metal->getFormatted() }}">
                                    {{ $row['production']->metal->getFormatted() }}
                                </span>
                                </td>
                                <td class="{{ $row['production']->crystal->get() > 0 ? 'overmark' : ($row['production']->crystal->get() < 0 ? 'undermark' : 'normalmark') }}">
                                <span class="tooltipCustom " title="{{ $row['production']->crystal->getFormatted() }}">
                                    {{ $row['production']->crystal->getFormatted() }}
                                </span>
                                </td>
                                <td class="{{ $row['production']->deuterium->get() > 0 ? 'overmark' : ($row['production']->deuterium->get() < 0 ? 'undermark' : 'normalmark') }}">
                                <span class="tooltipCustom "
                                      title="{{ $row['production']->deuterium->getFormatted() }}">
                                    {{ $row['production']->deuterium->getFormatted() }}
                                </span>
                                </td>
                                <td class="{{ ($row['production']->energy->get()) > 0 ? 'undermark' : 'normalmark' }}">
                                <span class="tooltipCustom " title=" {{ $row['production']->energy->getFormatted() }}">
                                    {{ $row['production']->energy->getFormatted() }}
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
                        <tr class="alt">
                            <td class="label">
                                Solar Satellite (Number: 0)
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
                                <select name="last212" size="1" class="overmark dropdownInitialized"
                                        style="display: none;">
                                    <option class="undermark" value="100">100%</option>
                                    <option class="undermark" value="90">90%</option>
                                    <option class="undermark" value="80">80%</option>
                                    <option class="undermark" value="70">70%</option>
                                    <option class="middlemark" value="60">60%</option>
                                    <option class="middlemark" value="50">50%</option>
                                    <option class="middlemark" value="40">40%</option>
                                    <option class="overmark" value="30">30%</option>
                                    <option class="overmark" value="20">20%</option>
                                    <option class="overmark" value="10">10%</option>
                                    <option class="overmark" value="0" selected="">0%</option>
                                </select><span class="dropdown currentlySelected overmark" rel="dropdown172"
                                               style="width: 67px;"><a class="overmark" data-value="0" rel="dropdown172"
                                                                       href="javascript:void(0);">0%</a></span>
                            </td>
                        </tr>
                        <tr class="">
                            <td class="label">
                                Plasma Technology (Level 0)
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
                        <tr class="alt">
                            <td class="label">
                                Items
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
                                Geologist
                            </td>
                            <td>
                                <div class="tooltipCustom smallOfficer geologe grayscale" title="+10% mine production">
                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="25" height="25">
                                </div>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled " title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="alt">
                            <td class="label">
                                Engineer
                            </td>
                            <td>
                                <div class="tooltipCustom smallOfficer engineer grayscale"
                                     title="+10% energy production">
                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="25" height="25">
                                </div>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="">
                            <td class="label">
                                Commanding Staff
                            </td>
                            <td>
                                <div class="tooltipCustom smallOfficer stab grayscale"
                                     title="+2% mine production<br>+2% energy production">
                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="25" height="25">
                                </div>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td class="normalmark">
                                <span class="tooltipCustom disabled" title="0">
                                    0
                                </span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="">
                            <td colspan="2" class="label">Storage capacity</td>
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
                            <td colspan="2" class="label"><em>Total per hour:</em></td>
                            <td class="undermark">
                            <span class="tooltipCustom" title="{{ $production_total->metal->getFormatted() }}">
                                {{ $production_total->metal->getFormatted() }}
                            </span>
                            </td>
                            <td class="undermark">
                            <span class="tooltipCustom" title="{{ $production_total->crystal->getFormatted() }}">
                                {{ $production_total->crystal->getFormatted() }}
                            </span>
                            </td>
                            <td class="undermark">
                            <span class="tooltipCustom" title="{{ $production_total->deuterium->getFormatted() }}">
                                {{ $production_total->deuterium->getFormatted() }}
                            </span>
                            </td>
                            <td class="{{ ($production_total->energy->getFormatted() > 0) ? 'undermark' : 'overmark' }}">
                            <span class="tooltipCustom" title="{{ $production_total->energy->getFormatted() }}">
                                {{ $production_total->energy->getFormatted() }}
                            </span>
                            </td>
                            <td></td>
                        </tr>
                        <tr class="">
                            <td colspan="2" class="label"><em>Total per day:</em></td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ $production_total->metal->getFormatted(24) }}">
                                    {{ $production_total->metal->getFormatted(24) }}
                                </span>
                            </td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ $production_total->crystal->getFormatted(24) }}">
                                    {{ $production_total->crystal->getFormatted(24) }}
                                </span>
                            </td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ $production_total->deuterium->getFormatted(24) }}">
                                    {{ $production_total->deuterium->getFormatted(24) }}
                                </span>
                            </td>
                            <td class="{{ ($production_total->energy->getFormatted(24) > 0) ? 'undermark' : 'overmark' }}">
                                <span class="tooltipCustom" title="{{ $production_total->energy->getFormatted(24) }}">
                                    {{ $production_total->energy->getFormatted(24) }}
                                </span>
                            </td>
                            <td></td>
                        </tr>
                        <tr class="alt">
                            <td colspan="2" class="label"><em>Total per week:</em></td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ $production_total->metal->getFormatted(168) }}">
                                    {{ $production_total->metal->getFormatted(168) }}
                                </span>
                            </td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ $production_total->crystal->getFormatted(168) }}">
                                    {{ $production_total->crystal->getFormatted(168) }}
                                </span>
                            </td>
                            <td class="undermark">
                                <span class="tooltipCustom" title="{{ $production_total->deuterium->getFormatted(168) }}">
                                    {{ $production_total->deuterium->getFormatted(168) }}
                                </span>
                            </td>
                            <td class="{{ ($production_total->energy->getFormatted(168) > 0) ? 'undermark' : 'overmark' }}">
                                <span class="tooltipCustom" title="{{ $production_total->energy->getFormatted(168) }}">
                                    {{ $production_total->energy->getFormatted(168) }}
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
