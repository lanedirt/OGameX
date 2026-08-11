function errorBoxNotify(head, content, ok, okHandler, useHashCharacter) {
  var useHash = getIEVersion() <= 9 && (useHashCharacter || false);
  var errorBox = $("#errorBoxNotify");
  errorBox.find("#errorBoxNotifyHead").html(head);
  errorBox.find("#errorBoxNotifyContent").html(content);

  var okFunction = function (e) {
    e.stopPropagation();
    errorBox.dialog("destroy");

    if (typeof okHandler == "function") {
      okHandler();
    } else if (typeof window[okHandler] == "function") {
      window[okHandler]();
    }
  };

  var $okButton = errorBox.find(".ok");
  $okButton.unbind("click").bind("click", okFunction).find("#errorBoxNotifyOk").html(ok);

  if (useHash) {
    $okButton.attr("href", "#");
  } else {
    $okButton.attr("href", "javascript:void(0);");
  }

  Tipped.hideAll();
  errorBox.dialog({
    resizable: false,
    modal: true,
    title: head,
    close: okFunction,
    width: 400,
    dialogClass: "errorBox",
  });
}
