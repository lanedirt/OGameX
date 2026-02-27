<div id="FederationLayer">
<div id="sftcontainer">
    <!--<br class="clearfloat" />-->
    <div id="changefleetname">
        <form action="{{ route('fleet.union.create') }}" id="unionform" name="unionform" onsubmit="submit_unionform(); return false;">
            <input type="hidden" name="fleetID" value="{{ $fleetMissionId ?? '' }}">
            <input type="hidden" name="targetID" value="">
            <input type="hidden" name="unionID" value="">
            <input type="hidden" id="unionUsers" name="unionUsers" value="">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="wrap">
            <div class="col textCenter">
                <a id="switch" href="javascript:void(0);" class="textBeefy">@lang('Search user')</a>
            </div>
            <div class="col">
                <span class="textBeefy">@lang('Union name')</span>
                <input class="textInput fix" id="groupNameInput" type="text" value="" name="groupname">
            </div>
            <br class="clearfloat">
        </div>
        <div class="wrap" style="position:relative">
            <div class="wrapInner">
                <div class="textBeefy">@lang('Buddy list'):</div>
                    <ul size="7" id="buddyselect" class="ui-selectable">
                        <li class="empty">@lang('Loading...')</li>
                    </ul>
                </div>
                <div class="buttonWrap">
                        <input type="button" onclick="addUserToUnion(); return false" class="btn_blue" value="@lang('Invite') &gt;&gt;">
                        <input type="button" onclick="removeUserFromUnion(); return false" class="btn_blue" value="&lt;&lt; @lang('Kick')">
                </div>
                <div class="wrapInner">
                    <div class="textBeefy">@lang('Union user'): (1/5)</div>
                    <ul size="7" id="participantselect" class="ui-selectable">
                        <li ref="current" class="undermark">@lang('You')</li>
                    </ul>
                </div><!-- float1 -->
                   <br class="clearfloat">
            </div>
            <input type="submit" id="fleetOK" class="btn_blue float_right buttonOK" value="@lang('Ok')">
        </form>
        <form action="#" name="unionUserSearch" id="unionUserSearch" onsubmit="submit_unionUserSearch(); return false;">
            <div id="searchFed">
                <div id="honorWarning">
                    @lang('Honourable battles can become dishonourable battles if strong players enter through ACS. The attackers` sum of total military points in comparison to the defenders` sum of total military points is the decisive factor here.')
                </div>
                <div class="wrap" style="display:none;">
                    <span class="textBeefy">@lang('Search user'):</span>
                    <input class="textInput fix" type="text" name="addtogroup">
                    <input type="submit" class="btn_blue" value="@lang('Search')">
                    </div>
            </div><!-- float2 -->
        </form>
        <br class="clearfloat">
    </div><!-- changefleetname -->
</div><!-- sftcontainer -->
</div>
<script type="text/javascript">
function submit_unionUserSearch(){ajaxFormSubmit('unionUserSearch','{{ route('fleet.federation.overlay') }}',unionUser);}

(function() {
    // Load buddies from server
    jQuery.ajax({
        url: '{{ route('buddies.online') }}',
        type: 'GET',
        success: function(response) {
            var buddyList = jQuery('#buddyselect');
            buddyList.empty();

            if (response && response.buddies && response.buddies.length > 0) {
                jQuery.each(response.buddies, function(index, buddy) {
                    buddyList.append('<li ref="' + buddy.username + '" class="ui-selectee">' + buddy.username + '</li>');
                });
            } else {
                buddyList.append('<li class="empty">@lang('No buddies available')</li>');
            }

            initFederationLayer();
        },
        error: function() {
            jQuery('#buddyselect').html('<li class="empty">@lang('Failed to load buddies')</li>');
            initFederationLayer();
        }
    });
})();
</script>
