function updateExpeditionFleetTemplates(newExpeditionFleetTemplates) {
  expeditionFleetTemplates = newExpeditionFleetTemplates;
  $("#expeditionFleetTemplateSelect").ogameDropDown("destroy");
  $("#expeditionFleetTemplateSelect option").each((idx, option) => {
    if (option.value !== "0") {
      $(option).remove();
    }
  });
  newExpeditionFleetTemplates.map((fleetTemplate) => {
    $("#expeditionFleetTemplateSelect").append(`<option value="${fleetTemplate.id}">${fleetTemplate.name}</option>`);
  });
  $("#expeditionFleetTemplateSelect").ogameDropDown();
  $("#expeditionFleetTemplateSelect").val("0").trigger("change");
}
