@if (!empty($object->storage))
    <!-- Storage -->
    <table class="general_details">
        <thead>
        <tr>
            <th>Level</th>
            <th>Storage cap.</th>
            <th>Difference</th>
            <th>Difference/level</th>
            <th>Protected (Percent)</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($storage_table as $record)
            <tr class="@if ($record['level'] === $current_level)
                                    current
                                    @endif">
                <td class="level" data-value="{{ $record['level'] }}">{{ $record['level'] }}</td>
                <td class="capacity" data-value="{{ $record['storage'] }}">
                    {{ \OGame\Facades\AppUtil::formatNumberLong($record['storage']) }}
                </td>
                <td class="capacity_difference" data-value="{{ $record['storage_difference'] }}">
                    {{ \OGame\Facades\AppUtil::formatNumberLong($record['storage_difference']) }}
                </td>
                <td class="capacity_level_difference" data-value="{{ $record['storage_difference_per_level'] }}">
                    {{ \OGame\Facades\AppUtil::formatNumberLong($record['storage_difference_per_level']) }}
                </td>
                <td class="protection" data-value="{{ $record['protected'] }}">
                    {{ $record['protected'] }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
