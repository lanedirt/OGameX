function change_class(ele) {
  if (document.getElementById(ele).className == "closed") {
    document.getElementById(ele).className = "opened";
  } else {
    document.getElementById(ele).className = "closed";
  }
}
