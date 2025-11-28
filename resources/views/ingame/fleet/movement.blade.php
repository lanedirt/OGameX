@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="movementcomponent" class="maincontent">
        <div id="movement">
            <div id="inhalt">
                <header id="planet" class="planet-header">
                    <h2>@lang('Fleet movement') - {{ $planet->getPlanetName() }}</h2>
                    <a class="toggleHeader" data-name="movement">
                        <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                    </a>
                </header>
                <div class="c-left"></div>
                <div class="c-right"></div>
                <div class="fleetStatus">
                    <span class="reload">
                        <a class="dark_highlight_tablet" href="javascript:void(0);" onclick="reloadPage();">
                            <span class="icon icon_reload"></span>
                            <span>@lang('Reload')</span>
                        </a>
                    </span>
                    <span class="fleetSlots">
                        @lang('Fleets'): <span class="current">{{ $fleetSlotsInUse }}</span> / <span class="all">{{ $fleetSlotsMax }}</span>
                    </span>
                    <span class="expSlots">
                        @lang('Expeditions'): <span class="current">{{ $expeditionSlotsInUse }}</span> / <span class="all">{{ $expeditionSlotsMax }}</span>
                    </span>
                    <span class="closeAll">
                        <a href="javascript:void(0);" class="all_open">
                            <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                        </a>
                    </span>
                </div>

                @foreach ($fleet_events as $fleet_event)
                    @include('ingame.fleet.partials.movement-row', ['fleet_event' => $fleet_event])
                @endforeach

                <div class="placeholder"></div>
            </div>
        </div>
        <script type="text/javascript">
            function unionEdit(response)
            {
                var data = $.parseJSON(response);
                errorBoxAsArray(data["errorbox"]);
                token = data.token;
                $("#federation_" + data["fleetID"]).children().attr("href", "#federationlayer&ajax=1&union=" + data["unionID"] + "&fleet=" + data["fleetID"] + "&target=" + data["targetID"]);
                $("#FederationLayer").parent('.overlayDiv').dialog('close');
                $("#FederationLayer").remove();
            }

            function reloadPage()
            {
                openParentLocation("{{ route('fleet.movement') }}");
            }

            var currentMovementTabExtensionStates = {!! json_encode(collect($fleet_events)->mapWithKeys(fn($e) => [$e->id => [1, $e->mission_time_arrival]])) !!};
            var showInfos = 1;

            $(document).ready(function() {
                var movementLoca = "{\"callBack\":\"Recall\"}";

                if (showInfos == 0) {
                    showInfos = 1;
                    $(".closeAll").children().removeClass('all_open').addClass('all_closed');
                } else {
                    showInfos = 0;
                    $(".closeAll").children().removeClass('all_closed').addClass('all_open');
                }

                @foreach ($fleet_events as $fleet_event)
                    @if ($fleet_event->remaining_time > 0)
                        new simpleCountdown(
                            getElementByIdWithCache("timer_{{ $fleet_event->id }}"),
                            {{ $fleet_event->remaining_time }},
                            function() { reloadPage(); }
                        );

                        @if (!$fleet_event->is_at_destination)
                        new movementImageCountdown(
                            getElementByIdWithCache("route_{{ $fleet_event->id }}"),
                            {{ $fleet_event->remaining_time }},
                            {{ $fleet_event->duration }},
                            {{ $fleet_event->is_return_trip ? 1 : 0 }},
                            0,
                            274
                        );
                        @endif
                    @endif

                    @if ($fleet_event->is_recallable && !$fleet_event->is_return_trip && !$fleet_event->is_at_destination)
                        new recallShipCountdown(
                            {{ $fleet_event->id }},
                            {{ $fleet_event->active_recall_time }}
                        );
                    @endif

                    @if ($fleet_event->return_remaining_time > 0)
                        new simpleCountdown(
                            getElementByIdWithCache("timerNext_{{ $fleet_event->id }}"),
                            {{ $fleet_event->return_remaining_time }}
                        );
                    @endif
                @endforeach

                initMovement();

                // Recall fleet handler - no confirmation, direct recall
                // Unbind any existing click handlers first, then bind our own
                $("#movement a.recallFleet").off('click').on('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    var fleetId = $(this).attr("data-fleet-id");
                    $.post(ajaxRecallFleetURI, {fleet_mission_id: fleetId, _token: '{{ csrf_token() }}'}, function(data) {
                        if (data.success) {
                            reloadPage();
                        }
                    });
                    return false;
                });
            });
        </script>
    </div>

@endsection
