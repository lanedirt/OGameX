function clearPlayer(obj) {
  let participantId = $(obj).data("participantId");
  let attackType = $(obj).data("attackType");
  $(
    "fleet-content[data-participant-id=" +
      participantId +
      "][data-attack-type=" +
      attackType +
      "] fleetspeed-section input",
  ).prop("checked", false);
  $(
    "fleet-content[data-participant-id=" +
      participantId +
      "][data-attack-type=" +
      attackType +
      "] fleetspeed-section input[value='10']",
  ).prop("checked", true);
  let fleetSection = $("fleet-content[data-participant-id=" + participantId + "][data-attack-type=" + attackType + "]");
  changeClass(fleetSection.find("characterclass-icon"), characterClassArr[0], "characterclass");
  changeClass(fleetSection.find("allianceclass-icon"), allianceClassArr[0], "allianceclass");
  fleetSection.find("research-section input").val("");
  fleetSection.find("ship-section input").val("");
  fleetSection.find("defense-section input").val("");
  fleetSection.find("coordinates-section input").val("");
  fleetSection.find("lifeform-data input").val("");
  simChanged(obj);
}
