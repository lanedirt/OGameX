function changeSetting(key, value, success, error, showError) {
  $.ajax({
    type: "POST",
    url: changeSettingsLink,
    dataType: "json",
    data: {
      _token: changeSettingsToken,
      key: key,
      value: value,
    },
    success: function (data) {
      changeSettingsToken = data.newToken;

      if (data.message.length > 0) {
        fadeBox(data.message, data.error);
      }

      if (!data.error && typeof success == "function") {
        success();
      } else if (data.error && typeof error == "function") {
        error();
      }
    },
    error: function (data) {
      if (typeof showError == "undefined" || showError) {
        fadeBox(LocalizationStrings["error"], true);
      }

      if (data.error && typeof error == "function") {
        error();
      }
    },
  });
}
