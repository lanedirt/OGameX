function updateSupplyDetails(ships, costs, index) {
  $("#shipCount").html(gfNumberGetHumanReadable(ships));
  $("#deutCosts").html(gfNumberGetHumanReadable(costs));
  $("span.countdown").hide();
  $("#holdingTime-" + index).show();
}
