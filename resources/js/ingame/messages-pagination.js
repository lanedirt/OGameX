(function () {
  var originalInitDetailMessages = ogame.messages.initDetailMessages;

  ogame.messages.initDetailMessages = function (commentsAllowed) {
    originalInitDetailMessages.call(ogame.messages, commentsAllowed);

    $(".overlayDiv ul.pagination")
      .off("click.msgPagination")
      .on("click.msgPagination", "li.p_li a", function (e) {
        e.preventDefault();
        e.stopPropagation();

        if ($(this).hasClass("disabled")) {
          return false;
        }

        var messageId = $(this).data("messageid");
        var tab = $(this).data("tab");
        var subtab = $(this).data("subtab");

        if (!messageId) {
          return false;
        }

        var url = "/ajax/messages/" + messageId;
        if (tab) {
          url += "?tab=" + encodeURIComponent(tab);
          if (subtab) {
            url += "&subtab=" + encodeURIComponent(subtab);
          }
        }

        var $overlayDiv = $(this).closest(".overlayDiv");
        if ($overlayDiv.length === 0) {
          $overlayDiv = $(".overlayDiv");
        }

        var $uiDialog = $overlayDiv.closest(".ui-dialog");
        var dialogWidth = $uiDialog.length ? $uiDialog.width() : null;
        var dialogHeight = $uiDialog.length ? $uiDialog.height() : null;
        var dialogPosition = $uiDialog.length ? $uiDialog.position() : null;

        $.get(url, function (response) {
          try {
            removeTooltip($overlayDiv.find(getTooltipSelector()));
          } catch (err) {}

          $overlayDiv.empty().append(response);

          if ($uiDialog.length && dialogWidth && dialogHeight) {
            $uiDialog.css({
              width: dialogWidth,
              height: dialogHeight,
            });
            if (dialogPosition) {
              $uiDialog.css({
                top: dialogPosition.top,
                left: dialogPosition.left,
              });
            }
            $uiDialog.hide().show();
          }

          ogame.messages.initDetailMessages(true);

          try {
            initTooltips();
          } catch (err) {}
        }).fail(function () {
          if (typeof fadeBox === "function" && typeof loca !== "undefined") {
            fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
          }
        });

        return false;
      });
  };
})();
