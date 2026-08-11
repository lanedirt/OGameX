function reinitializeExpeditionFleetTemplateOGameDropdown() {
  $("#expeditionFleetTemplateForm select").ogameDropDown("destroy");
  $("#expeditionFleetTemplateForm span.dropdown.currentlySelected").remove();
  $("#expeditionFleetTemplateForm select").ogameDropDown();
}
