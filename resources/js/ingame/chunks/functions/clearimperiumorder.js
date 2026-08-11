/**
 * Reset the current sort order
 *
 */

function clearImperiumOrder() {
  $.ajax({
    url: saveUrl,
    method: "post",
    dataType: "json",
    data: {
      ajax: 1,
      type: "reset",
    },
    success: function (data) {
      if (!data.error) {
        location.reload();
      }
    },
  });
}
