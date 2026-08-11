function tabletToggleTechtreeInfos(id) {
  if (!isMobile) {
    return false;
  }

  var $techtree = $("div.techtree[data-id='" + id + "']");
  var $techDiv = $techtree.find(".techImage a");
  $techDiv.each(function () {
    var $thisObj = $(this);
    var colorClass = $thisObj.parent().hasClass("built") ? "undermark" : "overmark";
    var techName = $thisObj.data("tech-name");
    var techType = $thisObj.data("tech-type");
    $thisObj.append(
      '<div class="short_info" style="display: none"><span class="' +
        colorClass +
        '">' +
        techName +
        "</span><br/>" +
        techType +
        "</div>",
    );
  });

  if ($techDiv.length) {
    $techtree.append(
      $(
        '<a id="toggleDetails" href="javascript:void(0)" class="btn_blue">' + LocalizationStrings.moreDetails + "</a>",
      ).click(function () {
        var $shortInfo = $techtree.find(".short_info");

        if ($shortInfo.is(":visible")) {
          $(this).text(LocalizationStrings.moreDetails);
        } else {
          $(this).text(LocalizationStrings.lessDetails);
        }

        $shortInfo.toggle();
      }),
    );
  }
}
