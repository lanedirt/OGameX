function initExpeditionFleetTemplates() {
  $("#expeditionFleetTemplateSelect").on("change", function (e) {
    let expeditionFleetTemplateId = getValue($("#expeditionFleetTemplateSelect").val());

    if (expeditionFleetTemplateId === 0) {
      $("#expeditionbutton").show();
      $("#sendExpeditionFleetTemplateFleet").hide().attr("disabled", true);
    } else {
      let expeditionFleetTemplate = expeditionFleetTemplates.find(
        (template) => template.id === expeditionFleetTemplateId,
      );

      if (!expeditionFleetTemplate) {
        $("#expeditionbutton").show();
        $("#sendExpeditionFleetTemplateFleet").hide().attr("disabled", true);
      } else {
        $("#expeditionbutton").hide();
        $("#sendExpeditionFleetTemplateFleet").show();
        galaxyCheckTarget(expeditionFleetTemplateId, galaxy, system);
      }
    }
  });
}
