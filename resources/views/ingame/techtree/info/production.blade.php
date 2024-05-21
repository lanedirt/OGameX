@if (!empty($object->production))
    @if ($object->id == 4)
        <!--  Basic energy production -->
        <table class="general_details">
            <thead>
            <tr>
                <th>
                    Level
                </th>
                <th>
                    Energy Balance
                </th>
                <th>
                    Difference
                </th>
                <th>
                    Difference/Level
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($production_table as $record)
                <tr class="@if ($record['level'] === $current_level)
                                        current
                                        @endif">
                    <td>
                            <span class="undermark"
                                  style="white-space: nowrap">
                                {{ $record['level'] }}                </span>
                    </td>
                    <td>
                            <span class="undermark"
                                  style="white-space: nowrap">
                                {{ number_format($record['production'], 0, ',', '.') }}                </span>
                    </td>
                    <td>
                            <span class="@if ($record['production_difference'] >= 0)
                                    undermark
                                    @else
                                    overmark
                                    @endif" style="white-space: nowrap">
                                {{ number_format($record['production_difference'], 0, ',', '.') }}                </span>
                    </td>
                    <td>
                            <span class="@if ($record['production_difference_per_level'] >= 0)
                                    undermark
                                    @else
                                    overmark
                                    @endif" style="white-space: nowrap">
                                {{ number_format($record['production_difference_per_level'], 0, ',', '.')  }}                </span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @elseif ($object->id === 12)
        <!--  Resource production -->
        <table class="general_details">
            <thead>
                <tr>
                    <th>
                        Level
                    </th>
                    <th>
                        Energy Balance
                    </th>
                    <th>
                        Difference
                    </th>
                    <th>
                        Difference/Level
                    </th>
                    <th>
                        Deuterium consumption
                    </th>
                    <th>
                        Difference
                    </th>
                </tr>
            </thead>
            <tbody>
            @foreach ($production_table as $record)
                <tr class="@if ($record['level'] === $current_level)
                                            current
                                            @endif">
                    <td>
                            <span class="undermark"
                                  style="white-space: nowrap">
                                {{ $record['level'] }}                </span>
                    </td>
                    <td>
                            <span class="undermark"
                                  style="white-space: nowrap">
                                {{ number_format($record['production'], 0, ',', '.') }}                </span>
                    </td>
                    <td>
                            <span class="@if ($record['production_difference'] >= 0)
                                    undermark
                                    @else
                                    overmark
                                    @endif" style="white-space: nowrap">
                                {{ number_format($record['production_difference'], 0, ',', '.') }}                </span>
                    </td>
                    <td>
                            <span class="@if ($record['production_difference_per_level'] >= 0)
                                    undermark
                                    @else
                                    overmark
                                    @endif" style="white-space: nowrap">
                                {{ number_format($record['production_difference_per_level'], 0, ',', '.')  }}                </span>
                    </td>
                    <td>
                            <span class="overmark"
                                  style="white-space: nowrap">
                                {{ number_format($record['deuterium_consumption'], 0, ',', '.') }}                 </span>
                    </td>
                    <td>
                            <span class="@if ($record['deuterium_consumption_per_level'] >= 0)
                                    undermark
                                    @else
                                    overmark
                                    @endif" style="white-space: nowrap">
                                {{ number_format($record['deuterium_consumption_per_level'], 0, ',', '.')  }}                </span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <!--  Resource production -->
        <table class="general_details">
            <thead>
                <tr>
                    <th>
                        Level
                    </th>
                    <th>
                        Production/h
                    </th>
                    <th>
                        Difference
                    </th>
                    <th>
                        Difference/Level
                    </th>
                    <th>
                        Energy Balance:
                    </th>
                    <th>
                        Difference
                    </th>
                    <th>
                        Protected
                    </th>
                </tr>
            </thead>
            <tbody>
            @foreach ($production_table as $record)
                <tr class="@if ($record['level'] === $current_level)
                                        current
                                        @endif">
                    <td>
                            <span class="undermark"
                                  style="white-space: nowrap">
                                {{ $record['level'] }}                </span>
                    </td>
                    <td>
                            <span class="undermark"
                                  style="white-space: nowrap">
                                {{ number_format($record['production'], 0, ',', '.') }}                </span>
                    </td>
                    <td>
                            <span class="@if ($record['production_difference'] >= 0)
                                    undermark
                                    @else
                                    overmark
                                    @endif" style="white-space: nowrap">
                                {{ number_format($record['production_difference'], 0, ',', '.') }}                </span>
                    </td>
                    <td>
                            <span class="@if ($record['production_difference_per_level'] >= 0)
                                    undermark
                                    @else
                                    overmark
                                    @endif" style="white-space: nowrap">
                                {{ number_format($record['production_difference_per_level'], 0, ',', '.')  }}                </span>
                    </td>
                    <td>
                            <span class="overmark"
                                  style="white-space: nowrap">
                                {{ number_format($record['energy_balance'], 0, ',', '.') }}                 </span>
                    </td>
                    <td>
                            <span class="@if ($record['energy_difference'] >= 0)
                                    undermark
                                    @else
                                    overmark
                                    @endif" style="white-space: nowrap">
                                {{ number_format($record['energy_difference'], 0, ',', '.')  }}                </span>
                    </td>
                    <td>
                            <span class="undermark"
                                  style="white-space: nowrap">
                                <!-- TODO: implement den capacity -->
                                {{ number_format($record['protected'], 0, ',', '.') }}                </span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endif
