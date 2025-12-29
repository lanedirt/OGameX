@include('ingame.shared.buddy.bbcode-parser')

<form method="post" id="allianceHandleApplication">
    <div class="sectioncontent" id="section31" style="display:block;">
        <div class="contentz allycomm">
            <table id="writeapplication">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <span class="content"><h2>{{ __('Application text') }}</h2></span>
                            <div class="h10"></div>
                            <div class="bborder"></div>
                            <div class="h10"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="textLeft">
                            <textarea name="message" class="alliancetexts markItUpEditor"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input class="sendNewApplication btn_blue float_right" value="{{ __('Send') }}" name="submitMail" data-allianceid="{{ $allianceId }}" id="submitMail" type="button">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script type="text/javascript">
    var urlSendApplication = "{{ route('alliance.apply') }}";
    var urlCancelApplication = "";

    (function($) {
        initBBCodeEditor(
            {
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
            },
            {}, // items - empty for now
            false,
            '.alliancetexts',
            2000
        );

        $('.alliancetexts').keyup(); // This will trigger the keyup-Event for the editor. This will set the remaining Chars Counter to the right value.

        // Handle form submission
        $('.sendNewApplication').on('click', function() {
            var allianceId = $(this).data('allianceid');
            var message = $('.alliancetexts').val();

            if (message.length > 2000) {
                fadeBox('{{ __("Message is too long (max 2000 characters)") }}', true);
                return false;
            }

            $.ajax({
                url: urlSendApplication,
                type: 'POST',
                data: {
                    alliance_id: allianceId,
                    message: message,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        fadeBox(response.message || '{{ __("Application submitted successfully") }}', false);

                        // Reload alliance page after short delay
                        setTimeout(function() {
                            window.location.href = '{{ route('alliance.index') }}';
                        }, 2000);
                    } else {
                        fadeBox(response.message || '{{ __("Failed to submit application") }}', true);
                    }
                },
                error: function(xhr) {
                    var errorMessage = '{{ __("An error occurred") }}';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            // Handle Laravel validation errors
                            var validationErrors = [];
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                $.each(messages, function(index, message) {
                                    validationErrors.push(message);
                                });
                            });
                            if (validationErrors.length > 0) {
                                errorMessage = validationErrors.join(', ');
                            }
                        }
                    }

                    fadeBox(errorMessage, true);
                }
            });

            return false;
        });
    })(jQuery);
</script>
