function closeSearch() {
  if (currentPage !== undefined) {
    if (currentPage == "fleet1" || currentPage == "fleet2") {
      $("a#continue").focus();
    } else if (currentPage == "fleet3") {
      $("a#start").focus();
    }
  }
}
