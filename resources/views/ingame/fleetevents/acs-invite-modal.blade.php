<style>
/* Completely disable tooltips on ACS buttons */
a.convertToACS,
a.inviteToACS {
    pointer-events: auto !important;
}

/* Prevent tooltip classes from working */
a.convertToACS.tooltip,
a.inviteToACS.tooltip,
a.convertToACS.tooltipHTML,
a.inviteToACS.tooltipHTML {
    pointer-events: auto !important;
}

/* Hide any tooltip content */
a.convertToACS[title]:hover::before,
a.convertToACS[title]:hover::after,
a.inviteToACS[title]:hover::before,
a.inviteToACS[title]:hover::after {
    display: none !important;
    content: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

/* Ensure select dropdown is visible */
#playerSelect {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 9999 !important;
}

/* Hide custom dropdown widget if applied */
#playerSelect + .dropdown {
    display: none !important;
}
</style>

<div id="acsInviteModal" class="acs_invite_layer" style="display:none;">
    <div class="messagebox">
        <div id="netz">
            <div id="message">
                <div id="inhalt">
                    <div class="sectioncontent" style="display:block;">
                        <div class="contentz">
                            <h2>Invite Player to ACS Group</h2>

                            <form id="acsInviteForm">
                                <input type="hidden" name="acs_group_id" id="acs_group_id_input" value="">

                                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td class="textCenter" style="padding: 20px;">
                                                <label for="playerSelect" style="display: block; margin-bottom: 10px; color: #6f9fc8;">
                                                    <strong>Select Player:</strong>
                                                </label>
                                                <select id="playerSelect"
                                                        name="player_id"
                                                        class="no-custom-dropdown"
                                                        style="width: 300px !important; height: 30px !important; padding: 5px !important; font-size: 11px !important; background: #0d1014 !important; color: #6f9fc8 !important; border: 1px solid #4a5968 !important; display: inline-block !important; visibility: visible !important;">
                                                    <option value="">Loading players...</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="textCenter" style="padding: 10px;">
                                                <div style="color: #999; font-size: 0.9em; margin-bottom: 15px;">
                                                    Only buddies and alliance members can be invited to ACS attacks.
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="textCenter" style="padding-bottom: 20px;">
                                                <button type="submit" class="btn_blue" style="margin-right: 10px;">Send Invitation</button>
                                                <button type="button" class="btn_blue" id="cancelAcsInvite">Cancel</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var acsInviteDialog = null;

function openACSInviteModal(acsGroupId) {
    console.log('Opening ACS invite modal for group:', acsGroupId);

    // Set the ACS group ID
    $('#acs_group_id_input').val(acsGroupId);

    // Load eligible players
    $('#playerSelect').html('<option value="">Loading players...</option>');

    $.ajax({
        url: "{{ route('fleet.acs.eligible') }}",
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Eligible players response:', data);

            if (data.success && data.players && data.players.length > 0) {
                var options = '<option value="">-- Select a player --</option>';
                data.players.forEach(function(player) {
                    var badge = player.type === 'buddy' ? '[Buddy]' : '[Alliance]';
                    options += '<option value="' + player.id + '">' + player.username + ' ' + badge + '</option>';
                });
                $('#playerSelect').html(options);
                console.log('Loaded', data.players.length, 'eligible players');
            } else {
                console.log('No eligible players found');
                $('#playerSelect').html('<option value="">No eligible players found</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading players:', status, error);
            console.error('Response:', xhr.responseText);
            $('#playerSelect').html('<option value="">Error loading players</option>');
        }
    });

    // Show modal using jQuery dialog
    if (acsInviteDialog === null) {
        acsInviteDialog = $('#acsInviteModal').dialog({
            width: 500,
            height: 'auto',
            modal: true,
            autoOpen: false,
            closeOnEscape: true,
            draggable: true,
            resizable: false,
            open: function() {
                // Remove any custom dropdown widget applied to our select
                setTimeout(function() {
                    var $select = $('#playerSelect');
                    var $customDropdown = $select.next('.dropdown');

                    if ($customDropdown.length) {
                        console.log('Removing custom dropdown widget');
                        $customDropdown.remove();
                    }

                    // Ensure select is visible and functional
                    $select.css({
                        'display': 'inline-block !important',
                        'visibility': 'visible !important',
                        'width': '300px !important',
                        'height': '30px !important'
                    }).show();
                }, 100);
            }
        });
    }

    acsInviteDialog.dialog('open');

    // Also remove custom dropdown after a short delay (in case it's applied after dialog opens)
    setTimeout(function() {
        var $select = $('#playerSelect');
        var $customDropdown = $select.next('.dropdown');

        if ($customDropdown.length) {
            console.log('Removing custom dropdown widget (delayed)');
            $customDropdown.remove();
        }

        $select.show();
    }, 200);
}

$(document).ready(function() {
    // Handle form submission
    $('#acsInviteForm').on('submit', function(e) {
        e.preventDefault();

        var playerId = $('#playerSelect').val();
        var acsGroupId = $('#acs_group_id_input').val();

        if (!playerId) {
            errorBoxAsArray(['Please select a player to invite']);
            return false;
        }

        $.post("{{ route('fleet.acs.invite') }}", {
            acs_group_id: acsGroupId,
            player_id: playerId,
            _token: '{{ csrf_token() }}'
        }, function(data) {
            if (data.success) {
                errorBoxAsArray(['Player invited successfully! They will receive a message.']);
                acsInviteDialog.dialog('close');
            } else {
                errorBoxAsArray([data.message || 'Failed to invite player']);
            }
        }).fail(function() {
            errorBoxAsArray(['An error occurred while inviting the player']);
        });

        return false;
    });

    // Handle cancel button
    $('#cancelAcsInvite').on('click', function() {
        acsInviteDialog.dialog('close');
    });
});
</script>
