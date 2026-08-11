function submitForm() {
  galaxy = $("#galaxy_input").val();
  system = $("#system_input").val();

  if (0 === galaxy.length || !$.isNumeric(+galaxy)) {
    galaxy = 1;
  }

  if (0 === system.length || !$.isNumeric(+system)) {
    system = 1;
  }

  if (mobile) {
    loadContent(galaxy, system);
  } else {
    loadContentNew(galaxy, system);
  }
}
