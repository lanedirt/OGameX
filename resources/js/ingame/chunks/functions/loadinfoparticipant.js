function loadInfoParticipant(obj) {
  let loadDataSection = $(obj).closest("div.load_data");
  let inputField = loadDataSection.find("input").first();
  let attackType = inputField.data("attackType");
  let isBaseDefender =
    attackType === 2 &&
    inputField.data("participantId") === $("fleet-content[data-attack-type=2]").first().data("participantId");

  if (isJsonString(inputField.val())) {
    fillData($(obj).closest("fleet-content"), JSON.parse(inputField.val()), isBaseDefender, attackType);
  } else if (isReportString(inputField.val())) {
    loadSpyReport(inputField.val(), $(obj), isBaseDefender, attackType);
  }
}
