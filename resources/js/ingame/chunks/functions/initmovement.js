function initMovement() {
  initToggleHeader("movement");
  preCloseMovements();

  if (showInfos == undefined) {
    var showInfos = 0;
  }

  $("a.openCloseDetails").click(function () {
    openCloseDetails($(this).attr("data-mission-id"), $(this).attr("data-end-time"));
  });
  $(".closeAll").click(function () {
    if (showInfos == 0) {
      showInfos = 1;
      $(".closeAll").children().removeClass("all_open").addClass("all_closed");
    } else {
      showInfos = 0;
      $(".closeAll").children().removeClass("all_closed").addClass("all_open");
    }

    $("a.openCloseDetails").each(function () {
      if (showInfos === 1) {
        closeDetails($(this).attr("data-mission-id"), $(this).attr("data-end-time"));
      } else if (showInfos === 0) {
        openDetails($(this).attr("data-mission-id"), $(this).attr("data-end-time"));
      }
    });
  });
  timerHandler.appendCallback(function () {});
}
