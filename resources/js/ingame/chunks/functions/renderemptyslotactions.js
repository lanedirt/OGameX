function renderEmptySlotActions(galaxyContentObject, systemData) {
  $("#galaxyRow" + galaxyContentObject.position + " .cellAction").html(
    `${getEmptySlotActions(galaxyContentObject, systemData)}`,
  );
}
