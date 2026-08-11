function checkUsername() {
  var username = document.forms["new"].elements["username"].value;

  if (username.length < 3 || username.length >= 20) {
    display_error("username");
  } else {
    hide_error();
  }
}
