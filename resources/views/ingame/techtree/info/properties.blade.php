@if (!empty($object['properties']))
    <table class="combat_unit_details">
        <thead>
        <tr>
            <th colspan="2">Technical data</th>
        </tr>
        </thead>
        <tbody>
        @if (isset($object['properties']['structural_integrity']))
            <tr class="structural_integrity">
                <th>Structural Integrity</th>
                <td>
                                    <span class="tooltipHTML tipped advice"
                                          title="{{ $object['tooltips']['structural_integrity'] }}" data-value="9600"
                                          data-base-value="4000" data-bonus="4800" data-bonus-formula="12 x 400">
                                        {{ number_format($object['properties']['structural_integrity'], 0, ',', '.') }}
                                    </span>
                </td>
            </tr>
        @endif
        @if (isset($object['properties']['shield']))
            <tr class="shield_strength">
                <th>Shield Strength</th>
                <td>
                                <span class="tooltipHTML tipped advice" title="{{ $object['tooltips']['shield'] }}"
                                      data-value="21" data-base-value="10" data-bonus="9" data-bonus-formula="9 x 1">
                                    {{ number_format($object['properties']['shield'], 0, ',', '.') }}
                                </span>
                </td>
            </tr>
        @endif
        @if (isset($object['properties']['attack']))
            <tr class="attack_strength">
                <th>Attack Strength</th>
                <td>
                                    <span class="tooltipHTML tipped advice" title="{{ $object['tooltips']['attack'] }}"
                                          data-value="110" data-base-value="50" data-bonus="50"
                                          data-bonus-formula="10 x 5">
                                        {{ number_format($object['properties']['attack'], 0, ',', '.') }}
                                    </span>
                </td>
            </tr>
        @endif
        @if (isset($object['properties']['speed']))
            <tr class="speed">
                <th>Speed</th>
                <td>
                                    <span class="tooltipHTML tipped advice" title="{{ $object['tooltips']['speed'] }}"
                                          data-value="32500" data-base-value="12500" data-bonus="7500"
                                          data-bonus-formula="6 × 1,250">
                                        {{ number_format($object['properties']['speed'], 0, ',', '.') }}
                                    </span>
                </td>
            </tr>
        @endif
        @if (isset($object['properties']['capacity']))
            <tr class="cargo_capacity">
                <th>Cargo Capacity</th>
                <td>
                                    <span class="tooltipHTML tipped advice" title="{{ $object['tooltips']['shield'] }}"
                                          data-value="57" data-base-value="50" data-bonus="7.5"
                                          data-bonus-formula="50 × 15%">
                                        {{ number_format($object['properties']['capacity'], 0, ',', '.') }}
                                    </span>
                </td>
            </tr>
        @endif
        @if (isset($object['properties']['fuel']))
            <tr class="fuel_consumption">
                <th>Fuel usage (Deuterium)</th>
                <td>
                                <span class="tooltipHTML tipped advice" title="{{ $object['tooltips']['fuel'] }}"
                                      data-value="7" data-base-value="20" data-bonus="-10" data-bonus-formula="50%">
                                    {{ number_format($object['properties']['fuel'], 0, ',', '.') }}
                                </span>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
@endif