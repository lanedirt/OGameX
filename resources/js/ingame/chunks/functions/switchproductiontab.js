function switchProductionTab(tab) {
  $("#productionqueuecomponent .spaceObjectTab").addClass("inactive");
  $(`#productionqueuecomponent .spaceObjectTab.${tab}`).removeClass("inactive");
  let targetQueues = tab === "planet" ? "moonProduction" : "planetProduction";
  $(`#productionqueuecomponent .${targetQueues}`).hide();
  $(`#productionqueuecomponent .${tab}Production`).show();
}
