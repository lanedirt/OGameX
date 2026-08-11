function initOverview() {
  $(".cancelMove").click(function () {
    var thisObj = $(this);
    var locationObj = window.location;
    errorBoxDecision(
      planetMoveLoca["askTitle"],
      planetMoveLoca["askCancel"],
      planetMoveLoca["yes"],
      planetMoveLoca["no"],
      function () {
        $.ajax({
          method: "get",
          url: thisObj.attr("rel"),
          dataType: "json",
          cache: false,
          success: function (data) {
            if (data.error.length > 0) {
              fadeBox(data.error, true);
            } else {
              location.href = getRedirectLink();
            }
          },
          error: function () {
            fadeBox(planetMoveLoca["error"], true);
          },
        });
      },
    );
  });
  $(document)
    .undelegate("#planetMaintenanceDelete", "submit")
    .delegate("#planetMaintenanceDelete", "submit", function (e) {
      e.preventDefault();
      ajaxFormSubmit("planetMaintenanceDelete", $(this).attr("action"), planetGivenup);
    })
    .undelegate("#abandonplanet #block", "click")
    .delegate("#abandonplanet #block", "click", function (e) {
      e.preventDefault();

      if (!hasAPassword) {
        var question = $("#giveupHeadline").attr("rel") == 3 ? loca.moonGiveupQuestion : loca.planetGiveupQuestion;
        question = question
          .replace("%planetName%", $("#giveupName").text())
          .replace("%planetCoordinates%", $("#giveupCoordinates").text());
        errorBoxDecision(
          $("#giveupHeadline").text(),
          question,
          LocalizationStrings.yes,
          LocalizationStrings.no,
          function () {
            $("#planetMaintenanceDelete").submit();
          },
        );
      } else {
        show_hide_menus("#validate");
        show_hide_menus("#giveUpNotification");
      }
    })
    .undelegate(".openPlanetRenameGiveupBox", "click")
    .delegate(".openPlanetRenameGiveupBox", "click", function (e) {
      e.stopPropagation();
      openPlanetRenameGiveupBox();
    });
}
