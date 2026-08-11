function checkIntInput(id, minVal, maxVal) {
  var value = $(id).val();

  if (typeof value != "undefined" && value != "") {
    intVal = Math.abs(getValue(value));

    if (maxVal != null) {
      intVal = Math.min(intVal, maxVal);
    }

    $(id).val(intVal);
  }
}
