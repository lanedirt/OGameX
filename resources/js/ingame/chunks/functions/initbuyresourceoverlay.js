function initBuyResourceOverlay(isLastBuildingSlot, showLastBuildingSlotWarning, localization) {
  $(".close_buyResourceOverlay").on("click", function () {
    $("#buyResourceOverlayBody").closest(".ui-dialog").find(".ui-icon-closethick").click();
  });

  var sendStuff = function () {
    if (isLastBuildingSlot && showLastBuildingSlotWarning) {
      errorBoxDecision(
        localization.allNetworkAttention,
        localization.slotWarning,
        localization.allYes,
        localization.allNo,
        sendDMAcceptanceForm,
      );
    } else {
      sendDMAcceptanceForm();
    }
  };

  $("#premiumConfirmButton").on("click", function (event) {
    event.preventDefault();
    sendStuff();
  });
  $(document).on("ajaxShowOverlay", function (event) {
    $("#premiumConfirmButton").focus();
  });
  $("#premiumConfirmButton").on("keyup", function (event) {
    event.stopPropagation();

    if (event.keyCode == 13) {
      sendStuff();
    }
  });
}
