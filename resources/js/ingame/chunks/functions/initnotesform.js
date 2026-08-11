function initNotesForm() {
  $("select").ogameDropDown();
  $("#createNote .text").trigger("keyup");

  if ($("#popupContent").length) {
    initNotes();
  }
}
