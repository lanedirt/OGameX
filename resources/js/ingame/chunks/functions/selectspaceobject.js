function selectSpaceObject(obj) {
  let currentTarget = $(obj);
  let basicData = currentTarget.closest("basic-data");
  basicData
    .find(".toggleLink")
    .attr("data-selected-planetid", currentTarget.data("planetid"))
    .data("selectedPlanetid", currentTarget.data("planetid"));
  basicData.find(".togglePanel").hide();
  basicData.find(".togglePanel li").removeClass("selected");
  basicData.find(".togglePanel ul #" + currentTarget.data("planetid")).addClass("selected");
  basicData.find(".toggleLink").html(currentTarget.html());
}
