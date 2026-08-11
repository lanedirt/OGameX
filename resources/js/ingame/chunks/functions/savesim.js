function saveSim() {
  let completeCombatsSim = $("div#combatsim");
  let fleetContent, participantId, attackType;
  let attackerObj = {},
    defenderObj = {};
  let currentObject;
  let totalShipCount = 0;
  let firstDefender = $(completeCombatsSim.find("combatsim-section[base-defender] fleet-content")[0]);
  let mainDefenderParticipantId = firstDefender.data("participantId");
  firstDefender = $(
    'fleet-content[data-attack-type="2"][data-participant-id="' +
      mainDefenderParticipantId +
      '"]:not(fleet-content[base-defender])',
  );

  if (
    $(firstDefender.find("coordinates-section input[name='galaxy']")[0]).val().length === 0 ||
    $(firstDefender.find("coordinates-section input[name='system']")[0]).val().length === 0 ||
    $(firstDefender.find("coordinates-section input[name='position']")[0]).val().length === 0
  ) {
    showNotification(combatSimLoca.LOCA_COMBATSIM_INVALID_TARGET, "warning");
    return;
  }

  completeCombatsSim.find("fleet-content").each(function () {
    fleetContent = $(this);
    participantId = fleetContent.data("participantId");

    if (typeof participantId === "undefined") {
      return;
    }

    attackType = fleetContent.data("attackType");

    if (attackType === 1) {
      currentObject = attackerObj;
    } else {
      currentObject = defenderObj;
    }

    if (!currentObject[participantId]) {
      currentObject[participantId] = {
        resources: {},
        researches: {},
        ships: {},
        defenses: {},
        missiles: {},
        bonuses: {},
        fleetspeed: 10,
        allianceClassId: $(fleetContent.find("basic-data class-selection input.allianceClass")[0]).data("classId"),
        characterClassId: $(fleetContent.find("basic-data class-selection input.characterClass")[0]).data("classId"),
        coords: {
          galaxy: $(fleetContent.find("coordinates-section input[name='galaxy']")[0]).val(),
          system: $(fleetContent.find("coordinates-section input[name='system']")[0]).val(),
          position: $(fleetContent.find("coordinates-section input[name='position']")[0]).val(),
        },
      };
    }

    let fleetSpeedElement = $(fleetContent.find("fleetspeed-section")[0]);

    if (fleetSpeedElement.length) {
      currentObject[participantId].fleetspeed = fleetSpeedElement
        .find("input[name='speed[" + attackType + "][" + participantId + "]']:checked")
        .val();
    }

    let technologyId, classId;
    $(fleetContent.find("research-section")[0])
      .find("input")
      .each(function () {
        technologyId = $(this).data("technologyId");

        if (technologyId >= 100 && technologyId < 200) {
          currentObject[participantId].researches[technologyId] = parseInt(
            $(this).val().length === 0 ? 0 : $(this).val(),
          );
          const characterClassName = findClassName(characterClassArr, currentObject[participantId]["characterClassId"]);

          if (characterClassBonuses[characterClassName] && characterClassBonuses[characterClassName][technologyId]) {
            currentObject[participantId].researches[technologyId] = Math.max(
              0,
              currentObject[participantId].researches[technologyId] -
                characterClassBonuses[characterClassName][technologyId],
            );
          }

          const allianceClassName = findClassName(allianceClassArr, currentObject[participantId]["allianceClassId"]);

          if (allianceClassBonuses[allianceClassName] && allianceClassBonuses[allianceClassName][technologyId]) {
            currentObject[participantId].researches[technologyId] = Math.max(
              0,
              currentObject[participantId].researches[technologyId] -
                allianceClassBonuses[allianceClassName][technologyId],
            );
          }
        }
      });
    let lfData = fleetContent.find("lifeform-data").first();
    $(fleetContent.find("ship-section")[0])
      .find("input")
      .each(function () {
        technologyId = $(this).data("technologyId");

        if (technologyId >= 200 && technologyId < 300) {
          totalShipCount += parseInt($(this).val().length === 0 ? 0 : $(this).val());
          currentObject[participantId].ships[technologyId] = {
            amount: parseInt($(this).val().length === 0 ? 0 : $(this).val()),
            weapon: lfData.find("input[name='weapon[" + technologyId + "]']").val() / 100,
            shield: lfData.find("input[name='shield[" + technologyId + "]']").val() / 100,
            armor: lfData.find("input[name='armor[" + technologyId + "]']").val() / 100,
            cargo: lfData.find("input[name='cargo[" + technologyId + "]']").val() / 100,
            speed: lfData.find("input[name='speed[" + technologyId + "]']").val() / 100,
            fuel: lfData.find("input[name='fuel[" + technologyId + "]']").val() / 100,
          };
        }
      });
    fleetContent.find(".resource-row input").each(function () {
      if ($(this).data("resource")) {
        currentObject[participantId].resources[$(this).data("resource")] = parseInt(
          $(this).val().length === 0 ? 0 : $(this).val(),
        );
      }
    });
    lfData.find("characterclass-bonus input").each(function () {
      classId = $(this).data("classId");

      if (classId > 0) {
        currentObject[participantId].bonuses.characterClassBooster = {
          1: parseFloat(
            lfData.find("input[name='classBonus[1]']").val().length === 0
              ? 0
              : lfData.find("input[name='classBonus[1]']").val() / 100,
          ),
          2: parseFloat(
            lfData.find("input[name='classBonus[2]']").val().length === 0
              ? 0
              : lfData.find("input[name='classBonus[2]']").val() / 100,
          ),
          3: parseFloat(
            lfData.find("input[name='classBonus[3]']").val().length === 0
              ? 0
              : lfData.find("input[name='classBonus[3]']").val() / 100,
          ),
        };
      }
    });
    $(fleetContent.find("defense-section")[0])
      .find("input")
      .each(function () {
        technologyId = $(this).data("technologyId");

        if (technologyId >= 200 && technologyId < 300) {
          totalShipCount += parseInt($(this).val().length === 0 ? 0 : $(this).val());
          currentObject[participantId].ships[technologyId] = {
            amount: parseInt($(this).val().length === 0 ? 0 : $(this).val()),
            weapon: firstDefender.find("lifeform-data input[name='weapon[" + technologyId + "]']").val() / 100,
            shield: firstDefender.find("lifeform-data input[name='shield[" + technologyId + "]']").val() / 100,
            armor: firstDefender.find("lifeform-data input[name='armor[" + technologyId + "]']").val() / 100,
            cargo: firstDefender.find("lifeform-data input[name='cargo[" + technologyId + "]']").val() / 100,
            speed: firstDefender.find("lifeform-data input[name='speed[" + technologyId + "]']").val() / 100,
            fuel: firstDefender.find("lifeform-data input[name='fuel[" + technologyId + "]']").val() / 100,
          };
        }

        if (technologyId >= 400 && technologyId < 500) {
          totalShipCount += parseInt($(this).val().length === 0 ? 0 : $(this).val());
          currentObject[participantId].defenses[technologyId] = {
            amount: parseInt($(this).val().length === 0 ? 0 : $(this).val()),
            weapon: lfData.find("input[name='weapon[" + technologyId + "]']").val() / 100,
            shield: lfData.find("input[name='shield[" + technologyId + "]']").val() / 100,
            armor: lfData.find("input[name='armor[" + technologyId + "]']").val() / 100,
          };
        }

        if (technologyId >= 500 && technologyId < 600) {
          currentObject[participantId].missiles[technologyId] = {
            amount: parseInt($(this).val().length === 0 ? 0 : $(this).val()),
          };
        }

        currentObject[participantId].bonuses.denCapacity = {
          metal: parseFloat(
            lfData.find("input[name='denCapacity[22]']").val().length === 0
              ? 0
              : lfData.find("input[name='denCapacity[22]']").val() / 100,
          ),
          crystal: parseFloat(
            lfData.find("input[name='denCapacity[23]']").val().length === 0
              ? 0
              : lfData.find("input[name='denCapacity[23]']").val() / 100,
          ),
          deuterium: parseFloat(
            lfData.find("input[name='denCapacity[24]']").val().length === 0
              ? 0
              : lfData.find("input[name='denCapacity[24]']").val() / 100,
          ),
        };
        currentObject[participantId].bonuses.lifeformProtection = parseFloat(
          lfData.find("input[name='special[11112]']").val().length === 0
            ? 0
            : lfData.find("input[name='special[11112]']").val() / 100,
        );
        currentObject[participantId].bonuses.moonChanceIncrease = parseFloat(
          lfData.find("input[name='special[12112]']").val().length === 0
            ? 0
            : lfData.find("input[name='special[12112]']").val() / 100,
        );
        currentObject[participantId].bonuses.recycleAttackerFleet = parseFloat(
          lfData.find("input[name='special[13112]']").val().length === 0
            ? 0
            : lfData.find("input[name='special[13112]']").val() / 100,
        );
        currentObject[participantId].bonuses.spaceDockExtender = parseFloat(
          lfData.find("input[name='special[14112]']").val().length === 0
            ? 0
            : lfData.find("input[name='special[14112]']").val() / 100,
        );
      });
    let miscSection = $(fleetContent.find("misc-section")[0]);

    if (miscSection.length) {
      currentObject[participantId].engineerActive = $("#square-checkboxEngineer").is(":checked");
      currentObject[participantId].lootFood = $("#square-checkboxFoodLoot").is(":checked");
      currentObject[participantId].lootModifier = $("misc-section input[name=lootModifier]:checked").val();
    }

    if (attackType === 1) {
      attackerObj = currentObject;
    } else {
      defenderObj = currentObject;
    }
  });

  if (Object.keys(attackerObj).length + Object.keys(defenderObj).length > combatSimMaxParticipants) {
    showNotification(
      combatSimLoca.LOCA_COMBATSIM_TOO_MUCH_PARTICIPANTS.replace("#number#", combatSimMaxParticipants),
      "error",
    );
    return;
  }

  if (totalShipCount > combatSimMaxShips) {
    showNotification(
      combatSimLoca.LOCA_COMBATSIM_TOO_MUCH_SHIPS.replace(
        "#number#",
        combatSimMaxShips.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."),
      ),
      "error",
    );
    return;
  }

  let body = {
    _token: token,
    simId: combatSimId,
    participants: JSON.stringify({
      attacker: attackerObj,
      defender: defenderObj,
    }),
  };
  $.ajax({
    url: simBackendUrl + "&action=saveSim",
    data: body,
    type: "POST",
    dataType: "json",
    success: function (json) {
      token = json.newAjaxToken;

      if (typeof json.simData == "object") {
        combatSimChanged = false;
        let combatSimIdBeforeSave = combatSimId;
        $("gradient-button button#saveCombatPlanning div.emoji").remove();
        $("#saveCombatPlanning").prop("disabled", true);
        showNotification(json.message, "success"); // update short info

        $("combatsim-shortinfo .shortSimId span").html(json.simData.simId);
        $("combatsim-shortinfo .shortTarget span").html(json.simData.simName + " " + json.simData.target);
        $("combatsim-shortinfo .shortAttackerCount span").html(json.simData.attackerCount);
        $("combatsim-shortinfo .shortDefenderCount span").html(json.simData.defenderCount);
        $("combatsim-shortinfo .shortShipCount span").html(json.simData.shipCount);
        combatSimId = json.simData.simId;
        $("#deleteCombatPlanning").attr("data-simulation-id", combatSimId).data("simulationId", combatSimId);

        if (json.simData.isOwner === 0) {
          $("#deleteCombatPlanning").removeAttr("disabled");
          $("#simulateCombatPlanning").removeAttr("disabled");

          if ($("combatsim-list owned-sims .noentries").length === 1) {
            $("combatsim-list owned-sims .noentries").remove();
          }
        } else {
          $("#deleteCombatPlanning").prop("disabled", true);
          $("#simulateCombatPlanning").prop("disabled", true);
        } // update overview

        if ($("combatsim-list single-simulation[data-simulation-id=" + json.simData.simId + "]").length === 0) {
          if (json.simData.isOwner === 0) {
            $("combatsim-list owned-sims").append(json.simData.singleSimTemplate);
          }
        } else {
          $("combatsim-list single-simulation[data-simulation-id=" + json.simData.simId + "] .target span").html(
            json.simData.target,
          );
          $("combatsim-list single-simulation[data-simulation-id=" + json.simData.simId + "] .attackerCount span").html(
            json.simData.attackerCount,
          );
          $("combatsim-list single-simulation[data-simulation-id=" + json.simData.simId + "] .defenderCount span").html(
            json.simData.defenderCount,
          );
          $("combatsim-list single-simulation[data-simulation-id=" + json.simData.simId + "] .shipCount span").html(
            json.simData.shipCount,
          );
        }

        $("combatsim-list .entryCount .current").html($("combatsim-list owned-sims single-simulation").length);

        if (combatSimIdBeforeSave !== combatSimId) {
          loadSimDetails();
        }
      }

      if (json.status === "failure") {
        showNotification(json.errors[0].message, "error");

        if (json.errors[0].error === 280001) {
          newCombatPlanning();
          $("combatsim-list single-simulation[data-simulation-id=" + body.simId + "]").remove();
        }
      }
    },
    error: function () {
      showNotification(combatSimLoca.LOCA_ERROR_DEFAULT, "error");
    },
  });
}
