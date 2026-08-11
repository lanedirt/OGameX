function sendBuildRequest(url, ev, showSlotWarning) {
  if (ev != undefined) {
    var keyCode;

    if (window.event) {
      keyCode = window.event.keyCode;
    } else if (ev) {
      keyCode = ev.which;
    } else {
      return true;
    }

    if (keyCode != 13) {
      return true;
    }
  }

  function build() {
    if (url == null) {
      //sendForm();
    } else {
      fastBuild();
    }
  }

  if (url == null) {
    //fallBackFunc = sendForm;
  } else {
    fallBackFunc = build;
    buildUrl = url;
  }

  if (planetMoveInProgress) {
    errorBoxDecision(
      LOCA_ALL_NETWORK_ATTENTION,
      LOCA_PLANETMOVE_BREAKUP_WARNING,
      LOCA_ALL_YES,
      LOCA_ALL_NO,
      fallBackFunc,
    );
  } else {
    if (showSlotWarning) {
      if (
        lastBuildingSlot["showWarning"] &&
        lastBuildingSlot["shouldWarnForTechnologyId"](typeof techID !== "undefined" ? techID : null)
      ) {
        errorBoxDecision(LOCA_ALL_NETWORK_ATTENTION, lastBuildingSlot["slotWarning"], LOCA_ALL_YES, LOCA_ALL_NO, build);
      } else {
        build();
      }
    } else {
      build();
    }
  }

  return false;
}
