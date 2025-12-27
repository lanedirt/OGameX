<div id="rocketsilo">
    <div id="inner">
        <div class="fleft sprite building large building44"></div>
        <div class="content">
            <p>@lang('Missile silos are used to construct, store and launch interplanetary and anti-ballistic missiles. With each level of the silo, five interplanetary missiles or ten anti-ballistic missiles can be stored. One Interplanetary missile uses the same space as two Anti-Ballistic missiles. Storage of both Interplanetary missiles and Anti-Ballistic missiles in the same silo is allowed.')</p>
            <span class="capacity">@lang('A missile silo on level :level can hold :ipm interplanetary missiles or :abm anti-ballistic missiles.', ['level' => $silo_level, 'ipm' => $max_ipm_capacity, 'abm' => $max_abm_capacity])</span>
            <form id="rocketForm">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <th>@lang('Type')</th>
                            <th class="textCenter">@lang('Number')</th>
                            <th class="textCenter">@lang('tear down')</th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>@lang('Anti-Ballistic Missiles')</td>
                            <td class="textCenter">{{ $abm_count }}</td>
                            <td class="textCenter"><input type="text" pattern="[0-9]*" value="" class="txt" size="4" maxlength="4" name="destroy_502" id="destroy_502"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>@lang('Interplanetary Missiles')</td>
                            <td class="textCenter">{{ $ipm_count }}</td>
                            <td class="textCenter"><input type="text" pattern="[0-9]*" value="" class="txt" size="4" maxlength="4" name="destroy_503" id="destroy_503"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <input type="button" class="btn_blue float_right" id="destroyMissiles" value="@lang('Proceed')">
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
(function($) {
    var maxABM = {{ $abm_count }};
    var maxIPM = {{ $ipm_count }};

    $('#destroyMissiles').on('click', function(e) {
        e.preventDefault();

        var abmAmount = parseInt($('#destroy_502').val()) || 0;
        var ipmAmount = parseInt($('#destroy_503').val()) || 0;

        // Validate at least one missile selected
        if (abmAmount === 0 && ipmAmount === 0) {
            fadeBox('@lang('Please enter at least one missile to destroy')', 1);
            return;
        }

        // Validate amounts don't exceed available
        if (abmAmount > maxABM) {
            fadeBox('@lang('You do not have that many Anti-Ballistic Missiles')', 1);
            return;
        }

        if (ipmAmount > maxIPM) {
            fadeBox('@lang('You do not have that many Interplanetary Missiles')', 1);
            return;
        }

        // Disable button
        $(this).prop('disabled', true);

        // Submit via AJAX
        $.ajax({
            url: '{{ route('facilities.destroy-rockets') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                abm_amount: abmAmount,
                ipm_amount: ipmAmount
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    fadeBox(response.message || '@lang('Missiles destroyed successfully')', 0);

                    // Close overlay after brief delay
                    setTimeout(function() {
                        $('#rocketsilo').closest('.overlayDiv').dialog('close');
                        // Reload facilities page to update counts
                        if (typeof reloadPage === 'function') {
                            reloadPage();
                        } else {
                            location.reload();
                        }
                    }, 1500);
                } else {
                    fadeBox(response.error || '@lang('Failed to destroy missiles')', 1);
                    $('#destroyMissiles').prop('disabled', false);
                }
            },
            error: function(xhr) {
                var errorMessage = '@lang('An error occurred. Please try again.')';

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                fadeBox(errorMessage, 1);
                $('#destroyMissiles').prop('disabled', false);
            }
        });
    });
})(jQuery);
</script>
