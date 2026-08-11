function closeCombatSimResultOverlay() {
  if (!$(".overlayDiv.combatSimResultOverlay").length) {
    return;
  }

  $(".overlayDiv.combatSimResultOverlay").remove();
}
