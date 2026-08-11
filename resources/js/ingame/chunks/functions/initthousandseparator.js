function initThousandSeparator() {
  $(".checkThousandSeparator")
    .each(function (e) {
      formatNumber(this, this.value);
    })
    .on("keydown", function (event) {
      var range = $(this).getSelection();

      if (range.length === 0) {
        var text = $(this).val();

        if (event.which === 8 && text.substr(range.start - 1, 1) === LocalizationStrings["thousandSeperator"]) {
          range.start -= 1;
          range.end -= 1;
          $(this).setSelection(range);
        }

        if (event.which === 46 && text.substr(range.start, 1) === LocalizationStrings["thousandSeperator"]) {
          range.start += 1;
          range.end += 1;
          $(this).setSelection(range);
        }
      }
    })
    .on("keyup", function (event) {
      formatNumber(this, this.value);
    });
}
