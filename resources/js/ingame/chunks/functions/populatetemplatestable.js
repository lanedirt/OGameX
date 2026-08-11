
// Populate the templates table with given templates array
function populateTemplatesTable(templates) {
  var $tbody = $("#fleetTemplates tbody");

  if ($tbody.length === 0) {
    console.warn("Template table not found");
    return;
  }

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
  var displayIndex = 1;

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
        $("#addNewTpl").click();
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
}
