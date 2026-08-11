function checkValue(id) {
  setValue(id, Math.min(getValue($("#" + id + "_value").val()), Math.round(freeStorage[id])));
  free_id = 6 - id - offer_id;
  offer_costs = calcCosts(free_id, getValue($("#" + free_id + "_value").val()));
  costs = calcCosts(id, getValue($("#" + id + "_value").val()));
  freeOfferCosts = Math.round(offer_amount - offer_costs);

  if (costs > freeOfferCosts) {
    setValue(id, calcInputFromCosts(id, freeOfferCosts));
    costs = calcCosts(id, getValue($("#" + id + "_value").val()));
  }

  offer_costs = offer_costs + costs;
  setValue(offer_id, offer_costs);
  document.getElementById(id + "_storage").innerHTML = number_format(
    freeStorage[id] - getValue($("#" + id + "_value").val()),
    0,
    loca["decimalPoint"],
    loca["thousandsSeparator"],
  );
}
