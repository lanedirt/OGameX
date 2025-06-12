<!-- Plasma -->
<table class="general_details">
    <thead>
    <tr>
        <th>Level</th>
        <th>Maximum colonies</th>
        <th>Maximum expeditions</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($astrophysics_table as $record)
        <tr class="@if ($record['level'] === $current_level)
                                current
                                @endif">
            <td class="level" data-value="{{ $record['level'] }}">{{ $record['level'] }}</td>
            <td class="max_colonies" data-value="{{ $record['max_colonies'] }}">
                {{ $record['max_colonies'] }}
            </td>
            <td class="max_expedition" data-value="{{ $record['max_expedition_slots'] }}">
                {{ $record['max_expedition_slots'] }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<ul class="additional_information">
    <li>Positions 3 and 13 can be populated from level 4 onwards.</li>
    <li>Positions 2 and 14 can be populated from level 6 onwards.</li>
    <li>Positions 1 and 15 can be populated from level 8 onwards.</li>
</ul>
