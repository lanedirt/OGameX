function callTrader(e) {
  if ($(e.currentTarget).attr("disabled") == "disabled") {
    return;
  }

  var id = $(e.currentTarget).data("offerId"),
    askOverwrite = $(e.currentTarget).data("askOverwrite");

  if (typeof askOverwrite == "undefined") {
    askOverwrite = true;
  }

  if (darkMatter < traderCosts) {
    errorBoxDecision(
      LocalizationStrings.error,
      loca.errorNotEnoughDM,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      redirectBuyPremium,
    );
    return;
  }

  function newTrader() {
    if (!$(".call_trader_box .getNewTraderDiv").hasClass("hidden")) {
      $(".call_trader_box").children().toggleClass("hidden");
    }

    $(".resource_list .resource_link")
      .removeClass("oldTraderActive")
      .filter(function (index) {
        return $(this).data("resourceId") == id;
      })
      .addClass("oldTraderActive"); // remove class from all resources, add to active trader

    $.post(
      traderCallLink,
      {
        offer_id: id,
        _token: token,
      },
      function (data) {
        data = $.parseJSON(data);
        token = data.newAjaxToken;

        if (data["status"] == "1") {
          $("#callTrader").show().addClass("traderActive");
          traderObj.reloadResources();
          $("#callTrader").addClass("traderActive").show();
          openOverlay(traderOverlayLink, {
            class: "traderlayer",
          });
          var $activeTrader = $("#activeTrader");
          var resourceName = "metal";
          var headline = loca.traderResourceTitleMetal;

          switch (id) {
            case 2:
              resourceName = "crystal";
              headline = loca.traderResourceTitleCrystal;
              break;

            case 3:
              resourceName = "deut";
              headline = loca.traderResourceTitleDeuterium;
              break;
          }

          $activeTrader.find(".left_content #material").attr("class", resourceName);
          $activeTrader.find("p.stimulus").html(headline);
          $activeTrader.show();
          $("#boxHeader, #boxFooter").show();
        } else {
          errorBoxAsArray(data["errorbox"]);
        }
      },
    );
  }

  if (askOverwrite && $("#callTrader").is(":visible")) {
    errorBoxDecision(
      loca.traderResourceNewQuestionHeadline,
      loca.traderResourceNewQuestion,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      newTrader,
    );
  } else {
    newTrader();
  }
}
