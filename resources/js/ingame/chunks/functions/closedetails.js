function closeDetails(id, expireTime) {
  var elem = $("#fleet" + id);
  elem.children(".openDetails").children().children().attr("src", "/img/icons/de1e5f629d9e47d283488eee0c0ede.gif");
  elem.children(".quantity").show();
  elem.removeClass("detailsOpened");
  elem.addClass("detailsClosed");
  currentMovementTabExtensionStates[id] = [0, expireTime]; // set to 0 == closed

  updateCookieStatus(currentMovementTabExtensionStates);
}
