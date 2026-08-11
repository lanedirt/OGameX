function simChanged(obj) {
  if ($(obj).attr("max")) {
    $(obj).val(Math.min(parseInt($(obj).attr("max")), parseInt($(obj).val())) || 0);
  }

  if ($(obj).attr("min")) {
    $(obj).val(Math.max(parseInt($(obj).attr("min")), parseInt($(obj).val())) || $(obj).attr("min"));
  } else {
    $(obj).val(Math.max(0, $(obj).val()));
  }

  if ($("gradient-button button#saveCombatPlanning div.emoji").length === 0) {
    $(
      '<div class="emoji warningsign tooltipLeft" title="' + combatSimLoca.LOCA_COMBATSIM_DATA_CHANGED + '"></div>',
    ).insertBefore($("gradient-button button#saveCombatPlanning span"));
  }

  combatSimChanged = true;
  $("#showCombatResultShortInfo").prop("disabled", true).data("target", "");
  $("#saveCombatPlanning").removeAttr("disabled");
  $("#simulateCombatPlanning").prop("disabled", true);
}
