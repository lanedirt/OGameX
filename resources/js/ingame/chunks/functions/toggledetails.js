function toggleDetails() {
  var relTr = $(".partnerInfo." + $(this).attr("rel"));

  if ($(relTr).is(":hidden")) {
    $(relTr).show();
    $(this).parents("tr").addClass("detailsOpened").removeClass("detailsClosed");
  } else {
    $(relTr).hide();
    $(this).parents("tr").addClass("detailsClosed").removeClass("detailsOpened");
  }
}
