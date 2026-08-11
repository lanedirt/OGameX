function showSpaceObjectSelection(obj) {
  let basicData = $($(obj).closest("basic-data")[0]);
  let togglePanel = basicData.find(".js_togglePanel");
  togglePanel.toggle();
}
