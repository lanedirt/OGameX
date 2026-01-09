{{-- Alliance Management Tab --}}
<div class="section">
    <h3>
        <a id="link21" class="closed" href="javascript:void(0);" rel="allyRanks" onclick="manageTabs('link21');">
            <span>Configure privileges</span>
        </a>
    </h3>
</div>
<div class="sectioncontent" id="allyRanks" style="display:none;">
    <div class="contentz ui-tabs ui-corner-all ui-widget ui-widget-content">
        <form action="" id="form_allyRankRights" method="post" autocomplete="off">
            @csrf
            <table cellpadding="0" cellspacing="0" id="ranks" class="zebra">
                <tbody>
                    <tr class="rank_cat border_bottom">
                        <th></th>
                        <th class=""><span class="rank_name">Rank name</span></th>
                        <th class="border_left" colspan="2">Applications</th>
                        <th class="border_left" colspan="4">Member</th>
                        <th class="border_left" colspan="4">Alliance</th>
                    </tr>
                    <tr class="border_bottom">
                        <th class="delete_rank"></th>
                        <th class="rank_name"></th>
                        <th class="border_left">
                            <img src="/img/alliance/0086e59764f0a5933447ae9c2306c6.gif" title="Show applications">
                        </th>
                        <th class="border_left">
                            <img src="/img/alliance/3c02301d58e4d1311ddc1d2b817d9e.gif" title="Process applications">
                        </th>
                        <th class="border_left">
                            <img src="/img/alliance/f73de0e81ff41d09e49c9e6a89dc60.gif" title="Show member list">
                        </th>
                        <th class="border_left">
                            <img src="/img/alliance/c3844547c784f6fd37b6f74d0103e8.gif" title="Kick user">
                        </th>
                        <th class="border_left">
                            <img src="/img/alliance/b13f429fc7a0f306cdacd30ba99a0b.gif" title="See online status">
                        </th>
                        <th class="border_left">
                            <img src="/img/alliance/e7609dc4601cd9e7bb85c3cbac1ba6.gif" title="Write circular message">
                        </th>
                        <th class="border_left">
                            <img src="/img/alliance/2314800e7f417204ffd5c6e6063ea3.gif" title="Disband alliance">
                        </th>
                        <th class="border_left">
                            <img src="/img/alliance/4271182aabee127ff9d52e0abd3729.gif" title="Manage alliance">
                        </th>
                        <th class="border_left">
                            <img src="/img/alliance/bd63d76f92c9d99cefb727037575e1.gif" title="Right hand">
                        </th>
                        <th class="border_left">
                            <img src="/img/alliance/9bdc920c9196e6487193ceb15c9638.png" title="Manage alliance class">
                        </th>
                    </tr>

                    {{-- Founder Rank (always all permissions, disabled) --}}
                    <tr id="rankRights_founder">
                        <td class="delete-rank"></td>
                        <td class="desc">
                            <span class="rank_name">{{ $alliance->founder_rank_name }}</span>
                        </td>
                        @foreach([
                            'see_applications', 'edit_applications', 'see_members', 'kick_user',
                            'see_member_online_status', 'send_circular_msg', 'delete_ally',
                            'manage_ally', 'right_hand', 'manage_classes'
                        ] as $permission)
                            <td class="check border_left">
                                <input type="checkbox" checked disabled>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Newcomer Rank (no permissions, disabled) --}}
                    <tr id="rankRights_newcomer">
                        <td class="delete-rank"></td>
                        <td class="desc">
                            <span class="rank_name">{{ $alliance->newcomer_rank_name }}</span>
                        </td>
                        @foreach([
                            'see_applications', 'edit_applications', 'see_members', 'kick_user',
                            'see_member_online_status', 'send_circular_msg', 'delete_ally',
                            'manage_ally', 'right_hand', 'manage_classes'
                        ] as $permission)
                            <td class="check border_left">
                                <input type="checkbox" disabled>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Custom Ranks --}}
                    @foreach($ranks as $rank)
                        <tr id="rankRights{{ $rank->id }}">
                            <td class="delete-rank">
                                @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
                                    <a class="deleteRank tooltipLeft js_hideTipOnMobile"
                                       id="delete-{{ $rank->id }}"
                                       data-rankid="{{ $rank->id }}"
                                       href="javascript:void(0);"
                                       data-tooltip-title="Delete rank">
                                        <span>Delete rank</span>
                                    </a>
                                @endif
                            </td>
                            <td class="desc">
                                <span class="rank_name">{{ $rank->rank_name }}</span>
                            </td>
                            @php
                                $permissions = [
                                    ['name' => 'see_applications', 'value' => 1],
                                    ['name' => 'edit_applications', 'value' => 2],
                                    ['name' => 'see_members', 'value' => 4],
                                    ['name' => 'kick_user', 'value' => 8],
                                    ['name' => 'see_member_online_status', 'value' => 16],
                                    ['name' => 'send_circular_msg', 'value' => 32],
                                    ['name' => 'delete_ally', 'value' => 64],
                                    ['name' => 'manage_ally', 'value' => 128],
                                    ['name' => 'right_hand', 'value' => 256],
                                    ['name' => 'manage_classes', 'value' => 2048],
                                ];
                            @endphp
                            @foreach($permissions as $permission)
                                <td class="check border_left">
                                    <input type="checkbox"
                                           name="rankSetting{{ $rank->id }}[{{ $permission['name'] }}]"
                                           data-rankid="{{ $rank->id }}"
                                           data-rankvalue="{{ $permission['value'] }}"
                                           value="1"
                                           {{ $rank->hasPermission($permission['name']) ? 'checked' : '' }}
                                           {{ $member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY) ? '' : 'disabled' }}>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="12" class="no_bg">
                            @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
                                <a class="editRank action btn_blue float_right" href="javascript:void(0);">Save</a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="12"><strong>Warning!</strong> You can only give permissions that you have yourself.</td>
                    </tr>
                </tbody>
            </table>
        </form>

        {{-- Rights Legend --}}
        <div id="rank_explanation">
            <table>
                <tbody>
                    <tr>
                        <td colspan="6">
                            <h3>Rights legend</h3>
                        </td>
                    </tr>
                    <tr>
                        <td width="25px">
                            <img src="/img/alliance/0086e59764f0a5933447ae9c2306c6.gif">
                        </td>
                        <td>Show applications</td>
                        <td width="25px">
                            <img src="/img/alliance/3c02301d58e4d1311ddc1d2b817d9e.gif">
                        </td>
                        <td>Process applications</td>
                        <td width="25px">
                            <img src="/img/alliance/f73de0e81ff41d09e49c9e6a89dc60.gif">
                        </td>
                        <td>Show member list</td>
                    </tr>
                    <tr>
                        <td width="25px">
                            <img src="/img/alliance/c3844547c784f6fd37b6f74d0103e8.gif">
                        </td>
                        <td>Kick user</td>
                        <td width="25px">
                            <img src="/img/alliance/b13f429fc7a0f306cdacd30ba99a0b.gif">
                        </td>
                        <td>See online status</td>
                        <td width="25px">
                            <img src="/img/alliance/e7609dc4601cd9e7bb85c3cbac1ba6.gif">
                        </td>
                        <td>Write circular message</td>
                    </tr>
                    <tr>
                        <td width="25px">
                            <img src="/img/alliance/2314800e7f417204ffd5c6e6063ea3.gif">
                        </td>
                        <td>Disband alliance</td>
                        <td width="25px">
                            <img src="/img/alliance/4271182aabee127ff9d52e0abd3729.gif">
                        </td>
                        <td>Manage alliance</td>
                        <td width="25px">
                            <img src="/img/alliance/bd63d76f92c9d99cefb727037575e1.gif">
                        </td>
                        <td>
                            <span class="right_hand_tablet">Right hand</span>
                            <a href="javascript:void(0);" class="tooltipRight help" data-tooltip-title="`Right Hand` (necessary to transfer founder rank)">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td width="25px">
                            <img src="/img/alliance/9bdc920c9196e6487193ceb15c9638.png">
                        </td>
                        <td>Manage alliance class</td>
                        <td width="25px"></td>
                        <td></td>
                        <td width="25px"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Create New Rank --}}
        @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
            <div class="new_rank">
                <div id="newRank">
                    <form method="post" action="{{ route('alliance.rank.create') }}" id="form_newRank" autocomplete="off">
                        @csrf
                        <a class="createRank action btn_blue float_right" href="javascript:void(0);">Create new rank</a>
                        <input type="text" class="textInput float_right" size="30" maxlength="20" id="newRankName" name="rank_name" placeholder="Rank name">
                    </form>
                </div>
            </div>
        @endif
    </div>
    <div class="footer"></div>
