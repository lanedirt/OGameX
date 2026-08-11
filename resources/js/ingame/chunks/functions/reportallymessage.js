function reportAllyMessage(id, fromPlayer) {
  $.ajax({
    type: "POST",
    url: "?page=reportSpam_ajax",
    dataType: "json",
    data: {
      messageId: id,
      from: fromPlayer,
    },
    success: function (data) {
      fadeBox(data.message, !data.result);
    },
    error: function () {},
  });
}
