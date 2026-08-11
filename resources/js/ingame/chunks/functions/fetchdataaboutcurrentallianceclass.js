function fetchDataAboutCurrentAllianceClass(newClassName, upgradeItemAjax, questionType, price) {
  if (!activatingItem) {
    activatingItem = true;
    $.ajax({
      url: inventoryObj.ingameUrl,
      type: "GET",
      data: {
        component: "allianceclassselection",
        action: "fetchDataAboutCurrentAllianceClass",
        ajax: 1,
        asJson: 1,
      },
      dataType: "json",
      error: function (error) {
        promptUserForAllianceClassChange(newClassName, upgradeItemAjax, questionType, price);
      },
      success: function (data) {
        promptUserForAllianceClassChange(newClassName, upgradeItemAjax, questionType, price, data);
      },
    });
  }
}
