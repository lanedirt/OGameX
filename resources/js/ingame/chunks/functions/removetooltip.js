function removeTooltip(object) {
  var targetElement = $(object);
  targetElement.each(function () {
    if ($(this).data("tooltipLoaded")) {
      $(this).data("tooltipLoaded", false);
      Tipped.remove($(this));
    }
  });
}
