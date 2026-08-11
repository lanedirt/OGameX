function initNetworkAjax() {
  var $myRiders = $(".reiter");

  if (!$.isFunction(clickFunction)) {
    var clickFunction = function () {
      $myRiders.removeClass("active");
      $(this).addClass("active");
      ajaxLoad($(this).attr("id"), 1);
    };
  }

  $myRiders.off("click");
  $myRiders.click(clickFunction);
  $("#checkAll")
    .off("click")
    .click(function () {
      $(".checker").prop("checked", $(this).is(":checked"));
    });

  function hide(id) {
    $("#TR" + id).hide();
  }

  $(".overlay").click(function () {
    var msg_id = $(this).attr("id");
    markAsRead(msg_id);
  });
  $("#messageContent select").change(function () {
    if (typeof $("select option:selected").attr("id") == "undefined") {
      $(".buttonOK").hide();
      mod = "";
    } else {
      $(".buttonOK").show();
      mod = $("select option:selected").attr("id");
    }
  });
  $(".del").click(function () {
    mod = $(this).attr("id");
  });
  $(".underlined").click(function () {
    $(".buttonOK").hide();
  });
  reduceMsgCount(aktCat);
}
