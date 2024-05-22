<ul class="pagination">
    <li class="p_li last"><a class="fright txt_link msg_action_link" data-messageid="1741736"
                             data-tabid="20">|&lt;&lt;</a></li>
    <li class="p_li"><a class="fright txt_link msg_action_link" data-messageid="1659201" data-tabid="20">&lt;</a></li>
    <li class="p_li active"><a class="fright txt_link msg_action_link active" data-messageid="1645218" data-tabid="20">1/1</a>
    </li>
    <li class="p_li"><a class="fright txt_link msg_action_link" data-messageid="1645113" data-tabid="20">&gt;</a></li>
    <li class="p_li last"><a class="fright txt_link msg_action_link" data-messageid="1587644"
                             data-tabid="20">&gt;&gt;|</a></li>
</ul>

<div class="detail_msg" data-msg-id="1645218" data-message-type="10">
    {!! $messageBody !!}
</div>
<script type="text/javascript">
    ogame.messages.initDetailMessages(true);
    ogame.messagecounter.resetCounterByType(ogame.messagecounter.type_message);
    var elem, messageId, senderId, receiverId, associationId;

    function reportMessageConfirmation(_elem, _messageId, _senderId, _receiverId, _question) {
        elem = _elem;
        messageId = _messageId;
        senderId = _senderId;
        receiverId = _receiverId;

        errorBoxDecision(
            "Caution",
            _question,
            "yes",
            "No",
            reportMessageCallback
        );
    }

    function reportMessageCallback() {
        elem.hide();
        reportMessage(messageId, senderId, receiverId);
    }
</script>
