
// Load templates and populate the standard fleets dropdown
function loadStandardFleetDropdown() {
  $.get("/ajax/fleet/templates", function (response) {
    var templates = response.templates || [];
    cachedTemplates = templates;

    // Update the FleetDispatcher's standardFleets array if it exists
    if (typeof fleetDispatcher !== "undefined" && fleetDispatcher) {
      // Convert templates to the format expected by FleetDispatcher
      fleetDispatcher.standardFleets = templates.map(function (template) {
        return {
          id: template.id,
          name: template.name,
          ships: template.ships,
        };
      });
    }

    // Update the select dropdown
    var $select = $("#standardfleet");
    if ($select.length > 0) {
      // Destroy the existing dropdown widget
      $select.ogameDropDown("destroy");

      // Clear existing options except the first one (default "-")
      $select.find("option:not(:first)").remove();

      // Add template options
      templates.forEach(function (template) {
        var $option = $("<option></option>").attr("value", template.id).text(template.name);
        $select.append($option);
      });

      // Reinitialize the dropdown
      $select.ogameDropDown();
    }
  });
}
