ogame.retrieveEmail = {
  send: function () {
    $.ajax({
      type: "POST",
      url:
        window.location.href +
        "&" +
        $.param({
          action: "get",
        }),
      data: {
        username: $("#username").val(),
        password: $("#password").val(),
      },
      dataType: "json",
      success: function (data) {
        $("#response").html(data.response).removeClass().addClass(data.type);
      },
    });
  },
};
