function getKeyCode(e) {
  if (window.event) {
    return window.event.keyCode;
  } else if (e) {
    return e.which;
  }

  return null;
}
