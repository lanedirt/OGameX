function ajaxFormSubmit(form, url, okFunction, additionalParams) {
  var params = $("#" + form + "").serialize();

  if (typeof additionalParams === "object") {
    for (var key in additionalParams) {
      if (!additionalParams.hasOwnProperty(key)) {
        continue;
      }

      params += "&" + key + "=" + additionalParams[key];
    }
  }

  var successFunction = null;

  if (okFunction != null && typeof okFunction == "function") {
    successFunction = okFunction;
  }

  $.ajax({
    type: "POST",
    url: url,
    data: params,
    success: successFunction,
  });
}
