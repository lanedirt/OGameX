function initPlanetSorting() {
  $("#planetList.sortable").sortable({
    start: function () {
      Tipped.hideAll();
    },
    stop: function () {
      Tipped.hideAll();
      changeSetting("customPlanetOrder", $(this).sortable("toArray"));
    },
  });

  if ($(".lockPlanets").hasClass("closed")) {
    $("#planetList.sortable").sortable("disable");
  }

  $(".lockPlanets")
    .unbind("click")
    .bind("click", function () {
      var $thisObj = $(this);
      changeSetting("planetOrderLocked", $thisObj.hasClass("open") ? 1 : 0, function () {
        var text;

        if ($thisObj.hasClass("open")) {
          $thisObj.removeClass("open").addClass("closed");
          $("#planetList.sortable").sortable("disable");
          text = LocalizationStrings.planetOrder.unlock;
        } else {
          $thisObj.removeClass("closed").addClass("open");
          $("#planetList.sortable").sortable("enable");
          text = LocalizationStrings.planetOrder.lock;
        }

        changeTooltip($thisObj, text);
      });
    });
}
