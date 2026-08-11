function show_hide_menus(element) {
  if ($(element).is(":visible")) {
    $(element).hide();
  } else {
    $(element).show();
  }
}
