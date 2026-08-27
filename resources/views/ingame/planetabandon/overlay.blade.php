@php /** @var OGame\Services\PlanetService $currentPlanet */ @endphp
<div id="abandonplanet">
    <img src="{!! asset('img/planets/big/' . $currentPlanet->getPlanetBiomeType() . '_' . $currentPlanet->getPlanetImageType() . '.png') !!}"
         class="float_left"/>
    <p class="desc_txt">{{ __('t_ingame.planet_abandon.description') }}</p>
    <table cellpadding="0" cellspacing="0">
        <tbody>
        <tr class="head">
            <th colspan="3">{{ __('t_ingame.planet_abandon.rename_heading') }}</th>
        </tr>
        <tr>
            <td colspan="3" class="ipiHintable" data-ipi-hint="ipiPlanetSettingsName">
                <form id="planetMaintenance" class="formValidation"
                      onsubmit="clearField(); $('#newPlanetName').val($('#planetName').val()); ajaxFormSubmit('planetMaintenance', '{{ route('planetabandon.rename') }}', planetRenamed); return false;">
                    <input type="hidden" id="newPlanetName" name="newPlanetName" value="{{ $isMoon ? __('t_ingame.planet_abandon.new_moon_name') : __('t_ingame.planet_abandon.new_planet_name') }}">
                    <input type='hidden' name='_token' value='{{ csrf_token() }}'/>

                    @if ($isMoon)
                        <a title="{{ __('t_ingame.planet_abandon.tooltip_rules_title') }}|{{ __('t_ingame.planet_abandon.tooltip_rename_moon') }}"
                       href="javascript:void(0);"
                       class="tooltipHTML tooltipLeft help"></a>
                    @else
                        <a title="{{ __('t_ingame.planet_abandon.tooltip_rules_title') }}|{{ __('t_ingame.planet_abandon.tooltip_rename_planet') }}"
                       href="javascript:void(0);"
                       class="tooltipHTML tooltipLeft help"></a>
                    @endif
                    <input
                            class="text w200 validate[optional,custom[noSpecialCharacters],custom[noBeginOrEndUnderscore],custom[noBeginOrEndWhitespace],custom[noBeginOrEndHyphen],custom[notMoreThanThreeUnderscores],custom[notMoreThanThreeWhitespaces],custom[notMoreThanThreeHyphen],custom[noCollocateUnderscores],custom[noCollocateWhitespaces],custom[noCollocateHyphen],minSize[2]]"
                            type="text"
                            maxlength="20"
                            size="25"
                            id="planetName"
                            value="{{ $isMoon ? __('t_ingame.planet_abandon.new_moon_name') : __('t_ingame.planet_abandon.new_planet_name') }}"
                            onFocus="clearField()"
                            onBlur="fillField()"
                    />
                    <input class="btn_blue float_right" type="submit" value="{{ __('t_ingame.planet_abandon.rename_btn') }}" name="aktion"/>
                </form>
            </td>
        </tr>
        <tr class="head">
            <th colspan="3" class="second" id="giveupHeadline" rel="1">
                @if ($isCurrentPlanetHomePlanet)
                    {{ __('t_ingame.planet_abandon.abandon_home_planet') }}
                @elseif ($isMoon)
                    {{ __('t_ingame.planet_abandon.abandon_moon') }}
                @else
                    {{ __('t_ingame.planet_abandon.abandon_colony') }}
                @endif
            </th>
        </tr>

        @if ($isCurrentPlanetHomePlanet)
            <tr>
                <td colspan="3">
                    {{ __('t_ingame.planet_abandon.home_planet_warning') }}
                </td>
            </tr>
        @endif

        <tr>
            <td id="giveupCoordinates">[{{ $currentPlanet->getPlanetCoordinates()->asString() }}]</td>
            <td id="giveupName">{{ $currentPlanet->getPlanetName() }}</td>
            <td>
                <a id="block" class="start btn_blue float_right">
                    @if ($isCurrentPlanetHomePlanet)
                        {{ __('t_ingame.planet_abandon.abandon_home_planet_btn') }}
                    @elseif ($isMoon)
                        {{ __('t_ingame.planet_abandon.abandon_moon_btn') }}
                    @else
                        {{ __('t_ingame.planet_abandon.abandon_colony_btn') }}
                    @endif
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <form id="planetMaintenanceDelete" action="{{ route('planetabandon.abandon.confirm') }}">
                    <input type='hidden' name='_token' value='{{ csrf_token() }}'/>
                    <div id="giveUpNotification">
                        @if ($isMoon)
                            {{ __('t_ingame.planet_abandon.items_lost_moon') }}
                        @else
                            {{ __('t_ingame.planet_abandon.items_lost_planet') }}
                        @endif
                    </div>
                    <div class="validate" id="validate" style="display:none;">
                        <p class="margin_10_0">{{ __('t_ingame.planet_abandon.confirm_password', [
                            'type' => $isMoon ? __('t_ingame.planet_abandon.type_moon') : __('t_ingame.planet_abandon.type_planet'),
                            'coordinates' => $currentPlanet->getPlanetCoordinates()->asString()
                        ]) }}</p>
                        <input class="text w200 pw_field" type="password" name="password" maxlength="1024" size="25"/>
                        <input class="btn_blue" type="submit" value="{{ __('t_ingame.planet_abandon.confirm_btn') }}"/>
                    </div>
                </form>
            </td>
        </tr>
        </tbody>
    </table>

    <script type="text/javascript">
        (function ($) {
            var locaValidation = {
                minSize: {!! json_encode(__('t_ingame.planet_abandon.validation_min_chars')) !!},
                pwMinSize: {!! json_encode(__('t_ingame.planet_abandon.validation_pw_min')) !!},
                pwMaxSize: {!! json_encode(__('t_ingame.planet_abandon.validation_pw_max')) !!},
                email: {!! json_encode(__('t_ingame.planet_abandon.validation_email')) !!},
                noSpecialCharacters: {!! json_encode(__('t_ingame.planet_abandon.validation_special')) !!},
                noBeginOrEndUnderscore: {!! json_encode(__('t_ingame.planet_abandon.validation_underscore')) !!},
                noBeginOrEndHyphen: {!! json_encode(__('t_ingame.planet_abandon.validation_hyphen')) !!},
                noBeginOrEndWhitespace: {!! json_encode(__('t_ingame.planet_abandon.validation_space')) !!},
                notMoreThanThreeUnderscores: {!! json_encode(__('t_ingame.planet_abandon.validation_max_underscores')) !!},
                notMoreThanThreeHyphen: {!! json_encode(__('t_ingame.planet_abandon.validation_max_hyphens')) !!},
                notMoreThanThreeWhitespaces: {!! json_encode(__('t_ingame.planet_abandon.validation_max_spaces')) !!},
                noCollocateUnderscores: {!! json_encode(__('t_ingame.planet_abandon.validation_consec_underscores')) !!},
                noCollocateHyphen: {!! json_encode(__('t_ingame.planet_abandon.validation_consec_hyphens')) !!},
                noCollocateWhitespaces: {!! json_encode(__('t_ingame.planet_abandon.validation_consec_spaces')) !!}
            };

            $.fn.validationEngineLanguage = function () {
            };
            $.validationEngineLanguage = {
                newLang: function () {
                    $.validationEngineLanguage.allRules = {
                        "minSize": {
                            "regex": "none",
                            "alertText": locaValidation.minSize
                        },
                        "pwMinSize": {
                            "regex": /^.{ 4,}$/,
                            "alertText": locaValidation.pwMinSize
                        },
                        "pwMaxSize": {
                            "regex": /^.{0, 20}$/,
                            "alertText": locaValidation.pwMaxSize
                        },
                        "email": {
                            "regex": /^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/,
                            "alertText": locaValidation.email
                        },
                        "noSpecialCharacters": {
                            "regex": /^[a-zA-Z0-9\-_\s]+$/,
                            "alertText": locaValidation.noSpecialCharacters
                        },
                        "noBeginOrEndUnderscore": {
                            "regex": /^([^_]+(.*[^_])?)?$/,
                            "alertText": locaValidation.noBeginOrEndUnderscore
                        },
                        "noBeginOrEndHyphen": {
                            "regex": /^([^\-]+(.*[^\-])?)?$/,
                            "alertText": locaValidation.noBeginOrEndHyphen
                        },
                        "noBeginOrEndWhitespace": {
                            "regex": /^([^\s]+(.*[^\s])?)?$/,
                            "alertText": locaValidation.noBeginOrEndWhitespace
                        },
                        "notMoreThanThreeUnderscores": {
                            "regex": /^[^_]*(_[^_]*){0,3}$/,
                            "alertText": locaValidation.notMoreThanThreeUnderscores
                        },
                        "notMoreThanThreeHyphen": {
                            "regex": /^[^\-]*(\-[^\-]*){0,3}$/,
                            "alertText": locaValidation.notMoreThanThreeHyphen
                        },
                        "notMoreThanThreeWhitespaces": {
                            "regex": /^[^\s]*(\s[^\s]*){0,3}$/,
                            "alertText": locaValidation.notMoreThanThreeWhitespaces
                        },
                        "noCollocateUnderscores": {
                            "regex": /^[^_]*(_[^_]+)*_?$/,
                            "alertText": locaValidation.noCollocateUnderscores
                        },
                        "noCollocateHyphen": {
                            "regex": /^[^\-]*(\-[^\-]+)*-?$/,
                            "alertText": locaValidation.noCollocateHyphen
                        },
                        "noCollocateWhitespaces": {
                            "regex": /^[^\s]*(\s[^\s]+)*\s?$/,
                            "alertText": locaValidation.noCollocateWhitespaces
                        }

                    }
                }
            }
            $.validationEngineLanguage.newLang();
        })(jQuery);
    </script>
    <script language="javascript">
        var defaultName = {!! json_encode($isMoon ? __('t_ingame.planet_abandon.new_moon_name') : __('t_ingame.planet_abandon.new_planet_name')) !!};
    </script>
    <script>
        initFormValidation();
        if (typeof IPI !== 'undefined') {
            IPI.refreshHighlights()
        }
    </script>
</div>
