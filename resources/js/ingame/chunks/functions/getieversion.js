/**
 * @see http://obvcode.blogspot.de/2007/11/easiest-way-to-check-ie-version-with.html
 * @return {Number}
 */

function getIEVersion() {
  var version = 999;
  if (navigator.appVersion.indexOf("MSIE") != -1) version = parseFloat(navigator.appVersion.split("MSIE")[1]);
  return version;
}
