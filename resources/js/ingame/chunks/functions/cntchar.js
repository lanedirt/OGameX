function cntchar(inputField, m) {
  var $inputField = $(inputField);

  if ($inputField.val().length > m) {
    $inputField.val($inputField.val().substr(0, m));
  }

  $inputField.parents("form").find(".cntChars").text($inputField.val().length);
}
