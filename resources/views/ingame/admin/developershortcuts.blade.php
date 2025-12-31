@extends('ingame.layouts.main')

@section('content')

    @php /** @var \OGame\Services\PlanetService $currentPlanet */ @endphp

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>@lang('Developer shortcuts')</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>@lang('Developer shortcuts')</h2>
            </div>
            <div class="content">
                <div class="buddylistContent">
                    <form action="{{ route('admin.developershortcuts.update') }}" name="form" method="post">
                        {{ csrf_field() }}
                                <p class="box_highlight textCenter no_buddies">@lang('Update current planet:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <input type="submit" class="btn_blue" name="set_mines" value="@lang('Set all mines to level 30')">
                                        <input type="submit" class="btn_blue" name="set_storages" value="@lang('Set all storages to level 15')">
                                        <input type="submit" class="btn_blue" name="set_shipyard" value="@lang('Set all shipyard facilities to level 12')">
                                        <input type="submit" class="btn_blue" name="set_research" value="@lang('Set all research to level 10')">
                                    </div>
                                </div>

                                <p class="box_highlight textCenter no_buddies">@lang('Add X of unit to current planet:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <label class="styled textBeefy">@lang('Amount of units to add:')</label>
                                        <div class="thefield">
                                            <input type="text" pattern="^[0-9,.kmb]+$" class="textInput w50 textCenter textBeefy" placeholder="1" size="2" name="amount_of_units">
                                        </div>
                                    </div>
                                    <div class="fieldwrapper">
                                        @php /** @var OGame\GameObjects\Models\UnitObject $unit */ @endphp
                                        @foreach ($units as $unit)
                                            <input type="submit" name="unit_{{ $unit->id }}" class="btn_blue" value="{{ $unit->title }}">
                                        @endforeach
                                        <input type="submit" class="btn_blue" value="@lang('Light fighter')">
                                    </div>
                                </div>

                                <p class="box_highlight textCenter no_buddies">@lang('Set building level on current planet:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <label class="styled textBeefy">@lang('Level to set:')</label>
                                        <div class="thefield">
                                            <input type="text" pattern="^[0-9]+$" placeholder="0" class="textInput w50 textCenter textBeefy" size="2" name="building_level">
                                        </div>
                                    </div>
                                    <div class="fieldwrapper">
                                        @foreach ($buildings as $building)
                                            <input type="submit" name="building_{{ $building->id }}" class="btn_blue" value="{{ $building->title }}">
                                        @endforeach
                                    </div>
                                </div>

                                <p class="box_highlight textCenter no_buddies">@lang('Set research level for current player:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <label class="styled textBeefy">@lang('Level to set:')</label>
                                        <div class="thefield">
                                            <input type="text" pattern="^[0-9]+$" placeholder="0" class="textInput w50 textCenter textBeefy" size="2" name="research_level">
                                        </div>
                                    </div>
                                    <div class="fieldwrapper">
                                        @foreach ($research as $tech)
                                            <input type="submit" name="research_{{ $tech->id }}" class="btn_blue" value="{{ $tech->title }}">
                                        @endforeach
                                    </div>
                                </div>

                                <p class="box_highlight textCenter no_buddies">@lang('Character Class Settings')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        @php
                                            $freeClassChanges = app(\OGame\Services\SettingsService::class)->get('dev_free_class_changes', false);
                                        @endphp
                                        @if($freeClassChanges)
                                            <input type="submit" class="btn_blue" name="disable_free_class_changes" value="@lang('Disable Free Class Changes')">
                                        @else
                                            <input type="submit" class="btn_blue" name="enable_free_class_changes" value="@lang('Enable Free Class Changes')">
                                        @endif
                                        <input type="submit" class="btn_blue" name="reset_character_class" value="@lang('Reset Character Class')">
                                        <a href="{{ route('characterclass.index') }}" class="btn_blue" style="display: inline-block; padding: 5px 10px; text-decoration: none;">@lang('Go to Class Selection')</a>
                                    </div>
                                </div>

                                <p class="box_highlight textCenter no_buddies">@lang('Reset planet')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <input type="submit" class="btn_blue" name="reset_buildings" value="@lang('Set all buildings to level 0')">
                                        <input type="submit" class="btn_blue" name="reset_research" value="@lang('Set all research to level 0')">
                                        <input type="submit" class="btn_blue" name="reset_units" value="@lang('Remove all units')">
                                        <input type="submit" class="btn_blue" name="reset_resources" value="@lang('Set all resources to 0')">
                                    </div>
                                </div>
                            </form>

                            <form action="{{ route('admin.developershortcuts.update-resources') }}" name="form" method="post">
                                {{ csrf_field() }}
                                <p class="box_highlight textCenter no_buddies">@lang('Add / subtract resources at coordinates:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <div class="smallFont">@lang('You can enter positive or negative values to add or subtract to the selected resource. Supports k/m/b suffixes (e.g., 1k, 2m, 3b)')</div>
                                        <label class="styled textBeefy">@lang('Coordinates:')</label>
                                        <div class="thefield" style="display: flex; gap: 10px;">
                                            <div>
                                                <label for="galaxy">@lang('Galaxy:')</label>
                                                <input type="text" id="galaxy" pattern="^[-+0-9,.kmb]+$" class="textInput w50 textCenter textBeefy"
                                                       value="{{ $currentPlanet->getPlanetCoordinates()->galaxy }}" min="1" max="{{ $settings->numberOfGalaxies() }}" name="galaxy">
                                            </div>
                                            <div>
                                                <label for="system">@lang('System:')</label>
                                                <input type="text" id="system" pattern="^[-+0-9,.kmb]+$" class="textInput w50 textCenter textBeefy"
                                                       value="{{ $currentPlanet->getPlanetCoordinates()->system }}" min="1" max="499" name="system">
                                            </div>
                                            <div>
                                                <label for="position">@lang('Position:')</label>
                                                <input type="text" id="position" pattern="^[-+0-9,.kmb]+$" class="textInput w50 textCenter textBeefy"
                                                       value="{{ $currentPlanet->getPlanetCoordinates()->position }}" min="1" max="15" name="position">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fieldwrapper"><label class="styled textBeefy">@lang('Resources to add/subtract:')</label>
                                        <div class="thefield" style="display: flex; flex-direction: column; gap: 10px;">
                                            @foreach (\OGame\Models\Enums\ResourceType::cases() as $resource)
                                                <div style="display: flex; gap: 10px;">
                                                    <label for="{{ $resource->value }}" style="min-width: 80px;">{{ $resource->name }}:</label>
                                                    <input type="text" id="{{ $resource->value }}" pattern="^[-+0-9,.kmb]+$"
                                                           class="textInput w100 textCenter textBeefy"
                                                           placeholder="0" name="{{ $resource->value }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="fieldwrapper" style="text-align: center;">
                                        <input type="submit" class="btn_blue" name="update_resources_planet" value="Update Resources (planet)">
                                        <input type="submit" class="btn_blue" name="update_resources_moon" value="Update Resources (moon)">
                                    </div>
                                </div>
                            </form>

                            <form action="{{ route('admin.developershortcuts.create-at-coords') }}" name="form" method="post">
                                {{ csrf_field() }}
                                <p class="box_highlight textCenter no_buddies">@lang('Create planet/moon at coordinates:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <label class="styled textBeefy">@lang('Coordinates:')</label>
                                        <div class="thefield" style="display: flex; gap: 10px;">
                                            <div>
                                                <label for="galaxy">@lang('Galaxy:')</label>
                                                <input type="text" id="galaxy" pattern="^[-+0-9,.kmb]+$" class="textInput w50 textCenter textBeefy"
                                                       value="{{ $currentPlanet->getPlanetCoordinates()->galaxy }}" min="1" max="{{ $settings->numberOfGalaxies() }}" name="galaxy">
                                            </div>
                                            <div>
                                                <label for="system">@lang('System:')</label>
                                                <input type="text" id="system" pattern="^[-+0-9,.kmb]+$" class="textInput w50 textCenter textBeefy"
                                                       value="{{ $currentPlanet->getPlanetCoordinates()->system }}" min="1" max="499" name="system">
                                            </div>
                                            <div>
                                                <label for="position">@lang('Position:')</label>
                                                <input type="text" id="position" pattern="^[-+0-9,.kmb]+$" class="textInput w50 textCenter textBeefy"
                                                       value="{{ $currentPlanet->getPlanetCoordinates()->position }}" min="1" max="15" name="position">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fieldwrapper">
                                        <label class="styled textBeefy">@lang('Moon Size (for Create Moon):')</label>
                                        <div class="thefield" style="display: flex; gap: 15px; align-items: flex-start;">
                                            <div style="flex: 1;">
                                                <label for="moon_debris" style="display: block; margin-bottom: 5px;">@lang('Debris Amount:')</label>
                                                <input type="text" id="moon_debris" pattern="^[-+0-9,.kmb]+$" class="textInput textCenter textBeefy"
                                                       style="width: 100%;" value="2000000" placeholder="2000000" name="moon_debris" title="Total debris (metal+crystal+deuterium) that determines moon size">
                                                <span style="display: block; font-size: 0.9em; color: #666; margin-top: 5px;">Examples: 100k, 500k, 1M, 2M</span>
                                            </div>
                                            <div style="flex: 1;">
                                                <label for="moon_factor" style="display: block; margin-bottom: 5px;">@lang('X Factor (10-20):')</label>
                                                <input type="text" id="moon_factor" pattern="^[0-9]+$" class="textInput textCenter textBeefy"
                                                       style="width: 100%;" value="" placeholder="Random" name="moon_factor" min="10" max="20" title="X factor in formula (10-20). Leave blank for random.">
                                                <span style="display: block; font-size: 0.9em; color: #666; margin-top: 5px;">Leave blank = random</span>
                                            </div>
                                        </div>
                                        <span style="display: block; font-size: 0.9em; color: #999; margin-top: 8px; font-style: italic;">Formula: diameter = floor((x + 3*debris/100000)^0.5 * 1000)</span>
                                    </div>
                                    <div class="fieldwrapper" style="text-align: center; margin-bottom: 20px;">
                                        <input type="submit" class="btn_blue" name="create_planet" value="@lang('Create Planet')">
                                        <input type="submit" class="btn_blue" name="create_moon" value="@lang('Create Moon')">
                                        <input type="submit" class="btn_blue" name="delete_planet" value="@lang('Delete Planet')">
                                        <input type="submit" class="btn_blue" name="delete_moon" value="@lang('Delete Moon')">
                                    </div>
                                </div>
                            </form>

                            <form action="{{ route('admin.developershortcuts.create-debris') }}" name="form" method="post">
                                {{ csrf_field() }}
                                <p class="box_highlight textCenter no_buddies">@lang('Create/delete debris field at coordinates:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <label class="styled textBeefy">@lang('Coordinates:')</label>
                                        <div class="thefield" style="display: flex; gap: 10px;">
                                            <div>
                                                <label for="galaxy">@lang('Galaxy:')</label>
                                                <input type="text" id="galaxy" pattern="^[-+0-9,.kmb]+$" class="textInput w50 textCenter textBeefy"
                                                       value="{{ $currentPlanet->getPlanetCoordinates()->galaxy }}" min="1" max="{{ $settings->numberOfGalaxies() }}" name="galaxy">
                                            </div>
                                            <div>
                                                <label for="system">@lang('System:')</label>
                                                <input type="text" id="system" pattern="^[-+0-9,.kmb]+$" class="textInput w50 textCenter textBeefy"
                                                       value="{{ $currentPlanet->getPlanetCoordinates()->system }}" min="1" max="499" name="system">
                                            </div>
                                            <div>
                                                <label for="position">@lang('Position:') (1-16)</label>
                                                <input type="text" id="position" pattern="^[-+0-9,.kmb]+$" class="textInput w50 textCenter textBeefy"
                                                       value="{{ $currentPlanet->getPlanetCoordinates()->position }}" min="1" max="16" name="position">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fieldwrapper">
                                        <label class="styled textBeefy">@lang('Resources to add:')</label>
                                        <div class="thefield" style="display: flex; flex-direction: column; gap: 10px;">
                                            @foreach (\OGame\Models\Enums\ResourceType::cases() as $resource)
                                                <div style="display: flex; gap: 10px;">
                                                    <label for="{{ $resource->value }}" style="min-width: 80px;">{{ $resource->name }}:</label>
                                                    <input type="text" id="{{ $resource->value }}" pattern="^[-+0-9,.kmb]+$"
                                                           class="textInput w100 textCenter textBeefy"
                                                           placeholder="0" name="{{ $resource->value }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="fieldwrapper" style="text-align: center; margin-bottom: 20px;">
                                        <input type="submit" class="btn_blue" name="create_debris" value="@lang('Create/Append Debris Field')">
                                        <input type="submit" class="btn_blue" name="delete_debris" value="@lang('Delete Debris Field')">
                                    </div>
                                    <div class="fieldwrapper" style="text-align: center; margin-bottom: 50px; padding-top: 10px; border-top: 1px solid #444;">
                                        <p style="margin-bottom: 10px; color: #999; font-size: 0.9em;">Quick shortcut for testing Discoverer class:</p>
                                        <button type="submit" class="btn_blue" onclick="document.getElementById('position').value='16'; document.getElementById('metal').value='100000'; document.getElementById('crystal').value='50000'; document.getElementById('deuterium').value='25000'; return true;">
                                            Create Expedition Debris (Position 16)
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <div class="footer">
                        </div>
                </div>
            </div>
            </div>

        <script language="javascript">
            initBBCodes();
            initOverlays();
        </script>
    </div>

@endsection
