function selectRound(round) {
  let attacker = "all";

  if ($("#combatSimReport .attacker .participant_select").length) {
    attacker = $("#combatSimReport .attacker .participant_select").val();
  }

  displayRoundData(round, attacker, "attacker");
  let defender = "all";

  if ($("#combatSimReport .defender .participant_select").length) {
    defender = $("#combatSimReport .defender .participant_select").val();
  }

  displayRoundData(round, defender, "defender");
  displayRoundStatistics(round);
  $(".selectRoundBtn").removeAttr("disabled");
  $(`.selectRoundBtn[data-round-number=${round}]`).prop("disabled", true);
}
