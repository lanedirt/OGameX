function initExpeditionFleetTemplate() {
  $(".list tr:even").addClass("alt");
  $("#expeditionFleetTemplateResetForm").on("click", function (event) {
    event.preventDefault();
    resetExpedtionFleetTemplateForm();
  });
}
