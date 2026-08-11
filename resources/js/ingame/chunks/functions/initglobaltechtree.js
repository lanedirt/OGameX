function initGlobalTechtree(id) {
  var $techtree = $("div.graph[data-id='" + id + "']");
  $techtree
    .find(".headline")
    .unbind("click")
    .bind("click", function () {
      $(this)
        .next()
        .toggle(function () {
          var $dialog = $techtree.parents(".ui-dialog");
          $dialog.hide();
          $(this).toggleClass("open");
          $dialog.show();
        });
      /*$(this).next().slideToggle("slow", function() {
        $(this).toggleClass("open");
         $dialog.css('zoom', 1.1);
        setTimeout(function() {
            $dialog.css('zoom', 1);
        }, 1000);
    });*/
    });

  if (openTree == "all") {
    $techtree.find(".techtree_content").show(0, function () {
      $(this).addClass("open");
    });
  } else if (openTree != null) {
    $techtree.find(".techtree_content_" + openTree).show(0, function () {
      $(this).addClass("open");
    });
  }
}
