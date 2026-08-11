function initOverlays() {
  $(document)
    .undelegate('a[href*="overlay=1"], button[data-target*="overlay=1"], a.overlay, button.overlay', "click")
    .delegate(
      'a[href*="overlay=1"], button[data-target*="overlay=1"], a.overlay, button.overlay',
      "click",
      function (e) {
        e.preventDefault();
        var url = $(this).attr("href") || $(this).attr("data-target");

        if (typeof $(this).data("overlay-token") !== "undefined") {
          url += "&token=" + $(this).data("overlay-token");
        }

        if ($(this).data("overlay-same")) {
          var $uiDialog = $(this).parents(".ui-dialog");
          var $overlayDiv = $uiDialog.find(".overlayDiv");

          if ($(this).data("overlay-same") && $overlayDiv.length > 0) {
            $.get(url, {}, function (data) {
              removeTooltip($overlayDiv.find(getTooltipSelector()));
              $overlayDiv
                .empty() // force repaint (ie 8 bug q.q)
                .append(data) // force repaint (ie 8 bug -.-)
                .dialog("moveToTop");
              $overlayDiv.dialog("option", "position", $overlayDiv.dialog("option", "position"));
              $uiDialog.hide(); // force repaint (ie 9/10 bug ~=[,,_,,]:3)

              $uiDialog.show(); // force repaint...  ie 9/10 bug (/¯◡ ‿ ◡)/¯ ~ ┻━┻
            });
            return false;
          }
        }

        var dialogParams = {
          zIndex: 4000,
        };

        if ($(this).data("overlay-title")) {
          dialogParams.title = $(this).data("overlay-title");
        } else if (typeof $(this).attr("title") != "undefined" && $(this).attr("title").length) {
          dialogParams.title = $(this).attr("title");
        } else if ($(this).data("tipped_restore_title")) {
          dialogParams.title = $(this).data("tipped_restore_title").replace(/^.+\|/, "");
        }

        if ($(this).data("overlay-class")) {
          dialogParams["class"] = $(this).data("overlay-class");
        }

        if ($(this).data("overlay-width")) {
          dialogParams.width = $(this).data("overlay-width");
        }

        if ($(this).data("overlay-height")) {
          dialogParams.height = $(this).data("overlay-height");
        }

        if ($(this).data("overlay-popup-width")) {
          dialogParams.popupWidth = $(this).data("overlay-popup-width");
        }

        if ($(this).data("overlay-popup-height")) {
          dialogParams.popupHeight = $(this).data("overlay-popup-height");
        }

        if ($(this).data("overlay-modal")) {
          dialogParams.modal = $(this).data("overlay-modal");
          dialogParams.resizable = false;
          dialogParams.draggable = false;
        }

        if ($(this).data("overlay-iframe")) {
          dialogParams.type = "iframe";

          if ($(this).data("iframe-width")) {
            dialogParams.iframeWidth = $(this).data("iframe-width");
          }

          if ($(this).data("iframe-height")) {
            dialogParams.iframeHeight = $(this).data("iframe-height");
          }
        } else if ($(this).data("overlay-inline")) {
          dialogParams.type = "inline";
          url = $(this).data("overlay-inline");
        }

        if ($(this).data("overlay-close")) {
          dialogParams.close = $(this).data("overlay-close");
        }

        openOverlay(url, dialogParams);
        return false;
      },
    );
}
