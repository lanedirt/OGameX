function setupOverlay(pageLink, overlayTitle, techID) {
  $(".build-it_premium").addClass("overlay");
  $(".build-it_premium").attr("href", pageLink);
  $(".build-it_premium").data("overlay-title", overlayTitle);
  $(".build-it_premium").data("techid", techID);
}
