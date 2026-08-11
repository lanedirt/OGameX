function selectShipsPerFleet(templateId) {
  $("#expeditionFleetTemplateSelect").ogameDropDown("destroy");
  $("#expeditionFleetTemplateSelect").ogameDropDown();
  $("#expeditionFleetTemplateSelect").ogameDropDown("select", templateId);
  $("#expeditionFleetTemplateSelect").val(templateId).trigger("change");
  $("#expeditionFleetTemplates").parents(".ui-dialog").find(".ui-dialog-titlebar-close").click();
}
