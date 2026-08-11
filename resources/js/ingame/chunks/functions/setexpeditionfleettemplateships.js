function setExpeditionFleetTemplateShips(ships, tempName, templateId, selectedExpeditionTime, selectedSpeed) {
  $("#expeditionFleetTemplateForm")[0].reset();
  $("#expeditionFleetTemplateId").val(templateId);
  $("#expeditionFleetTemplateName").val(tempName);

  for (let shipId in ships) {
    $("#expeditionFleetTemplateShip_" + shipId).val(ships[shipId]);
  }

  $("#expeditionFleetTemplateHoldingTimeSelect")
    .val(selectedExpeditionTime.toString())
    .ogameDropDown("select", selectedExpeditionTime.toString());
  $("#expeditionFleetTemplateSpeedWarning").css({
    display: "none",
  });

  if (allowedSpeedsInExpeditionTemplate.indexOf(selectedSpeed) === -1) {
    $("#expeditionFleetTemplateSpeedWarning").css({
      display: "flex",
    });
  }

  let speedToSelect = allowedSpeedsInExpeditionTemplate.find((speed) => speed >= selectedSpeed);
  $("#expeditionFleetTemplateSpeedSelect")
    .val(speedToSelect.toString())
    .ogameDropDown("select", speedToSelect.toString());
}
