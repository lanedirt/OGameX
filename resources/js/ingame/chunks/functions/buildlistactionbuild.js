function buildListActionBuild(technologyId, amount, mode, buyWithDmAmount, planetId) {
  if (typeof scheduleBuildListEntryUrl === "undefined") {
    return;
  }

  if (buildListActionCalled) {
    return;
  }

  buildListActionCalled = true;
  let body = {
    technologyId: technologyId,
    amount: amount ? amount : 1,
    mode: mode ? mode : 1,
    _token: token,
  };

  if ($(".shipyardSelection .radioShipyardSelection").length !== 0) {
    body["selectedShipyard"] = parseInt($(".shipyardSelection .radioShipyardSelection:checked").val());
  }

  if (buyWithDmAmount) {
    body["buyWithDmAmount"] = buyWithDmAmount;
  }

  if (planetId) {
    body["planetId"] = planetId;
  }

  $.ajax({
    url: scheduleBuildListEntryUrl,
    data: body,
    type: "POST",
    dataType: "json",
    success: function (json) {
      if (json.status === "success") {
        if (json.message) {
          fadeBox(json.message);
        }

        window.location.reload();
      } else {
        token = json.newAjaxToken;

        if (json.errors && json.errors.length) {
          fadeBox(json.errors[0].message, true);
        }

        buildListActionCalled = false;
      }
    },
    error: function () {
      if (typeof LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN !== "undefined" && LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN) {
        fadeBox(LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN, true);
      }

      buildListActionCalled = false;
    },
  });
}
