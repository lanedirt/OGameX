<div id="eventListWrap">
    <div id="eventHeader">
        <a class="close_details eventToggle" href="javascript:toggleEvents();">
        </a>
        <h2>Events</h2>
    </div>
    <table id="eventContent">
        <tbody>
        {{-- Parse the fleet events as separate rows --}}
        @foreach ($fleet_events as $fleet_event)
            @include ('ingame.fleetevents.eventrow', ['fleet_event_row' => $fleet_event])
        @endforeach
        </tbody>
    </table>
    <div id="eventFooter"></div>
</div>
<script type="text/javascript">
    var timeDelta = 1713793145000 - (new Date()).getTime();
    var LocalizationStrings = {"timeunits":{"short":{"year":"y","month":"m","week":"w","day":"d","hour":"h","minute":"m","second":"s"}},"status":{"ready":"done"},"decimalPoint":".","thousandSeperator":",","unitMega":"M","unitKilo":"K","unitMilliard":"Bn","question":"Question","error":"Error","loading":"load...","notice":"Reference","yes":"yes","no":"No","ok":"Ok","attention":"Caution","outlawWarning":"You are about to attack a stronger player. If you do this, your attack defences will be shut down for 7 days and all players will be able to attack you without punishment. Are you sure you want to continue?","lastSlotWarningMoon":"This building will use the last available building slot. Expand your Lunar Base to receive more space. Are you sure you want to build this building?","lastSlotWarningPlanet":"This building will use the last available building slot. Expand your Terraformer or buy a Planet Field item to obtain more slots. Are you sure you want to build this building?","forcedVacationWarning":"Confirm your lobby account now and we\u2019ll gift you Dark Matter in each universe!","planetMoveBreakUpWarning":"Caution! This mission may still be running once the relocation period starts and if this is the case, the process will be canceled. Do you really want to continue with this job?","moreDetails":"More details","lessDetails":"Less detail","planetOrder":{"lock":"Lock arrangement","unlock":"Unlock arrangement"},"darkMatter":"Dark Matter","errorNotEnoughDM":"Not enough Dark Matter available!","activateItem":{"upgradeItemQuestion":"Would you like to replace the existing item? The old bonus will be lost in the process.","upgradeItemQuestionHeader":"Replace item?"},"characterClassItem":{"buyAndActivateItemQuestion":"Do you want to activate the #characterClassName# class for #darkmatter# Dark Matter? In doing so, you will lose your current class.","activateItemQuestion":"Do you want to activate the #characterClassName# class? In doing so, you will lose your current class."},"allianceClassItem":{"buyAndActivateItemQuestion":"Do you want to activate the alliance class #allianceClassName# for #darkmatter# Dark Matter? In doing so, you will lose your current alliance class.","activateItemQuestion":"Do you want to activate the alliance class #allianceClassName#? In doing so, you will lose your current alliance class.","appendCurrentClassQuestion":"<br><br>Current alliance class: #currentAllianceClassName#<br><br>Last changed on: #lastAllianceClassChange#"},"LOCA_ALL_NETWORK_ATTENTION":"Caution","LOCA_ALL_YES":"yes","LOCA_ALL_NO":"No","redirectMessage":"By following this link, you will leave OGame. Do you wish to continue?"};
    $("a.icon_link.recallFleet").click(function (e) {
        e.preventDefault();
        var fleetId = $(this).attr("data-fleet-id");
        errorBoxDecision(
            "Recall",
            "Recall fleet",
            "yes",
            "No",
            function() {
                $.post(ajaxRecallFleetURI, {fleet_mission_id: fleetId, _token: '{{ csrf_token() }}'}, (data) => {
                    token = data.newAjaxToken

                    if (data.success) {
                        let currentUrl = window.location.href
                        let params = new URLSearchParams(currentUrl)
                        let currentComponent = params.get("component")

                        switch (currentComponent) {
                            case "movement":
                                window.location.reload()
                                return;
                            case "galaxy":
                                if (
                                    submitForm &&
                                    typeof submitForm === "function" &&
                                    typeof galaxy !== "undefined" &&
                                    typeof system !== "undefined"
                                ) {
                                    submitForm();
                                }
                                break;
                        }

                        getAjaxEventbox()
                        refreshFleetEvents()
                    }
                })
            }
        );
        return false;
    });
</script>


