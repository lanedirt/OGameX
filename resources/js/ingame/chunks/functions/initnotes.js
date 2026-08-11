function initNotes() {
  $("select").ogameDropDown();

  function formHasChanges(form) {
    var hasChanges = false;
    $(form)
      .find(":input")
      .each(function () {
        if (typeof $(this).data("value") != "undefined") {
          if ($(this).data("value") != $(this).val()) {
            hasChanges = true;
          }
        }
      });
    return hasChanges;
  }

  var $overlayDiv = $(".overlayDiv.notices");
  $overlayDiv
    .find(".openOverlay")
    .unbind("click")
    .bind("click", function () {
      var overlayClass = $(this).attr("data-overlay-class");
      var options = {
        title: $(this).attr("data-title"),
        close: function () {
          var $thisObj = $("." + overlayClass);

          if (formHasChanges($thisObj.find("form"))) {
            errorBoxDecision(
              LocalizationStrings.question,
              locaNotes.changesNotSaved + "<br/><br/>" + locaNotes.questionSaveChanges,
              LocalizationStrings.yes,
              LocalizationStrings.no,
              function () {
                $thisObj.find("form").trigger("submit");
                $thisObj.remove();
              },
              function () {
                $thisObj.remove();
              },
              true,
            );
          } else {
            $thisObj.remove();
          }
        },
        class: overlayClass,
      };
      openOverlay($(this).attr("href"), options);

      if (overlayClass.indexOf("newNote-") === 0) {
        var number = parseInt(overlayClass.replace(/^newNote-/, "")) + 1;
        $(this).attr("data-overlay-class", "newNote-" + number);
      }

      return false;
    });
  $(document)
    .undelegate("#noteList form", "submit")
    .delegate("#noteList form", "submit", function () {
      $.post($("#noteList").attr("rel"), $(this).serialize(), function (data) {
        $overlayDiv.html(data);
      });
    })
    .undelegate("#createNote form [type=submit]", "click")
    .delegate("#createNote form [type=submit]", "click", function (e) {
      e.preventDefault();
      $(this).parents("form").submit();
    })
    .undelegate("#createNote form", "submit")
    .delegate("#createNote form", "submit", function (e) {
      e.preventDefault();
      var $thisObj = $(this);
      $.ajax({
        url: $(this).attr("rel"),
        type: "post",
        data: $thisObj.serialize(),
        dataType: "json",
        error: function () {
          fadeBox(LocalizationStrings.error, true);
        },
        success: function (data) {
          if (data.error != null) {
            fadeBox(data.error, true);
          } else {
            if ($("#popupContent").length) {
              $(window).unbind("beforeunload.checkChanges");
              location.href = $thisObj.attr("rel") + "&popup=1";
            } else {
              if ($thisObj.parents(".overlayDiv").is(":visible")) {
                $thisObj
                  .parents(".overlayDiv")
                  .dialog("option", "close", function () {
                    $(this).remove();
                  })
                  .dialog("close");
              }

              if (data.success != null) {
                fadeBox(data.success, false);
              }

              $overlayDiv.load($overlayDiv.find("#noteList").attr("rel"));
            }
          }
        },
      });
      return false;
    })
    .undelegate("#createNote .textBox", "keyup touchstart change")
    .delegate("#createNote .textBox", "keyup touchstart change", function () {
      var sum = $(this).val().length;
      var max = $(this).attr("data-max-length");

      if (sum > max) {
        var range = $(this).getSelection();
        $(this).val($(this).val().substr(0, max));
        sum = max;
        $(this).setSelection(range);
      }

      $(this).parents("form").find(".cntChars").text(sum);
    });
  $(window)
    .unbind("beforeunload.checkChanges")
    .bind("beforeunload.checkChanges", function () {
      var hasChanges = false;
      $("#createNote form").each(function () {
        if (formHasChanges(this)) {
          hasChanges = true;
        }
      });

      if (hasChanges) {
        return locaNotes.changesNotSaved;
      }
    });
}
