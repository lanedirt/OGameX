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
    <meta name="language" content="en"/>
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
                            "alertText": "This field is required",
                            "alertTextCheckboxMultiple": "Make a decision",
                            "alertTextCheckboxe": "You must accept the T&Cs."
                        },
                        "length": {
                            "regex": /^.{3,20}$/,
                            "alertText": "Between 3 and 20 characters allowed."
                        },
                        "pwLength": {
                            "regex": /^.{4,20}$/,
                            "alertText": "Between 4 and 20 characters allowed."
                        },
                        "email": {
                            "regex": /^[a-zA-Z0-9_.\-]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/,
                            "alertText": "You need to enter a valid email address!"
                        },
                        "noSpecialCharacters": {
                            "regex": /^[a-zA-Z0-9\s_\-]+$/,
                            "alertText": "Contains invalid characters."
                        },
                        "noBeginOrEndUnderscore": {
                            "regex": /^([^_]+(.*[^_])?)?$/,
                            "alertText": "Your name may not start or end with an underscore."
                        },
                        "noBeginOrEndHyphen": {
                            "regex": /^([^\-]+(.*[^\-])?)?$/,
                            "alertText": ""
                        },
                        "noBeginOrEndWhitespace": {
                            "regex": /^([^\s]+(.*[^\s])?)?$/,
                            "alertText": "Your name may not start or end with a space."
                        },
                        "notMoreThanThreeUnderscores": {
                            "regex": /^[^_]*(_[^_]*){0,3}$/,
                            "alertText": "Your name may not contain more than 3 underscores in total."
                        },
                        "notMoreThanThreeHyphen": {
                            "regex": /^[^\-]*(\-[^\-]*){0,3}$/,
                            "alertText": ""
                        },
                        "notMoreThanThreeWhitespaces": {
                            "regex": /^[^\s]*(\s[^\s]*){0,3}$/,
                            "alertText": "Your name may not include more than 3 spaces in total."
                        },
                        "noCollocateUnderscores": {
                            "regex": /^[^_]*(_[^_]+)*_?$/,
                            "alertText": "You may not use two or more underscores one after the other."
                        },
                        "noCollocateHyphen": {
                            "regex": /^[^\-]*(\-[^\-]+)*-?$/,
                            "alertText": ""
                        },
                        "noCollocateWhitespaces": {
                            "regex": /^[^\s]*(\s[^\s]+)*\s?$/,
                            "alertText": "You may not use two or more spaces one after the other."
                        },
                        "ajaxUser": {
                            "file": "../validateUser.php",
                            "alertTextOk": "This username is available.",
                            "alertTextLoad": "Please wait, loading...",
                            "alertText": "This username is not available anymore."
                        },
                        "ajaxName": {
                            "file": "../validateUser.php",
                            "alertTextOk": "This username is available.",
                            "alertTextLoad": "This username is available."
                        },
                        "alertText": "This username is not available anymore.",
                        "onlyLetter": {
                            "regex": /^[a-zA-Z ']+$/,
                            "alertText": "Use characters only."
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
<body class='en'>
<div id="dieIE6">
    <div class="logo_gf"></div>
    <div class="logo_ogame"></div>
    <h1 class="ie6_header">Your browser is not up to date.</h1>

    <p class="ie6_desc">Your Internet Explorer version does not correspond to the existing standards and is not
        supported by this website anymore.</p>
    <p class="ie6_desc_box">To use this website please update your web browser to a current version or use another web
        browser. If you are already using the latest version, please reload the page to display it properly.</p>
    <p class="ie6_desc">Here`s a list of the most popular browsers. Click on one of the symbols to get to the download
        page:</p>

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
            <a href="{{ route('login') }}" title="OGameX - Conquer the universe">
                OGame - Conquer the universe </a>
        </h1>
        <a id="loginBtn" href="javascript:void(0)" title="Login">
            Login </a>
        <div id="login">
            <form id="loginForm" name="loginForm" method="post" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="input-wrap">
                    <label for="usernameLogin">Email address:</label>
                    <div class="black-border">
                        <input class="js_userName"
                               type="text"
                               onKeyDown="hideLoginErrorBox();"
                               id="usernameLogin"
                               name="email"
                               value="{{ old('email') }}"
                        />
                        <div id="transition_email_only_login_dialog" title="Notice">
                            We have reworked the login system. In future you will no longer be able to log in using your
                            username. For this reason, please now start using your email address to log in.
                            Don’t know your email address? You’ll find the new link for ‘Forgot email address’ under the
                            login window.
                        </div>
                    </div>
                    <div id="usernameLogin_dialog" class="right">
                    </div>
                </div>
                <div class="input-wrap">
                    <label for="passwordLogin">Password:</label>
                    <div class="black-border">
                        <input type="password"
                               onKeyDown="hideLoginErrorBox();"
                               id="passwordLogin"
                               name="password"
                               maxlength="20"
                        />
                    </div>
                </div>
                <div class="input-wrap">
                    <label for="serverLogin">
                        Universe: </label>
                    <div class="black-border">
                        <select class="js_uniUrl" id="serverLogin" name="uni">
                            <option value="s1">
                                1. Universe
                            </option>
                        </select>
                    </div>
                </div>
                <input type="submit" id="loginSubmit" value="Log in"/>
                <a href="#" id="pwLost" target="_blank" title="Forgot your password?">Forgot your password?</a>
                <br/>
                <a href="#" id="emailLost" target="_blank" title="Forgot your email address?">Forgot your email
                    address?</a>
                <p id="TermsAndConditionsAcceptWithLogin">
                    With the login I accept the <a class="" href="#" target="_blank" title="T&Cs">T&Cs</a></p>
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

                <h2>PLAY FOR FREE!</h2>
                <div class="input-wrap">
                    <div class="input-wrap">
                        <label for="email">Email address:</label>
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
                    <label for="password">Password:</label>
                    <div class="black-border">
                        <input class="validate[required,custom[pwLength]]"
                               type="password"
                               id="password"
                               name="password"
                               autocomplete="new-password"
                               value="{{ old('password') }}"
                               maxlength="20"
                        />
                    </div>

                    <div id="password_dialog" class="left">
                    </div>

                </div>
                <div class="input-wrap first">
                    <label for="server">Universe: (<a class="overlay" data-type="ajax" href="/ajax/main/distinctions">Distinctions</a>)</label>
                    <!--<div id="server" style="position:relative;">
                        <table cellspacing="0"
                               cellpadding="0"
                               onclick="switch_uni_selection()"
                               onmouseover="this.style.cursor='pointer'"
                               class="server_table"
                        >
                            <tr>
                                <td id="uni_select_box" class="select" style="height:19px;overflow:hidden;">
                                    <span id="uni_name" class="margin-uni-selection">PLEASE_SELECT_UNI</span>
                                </td>
                                <td style="width:18px; background: url(img/outgame/69677f0e9f1a6f9da264837a284c2d.png) no-repeat scroll 0 0 #8D9AA7;"></td>
                            </tr>
                        </table>
                        <input class="js_uniUrl" type="hidden" name="uni_url" id="uni_domain"/>
                        <div id="uni_selection">
                            <script type="text/javascript">
                                select_uni('s128', 'Betelgeuse','exodus-server-old');
                            </script>

                            <div id="row-0"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s128', 'Betelgeuse ','exodus-server-old');"
                                 onmouseover="highlightRow('row-0');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-0');"
                                 data-tooltip='{"general":{"name":"128","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"20000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"4","fleet":"2"},"size":{"galaxies_max":"7","planets_max":"15","planet_field_bonus":"25","systems_max":"499"},"combat":{"debris_field_factor_ships":"70","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Betelgeuse                                            </div>

                            <div id="row-1"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s129', 'Cygnus ','exodus-server-old');"
                                 onmouseover="highlightRow('row-1');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-1');"
                                 data-tooltip='{"general":{"name":"129","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"5","fleet":"4"},"size":{"galaxies_max":"9","planets_max":"15","planet_field_bonus":"25","systems_max":"499"},"combat":{"debris_field_factor_ships":"70","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Cygnus                                            </div>

                            <div id="row-2"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s130', 'Deimos ','exodus-server-old');"
                                 onmouseover="highlightRow('row-2');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-2');"
                                 data-tooltip='{"general":{"name":"130","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"2","fleet":"1"},"size":{"galaxies_max":"9","planets_max":"15","planet_field_bonus":"0","systems_max":"499"},"combat":{"debris_field_factor_ships":"30","debris_field_factor_def":"30","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Deimos                                            </div>

                            <div id="row-3"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s131', 'Eridanus ','exodus-server-old');"
                                 onmouseover="highlightRow('row-3');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-3');"
                                 data-tooltip='{"general":{"name":"131","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"3","fleet":"3"},"size":{"galaxies_max":"9","planets_max":"15","planet_field_bonus":"0","systems_max":"499"},"combat":{"debris_field_factor_ships":"50","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Eridanus                                            </div>

                            <div id="row-4"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s132', 'Fidis ','exodus-server-old');"
                                 onmouseover="highlightRow('row-4');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-4');"
                                 data-tooltip='{"general":{"name":"132","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"2","fleet":"1"},"size":{"galaxies_max":"9","planets_max":"15","planet_field_bonus":"0","systems_max":"499"},"combat":{"debris_field_factor_ships":"70","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Fidis                                            </div>

                            <div id="row-5"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s133', 'Ganimed ','exodus-server-old');"
                                 onmouseover="highlightRow('row-5');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-5');"
                                 data-tooltip='{"general":{"name":"133","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"4","fleet":"4"},"size":{"galaxies_max":"9","planets_max":"15","planet_field_bonus":"0","systems_max":"499"},"combat":{"debris_field_factor_ships":"70","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"0"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Ganimed                                            </div>

                            <div id="row-6"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s134', 'Hyperion ','exodus-server-old');"
                                 onmouseover="highlightRow('row-6');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-6');"
                                 data-tooltip='{"general":{"name":"134","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"2","fleet":"2"},"size":{"galaxies_max":"7","planets_max":"15","planet_field_bonus":"25","systems_max":"499"},"combat":{"debris_field_factor_ships":"60","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Hyperion                                            </div>

                            <div id="row-7"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s135', 'Izar ','exodus-server-old');"
                                 onmouseover="highlightRow('row-7');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-7');"
                                 data-tooltip='{"general":{"name":"135","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"5","fleet":"5"},"size":{"galaxies_max":"9","planets_max":"15","planet_field_bonus":"25","systems_max":"499"},"combat":{"debris_field_factor_ships":"30","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Izar                                            </div>

                            <div id="row-8"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s136', 'Japetus ','exodus-server-old');"
                                 onmouseover="highlightRow('row-8');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-8');"
                                 data-tooltip='{"general":{"name":"136","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"3","fleet":"3"},"size":{"galaxies_max":"6","planets_max":"15","planet_field_bonus":"25","systems_max":"499"},"combat":{"debris_field_factor_ships":"30","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Japetus                                            </div>

                            <div id="row-9"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s137', 'Kallisto ','exodus-server-old');"
                                 onmouseover="highlightRow('row-9');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-9');"
                                 data-tooltip='{"general":{"name":"137","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"22500"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"4","fleet":"1"},"size":{"galaxies_max":"5","planets_max":"15","planet_field_bonus":"25","systems_max":"499"},"combat":{"debris_field_factor_ships":"30","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Kallisto                                            </div>

                            <div id="row-10"
                                 class="server-row exodus-server-old uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s138', 'Libra ','exodus-server-old');"
                                 onmouseover="highlightRow('row-10');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-10');"
                                 data-tooltip='{"general":{"name":"138","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"4","fleet":"4"},"size":{"galaxies_max":"7","planets_max":"15","planet_field_bonus":"0","systems_max":"499"},"combat":{"debris_field_factor_ships":"70","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Old Universe: This universe is highly advanced and is therefore only recommended to experienced players."}'
                            >
                                Libra                                            </div>

                            <div id="row-11"
                                 class="server-row exodus-server-normal uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s140', 'Nusakan ','exodus-server-normal');"
                                 onmouseover="highlightRow('row-11');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-11');"
                                 data-tooltip='{"general":{"name":"140","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"3","fleet":"2"},"size":{"galaxies_max":"6","planets_max":"15","planet_field_bonus":"25","systems_max":"499"},"combat":{"debris_field_factor_ships":"70","debris_field_factor_def":"70","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Normal universe"}'
                            >
                                Nusakan                                            </div>

                            <div id="row-12"
                                 class="server-row exodus-server-normal uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s141', 'Oberon ','exodus-server-normal');"
                                 onmouseover="highlightRow('row-12');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-12');"
                                 data-tooltip='{"general":{"name":"141","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"5","fleet":"5"},"size":{"galaxies_max":"6","planets_max":"15","planet_field_bonus":"0","systems_max":"499"},"combat":{"debris_field_factor_ships":"70","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Normal universe"}'
                            >
                                Oberon                                            </div>

                            <div id="row-13"
                                 class="server-row exodus-server-normal uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s142', 'Polaris ','exodus-server-normal');"
                                 onmouseover="highlightRow('row-13');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-13');"
                                 data-tooltip='{"general":{"name":"142","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"5","fleet":"2"},"size":{"galaxies_max":"6","planets_max":"15","planet_field_bonus":"25","systems_max":"499"},"combat":{"debris_field_factor_ships":"50","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Normal universe"}'
                            >
                                Polaris                                            </div>

                            <div id="row-14"
                                 class="server-row exodus-server-normal uni_span margin-uni-selection"
                                 title=""
                                 onclick="select_uni('s143', 'Quaoar ','exodus-server-normal');"
                                 onmouseover="highlightRow('row-14');this.style.cursor='pointer'"
                                 onmouseout="unHighlightRow('row-14');"
                                 data-tooltip='{"general":{"name":"143","language":"en","noob_protection_factor":"2","noob_protection_advanced":"0","expedition":"1","dark_matter_signup_gift":"8000"},"jumpgate":{"enabled":"1","basic_duration":"3600","minimum_duration":"3600"},"speed":{"server":"5","fleet":"2"},"size":{"galaxies_max":"6","planets_max":"15","planet_field_bonus":"25","systems_max":"499"},"combat":{"debris_field_factor_ships":"50","debris_field_factor_def":"0","espionage_raids":"0"},"wreckfield":{"enabled":"1"},"alliance":{"aks":"1"},"exodusInfo":"Normal universe"}'
                            >
                                Quaoar                                            </div>
                        </div>
                    </div>
                    <div id="universeDistinction" class="formError" style="top: -24px;">
                        <div class="formErrorContent">
                            <div class="icon"></div>
                            <div class="formErrorArrow"></div>
                        </div>
                    </div>-->
                </div>
                <div class="input-wrap expand">
                    <input type="hidden" id="agb" name="agb" value="on"/>
                    <label id="agbLabel">
                        <span>Our <a class="" target="_blank" href="#" title="T&Cs"> T&Cs </a> and <a class=""
                                                                                                      target="_blank"
                                                                                                      href="#"
                                                                                                      title="Privacy Policy"> Privacy Policy </a> apply in the game</span>
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
                        <input type="submit" id="regSubmit" value="Register"/>
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
                <a target="_blank" href="#">Legal</a> |
                <a target="_blank" href="#">Privacy Policy</a> |
                <a target="_blank" href="#">T&Cs</a> |
                <a target="_blank" href="#">Contact</a> |
                <a class="overlay" data-type="ajax" href="/ajax/main/rules">Rules</a>
                <br/>
                <div class="align_center">
                    <a href="https://plus.google.com/118150651196691403580" target="_blank">
                        <div class="socialMediaLogo gPlusLogo">Google+</div>
                    </a>
                    <a href="https://www.facebook.com/ogame" target="_blank">
                        <div class="socialMediaLogo fbLogo">Facebook</div>
                    </a>
                </div>
                <p id="copyright">© OGameX. All rights reserved. {{ \OGame\Facades\GitInfoUtil::getAppVersion() }} </p>
            </div>
            <div class="logos">
                <a id="safeplay" href="#" target="_blank"></a>
            </div>
        </div>
    </div>
</div>
<!-- OVERLAY DIVISION -->
<script type="text/javascript">
    checkIpadApp();
    JSLoca = new Array('Login', 'Close');

    var global_language = "en";
    var text_age_check_failed = "We are sorry, but you are not eligible to register. Please see our T&C for more information.";
</script>
<script type="text/javascript">
    $(document).ready(function () {
        ogame.characteristics.init({
            "speed_fleet": {
                "css": "speed_fleet",
                "text": "Fleet Speed: the higher the value, the less time you have left to react to an attack.",
                "valueCategory": "speed",
                "valueKey": "fleet",
                "valueAppendix": "x",
                "type": "range"
            },
            "speed_economy": {
                "css": "speed_economy",
                "text": "Economy Speed: the higher the value, the faster constructions and research will be completed and resources gathered.",
                "valueCategory": "speed",
                "valueKey": "server",
                "valueAppendix": "x",
                "type": "range"
            },
            "debris_field_factor_ships": {
                "css": "ships_in_debris_field",
                "text": "Some of the ships destroyed in battle will enter the debris field.",
                "valueCategory": "combat",
                "valueKey": "debris_field_factor_ships",
                "valueAppendix": "%",
                "type": "range",
                "step": 10
            },
            "defence_in_debris_field": {
                "css": "defence_in_debris_field",
                "text": "Some of the defensive structures destroyed in battle will enter the debris field.",
                "valueCategory": "combat",
                "valueKey": "debris_field_factor_def",
                "valueAppendix": "%",
                "type": "range",
                "step": 10
            },
            "dark_matter_signup_gift": {
                "css": "dm",
                "text": "You will receive Dark Matter as a reward for confirming your email address.",
                "valueCategory": "general",
                "valueKey": "dark_matter_signup_gift",
                "type": "range",
                "step": 1000
            },
            "aks_on": {
                "css": "aks_on",
                "text": "Alliance battle system activated",
                "valueCategory": "alliance",
                "valueKey": "aks",
                "condition": "tooltip.alliance.aks",
                "type": "binary"
            },
            "planet_fields": {
                "css": "planet_fields",
                "text": "The maximum amount of building slots has been increased.",
                "valueCategory": "size",
                "valueKey": "planet_field_bonus",
                "type": "range",
                "step": 10
            },
            "wreckfield": {
                "css": "wreck_field",
                "text": "Space Dock activated: some destroyed ships can be restored using the Space Dock.",
                "valueCategory": "wreckfield",
                "valueKey": "enabled",
                "type": "binary"
            },
            "universe_big": {
                "css": "universe_big",
                "text": "Amount of Galaxies in the Universe",
                "valueCategory": "size",
                "valueKey": "galaxies_max",
                "condition": "tooltip.size.galaxies_max > 9",
                "type": "range"
            }
        });

        $("#transition_email_only_login_dialog").dialog({
            create: function () {
                $(this).dialog('widget')
                    .find('.ui-dialog-titlebar')
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-top');
            },
            autoOpen: false,
            modal: false,
            width: 200,
            resizable: false,
            draggable: false,
            show: {
                effect: 'fade',
                duration: 'fast'
            },
            hide: {
                effect: 'fade'
            },
            position: {
                my: 'right-20px top-50px',
                at: 'left center',
                of: $('#usernameLogin')
            }
        });

    });
</script>
</body>
</html>