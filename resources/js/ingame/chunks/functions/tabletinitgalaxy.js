function tabletInitGalaxy() {
  if (!isMobile) {
    return false;
  }

  $("#galaxyContent").wipetouch({
    wipeLeft: function (e) {
      if (!$("#galaxyLoading:visible").length) {
        submitOnKey("ArrowLeft");
      }
    },
    wipeRight: function (e) {
      if (!$("#galaxyLoading:visible").length) {
        submitOnKey("ArrowRight");
      }
    },
    preventDefault: false,
    preventDefaultWhenTriggering: true,
    moveX: 180,
    moveY: 60,
  });
}
