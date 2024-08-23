<div class="content">
    <div id="one" class="wrap">
        <form autocomplete="off" method="post" name="prefs" id="prefs" class="formValidation" action="{{ route('changenick.rename') }}" rel="{{ route('changenick.rename') }}">
            <input type="hidden" name="mode" value="save">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />
            <div class="group bborder">
                <div class="fieldwrapper">
                    <label class="styled textBeefy">@lang('Your player name:')</label>
                    <div class="thefield">{{ $currentUsername }}</div>
                </div>
                @if ($canUpdateUsername)
                    <div class="fieldwrapper">
                        <label class="styled textBeefy">@lang('New player name:')</label>
                        <div class="thefield">
                            <input class="textInput w200 validate[optional,custom[noSpecialCharacters],custom[noBeginOrEndUnderscore],custom[noBeginOrEndWhitespace],custom[noBeginOrEndHyphen],custom[notMoreThanThreeUnderscores],custom[notMoreThanThreeWhitespaces],custom[notMoreThanThreeHyphen],custom[noCollocateUnderscores],custom[noCollocateWhitespaces],custom[noCollocateHyphen],minSize[3]]" type="text" maxlength="1024" value="" size="30" id="db_character" name="db_character">
                        </div>
                    </div>
                    <div class="fieldwrapper">
                        <label class="styled textBeefy">Enter password <em>(as confirmation)</em>:</label>
                        <div class="thefield">
                            <input class="textInput w200" type="password" value="" size="30" name="db_character_password" id="db_character_password">
                        </div>
                    </div>
                @endif
                <div class="fieldwrapper">
                    <p>@lang('You can change your username once per week.')
                    @lang('To do so, click on your name or the settings at the top of the screen.')</p>
                </div>
                @if ($canUpdateUsername)
                <div class="textCenter">
                    <input type="submit" class="btn_blue" value="@lang('Use settings')">
                </div>
                @endif
            </div>
        </form>
    </div>
</div> 