$(function () {
  $(".slideIn").on("click", function () {
    loadDetails($(this).data("type"));
  });
  $("#detail").on("click", ".close_details", function () {
    $("#detail").hide();
  });
});
