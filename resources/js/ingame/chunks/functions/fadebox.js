function fadeBox(message, failed, callback, duration) {
  if (failed) {
    $("#fadeBoxStyle").attr("class", "failed");
  } else {
    $("#fadeBoxStyle").attr("class", "success");
  }

  $("#fadeBoxContent").html(message);
  $("#fadeBox")
    .stop(false, true)
    .show()
    .fadeOut(duration || 10000, callback);
}
