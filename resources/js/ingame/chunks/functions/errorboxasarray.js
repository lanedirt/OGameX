function errorBoxAsArray(data) {
  if (data["type"] == "notify") {
    notifyBoxAsArray(data);
  } else if (data["type"] == "decision") {
    decisionBoxAsArray(data);
  } else if (data["type"] == "fadeBox") {
    fadeBox(data["text"], data["failed"]);
  }
}
