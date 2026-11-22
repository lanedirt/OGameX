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
                <a id="switch" href="javascript:void(0);" class="textBeefy">{{ __('t_ingame.fleet.search_user') }}</a>
            </div>
            <div class="col">
                <span class="textBeefy">{{ __('t_ingame.fleet.union_name') }}</span>
                <input class="textInput fix" id="groupNameInput" type="text" value="{{ $unionName ?? '' }}" name="groupname">
            </div>
            <br class="clearfloat">
        </div>
        <div class="wrap" style="position:relative">
            <div class="wrapInner">
                <div class="textBeefy">{{ __('t_ingame.fleet.buddy_list') }}:</div>
                    <ul size="7" id="buddyselect" class="ui-selectable">
                        <li class="empty">{{ __('t_ingame.fleet.buddy_list_loading') }}</li>
                    </ul>
                </div>
                <div class="buttonWrap">
                        <input type="button" onclick="addUserToUnion(); return false" class="btn_blue" value="{{ __('t_ingame.fleet.invite') }} &gt;&gt;">
                        <input type="button" onclick="removeUserFromUnion(); return false" class="btn_blue" value="&lt;&lt; {{ __('t_ingame.fleet.kick') }}">
                </div>
                <div class="wrapInner">
                    <div class="textBeefy">{{ __('t_ingame.fleet.union_user') }}: (1/5)</div>
                    <ul size="7" id="participantselect" class="ui-selectable">
                        <li ref="current" class="undermark">{{ $playerName }}</li>
                        @foreach ($unionMembers as $memberName)
                            <li ref="{{ $memberName }}" class="undermark">{{ $memberName }}</li>
                        @endforeach
                    </ul>
                </div><!-- float1 -->
                   <br class="clearfloat">
            </div>
            <input type="submit" id="fleetOK" class="btn_blue float_right buttonOK" value="{{ __('t_ingame.fleet.ok') }}">
        </form>
        <form action="#" name="unionUserSearch" id="unionUserSearch" onsubmit="submit_unionUserSearch(); return false;">
            <div id="searchFed">
                <div id="honorWarning">
                    {{ __('t_ingame.fleet.desc_acs_attack') }}
                </div>
                <div class="wrap" style="display:none;">
                    <span class="textBeefy">{{ __('t_ingame.fleet.search_user') }}:</span>
                    <input class="textInput fix" type="text" name="addtogroup">
                    <input type="submit" class="btn_blue" value="{{ __('t_ingame.fleet.search') }}">
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
                buddyList.append('<li class="empty">{{ __('t_ingame.fleet.buddy_list_empty') }}</li>');
            }

            initFederationLayer();
        },
        error: function() {
            jQuery('#buddyselect').html('<li class="empty">{{ __('t_ingame.fleet.buddy_list_error') }}</li>');
            initFederationLayer();
        }
    });
})();
</script>
