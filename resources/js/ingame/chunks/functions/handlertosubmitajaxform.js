function handlerToSubmitAjaxForm(form) {
  var submitFunction = "submit_" + String(form);

  if ($.isFunction(window[submitFunction])) {
    window[submitFunction]();
  }

  return false;
}
