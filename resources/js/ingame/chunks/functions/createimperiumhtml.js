/**
 * Generates the imperium overview table
 *
 * @param string destination
 * @param string loading
 * @param array data
 */

function createImperiumHtml(destination, loading, data, isMoon) {
  // Set width to wrapper
  var planetCount = 0;

  if (typeof data.planets != "undefined") {
    planetCount = data["planets"].length;
  }

  var wrapperWidth = 345 + planetCount * 165;
  $("#mainWrapper").attr("style", "width: " + wrapperWidth + "px"); // Show the loading "screen"

  $(loading).show(); // Lets build up the new content

  var newContent =
    createHeaderHtml(data) +
    '<div class="planetWrapper">' +
    createPlanetsHtml(data) +
    createSummaryHtml(data) +
    "</div>" +
    '<br class="clearfloat"/>'; // Update the destination with the new content

  $(destination).append(newContent); // Finally we need the whole planetlist to be sortable

  $(destination + " .planetWrapper")
    .sortable({
      start: function () {
        removeTooltip(getTooltipSelector());
      },
      update: function () {
        saveImperiumOrder(destination + " .planetWrapper", isMoon);
      },
    })
    .disableSelection(); // Make the groups clickable and load the toggle state

  for (group in data.groups) {
    $(destination + " .headers" + group).click(function () {
      var selector = destination + " .group" + $(this).attr("group");
      var selectorHeader = "#" + $(this).attr("group") + " h3";
      $(selector).toggle();
      $(selectorHeader).removeClass("openhover").removeClass("closehover").removeClass("close").removeClass("open");

      if ($(selector).attr("style").toLowerCase().substr(9, 4) == "none") {
        $(selectorHeader).addClass("close");
        $.cookie("impToggleState" + $(this).attr("group"), "1", {
          expires: 365,
        });
      } else {
        $(selectorHeader).addClass("open");
        $.cookie("impToggleState" + $(this).attr("group"), "0", {
          expires: 365,
        });
      }
    });
    var cookie = $.cookie("impToggleState" + group);

    if (cookie != null && cookie == "1") {
      $("#" + group + " > h3").removeClass("open");
      $("#" + group + " > h3").addClass("close");
      $(destination + " .group" + group).toggle();
    }
  } // Hide the loading "screen"

  $(loading).hide();
  var summaryIndex = $.inArray(0, empireOrder);

  if (summaryIndex > -1) {
    $(".planetWrapper .planet:eq(" + summaryIndex + "):not(:last-child)").before($("#planet0"));
  } // Load Tooltips

  initTooltips();
}
