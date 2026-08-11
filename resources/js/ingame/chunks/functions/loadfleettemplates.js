function loadFleetTemplates() {
  // First, ensure we're looking for the table in the right place
  // The overlay system might have cloned the content, so we need to find it
  var $tbody = null;

  // Try multiple selectors to find the table
  var $table = $(".ui-dialog:visible #fleetTemplates, #zeuch666:visible #fleetTemplates, #fleetTemplates").first();

  if ($table.length === 0) {
    console.warn("Template table not found, will retry...");
    // Retry after a short delay
    setTimeout(function () {
      loadFleetTemplates();
    }, 300);
    return;
  }

  $tbody = $table.find("tbody");

  $.get("/ajax/fleet/templates", function (response) {
    var templates = response.templates || [];

    // Find the tbody again in case the dialog was re-created
    $table = $(".ui-dialog:visible #fleetTemplates, #zeuch666:visible #fleetTemplates, #fleetTemplates").first();
    if ($table.length === 0) {
      return;
    }
    $tbody = $table.find("tbody");

    // Keep only the header row
    $tbody.find("tr:not(.separator)").remove();

    if (templates.length === 0) {
      var $row = $('<tr><td colspan="6" style="text-align: center;">No templates saved yet.</td></tr>');
      $tbody.append($row);
      return;
    }

    // Split templates into two columns
    var $row = $("<tr></tr>");
    var cellCount = 0;
    var displayIndex = 1; // For sequential display numbering

    templates.forEach(function (template) {
      if (cellCount >= 2) {
        $tbody.append($row);
        $row = $("<tr></tr>");
        cellCount = 0;
      }

      var $cell = $('<td colspan="3" style="padding: 5px;"></td>');
      var $tableInner = $(
        '<table class="list" style="width: 100%;"><tr>' +
          '<th class="textCenter fleet_id" style="width: 30px;">' +
          displayIndex +
          "</th>" +
          '<th class="fleet_name">' +
          template.name +
          "</th>" +
          '<th class="fleet_actions" style="width: 80px;">' +
          '<a href="javascript:void(0);" class="tooltip icon_link editTpl" data-id="' +
          template.id +
          '" data-name="' +
          template.name +
          "\" data-ships='" +
          JSON.stringify(template.ships) +
          '\' title="Edit">' +
          '<span class="icon icon_edit"></span>' +
          "</a>" +
          '<a href="javascript:void(0);" class="tooltip icon_link deleteTpl" data-id="' +
          template.id +
          '" title="Delete">' +
          '<span class="icon icon_trash"></span>' +
          "</a>" +
          "</th>" +
          "</tr></table>",
      );

      $cell.append($tableInner);
      $row.append($cell);
      cellCount++;
      displayIndex++;
    });

    // Fill remaining cells if any
    while (cellCount < 2) {
      $row.append('<td colspan="3"></td>');
      cellCount++;
    }

    $tbody.append($row);

    // Bind click events for edit and delete
    $(".editTpl")
      .off("click")
      .on("click", function () {
        var id = $(this).data("id");
        var name = $(this).data("name");
        var ships = $(this).data("ships");
        setShipsFleet(ships, name, id);
        // Open edit overlay using the overlay system
        var $editOverlay = $("#fleetTemplatesEdit");
        if ($editOverlay.length > 0) {
          // Trigger click on a button that would open this overlay
          $("#addNewTpl").click();
          // The click above resets the form, so we need to set our values again
          setTimeout(function () {
            setShipsFleet(ships, name, id);
          }, 50);
        } else {
          $editOverlay.show();
        }
      });

    $(".deleteTpl")
      .off("click")
      .on("click", function () {
        var id = $(this).data("id");
        errorBoxDecision(
          LocalizationStrings.attention,
          "Are you sure you want to delete this template?",
          LocalizationStrings.yes,
          LocalizationStrings.no,
          function () {
            deleteTemplate(id);
          },
        );
      });

    $(".list tr:even").addClass("alt");
  });
}
