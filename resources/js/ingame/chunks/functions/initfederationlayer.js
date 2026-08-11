function initFederationLayer() {
  $("#switch").click(function () {
    var searchFed = $("#searchFed");
    searchFed.find("> .wrap").toggle();
    searchFed.find("> #honorWarning").toggle();
  });
  $("#buddyselect, #participantselect").selectable({
    filter: "li:not(.undermark)",
  });
  $(document)
    .undelegate("ul#buddyselect li", "dblclick")
    .delegate("ul#buddyselect li", "dblclick", function () {
      addUserToUnion();
    })
    .undelegate("ul#participantselect li", "dblclick")
    .delegate("ul#participantselect li", "dblclick", function () {
      removeUserFromUnion();
    });
}
