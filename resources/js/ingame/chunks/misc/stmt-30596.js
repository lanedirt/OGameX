$(document).on("ajaxShowElement", function (event, tid) {
  if (tid == TECHID_REPAIR_DOCK) {
    registerBurnUpCountDown("#burnUpCountDownForStationScreen");
    registerRepairTimeCountDown("#repairTimeCountDownForStationScreen");
  }
});
