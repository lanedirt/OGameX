@php
    // Calculate capacity: 10,000 deuterium per level per hour
    $capacity = $alliance_depot_level * 10000;

    // Build supply times array for JavaScript countdown
    $supplyTimesArray = [];
    foreach ($holding_fleets as $fleet) {
        $supplyTimesArray[$fleet['id']] = $fleet['hold_duration'];
    }
@endphp

<div id="supplydepotlayer">
    <div id="inner">
        <div class="fleft sprite building large building34"></div>
        <div class="content">
            <p>@lang('The alliance depot supplies fuel to friendly fleets in orbit helping with defence. For each upgrade level of the alliance depot, a special demand of deuterium per hour can be sent to an orbiting fleet.')</p>
            <span class="capacity">@lang('Capacity'): {{ number_format($capacity, 0, ',', '.') }} / {{ number_format($capacity, 0, ',', '.') }}</span>

            @if (count($holding_fleets) === 0)
                <div class="textBeefy">@lang('There are no holding fleets!')</div>
            @else
                <form id="supplyForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <table id="supportWrap">
                        <tbody>
                            <tr>
                                <th>@lang('Fleet owner'):</th>
                                <th>@lang('Ships'):</th>
                                <th class="textCenter tooltip" data-tooltip-title="@lang('Hold time')">
                                    <span class="dark_highlight_tablet">
                                        <img src="{{ asset('img/icons/time.gif') }}" height="16" width="16">
                                    </span>
                                </th>
                                <th class="textCenter tooltip" data-tooltip-title="@lang('Extend')">
                                    <span class="dark_highlight_tablet">
                                        <img src="{{ asset('img/icons/extend.gif') }}" height="16" width="16">
                                    </span>
                                </th>
                                <th class="textCenter tooltip" data-tooltip-title="@lang('Supply costs Deuterium / h')">
                                    <span class="dark_highlight_tablet">
                                        <img src="{{ asset('img/icons/deuterium.gif') }}" height="16" width="16">
                                    </span>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <select name="supplyFleetID" id="supplyFleetID" class="dropdown" style="width: 150px">
                                        @foreach ($holding_fleets as $fleet)
                                            <option value="{{ $fleet['id'] }}"
                                                data-ship-count="{{ array_sum(array_column($fleet['ships'], 'amount')) }}"
                                                data-deut-cost="{{ $fleet['deut_cost_per_hour'] ?? 0 }}">{{ $fleet['sender_player_name'] ?? 'Unknown' }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="textCenter">
                                    <span id="shipCount">{{ array_sum(array_column($holding_fleets[0]['ships'], 'amount')) }}</span>
                                </td>
                                <td class="textCenter" id="holdingTimeCell">
                                    @foreach ($holding_fleets as $index => $fleet)
                                        <span class="countdown holdingTime" id="holdingTime-{{ $fleet['id'] }} " style="display: {{ $index === 0 ? 'inline' : 'none' }};"></span>
                                    @endforeach
                                </td>
                                <td class="textCenter supplyTime">
                                    <input type="text" pattern="[0-9,.]*" class="textInput" name="supplyTime" id="supplyTimeInput" value="1" size="2" maxlength="2">&nbsp;h
                                </td>
                                <td class="textCenter">
                                <span id="deutCosts" class="dark_highlight_tablet tooltip" data-tooltip-title="@lang('Supply costs Deuterium / h')">
                                    {{ $holding_fleets[0]['deut_cost_per_hour'] ?? 0 }}
                                </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="button" class="btn_blue float_right" value="@lang('Start supply rockets')" onclick="supplyFleet();">
                </form>
            @endif
        </div>
    </div>
    <br class="clearfloat">
</div>

<script type="text/javascript">
  var supplyTimes = @json($supplyTimesArray);

  function updateSupplyDetails() {
    var $select = $('#supplyFleetID');
    var selectedOption = $select.find('option:selected');
    var fleetId = $select.val();
    var shipCount = selectedOption.data('ship-count');
    var deutCost = selectedOption.data('deut-cost');

    $('#shipCount').text(shipCount);
    $('#deutCosts').text(deutCost);

    // Update holding time display
    $('.holdingTime').hide();
    $('#holdingTime-' + fleetId + ' ').show();

    // Update cost based on hours input
    updateDeutCostBasedOnInput();
  }

  function updateDeutCostBasedOnInput() {
    var $select = $('#supplyFleetID');
    var selectedOption = $select.find('option:selected');
    var deutCostPerHour = selectedOption.data('deut-cost');
    var hours = parseInt($('#supplyTimeInput').val()) || 1;
    $('#deutCosts').text(deutCostPerHour * hours);
  }

  function supplyFleet() {
    var fleetId = $('select[name="supplyFleetID"]').val();
    var hours = parseInt($('#supplyTimeInput').val()) || 1;

    if (!fleetId) {
      if (typeof errorBoxDecision === 'function') {
        errorBoxDecision('@lang("Error")', '@lang("Please select a fleet.")', '@lang("OK")', null, null);
      }
      return;
    }

    if (hours < 1 || hours > 32) {
      if (typeof errorBoxDecision === 'function') {
        errorBoxDecision('@lang("Error")', '@lang("Extension hours must be between 1 and 32.")', '@lang("OK")', null, null);
      }
      return;
    }

    $.ajax({
      url: '{{ route('alliance-depot.send-supply-rocket') }}',
      type: 'POST',
      data: {
        '_token': '{{ csrf_token() }}',
        'fleet_mission_id': fleetId,
        'extension_hours': hours
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          if (typeof fadeBox === 'function') {
            fadeBox(response.message, false);
          }
          // Update the countdown timer with new return time
          // The page will not reload - user can continue supplying fleets
        } else {
          if (typeof errorBoxDecision === 'function') {
            errorBoxDecision('@lang("Error")', response.error, '@lang("OK")', null, null);
          }
        }
      },
      error: function(xhr) {
        var message = '@lang("An error occurred.")';
        if (xhr.responseJSON && xhr.responseJSON.error) {
          message = xhr.responseJSON.error;
        }
        if (typeof errorBoxDecision === 'function') {
          errorBoxDecision('@lang("Error")', message, '@lang("OK")', null, null);
        }
      }
    });
  }

  (function($) {
    // Initialize dropdowns immediately (overlay already loaded)
    if (typeof $.fn.ogameDropDown === 'function') {
      $('#supplydepotlayer select.dropdown').ogameDropDown();
    }

    // Update details when fleet selection changes
    $('#supplyFleetID').on('change', function() {
      updateSupplyDetails();
    });

    // Update cost when hours input changes
    $('#supplyTimeInput').on('input change', function() {
      updateDeutCostBasedOnInput();
    });

    // Initialize Alliance Depot if function exists
    if (typeof initAllianceDepot === 'function') {
      initAllianceDepot();
    }
  })($);
</script>
