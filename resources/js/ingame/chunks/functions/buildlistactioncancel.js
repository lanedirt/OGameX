function buildListActionCancel(technologyId, listId, planetId) {
  if (typeof cancelBuildListEntryUrl === "undefined") {
    return;
  }

  if (buildListActionCalled) {
    return;
  }

  buildListActionCalled = true;
  let body = {
    technologyId: technologyId,
    listId: listId,
    _token: token,
  };

  if (planetId) {
    body["planetId"] = planetId;
  }

  $.ajax({
    url: cancelBuildListEntryUrl,
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
