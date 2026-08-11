/**
 * Generates all the planet columns for the imperium overview table
 *
 * @see createImperiumHtml()
 * @param array data
 * @return string
 */

function createPlanetsHtml(data) {
  // Some basic variables we use
  var planet = "";
  var shortname = "";
  var content = "";
  var headerKey = "";
  var newContent = "";
  var i = 0;
  var key = ""; // Iterate over the planets

  $.each(data.planets, function () {
    planet = this;
    content = "";
    headerKey = "";
    shortname = planet.name.length > 13 ? planet.name.substr(0, 11) + "..." : planet.name;
    content += '<div class="planetHead">';

    if (planet.name != shortname) {
      content += '<div class="planetname tooltip" title="' + planet.name + '">' + shortname + "</div>";
    } else {
      content += '<div class="planetname">' + shortname + "</div>";
    }

    if (isMobile) {
      content +=
        '<div class="planetImg"><img class="' +
        planet.border +
        '" src="' +
        planet.image +
        '"/></div>' +
        '<div class="planetData">' +
        '<div class="planetDataTop odd">' +
        "<ul>" +
        '<li class="coords textLeft"><a class="dark_highlight_tablet" href="' +
        planet.coordinatesLink +
        '" >' +
        planet.coordinates +
        "</a></li>" +
        '<li class="coords">' +
        '<span class="dark_highlight_tablet energy tooltipRight" title="' +
        (planet.type == 3 ? planet.diameterTooltip : planet.energyTooltip) +
        '">' +
        (planet.type == 3 ? "\u2300: " + planet.diameter : planet.energyDescr + planet.energy) +
        "</span>" +
        "</li>" +
        "</ul>" +
        "</div>" +
        '<div class="planetDataTop">' +
        '<ul class="planet_data_2">' +
        '<li class="fields textLeft">' +
        planet.fieldUsed +
        "/" +
        planet.fieldMax +
        "</li>" +
        '<li class="fields textLeft">' +
        planet.temperature +
        "</li>" +
        "</ul>" +
        "</div>" +
        "</div>" +
        '<div class="clearfloat"></div>' +
        "</div>";
    } else {
      content +=
        '<div class="planetImg"><img class="' +
        planet.border +
        '" src="' +
        planet.image +
        '"/></div>' +
        '<div class="planetData">' +
        '<div class="planetDataTop odd">' +
        "<ul>" +
        '<li class="coords textLeft"><a href="' +
        planet.coordinatesLink +
        '" >' +
        planet.coordinates +
        "</a></li>" +
        '<li class="fields textRight">' +
        planet.fieldUsed +
        "/" +
        planet.fieldMax +
        "</li>" +
        "</ul>" +
        "</div>" +
        '<div class="planetDataTop">' +
        "<ul>" +
        '<li class="coords textLeft">' +
        (planet.type == 3 ? planet.diameterDescr : planet.energyDescr) +
        "</li>" +
        '<li class="coords textRight">' +
        (planet.type == 3 ? planet.diameter : planet.energy) +
        "</li>" +
        "</ul>" +
        "</div>" +
        '<div class="planetDataBottom odd">' +
        "<ul>" +
        '<li class="fields textCenter">' +
        planet.temperature +
        "</li>" +
        "</ul>" +
        "</div>" +
        "</div>" +
        '<div class="clearfloat"></div>' +
        "</div>";
    } // Generate the content

    for (var group in data.groups) {
      content = content + '<div class="row"></div>' + '<div class="values ' + group + " group" + group + '">';

      for (i = 0; (key = data.groups[group][i]); i++) {
        key = String(key); // We have some special html for this!

        if (planet[key + "_html"] != null) {
          key = key + "_html";
        } // Define the header key

        headerKey = key;

        if (key.substring(key.length - 5) == "_html") {
          headerKey = key.substring(0, key.length - 5);
        }

        content = content + '<div class="' + headerKey + '">' + planet[key] + "</div>";
      }

      content = content + "</div>";
    } // And add the planet to the planetlist

    newContent = newContent + '<div id="planet' + this.id + '" class="planet">' + content + "</div>";
  });
  return newContent;
}
