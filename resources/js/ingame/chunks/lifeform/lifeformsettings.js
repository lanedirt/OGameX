function LifeformSettings(cfg) {
  this.token = cfg.token || null;
  this.loca = cfg.loca;
  this.lifeformIds = cfg.lifeformIds;
}

LifeformSettings.prototype.init = function () {
  let that = this;
  this.lifeformWrapper = $("#lfsettings .lfsettingsContent");
  this.loadingIndicator = this.lifeformWrapper.ogameLoadingIndicator();
  initToggleHeader("lfsettings");

  if ($("#removeLifeform").hasClass("disabled") === false) {
    $("#removeLifeform").bind("click", that.onClickSubmitRemove.bind(that));
  }

  $(".selectLifeform").each(function () {
    if ($(this).hasClass("disabled") === false) {
      $(this).bind("click", that.onClickSubmitSelect.bind(that));
    }
  });
};

LifeformSettings.prototype.handleResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  this.updateToken(data.newAjaxToken);

  if (status === "success") {
    fadeBox(data.message, false);

    if (data.redirectUrl !== undefined) {
      window.location = data.redirectUrl;
    }
  } else {
    if (data.tabs !== undefined) {
      this.refreshTabs(data.tabs);
    }

    if (data.redirectUrl !== undefined) {
      window.location = data.redirectUrl;
    }

    this.displayErrors(data.errors);
  }
};

LifeformSettings.prototype.updateToken = function (newtoken) {
  this.token = newtoken;
  token = newtoken;
};

LifeformSettings.prototype.displayErrors = function (errors) {
  // only display the first error
  let error = errors[0] || undefined;

  if (error) {
    fadeBox(error.message, true);
  }
};

LifeformSettings.prototype.onAjaxDone = function () {
  this.loadingIndicator.hide();
  let that = this;
  $("#removeLifeform").bind("click", that.onClickSubmitRemove.bind(that));
  $(".selectLifeform").each(function () {
    $(this).bind("click", that.onClickSubmitSelect.bind(that));
  });
};

LifeformSettings.prototype.onClickSubmitRemove = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
    planetId: $(e.currentTarget).data("planetid"),
  };
  let that = this;
  this.loadingIndicator.show();
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.LOCA_LIFEFORM_REMOVE_FROM_PLANET +
      "<br/><br/>" +
      this.loca.LOCA_DEBUFF_ACTIVATION +
      "<br/>" +
      this.loca.LOCA_DEBUFF_REMOVED_LIFEFORM_DESCRIPTION,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(this.urlRemoveLifeform, params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

LifeformSettings.prototype.onClickSubmitSelect = function (e) {
  e.preventDefault();

  if (!$(e.currentTarget).hasClass("disabled") && !e.currentTarget.hasAttribute("lifeformid")) {
    this.submitLifeform(
      $(e.currentTarget).data("lifeformid"),
      $(e.currentTarget).data("planetid"),
      $(e.currentTarget).data("name"),
    );
  }
};

LifeformSettings.prototype.submitLifeform = function (lifeformid, planetid, name) {
  this.loadingIndicator.show();
  let params = {
    lifeformId: lifeformid,
    planetId: planetid,
    _token: this.token,
  };
  let question = this.loca.LOCA_LIFEFORM_SELECT_QUESTION.replace("#lifeformname#", name);
  let that = this;
  this.loadingIndicator.show();
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    question,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(this.urlSelectLifeform, params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};
