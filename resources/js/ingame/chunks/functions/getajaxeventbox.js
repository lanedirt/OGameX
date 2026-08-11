function getAjaxEventbox() {
  if (typeof ajaxEventboxURI === "undefined") {
    return;
  }

  $.get(ajaxEventboxURI, reloadEventbox, "text");
}
