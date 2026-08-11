function ajaxCall(url, targetSelector, callback) {
  if (typeof targetSelector === "string") {
    let $targetHTMLObj = $(targetSelector);
    $targetHTMLObj.find("select").ogameDropDown("destroy");
    $targetHTMLObj.html('<p class=\"ajaxLoad\"></p>');
  }

  $.post(url, function (data) {
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
