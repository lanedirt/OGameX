@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>Server settings</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>Server settings</h2>
            </div>
            <form action="{{ route('admin.serversettings.update') }}" name="form" method="post">
                {{ csrf_field() }}
                <div class="content">
                    <div class="buddylistContent">
                        <p class="box_highlight textCenter no_buddies">@lang('You can change the server settings below. Changes will be applied immediately.')</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">Economy speed:</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $economy_speed }}" size="2" maxlength="2" name="economy_speed">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">Research speed:</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $research_speed }}" size="2" maxlength="2" name="research_speed">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">Fleet speed:</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $fleet_speed }}" size="2" maxlength="2" name="fleet_speed">
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">@lang('Note: basic income values below are multiplied by economy speed.')</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">Basic metal income per hour:</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_metal }}" size="6" name="basic_income_metal"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_metal * $economy_speed) }})
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">Basic crystal income per hour:</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_crystal }}" size="6" name="basic_income_crystal"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_crystal * $economy_speed) }})
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">Basic deuterium income per hour:</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_deuterium }}" size="6" name="basic_income_deuterium"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_deuterium * $economy_speed) }})
                                </div>
                            </div>
                            <div class="fieldwrapper" style="margin-bottom: 50px;">
                                <label class="styled textBeefy">Basic energy income per hour:</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_energy }}" size="6" name="basic_income_energy"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_energy * $economy_speed) }})
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="footer">
                        <div class="textCenter">
                            <input type="submit" class="btn_blue" value="Save settings">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <script language="javascript">
            initBBCodes();
            initOverlays();
        </script>
    </div>

@endsection
