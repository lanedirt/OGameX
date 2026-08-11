function outlawWarning(order, galaxy, system, planet, planettype, shipCount, callbackFunction) {
  if (typeof callbackFunction != "function") {
    if (order == constants.espionage) {
      callbackFunction = sendEspionageProbes;
    } else if (order == constants.missleattack) {
      callbackFunction = openMissleLaunchBox;
    }
  }

  if (showOutlawWarning) {
    errorBoxDecision(
      LocalizationStrings.attention,
      LocalizationStrings.outlawWarning,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      callbackFunction,
    );
  } else {
    callbackFunction();
  }

  function sendEspionageProbes() {
    sendShips(order, galaxy, system, planet, planettype, shipCount);
  }

  function openMissleLaunchBox() {
    openOverlay(
      missleAttackLink + "&galaxy=" + galaxy + "&system=" + system + "&position=" + planet + "&type=" + planettype,
      {
        modal: true,
        title: loca.LOCA_FLEET_MISSILEATTACK || "Missile Attack",
      },
    );
  }
}
