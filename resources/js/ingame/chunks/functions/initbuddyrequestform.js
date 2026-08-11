function initBuddyRequestForm() {
  $(".overlayDiv .buddyRequest form")
    .unbind("submit")
    .bind("submit", function (e) {
      var $thisObj = $(this);
      e.preventDefault();
      $.post($thisObj.attr("action"), $thisObj.serialize(), "html")
        .done(function (data) {
          document.open();
          document.write(data);
          document.close();
        })
        .fail(function () {
          var currentlocation = window.location.href;
          window.location =
            currentlocation.substring(0, currentlocation.indexOf("?")) + "?page=ingame&component=buddies";
        })
        .always(function () {
          return false;
        });
    });
  $(".buddyRequest").each(function () {
    var $thisObj = $(this);
    var $overlayTitle = $thisObj.parents(".ui-dialog").find(".ui-dialog-title");

    if (!$overlayTitle.find("span.buddyName").length) {
      $overlayTitle.append(
        $(document.createElement("span"))
          .addClass("buddyName")
          .text(" (" + $thisObj.attr("data-title") + ")"),
      );
    }
  });
}
