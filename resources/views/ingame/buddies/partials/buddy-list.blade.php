@if(isset($buddies) && $buddies->isNotEmpty())
    @foreach($buddies as $buddyRequest)
        @php
            // Determine which user is the buddy (not the current user)
            $buddy = $buddyRequest->sender_user_id === auth()->id()
                ? $buddyRequest->receiver
                : $buddyRequest->sender;
        @endphp
        <tr>
            <td>{{ $buddy->id }}</td>
            <td>{{ $buddy->username }}</td>
            <td>
                <a href="#" class="txt_link" onclick="deleteBuddy.call(this); return false;"
                   id="{{ $buddy->id }}"
                   ref="Do you really want to delete {{ $buddy->username }} from your buddy list?">
                    <span class="icon icon_trash"></span>
                </a>
            </td>
        </tr>
    @endforeach
@elseif(isset($search_results) && $search_results->isNotEmpty())
    @foreach($search_results as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->username }}</td>
            <td>
                <a href="#" class="txt_link" onclick="sendBuddyRequest({{ $user->id }}); return false;">
                    <span>Add as buddy</span>
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="3" class="textCenter">
            <p class="box_highlight textCenter no_buddies">No buddies found</p>
        </td>
    </tr>
@endif
