function setValue(id, value) {
  if (offer_id == id) {
    $("#" + id + "_value_label").html(number_format(value, 0, loca["decimalPoint"], loca["thousandsSeparator"]));
  } else {
    formatNumber("#" + id + "_value", value);
  }
}
