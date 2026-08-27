{{-- Alliance Applications Tab --}}
<div class="sectioncontent" id="section41" style="display:block;">
    <div class="contentz">
        @if($applications->count() > 0)
            <table class="members">
                <tbody>
                    @foreach($applications as $application)
                        <tr id="application_{{ $application->id }}">
                            <td class="nr">{{ $loop->iteration }}</td>
                            <td class="playername">
                                <a href="{{ route('highscore.index', ['category' => 1, 'type' => 0, 'searchRelId' => $application->user_id]) }}">
                                    {{ $application->user->username }}
                                </a>
                            </td>
                            <td class="date">{{ $application->created_at->format('d.m.Y H:i:s') }}</td>
                            <td class="msg">
                                <div class="application_message">
                                    {{ $application->application_message ?? '' }}
                                </div>
                            </td>
                            <td class="action">
                                @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_EDIT_APPLICATIONS))
                                    <a class="accept_application" data-application-id="{{ $application->id }}" href="javascript:void(0);">
                                        {{ __('t_ingame.alliance.accept_btn') }}
                                    </a>
                                    <a class="reject_application" data-application-id="{{ $application->id }}" href="javascript:void(0);">
                                        {{ __('t_ingame.alliance.deny_btn') }}
                                    </a>
                                    <a class="report_application" data-application-id="{{ $application->id }}" data-user-id="{{ $application->user_id }}" href="javascript:void(0);">
                                        {{ __('t_ingame.alliance.report_btn') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table class="members">
                <tbody>
                    <tr>
                        <td class="nr" style="text-align: center;">{{ __('t_ingame.alliance.no_applications') }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
        <div class="h10"></div>
    </div>
    <div class="footer"></div>
</div>

<script type="text/javascript">
    var urlAccept = "{{ route('alliance.action') }}?action=acceptApplication&asJson=1";
    var urlDeny = "{{ route('alliance.action') }}?action=denyApplication&asJson=1";
    var urlReport = "{{ route('alliance.action') }}?action=report&asJson=1";

    $(document).ready(function(){
        // Handle accept application
        $('.accept_application').click(function(){
            var applicationId = $(this).data('application-id');
            var $row = $('#application_' + applicationId);

            $.ajax({
                url: urlAccept,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    application_id: applicationId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        fadeBox(response.message || @json(__('t_ingame.alliance.msg_accepted')), false);

                        // Remove the row from the DOM
                        $row.fadeOut(400, function() {
                            $(this).remove();

                            // Check if there are any applications left
                            var remainingApplications = $('table.members tbody tr').length;
                            if (remainingApplications === 0) {
                                // Reload the tab to show "No applications found" message
                                if (typeof alliance !== 'undefined' && alliance.fetch) {
                                    alliance.fetch('applications');
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                // Update the application count in the tab
                                updateApplicationCount(remainingApplications);
                            }
                        });
                    }
                },
                error: function(xhr) {
                    fadeBox(xhr.responseJSON?.message || @json(__('t_ingame.alliance.msg_error')), true);
                }
            });
        });

        // Handle reject application
        $('.reject_application').click(function(){
            var applicationId = $(this).data('application-id');
            var $row = $('#application_' + applicationId);

            errorBoxDecision(
                @json(__('t_ingame.alliance.confirm_deny_title')),
                @json(__('t_ingame.alliance.confirm_deny')),
                @json(__('t_ingame.shared.yes')),
                @json(__('t_ingame.shared.no')),
                function() {
                    $.ajax({
                        url: urlDeny,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            application_id: applicationId
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                fadeBox(response.message || @json(__('t_ingame.alliance.msg_rejected')), false);

                                // Remove the row from the DOM
                                $row.fadeOut(400, function() {
                                    $(this).remove();

                                    // Check if there are any applications left
                                    var remainingApplications = $('table.members tbody tr').length;
                                    if (remainingApplications === 0) {
                                        // Reload the tab to show "No applications found" message
                                        if (typeof alliance !== 'undefined' && alliance.fetch) {
                                            alliance.fetch('applications');
                                        } else {
                                            window.location.reload();
                                        }
                                    } else {
                                        // Update the application count in the tab
                                        updateApplicationCount(remainingApplications);
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            fadeBox(xhr.responseJSON?.message || @json(__('t_ingame.alliance.msg_error')), true);
                        }
                    });
                },
                null
            );
        });

        // Handle report application
        $('.report_application').click(function(){
            var applicationId = $(this).data('application-id');
            var userId = $(this).data('user-id');

            $.ajax({
                url: urlReport,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId,
                    type: 'application',
                    reference_id: applicationId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        fadeBox(response.message || @json(__('t_ingame.alliance.report_btn')), false);
                    }
                },
                error: function(xhr) {
                    fadeBox(xhr.responseJSON?.message || @json(__('t_ingame.alliance.msg_error')), true);
                }
            });
        });

        // Helper function to update the application count in the tab header
        function updateApplicationCount(count) {
            var $applicationTab = $('#applicationTab');
            if ($applicationTab.length) {
                $applicationTab.html(@json(__('t_ingame.alliance.tab_applications')) + ' (' + count + ')<span class="newApplications undermark" style="display: ' + (count > 0 ? 'inline' : 'none') + '"></span>');
            }
        }
    });
</script>
