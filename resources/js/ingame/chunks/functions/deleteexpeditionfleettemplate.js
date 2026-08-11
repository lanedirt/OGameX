function deleteExpeditionFleetTemplate(id) {
  if (editingTemplate) {
    return;
  }

  editingTemplate = true;
  $.ajax({
    url: fleetTemplateUrl,
    type: "POST",
    data: {
      action: "deleteExpeditionTemplate",
      expeditionFleetTemplateId: id,
      _token: token,
    },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        showNotification(response.message, "success");

        if ($('div.ui-dialog[aria-describedby="expeditionFleetTemplatesEdit"]').length) {
          $("#expeditionFleetTemplateForm").parents(".ui-dialog").find(".ui-dialog-titlebar-close").click();
          $('div.ui-dialog[aria-describedby="expeditionFleetTemplatesEdit"]').remove();
        }

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
