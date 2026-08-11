function getOverlayText($thisObj) {
  if ($thisObj.hasClass("building") && $thisObj.children().hasClass("build-faster-img")) {
    return questionbuilding;
  } else if ($thisObj.hasClass("building") && $thisObj.children().hasClass("build-finish-img")) {
    return questionbuilding;
  } else if ($thisObj.hasClass("lfbuilding") && $thisObj.children().hasClass("build-faster-img")) {
    return questionlfbuilding;
  } else if ($thisObj.hasClass("lfbuilding") && $thisObj.children().hasClass("build-finish-img")) {
    return questionlfbuilding;
  } else if ($thisObj.hasClass("ship") && $thisObj.children().hasClass("build-faster-img")) {
    return questionship;
  } else if ($thisObj.hasClass("ship") && $thisObj.children().hasClass("build-finish-img")) {
    return questionship;
  } else if ($thisObj.hasClass("shipextended") && $thisObj.children().hasClass("build-faster-img")) {
    return questionshipextended;
  } else if ($thisObj.hasClass("shipextended") && $thisObj.children().hasClass("build-finish-img")) {
    return questionshipextended;
  } else if ($thisObj.hasClass("research") && $thisObj.children().hasClass("build-faster-img")) {
    return questionresearch;
  } else if ($thisObj.hasClass("research") && $thisObj.children().hasClass("build-finish-img")) {
    return questionresearch;
  } else if ($thisObj.hasClass("lfresearch") && $thisObj.children().hasClass("build-faster-img")) {
    return questionlfresearch;
  } else if ($thisObj.hasClass("lfresearch") && $thisObj.children().hasClass("build-finish-img")) {
    return questionlfresearch;
  }
}
