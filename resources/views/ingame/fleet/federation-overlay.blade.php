<div id="FederationLayer">
<div id="sftcontainer">
    <!--<br class="clearfloat" />-->
    <div id="changefleetname">
        <form action="{{ route('fleet.union.create') }}" id="unionform" name="unionform" onsubmit="handlerToSubmitAjaxForm('unionform'); return false;">
            <input type="hidden" name="fleetID" value="{{ $fleetMissionId ?? '' }}">
            <input type="hidden" name="targetID" value="">
            <input type="hidden" name="unionID" value="">
            <input type="hidden" id="unionUsers" name="unionUsers" value="">
            <input type="hidden" name="token" value="{{ csrf_token() }}">
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
        <form action="#" name="unionUserSearch" id="unionUserSearch" onsubmit="handlerToSubmitAjaxForm('unionUserSearch'); return false;">
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
$(function(){$(document).find('.ui-dialog-titlebar-close').on('click',function(){$("#FederationLayer").remove();});})

(function ($) {
    var buddies = [];
    var participants = [];
    var maxParticipants = 5;

    // Load buddies from server
    function loadBuddies() {
        $.ajax({
            url: '{{ route('buddies.online') }}',
            type: 'GET',
            success: function(response) {
                var buddyList = $('#buddyselect');
                buddyList.empty();

                if (response && response.buddies && response.buddies.length > 0) {
                    buddies = response.buddies;
                    $.each(response.buddies, function(index, buddy) {
                        buddyList.append('<li ref="' + buddy.username + '" class="ui-selectee">' + buddy.username + '</li>');
                    });
                } else {
                    buddyList.append('<li class="empty">@lang('No buddies available')</li>');
                }

                // Initialize selectable
                initSelectable();
            },
            error: function() {
                $('#buddyselect').html('<li class="empty">@lang('Failed to load buddies')</li>');
            }
        });
    }

    // Initialize selectable lists
    function initSelectable() {
        $('#buddyselect').selectable({
            filter: 'li:not(.empty)',
            stop: function() {
                $('.ui-selected', '#buddyselect').each(function() {
                    $(this).data('selected', true);
                });
            }
        });

        $('#participantselect').selectable({
            filter: 'li:not(.undermark)',
            stop: function() {
                $('.ui-selected', '#participantselect').each(function() {
                    $(this).data('selected', true);
                });
            }
        });
    }

    // Add user to union
    window.addUserToUnion = function() {
        var buddyList = $('#buddyselect');
        var participantList = $('#participantselect');

        buddyList.find('li.ui-selected').each(function() {
            var $this = $(this);
            var userId = $this.attr('ref');
            var userName = $this.text();

            // Check if already in participants
            if (!participantList.find('li[ref="' + userId + '"]').length) {
                // Check max participants
                var currentCount = participantList.find('li').length;
                if (currentCount < maxParticipants) {
                    participantList.append('<li ref="' + userId + '" class="ui-selectee">' + userName + '</li>');
                    $this.remove();
                }
            }
        });

        updateParticipantCount();
    };

    // Remove user from union
    window.removeUserFromUnion = function() {
        var participantList = $('#participantselect');
        var buddyList = $('#buddyselect');

        participantList.find('li.ui-selected:not(.undermark)').each(function() {
            var $this = $(this);
            var userId = $this.attr('ref');
            var userName = $this.text();

            // Add back to buddy list
            if (!buddyList.find('li[ref="' + userId + '"]').length && userId !== 'current') {
                if (buddyList.find('.empty').length) {
                    buddyList.find('.empty').remove();
                }
                buddyList.append('<li ref="' + userId + '" class="ui-selectee">' + userName + '</li>');
            }

            $this.remove();
        });

        updateParticipantCount();
    };

    // Update participant count
    function updateParticipantCount() {
        var count = $('#participantselect li').length;
        $('#participantselect').prev('div.textBeefy').text('@lang('Union user'): (' + count + '/' + maxParticipants + ')');
    }

    // Toggle between name edit and user search
    $('#switch').on('click', function() {
        $('#searchFed .wrap').toggle();
    });

    // Ajax form submit handler
    window.handlerToSubmitAjaxForm = function(formId) {
        var $form = $('#' + formId);
        var formData = $form.serialize();
        var action = $form.attr('action');

        $.ajax({
            url: action,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Close the overlay
                    $("#FederationLayer").parent('.overlayDiv').dialog('close');
                    $("#FederationLayer").remove();

                    // Show success message
                    if (typeof fadeBox !== 'undefined') {
                        fadeBox('@lang('Fleet union created successfully')', 1);
                    }

                    // Refresh the page
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (typeof errorBox !== 'undefined') {
                        errorBox(response.error || '@lang('Failed to create fleet union')');
                    }
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON || {};
                if (typeof errorBox !== 'undefined') {
                    errorBox(response.error || '@lang('An error occurred')');
                }
            }
        });
    };

    // Load buddies on init
    loadBuddies();

    // Initialize federation layer
    if (typeof initFederationLayer === 'function') {
        initFederationLayer();
    }
})(jQuery);
</script>
