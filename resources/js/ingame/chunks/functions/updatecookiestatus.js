function updateCookieStatus(tabStates) {
  var stringifiedState = JSON.stringify(tabStates);
  var stringifiedOptions = JSON.stringify({
    expires: Math.round(new Date().getTime() / 1000) + 7 * 86400,
  });
  $.cookie("tabBoxFleets", stringifiedState, stringifiedOptions);
}
