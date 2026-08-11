function formatNumber(object, value) {
  var formattedValue = number_format(
    getValue(value),
    0,
    LocalizationStrings["decimalPoint"],
    LocalizationStrings["thousandSeparator"],
  );
  var $thisObj = $(object);
  var range = $thisObj.getSelection();

  if ($thisObj.val().length !== formattedValue.length) {
    range.start = Math.max(0, range.start + formattedValue.length - $thisObj.val().length);
    range.end = Math.max(0, range.end + formattedValue.length - $thisObj.val().length);
  }

  $thisObj.val(formattedValue);

  if ($thisObj.is(":focus")) {
    $thisObj.setSelection(range);
  }
}
