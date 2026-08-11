function AllianceClassBoxes(params) {
  this.loca = params.loca;
  this.token = params.token;
  this.activatingItem = false;
  this.ingameUrl = params.ingameUrl;
  this.init();
}

AllianceClassBoxes.prototype.init = function () {
  $(document).on("click", ".allianceclass.boxes .buttons .freeselect", this.onClickFreeSelect.bind(this));
  $(document).on("click", ".allianceclass.boxes .buttons .darkmatter", this.onClickDarkMatter.bind(this));
  $(document).on("click", ".allianceclass.boxes .buttons .deactivate", this.onClickDeactivate.bind(this));
  $(document).on("click", ".allianceclass.boxes .buttons .nodarkmatter", this.onClickNoDarkMatter.bind(this));
  $(document).on("click", ".allianceclass.boxes .buttons .classchangeitem", this.onClickClassChangeItem.bind(this));
};

AllianceClassBoxes.prototype.hasActiveSelection = function () {
  return $(".allianceclass.box.selected").length > 0;
};

AllianceClassBoxes.prototype.executeActionWithRedirect = function (url) {
  let that = this;
  let params = {
    _token: token,
  };
  $.post(url, params, this.handleResponse.bind(this));
};

AllianceClassBoxes.prototype.onClickFreeSelect = function (e) {
  let that = this;
  let url = $(e.currentTarget).attr("rel");
  let allianceClassBox = $(e.currentTarget).closest(".allianceclass.box");
  let name = allianceClassBox.data("allianceClassName");

  if ($(e.currentTarget).data("disabled") !== 1) {
    this.fetchDataAboutCurrentAllianceClass(
      name,
      function () {
        that.executeActionWithRedirect(url);
      },
      "",
      0,
    );
  }
};

AllianceClassBoxes.prototype.onClickDarkMatter = function (e) {
  let that = this;
  let url = $(e.currentTarget).attr("rel");
  let allianceClassBox = $(e.currentTarget).closest(".allianceclass.box");
  let name = allianceClassBox.data("allianceClassName");
  let price = allianceClassBox.data("allianceClassPrice");

  if ($(e.currentTarget).data("disabled") !== 1) {
    this.fetchDataAboutCurrentAllianceClass(
      name,
      function () {
        that.executeActionWithRedirect(url);
      },
      "buyAndActivateItemQuestion",
      price,
    );
  }
};

AllianceClassBoxes.prototype.onClickNoDarkMatter = function (e) {
  let that = this;
  let urlDarkMatter = $(e.currentTarget).attr("rel");

  if ($(e.currentTarget).data("disabled") !== 1) {
    errorBoxDecision(
      this.loca.LOCA_ALL_NOTICE,
      this.loca.LOCA_ALL_ERROR_LACKING_DM,
      this.loca.LOCA_ALL_YES,
      this.loca.LOCA_ALL_NO,
      redirectPremium,
    );
  }
};

AllianceClassBoxes.prototype.onClickDeactivate = function (e) {
  let that = this;
  let url = $(e.currentTarget).attr("rel");
  let allianceClassBox = $(e.currentTarget).closest(".allianceclass.box");
  let name = allianceClassBox.data("allianceClassName");
  let label = this.loca.LOCA_ALLIANCE_CLASS_NOTE_DEACTIVATE.replace("#allianceClassName#", name);

  if ($(e.currentTarget).data("disabled") !== 1) {
    errorBoxDecision(this.loca.LOCA_ALL_NOTICE, label, this.loca.LOCA_ALL_YES, this.loca.LOCA_ALL_NO, function () {
      that.executeActionWithRedirect(url);
    });
  }
};

AllianceClassBoxes.prototype.onClickClassChangeItem = function (e) {
  let that = this;
  let url = $(e.currentTarget).attr("rel");
  let allianceClassBox = $(e.currentTarget).closest(".allianceclass.box");
  let name = allianceClassBox.data("allianceClassName");

  if ($(e.currentTarget).data("disabled") !== 1) {
    this.fetchDataAboutCurrentAllianceClass(
      name,
      function () {
        let params = {
          _token: token,
        };
        $.post(url, params).done(function (data) {
          var json = $.parseJSON(data);
          token = json.newAjaxToken;

          if (json.status === "success") {
            window.location.reload();
          } else {
            that.displayErrors(json);
          }
        });
      },
      "activateItemQuestion",
      null,
    );
  }
};

AllianceClassBoxes.prototype.displayErrors = function (errors) {
  // only display the first error
  let error = errors[0] || undefined;

  if (error) {
    fadeBox(error.message, true);
  }
};

AllianceClassBoxes.prototype.handleResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  token = data.newAjaxToken;
  alliance.updateToken(data.newAjaxToken);

  if (status === "success") {
    if (data.redirectUrl !== undefined) {
      window.location = data.redirectUrl;
    } else {
      if (data.tabs !== undefined) {
        alliance.refreshTabs(data.tabs);
      }

      fadeBox(data.message, false);
      getAjaxEventbox();
      // Don't call getAjaxResourcebox() - alliance operations don't affect resources
      this.fetch(this.tab);
    }
  } else {
    if (data.tabs !== undefined) {
      alliance.refreshTabs(data.tabs);
    }

    this.displayErrors(data.errors);
  }
};

AllianceClassBoxes.prototype.fetchDataAboutCurrentAllianceClass = function (
  newClassName,
  upgradeItemAjax,
  questionType,
  price,
) {
  if (!this.activatingItem) {
    this.activatingItem = true;
    let that = this;
    $.ajax({
      url: this.ingameUrl,
      type: "GET",
      data: {
        component: "allianceclassselection",
        action: "fetchDataAboutCurrentAllianceClass",
        ajax: 1,
        asJson: 1,
      },
      dataType: "json",
      error: function (error) {
        that.promptUserForAllianceClassChange(newClassName, upgradeItemAjax, questionType, price);
      },
      success: function (data) {
        that.promptUserForAllianceClassChange(newClassName, upgradeItemAjax, questionType, price, data);
      },
    });
  }
};

AllianceClassBoxes.prototype.promptUserForAllianceClassChange = function (
  newClassName,
  upgradeItemAjax,
  questionType,
  price,
  response,
) {
  this.activatingItem = false;

  if (response.userDoesNotHaveAlliance) {
    return 0;
  }

  let localizationString = this.loca.LOCA_ALLIANCE_CLASS_NOTE_ACTIVATE_WITH_ITEM;

  if (questionType === "buyAndActivateItemQuestion") {
    localizationString = this.loca.LOCA_ALLIANCE_CLASS_NOTE_ACTIVATE_WITH_DM;
  }

  localizationString = localizationString.replace("#allianceClassName#", newClassName);

  if (questionType === "buyAndActivateItemQuestion") {
    localizationString = localizationString.replace("#darkmatter#", tsdpkt(price));
  }

  if (response && response.currentAllianceClass && response.dateOfLastAllianceClassChange) {
    localizationString += this.loca.LOCA_ALLIANCE_CLASS_NOTE_ACTIVATE_APPEND_CURRENT_CLASS;
    localizationString = localizationString.replace("#currentAllianceClassName#", response.currentAllianceClass);
    localizationString = localizationString.replace(
      "#lastAllianceClassChange#",
      response.dateOfLastAllianceClassChange,
    );
  }

  errorBoxDecision(
    LocalizationStrings.notice,
    localizationString,
    LocalizationStrings.yes,
    LocalizationStrings.no,
    upgradeItemAjax,
  );
};
