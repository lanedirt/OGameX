PercentSelector.setPercentFromPageX = function (bar, page_x, animate) {
  var $bar = $(bar);
  var x = page_x - $bar.offset().left;
  var width = $bar.outerWidth();
  var percent = (100 * x) / width;
  if (percent > 100) percent = 100;
  if (percent < 10) percent = 10;
  percent = Math.round(percent);
  PercentSelector.setPercent(bar, percent, animate);
};
