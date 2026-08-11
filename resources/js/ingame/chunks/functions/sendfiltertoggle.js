function sendFilterToggle(id, state) {
  $.ajax({
    type: "POST",
    url: "?page=togglefilter",
    dataType: "json",
    data: {
      id: id,
      state: state,
    },
    success: function (data) {},
    error: function () {},
  });
}
