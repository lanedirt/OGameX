function handleInputForResourcePackages(e) {
  let regex;

  if (LocalizationStrings.thousandSeperator === ".") {
    regex = new RegExp("\\" + LocalizationStrings.thousandSeperator, "g");
  } else {
    regex = new RegExp(LocalizationStrings.thousandSeperator, "g");
  }

  let $input = $(e.target),
    val = parseInt($input.val().replace(regex, "")) || 0,
    original = $input.data("original"),
    modified = false;

  if (e.which !== 75 && e.which >= 65 && e.which <= 90) {
    // prevent a-z. "k" is handled before.
    val = 0;
    modified = true;
  }

  if (val > original) {
    val = original;
    modified = true;
  }

  if (modified === true) {
    $input.val(tsdpkt(val));
  }

  updateCostsAfterUserModification($input);
}