</div>

{{-- Manage Texts Section --}}
<div class="section">
    <h3>
        <a id="link23" class="closed" href="javascript:void(0);" onclick="manageTabs('link23');" rel="allyText">
            <span>Manage texts</span>
        </a>
    </h3>
</div>

<div class="sectioncontent" id="allyText" style="display:none;">
    <div class="contentz ui-tabs ui-corner-all ui-widget ui-widget-content">
        <ul class="tabsbelow subsection_tabs ui-state-active ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header" id="tabs_text" role="tablist">
            <li role="tab" tabindex="0" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active" aria-controls="one" aria-labelledby="tabIntern" aria-selected="true" aria-expanded="true">
                <a href="#one" id="tabIntern" role="presentation" tabindex="-1" class="ui-tabs-anchor"><span>Internal text</span></a>
            </li>
            <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab" aria-controls="two" aria-labelledby="tabExtern" aria-selected="false" aria-expanded="false">
                <a href="#two" id="tabExtern" role="presentation" tabindex="-1" class="ui-tabs-anchor"><span>External text</span></a>
            </li>
            <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab" aria-controls="three" aria-labelledby="tabBewerb" aria-selected="false" aria-expanded="false">
                <a href="#three" id="tabBewerb" role="presentation" tabindex="-1" class="ui-tabs-anchor"><span>Application text</span></a>
            </li>
        </ul>
        <div id="one" aria-labelledby="tabIntern" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="false">
            <form id="form_internAllyText" method="post" autocomplete="off">
                <textarea name="text" class="alliancetexts">{{ $alliance->internal_text ?? '' }}</textarea>
                @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
                    <a class="submitText action btn_blue float_right" data-type="intern" href="javascript:void(0);">Save</a>
                @endif
            </form>
        </div>
        <div id="two" aria-labelledby="tabExtern" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" style="display: none;" aria-hidden="true">
            <form id="form_externAllyText" method="post" autocomplete="off">
                <textarea name="text" class="alliancetexts">{{ $alliance->external_text ?? '' }}</textarea>
                @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
                    <a class="submitText action btn_blue float_right" data-type="extern" href="javascript:void(0);">Save</a>
                @endif
            </form>
        </div>
        <div id="three" aria-labelledby="tabBewerb" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" style="display: none;" aria-hidden="true">
            <form id="form_candidacyText" method="post" autocomplete="off">
                <textarea name="text" class="alliancetexts">{{ $alliance->application_text ?? '' }}</textarea>
                @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
                    <a class="submitText action btn_blue float_right" data-type="candidacy" href="javascript:void(0);">Save</a>
                @endif
            </form>
        </div>
    </div>
    <div class="footer"></div>
