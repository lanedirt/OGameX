/**
 *
 * @param labelObject object
 * @param useCount int
 * @returns string
 */

function readableVersionOfLabel(labelObject, useCount) {
  labelObject.location = -0.05 * useCount + 0.85;
  var split = labelObject.label.indexOf("/");

  if (split) {
    //        label = label.substring(0,split) + '<br/>/<br/>' + label.substring(split + 1);
  }

  return labelObject;
}
