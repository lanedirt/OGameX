function initRetinaImages() {
  // we only want to replace images if we're on a retina display:
  if ($(".js_replace2x").css("font-size") == "1px") {
    $("img.js_replace2x").each(function () {
      $(this).attr("src", $(this).attr("rel"));
    });
  }
}
