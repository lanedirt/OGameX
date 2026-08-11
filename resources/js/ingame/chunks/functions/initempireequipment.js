function initEmpireEquipment() {
  $(".overview_equipment .item_img_box .hidden").each(function () {
    startCooldown($(this), $(this).parent().parent().find(".pusher"), 32);
  });
}
