function initJumpgate() {
  $("select").ogameDropDown();
  $(".list tr:even").addClass("alt");
  $(document)
    .undelegate("#jumpgateForm .ship_input_row .textinput", "keyup change input")
    .delegate("#jumpgateForm .ship_input_row .textinput", "keyup change input", function () {
      checkIntInput(this, 0, $(this).attr("rel"));
    })
    .undelegate("#jumpgateForm .ship_input_row .textinput", "focus")
    .delegate("#jumpgateForm .ship_input_row .textinput", "focus", function () {
      if ($.isNumeric($(this).val()) === false) {
        $(this).val("");
      } else {
        $(this).select();
      }
    });
  $("#jumpgate .answerHeadline, .js_openStandardMoonMenu").click(function () {
    if (!player.hasCommander) {
      errorBoxNotify(
        LocalizationStrings.error,
        translation.changeSettingOnlyWithCommander,
        LocalizationStrings.ok,
        null,
        false,
      );
    } else {
      $("#jumpgate").find(".answerHeadline").toggleClass("open");
      $(".thirdCol").toggleClass("hidden");
    }
  });
  $(".js_executeJumpButton").click(function () {
    var selectedMoon = $("#jumpgateForm").find('select[name="targetSpaceObjectId"]').val();
    window.jumpGateTargetId = selectedMoon;

    if (selectedMoon != 0) {
      var noShipsSelected = true;
      $(".ship_selection_table input").each(function () {
        if ($(this).val() > 0) {
          noShipsSelected = false;
        }
      });

      if (!noShipsSelected) {
        ajaxFormSubmit("jumpgateForm", $(this).attr("data-url"), jumpgateDone);
      } else {
        fadeBox(translation.noShipsWereSelected, true);
      }
    } else {
      fadeBox(translation.validTargetNeeded, true);
    }
  });
}
