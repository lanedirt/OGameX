function reCommissionShips() {
  var button = $("input.reCommissionButton");
  $.ajax({
    url: button.data("url"),
    success: function (result) {
      var decoded = jQuery.parseJSON(result);

      if (decoded.success) {
        fadeBox(decoded.reason, false, function () {});
        redirectSpaceDock();
      } else {
        fadeBox(decoded.reason, true, function () {});
      }
    },
  });
}
