<script type="text/javascript">
    var activeTabid = $('.ui-tabs-active a').attr('id'); //erster tab als default
    var hasSubtabs = $('div[aria-labelledby="' + activeTabid + '"] .tab_ctn div ul.subtabs').length;
    var activeSubtabid = '';

    $('.ui-tabs-active a').each(function () {
        activeSubtabid = $(this).attr('id');
    });

    var msgids = [];
    var index = 0;

    if (hasSubtabs > 0) {
        $('div[aria-labelledby="' + activeSubtabid + '"] .msg_new').each(function () {
            msgids[index] = $(this).data('msg-id');
            index++;
        });
    } else {
        $('div[aria-labelledby="' + activeTabid + '"] .msg_new').each(function () {
            msgids[index] = $(this).data('msg-id');
            index++;
        });
    }

    msgids = JSON.stringify(msgids);

    // Remove new message count indicator as soon as the tab is opened
    var message_menu_count = $('.comm_menu.messages span.new_msg_count');
    var message_tab_count = $('.ui-tabs-active .new_msg_count');

    if (message_menu_count.length > 0 && message_tab_count.length > 0) {
        var menuCount = parseInt(message_menu_count[0].innerHTML);
        var tabCount = parseInt(message_tab_count[0].innerHTML);
        var newCount = menuCount - tabCount;

        if (newCount > 0) {
            message_menu_count.html(newCount);
        } else {
            message_menu_count.remove();
        }
    }

    $('.ui-tabs-active .new_msg_count').remove();

    if (hasSubtabs > 0) {
        $('.ui-tabs-active a span:not(.icon_caption)').remove();
    }
</script>