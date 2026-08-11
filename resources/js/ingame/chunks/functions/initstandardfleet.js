function initStandardFleet() {
  // Use event delegation on document to catch when the dialog opens
  $(document).on("dialogopen", ".ui-dialog", function (e) {
    var $dialog = $(e.target);
    if ($dialog.find("#fleetTemplates").length > 0 || $dialog.attr("aria-describedby") === "zeuch666") {
      // Use cached templates if available for instant display
      if (cachedTemplates && cachedTemplates.length > 0) {
        populateTemplatesTable(cachedTemplates);
      } else {
        // Fallback to loading via AJAX if cache is empty
        setTimeout(loadFleetTemplates, 50);
      }
    }
  });

  // Handle save button click directly via AJAX
  $(document)
    .off("click", ".standardFleetSubmit")
    .on("click", ".standardFleetSubmit", function (e) {
      e.preventDefault();
      e.stopPropagation();

      var $form = $("#submit_std");
      var templateId = $("#template_id").val();
      var templateName = $("#template_name").val();

      // Validate name
      if (!templateName || templateName.trim() === "") {
        errorBoxDecision(
          LocalizationStrings.attention,
          "Please enter a template name.",
          LocalizationStrings.yes,
          LocalizationStrings.no,
          function () {},
        );
        return;
      }

      // Build ships object from form inputs
      var ships = {};
      var totalShips = 0;
      $('input[name^="ship["]').each(function () {
        var match = $(this)
          .attr("name")
          .match(/\[(\d+)\]/);
        if (match) {
          var shipId = match[1];
          var value = parseInt($(this).val()) || 0;
          ships[shipId] = value;
          totalShips += value;
        }
      });

      if (totalShips === 0) {
        errorBoxDecision(
          LocalizationStrings.attention,
          "Please add at least one ship to the template.",
          LocalizationStrings.yes,
          LocalizationStrings.no,
          function () {},
        );
        return;
      }

      // Prepare data for AJAX
      var data = {
        template_id: templateId,
        template_name: templateName.trim(),
        ship: ships,
      };

      $.ajax({
        url: "/ajax/fleet/templates",
        type: "POST",
        data: data,
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
          if (response.success) {
            // Close the edit overlay
            $(".ui-dialog:has(#fleetTemplatesEdit)").find(".ui-dialog-titlebar-close").click();
            // Reset form immediately
            $form[0].reset();
            $("#template_id").val(0);
            $("#template_name").val("");
            // Reset all ship inputs
            $('input[name^="ship["]').val(0);
            // Reload templates list and dropdown after a short delay
            setTimeout(function () {
              loadFleetTemplates();
              loadStandardFleetDropdown();
            }, 200);
          } else {
            errorBoxDecision(
              LocalizationStrings.attention,
              response.message || "Failed to save template.",
              LocalizationStrings.yes,
              LocalizationStrings.no,
              function () {},
            );
          }
        },
        error: function (xhr) {
          var message = "Failed to save template.";
          if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          }
          errorBoxDecision(
            LocalizationStrings.attention,
            message,
            LocalizationStrings.yes,
            LocalizationStrings.no,
            function () {},
          );
        },
      });
    });

  $(".standardFleetReset")
    .unbind("click")
    .bind("click", function () {
      $(this).parents("form")[0].reset();
    });
  $(".changeFleet")
    .unbind("click")
    .bind("click", function () {
      $(".combatunits").val($(this).attr("rel")).trigger("change");
      $(this).parents(".ui-dialog").find(".ui-dialog-titlebar-close").click();
    });
}
