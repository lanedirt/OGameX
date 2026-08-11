function setMaxIntInput(formElement, data) {
  for (var techID in data) {
    if (
      !$(formElement)
        .find("#ship_" + techID)
        .attr("disabled")
    ) {
      $(formElement)
        .find("#ship_" + techID)
        .val(data[techID]);
      checkIntInput($(formElement).find("ship_" + techID), 0, data[techID]);
    }
  }
}
