function openDetails(id, expireTime) {
  var elem = $("#fleet" + id);
  elem.children(".openDetails").children().children().attr("src", "/img/icons/577565fadab7780b0997a76d0dca9b.gif");
  elem.children(".quantity").hide();
  elem.removeClass("detailsClosed");
  elem.addClass("detailsOpened");
  currentMovementTabExtensionStates[id] = [1, expireTime]; // set to 0 == closed

  updateCookieStatus(currentMovementTabExtensionStates);
}
