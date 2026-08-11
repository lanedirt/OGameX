function initHideElements() {
  $(document)
    .undelegate("html", "touchstart.hideElem click.hideElem")
    .delegate("html", "touchstart.hideElem click.hideElem", function (e) {
      e.stopPropagation();

      if ($(this).data("noclick")) {
        return;
      }

      if (isMobile) {
        var targetTagName = e.target.tagName.toUpperCase();

        if (!(targetTagName === "TEXTAREA" || targetTagName === "INPUT" || targetTagName === "SELECT")) {
          document.activeElement.blur();
        }

        if (!$(e.target).parents(".markItUpHeader ul").length) {
          $(".markItUpHeader ul ul").hide();
        }
      } else {
        if ($(e.target).parents(".ui-dialog").length || $(e.target).parents(".tpd-tooltip").length) {
          // don't hide overlays when click was inside overlay itself or a tooltip
          // (tooltips might be part of the overlay even though they're technically located outside)
          return;
        }

        var $overlayDivs = $(".overlayDiv");

        if (typeof $overlayDivs.data("uiDialog") != "undefined") {
          // schliesse auch die Dropdowns sofern vorhanden
          var dropDowns = $overlayDivs.find(".markItUpDropMenu[id]");

          for (var i = 0; i < dropDowns.length; ++i) {
            var $innerUl = $("body>ul[rel=" + dropDowns[i].id + "]");
            $innerUl.hide();
          }

          $overlayDivs.dialog("close");

          if ($("#FederationLayer").length > 0) {
            $("#FederationLayer").remove();
          }
        }
      }
    });
}
