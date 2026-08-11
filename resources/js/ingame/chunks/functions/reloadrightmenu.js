function reloadRightmenu(url) {
  $.get(url, {}, displayRightmenu);
}
