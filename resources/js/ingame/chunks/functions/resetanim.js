function resetAnim(obj) {
  let currentTarget = $(obj);
  currentTarget.find(".togglePanel").hide();
  currentTarget.find("class-selection input").prop("checked", false);
}
