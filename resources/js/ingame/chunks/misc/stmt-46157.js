$(function () {
  var sheet = (function () {
    var style = document.createElement("style"); // WebKit hack

    style.appendChild(document.createTextNode(""));
    document.head.appendChild(style);
    return style.sheet;
  })();

  $(document).on("click", ".technology .icon button.upgrade", function (event) {
    event.preventDefault();
    event.stopPropagation();
    let isSpaceProvider = $(this).data("is-spaceprovider") == 1;
    let technologyId = $(this).data("technology");
    let showLifeformBonusCapReached = $(this).data("lifeform-bonus-cap-reached") > 0;

    if (planetMoveInProgress) {
      return errorBoxDecision(
        LocalizationStrings.attention,
        LocalizationStrings.planetMoveBreakUpWarning,
        LocalizationStrings.yes,
        LocalizationStrings.no,
        function () {
          buildListActionBuild(technologyId);
        },
      );
    }

    if (lastBuildingSlot.shouldWarnForTechnologyId(technologyId) && !isSpaceProvider) {
      return errorBoxDecision(
        LocalizationStrings.notice,
        lastBuildingSlot.slotWarning,
        LocalizationStrings.yes,
        LocalizationStrings.no,
        function () {
          buildListActionBuild(technologyId);
        },
      );
    }

    if (showLifeformBonusCapReached) {
      return errorBoxDecision(
        LocalizationStrings.attention,
        loca.LOCA_LIFEFORM_BONUS_CAP_REACHED_WARNING,
        LocalizationStrings.yes,
        LocalizationStrings.no,
        function () {
          buildListActionBuild(technologyId);
        },
      );
    }

    buildListActionBuild(technologyId);
  });
  $(document).on("click", ".technology .icon button.buildmulti", function (event) {
    event.preventDefault();
    event.stopPropagation();
    let isSpaceProvider = $(this).data("is-spaceprovider") == 1;
    let technologyId = $(this).data("technology");
    let showLifeformBonusCapReached = $(this).data("lifeform-bonus-cap-reached") > 0;

    if (planetMoveInProgress) {
      return errorBoxDecision(
        LocalizationStrings.attention,
        LocalizationStrings.planetMoveBreakUpWarning,
        LocalizationStrings.yes,
        LocalizationStrings.no,
        function () {
          buildListActionBuild(technologyId, 1, 4);
        },
      );
    }

    if (lastBuildingSlot.shouldWarnForTechnologyId(technologyId) && !isSpaceProvider) {
      return errorBoxDecision(
        LocalizationStrings.notice,
        lastBuildingSlot.slotWarning,
        LocalizationStrings.yes,
        LocalizationStrings.no,
        function () {
          buildListActionBuild(technologyId, 1, 4);
        },
      );
    }

    if (showLifeformBonusCapReached) {
      return errorBoxDecision(
        LocalizationStrings.attention,
        loca.LOCA_LIFEFORM_BONUS_CAP_REACHED_WARNING,
        LocalizationStrings.yes,
        LocalizationStrings.no,
        function () {
          buildListActionBuild(technologyId, 1, 4);
        },
      );
    }

    buildListActionBuild(technologyId, 1, 4);
  }); // var active = $('.technology[data-status="active"]');
  // if (active.length > 0) {
  //     setInterval(function() {
  //         active.each(function() {
  //             var $this = $(this);
  //             $this.attr("data-progress",
  //                 Math.round(
  //                     (1 - (
  //                         ($this.data("end") - Math.floor((Date.now() + window.timeDiff + window.timeZoneDiffSeconds * 1000) / 1000))
  //                         / ($this.data("end") - $this.data("start"))
  //                     )) * 100
  //                 )
  //             );
  //
  //             let targetAmount = $('.targetamount').attr('data-value');
  //             let amountHolder = $this.find('.amount');
  //             amountHolder.text(parseInt(targetAmount) - parseInt($('#shipSumCount7').text()));
  //         });
  //     }, 1000);
  // }
});
