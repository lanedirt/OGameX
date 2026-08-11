function initIndex() {
  initConnectionErrorFunction();
  timerHandler.appendCallback(function () {
    localTime = new Date();
    serverTime = new Date(localTime.valueOf() + timeDiff);
    $(".OGameClock").html(getFormatedDate(serverTime.getTime(), "[d].[m].[Y] <span>[H]:[i]:[s]</span>"));
  });
  $("select").ogameDropDown();
  $("#planet .slot").hover(
    function () {
      $(this).addClass("slot-hover");
    },
    function () {
      $(this).removeClass("slot-hover");
    },
  );
  $("#eventboxFilled").hover(
    function () {
      $(this).addClass("eventboxHover");
    },
    function () {
      $(this).removeClass("qeventboxHover");
    },
  );
  $(document)
    .undelegate("a.build-faster", "click")
    .delegate("a.build-faster", "click", function () {
      var $thisObj = $(this);

      if (darkMatter < getFastBuildPrice($thisObj)) {
        errorBoxDecision(
          LocalizationStrings.error,
          LocalizationStrings.errorNotEnoughDM,
          LocalizationStrings.yes,
          LocalizationStrings.no,
          redirectPremium,
        );
        return;
      }

      if (speedingUpBuildListEntry) {
        return;
      }

      speedingUpBuildListEntry = true;
      var referrerPage = $.deparam.querystring().page;
      errorBoxDecision(
        LocalizationStrings.notice,
        getOverlayText($thisObj),
        LocalizationStrings.yes,
        LocalizationStrings.no,
        function () {
          $.ajax({
            url: $thisObj.attr("rel"),
            data: {
              ajax: 1,
              _token: token,
              referrerPage: referrerPage,
            },
            type: "POST",
            dataType: "json",
            error: function () {
              fadeBox(LocalizationStrings["error"], true);
              $thisObj.addClass("disabled");
              speedingUpBuildListEntry = false;
            },
            success: function (data) {
              token = data.newAjaxToken;

              if (data.error) {
                fadeBox(data.message, true);
                $thisObj.addClass("disabled");
                speedingUpBuildListEntry = false;
              } else {
                location.href = getRedirectLink();
              }
            },
          });
          return false;
        },
        function () {
          speedingUpBuildListEntry = false;
        },
      );
    }) // detail slides
    .undelegate(".slideIn", "click")
    .delegate(".slideIn", "click", function () {
      $(".slideIn").removeClass("active");
      var id = $(this).attr("ref");
      $("a[ref='" + id + "']").addClass("active");
      Tipped.hideAll();
      $("html, body").animate(
        {
          scrollTop: 0,
        },
        500,
      );
      gfSlider.slideIn(getElementByIdWithCache("detail"), id);
    })
    .undelegate("a.close_details", "click")
    .delegate("a.close_details", "click", function () {
      if (window.gfSlider !== undefined) {
        gfSlider.hide(getElementByIdWithCache("detail"));
      }
    });
  $("#banner_skyscraper a.close_details").click(function () {
    changeSetting("hideBanner", $(this).attr("ref"), function () {
      $("#banner_skyscraper").remove();
    });
  });
  var wreckfield = $("#wreckFieldCountDown");

  if (wreckfield) {
    new simpleCountdown(wreckfield, wreckfield.data("duration"), null);
  }

  initHideElements();
  initOverlays();
  initThousandSeparator();
  initTooltips();
  initPlanetSorting(); // for Tablets only

  initRetinaImages(); //just during the birthday events

  initBDayEventHints();
}
