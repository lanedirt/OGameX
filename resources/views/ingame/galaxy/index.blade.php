@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <script type="text/javascript">


        var galaxy = "{{ $current_galaxy }}";
        var system = "{{ $current_system }}";
        var maxGalaxies = 4;
        var maxSystems = 499;
        var spionageAmount = 1;
        var officersLink = "{{ route('premium.index', ['openDetail' => 2]) }}";
        var contentLink = "{{ route('galaxy.ajax') }}";
        var preserveSystemOnPlanetChange = false;
        var galaxyLoca = {
            "reservationSuccess": "The position has been reserved for you. Your colony`s relocation has begun.",
            "questionTitle": "Resettle Planet",
            "question": "Are you sure you want to relocate your planet to these coordinates? To finance the relocation you`ll need 168.000 Dark Matter.",
            "deuteriumNeeded": "You don`t have enough Deuterium! You need 10 Units of Deuterium.",
            "notAfterValidation": "<br>This restriction no longer applies once you validate.",
            "fleetAttacking": "Fleet is attacking!",
            "fleetUnderway": "Fleet is en-route"
        };
        var shipsendingDone = 1;
        $(document.documentElement).keyup(keyevent);

        function initGalaxy() {
            tabletInitGalaxy();
            //loadContent(galaxy, system, false);
            focusOnTabChange("#showbutton a", true);
        }

        function trySubmit() {
            submitForm();
        }

        fadingDivs = new Array();

        function doExpedition() {
            location.href = "{{ route('fleet.index', ['mission' => 15, 'position' => 16]) }}&galaxy=" + galaxy + "&system=" + system + "&type=" + 1;
        }

        var player = {hasCommander: true};
        var localizedBBCode = {
            "bold": "Bold",
            "italic": "Italic",
            "underline": "Underline",
            "stroke": "Strikethrough",
            "sub": "Subscript",
            "sup": "Superscript",
            "fontColor": "Font colour",
            "fontSize": "Font size",
            "backgroundColor": "Background colour",
            "backgroundImage": "Background image",
            "tooltip": "Tool-tip",
            "alignLeft": "Left align",
            "alignCenter": "Centre align",
            "alignRight": "Right align",
            "alignJustify": "Justify",
            "block": "Break",
            "code": "Code",
            "spoiler": "Spoiler",
            "moreopts": "More Options",
            "list": "List",
            "hr": "Horizontal line",
            "picture": "Image",
            "link": "Link",
            "email": "Email",
            "player": "Player",
            "item": "Item",
            "coordinates": "Coordinates",
            "preview": "Preview",
            "textPlaceHolder": "Text...",
            "playerPlaceHolder": "Player ID or name",
            "itemPlaceHolder": "Item ID",
            "coordinatePlaceHolder": "Galaxy:system:position",
            "charsLeft": "Characters remaining",
            "colorPicker": {"ok": "Ok", "cancel": "Cancel", "rgbR": "R", "rgbG": "G", "rgbB": "B"},
            "backgroundImagePicker": {"ok": "Ok", "repeatX": "Repeat horizontally", "repeatY": "Repeat vertically"}
        }, itemNames = {
            "090a969b05d1b5dc458a6b1080da7ba08b84ec7f": "Bronze Crystal Booster",
            "e254352ac599de4dd1f20f0719df0a070c623ca8": "Bronze Deuterium Booster",
            "b956c46faa8e4e5d8775701c69dbfbf53309b279": "Bronze Metal Booster",
            "3c9f85221807b8d593fa5276cdf7af9913c4a35d": "Bronze Crystal Booster",
            "422db99aac4ec594d483d8ef7faadc5d40d6f7d3": "Silver Crystal Booster",
            "118d34e685b5d1472267696d1010a393a59aed03": "Gold Crystal Booster",
            "d3d541ecc23e4daa0c698e44c32f04afd2037d84": "DETROID Bronze",
            "0968999df2fe956aa4a07aea74921f860af7d97f": "DETROID Gold",
            "27cbcd52f16693023cb966e5026d8a1efbbfc0f9": "DETROID Silver",
            "d9fa5f359e80ff4f4c97545d07c66dbadab1d1be": "Bronze Deuterium Booster",
            "e4b78acddfa6fd0234bcb814b676271898b0dbb3": "Silver Deuterium Booster",
            "5560a1580a0330e8aadf05cb5bfe6bc3200406e2": "Gold Deuterium Booster",
            "40f6c78e11be01ad3389b7dccd6ab8efa9347f3c": "KRAKEN Bronze",
            "929d5e15709cc51a4500de4499e19763c879f7f7": "KRAKEN Gold",
            "4a58d4978bbe24e3efb3b0248e21b3b4b1bfbd8a": "KRAKEN Silver",
            "de922af379061263a56d7204d1c395cefcfb7d75": "Bronze Metal Booster",
            "ba85cc2b8a5d986bbfba6954e2164ef71af95d4a": "Silver Metal Booster",
            "05294270032e5dc968672425ab5611998c409166": "Gold Metal Booster",
            "be67e009a5894f19bbf3b0c9d9b072d49040a2cc": "Bronze Moon Fields",
            "05ee9654bd11a261f1ff0e5d0e49121b5e7e4401": "Gold Moon Fields",
            "c21ff33ba8f0a7eadb6b7d1135763366f0c4b8bf": "Silver Moon Fields",
            "485a6d5624d9de836d3eb52b181b13423f795770": "Bronze M.O.O.N.S.",
            "45d6660308689c65d97f3c27327b0b31f880ae75": "Gold M.O.O.N.S.",
            "fd895a5c9fd978b9c5c7b65158099773ba0eccef": "Silver M.O.O.N.S.",
            "da4a2a1bb9afd410be07bc9736d87f1c8059e66d": "NEWTRON Bronze",
            "8a4f9e8309e1078f7f5ced47d558d30ae15b4a1b": "NEWTRON Gold",
            "d26f4dab76fdc5296e3ebec11a1e1d2558c713ea": "NEWTRON Silver",
            "16768164989dffd819a373613b5e1a52e226a5b0": "Bronze Planet Fields",
            "04e58444d6d0beb57b3e998edc34c60f8318825a": "Gold Planet Fields",
            "0e41524dc46225dca21c9119f2fb735fd7ea5cb3": "Silver Planet Fields"
        };
        $(document).ready(function () {
            initGalaxy();
        });</script>
    <!-- END JAVASCRIPT -->

    <div id="inhalt">
        <div id="galaxyHeader">
            <form action="{{ route('galaxy.index') }}" name="galaform" method="post">
                <span class="galaxy_icons galaxy tooltip" title="Galaxy"></span>
                <span class="galaxy_icons prev" onclick="submitOnKey(40);"></span>
                <input id="galaxy_input" class="hideNumberSpin" maxlength="3" type="text" pattern="[0-9]*" value="{{ $current_galaxy }}" name="galaxy" tabindex="2" onfocus="clearInput(this);" onkeyup="checkIntInput(this, 1, 6)" onkeypress="return submitOnEnter(event);">
                <span class="galaxy_icons next" onclick="submitOnKey(38);"></span>
                <span class="galaxy_icons solarsystem tooltip" title="System"></span>
                <span class="galaxy_icons prev" onclick="submitOnKey(37);"></span>
                <input id="system_input" class="hideNumberSpin" maxlength="3" type="text" pattern="[0-9]*" value="{{ $current_system }}" tabindex="2" name="system" onfocus="clearInput(this);" onkeyup="checkIntInput(this, 1, 499)" onkeypress="return submitOnEnter(event);">
                <span class="galaxy_icons next" onclick="submitOnKey(39);"></span>
                <div class="btn_blue" onclick="submitForm();">
                    Go!                </div>
                <div id="expeditionbutton" class="btn_blue float_right" onclick="doExpedition();">
                    Expedition                </div>
            </form>
        </div>
        <div id="eventboxContent" style="display: none">
            <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
        </div>
        <div id="galaxyLoading" style="display: none;">
            <img src="/img/icons/6e0f46d7504242302bc8055ad9c8c2.gif" alt="">
        </div>
        <div id="galaxyContent"><!--[if lte IE 11]>
            <style type="text/css">
                .icon.icon_eye.hueRotate {
                    background: url(/img/icons/iconsprite16px.png);
                    background-position: -993px;
                }
            </style>
            <![endif]-->
            <div id="mobileDiv">
                {!! $galaxy_table_html  !!}



                <div id="legendTT" style="display: none;" class="htmlTooltip">
                    <h1>Legend</h1>
                    <div class="splitLine"></div>
                    <dl>
                        <dt class="abbreviation status_abbr_admin">A</dt>
                        <dd class="description">Administrator</dd>

                        <dt class="abbreviation status_abbr_strong">s</dt>
                        <dd class="description">Stronger Player</dd>

                        <dt class="abbreviation status_abbr_noob">n</dt>
                        <dd class="description">Weaker Player (newbie)</dd>

                        <dt class="abbreviation status_abbr_outlaw">o</dt>
                        <dd class="description">Outlaw (temporary)</dd>

                        <dt class="abbreviation status_abbr_vacation">v</dt>
                        <dd class="description">Vacation Mode</dd>

                        <dt class="abbreviation status_abbr_banned">b</dt>
                        <dd class="description">Banned</dd>

                        <dt class="abbreviation status_abbr_inactive">i</dt>
                        <dd class="description">7 days inactive</dd>

                        <dt class="abbreviation status_abbr_longinactive">I</dt>
                        <dd class="description">28 days inactive</dd>

                        <dt class="abbreviation status_abbr_honorableTarget">hp</dt>
                        <dd class="description">Honourable target</dd>
                    </dl>
                </div>
            </div>
            <script type="text/javascript">
                var buildListCountdowns = new Array();
                $(document).ready(function() {
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-0"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-1"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-2"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-3"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-4"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-5"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-6"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-7"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-8"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-9"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-10"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-11"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-12"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-13"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-14"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );
                    buildListCountdowns.push(
                            new baulisteCountdown(
                                    document.getElementById("cooldown-15"),
                                    0,
                                    '{{ route('galaxy.index') }}&amp;galaxy=' + galaxy + '&amp;system=' + system
                            )
                    );

                    $(document.documentElement).off( "keyup" );
                    $(document.documentElement).on( "keyup", keyevent );
                });
            </script>
        </div>
    </div>

@endsection
