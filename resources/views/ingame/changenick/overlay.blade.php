<div class="content">
    <div id="one" class="wrap">
        <form autocomplete="off" method="post" name="prefs" id="prefs" class="formValidation" action="#" rel="#">
            <input type="hidden" name="mode" value="save">
            <div class="group bborder">
                <div class="fieldwrapper">
                    <label class="styled textBeefy">Your player name:</label>
                    <div class="thefield">President Hati</div>
                </div>
                <div class="fieldwrapper">
                    <label class="styled textBeefy">New player name:</label>
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

                <div class="fieldwrapper">
                    <p>You can change your username once per week.
                        To do so, click on your name or the settings at the top of the screen.</p>
                </div>
                <div class="textCenter">
                    <input type="submit" class="btn_blue" value="Use settings">
                </div>
            </div>
        </form>
    </div>
</div>