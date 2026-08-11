function galaxyCheckTarget(expeditionFleetTemplateId, selectedGalaxy, selectedSystem) {
  if (!selectedGalaxy) {
    return;
  }

  if (!selectedSystem) {
    return;
  }

  let expeditionFleetTemplate = expeditionFleetTemplates.find((template) => template.id === expeditionFleetTemplateId);

  if (!expeditionFleetTemplate) {
    return;
  }

  if (checkingTarget) {
    return;
  }

  checkingTarget = true;
  let params = {
    galaxy: selectedGalaxy,
    system: selectedSystem,
    position: expeditionPosition,
    type: spaceObjectTypePlanet,
    mission: missionExpedition,
    speed: expeditionFleetTemplate.fleetSpeed,
    _token: token,
  };
  let ships = expeditionFleetTemplate.ships;
  Object.keys(ships).forEach(function (shipId) {
    params["am" + shipId] = ships[shipId];
  });
  $.ajax({
    url: checkTargetUrl,
    type: "POST",
    dataType: "json",
    data: params,
    success: function (response) {
      if (
        selectedGalaxy === galaxy &&
        selectedSystem === system &&
        expeditionFleetTemplateId === getValue($("#expeditionFleetTemplateSelect").val())
      ) {
        if (response.targetOk) {
          $("#sendExpeditionFleetTemplateFleet").removeAttr("disabled");
        } else {
          let responseError = response.errors[0];
          showNotification(responseError ? responseError.message : undefined, "error");
        }
      }

      token = response.newAjaxToken;
      checkingTarget = false;
    },
    error: function (e) {
      showNotification(undefined, "error");
      checkingTarget = false;
    },
  });
}
