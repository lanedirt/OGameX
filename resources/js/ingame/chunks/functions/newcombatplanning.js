function newCombatPlanning() {
  let participantId = 0,
    attackType;
  let completeCombatsSim = $("div#combatsim");
  completeCombatsSim
    .find("combatsim-section:not(combatsim-section[base-defender]):not(combatsim-section[overview])")
    .each((index, element) => {
      const combatsimSection = $(element);
      combatsimSection.attr("show-lifeform", "0");
      attackType = $(combatsimSection.find("thick-headline-background")[0]).data("attackType"); // remove header

      combatsimSection.find("participants-headline participant-header").remove();
      let newParticipant = [
        {
          attackType: attackType,
          participantId: participantId,
        },
      ]
        .map(participantHeaderTemplate)
        .join("");
      combatsimSection.find("participants-headline").hide().append(newParticipant);
      combatsimSection.find("participants-headline participant-header p").each(function (index, element) {
        $(element).html(index + 1);
      }); // remove fleet sections

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

      combatsimSection.find("fleet-content").remove();
      let newElement = $(newFleetSection);

      if (attackType !== 2) {
        newElement.find("lifeform-data technology-icon[solarsatellite]").closest("div.technology-fullrow").hide();
        newElement.find("lifeform-data technology-icon[resbuggy]").closest("div.technology-fullrow").hide();
      }

      newElement.insertBefore(combatsimSection.find("fleet-section-clear")); //adjust buttons

      $(combatsimSection.find("fleet-section-clear .clearPlayer"))
        .attr("data-participant-id", 0)
        .data("participantId", 0)
        .attr("data-attack-type", attackType)
        .data("attackType", attackType);
      $(combatsimSection.find("fleet-section-clear .clearTechnologies"))
        .attr("data-participant-id", 0)
        .data("participantId", 0)
        .attr("data-attack-type", attackType)
        .data("attackType", attackType);
    });
  completeCombatsSim.find("fleet-content[base-defender] misc-section input").removeAttr("checked");
  completeCombatsSim.find("fleet-content[base-defender] misc-section #round-radioLootModifier2").attr("checked", true);
  completeCombatsSim.find("combatsim-section[base-defender] basic-data .resource-row input").val("");
  completeCombatsSim.find("combatsim-section[base-defender] basic-data defense-section input").val("");
  completeCombatsSim.find("combatsim-section[base-defender] lifeform-data input").val("");
  completeCombatsSim.find("combatsim-section[base-defender] lifeform-data").hide();
  completeCombatsSim.find("combatsim-section[base-defender]").attr("show-lifeform", "0");
  combatSimChanged = false;
  $("gradient-button button#saveCombatPlanning div.emoji").remove();
  combatSimId = 0;
  loadSimDetails();
  $("#deleteCombatPlanning").attr("disabled", true).attr("data-simulation-id", 0).data("simulationId", 0);
  $("combatsim-shortinfo div > span").html("-");
  $("#showCombatResultShortInfo").prop("disabled", true).attr("data-target", "");
  $("#saveCombatPlanning").prop("disabled", true);
  $("#simulateCombatPlanning").prop("disabled", true);
  $(".removeParticipantBtn").addClass("disabled");
}
