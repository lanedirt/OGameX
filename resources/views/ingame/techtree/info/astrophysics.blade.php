<!-- Astrophysics -->
<table class="general_details">
    <thead>
    <tr>
        <th>{{ __('t_ingame.techtree.col_level') }}</th>
        <th>{{ __('t_ingame.techtree.astrophysics_max_colonies') }}</th>
        <th>{{ __('t_ingame.techtree.astrophysics_max_expeditions') }}</th>
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
    <li>{{ __('t_ingame.techtree.astrophysics_note_1') }}</li>
    <li>{{ __('t_ingame.techtree.astrophysics_note_2') }}</li>
    <li>{{ __('t_ingame.techtree.astrophysics_note_3') }}</li>
</ul>
