function addFleetContainer(planetPosition, planetType) {
  return `<div id="ownFleetStatus_${planetPosition}_${planetType}"
            class="fleetAction js_hideTipOnMobile hideTooltipOnMouseenter"
            title="">
        </div>`;
}
