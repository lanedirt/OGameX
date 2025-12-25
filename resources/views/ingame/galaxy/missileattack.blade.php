<div id="missileAttackLayer">
    <h3>@lang('Missile Attack')</h3>

    <div class="target-info">
        <p>
            <strong>@lang('Target'):</strong>
            <span class="planetname">{{ $target_planet_name }}</span>
            [{{ $target_coords }}]
        </p>
        @if($target_player_name)
        <p>
            <strong>@lang('Owner'):</strong>
            <span class="playername">{{ $target_player_name }}</span>
        </p>
        @endif
    </div>

    @if(!empty($error))
        <div class="error-box">
            <p class="error">{{ $error }}</p>
        </div>
    @else
        <form id="missileAttackForm" method="POST" action="{{ route('galaxy.missile-attack') }}">
            @csrf
            <input type="hidden" name="galaxy" value="{{ $galaxy }}">
            <input type="hidden" name="system" value="{{ $system }}">
            <input type="hidden" name="position" value="{{ $position }}">
            <input type="hidden" name="type" value="{{ $type }}">

            <div class="missiles-info">
                <p>
                    <strong>@lang('Available missiles'):</strong>
                    <span class="available-missiles">{{ $available_missiles }}</span>
                </p>
                <p>
                    <strong>@lang('Missile range'):</strong>
                    <span class="missile-range">{{ $missile_range }} @lang('systems')</span>
                </p>
                @if($target_abm_count > 0)
                    <p class="warning">
                        <img src="/img/galaxy/activity.gif" alt="Warning">
                        @lang('Target has') <strong>{{ $target_abm_count }}</strong> @lang('Anti-Ballistic Missiles')
                    </p>
                @endif
            </div>

            <div class="form-row">
                <label for="missile_count">@lang('Number of missiles'):</label>
                <input
                    type="number"
                    id="missile_count"
                    name="missile_count"
                    min="1"
                    max="{{ $available_missiles }}"
                    value="1"
                    required
                    class="text"
                >
            </div>

            <div class="form-row">
                <label for="target_priority">@lang('Target priority'):</label>
                <select id="target_priority" name="target_priority" class="dropdown">
                    <option value="0">@lang('Cheapest defenses first')</option>
                    <option value="1">@lang('Most expensive defenses first')</option>
                    <option value="2">@lang('Rocket Launcher')</option>
                    <option value="3">@lang('Light Laser')</option>
                    <option value="4">@lang('Heavy Laser')</option>
                    <option value="5">@lang('Gauss Cannon')</option>
                    <option value="6">@lang('Ion Cannon')</option>
                    <option value="7">@lang('Plasma Turret')</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn_blue" id="launchMissileBtn">
                    <span>@lang('Launch Attack')</span>
                </button>
                <button type="button" class="btn_blue close-overlay" onclick="closeOverlay()">
                    <span>@lang('Cancel')</span>
                </button>
            </div>

            <div id="missileAttackResult" class="result-message" style="display: none;"></div>
        </form>
    @endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#missileAttackForm').on('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading state
        var $submitBtn = $('#launchMissileBtn');
        var originalText = $submitBtn.find('span').text();
        $submitBtn.prop('disabled', true).find('span').text('@lang('Launching')...');

        // Hide any previous result messages
        $('#missileAttackResult').hide();

        // Get form data
        var formData = $(this).serialize();

        // Submit via AJAX
        $.ajax({
            url: '{{ route('galaxy.missile-attack') }}',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $('#missileAttackResult')
                        .removeClass('error')
                        .addClass('success')
                        .html(response.message || '@lang('Missiles launched successfully!')')
                        .fadeIn();

                    // Close overlay after 2 seconds
                    setTimeout(function() {
                        closeOverlay();
                        // Refresh galaxy view if needed
                        if (typeof reloadGalaxy === 'function') {
                            reloadGalaxy();
                        }
                    }, 2000);
                } else {
                    // Show error message
                    $('#missileAttackResult')
                        .removeClass('success')
                        .addClass('error')
                        .html(response.error || '@lang('Failed to launch missiles')')
                        .fadeIn();

                    // Re-enable submit button
                    $submitBtn.prop('disabled', false).find('span').text(originalText);
                }
            },
            error: function(xhr) {
                var errorMessage = '@lang('An error occurred. Please try again.')';

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                // Show error message
                $('#missileAttackResult')
                    .removeClass('success')
                    .addClass('error')
                    .html(errorMessage)
                    .fadeIn();

                // Re-enable submit button
                $submitBtn.prop('disabled', false).find('span').text(originalText);
            }
        });
    });
});
</script>

<style>
#missileAttackLayer {
    padding: 20px;
}

#missileAttackLayer h3 {
    margin-bottom: 20px;
}

#missileAttackLayer .target-info {
    background: #0d1014;
    border: 1px solid #1b2024;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

#missileAttackLayer .missiles-info {
    background: #0d1014;
    border: 1px solid #1b2024;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

#missileAttackLayer .missiles-info p.warning {
    color: #ff6b6b;
    font-weight: bold;
    margin-top: 10px;
}

#missileAttackLayer .form-row {
    margin-bottom: 15px;
}

#missileAttackLayer .form-row label {
    display: inline-block;
    width: 200px;
    font-weight: bold;
}

#missileAttackLayer .form-row input.text,
#missileAttackLayer .form-row select.dropdown {
    width: 200px;
    padding: 5px;
}

#missileAttackLayer .form-actions {
    margin-top: 20px;
    text-align: center;
}

#missileAttackLayer .form-actions button {
    margin: 0 10px;
}

#missileAttackLayer .error-box {
    background: #3d1a1a;
    border: 1px solid #ff6b6b;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

#missileAttackLayer .error-box p.error {
    color: #ff6b6b;
    margin: 0;
}

#missileAttackLayer .result-message {
    margin-top: 15px;
    padding: 15px;
    border-radius: 4px;
    text-align: center;
}

#missileAttackLayer .result-message.success {
    background: #1a3d1a;
    border: 1px solid #6bff6b;
    color: #6bff6b;
}

#missileAttackLayer .result-message.error {
    background: #3d1a1a;
    border: 1px solid #ff6b6b;
    color: #ff6b6b;
}
</style>
