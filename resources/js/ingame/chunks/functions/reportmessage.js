function reportMessage(id, fromPlayer, toPlayer) {
  $.ajax({
    type: "POST",
    url: "?page=reportSpam_ajax",
    dataType: "json",
    data: {
      messageId: id,
      from: fromPlayer,
      to: toPlayer,
    },
    success: function (data) {
      fadeBox(data.message, !data.result);
    },
    error: function () {},
  });
}
