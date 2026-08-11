function saveExpeditionFleetTemplate(event) {
  event.preventDefault();

  if (editingTemplate) {
    return;
  }

  editingTemplate = true;
  let expeditionFleetTemplateData = $("#expeditionFleetTemplateForm").serialize();
  expeditionFleetTemplateData += `&token=${token}`;
  expeditionFleetTemplateData += `&action=saveExpeditionTemplate`;
  $.ajax({
    url: fleetTemplateUrl,
    type: "POST",
    data: expeditionFleetTemplateData,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        showNotification(response.message, "success");
        $("#expeditionFleetTemplateForm").parents(".ui-dialog").find(".ui-dialog-titlebar-close").click();
        $('div.ui-dialog[aria-describedby="expeditionFleetTemplatesEdit"]').remove();
        updateExpeditionFleetTemplates(response.expeditionFleetTemplates);
        reloadComponent("expeditionfleettemplate");
      } else {
        showNotification(response.errors[0].message, "error");
      }

      token = response.newAjaxToken;
      editingTemplate = false;
    },
    error: function (e) {
      window.location.reload();
    },
  });
}
