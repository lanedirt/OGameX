@include('ingame.shared.buddy.bbcode-parser')

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Alliance Information') }}</title>
    <link rel="stylesheet" href="{{ asset('css/ingame.css') }}">
    <style>
        body {
            color: #848484;
            font: 100 12px Verdana, Arial, SunSans-Regular, Sans-Serif;
            background-position: center;
            background-attachment: fixed;
        }

        #allianceInfo td, #allianceInfo th{
            background-color:#0D1014;
            font: 12px Verdana, Arial, SunSans-Regular, Sans-Serif;
            text-align:center;
        }

        #allianceInfo .allyText {
            padding:5px;
            text-align:left;
        }

        #allianceInfo ul, ol {
            list-style-position:outside;
            list-style-type:square;
            list-style-image:inherit;
            margin: 20px 0 20px 20px;
            padding:0;
        }

        #allianceInfo li {
            display:list-item;
        }

        /* beware: keep the following in sync with 02base.css */
        .alliance_class.small {
            margin-left: 22px;
            position: relative;
        }
        .alliance_class.small:before {
            content: '';
            position: absolute;
            left: -23px;
            top: -3px;
            width: 20px;
            height: 20px;
        }
        .alliance_class.small.none:before {
            background: url("{{ asset('img/alliance/91f4cdf171328d7cef3443860cd063.png') }}") no-repeat;
        }
        .alliance_class.small.explorer:before {
            background: url("{{ asset('img/alliance/bb0ff2146d93887ff9bf2a14a25a45.png') }}") no-repeat;
        }
        .alliance_class.small.trader:before {
            background: url("{{ asset('img/alliance/02614bfe12340c2d8f89ce58ad83c7.png') }}") no-repeat;
        }
        .alliance_class.small.warrior:before {
            background: url("{{ asset('img/alliance/f2435fcc1304b0d181323254d3d3ec.png') }}") no-repeat;
        }
        /* beware: keep the stuff above in sync with 02base.css */
    </style>
</head>
<body class="no-touch">
    <div id="allianceInfo">
        <center>
            <table width="519" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                        <td style="background-color:#13181D; font-weight:bold;" colspan="2">
                            {{ __('Alliance Information') }}
                        </td>
                    </tr>
                    @if($alliance->logo_url)
                        <tr>
                            <td colspan="2">
                                <img src="{{ $alliance->logo_url }}" class="allylogo" alt="{{ __('Alliance logo') }}" style="image-orientation: from-image;">
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>{{ __('Tag') }}</td>
                        <td>
                            {{ $alliance->alliance_tag }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('Name') }}</td>
                        <td>
                            {{ $alliance->alliance_name }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('Member') }}</td>
                        <td>
                            {{ $memberCount }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('Alliance highscore') }}</td>
                        <td>
                            {{ $allianceRank ?? '-' }}
                        </td>
                    </tr>
                    @if($alliance->external_text)
                        <tr>
                            <td colspan="2" height="100" class="textLeft allyText" id="allianceTextContent">
                                {{ $alliance->external_text }}
                            </td>
                        </tr>
                    @endif
                    @if($alliance->homepage_url)
                        <tr>
                            <td>{{ __('Homepage') }}</td>
                            <td>
                                <a href="javascript:void(0);" data-homepage-link="">{{ $alliance->homepage_url }}</a>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if($canApply)
                <center style="margin-top: 20px;">
                    <button class="btn_blue" id="applyToAllianceBtn" style="padding: 5px 20px;">
                        {{ __('Apply to Alliance') }}
                    </button>
                </center>
            @endif
        </center>

        <div id="decisionTB" style="display:none;">
            <div id="errorBoxDecision" class="errorBox TBfixedPosition">
                <div class="head"><h4 id="errorBoxDecisionHead">-</h4></div>
                <div class="middle">
                    <span id="errorBoxDecisionContent">-</span>
                    <div class="response">
                        <div style="float:left; width:180px;">
                            <a href="javascript:void(0);" class="yes"><span id="errorBoxDecisionYes">.</span></a>
                        </div>
                        <div style="float:left; width:180px;">
                            <a href="javascript:void(0);" class="no"><span id="errorBoxDecisionNo">.</span></a>
                        </div>
                        <br class="clearfloat">
                    </div>
                </div>
                <div class="foot"></div>
            </div>
        </div>

        <div id="fadeBox" class="fadeBox fixedPostion" style="display:none;" role="alert">
            <span id="fadeBoxStyle" class="success"></span>
            <p id="fadeBoxContent"></p>
        </div>

        <div id="notifyTB" style="display:none;">
            <div id="errorBoxNotify" class="errorBox TBfixedPosition">
                <div class="head"><h4 id="errorBoxNotifyHead">-</h4></div>
                <div class="middle">
                    <span id="errorBoxNotifyContent">-</span>
                    <div class="response">
                        <div>
                            <a href="javascript:void(0);" class="ok">
                                <span id="errorBoxNotifyOk">.</span>
                            </a>
                        </div>
                        <br class="clearfloat">
                    </div>
                </div>
                <div class="foot"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('js/ingame.js') }}"></script>
    <script type="text/javascript">
        isMobile = false;
        isMobileApp = false;
        LocalizationStrings = {!! json_encode([
            'attention' => __('Caution'),
            'yes' => __('yes'),
            'no' => __('No'),
            'ok' => __('Ok'),
            'redirectMessage' => __('By following this link, you will leave OGame. Do you wish to continue?'),
        ]) !!};
        var allyHome = '{{ $alliance->homepage_url ?? '' }}';

        initAllianceInfo();
        initBBCodes();

        // Parse BBCode in alliance text on page load
        $(document).ready(function() {
            var allianceTextElement = document.getElementById('allianceTextContent');
            if (allianceTextElement && typeof window.buddyBBCodeParser === 'function') {
                var rawText = allianceTextElement.textContent.trim();
                if (rawText) {
                    allianceTextElement.innerHTML = window.buddyBBCodeParser(rawText);
                }
            }

            // Handle apply to alliance button
            $('#applyToAllianceBtn').on('click', function() {
                errorBoxDecision(
                    LocalizationStrings.attention,
                    '{{ __('Do you want to apply to this alliance?') }}',
                    LocalizationStrings.yes,
                    LocalizationStrings.no,
                    function() {
                        $.ajax({
                            url: '{{ route('alliance.apply') }}',
                            type: 'POST',
                            data: {
                                alliance_id: {{ $alliance->id }},
                                message: '',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    fadeBox(response.message || '{{ __('Application submitted successfully') }}', false);
                                    setTimeout(function() {
                                        window.close();
                                    }, 2000);
                                } else {
                                    fadeBox(response.message || '{{ __('Failed to submit application') }}', true);
                                }
                            },
                            error: function(xhr) {
                                var errorMessage = '{{ __('Failed to submit application') }}';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                fadeBox(errorMessage, true);
                            }
                        });
                    }
                );
            });
        });
    </script>
</body>
</html>
