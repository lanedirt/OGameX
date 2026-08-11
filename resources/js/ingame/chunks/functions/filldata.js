function fillData(fleetSection, jsonObj, isBaseDefender, attackType) {
  changeClass(fleetSection.find("characterclass-icon"), characterClassArr[0], "characterclass");
  changeClass(fleetSection.find("allianceclass-icon"), allianceClassArr[0], "allianceclass");
  let inputField;
  $.each(jsonObj.researches, function (index, value) {
    inputField = $(fleetSection.find(".technology-row input[name='amount[" + index + "]']")[0]);
    inputField.val(value);
  });
  let selection = 0;

  if (characterClassArr[parseInt(jsonObj.characterClassId)]) {
    selection = parseInt(jsonObj.characterClassId);
  }

  changeClass(fleetSection.find("characterclass-icon"), characterClassArr[selection], "characterclass");

  if (allianceClassArr[parseInt(jsonObj.allianceClassId)]) {
    selection = parseInt(jsonObj.allianceClassId);
  }

  changeClass(fleetSection.find("allianceclass-icon"), allianceClassArr[selection], "allianceclass");
  $.each(jsonObj.ships, function (index, value) {
    inputField = $(fleetSection.find(".technology-row input[name='amount[" + index + "]']")[0]);
    inputField.val(value.amount);
    inputField = $(fleetSection.find(".technology-fullrow input[name='weapon[" + index + "]']")[0]);
    inputField.val((Math.floor(value.weapon * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='shield[" + index + "]']")[0]);
    inputField.val((Math.floor(value.shield * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='armor[" + index + "]']")[0]);
    inputField.val((Math.floor(value.armor * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='cargo[" + index + "]']")[0]);
    inputField.val((Math.floor(value.cargo * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='speed[" + index + "]']")[0]);
    inputField.val((Math.floor(value.speed * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='fuel[" + index + "]']")[0]);
    inputField.val((Math.floor(value.fuel * 10000) / 100).toFixed(2));
  });
  inputField = $(fleetSection.find(".technology-fullrow input[name='classBonus[1]']")[0]);
  inputField.val((Math.floor(jsonObj.bonuses.characterClassBooster[1] * 10000) / 100).toFixed(2));
  inputField = $(fleetSection.find(".technology-fullrow input[name='classBonus[2]']")[0]);
  inputField.val((Math.floor(jsonObj.bonuses.characterClassBooster[2] * 10000) / 100).toFixed(2));
  inputField = $(fleetSection.find(".technology-fullrow input[name='classBonus[3]']")[0]);
  inputField.val((Math.floor(jsonObj.bonuses.characterClassBooster[3] * 10000) / 100).toFixed(2));
  inputField = $(fleetSection.find("fleetspeed-section input[value='" + jsonObj.fleetspeed + "']")[0]);
  inputField.prop("checked", true);
  let coords = {};

  if (typeof jsonObj.coords == "object") {
    coords[0] = jsonObj.coords.galaxy;
    coords[1] = jsonObj.coords.system;
    coords[2] = jsonObj.coords.position;
  } else {
    coords = jsonObj.coords.split(":");
  }

  $(fleetSection.find("coordinates-section input[name='galaxy']")[0]).val(coords[0]);
  $(fleetSection.find("coordinates-section input[name='system']")[0]).val(coords[1]);
  $(fleetSection.find("coordinates-section input[name='position']")[0]).val(coords[2]);

  if (isBaseDefender === true) {
    fleetSection = $("combatsim-section[base-defender] fleet-content");
    $.each(jsonObj.resources, function (index, value) {
      inputField = $(fleetSection.find(".resource-row input[name='resource[" + index + "]']")[0]);
      inputField.val(value);
    });
    $.each(jsonObj.ships, function (index, value) {
      inputField = $(fleetSection.find(".technology-row input[name='amount[" + index + "]']")[0]);
      inputField.val(value.amount);
    });
    $.each(jsonObj.defenses, function (index, value) {
      inputField = $(fleetSection.find(".technology-row input[name='amount[" + index + "]']")[0]);
      inputField.val(value.amount);
      inputField = $(fleetSection.find(".technology-fullrow input[name='weapon[" + index + "]']")[0]);
      inputField.val((Math.floor(value.weapon * 10000) / 100).toFixed(2));
      inputField = $(fleetSection.find(".technology-fullrow input[name='shield[" + index + "]']")[0]);
      inputField.val((Math.floor(value.shield * 10000) / 100).toFixed(2));
      inputField = $(fleetSection.find(".technology-fullrow input[name='armor[" + index + "]']")[0]);
      inputField.val((Math.floor(value.armor * 10000) / 100).toFixed(2));
    });
    inputField = $(fleetSection.find(".technology-fullrow input[name='special[11112]']")[0]);
    inputField.val((Math.floor(jsonObj.bonuses.lifeformProtection * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='special[12112]']")[0]);
    inputField.val((Math.floor(jsonObj.bonuses.spaceDockExtender * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='special[13112]']")[0]);
    inputField.val((Math.floor(jsonObj.bonuses.recycleAttackerFleet * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='special[14112]']")[0]);
    inputField.val((Math.floor(jsonObj.bonuses.moonChanceIncrease * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='denCapacity[22]']")[0]);
    inputField.val((Math.floor(jsonObj.bonuses.denCapacity.metal * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='denCapacity[23]']")[0]);
    inputField.val((Math.floor(jsonObj.bonuses.denCapacity.crystal * 10000) / 100).toFixed(2));
    inputField = $(fleetSection.find(".technology-fullrow input[name='denCapacity[24]']")[0]);
    inputField.val((Math.floor(jsonObj.bonuses.denCapacity.deuterium * 10000) / 100).toFixed(2));
    $.each(jsonObj.missiles, function (index, value) {
      inputField = $(fleetSection.find(".technology-row input[name='amount[" + index + "]']")[0]);
      inputField.val(value.amount);
    });
  }

  simChanged(fleetSection);
}
