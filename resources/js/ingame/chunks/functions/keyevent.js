function keyevent(ev) {
  let keyCode;
  let focusElement = $(":focus");

  if (focusElement.closest(".ui-dialog").length) {
    return true;
  }

  if (focusElement.closest(".chat_box_textarea").length) {
    return true;
  }

  if (ev) {
    keyCode = ev.key;
  } else {
    return true;
  }

  submitOnKey(keyCode);
}
