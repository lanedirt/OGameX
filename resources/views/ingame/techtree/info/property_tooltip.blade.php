{{ $property_name }}|
<table class=&quot;combat_unit_details_tooltip&quot;>
    <tr>
        <th>Basic value:</th>
        <td>{{ \OGame\Facades\AppUtil::formatNumber($property_breakdown['rawValue']) }}</td>
    </tr>
    @foreach ($property_breakdown['bonuses'] as $property_bonus)
        <tr>
            <th>
                {{ $property_bonus['type'] }}:
                <span class=&quot;formula&quot;>({{ $property_bonus['percentage'] }}%)</span>
            </th>
            <td>{{ \OGame\Facades\AppUtil::formatNumber($property_bonus['value']) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan=&quot;2&quot; class=&quot;sum&quot;>{{ \OGame\Facades\AppUtil::formatNumber($property_value) }}</td>
    </tr>
</table>
