function notifyBoxAsArray(data) {
  errorBoxNotify(
    data["title"],
    data["text"],
    data["buttonOk"],
    String(data["okFunction"]),
    data["removeOpen"],
    data["modal"],
  );
}
