<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{{ app()->getLocale() }}">
<head>
    <!--
     ===========================================
       ____   _____                     __   __
      / __ \ / ____|                    \ \ / /
     | |  | | |  __  __ _ _ __ ___   ___ \ V /
     | |  | | | |_ |/ _` | '_ ` _ \ / _ \ > <
     | |__| | |__| | (_| | | | | | |  __// . \
      \____/ \_____|\__,_|_| |_| |_|\___/_/ \_\
     ===========================================

     Powered by OGameX - Explore the universe! Conquer your enemies!
     GitHub: https://github.com/lanedirt/OGameX
     Version: {{ \OGame\Facades\GitInfoUtil::getAppVersionBranchCommit() }}

    This application is released under the MIT License. For more details, visit the GitHub repository.
-->
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="{{ app()->getLocale() }}"/>
    <meta name="author" content="OGameX"/>
    <meta name="publisher" content="OGameX"/>
    <meta name="copyright" content="OGameX"/>
    <meta name="audience" content="all"/>
    <meta name="Expires" content="never"/>
    <meta name="Keywords"
          content="Game, Browser, online, for free, legendary, MMOG, Science fiction, space, space ship"/>
    <meta name="Description"
          content="OGameX - The legendary game in the space! Discover the universe together with thousands of players."/>
    <meta name="robots" content="index,follow"/>
    <meta name="Revisit" content="After 14 days"/>
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="apple-touch-icon" href="/img/outgame/20da7e6c416e6cd5f8544a73f588e5.png"/>
    <link rel="stylesheet" href="{{ mix('css/outgame.css') }}">
    <script type="text/javascript" src="{{ mix('js/outgame.min.js') }}"></script>

    <script type="text/javascript">
        // <![CDATA[
        (function ($) {
            $.fn.validationEngineLanguage = function () {
            };
            $.validationEngineLanguage = {
                newLang: function () {
                    $.validationEngineLanguage.allRules = {
                        "required": {
                            "alertText": {!! json_encode(__('t_external.validation.required')) !!},
                            "alertTextCheckboxMultiple": {!! json_encode(__('t_external.validation.make_decision')) !!},
                            "alertTextCheckboxe": {!! json_encode(__('t_external.validation.accept_terms')) !!}
                        },
                        "length": {
                            "regex": /^.{3,20}$/,
                            "alertText": {!! json_encode(__('t_external.validation.length')) !!}
                        },
                        "pwLength": {
                            "regex": /^.{4,20}$/,
                            "alertText": {!! json_encode(__('t_external.validation.pw_length')) !!}
                        },
                        "email": {
                            "regex": /^[a-zA-Z0-9_.\-]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/,
                            "alertText": {!! json_encode(__('t_external.validation.email')) !!}
                        },
                        "noSpecialCharacters": {
                            "regex": /^[a-zA-Z0-9\s_\-]+$/,
                            "alertText": {!! json_encode(__('t_external.validation.invalid_chars')) !!}
                        },
                        "noBeginOrEndUnderscore": {
                            "regex": /^([^_]+(.*[^_])?)?$/,
                            "alertText": {!! json_encode(__('t_external.validation.no_begin_end_underscore')) !!}
                        },
                        "noBeginOrEndHyphen": {
                            "regex": /^([^\-]+(.*[^\-])?)?$/,
                            "alertText": ""
                        },
                        "noBeginOrEndWhitespace": {
                            "regex": /^([^\s]+(.*[^\s])?)?$/,
                            "alertText": {!! json_encode(__('t_external.validation.no_begin_end_whitespace')) !!}
                        },
                        "notMoreThanThreeUnderscores": {
                            "regex": /^[^_]*(_[^_]*){0,3}$/,
                            "alertText": {!! json_encode(__('t_external.validation.max_three_underscores')) !!}
                        },
                        "notMoreThanThreeHyphen": {
                            "regex": /^[^\-]*(\-[^\-]*){0,3}$/,
                            "alertText": ""
                        },
                        "notMoreThanThreeWhitespaces": {
                            "regex": /^[^\s]*(\s[^\s]*){0,3}$/,
                            "alertText": {!! json_encode(__('t_external.validation.max_three_whitespaces')) !!}
                        },
                        "noCollocateUnderscores": {
                            "regex": /^[^_]*(_[^_]+)*_?$/,
                            "alertText": {!! json_encode(__('t_external.validation.no_consecutive_underscores')) !!}
                        },
                        "noCollocateHyphen": {
                            "regex": /^[^\-]*(\-[^\-]+)*-?$/,
                            "alertText": ""
                        },
                        "noCollocateWhitespaces": {
                            "regex": /^[^\s]*(\s[^\s]+)*\s?$/,
                            "alertText": {!! json_encode(__('t_external.validation.no_consecutive_whitespaces')) !!}
                        },
                        "ajaxUser": {
                            "file": "../validateUser.php",
                            "alertTextOk": {!! json_encode(__('t_external.validation.username_available')) !!},
                            "alertTextLoad": {!! json_encode(__('t_external.validation.username_loading')) !!},
                            "alertText": {!! json_encode(__('t_external.validation.username_taken')) !!}
                        },
                        "ajaxName": {
                            "file": "../validateUser.php",
                            "alertTextOk": {!! json_encode(__('t_external.validation.username_available')) !!},
                            "alertTextLoad": {!! json_encode(__('t_external.validation.username_available')) !!}
                        },
                        "alertText": {!! json_encode(__('t_external.validation.username_taken')) !!},
                        "onlyLetter": {
                            "regex": /^[a-zA-Z ']+$/,
                            "alertText": {!! json_encode(__('t_external.validation.only_letters')) !!}
                        }
                    }
                }
            }
        })(jQuery);
        var universeDistinctions = [];

        $(document).ready(function () {
            $(".zebra tr:odd").addClass("alt");
            $.validationEngineLanguage.newLang();
        });
        // ]]>
    </script>
    <script type="text/javascript">
        var emailOnlySignup = 1;
        var emailOnlyLogin = 1;
    </script>
</head>
<body class='{{ app()->getLocale() }}'>
<div id="dieIE6">
    <div class="logo_gf"></div>
    <div class="logo_ogame"></div>
    <h1 class="ie6_header">{{ __('t_external.browser_warning.title') }}</h1>

    <p class="ie6_desc">{{ __('t_external.browser_warning.desc1') }}</p>
    <p class="ie6_desc_box">{{ __('t_external.browser_warning.desc2') }}</p>
    <p class="ie6_desc">{{ __('t_external.browser_warning.desc3') }}</p>

    <div class="browser_downloads">
        <a href="http://windows.microsoft.com/en-GB/internet-explorer/download-ie" target="_blank"
           class="browserimg ie">IE 8+</a>
        <a href="http://www.mozilla.org/de/firefox/" target="_blank" class="browserimg firefox">Firefox 16+</a>
        <a href="http://www.google.com/chrome" target="_blank" class="browserimg chrome">Chrome 23+</a>
        <a href="http://www.apple.com/de/safari/" target="_blank" class="browserimg safari">Safari 5+</a>
    </div>
</div>
<div class="products">

    <!-- #MMO:NETBAR# -->
    <div id="pagefoldtarget"></div>
</div>
<div id="start">
    <div id="header">
        <h1>
            <a href="{{ route('login') }}" title="{{ __('t_external.login.page_title') }}">
                {{ __('t_external.login.page_title') }} </a>
        </h1>
        <a id="loginBtn" href="javascript:void(0)" title="{{ __('t_external.login.btn') }}">
            {{ __('t_external.login.btn') }} </a>
        <div id="login">
            <form id="loginForm" name="loginForm" method="post" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="input-wrap">
                    <label for="usernameLogin">{{ __('t_external.login.email_label') }}</label>
                    <div class="black-border">
                        <input class="js_userName"
                               type="text"
                               onKeyDown="hideLoginErrorBox();"
                               id="usernameLogin"
                               name="email"
                               value="{{ old('email') }}"
                        />
                    </div>
                    <div id="usernameLogin_dialog" class="right">
                    </div>
                </div>
                <div class="input-wrap">
                    <label for="passwordLogin">{{ __('t_external.login.password_label') }}</label>
                    <div class="black-border">
                        <input type="password"
                               onKeyDown="hideLoginErrorBox();"
                               id="passwordLogin"
                               name="password"
                               maxlength="128"
                        />
                    </div>
                </div>
                <div class="input-wrap">
                    <label for="serverLogin">
                        {{ __('t_external.login.universe_label') }} </label>
                    <div class="black-border">
                        <select class="js_uniUrl" id="serverLogin" name="uni">
                            <option value="s1">
                                {{ __('t_external.login.universe_option_1') }}
                            </option>
                        </select>
                    </div>
                </div>
                <input type="submit" id="loginSubmit" value="{{ __('t_external.login.submit') }}"/>
                <a href="{{ route('password.request') }}" id="pwLost" title="{{ __('t_external.login.forgot_password') }}">{{ __('t_external.login.forgot_password') }}</a>
                <br/>
                @if(Route::has('password.email-lookup'))
                <a href="{{ route('password.email-lookup') }}" id="emailLost" title="{{ __('t_external.login.forgot_email') }}">{{ __('t_external.login.forgot_email') }}</a>
                @endif
                <p id="TermsAndConditionsAcceptWithLogin">
                    {!! __('t_external.login.terms_accept_html') !!}</p>
            </form>
        </div>
    </div>
    <div id="content" class="clearfix">
        <div id="subscribe">
            <form id="subscribeForm"
                  class=""
                  name="subscribeForm"
                  method="POST"
                  onsubmit="changeAction('register','subscribeForm');"
                  action="{{ route('register') }}"
                  autocomplete="off"
            >
                {{ csrf_field() }}
                <input style="display:none;" type="text" name="somefakename"/>
                <input style="display:none;" type="password" name="anotherfakename"/>

                <input type="hidden" name="v" value="3"/>
                <input type="hidden" name="step" value="validate"/>
                <input type="hidden" name="kid" value=""/>
                <input type="hidden" name="errorCodeOn" value="1"/>
                <input type="hidden" name="is_utf8" value="1"/>

                <h2>{{ __('t_external.register.play_free') }}</h2>
                <div class="input-wrap">
                    <div class="input-wrap">
                        <label for="email">{{ __('t_external.register.email_label') }}</label>
                        <div class="black-border">
                            <input class="validate[required,custom[email]]"
                                   type="text"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                            />
                        </div>
                    </div>
                </div>
                <div class="input-wrap">
                    <label for="password">{{ __('t_external.register.password_label') }}</label>
                    <div class="black-border">
                        <input class="validate[required,custom[pwLength]]"
                               type="password"
                               id="password"
                               name="password"
                               autocomplete="new-password"
                               value="{{ old('password') }}"
                               maxlength="128"
                        />
                    </div>

                    <div id="password_dialog" class="left">
                    </div>

                </div>
                <div class="input-wrap first">
                    <label for="server">{{ __('t_external.register.universe_label') }} (<a class="overlay" data-type="ajax" href="/ajax/main/distinctions">{{ __('t_external.register.distinctions') }}</a>)</label>
                </div>
                <div class="input-wrap expand">
                    <input type="hidden" id="agb" name="agb" value="on"/>
                    <label id="agbLabel">
                        <span>{!! __('t_external.register.terms_html') !!}</span>
                    </label>
                    @if ($errors->has('email'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                    @endif
                    @if ($errors->has('password'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                    @endif
                    <div>
                        <input type="submit" id="regSubmit" value="{{ __('t_external.register.submit') }}"/>
                    </div>
                    <div id="ipadapp">
                        <a href="#"
                        >
                            <img src="/img/outgame/1817433e4a8d432a8d8ed25a4d6060.png" alt=""/>
                            <img src="/img/outgame/c98a4685de676300b80da072ab6ad7.png" alt=""/>
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div id="contentWrap">
            @yield('content')
        </div>
    </div>
    <div id="push"></div>
</div>
<div id="footer">
    <div id="footerContent">
        <div class="linksAndCopyright">
            <div id="footerLinks">
                <a class="overlay" data-type="ajax" href="/ajax/main/legal">{{ __('t_external.footer.legal') }}</a> |
                <a class="overlay" data-type="ajax" href="/ajax/main/privacy-policy">{{ __('t_external.footer.privacy_policy') }}</a> |
                <a class="overlay" data-type="ajax" href="/ajax/main/terms">{{ __('t_external.footer.terms') }}</a> |
                <a class="overlay" data-type="ajax" href="/ajax/main/contact">{{ __('t_external.footer.contact') }}</a> |
                <a class="overlay" data-type="ajax" href="/ajax/main/rules">{{ __('t_external.footer.rules') }}</a>
                <br/>
                @php
                    $outgameLocale = app()->getLocale();
                    $outgameLocales = \OGame\Http\Middleware\Locale::SUPPORTED_LOCALES;
                @endphp
                <div class="lang-dropdown-wrapper" style="display:inline-block;position:relative;cursor:pointer;vertical-align:middle;">
                    <span class="lang-flag lang-flag-{{ $outgameLocale }}" style="vertical-align:middle;"></span>
                    <span style="color:#6f9fc8;font-size:11px;vertical-align:middle;">{{ strtoupper($outgameLocale) }} ▾</span>
                    <div class="lang-dropdown" style="display:none;position:absolute;bottom:100%;left:50%;transform:translateX(-50%);margin-bottom:0;padding:4px 0 8px;background:#0d1014;border:1px solid #3a4959;border-radius:3px;box-shadow:0 4px 12px rgba(0,0,0,0.6);z-index:9999;min-width:90px;max-height:320px;overflow-y:auto;">
                        @foreach($outgameLocales as $lng)
                            <a href="{{ route('language.switch', ['lang' => $lng]) }}" style="display:flex;align-items:center;padding:4px 10px;color:{{ $outgameLocale === $lng ? '#6f9fc8' : '#848484' }};text-decoration:none;font-size:11px;"><span class="lang-flag lang-flag-{{ $lng }}"></span><span>{{ strtoupper($lng) }}</span></a>
                        @endforeach
                    </div>
                </div>
           <style>
    .lang-dropdown-wrapper:hover .lang-dropdown { display: block !important; }
    .lang-dropdown a:hover { background: #1a2230; color: #fff !important; }
    
    .lang-flag { 
        display: inline-block;
        flex-shrink: 0;
        box-sizing: border-box; /* NUOVO: Impedisce al browser di aggiungere pixel invisibili */
        width: 16px;
        min-width: 16px;
        height: 11px;
        margin-right: 6px;
        border-radius: 1px;
        vertical-align: middle;
        background-size: 100% 100%; /* SOSTITUITO "cover": Forza l'immagine esattamente a 16x11 pixel */
        background-repeat: no-repeat;
        background-position: center;
        box-shadow: 0 0 1px rgba(0,0,0,0.4); 
    }
    
    /* EN rimane immagine */
    .lang-flag-en { 
        background-image: url('/img/flags/en.svg'); 
    }
    
    /* IT, NL e DE restano gradienti matematicamente perfetti */
    .lang-flag-it { 
        background-image: linear-gradient(to right, #009246 33.33%, #ffffff 33.33%, #ffffff 66.66%, #CE2B37 66.66%); 
    }
    .lang-flag-nl { 
        background-image: linear-gradient(to bottom, #AE1C28 33.33%, #ffffff 33.33%, #ffffff 66.66%, #21468B 66.66%); 
    }
    .lang-flag-de {
        background-image: linear-gradient(to bottom, #000000 33.33%, #DD0000 33.33%, #DD0000 66.66%, #FFCE00 66.66%);
    }

    /* 23 OGame community flags from SVG sources */
    .lang-flag-ar { background-image: url('/img/flags/ar.svg'); }
    .lang-flag-br { background-image: url('/img/flags/br.svg'); }
    .lang-flag-cz { background-image: url('/img/flags/cz.svg'); }
    .lang-flag-dk { background-image: url('/img/flags/dk.svg'); }
    .lang-flag-es { background-image: url('/img/flags/es.svg'); }
    .lang-flag-fi { background-image: url('/img/flags/fi.svg'); }
    .lang-flag-fr { background-image: url('/img/flags/fr.svg'); }
    .lang-flag-gr { background-image: url('/img/flags/gr.svg'); }
    .lang-flag-hr { background-image: url('/img/flags/hr.svg'); }
    .lang-flag-hu { background-image: url('/img/flags/hu.svg'); }
    .lang-flag-jp { background-image: url('/img/flags/jp.svg'); }
    .lang-flag-mx { background-image: url('/img/flags/mx.svg'); }
    .lang-flag-pl { background-image: url('/img/flags/pl.svg'); }
    .lang-flag-pt { background-image: url('/img/flags/pt.svg'); }
    .lang-flag-ro { background-image: url('/img/flags/ro.svg'); }
    .lang-flag-ru { background-image: url('/img/flags/ru.svg'); }
    .lang-flag-se { background-image: url('/img/flags/se.svg'); }
    .lang-flag-si { background-image: url('/img/flags/si.svg'); }
    .lang-flag-sk { background-image: url('/img/flags/sk.svg'); }
    .lang-flag-tr { background-image: url('/img/flags/tr.svg'); }
    .lang-flag-tw { background-image: url('/img/flags/tw.svg'); }
    .lang-flag-us { background-image: url('/img/flags/us.svg'); }
    .lang-flag-yu { background-image: url('/img/flags/yu.svg'); }
</style>
                <br/>
                <div class="align_center">
                    <a href="#" target="_blank">
                        <div class="socialMediaLogo gPlusLogo">Google+</div>
                    </a>
                    <a href="#" target="_blank">
                        <div class="socialMediaLogo fbLogo">Facebook</div>
                    </a>
                </div>
                <p id="copyright">{{ __('t_external.footer.copyright') }} {{ \OGame\Facades\GitInfoUtil::getAppVersion() }} </p>
            </div>
        </div>
    </div>
</div>
<!-- OVERLAY DIVISION -->
<script type="text/javascript">
    checkIpadApp();
    JSLoca = new Array({!! json_encode(__('t_external.js.login')) !!}, {!! json_encode(__('t_external.js.close')) !!});

    var global_language = "{{ app()->getLocale() }}";
    var text_age_check_failed = {!! json_encode(__('t_external.js.age_check_failed')) !!};
</script>
<script type="text/javascript">
    $(document).ready(function () {
        ogame.characteristics.init({
            "speed_fleet": {
                "css": "speed_fleet",
                "text": {!! json_encode(__('t_external.universe_characteristics.fleet_speed')) !!},
                "valueCategory": "speed",
                "valueKey": "fleet",
                "valueAppendix": "x",
                "type": "range"
            },
            "speed_economy": {
                "css": "speed_economy",
                "text": {!! json_encode(__('t_external.universe_characteristics.economy_speed')) !!},
                "valueCategory": "speed",
                "valueKey": "server",
                "valueAppendix": "x",
                "type": "range"
            },
            "debris_field_factor_ships": {
                "css": "ships_in_debris_field",
                "text": {!! json_encode(__('t_external.universe_characteristics.debris_ships')) !!},
                "valueCategory": "combat",
                "valueKey": "debris_field_factor_ships",
                "valueAppendix": "%",
                "type": "range",
                "step": 10
            },
            "defence_in_debris_field": {
                "css": "defence_in_debris_field",
                "text": {!! json_encode(__('t_external.universe_characteristics.debris_defence')) !!},
                "valueCategory": "combat",
                "valueKey": "debris_field_factor_def",
                "valueAppendix": "%",
                "type": "range",
                "step": 10
            },
            "dark_matter_signup_gift": {
                "css": "dm",
                "text": {!! json_encode(__('t_external.universe_characteristics.dark_matter_gift')) !!},
                "valueCategory": "general",
                "valueKey": "dark_matter_signup_gift",
                "type": "range",
                "step": 1000
            },
            "aks_on": {
                "css": "aks_on",
                "text": {!! json_encode(__('t_external.universe_characteristics.aks_on')) !!},
                "valueCategory": "alliance",
                "valueKey": "aks",
                "condition": "tooltip.alliance.aks",
                "type": "binary"
            },
            "planet_fields": {
                "css": "planet_fields",
                "text": {!! json_encode(__('t_external.universe_characteristics.planet_fields')) !!},
                "valueCategory": "size",
                "valueKey": "planet_field_bonus",
                "type": "range",
                "step": 10
            },
            "wreckfield": {
                "css": "wreck_field",
                "text": {!! json_encode(__('t_external.universe_characteristics.wreckfield')) !!},
                "valueCategory": "wreckfield",
                "valueKey": "enabled",
                "type": "binary"
            },
            "universe_big": {
                "css": "universe_big",
                "text": {!! json_encode(__('t_external.universe_characteristics.universe_big')) !!},
                "valueCategory": "size",
                "valueKey": "galaxies_max",
                "condition": "tooltip.size.galaxies_max > 9",
                "type": "range"
            }
        });
    });
</script>
</body>
</html>
