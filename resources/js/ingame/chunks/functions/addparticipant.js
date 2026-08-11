function addParticipant(combatsimSection, participantId = null) {
  let attackType = combatsimSection.data("attackType");
  combatsimSection.attr("show-lifeform", "0");
  combatsimSection.find("participants-headline").show();
  combatsimSection.find(".removeParticipantBtn").removeClass("disabled");

  if (participantId === null) {
    participantId = combatsimSection.find("participants-headline participant-header").last().data("participantId") + 1;
  }

  if ($("participant-header").length >= combatSimMaxParticipants) {
    showNotification(
      combatSimLoca.LOCA_COMBATSIM_TOO_MUCH_PARTICIPANTS.replace("#number#", combatSimMaxParticipants),
      "error",
    );
    return;
  } // handle participants

  $(combatsimSection.find("participant-header")).removeClass("active");
  let newParticipant = [
    {
      attackType: attackType,
      participantId: participantId,
    },
  ]
    .map(participantHeaderTemplate)
    .join("");
  let cloneParticipant = combatsimSection.find("participants-headline participant-header").last().clone(true);
  cloneParticipant
    .attr("data-participant-id", $(newParticipant).data("participantId"))
    .data("participantId", $(newParticipant).data("participantId"))
    .attr("data-attack-type", $(newParticipant).data("attackType"))
    .data("attackType", $(newParticipant).data("attackType"))
    .addClass("active")
    .html($(newParticipant).html())
    .insertAfter(combatsimSection.find("participants-headline participant-header").last()); // handle new fleet

  let newFleetSection = [
    {
      attackType: attackType,
      participantId: participantId,
    },
  ]
    .map(fleetContentTemplate)
    .join("");

  if (attackType === 2) {
    newFleetSection = newFleetSection.replace(/tabindex=\"(\d.*?)\"/g, function (i, match) {
      return 'tabindex="' + (parseInt(match) + 200) + '"';
    });
  }

  let cloneFleet = combatsimSection.find("fleet-content").last().clone(true);
  let newElement = $(newFleetSection);
  $(combatsimSection.find("fleet-content")).hide();
  newElement.find("lifeform-data technology-icon[solarsatellite]").closest("div.technology-fullrow").hide();
  newElement.find("lifeform-data technology-icon[resbuggy]").closest("div.technology-fullrow").hide();
  cloneFleet
    .attr("data-participant-id", newElement.data("participantId"))
    .data("participantId", newElement.data("participantId"))
    .attr("data-attack-type", newElement.data("attackType"))
    .data("attackType", newElement.data("attackType"))
    .html(newElement.html())
    .show()
    .insertAfter(combatsimSection.find("fleet-content").last());
  combatsimSection.find("participants-headline participant-header p").each(function (index, element) {
    $(element).html(index + 1);
  }); //adjust buttons

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
  simChanged(combatsimSection);
}
