function tabletInitEmpire() {
  if (!isMobile) {
    return false;
  }

  var width = $("#mainWrapper").width();
  width = width < 1024 ? "1024" : width;
  $("#outerWrapper").width(width);
  $(".reset").hide();
}
