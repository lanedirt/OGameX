function initPreferences() {
  $(".category").click(function () {
    document.prefs.reset();
  });
  let copySubmitButton = $(".copy2PtrConainer a");
  copySubmitButton.on("click", this.onClickExecutePtrCopy.bind(this)); // solution to chrome auto-filling username and password, dynamically add password field
  // once user types in new password

  $("#db_character").on("input", function () {
    if ($("#db_character").val()) {
      if (!$("#db_character_password").length) {
        $("#confirmPasswordWrapper").show();
        $("#confirmPasswordContainer").append(`
                        <input class="textInput w200"
                               id="db_character_password"
                               type="password"
                               value=""
                               size="30"
                               name="db_character_password"
                               autocomplete="new-password"
                        />
                    `);
      }
    } else {
      $("#confirmPasswordWrapper").hide();
      $("#db_character_password").remove();
    }
  }); // Generell immer die erste Gruppe öffnen
  // WICHTIG ZUM DURCHKLICKEN DER TABS

  $("div.wrap > div.group").hide();
  $(
    "div.wrap > div.group:first," +
      "div.wrap:eq(1) > div.group:eq(0)," +
      "div.wrap:eq(2) > div.group:eq(0)," +
      "div.wrap:eq(3) > div.group:eq(0)",
  ).show();
  $("div.wrap > div.bar").click(function () {
    $(this)
      .next("div.group:hidden")
      .slideDown("fast", function () {
        Tipped.show($(":input:visible"));
      })
      .siblings("div.group:visible")
      .slideUp("fast", function () {
        Tipped.hide($(":input:not(:visible)"));
      });
    $(".formError").validationEngine("closePrompt");
  });
  $(".content .bar").hover(
    function () {
      $(this).addClass("bar-hover");
    },
    function () {
      $(this).removeClass("bar-hover");
    },
  );
  $("#newpass1").bind("keyup", function () {
    var value = $(this).val();
    var length = value.length;
    var hasSpecialChars = value.match(/[^A-Za-z\d]/);
    var hasNumbers = value.match(/\d/);
    var hasMixedCase = value.match(/[a-z]/) && value.match(/[A-Z]/);
    var score = 0;
    var maxScore = 4;
    var fulfilled = {
      length: false,
      "mixed-case": false,
      "special-chars": false,
      numbers: false,
    };

    if (length >= passwordMinLength && length <= passwordMaxLength) {
      fulfilled["length"] = true;
      score++;
    }

    if (hasMixedCase) {
      fulfilled["mixed-case"] = true;
      score++;
    }

    if (hasNumbers) {
      fulfilled["numbers"] = true;
      score++;
    }

    if (hasSpecialChars) {
      fulfilled["special-chars"] = true;
      score++;
    }

    for (var name in fulfilled) {
      var isFulfilled = fulfilled[name];
      var element = $("#password-meter-status-" + name);
      element.find("img.status-checked").css("visibility", isFulfilled ? "visible" : "hidden");
    }

    var rating = Math.floor((score / maxScore) * 2);
    var levels = new Array("low", "medium", "high");

    for (var i in levels) {
      if (i != rating) {
        $("#password-meter-rating-" + levels[i]).removeClass("arrow");
      } else {
        $("#password-meter-rating-" + levels[i]).addClass("arrow");
      }
    }
  });
  $(".contentzs").tabs({
    beforeActivate: function (event, ui) {
      $("input#selectedTab").val($(ui.tab).parent().prevAll().length);
    },
    activate: function () {
      Tipped.hide($("input:not(:visible)"));
      Tipped.show($("input:visible"));
    },
    active: selectedTab,
  });
  $("#sortSetting")
    .unbind("change")
    .bind("change", function () {
      var name = "settings_order";

      if ($(this).val() == customSorting) {
        var $sortOrder = $("#sortOrder");
        $sortOrder.attr("disabled", "disabled").attr("name", "");
        $("#sortOrderHidden").attr("name", name).val($sortOrder.val());
        $("#sortOrder").next(".dropdown").addClass("disabled");
      } else {
        $("#sortOrder").next(".dropdown").removeClass("disabled");
        var $sortOrderHidden = $("#sortOrderHidden");
        $sortOrderHidden.attr("name", "");
        $("#sortOrder").removeClass("disabled").attr("name", name).removeAttr("disabled").val($sortOrderHidden.val());
      }
    })
    .trigger("change"); //initFormValidation();

  if (moveInProgress) {
    $("form#prefs").on("submit", function (e) {
      var $thisObj = $(this);

      if ($thisObj.find("input#urlaubs_modus.notOnVacation:checked").length) {
        errorBoxDecision(
          LocalizationStrings.attention,
          preferenceLoca.planetMoveQuestion,
          LocalizationStrings.yes,
          LocalizationStrings.no,
          function () {
            $thisObj.off("submit").submit();
          },
        );
        e.preventDefault();
        return false;
      }
    });
  }

  if (hasAPassword) {
    $("#prefs").bind("submit", function () {
      var $thisObj = $(this);
      var nameChange = $("#db_character", $thisObj);

      if (!$thisObj.data("asking") && nameChange.val() != undefined && nameChange.val().length) {
        $thisObj.data("asking", true);
        errorBoxDecision(
          preferenceLoca.changeNameTitle,
          preferenceLoca.changeNameQuestion.replace("%newName%", $("#db_character", $thisObj).val()),
          LocalizationStrings.yes,
          LocalizationStrings.no,
          function () {
            $thisObj.submit();
            $thisObj.data("asking", false);
          },
          function () {
            $thisObj.data("asking", false);
          },
        );
        return false;
      }
    });
  }

  // Vacation mode activation confirmation
  $("#prefs").on("submit", function (e) {
    var $thisObj = $(this);

    // Check if trying to activate vacation mode (checkbox is checked and has class 'notOnVacation')
    if ($thisObj.find("input#urlaubs_modus.notOnVacation:checked").length && !moveInProgress) {
      if (!$thisObj.data("vacation_confirming")) {
        $thisObj.data("vacation_confirming", true);
        errorBoxDecision(
          LocalizationStrings.attention,
          preferenceLoca.vacationModeQuestion,
          LocalizationStrings.yes,
          LocalizationStrings.no,
          function () {
            // User clicked yes - submit the form
            $thisObj.data("vacation_confirming", false);
            $thisObj.off("submit").submit();
          },
          function () {
            // User clicked no - reset the checkbox and clear the flag
            $thisObj.find("input#urlaubs_modus").prop("checked", false);
            $thisObj.data("vacation_confirming", false);
          },
        );
        e.preventDefault();
        return false;
      }
    }
  });

  // Vacation mode button handler
  $("#vacation-mode-button").on("click", function (e) {
    e.preventDefault();
    var vacationCheckbox = $("#urlaubs_modus");

    // Toggle the checkbox state
    if (vacationCheckbox.is(":checked")) {
      vacationCheckbox.prop("checked", false);
    } else {
      vacationCheckbox.prop("checked", true);
    }

    // Trigger form submission which will handle the confirmation popup
    $("#prefs").submit();
  });

  // Im aktiven Tab aber die richtige Auswahl öffnen

  if (tabsDisabled) {
    $(".contentzs").tabs("option", "disabled", [1, 2]);
    $("#tabGeneral, #tabRepresentation").attr("title", preferenceLoca.tabDisabled).attr("class", "tooltip");
  }

  $("div.wrap:visible > div.bar:eq(" + openGroup + ")").click();
}
