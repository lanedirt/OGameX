(function ($) {
  LazyLoader = {
    pendingCssFiles: [],
    loadedCssFiles: [],
    pendingJsFiles: [],
    loadedJsFiles: [],
    _downloadCompleteHandler: function (type, path) {
      switch (type) {
        case "css":
          LazyLoader.pendingCssFiles = $.grep(LazyLoader.pendingCssFiles, function (value) {
            return value != path;
          });
          LazyLoader.loadedCssFiles.push(path);

          if (LazyLoader.pendingCssFiles.length === 0) {
            $(document).trigger("cssComplete");
          }

          break;

        case "js":
          LazyLoader.pendingJsFiles = $.grep(LazyLoader.pendingJsFiles, function (value) {
            return value != path;
          });
          LazyLoader.loadedJsFiles.push(path);

          if (LazyLoader.pendingJsFiles.length === 0) {
            $(document).trigger("jsComplete");
          }

          break;
      }

      if (LazyLoader.pendingCssFiles.length === 0 && LazyLoader.pendingJsFiles.length === 0) {
        $(document).trigger("allComplete");
      }
    },
    _loadCssFiles: function (cssFiles) {
      var linkTags = [];
      $.each(cssFiles, function (key, value) {
        if ($.inArray(value, LazyLoader.pendingCssFiles) > -1 || $.inArray(value, LazyLoader.loadedCssFiles) > -1) {
          return true;
        }

        LazyLoader.pendingCssFiles.push(value);
        linkTags.push(
          $("<link />")
            .attr("href", value)
            .attr("rel", "stylesheet")
            .on(
              "load",
              {
                path: value,
              },
              function (event) {
                LazyLoader._downloadCompleteHandler("css", event.data.path);
              },
            ),
        );
      });

      if (linkTags.length === 0) {
        return {
          status: "done",
        };
      }

      $(linkTags).map($.fn.toArray).appendTo("head");
      return {
        status: "queued",
      };
    },
    _loadJsFiles: function (jsFiles) {
      var newScripts = false;
      $.each(jsFiles, function (key, value) {
        if ($.inArray(value, LazyLoader.pendingJsFiles) > -1 || $.inArray(value, LazyLoader.loadedJsFiles) > -1) {
          return true;
        }

        newScripts = true;
        LazyLoader.pendingJsFiles.push(value);
        $.ajax({
          cache: true,
          url: value,
          dataType: "script",
        }).success(function () {
          LazyLoader._downloadCompleteHandler("js", value);
        });
      });

      if (!newScripts) {
        return {
          status: "done",
        };
      }

      return {
        status: "queued",
      };
    },
    loadFiles: function (cssFiles, jsFiles) {
      var loadCssFiles = LazyLoader._loadCssFiles(cssFiles);

      var loadJsFiles = LazyLoader._loadJsFiles(jsFiles);

      if (loadCssFiles.status === "done" && loadJsFiles.status === "done") {
        $(document).trigger("allComplete");
      }
    },
  };
  $(document).ready(function () {
    $("#mainmenucomponent li.has-sub > a").on("click", function () {
      $(this).removeAttr("href");
      var element = $(this).parent("li");

      if (element.hasClass("open")) {
        element.removeClass("open");
        element.find("li").removeClass("open");
        element.find("ul").slideUp(50);
      } else {
        element.addClass("open");
        element.children("ul").slideDown(50);
        element.siblings("li").children("ul").slideUp(50);
        element.siblings("li").removeClass("open");
        element.siblings("li").find("li").removeClass("open");
        element.siblings("li").find("ul").slideUp(50);
      }
    });
  });
})(jQuery);
