function focusOnTabChange(element, focusOnReady) {
  var focusFunction = function () {
    $(element).focus();
  };

  if (focusOnReady == true) {
    $(document).ready(focusFunction);
  }

  $(window).unbind("blur").bind("blur", focusFunction);
}
