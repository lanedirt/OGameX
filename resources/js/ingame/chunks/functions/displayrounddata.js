function displayRoundData(round, participant, side = "attacker") {
  let roundData = getRoundData(round, participant, side); // let shownTechIds = Object.keys(roundData)

  $(`#combatSimRounds .combat_participant.${side} .military_ships > li`).hide().removeClass("even odd");
  $(`#combatSimRounds .combat_participant.${side} .civil_ships > li`).hide().removeClass("even odd");
  $(`#combatSimRounds .combat_participant.${side} .defence_techs > li`).hide().removeClass("even odd");
  Object.keys(roundData).map((techId) => {
    let { remaining, lost } = roundData[techId];

    if (remaining || lost) {
      $(`#${side}CombatSimTechRow_${techId}`).show();
      $(`#${side}CombatSimTechRow_${techId} .detail_shipsleft`).text(remaining);
      $(`#${side}CombatSimTechRow_${techId} .detail_shipslost`).text(-lost);
    }
  });
  let participantNumber = 0;

  if (participant !== "all") {
    participantNumber = participant;
  }

  $(`.${side}CharacterClass characterclass-icon`)
    .removeAttr(characterClassArr.join(" "))
    .attr(findClassName(characterClassArr, combatData[side][participantNumber].characterClassId), true);
  $(`.${side}AllianceClass allianceclass-icon`)
    .removeAttr(allianceClassArr.join(" "))
    .attr(findClassName(allianceClassArr, combatData[side][participantNumber].allianceClassId), true);
  let visibleIterator = 0;
  $(`#combatSimRounds .combat_participant.${side} .military_ships li`).each(function (index, obj) {
    if ($(obj).is(":visible")) {
      if (visibleIterator % 2 === 0) {
        $(obj).addClass("odd");
      } else {
        $(obj).addClass("even");
      }

      visibleIterator++;
    }
  });
  $(`#combatSimRounds .combat_participant.${side} .combatShipsTitle`).show();

  if (!visibleIterator) {
    $(`#combatSimRounds .combat_participant.${side} .combatShipsTitle`).hide();
  }

  visibleIterator = 0;
  $(`#combatSimRounds .combat_participant.${side} .civil_ships li`).each(function (index, obj) {
    if ($(obj).is(":visible")) {
      if (visibleIterator % 2 === 0) {
        $(obj).addClass("odd");
      } else {
        $(obj).addClass("even");
      }

      visibleIterator++;
    }
  });
  $(`#combatSimRounds .combat_participant.${side} .civilShipsTitle`).show();

  if (!visibleIterator) {
    $(`#combatSimRounds .combat_participant.${side} .civilShipsTitle`).hide();
  }

  visibleIterator = 0;
  $(`#combatSimRounds .combat_participant.${side} .defence_techs li`).each(function (index, obj) {
    if ($(obj).is(":visible")) {
      if (visibleIterator % 2 === 0) {
        $(obj).addClass("odd");
      } else {
        $(obj).addClass("even");
      }

      visibleIterator++;
    }
  });
  $(`#combatSimRounds .combat_participant.${side} .defenceTechsTitle`).show();

  if (!visibleIterator) {
    $(`#combatSimRounds .combat_participant.${side} .defenceTechsTitle`).hide();
  }
}
