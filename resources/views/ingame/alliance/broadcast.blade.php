@include('ingame.shared.buddy.bbcode-parser')

{{-- Alliance Communication Tab --}}
<div class="allianceContent">
    <form method="post" id="allianceBroadCast" autocomplete="off" action="javascript:void(0);" onsubmit="return false;">
        @csrf
        <div class="sectioncontent" id="section31" style="display:block;">
            <div class="contentz allycomm">
                <table id="broadcastTable">
                    <tbody>
                        <tr>
                            <td class="desc textBeefy">{{ __('To') }}</td>
                            <td>
                                <select class="dropdownInitialized" name="empfaenger[]" multiple id="selectNew" style="width: 310px;">
                                    <option value="-1" id="-1" selected>{{ __('all players') }}</option>
                                    @foreach($ranks as $rank)
                                        <option value="{{ $rank->id }}" id="{{ $rank->id }}">{{ __('only rank:') }} {{ $rank->rank_name }}</option>
                                    @endforeach
                                </select>
                                <script language="javascript">
                                    jQuery("#selectNew").select2({
                                        tags: true
                                    });
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="textLeft">
                                <textarea name="text" class="alliancetexts"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input class="btn_blue float_right" value="{{ __('Send') }}" name="submitMail" id="submitMail" type="button">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        var urlSend = "{{ route('alliance.action') }}?action=send_broadcast&asJson=1";
        (function($) {
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

            initBBCodeEditor(
                locaKeys,
                {}, // items - empty for now
                false,
                '.alliancetexts',
                2000
            );
            $('.alliancetexts').keyup(); //This will trigger the keyup-Event for the editor. This will set the remaining Chars Counter to the right value.

            // Note: Click handler for #submitMail is already bound in ingame.js
            // via Alliance.prototype.onFormClickBroadcastButton
        })(jQuery);
    </script>
</div>
