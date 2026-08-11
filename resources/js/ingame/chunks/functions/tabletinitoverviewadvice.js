/*
 * author: Stefanie Knoth
 * description: scripts that are only needed for tablet here
 * date: 04:07:2012
 */
function tabletInitOverviewAdvice() {
  if (!isMobile) {
    return false;
  }

  var $adviceWrapper = $(".adviceWrapper");
  var $exodus = $adviceWrapper.find("#exodus-indicator, #exodus-timer");
  var $exodusProcessed = $adviceWrapper.find("#exodus-indicator-processed");
  $adviceWrapper.prev().before($exodus);
  $adviceWrapper.prev().before($exodusProcessed);
  $("#planetdata").after($adviceWrapper);
}