</div>

{{-- Alliance Settings Section --}}
<div class="section">
    <h3>
        <a id="link24" class="closed" href="javascript:void(0);" onclick="manageTabs('link24');" rel="allySettings">
            <span>Options</span>
        </a>
    </h3>
</div>

<div class="sectioncontent" id="allySettings" style="display:none;">
    <div class="contentz">
        @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
            <table class="settings_table">
                <tr>
                    <td>Homepage</td>
                    <td><input type="text" class="textInput" id="homepageUrl" value="{{ $alliance->homepage_url ?? '' }}" size="30" maxlength="255"></td>
                </tr>
                <tr>
                    <td>Alliance logo</td>
                    <td><input type="text" class="textInput" id="logoUrl" value="{{ $alliance->logo_url ?? '' }}" size="30" maxlength="255"></td>
                </tr>
                <tr>
                    <td>Applications</td>
                    <td>
                        <select class="w300" name="state" id="state">
                            <option value="1" {{ $alliance->is_open ? 'selected' : '' }}>
                                Possible (alliance open)
                            </option>
                            <option value="0" {{ !$alliance->is_open ? 'selected' : '' }}>
                                Impossible (alliance closed)
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Rename founder title as</td>
                    <td><input type="text" class="textInput" id="foundername" value="{{ $alliance->founder_rank_name ?? 'Founder' }}" size="30" maxlength="20"></td>
                </tr>
                <tr>
                    <td>Rename Newcomer rank</td>
                    <td><input type="text" class="textInput" id="newcomerrankname" value="{{ $alliance->newcomer_rank_name ?? 'Newcomer' }}" size="30" maxlength="20"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a class="saveSetting action btn_blue float_right" href="javascript:void(0);">Save</a>
                    </td>
                </tr>
            </table>
        @else
            <p>You do not have permission to manage alliance settings.</p>
        @endif
    </div>
    <div class="footer"></div>
