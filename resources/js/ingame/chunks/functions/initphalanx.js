function initPhalanx() {
  getAjaxResourcebox();
  $(".eventFleet:odd").addClass("odd");
  $(".partnerInfo:even").addClass("part-even");
  $(".toggleInfos").click(function () {
    id = $(this).attr("rel");

    if ($(this).attr("class") == "toggleInfos infosOpen") {
      $(this).removeClass("infosOpen");
      $(this).addClass("infosClosed");
      $(this).children().attr("src", "/img/icons/de1e5f629d9e47d283488eee0c0ede.gif");
      $("." + id).attr("style", "display: none;");
    } else {
      $(this).addClass("infosOpen");
      $(this).removeClass("infosClosed");
      $(this).children().attr("src", "/img/icons/577565fadab7780b0997a76d0dca9b.gif");
      $("." + id).attr("style", "display: block;");
    }
  });
  var $titleBar = $(".overlayDiv.phalanx").siblings(".ui-dialog-titlebar");

  if ($titleBar.find(".refreshPhalanxLink").length) {
    $("#phalanxWrap .refreshPhalanxLink").remove();
  } else {
    $titleBar.find(".ui-dialog-title").append($("#phalanxWrap .refreshPhalanxLink"));
  }
}
