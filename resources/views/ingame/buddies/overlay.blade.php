<div class="buddyLayer">
    <div class="messagebox">
        <div id="netz">
            <div id="message">
                <div id="inhalt">
                    <div class="sectioncontent" style="display:block;">
                        <div class="contentz">
                            @if (session('success'))
                                <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="color: #6f9;">{{ session('success') }}</td>
                                    </tr>
                                </table>
                            @endif

                            @if (session('error'))
                                <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="color: #f66;">{{ session('error') }}</td>
                                    </tr>
                                </table>
                            @endif

                            @if ($errors->any())
                                <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="color: #f66;">
                                            @foreach ($errors->all() as $error)
                                                {{ $error }}<br>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <form method="POST" action="{{ route('buddies.sendRequest') }}">
                                @csrf
                                <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                    <tr>
                                        <th colspan="2">Send Buddy Request</th>
                                    </tr>
                                    <tr>
                                        <td style="width: 200px;">Player ID:</td>
                                        <td>
                                            <input class="text w200" type="text" name="receiver_id" id="receiver_id" required
                                                   pattern="[0-9]+"
                                                   title="Please enter a valid player ID"
                                                   value="{{ request()->get('add', '') }}">
                                        </td>
                                    </tr>
                                    <tr class="alt">
                                        <td>Player Name:</td>
                                        <td>
                                            <span style="color: #6f9;">{{ $playerName ?? 'Enter player ID' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Message (optional):</td>
                                        <td>
                                            <textarea name="message" id="message" rows="5" maxlength="500" style="width: 400px;"></textarea>
                                        </td>
                                    </tr>
                                    <tr class="alt">
                                        <td colspan="2" style="text-align: center; padding: 15px;">
                                            <button type="submit" class="btn_blue">Send Buddy Request</button>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
