function getFastBuildPrice($thisObj) {
  if ($thisObj.hasClass("building")) {
    return pricebuilding;
  } else if ($thisObj.hasClass("lfbuilding")) {
    return pricelfbuilding;
  } else if ($thisObj.hasClass("research")) {
    return priceresearch;
  } else if ($thisObj.hasClass("lfresearch")) {
    return pricelfresearch;
  } else if ($thisObj.hasClass("ship")) {
    return priceship;
  } else if ($thisObj.hasClass("shipextended")) {
    return priceshipextended;
  }
}
