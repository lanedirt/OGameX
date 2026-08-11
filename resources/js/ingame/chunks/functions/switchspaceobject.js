function switchSpaceObject(obj) {
  let currentTarget = $(obj);

  if (currentTarget.hasClass("selected")) {
    return false;
  }

  let basicData = $(currentTarget.closest("basic-data")[0]);
  basicData.find(".togglePanel ul").hide().removeClass("active");
  basicData.find(".planetSelection div").removeClass("selected");
  currentTarget.addClass("selected");
  let className = "planet";

  if (currentTarget.hasClass("moon")) {
    className = "moon";
  }

  basicData
    .find(".togglePanel ul." + className)
    .show()
    .addClass("active");
  let spaceObject = basicData.find(".togglePanel").find("ul." + className + " li:first");
  basicData
    .find(".toggleLink")
    .attr("data-selected-planetid", spaceObject.data("planetid"))
    .data("selectedPlanetid", spaceObject.data("planetid"));
  basicData.find(".togglePanel").hide();
  basicData.find(".togglePanel li").removeClass("selected");
  basicData.find(".togglePanel ul #" + spaceObject.data("planetid")).addClass("selected");
  basicData.find(".toggleLink").html(spaceObject.html());
}
