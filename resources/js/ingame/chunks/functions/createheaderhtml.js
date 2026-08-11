/**
 * Generates the first header column for the imperium table
 *
 * @see createImerpiumHtml()
 * @param array data
 * @return string
 */

function createHeaderHtml(data) {
  // Some variables we use
  var content = "";
  var i = 0;
  var key = "";
  content =
    content +
    '<div id="wrapTL">' +
    '<div id="tab-left">' +
    '<a id="planetsTab" href="javascript:void(0);" class="active" title="">' +
    "<span>" +
    data.translations["planetsTab"] +
    "</span>" +
    "</a>" +
    '<a id="moonsTab" href="javascript:void(0);" title="" class="">' +
    "<span>" +
    data.translations["moonsTab"] +
    "</span>" +
    "</a>" +
    "</div>" +
    "</div>"; // Generate the content

  for (group in data.groups) {
    content =
      content +
      '<div id="' +
      group +
      '" class="firstCat headers ' +
      group +
      " headers" +
      group +
      '" group="' +
      group +
      '">' +
      '<h3 class="open">' +
      "<span>" +
      data.translations.groups[group] +
      "</span>" +
      "</h3>" +
      '<ul class="secondCat ' +
      group +
      " group" +
      group +
      '">';

    for (i = 0; (key = data.groups[group][i]); i++) {
      if (data.translations.planets[key] == null) {
        continue;
      }

      content += '<li class="' + key + '">';

      if (data.translations.planets[key + "_full"] != data.translations.planets[key]) {
        content +=
          '<span class="tooltipLeft" title="' +
          data.translations.planets[key + "_full"] +
          '">' +
          data.translations.planets[key] +
          "</span>";
      } else {
        content += "<span>" + data.translations.planets[key] + "</span>";
      }

      content += "</li>";
    }

    content = content + "</ul>" + "</div>";
  } // Add the outer div to the output

  content =
    '<div id="empireTab">' +
    '<div class="wrapTab">' +
    '<div class="tab-part01"></div>' +
    "<h2>" +
    data.translations.header +
    "</h2>" +
    '<span class="reset"><img src="/img/icons/f805c477d15ae3131b7c39c7d70e48.gif" width="16" height="16"><a href="javascript:void(0);" onClick="clearImperiumOrder(); return false;">' +
    data.translations.reset +
    "</a></span>" +
    '<div class="wrapCorner"></div>' +
    '<br class="clearfloat"/>' +
    "</div>" +
    "</div>" +
    '<div class="header">' +
    content +
    "</div>"; // Return the content

  return content;
}
