/**
 * Generates the summary column for the imperium overview table
 *
 * @see createImperiumHtml()
 * @param array data
 * @return string
 */

function createSummaryHtml(data) {
  // Some variables we use
  var content = "";
  var planet = null;
  var value = 0;
  var i = 0;
  var key = "";
  content =
    content +
    '<div class="planetHead">' +
    '<div class="planetname">' +
    data.translations.summary +
    "</div>" +
    '<div class="planetImg"><img src="/img/icons/7efb2e73ca11d2344bbed43668da10.jpg"/></div>' +
    '<div class="planetData">' +
    "<ul>" +
    '<li class="coords textLeft"></li>' +
    '<li class="fields textRight"></li>' +
    "</ul>" +
    "</div>" +
    '<div class="clearfloat"></div>' +
    "</div>"; // Generate the content

  for (group in data.groups) {
    content = content + '<div class="row"></div>' + '<div class="values ' + group + " group" + group + '">';

    for (i = 0; (key = data.groups[group][i]); i++) {
      if (data.translations.planets[key] == null) {
        continue;
      }

      var production = {
        hourly: 0,
        daily: 0,
        weekly: 0,
      };

      if (key == "name") {
        value = data.translations.summary;
      } else {
        value = 0;

        if (group == "research") {
          if (!isNaN(data.planets[0][key])) {
            value = data.planets[0][key];
          }
        } else {
          $.each(data.planets, function () {
            planet = this;

            if (!isNaN(planet[key])) {
              value = value + parseInt(planet[key]);

              if (group == "supply" && !isNaN(planet["production"]["hourly"][key - 1])) {
                production.hourly += planet["production"]["hourly"][key - 1];
                production.daily += planet["production"]["daily"][key - 1];
                production.weekly += planet["production"]["weekly"][key - 1];
              }
            }
          });
        }
      }

      if (group == "supply" || group == "station") {
        value = "&#x00F8; " + tsdpkt(round(value / data.planets.length, 1));
      } else if (group == "items") {
        value = "&nbsp;";
      } else {
        value = tsdpkt(value);
      }

      if (group == "supply" && key != "name" && production.hourly > 0) {
        var tooltip =
          "<table>" +
          "<tr><td>" +
          data.translations.production.hourly +
          ":</td><td style=&quot;text-align: right;&quot;>" +
          tsdpkt(production.hourly) +
          "</td></tr>" +
          "<tr><td>" +
          data.translations.production.daily +
          ":</td><td style=&quot;text-align: right;&quot;>" +
          tsdpkt(production.daily) +
          "</td></tr>" +
          "<tr><td>" +
          data.translations.production.weekly +
          ":</td><td style=&quot;text-align: right;&quot;>" +
          tsdpkt(production.weekly) +
          "</td></tr></table>";
        tooltip = tooltip.replace(/</, "&lt;").replace(/>/, "&gt;");
        content = content + '<div class="tooltipRight ' + key + '" title="' + tooltip + '">' + value + "</div>";
      } else {
        content = content + '<div class="' + key + '">' + value + "</div>";
      }
    }

    content = content + "</div>";
  } // Add the outer div to the output

  content = '<div id="planet0" class="planet summary">' + content + "</div>";
  return content;
}
