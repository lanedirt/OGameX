function clearField() {
  currentValue = $("#planetName").val();

  if (defaultName == currentValue) {
    clearInput("#planetName");
  }
}
