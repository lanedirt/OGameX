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
            <h2>Developer shortcuts</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>Developer shortcuts</h2>
            </div>
            <div class="content">
                <div class="buddylistContent">
                    <form action="{{ route('admin.developershortcuts.update') }}" name="form" method="post">
                        {{ csrf_field() }}
                                <p class="box_highlight textCenter no_buddies">@lang('Update current planet:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <input type="submit" class="btn_blue" name="set_mines" value="Set all mines to level 30">
                                        <input type="submit" class="btn_blue" name="set_storages" value="Set all storages to level 15">
                                        <input type="submit" class="btn_blue" name="set_shipyard" value="Set all shipyard facilities to level 12">
                                        <input type="submit" class="btn_blue" name="set_research" value="Set all research to level 10">
                                    </div>
                                </div>

                                <p class="box_highlight textCenter no_buddies">@lang('Add X of unit to current planet:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <label class="styled textBeefy">Amount of units to add:</label>
                                        <div class="thefield">
                                            <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="1" size="2" name="amount_of_units">
                                        </div>
                                    </div>
                                    <div class="fieldwrapper">
                                        @php /** @var OGame\GameObjects\Models\UnitObject $unit */ @endphp
                                        @foreach ($units as $unit)
                                            <input type="submit" name="unit_{{ $unit->id }}" class="btn_blue" value="{{ $unit->title }}">
                                        @endforeach
                                        <input type="submit" class="btn_blue" value="Light fighter">
                                    </div>
                                </div>

                                <p class="box_highlight textCenter no_buddies">@lang('Reset planet')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper" style="margin-bottom: 50px;">
                                        <input type="submit" class="btn_blue" name="reset_buildings" value="Set all buildings to level 0">
                                        <input type="submit" class="btn_blue" name="reset_research" value="Set all research to level 0">
                                        <input type="submit" class="btn_blue" name="reset_units" value="Remove all units">
                                    </div>
                                </div>
                            </form>

                            <form action="{{ route('admin.developershortcuts.update-resources') }}" name="form" method="post">
                                {{ csrf_field() }}
                                <p class="box_highlight textCenter no_buddies">@lang('Add / Subtract X of resource to current planet:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <div class="smallFont">You can enter positive or negative values to add or subtract to the selected resource.</div>
                                        <label class="styled textBeefy">Amount of resources to add / subtract :</label>
                                        <div class="thefield">
                                            <input type="text" pattern="^([-+,0-9.]+)" class="textInput w50 textCenter textBeefy" value="1" size="2" name="amount_of_resources">
                                        </div>
                                    </div>
                                    <div class="fieldwrapper" style="margin-bottom: 50px;">
                                        @foreach (\OGame\Models\Enums\ResourceType::cases() as $resource)
                                            <input type="submit" name="resource_{{ $resource->value }}" class="btn_blue" value="{{$resource->name}}">
                                        @endforeach
                                    </div>
                                </div>
                            </form>

                            <form action="{{ route('admin.developershortcuts.create-at-coords') }}" name="form" method="post">
                                {{ csrf_field() }}
                                <p class="box_highlight textCenter no_buddies">@lang('Create planet/moon at coordinates:')</p>
                                <div class="group bborder" style="display: block;">
                                    <div class="fieldwrapper">
                                        <label class="styled textBeefy">Coordinates:</label>
                                        <div class="thefield" style="display: flex; gap: 10px;">
                                            <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy"
                                                   value="{{ $currentPlanet->getPlanetCoordinates()->galaxy }}" min="1" max="6" name="galaxy" placeholder="Galaxy">
                                            <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy"
                                                   value="{{ $currentPlanet->getPlanetCoordinates()->system }}" min="1" max="499" name="system" placeholder="System">
                                            <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy"
                                                   value="{{ $currentPlanet->getPlanetCoordinates()->position }}" min="1" max="15" name="position" placeholder="Position">
                                        </div>
                                    </div>
                                    <div class="fieldwrapper" style="margin-bottom: 50px;">
                                        <input type="submit" class="btn_blue" name="create_planet" value="Create Planet (For Myself)">
                                        <input type="submit" class="btn_blue" name="create_moon" value="Create Moon (For Planet Owner)">
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
