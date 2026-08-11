function initBBCodes() {
  $(document)
    .undelegate(".spoilerHeader", "click")
    .delegate(".spoilerHeader", "click", function () {
      var thisObj = this;
      $(this)
        .next(".spoilerText")
        .toggle(0, function () {
          Tipped.refresh(thisObj);
        });
    });
}
