<div class="buddyRequest" data-title="{{ $playerName }}">
    <div class="ajaxContent">
        <form action="{{ route('buddies.sendrequest') }}" method="post" id="buddyRequestForm">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $playerId }}">
            <div class="editor_wrap">
                <div>
                    <textarea name="message" class="buddy_request_textarea"></textarea>
                </div>
            </div>
            <input type="submit" class="btn_blue float_right" value="{{ __('t_buddies.ui.send') }}">
        </form>
    </div>
</div>
