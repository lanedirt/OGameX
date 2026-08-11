$(function () {
  $("thick-headline-background .minimizeBtn").on("click", function (event) {
    event.preventDefault();
    event.stopPropagation();

    if ($(event.currentTarget).hasClass("minimized")) {
      $(event.currentTarget).removeClass("minimized").html("&#128469;&#xFE0E;");
      $("combatsim-section[overview] combatsim-list").show();
      $("combatsim-section[overview] combatsim-shortinfo").show();
      $("combatsim-section[overview] combatsim-actions").removeAttr("style");
      $("combatsim-section[overview]").removeAttr("minimized");
    } else {
      $(event.currentTarget).addClass("minimized").html("&#128470;&#xFE0E;");
      $("combatsim-section[overview] combatsim-list").hide();
      $("combatsim-section[overview] combatsim-shortinfo").hide();
      $("combatsim-section[overview] combatsim-actions").css("flex-direction", "row").css("width", "100%");
      $("combatsim-section[overview]").attr("minimized", true);
    }
  });
  $("thick-headline-background .addParticipantBtn").on("click", function (event) {
    event.preventDefault();
    event.stopPropagation();
    let combatsimSection = $($(event.currentTarget).closest("combatsim-section")[0]);
    addParticipant(combatsimSection);
  });
  $("thick-headline-background .removeParticipantBtn").on("click", function (event) {
    event.preventDefault();
    event.stopPropagation();
    let attackType = $(event.currentTarget).closest("thick-headline-background").data("attackType");
    let combatsimSection = $(event.currentTarget).closest("combatsim-section");

    if (combatsimSection.find("participant-header").length === 1) {
      return;
    }

    let currentActive = combatsimSection.find("participant-header.active").first();
    let participantId = currentActive.data("participantId");
    const sectionToRemove = combatsimSection.find(
      "fleet-content[data-participant-id=" + participantId + "][data-attack-type=" + attackType + "]",
    );
    const previousCoords = {
      galaxy: sectionToRemove.find("coordinates-section input[name='galaxy']").first().val(),
      system: sectionToRemove.find("coordinates-section input[name='system']").first().val(),
      position: sectionToRemove.find("coordinates-section input[name='position']").first().val(),
    };
    sectionToRemove.remove();
    currentActive.remove();
    combatsimSection.find("participants-headline participant-header p").each(function (index, element) {
      $(element).html(index + 1);
    });
    let newActive = combatsimSection.find("fleet-content").first().data("participantId");
    combatsimSection
      .find("fleet-content[data-participant-id=" + newActive + "][data-attack-type=" + attackType + "]")
      .show();
    combatsimSection
      .find(
        "participants-headline participant-header[data-participant-id=" +
          newActive +
          "][data-attack-type=" +
          attackType +
          "]",
      )
      .addClass("active")
      .show();

    if (combatsimSection.find("participant-header").length === 1) {
      combatsimSection.find("participants-headline").hide();
      combatsimSection.find(".removeParticipantBtn").addClass("disabled");
    } //adjust buttons

    $(combatsimSection.find("fleet-section-clear .clearPlayer"))
      .attr("data-participant-id", newActive)
      .data("participantId", newActive)
      .attr("data-attack-type", attackType)
      .data("attackType", attackType);
    $(combatsimSection.find("fleet-section-clear .clearTechnologies"))
      .attr("data-participant-id", newActive)
      .data("participantId", newActive)
      .attr("data-attack-type", attackType)
      .data("attackType", attackType); //adjust base defender

    let firstDefender = $("fleet-content[data-attack-type=2]:not(fleet-content[base-defender])").first();
    firstDefender.find("lifeform-data technology-icon[solarsatellite]").closest("div.technology-fullrow").show();
    firstDefender.find("lifeform-data technology-icon[resbuggy]").closest("div.technology-fullrow").show();
    let coordsGalaxy = firstDefender.find("coordinates-section input[name='galaxy']").first();
    let coordsSystem = firstDefender.find("coordinates-section input[name='system']").first();
    let coordsPosition = firstDefender.find("coordinates-section input[name='position']").first();

    if (coordsGalaxy.val() === "" || coordsSystem.val() === "" || coordsPosition.val() === "") {
      coordsGalaxy.val(previousCoords.galaxy);
      coordsSystem.val(previousCoords.system);
      coordsPosition.val(previousCoords.position);
    }

    $("fleet-content[base-defender]")
      .attr("data-participant-id", firstDefender.data("participantId"))
      .data("participantId", firstDefender.data("participantId"));
    simChanged(combatsimSection);
  });
  $("gradient-button #newCombatPlanning").on("click", function (event) {
    event.preventDefault();
    event.stopPropagation();
    newCombatPlanning();
  });
  $("gradient-button #saveCombatPlanning").on("click", function (event) {
    event.preventDefault();
    event.stopPropagation();
  });
});
