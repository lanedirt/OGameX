function submit_unionform() {
  setUnionUsers();
  ajaxFormSubmit("unionform", $("form#unionform").attr("action"), unionEdit);
}
