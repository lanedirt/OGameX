function ajaxSubmit(url, formDataOrSelector, targetSelector, callback) {
  if (typeof targetSelector === "string") {
    let $targetHTMLObj = $(targetSelector);
    $targetHTMLObj.find("select").ogameDropDown("destroy");
    $targetHTMLObj.html('<p class="ajaxLoad"><?=LOCA_ALL_AJAXLOAD ?></p>');
  }

  let formData = typeof formDataOrSelector === "string" ? $(formDataOrSelector).serialize() : formDataOrSelector;
  $.post(url, formData, function (data) {
    if (typeof targetSelector === "string") {
      let $targetHTMLObj = $(targetSelector);
      $targetHTMLObj.html(data);
      $targetHTMLObj.find("select").ogameDropDown();
    }

    if (typeof callback === "function") {
      callback();
    }
  });
}
