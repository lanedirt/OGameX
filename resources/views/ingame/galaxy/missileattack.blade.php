<div class="missile_attack_layer">
    <div class="messagebox">
        <div id="netz">
            <div id="message">
                <div id="inhalt">
                    <div class="sectioncontent" style="display:block;">
                        <div class="contentz">
                            <h2>Missile Attack</h2>

                            @if(!$canAttack)
                                <div class="textCenter" style="padding: 20px; color: #ff6666;">
                                    <strong>{{ $errorMessage }}</strong>
                                </div>
                            @else
                                <table cellpadding="0" cellspacing="0" class="missileAttackTable" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td class="textCenter" style="padding-bottom:10px;">
                                                <strong>Target:</strong>
                                                @if($targetPlanet)
                                                    {{ $targetPlanet->getPlanetName() }} [{{ $galaxy }}:{{ $system }}:{{ $position }}]
                                                @else
                                                    [{{ $galaxy }}:{{ $system }}:{{ $position }}]
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="textCenter" style="padding-bottom:10px;">
                                                <strong>Distance:</strong> {{ $distance }} system(s)
                                                <br>
                                                <strong>Your Range:</strong> {{ $missileRange }} system(s)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="textCenter" style="padding-bottom:10px;">
                                                <strong>Available Missiles:</strong> {{ $availableMissiles }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ptb10 textCenter">
                                                <form id="missileAttackForm" method="POST" action="{{ route('galaxy.missile-attack.post') }}">
                                                    @csrf
                                                    <input type="hidden" name="galaxy" value="{{ $galaxy }}">
                                                    <input type="hidden" name="system" value="{{ $system }}">
                                                    <input type="hidden" name="position" value="{{ $position }}">
                                                    <input type="hidden" name="type" value="{{ $planetType }}">

                                                    <label for="missileCount">Number of missiles:</label><br>
                                                    <input type="number"
                                                           id="missileCount"
                                                           name="missiles"
                                                           class="textInput"
                                                           value="1"
                                                           min="1"
                                                           max="{{ $availableMissiles }}"
                                                           style="width: 100px; margin: 10px;">
                                                    <br><br>

                                                    <label for="targetPriority">Target Priority:</label><br>
                                                    <select id="targetPriority" name="target_priority" class="textInput" style="width: 200px; margin: 10px;">
                                                        <option value="cheapest">Cheapest First (Default)</option>
                                                        <option value="expensive">Most Expensive First</option>
                                                        <option value="rocket_launcher">Rocket Launchers</option>
                                                        <option value="light_laser">Light Lasers</option>
                                                        <option value="heavy_laser">Heavy Lasers</option>
                                                        <option value="gauss_cannon">Gauss Cannons</option>
                                                        <option value="ion_cannon">Ion Cannons</option>
                                                        <option value="plasma_turret">Plasma Turrets</option>
                                                    </select>
                                                    <br><br>
                                                    <button type="submit" class="btn_blue buttonSave">Launch Missiles</button>
                                                </form>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="textCenter" style="padding-top: 20px; font-size: 0.9em; color: #999;">
                                    <p>Each missile has 12,000 attack power and will destroy enemy defenses.</p>
                                    <p>The target's Anti-Ballistic Missiles (ABM) will intercept your missiles 1:1.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#missileAttackForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    fadeBox(response.message, false);
                    // Close the overlay using jQuery dialog
                    $('.missile_attack_layer').closest('.ui-dialog-content').dialog('close');
                    // Refresh the galaxy view to show updated missile count
                    if (typeof submitForm === 'function') {
                        submitForm();
                    }
                } else {
                    fadeBox(response.message || 'An error occurred', true);
                }
            },
            error: function(xhr) {
                var message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                fadeBox(message, true);
            }
        });
    });
});
</script>
