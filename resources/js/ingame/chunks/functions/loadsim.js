function loadSim(simId) {
  let body = {
    _token: token,
    simId: simId,
  };
  $.ajax({
    url: simBackendUrl + "&action=loadSim",
    data: body,
    type: "POST",
    dataType: "json",
    success: function (json) {
      token = json.newAjaxToken;

      if (json.status === "success") {
        newCombatPlanning(); // update short info

        $("combatsim-shortinfo .shortSimId span").html(json.simData.simId);
        $("combatsim-shortinfo .shortName span").html(json.simData.simName);
        $("combatsim-shortinfo .shortTarget span").html(
          "[" + json.simData.galaxy + ":" + json.simData.system + ":" + json.simData.position + "]",
        );
        $("combatsim-shortinfo .shortAttackerCount span").html(json.simData.attackerCount);
        $("combatsim-shortinfo .shortDefenderCount span").html(json.simData.defenderCount);
        $("combatsim-shortinfo .shortShipCount span").html(json.simData.shipCount);
        combatSimId = json.simData.simId;
        $("#deleteCombatPlanning")
          .removeAttr("disabled")
          .attr("data-simulation-id", combatSimId)
          .data("simulationId", combatSimId);
        let combatSimSection;

        if (json.simData.attackerCount > 1) {
          combatSimSection = $("combatsim-section[data-attack-type=1]").first();
          Object.keys(json.simData.data.attacker)
            .slice(1)
            .forEach((participantId) => {
              addParticipant(combatSimSection, participantId);
            });
        }

        if (json.simData.defenderCount > 1) {
          combatSimSection = $("combatsim-section[data-attack-type=2]").first();
          Object.keys(json.simData.data.defender)
            .slice(1)
            .forEach((participantId) => {
              addParticipant(combatSimSection, participantId);
            });
        }

        let attackType = 1;
        let isBaseDefender = false;
        Object.keys(json.simData.data.attacker).forEach((index) => {
          adjustResearchClassBonuses(json.simData.data.attacker[index]);
          fillData(
            $("fleet-content[data-participant-id=" + index + "][data-attack-type=1]").first(),
            json.simData.data.attacker[index],
            isBaseDefender,
            attackType,
          );
        });
        attackType = 2;
        isBaseDefender = true;
        Object.keys(json.simData.data.defender).forEach((index) => {
          adjustResearchClassBonuses(json.simData.data.defender[index]);
          const element = json.simData.data.defender[index];

          if (isBaseDefender === true) {
            index = 0;

            if (element.engineerActive === true) {
              $("#square-checkboxEngineer").attr("checked", true);
            } else {
              $("#square-checkboxEngineer").removeAttr("checked");
            }

            if (element.lootFood === true) {
              $("#square-checkboxFoodLoot").attr("checked", true);
            } else {
              $("#square-checkboxFoodLoot").removeAttr("checked");
            }

            $("fleet-content[base-defender] misc-section input[name='lootModifier']").removeAttr("checked");
            $("#round-radioLootModifier" + Math.floor((element.lootModifier - 0.25) / 0.25)).attr("checked", true);
          }

          fillData(
            $("fleet-content[data-participant-id=" + index + "][data-attack-type=2]").first(),
            element,
            isBaseDefender,
            attackType,
          );
          isBaseDefender = false;
        });
        combatSimChanged = false;
        $("gradient-button button#saveCombatPlanning div.emoji").remove();

        if (parseInt(json.simData.type) === 0) {
          $("#deleteCombatPlanning").removeAttr("disabled");

          if (json.simStateProgress === true) {
            $("#simulateCombatPlanning").prop("disabled", true);
          } else {
            $("#simulateCombatPlanning").removeAttr("disabled");
          }
        } else {
          $("#deleteCombatPlanning").prop("disabled", true);
          $("#simulateCombatPlanning").prop("disabled", true);
        }

        $("#saveCombatPlanning").prop("disabled", true);
        loadSimDetails();
        showNotification(json.message, "success");
      } else {
        showNotification(json.errors[0].message, "error");

        if (json.errors[0].error === 280001) {
          newCombatPlanning();
          $("combatsim-list single-simulation[data-simulation-id=" + simId + "]").remove();
        }
      }
    },
    error: function () {
      showNotification(combatSimLoca.LOCA_ERROR_DEFAULT, "error");
    },
  });
}
