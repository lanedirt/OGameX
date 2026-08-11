function changeTooltip(object, title) {
  var targetElement = $(object);

  if (targetElement.length == 0) {
    return;
  }

  removeTooltip(targetElement);
  $(targetElement).attr("title", title);
  initTooltips(targetElement);
}
