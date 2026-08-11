function resetExpedtionFleetTemplateForm() {
  $("#expeditionFleetTemplateForm")[0].reset();
  $("#expeditionFleetTemplateHoldingTimeSelect").val("1").ogameDropDown("select", "1");
  $("#expeditionFleetTemplateSpeedSelect").val("100").ogameDropDown("select", "100");
}
