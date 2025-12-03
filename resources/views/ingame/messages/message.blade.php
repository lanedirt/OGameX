@php
    $isFirstMessage = !isset($pagination['prevId']) || $pagination['prevId'] === null;
    $isLastMessage = !isset($pagination['nextId']) || $pagination['nextId'] === null;
    $currentIndex = $pagination['currentIndex'] ?? 1;
    $totalCount = $pagination['totalCount'] ?? 1;
    $tab = $pagination['tab'] ?? '';
    $subtab = $pagination['subtab'] ?? '';
@endphp

<ul class="pagination">
    <li class="p_li last">
        <a class="fright txt_link msg_action_link @if($isFirstMessage) disabled @endif"
           data-messageid="{{ $pagination['firstId'] ?? $messageId }}"
           data-tab="{{ $tab }}"
           data-subtab="{{ $subtab }}"
           @if($isFirstMessage) style="opacity: 0.5; cursor: default;" @endif>|&lt;&lt;</a>
    </li>
    <li class="p_li">
        <a class="fright txt_link msg_action_link @if($isFirstMessage) disabled @endif"
           data-messageid="{{ $pagination['prevId'] ?? $messageId }}"
           data-tab="{{ $tab }}"
           data-subtab="{{ $subtab }}"
           @if($isFirstMessage) style="opacity: 0.5; cursor: default;" @endif>&lt;</a>
    </li>
    <li class="p_li active">
        <a class="fright txt_link msg_action_link active"
           data-messageid="{{ $messageId }}"
           data-tab="{{ $tab }}"
           data-subtab="{{ $subtab }}">{{ $currentIndex }}/{{ $totalCount }}</a>
    </li>
    <li class="p_li">
        <a class="fright txt_link msg_action_link @if($isLastMessage) disabled @endif"
           data-messageid="{{ $pagination['nextId'] ?? $messageId }}"
           data-tab="{{ $tab }}"
           data-subtab="{{ $subtab }}"
           @if($isLastMessage) style="opacity: 0.5; cursor: default;" @endif>&gt;</a>
    </li>
    <li class="p_li last">
        <a class="fright txt_link msg_action_link @if($isLastMessage) disabled @endif"
           data-messageid="{{ $pagination['lastId'] ?? $messageId }}"
           data-tab="{{ $tab }}"
           data-subtab="{{ $subtab }}"
           @if($isLastMessage) style="opacity: 0.5; cursor: default;" @endif>&gt;&gt;|</a>
    </li>
</ul>

<div class="detail_msg" data-msg-id="{{ $messageId }}" data-message-type="10">
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
