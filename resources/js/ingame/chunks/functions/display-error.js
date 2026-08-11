function display_error(type) {
  if (
    document.getElementById("errorInput").innerHTML == "" ||
    document.getElementById("errorInput").innerHTML != get_errorText(type)
  ) {
    document.getElementById("errorInput").innerHTML = get_errorText(type);
    document.getElementById("error").style.display = "block";
  }
}
