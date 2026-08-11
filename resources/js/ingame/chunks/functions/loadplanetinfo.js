function loadPlanetInfo(obj) {
  let loadDataSection = $(obj).closest("div.selectWrapper");
  let inputField = loadDataSection.find(".toggleLink").first();
  let attackType = $(obj).data("attackType");
  let isBaseDefender =
    attackType === 2 &&
    $(obj).data("participantId") === $("fleet-content[data-attack-type=2]").first().data("participantId");
  loadPlanetAction(inputField.data("selectedPlanetid"), $(obj), isBaseDefender, attackType);
  simChanged(obj);
}
