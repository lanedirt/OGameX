
<ul class="subsection_tabs">
    <li>
        <a class="overlay reiter"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $object['id']]) }}">
            <span>
                Techtree            </span>
        </a>
    </li>
    <li>
        <a class="overlay reiter"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 4, 'object_id' => $object['id']]) }}">
            <span>
                Applications            </span>
        </a>
    </li>
    <li>
        <a class="overlay reiter active"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $object['id']]) }}">
            <span>
                Techinfo            </span>
        </a>
    </li>
    <li>
        <a class="overlay reiter"
           data-overlay-same="true"
           href="{{ route('techtree.ajax', ['tab' => 3, 'object_id' => $object['id']]) }}">
            <span>
                Technology            </span>
        </a>
    </li>
</ul>

<div class="techtree" data-id="c28d1c5551545f27be33f22c5643c45e" data-title="Techinfo - {{ $object['title'] }}">
    <div id="techinfo">
        <div class="techwrapper">
            <div class="leftcol building tech{{ $object_id }}">
                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="200" height="200" />
            </div>

            <div class="rightcol">
                <p>{!! nl2br($object['description_long']) !!}</p>

                <!--
                Different types of tables:
                - Resource production values
                - Energy production values
                - Storage values
                -->
                @if (!empty($object['production']))
                    @if ($object['id'] == 4)
                        <!--  Basic energy production -->
                        <table cellpadding="0" cellspacing="0">
                            <tbody>
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
                            @foreach ($production_table as $record)
                                <tr class="detailTableRow @if ($record['level'] == $current_level)
                                        currentlevel
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
                    @elseif ($object['id'] == 12)
                        <!--  Resource production -->
                            <table cellpadding="0" cellspacing="0">
                                <tbody>
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
                                @foreach ($production_table as $record)
                                    <tr class="detailTableRow @if ($record['level'] == $current_level)
                                            currentlevel
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
                        <table cellpadding="0" cellspacing="0">
                            <tbody>
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
                            @foreach ($production_table as $record)
                                <tr class="detailTableRow @if ($record['level'] == $current_level)
                                        currentlevel
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

            </div><!-- rightcol -->
            <br class="clearfloat"/>
        </div><!-- techwrapper -->
    </div><!-- techinfo -->

    <script type="text/javascript">
        $(document).ready(function(){
            $(".detailTableRow:not(.currentlevel):odd").addClass('alt');
        });
    </script>
</div>
<script type="text/javascript">
    $(document).ready(function(){initOverlayName();});</script>