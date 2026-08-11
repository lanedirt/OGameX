function expandLifeforms(obj) {
  let combatsimSection = $(obj).closest("combatsim-section");

  if (combatsimSection.attr("show-lifeform") === "1") {
    combatsimSection.attr("show-lifeform", "0");
  } else {
    combatsimSection.attr("show-lifeform", "1");
  }
}
