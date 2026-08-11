function displayBurnUpEnquiry() {
  var button = $("input.burnUpButton");
  errorBoxDecision(
    button.data("loca_box_text"),
    button.data("loca_decision_text"),
    button.data("loca_yes"),
    button.data("loca_no"),
    function () {
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
    },
    function () {},
  );
}
