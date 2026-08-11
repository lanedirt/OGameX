function display_info(type) {
  if (
    document.getElementById("infoInput").innerHTML == "" ||
    document.getElementById("infoInput").innerHTML != get_displayText(type)
  ) {
    document.getElementById("infoInput").innerHTML = get_displayText(type);
  }
}
