<div id="rocketsilo">
    <div id="inner">
        <div class="fleft sprite building large building44"></div>
        <div class="content">
            <p>{{ __('t_ingame.facilities_destroy.silo_description') }}</p>
            <span class="capacity">{{ __('t_ingame.facilities_destroy.silo_capacity', ['level' => $silo_level, 'ipm' => $max_ipm_capacity, 'abm' => $max_abm_capacity]) }}</span>
            <form id="rocketForm">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <th>{{ __('t_ingame.facilities_destroy.type') }}</th>
                            <th class="textCenter">{{ __('t_ingame.facilities_destroy.number') }}</th>
                            <th class="textCenter">{{ __('t_ingame.facilities_destroy.tear_down') }}</th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>{{ __('t_resources.anti_ballistic_missile.title') }}</td>
                            <td class="textCenter">{{ $abm_count }}</td>
                            <td class="textCenter"><input type="text" pattern="[0-9]*" value="" class="txt" size="4" maxlength="4" name="destroy_502" id="destroy_502"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('t_resources.interplanetary_missile.title') }}</td>
                            <td class="textCenter">{{ $ipm_count }}</td>
                            <td class="textCenter"><input type="text" pattern="[0-9]*" value="" class="txt" size="4" maxlength="4" name="destroy_503" id="destroy_503"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <input type="button" class="btn_blue float_right" id="destroyMissiles" value="{{ __('t_ingame.facilities_destroy.proceed') }}">
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
            fadeBox('{{ __('t_ingame.facilities_destroy.enter_minimum') }}', 1);
            return;
        }

        // Validate amounts don't exceed available
        if (abmAmount > maxABM) {
            fadeBox('{{ __('t_ingame.facilities_destroy.not_enough_abm') }}', 1);
            return;
        }

        if (ipmAmount > maxIPM) {
            fadeBox('{{ __('t_ingame.facilities_destroy.not_enough_ipm') }}', 1);
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
                    fadeBox(response.message || '{{ __('t_ingame.facilities_destroy.destroyed_success') }}', 0);

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
                    fadeBox(response.error || @json(__('t_ingame.facilities_destroy.destroy_failed')), 1);
                    $('#destroyMissiles').prop('disabled', false);
                }
            },
            error: function(xhr) {
                var errorMessage = @json(__('t_ingame.facilities_destroy.error'));

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
