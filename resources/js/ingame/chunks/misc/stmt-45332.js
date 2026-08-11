$(document).ready(function () {
  $("#retrieveEmailComponent #username")
    .off()
    .keypress(function (event) {
      if (event.which == 13) {
        event.preventDefault();
        $("#retrieveEmailComponent #password").focus();
      }
    });
  $("#retrieveEmailComponent #password")
    .off()
    .keypress(function (event) {
      if (event.which == 13) {
        event.preventDefault();
        ogame.retrieveEmail.send();
      }
    });
  $("#retrieve")
    .off()
    .on("click", function () {
      ogame.retrieveEmail.send();
    });
});
