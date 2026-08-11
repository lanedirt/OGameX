function gfNumberGetHumanReadable(value, shortForm, precision) {
  value = Math.floor(value);
  shortForm = shortForm || false;
  var unit = "";
  var precision = precision || 3;

  if (shortForm) {
    if (value >= 1000000000) {
      unit = LocalizationStrings["unitMilliard"];
      value = value / 1000000000;
    } else if (value >= 1000000) {
      unit = LocalizationStrings["unitMega"];
      value = value / 1000000;
    }
  }

  floorWithPrecision = function (value, precision) {
    return Math.floor(value * Math.pow(10, precision)) / Math.pow(10, precision);
  };

  value = floorWithPrecision(value, precision);

  while (precision >= 0) {
    if (floorWithPrecision(value, precision - 1) != value) {
      break;
    }

    precision = precision - 1;
  }

  return (
    number_format(value, precision, LocalizationStrings["decimalPoint"], LocalizationStrings["thousandSeperator"]) +
    unit
  );
}
