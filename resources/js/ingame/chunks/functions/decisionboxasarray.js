function decisionBoxAsArray(data) {
  errorBoxDecision(
    data["title"],
    data["text"],
    data["buttonOk"],
    data["buttonNOk"],
    String(data["okFunction"]),
    String(data["nokFunction"]),
    data["removeOpen"],
  );
}
