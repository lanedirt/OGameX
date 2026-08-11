function isReportString(str) {
  let [reportType, lang, serverId, apiKey] = str.split("-");

  if (
    str.length !== 50 ||
    reportType !== "sr" ||
    lang !== js_serverlang ||
    serverId !== js_serverid ||
    str.match("/sr-" + js_serverlang + "-" + js_serverid + "[A-Z]{40}/g") === false ||
    apiKey.length !== 40
  ) {
    showNotification(
      combatSimLoca.LOCA_COMBATSIM_INVALID_DATA + " " + combatSimLoca.LOCA_COMBATSIM_INVALID_API,
      "warning",
    );
    return false;
  }

  return true;
}
