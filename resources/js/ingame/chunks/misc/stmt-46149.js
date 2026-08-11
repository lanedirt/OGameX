$(function () {
  $(".techdetail").on("click", function () {
    loadDetails($(this).data("techid"));
  });
  $("#detail").on("click", ".close_details", function () {
    $("#detail").hide();
  });
});