</div>

{{-- Change Alliance Tag/Name Section --}}
<div class="section">
    <h3>
        <a id="link25" class="closed" href="javascript:void(0);" onclick="manageTabs('link25');" rel="allyTagName">
            <span>Change alliance tag/name</span>
        </a>
    </h3>
</div>

<div class="sectioncontent" id="allyTagName" style="display:none;">
    <div class="contentz">
        @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
            <form id="form_newTagName" method="post" autocomplete="off">
                @csrf
                <table class="settings_table">
                    <tr>
                        <td>Former alliance tag:</td>
                        <td>[{{ $alliance->alliance_tag }}]</td>
                    </tr>
                    <tr>
                        <td>New alliance tag:</td>
                        <td><input type="text" class="textInput" id="newTag" value="{{ $alliance->alliance_tag }}" size="30" maxlength="8"></td>
                    </tr>
                    <tr>
                        <td>Former alliance name:</td>
                        <td>{{ $alliance->alliance_name }}</td>
                    </tr>
                    <tr>
                        <td>New alliance name:</td>
                        <td><input type="text" class="textInput" id="newName" value="{{ $alliance->alliance_name }}" size="30" maxlength="30"></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a class="newTagName action btn_blue float_right" href="javascript:void(0);">Save</a>
                        </td>
                    </tr>
                </table>
            </form>
        @else
            <p>You do not have permission to change alliance tag/name.</p>
        @endif
    </div>
    <div class="footer"></div>
</div>

{{-- Delete Alliance/Pass Alliance On Section --}}
<div class="section">
    <h3>
        <a id="link26" class="closed" href="javascript:void(0);" onclick="manageTabs('link26');" rel="allyDelete">
            <span>Delete alliance/Pass alliance on</span>
        </a>
    </h3>
</div>

<div class="sectioncontent" id="allyDelete" style="display:none;">
    <div class="contentz" id="dissolveally">
        @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_DELETE_ALLY))
            <a class="dissolve action btn_blue" href="javascript:void(0);">Delete this alliance</a>
        @else
            <p>You do not have permission to delete the alliance.</p>
        @endif
    </div>
    <div class="footer"></div>
</div>

{{-- Leave Alliance Section (for non-founders) --}}
@if($member && !$member->isFounder())
<div class="section">
    <h3>
        <a id="link27" class="closed" href="javascript:void(0);" onclick="manageTabs('link27');" rel="allyLeave">
            <span>Leave alliance</span>
        </a>
    </h3>
</div>

<div class="sectioncontent" id="allyLeave" style="display:none;">
    <div class="contentz" id="leaveally">
        <p>If you leave the alliance, you will lose all your rank permissions and alliance benefits.</p>
        <a class="leaveAlliance action btn_blue" href="javascript:void(0);">Leave alliance</a>
    </div>
    <div class="footer"></div>
</div>
@endif

<script type="text/javascript">
    $(document).ready(function(){
        // Initialize tabs for alliance texts
        $('#allyText .contentz').tabs();

        // Initialize BBCode editor for alliance textareas
        initBBCodeEditor(locaKeys, {}, false, '.alliancetexts', 50000, true);

        // Initialize custom dropdown for Applications select
        $('#allySettings select#state').ogameDropDown();

        // Leave alliance handler
        $('.leaveAlliance').on('click', function(e) {
            e.preventDefault();

            errorBoxDecision(
                "{{ __('Caution') }}",
                "{{ __('Are you sure you want to leave the alliance?') }}",
                "{{ __('yes') }}",
                "{{ __('No') }}",
                function() {
                    alliance.loadingIndicator.show();
                    $.ajax({
                        url: '{{ route('alliance.action') }}',
                        type: 'POST',
                        data: {
                            action: 'leave_alliance',
                            _token: alliance.token
                        },
                        success: function(response) {
                            alliance.loadingIndicator.hide();
                            if (response.status === 'success') {
                                fadeBox(response.message, false);
                                // Reload the page after leaving
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                fadeBox(response.message || "{{ __('Failed to leave alliance') }}", true);
                            }
                            if (response.newAjaxToken) {
                                alliance.updateToken(response.newAjaxToken);
                            }
                        },
                        error: function(xhr) {
                            alliance.loadingIndicator.hide();
                            var errorMessage = "{{ __('Failed to leave alliance') }}";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            fadeBox(errorMessage, true);
                        }
                    });
                }
            );
        });
    });
</script>
