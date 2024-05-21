<!-- Plasma -->
<table class="general_details">
    <thead>
    <tr>
        <th>Level</th>
        <th>Metal bonus %</th>
        <th>Crystal bonus %</th>
        <th>Deuterium bonus %</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($plasma_table as $record)
        <tr class="@if ($record['level'] === $current_level)
                                current
                                @endif">
            <td class="level" data-value="{{ $record['level'] }}">{{ $record['level'] }}</td>
            <td class="metal_bonus" data-value="{{ $record['metal_bonus'] }}">
                {{ \OGame\Facades\AppUtil::formatNumberLong($record['metal_bonus']) }}
            </td>
            <td class="crystal_bonus" data-value="{{ $record['crystal_bonus'] }}">
                {{ $record['crystal_bonus'] }}
            </td>
            <td class="deuterium_bonus" data-value="{{ $record['deuterium_bonus'] }}">
                {{ $record['deuterium_bonus'] }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
