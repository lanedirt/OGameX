function tradeDone(data) {
  data = $.parseJSON(data);
  token = data.token;

  if (data.status === true) {
    closeTradeResourcesOverlay();
  }

  errorBoxAsArray(data["errorbox"]);
}
