function fillField() {
  currentValue = $("#planetName").val();

  if (currentValue == "") {
    $("#planetName").val(defaultName);
  }
}
