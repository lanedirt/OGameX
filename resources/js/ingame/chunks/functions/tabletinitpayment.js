function tabletInitPayment() {
  if (!isMobile) {
    return false;
  }

  $("#payment").parent(".overlayDiv").dialog("option", "title", paymentLoca.title);
  document.addEventListener(
    "deviceready",
    function () {
      $("#mobilePayment a.js_buyPacket")
        .unbind("click")
        .bind("click", function () {
          HostApp.StartPayment($(this).attr("ref"), userData.id, constants.name, constants.language);
        });

      HostApp.OnPaymentFinished = function () {
        getAjaxResourcebox(function (resources) {
          fadeBox(paymentLoca.success, false);
          $("#payment").parent().dialog("close");
          $("#planet #content .level span")
            .attr("class", "undermark")
            .text(gfNumberGetHumanReadable(resources.darkmatter.amount, isMobile));
        });
      };

      HostApp.OnPaymentFailed = function () {
        fadeBox(paymentLoca.error, true);
      };
    },
    false,
  );
}
