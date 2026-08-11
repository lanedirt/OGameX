function switchParticipant(obj) {
  if ($(obj).hasClass("active")) {
    return;
  }

  let attackType = $(obj).data("attackType");
  let participantId = $(obj).data("participantId");
  let combatsimSection = $(obj).closest("combatsim-section");
  $('participant-header[data-attack-type="' + attackType + '"]').removeClass("active");
  $('participant-header[data-attack-type="' + attackType + '"][data-participant-id="' + participantId + '"]').addClass(
    "active",
  );
  $('fleet-content[data-attack-type="' + attackType + '"]:not(fleet-content[base-defender])').hide();
  $('fleet-content[data-attack-type="' + attackType + '"][data-participant-id="' + participantId + '"]').show(); //adjust buttons

  $(combatsimSection.find("fleet-section-clear .clearPlayer"))
    .attr("data-participant-id", participantId)
    .data("participantId", participantId)
    .attr("data-attack-type", attackType)
    .data("attackType", attackType);
  $(combatsimSection.find("fleet-section-clear .clearTechnologies"))
    .attr("data-participant-id", participantId)
    .data("participantId", participantId)
    .attr("data-attack-type", attackType)
    .data("attackType", attackType);
}
