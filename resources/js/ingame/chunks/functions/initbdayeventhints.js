function initBDayEventHints() {
  $(document)
    .undelegate(".event_build_faster, .event_active_hint", "click")
    .delegate(".event_build_faster, .event_active_hint", "click", function (e) {
      e.stopPropagation();

      if ($(this).parent().attr("id") === "expeditionbutton") {
        doExpedition();
      } else {
        $(this).siblings(".detail_button").click();
      }
    });
}
