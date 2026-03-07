<div class="content">
    <div id="one" class="wrap">
        <form autocomplete="off" method="post" name="prefs" id="prefs" class="formValidation" action="{{ route('changenick.rename') }}" rel="{{ route('changenick.rename') }}">
            <input type="hidden" name="mode" value="save">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />
            <div class="group bborder">
                <div class="fieldwrapper">
                    <label class="styled textBeefy">{{ __('t_ingame.options.your_player_name') }}</label>
                    <div class="thefield">{{ $currentUsername }}</div>
                </div>
                @if ($canUpdateUsername)
                    <div class="fieldwrapper">
                        <label class="styled textBeefy">{{ __('t_ingame.options.new_player_name') }}</label>
                        <div class="thefield">
                            <input class="textInput w200 validate[optional,custom[noSpecialCharacters],custom[noBeginOrEndUnderscore],custom[noBeginOrEndWhitespace],custom[noBeginOrEndHyphen],custom[notMoreThanThreeUnderscores],custom[notMoreThanThreeWhitespaces],custom[notMoreThanThreeHyphen],custom[noCollocateUnderscores],custom[noCollocateWhitespaces],custom[noCollocateHyphen],minSize[3]]" type="text" maxlength="1024" value="" size="30" id="db_character" name="db_character">
                        </div>
                    </div>
                    <div class="fieldwrapper">
                        <label class="styled textBeefy">{{ __('t_ingame.options.enter_password_confirm') }}</label>
                        <div class="thefield">
                            <input class="textInput w200" type="password" value="" size="30" name="db_character_password" id="db_character_password">
                        </div>
                    </div>
                @endif
                <div class="fieldwrapper">
                    <p>{{ __('t_ingame.options.username_change_once_week') }}
                    {{ __('t_ingame.options.username_change_hint') }}</p>
                </div>
                @if ($canUpdateUsername)
                <div class="textCenter">
                    <input type="submit" class="btn_blue" value="{{ __('t_ingame.options.use_settings') }}">
                </div>
                @endif
            </div>
        </form>
    </div>
</div>
