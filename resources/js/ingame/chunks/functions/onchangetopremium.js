function onChangeToPremium(e) {
  var $btn = $(e.currentTarget).find(".btn_blue"),
    $fillup = $btn.closest(".fillup"),
    $premiumBar = $btn.closest(".fill_resource_ctn").find(".premium_bar"); // reset

  $(".fillup")
    .removeClass("premium")
    .parent()
    .find(".current_stock span")
    .removeClass("premium_txt")
    .each(function () {
      // color of the current amount of the selected resource
      var $this = $(this);
      $this.text($this.data("currentAmount")); // reset stock text to current amount
    });
  $(".fill_resource .btn_premium").html(loca.fillUpResource).attr("class", "btn_blue");
  $(".premium_bar").css("width", "0%").data("premiumPercent", 0); // do not highlight disabled buttons

  if ($btn.attr("disabled") === "disabled") {
    return;
  }

  updateBuyTextAndActivatePackage($btn, $fillup);
  $fillup
    .parent()
    .find(".current_stock span")
    .addClass("premium_txt") // color of the current amount of the selected resource
    .text($btn.data("newValueFormatted")); // set stock text to the amount the player will have after buying the package

  $premiumBar.data("premiumPercent", $btn.data("premiumPercent"));
  changeTooltip($premiumBar, "+" + tsdpkt(Math.floor($btn.data("premiumValue"))));
  refreshBars("bar_container", "filllevel_bar", "premium_bar");
}
