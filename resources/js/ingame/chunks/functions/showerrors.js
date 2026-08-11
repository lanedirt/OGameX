function showErrors() {
  var options = {
    allYes: loca.allYes,
    allNo: loca.allNo,
    allOk: loca.allOk,
  };

  if (isBuildlistNeeded) {
    if (!hasCommander && !(isShip || isRocket)) {
      drawErrorbox("decision", loca.infoBuildlist, loca.allError, options, links.decisionCommander, "build-it_premium");
      return 1;
    }

    if (isRocketAndStorageNotFree) {
      drawErrorbox("notify", loca.noRocketsiloCapacity, loca.allError, options, links.notify);
      return 1;
    }
  } else {
    if (error !== null && error !== 0) {
      if (premiumerror) {
        if (showErrorOnPremiumbutton) {
          drawErrorbox("decision", errorlist[error], loca.allError, options, links[error], buttonClass);
          return 1;
        } else {
          drawErrorbox("decision", errorlist[error], loca.allError, options, links[error]);
          return 1;
        }
      } else if (isRocketAndStorageNotFree) {
        drawErrorbox("notify", loca.noRocketsiloCapacity, loca.allError, options, links.notify);
        return 1;
      } else if (isBusy) {
        return 1;
      } else {
        drawErrorbox("notify", errorlist[error], loca.allError, options, "");
        return 1;
      }
    }
  }

  return 0;
}
