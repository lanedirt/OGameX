function submitOnEnter(ev) {
  // Number 13 is the "Enter" key on the keyboard
  if (ev.key === "Enter") {
    // Cancel the default action, if needed
    ev.preventDefault(); // Trigger the button element with a click

    trySubmit();
    return false;
  } else {
    return true;
  }
}
