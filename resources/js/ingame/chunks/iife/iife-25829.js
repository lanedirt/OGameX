(function ($) {
  $.fn.extend({
    ogameDropDown: function (functionName, preventScrollSelection) {
      if ($("body.showOldDropdowns").length) {
        return this;
      }

      function positionList($ul) {
        var ulHeight = $ul.outerHeight();
        var $dropdown = $('.dropdown.currentlySelected[rel="' + $ul.attr("id") + '"]');

        if ($dropdown.length) {
          var left = Math.min(
            $(window).innerWidth() + $(window).scrollLeft() - $ul.width() - 3,
            $dropdown.offset().left,
          );
          var top;
          var dropdownTop = Math.ceil($dropdown.offset().top);

          if (
            dropdownTop + $dropdown.height() + ulHeight + $("#siteFooter").outerHeight() >=
            $(window).innerHeight() + $(window).scrollTop()
          ) {
            top = dropdownTop - ulHeight + 1;
          } else {
            top = dropdownTop + $dropdown.height() + 1;
          }

          $ul.css("left", left).css("top", top).css("min-width", $dropdown.width());
        }
      }

      var functions = {
        destroy: function () {
          $(this)
            .filter("select.dropdownInitialized")
            .each(function () {
              var $this = $(this);
              $('.dropdown[rel="' + $this.data("dropdownId") + '"]').remove();
              $("ul#" + $this.data("dropdownId")).remove();
              $this.removeClass("dropdownInitialized").data("dropdownId", "").show();
            });
        },
        hide: function () {
          $(this)
            .filter("select.dropdownInitialized")
            .each(function () {
              var $currentlySelected = $('.currentlySelected[rel="' + $(this).data("dropdownId") + '"]');
              $currentlySelected.find("a").removeClass("hover");
              $(".dropdownList#" + $currentlySelected.attr("rel")).hide();
            });
        },
        reposition: function () {
          $(this)
            .filter("select.dropdownInitialized")
            .each(function () {
              positionList($("#" + $(this).data("dropdownId")));
            });
        },
        refresh: function () {
          var selected = $(this).find("option[selected]"); // get selected <option>

          var href = getIEVersion() < 999 ? "#" : "javascript:void(0);";
          var $currentItem = $(
            'a[class="' + selected.attr("class") + '"][rel="' + $(this).data("dropdownId") + '"]',
          ).text(selected.text());
        },
        select: function (value) {
          $(this).find("option").prop("selected", false).removeAttr("selected");

          if (typeof value == "string" && value.length > 0) {
            // select value and refresh
            let selected = $(this).find("option[value='" + value + "']");

            if (selected.length > 0) {
              selected.prop("selected", true).attr("selected", "selected");
              functions.refresh.call(this);
              return;
            }
          } // select default value and refesh

          let defaultOption = $(this.find("option[value='-']"));

          if (defaultOption.length > 0) {
            defaultOption.prop("selected", true).attr("selected", "selected");
            functions.refresh.call(this);
          }
        },
      };

      if (typeof functionName == "string") {
        if (typeof functions[functionName] == "function") {
          let args = Array.prototype.slice.call(arguments);
          args.shift();
          functions[functionName].apply(this, args);
        }

        return this;
      }

      $(this)
        .filter("select:not(.dropdownInitialized)")
        .each(function () {
          var $this = $(this);
          var randomId = "dropdown" + Math.floor(Math.random() * 1000);
          var selected = $this.find("option[selected]"); // get selected <option>

          if (selected.length == 0) {
            selected = $this.find("option:first-child");
          }

          var href = getIEVersion() < 999 ? "#" : "javascript:void(0);";
          var $currentItem = $(
            '<a class="' +
              selected.attr("class") +
              '" data-value="' +
              selected.val() +
              '" rel="' +
              randomId +
              '" href="' +
              href +
              '">' +
              selected.text() +
              "</a>",
          );
          var $dropdown = $(
            '<span class="dropdown currentlySelected ' + $this.attr("class") + '" rel="' + randomId + '"></span>',
          )
            .append($currentItem)
            .width($this.css("width").length ? $this.css("width") : $this.width())
            .data("selectElement", $this);

          if ($this.is("[readonly]") || $this.is(":disabled")) {
            $dropdown.addClass("disabled");
          }

          $this.after($dropdown).hide().addClass("dropdownInitialized").data("dropdownId", randomId); // iterate through all the <option> elements and create UL

          var $ul = $('<ul class="dropdown dropdownList" id="' + randomId + '"></ul>').delegate(
            "a",
            "click",
            function (e) {
              e.stopPropagation();

              if ($(this).attr("disabled") === "disabled") {
                return;
              }

              $currentItem
                .html($(this).html())
                .attr("class", $(this).attr("class"))
                .attr("data-value", $(this).attr("data-value"));
              $ul.hide().find("a").removeClass("focus");
              $(this).addClass("focus");
              var value = $(this).attr("data-value");
              $this
                .val($(this).attr("data-value"))
                .trigger("change")
                .find('option[value="' + value + '"], option:contains("' + value + '")')
                .trigger("click");
            },
          );

          function initialize() {
            if ($this.is($this.is("[readonly]") || ":disabled")) {
              return;
            }

            if (!$ul.hasClass("initialized")) {
              $ul.addClass("initialized");
              $this.find("option").each(function () {
                var html = $(this).html();
                var disabled = "";
                var title = "";

                if (typeof $(this).attr("data-html") != "undefined") {
                  html = $(this).attr("data-html");
                }

                if (typeof $(this).attr("data-html-prepend") != "undefined") {
                  html = $(this).attr("data-html-prepend") + html;
                }

                if (typeof $(this).attr("data-html-append") != "undefined") {
                  html += $(this).attr("data-html-append");
                }

                if (typeof $(this).attr("title") != "undefined") {
                  title = 'title="' + $(this).attr("title") + '" ';
                }

                if (typeof $(this).attr("disabled") != "undefined") {
                  disabled = 'disabled="' + $(this).attr("disabled") + '" ';
                }

                $li = $(
                  "<li><a " +
                    'class="' +
                    $(this).attr("class") +
                    '" ' +
                    disabled +
                    title +
                    'data-value="' +
                    $(this).val() +
                    '">' +
                    html +
                    "</a></li>",
                );
                $ul.append($li);

                if ($(this).is(":selected")) {
                  $li.find("a").addClass("focus");
                }
              });
            }
          }

          $currentItem
            .bind("focus", function (e) {
              $(".dropdownList").not($ul).hide();
              $(".dropdown.currentlySelected").removeClass("focus");
              $(this).addClass("hover");
              $dropdown.addClass("focus");
              initialize();
            })
            .bind("mousewheel", function (e) {
              initialize();
              $(this).unbind("mousewheel");
            })
            .bind("click", function (e) {
              e.preventDefault();

              if ($this.is($this.is("[readonly]") || ":disabled")) {
                return;
              }

              $(".dropdownList").not($ul).hide();
              $(".dropdown.currentlySelected").removeClass("focus");
              $dropdown.addClass("focus");

              if ($ul.is(":hidden")) {
                $(this).addClass("hover");
                initialize();
                positionList($ul);
                $(window)
                  .unbind("resize.dropdown" + randomId)
                  .bind("resize.dropdown" + randomId, function () {
                    positionList($ul);
                  });
                $ul.show();

                if ($ul.hasScrollbar()) {
                  $ul.find("a").css("padding-right", 22);
                }
              } else {
                $(this).removeClass("hover");
                $(window).unbind("resize.dropdown" + randomId);
                $ul.hide();
              }
            });
          $("body").append($ul);
        });
      var currentlyTyped = "";
      var currentlyTypedInterval;

      function updateCurrentlyTyped(text, e) {
        currentlyTyped = text;
        clearTimeout(currentlyTypedInterval);
        currentlyTypedInterval = setTimeout(function () {
          currentlyTyped = "";
        }, 1500);
        var $list = $(".dropdownList:visible");
        var instantSelect = false;

        if ($list.length == 0) {
          var $focusedElement = $(".dropdown.currentlySelected.focus");

          if ($focusedElement.length) {
            $list = $("#" + $focusedElement.attr("rel"));
            instantSelect = true;
          } else {
            return;
          }
        }

        var lowerText = currentlyTyped.toLowerCase();
        var $focusElement = $list.find("a").filter(function () {
          if ($(this).attr("data-value").toLowerCase().indexOf(lowerText) == 0) {
            return true;
          }

          return $(this).text().trim().toLowerCase().indexOf(lowerText) == 0;
        });

        if ($focusElement.length) {
          e.preventDefault();
          $list.find("a").removeClass("focus");
          $($focusElement.get(0)).addClass("focus").focus();

          if (instantSelect) {
            $focusElement.click();
          }
        } else {
          clearTimeout(currentlyTypedInterval);
          currentlyTyped = "";
        }
      }

      $(document) // hide the dropdown
        .undelegate("html", "touchstart.dropdown click.dropdown")
        .delegate("html", "touchstart.dropdown click.dropdown", function (e) {
          if ($(e.target).closest(".dropdown").length == 0) {
            $(".dropdownList").hide(); //$(".currentlySelected a").removeClass('hover');
            //$(".currentlySelected").removeClass('focus');
          }
        })
        .undelegate(".dropdown", "mousewheel.dropdown")
        .delegate(".dropdown", "mousewheel.dropdown", function (e, delta) {
          if (preventScrollSelection) {
            return;
          }

          e.preventDefault();
          var $target = $(e.target).closest(".dropdown");
          var $list;

          if ($target.hasClass("currentlySelected")) {
            $list = $("#" + $target.attr("rel"));
          } else {
            $list = $target;
          }

          var $currentItem = $('[rel="' + $list.attr("id") + '"] a');
          var $focussed = $list.find("a:focus");

          if ($focussed.length == 0) {
            $focussed = $list.find("a.focus");
          }

          if ($focussed.length == 0) {
            $focussed = $list.find('a[data-value="' + $currentItem.attr("data-value") + '"]');
          }

          var $focussedListElement = $focussed.parent();
          var $focusElement = null;
          var amount = Math.abs(delta);

          if (delta > 0) {
            for (var amountCounter = 0; amountCounter < amount; amountCounter++) {
              if ($focussedListElement.is(":first-child")) {
                $focusElement = $focussedListElement.find("a");
                break;
              } else {
                $focusElement = $focussedListElement.prev().find("a");
              }

              $focussedListElement = $focusElement.parent();
            }
          } else {
            for (var amountCounter = 0; amountCounter < amount; amountCounter++) {
              if ($focussedListElement.is(":last-child")) {
                $focusElement = $focussedListElement.find("a");
                break;
              } else {
                $focusElement = $focussedListElement.next().find("a");
              }

              $focussedListElement = $focusElement.parent();
            }
          }

          if ($focusElement != null) {
            $focussedListElement = $focusElement.parent();
            $list.find("a").removeClass("focus");
            $focusElement.addClass("focus").focus();

            if ($list.is(":hidden")) {
              $focusElement.click();
            } else {
              var currentPosition = $focussedListElement.position().top;

              if (currentPosition < 0) {
                $list.scrollTop($list.scrollTop() + currentPosition);
              } else if (currentPosition + $focussedListElement.outerHeight() > $list.innerHeight()) {
                $list.scrollTop(
                  $list.scrollTop() + currentPosition + $focussedListElement.outerHeight() - $list.innerHeight(),
                );
              }
            }
          }
        })
        .undelegate("*", "focus.dropdown")
        .delegate("*", "focus.dropdown", function (e) {
          if ($(e.target).closest(".dropdown").length == 0) {
            $(".currentlySelected a").removeClass("hover");
            $(".currentlySelected").removeClass("focus");
          }
        }) // get keyboard arrow keys and backspace typing
        .unbind("keydown.dropdown")
        .bind("keydown.dropdown ", function (e) {
          if ($(":focus").length > 0 && $(":focus").parents(".dropdown").length == 0) {
            return;
          }

          var instantSelect = false;
          var $list = $(".dropdownList:visible");

          if ($list.length == 0) {
            var $focusedElement = $(".dropdown.currentlySelected.focus");

            if ($focusedElement.length) {
              $list = $("#" + $focusedElement.attr("rel"));
              instantSelect = true;
            } else {
              return;
            }
          }

          var $currentItem = $('[rel="' + $list.attr("id") + '"] a');
          var $focussed = $list.find("a:focus");

          if ($focussed.length == 0) {
            $focussed = $list.find("a.focus");
          }

          if ($focussed.length == 0) {
            $focussed = $list.find('a[data-value="' + $currentItem.attr("data-value") + '"]');
          }

          var $focussedListElement = $focussed.parent();
          var $focusElement = null; // arrow keys

          if (e.keyCode == KeyEvent.DOM_VK_UP || e.keyCode == KeyEvent.DOM_VK_DOWN) {
            if (e.keyCode == KeyEvent.DOM_VK_UP) {
              if ($focussedListElement.is(":first-child")) {
                $focusElement = $focussedListElement.find("a");
              } else {
                $focusElement = $focussedListElement.prev().find("a");
              }

              $focussedListElement = $focusElement.parent();
            } else {
              if ($focussedListElement.is(":last-child")) {
                $focusElement = $focussedListElement.find("a");
              } else {
                $focusElement = $focussedListElement.next().find("a");
              }

              $focussedListElement = $focusElement.parent();
            }

            e.preventDefault();
          } else if (e.keyCode == KeyEvent.DOM_VK_BACK_SPACE) {
            // simulate keyboard typing
            updateCurrentlyTyped(currentlyTyped.substring(0, currentlyTyped.length - 1), e);
          } else if (e.keyCode == KeyEvent.DOM_VK_RETURN) {
            e.preventDefault();

            if (instantSelect) {
              $currentItem.parents("form").submit();
              return;
            } else {
              instantSelect = true;
              $focusElement = $focussed;
            }
          } else if (e.keyCode == KeyEvent.DOM_VK_ESCAPE) {
            $focusElement = $list.find('[data-value="' + $currentItem.attr("data-value") + '"]');
            instantSelect = true;
          }

          if ($focusElement != null) {
            $focussedListElement = $focusElement.parent();
            $list.find("a").removeClass("focus");
            $focusElement.addClass("focus").focus();

            if (instantSelect) {
              $focusElement.click();
            } else {
              var currentPosition = $focussedListElement.position().top;

              if (currentPosition < 0) {
                $list.scrollTop($list.scrollTop() + currentPosition);
              } else if (currentPosition + $focussedListElement.outerHeight() > $list.innerHeight()) {
                $list.scrollTop(
                  $list.scrollTop() + currentPosition + $focussedListElement.outerHeight() - $list.innerHeight(),
                );
              }
            }
          }
        }) // simulate keyboard typing
        .unbind("keypress.dropdown")
        .bind("keypress.dropdown", function (e) {
          if (($(":focus").length > 0 && $(":focus").parents(".dropdown").length == 0) || e.charCode == 0) {
            return;
          }

          updateCurrentlyTyped(currentlyTyped + String.fromCharCode(e.charCode), e);
        });
      return $(this);
    },

    /**
     * @url http://stackoverflow.com/a/9217301/1386610
     */
    selectText: function () {
      var range,
        selection,
        obj = this[0],
        type = {
          func: "function",
          obj: "object",
        },
        // Convenience
        is = function (type, o) {
          return typeof o === type;
        };

      if (
        is(type.obj, obj.ownerDocument) &&
        is(type.obj, obj.ownerDocument.defaultView) &&
        is(type.func, obj.ownerDocument.defaultView.getSelection)
      ) {
        selection = obj.ownerDocument.defaultView.getSelection();

        if (is(type.func, selection.setBaseAndExtent)) {
          // Chrome, Safari - nice and easy
          selection.setBaseAndExtent(obj, 0, obj, $(obj).contents().size());
        } else if (is(type.func, obj.ownerDocument.createRange)) {
          range = obj.ownerDocument.createRange();

          if (
            is(type.func, range.selectNodeContents) &&
            is(type.func, selection.removeAllRanges) &&
            is(type.func, selection.addRange)
          ) {
            // Mozilla
            range.selectNodeContents(obj);
            selection.removeAllRanges();
            selection.addRange(range);
          }
        }
      } else if (is(type.obj, document.body) && is(type.obj, document.body.createTextRange)) {
        range = document.body.createTextRange();

        if (is(type.obj, range.moveToElementText) && is(type.obj, range.select)) {
          // IE most likely
          range.moveToElementText(obj);
          range.select();
        }
      } // Chainable

      return this;
    },
    hasScrollbar: function () {
      return this.get(0).scrollHeight > this.innerHeight();
    },
  });
})(jQuery);
