function getNumberFormatShort(value, precision) {
  if (typeof precision == "undefined") {
    precision = 0;
  }

  value = Math.floor(value);
  var unit = "";

  if (value >= 1000000000) {
    unit = LocalizationStrings["unitMilliard"];
    value = value / 1000000000;
  }

  if (value >= 1000000) {
    unit = LocalizationStrings["unitMega"];
    value = value / 1000000;
  }

  if (value >= 1000) {
    unit = LocalizationStrings["unitKilo"];
    value = value / 1000;
  }

  return (
    number_format(value, precision, LocalizationStrings["decimalPoint"], LocalizationStrings["thousandSeperator"]) +
    unit
  );
}
