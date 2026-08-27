@include('ingame.shared.buddy.bbcode-parser')

<div class="section">
    <h3>
        <a id="link11" class="closed" onclick="manageTabs('link11');" href="javascript:void(0);" rel="allyData">
            <span>{{ __('t_ingame.alliance.your_alliance') }}</span>
        </a>
    </h3>
</div>
<div class="sectioncontent" id="allyData" style="display: none;">
    <div class="contentz">
        <table class="members bborder">
            <tbody>
            <tr class="alt">
                <td class="desc">{{ __('t_ingame.alliance.name') }}:</td>
                <td class="value"><span>{{ $alliance->alliance_name }}</span></td>
            </tr>
            <tr>
                <td class="desc">{{ __('t_ingame.alliance.tag') }}:</td>
                <td class="value"><span>{{ $alliance->alliance_tag }}</span></td>
            </tr>
            <tr class="alt">
                <td class="desc">{{ __('t_ingame.alliance.created') }}:</td>
                <td class="value"><span>{{ $alliance->created_at->format('d.m.Y') }}</span></td>
            </tr>
            <tr>
                <td class="desc">{{ __('t_ingame.alliance.member') }}:</td>
                <td class="value"><span>{{ $members->count() }}</span></td>
            </tr>
            <tr class="alt">
                <td class="desc">{{ __('t_ingame.alliance.your_rank') }}:</td>
                <td class="value">
                    <span>
                        @if($member->rank)
                            {{ $member->rank->name }}
                        @elseif($alliance->founder_user_id === auth()->id())
                            {{ $alliance->founder_rank_name }}
                        @else
                            {{ $alliance->newcomer_rank_name }}
                        @endif
                    </span>
                </td>
            </tr>
            @if($alliance->homepage_url)
            <tr>
                <td class="desc">{{ __('t_ingame.alliance.homepage') }}:</td>
                <td class="value">
                    <span>
                        <a href="{{ $alliance->homepage_url }}" target="_blank" rel="noopener noreferrer">{{ $alliance->homepage_url }}</a>
                    </span>
                </td>
            </tr>
            @endif
            </tbody>
        </table>
        @if($member && !$member->isFounder())
            <div class="h10"></div>
            <p style="padding: 0 10px; font-size: 11px; color: #999;">{{ __('t_ingame.alliance.leave_wait_warning') }}</p>
            <a class="leaveAllianceOverview action btn_blue" style="margin: 10px;" href="javascript:void(0);">{{ __('t_ingame.alliance.leave_btn') }}</a>
        @endif
        <div class="h10"></div>
    </div>
    <div class="footer"></div>
</div>

<div class="section">
    <h3>
        <a id="link12" class="opened" onclick="manageTabs('link12');" rel="allyMemberlist" href="javascript:void(0);"><span>{{ __('t_ingame.alliance.member_list') }}</span></a>
    </h3>
