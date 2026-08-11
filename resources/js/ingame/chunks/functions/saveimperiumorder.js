/**
 * Save the current sort order to the cookie
 *
 * @param string destination
 */
function saveImperiumOrder(destination, isMoon) {
  var typeName = "impSortOrder";

  if (isMoon) {
    typeName = "impSortOrderMoon";
  }

  $.ajax({
    url: saveUrl,
    method: "post",
    dataType: "json",
    data: {
      ajax: 1,
      type: typeName,
      planets: $(destination).sortable("toArray"),
    },
  });
}
