function reallySubmitBuyRequest($btn, userInputAmounts) {
  let costs = $btn.data("premiumCosts"),
    itemuuid = $btn.data("itemuuid");
  $.ajax({
    url: buyResourcesLink,
    data: {
      itemUuid: itemuuid,
      costs: costs,
      _token: token,
      userInputAmounts: userInputAmounts,
    },
    type: "POST",
    dataType: "json",
    success: function (dataFromBuy) {
      token = dataFromBuy.newAjaxToken;

      if (dataFromBuy.status === "failure") {
        let error = dataFromBuy.errors[0] || undefined;

        if (error && error.message) {
          fadeBox(error.message, true);
        } else {
          fadeBox(loca["error"], true);
        }

        return;
      } else {
        window.location.reload();
      }
    },
    error: function () {},
  });
}