</div>
<div class="sectioncontent" id="allyMemberlist" style="">
    <div class="contentz">
        <table class="members zebra bborder" cellpadding="0" cellspacing="0" id="member-list">
            <thead>
                <tr>
                    <th class="header">
                        <a href="javascript:void(0);">{{ __('t_ingame.alliance.col_name') }}</a>
                    </th>
                    <th></th>
                    <th class="header">
                        <a href="javascript:void(0);">{{ __('t_ingame.alliance.col_rank') }}</a>
                    </th>
                    <th class="header">
                        <a href="javascript:void(0);">{{ __('t_ingame.alliance.col_rank') }}</a>
                    </th>
                    <th class="header">
                        <a href="javascript:void(0);">{{ __('t_ingame.alliance.col_coords') }}</a>
                    </th>
                    <th class="header">
                        <a href="javascript:void(0);">{{ __('t_ingame.alliance.col_joined') }}</a>
                    </th>
                    @if($member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_SEE_MEMBER_ONLINE_STATUS))
                    <th class="header">
                        <a href="javascript:void(0);">{{ __('t_ingame.alliance.col_online') }}</a>
                    </th>
                    @endif
                    <th>{{ __('t_ingame.alliance.col_function') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($members as $allianceMember)
                <tr class="{{ $loop->iteration % 2 == 0 ? 'alt' : '' }}" data-user-id="{{ $allianceMember->user_id }}">
                    <td>
                        <span>{{ $allianceMember->user->username }}</span>
                    </td>
                    <td></td>
                    <td>
                        @if($alliance->founder_user_id === $allianceMember->user_id)
                            {{ $alliance->founder_rank_name }}
                        @elseif($member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
                            <select class="w100" id="{{ $allianceMember->user_id }}" name="memberRanks[{{ $allianceMember->user_id }}]" data-user-id="{{ $allianceMember->user_id }}">
                                <option value="">{{ $alliance->newcomer_rank_name }}</option>
                                @foreach($ranks as $rank)
                                    <option value="{{ $rank->id }}" {{ $allianceMember->rank_id == $rank->id ? 'selected="selected"' : '' }}>
                                        {{ $rank->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            {{ $allianceMember->rank ? $allianceMember->rank->name : $alliance->newcomer_rank_name }}
                        @endif
                    </td>
                    <td class="member_score">
                        @if($allianceMember->user->highscore && $allianceMember->user->highscore->general_rank)
                            <a href="{{ route('highscore.index', ['category' => 1, 'type' => 0]) }}">{{ $allianceMember->user->highscore->general_rank }}</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @php
                            $planet = \OGame\Models\Planet::where('user_id', $allianceMember->user_id)->first();
                        @endphp
                        @if($planet)
                            <span class="dark_highlight_tablet"><a href="{{ route('galaxy.index', ['galaxy' => $planet->galaxy, 'system' => $planet->system]) }}">[{{ $planet->galaxy }}:{{ $planet->system }}:{{ $planet->planet }}]</a></span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $allianceMember->joined_at->format('d.m.Y H:i:s') }}</td>
                    @if($member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_SEE_MEMBER_ONLINE_STATUS))
                    <td>
                        @if($allianceMember->user->isOnline())
                            <span class="undermark">On</span>
                        @elseif($allianceMember->user->last_login)
                            <span class="overmark">{{ $allianceMember->user->last_login->diffForHumans(null, true, true) }}</span>
                        @else
                            <span class="overmark">Off</span>
                        @endif
                    </td>
                    @endif
                    <td>
                        @if($alliance->founder_user_id !== $allianceMember->user_id)
                            @if($member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_KICK_USER))
                                <a href="#" class="kick-member icon_link tooltip" data-user-id="{{ $allianceMember->user_id }}" data-username="{{ $allianceMember->user->username }}" title="{{ __('t_ingame.alliance.kick_tooltip') }}">
                                    <span class="icon icon_against"></span>
                                </a>
                            @endif
                            <a href="{{ route('messages.index', ['category' => 1, 'user_id' => $allianceMember->user_id]) }}" class="sendMail tooltip" title="{{ __('t_ingame.alliance.write_msg_tooltip') }}">
                                <span class="icon icon_chat"></span>
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">{{ __('t_ingame.alliance.no_members') }}</td>
                </tr>
            @endforelse
            </tbody>
            @if($member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
            <tfoot>
                <tr>
                    <td colspan="8" align="right">
                        <a class="assignRank action btn_blue float_right">{{ __('t_ingame.alliance.assign_rank_btn') }}</a>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
        <div class="h10"></div>
    </div>
    <div class="footer"></div>
</div>

<div class="section">
    <h3>
        <a id="link13" class="closed" onclick="manageTabs('link13');" rel="allyInternText" href="javascript:void(0);">
            <span>{{ __('t_ingame.alliance.internal_area') }}</span>
        </a>
    </h3>
</div>
<div class="sectioncontent" id="allyInternText" style="display:none;">
    <div class="contentz">
        <div id="allyInternTextBB" class="bborder">
            {{ $alliance->internal_text ?? '' }}
        </div>
        <div class="h10"></div>
    </div>
    <div class="footer"></div>
</div>

<div class="section">
    <h3>
        <a id="link14" class="closed" onclick="manageTabs('link14');" rel="allyExternText" href="javascript:void(0);">
            <span>{{ __('t_ingame.alliance.external_area') }}</span>
        </a>
    </h3>
</div>
<div class="sectioncontent" id="allyExternText" style="display:none;">
    <div class="contentz">
        <div id="allyExternTextBB" class="bborder">
            {{ $alliance->external_text ?? '' }}
        </div>
        <div class="h10"></div>
    </div>
    <div class="footer"></div>
</div>

<script type="text/javascript">
    var kickConfirmTemplate = @json(__('t_ingame.alliance.confirm_kick'));

    $(document).ready(function() {
        // Initialize dropdowns for rank selection
        $('select').ogameDropDown(undefined, true);

        // Bind assignRank button
        $('.assignRank').on('click', function(e) {
            e.preventDefault();
            var memberRanks = {};
            $('select[name^="memberRanks"]').each(function() {
                memberRanks[$(this).attr('id')] = $(this).val();
            });

            $.post('{{ route('alliance.members.assign-rank') }}', {
                memberRanks: memberRanks,
                _token: alliance.token
            })
            .done(function(response) {
                if (response.status === 'success') {
                    fadeBox(response.message || @json(__('t_ingame.alliance.msg_ranks_assigned')), false);
                } else {
                    fadeBox(response.message || @json(__('t_ingame.alliance.msg_assign_error')), true);
                }
            })
            .fail(function(xhr) {
                var response = xhr.responseJSON || {};
                fadeBox(response.message || @json(__('t_ingame.alliance.msg_error')), true);
            });
        });

        // Handle kick member
        $('.kick-member').on('click', function(e) {
            e.preventDefault();
            var userId = $(this).data('user-id');
            var username = $(this).data('username');

            if (confirm(kickConfirmTemplate.replace(':username', username))) {
                $.post('{{ route('alliance.members.kick') }}', {
                    user_id: userId,
                    _token: alliance.token
                })
                .done(function(response) {
                    if (response.status === 'success') {
                        fadeBox(response.message, false);
                        $('tr[data-user-id="' + userId + '"]').fadeOut(400, function() {
                            $(this).remove();
                        });
                    } else {
                        fadeBox(response.message || @json(__('t_ingame.alliance.msg_kick_error')), true);
                    }
                })
                .fail(function(xhr) {
                    var response = xhr.responseJSON || {};
                    fadeBox(response.message || @json(__('t_ingame.alliance.msg_error')), true);
                });
            }
        });

        // Prevent default on header links
        $('table#member-list th a').bind('click', function (e) {
            e.preventDefault();
        });

        // Parse BBCode when Internal/External text sections are opened
        $('a[rel="allyInternText"]').on('click', function() {
            setTimeout(function() {
                var $element = $('#allyInternTextBB');
                if ($element.length && !$element.data('bbcode-parsed')) {
                    var bbcodeText = $element.text().trim();
                    if (bbcodeText && typeof window.buddyBBCodeParser === 'function') {
                        $element.html(window.buddyBBCodeParser(bbcodeText));
                        $element.data('bbcode-parsed', true);
                    }
                }
            }, 10);
        });

        $('a[rel="allyExternText"]').on('click', function() {
            setTimeout(function() {
                var $element = $('#allyExternTextBB');
                if ($element.length && !$element.data('bbcode-parsed')) {
                    var bbcodeText = $element.text().trim();
                    if (bbcodeText && typeof window.buddyBBCodeParser === 'function') {
                        $element.html(window.buddyBBCodeParser(bbcodeText));
                        $element.data('bbcode-parsed', true);
                    }
                }
            }, 10);
        });

        // Leave alliance handler
        $('.leaveAllianceOverview').on('click', function(e) {
            e.preventDefault();

            errorBoxDecision(
                "{{ __('t_ingame.shared.caution') }}",
                "{{ __('t_ingame.alliance.confirm_leave') }}",
                "{{ __('t_ingame.shared.yes') }}",
                "{{ __('t_ingame.shared.no') }}",
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
                                fadeBox(response.message || @json(__('t_ingame.alliance.msg_leave_error')), true);
                            }
                            if (response.newAjaxToken) {
                                alliance.updateToken(response.newAjaxToken);
                            }
                        },
                        error: function(xhr) {
                            alliance.loadingIndicator.hide();
                            var errorMessage = @json(__('t_ingame.alliance.msg_leave_error'));
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
