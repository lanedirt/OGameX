<div id="drei">
    <form action="" id="form_createAlly" method="post" name="asdf" class="formValidation">
        <div class="sectioncontent" id="section11" style="display:block;">
            <div class="contentz">
                <table class="createnote createALLY">
                    <tbody><tr>
                        <td class="desc">Alliance Tag (3-8 characters)</td>
                        <td class="value">
                            <input class="text w200 validate[optional,custom[noSpecialCharacters],custom[noBeginOrEndUnderscore],custom[noBeginOrEndWhitespace],custom[noBeginOrEndHyphen],custom[notMoreThanThreeUnderscores],custom[notMoreThanThreeWhitespaces],custom[notMoreThanThreeHyphen],custom[noCollocateUnderscores],custom[noCollocateWhitespaces],custom[noCollocateHyphen],minSize[3]]" style="padding:3px;" type="text" size="8" name="allyTag" id="allyTagField" maxlength="8" value="">
                        </td>
                    </tr>
                    <tr>
                        <td class="desc">Alliance name (3-30 characters)</td>
                        <td class="value">
                            <input class="text w200 validate[optional,custom[noSpecialCharacters],custom[noBeginOrEndUnderscore],custom[noBeginOrEndWhitespace],custom[noBeginOrEndHyphen],custom[notMoreThanThreeUnderscores],custom[notMoreThanThreeWhitespaces],custom[notMoreThanThreeHyphen],custom[noCollocateUnderscores],custom[noCollocateWhitespaces],custom[noCollocateHyphen],minSize[3]]" style="padding:3px;" type="text" size="30" name="allyName" id="allyNameField" maxlength="30" value="">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <a class="createAlly action btn_blue">Create alliance</a>
                        </td>
                    </tr>
                    </tbody></table>
            </div><!--contentdiv -->
            <div class="footer"></div>
        </div><!-- section11 -->
    </form>
</div>
<script type="text/javascript">
    (function($) {
        $.fn.validationEngineLanguage = function() {};
        $.validationEngineLanguage = {
            newLang: function() {
                $.validationEngineLanguage.allRules = 	{
                    "minSize": {
                        "regex": "none",
                        "alertText": "Not enough characters"},
                    "pwMinSize": {
                        "regex": /^.{ 4,}$/,
                        "alertText": "The entered password is to short (min. 4 characters)"},
                    "pwMaxSize": {
                        "regex": /^.{0, 20}$/,
                        "alertText": "The entered password is to long (max. 20 characters)"},
                    "email":{
                        "regex":/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/,
                        "alertText":"You need to enter a valid email address!"},
                    "noSpecialCharacters":{
                        "regex":/^[a-zA-Z0-9\-_\s]+$/,
                        "alertText": "Contains invalid characters."},
                    "noBeginOrEndUnderscore":{
                        "regex":/^([^_]+(.*[^_])?)?$/,
                        "alertText": "Your name may not start or end with an underscore."},
                    "noBeginOrEndHyphen":{
                        "regex":/^([^\-]+(.*[^\-])?)?$/,
                        "alertText": "Your name may not start or finish with a hyphen."},
                    "noBeginOrEndWhitespace":{
                        "regex":/^([^\s]+(.*[^\s])?)?$/,
                        "alertText": "Your name may not start or end with a space."},
                    "notMoreThanThreeUnderscores":{
                        "regex":/^[^_]*(_[^_]*){0,3}$/,
                        "alertText": "Your name may not contain more than 3 underscores in total."},
                    "notMoreThanThreeHyphen":{
                        "regex":/^[^\-]*(\-[^\-]*){0,3}$/,
                        "alertText": "Your name may not contain more than 3 hyphens."},
                    "notMoreThanThreeWhitespaces":{
                        "regex":/^[^\s]*(\s[^\s]*){0,3}$/,
                        "alertText": "Your name may not include more than 3 spaces in total."},
                    "noCollocateUnderscores":{
                        "regex":/^[^_]*(_[^_]+)*_?$/,
                        "alertText": "You may not use two or more underscores one after the other."},
                    "noCollocateHyphen":{
                        "regex":/^[^\-]*(\-[^\-]+)*-?$/,
                        "alertText": "You may not use two or more hyphens consecutively."},
                    "noCollocateWhitespaces":{
                        "regex":/^[^\s]*(\s[^\s]+)*\s?$/,
                        "alertText": "You may not use two or more spaces one after the other."}

                }
            }
        }
        $.validationEngineLanguage.newLang();
    })(jQuery);
</script>
<script language="javascript">
    var defaultName = "doh";
</script>
<script>
    initFormValidation();
</script>