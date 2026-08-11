function getTooltipSelector(selector) {
  var standardSelector =
    ".tooltipPremium, .tooltip, .tooltipRight, .tooltipLeft, .tooltipBottom, .tooltipClose, .tooltipHTML, .tooltipRel, .tooltipAJAX, .tooltipCustom, .markItUpButton a";

  if (typeof selector == "undefined") {
    selector = standardSelector;
  } else if (typeof selector == "string" && !selector.match(/\.tooltip/)) {
    var standardSelectorArray = standardSelector.split(", ");
    var previousSelector = selector;

    for (i in standardSelectorArray) {
      selector += ", " + previousSelector + " " + standardSelectorArray[i];
    }
  }

  return selector;
}
