function clearTechnologies(obj) {
  let participantId = $(obj).data("participantId");
  let attackType = $(obj).data("attackType");
  $(
    "fleet-content[data-participant-id=" + participantId + "][data-attack-type=" + attackType + "] ship-section input",
  ).val("");
  $(
    "fleet-content[data-participant-id=" +
      participantId +
      "][data-attack-type=" +
      attackType +
      "] defense-section input",
  ).val("");
  simChanged(obj);
}
