function show_hide_tbl(id) {
  var el = document.getElementById(id);

  try {
    if (el) el.style.display = el.style.display == "none" ? "table-row" : "none";
  } catch (e) {
    // Der IE bis V7 kann kein table-row, deshalb Fallback auf 'Block'
    el.style.display = "block";
  }
}
