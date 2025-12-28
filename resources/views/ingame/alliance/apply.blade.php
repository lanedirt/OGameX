@extends('ingame.layouts.main')

@section('content')

    @include('ingame.shared.buddy.bbcode-parser')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>{{ __('Apply to Alliance') }}</h2>
                        <a class="toggleHeader" href="javascript:void(0);" data-name="alliance">
                            <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                        </a>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            <div id="sendApplication" class="contentbox2">
                                <h3 class="header">{{ __('Application to') }} [{{ $alliance->alliance_tag }}] {{ $alliance->alliance_name }}</h3>

                                <div class="content">
                                    <form id="applicationForm" method="POST" action="{{ route('alliance.apply') }}">
                                        @csrf
                                        <input type="hidden" name="alliance_id" value="{{ $alliance->id }}">

                                        <table id="writeapplication" cellspacing="0" cellpadding="0" style="width:560px">
                                            <tbody>
                                                <tr>
                                                    <td colspan="2">
                                                        <textarea id="allitext" name="message" class="alliancetexts markItUpEditor" maxlength="2000" cols="80" rows="10"></textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="transparent textBeefy" style="width: 120px">
                                                        <span id="c_characters">2000</span> {{ __('Characters remaining') }}
                                                    </td>
                                                    <td class="transparent textRight">
                                                        <button type="submit" class="btn_blue float_right" id="submitApplication">
                                                            {{ __('Send application') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            var locaKeys = {
                "bold": "{{ __('Bold') }}",
                "italic": "{{ __('Italic') }}",
                "underline": "{{ __('Underline') }}",
                "stroke": "{{ __('Strikethrough') }}",
                "sub": "{{ __('Subscript') }}",
                "sup": "{{ __('Superscript') }}",
                "fontColor": "{{ __('Font colour') }}",
                "fontSize": "{{ __('Font size') }}",
                "backgroundColor": "{{ __('Background colour') }}",
                "backgroundImage": "{{ __('Background image') }}",
                "tooltip": "{{ __('Tool-tip') }}",
                "alignLeft": "{{ __('Left align') }}",
                "alignCenter": "{{ __('Centre align') }}",
                "alignRight": "{{ __('Right align') }}",
                "alignJustify": "{{ __('Justify') }}",
                "block": "{{ __('Break') }}",
                "code": "{{ __('Code') }}",
                "spoiler": "{{ __('Spoiler') }}",
                "moreopts": "{{ __('More Options') }}",
                "list": "{{ __('List') }}",
                "hr": "{{ __('Horizontal line') }}",
                "picture": "{{ __('Image') }}",
                "link": "{{ __('Link') }}",
                "email": "{{ __('Email') }}",
                "player": "{{ __('Player') }}",
                "item": "{{ __('Item') }}",
                "coordinates": "{{ __('Coordinates') }}",
                "preview": "{{ __('Preview') }}",
                "textPlaceHolder": "{{ __('Text...') }}",
                "playerPlaceHolder": "{{ __('Player ID or name') }}",
                "itemPlaceHolder": "{{ __('Item ID') }}",
                "coordinatePlaceHolder": "{{ __('Galaxy:system:position') }}",
                "charsLeft": "{{ __('Characters remaining') }}",
                "colorPicker": {
                    "ok": "{{ __('Ok') }}",
                    "cancel": "{{ __('Cancel') }}",
                    "rgbR": "{{ __('R') }}",
                    "rgbG": "{{ __('G') }}",
                    "rgbB": "{{ __('B') }}"
                },
                "backgroundImagePicker": {
                    "ok": "{{ __('Ok') }}",
                    "repeatX": "{{ __('Repeat horizontally') }}",
                    "repeatY": "{{ __('Repeat vertically') }}"
                }
            };

            $(document).ready(function() {
                initBBCodeEditor(
                    locaKeys,
                    {}, // items - empty for now
                    false,
                    '.alliancetexts',
                    2000,
                    false
                );
                $('.alliancetexts').keyup(); // Trigger keyup to set the character counter

                // Form submission
                $('#applicationForm').on('submit', function(e) {
                    var message = $('#allitext').val();

                    if (message.length > 2000) {
                        e.preventDefault();
                        alert('{{ __("Message is too long (max 2000 characters)") }}');
                        return false;
                    }
                });
            });
        </script>
    </div>
@endsection
