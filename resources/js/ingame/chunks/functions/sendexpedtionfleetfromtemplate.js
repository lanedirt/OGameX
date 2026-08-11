function sendExpedtionFleetFromTemplate() {
  let selectedExpedtionFleetTemplateId = getValue($("#expeditionFleetTemplateSelect").val());

  if (!selectedExpedtionFleetTemplateId) {
    return;
  }

  let expeditionFleetTemplate = expeditionFleetTemplates.find(
    (template) => template.id === selectedExpedtionFleetTemplateId,
  );

  if (!expeditionFleetTemplate) {
    return;
  }

  let ships = expeditionFleetTemplate.ships;
  let additionalParams = {
    speed: expeditionFleetTemplate.fleetSpeed / 10,
    holdingtime: expeditionFleetTemplate.expeditionTime,
  };
  Object.keys(ships).forEach(function (shipId) {
    additionalParams["am" + shipId] = ships[shipId];
  });
  sendShips(missionExpedition, galaxy, system, expeditionPosition, spaceObjectTypePlanet, 0, additionalParams);
}
