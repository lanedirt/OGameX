function LifeformResearch(cfg) {
  this.token = cfg.token || null;
  this.loca = cfg.loca;
}

LifeformResearch.prototype.init = function () {
  if ($(".lfresearchlayer").length > 1) {
    for (let i = 0; i < $(".lfresearchlayer").length - 1; i++) {
      $($(".lfresearchlayer")[i]).remove();
    }
  }

  let that = this;
  this.lifeformWrapper = $("#technologies");
  this.loadingIndicator = this.lifeformWrapper.ogameLoadingIndicator();
  $("#selectTechnology").bind("click", that.onClickSelect100.bind(that));
  $("#selectChance").bind("click", that.onClickSelectRandom.bind(that));

  if ($(".selectArtifacts").data("enabled") === true) {
    $(".selectArtifacts").bind("click", that.onClickSelectArtifacts.bind(that));
  }

  if ($("#resetTechTree").data("enabled") === true) {
    $("#resetTechTree").bind("click", that.onClickResetTree.bind(that));
  }

  if ($("#buyResetTechTree").data("enabled") === true) {
    $("#buyResetTechTree").bind("click", that.onClickResetTree.bind(that));
  }

  if ($("#restoreTechTree").data("enabled") === true) {
    $("#restoreTechTree").bind("click", that.onClickRestoreTree.bind(that));
  }
};

LifeformResearch.prototype.handleResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  this.updateToken(data.newAjaxToken);

  if (status === "success") {
    fadeBox(data.message, false);

    if (data.redirectUrl !== undefined) {
      window.location = data.redirectUrl;
    }
  } else {
    if (data.redirectUrl !== undefined) {
      window.location = data.redirectUrl;
    }

    this.displayErrors(data.errors);
  }
};

LifeformResearch.prototype.updateToken = function (newtoken) {
  this.token = newtoken;
  token = newtoken;
};

LifeformResearch.prototype.displayErrors = function (errors) {
  // only display the first error
  let error = errors[0] || undefined;

  if (error) {
    fadeBox(error.message, true);
  }
};

LifeformResearch.prototype.onAjaxDone = function () {
  this.loadingIndicator.hide();
  $("#lfresearchlayer").parents(".overlayDiv").dialog("close");
  $(".lfresearchlayer").remove();
};

LifeformResearch.prototype.onClickSelect100 = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
    slotNumber: $(e.currentTarget).data("slotNumber"),
    planetId: $(e.currentTarget).data("planetId"),
  };
  let that = this;
  this.loadingIndicator.show();
  let question = this.loca.LOCA_LIFEFORM_RESEARCH_SELECT_TECHNOLOGY + $(e.currentTarget).data("name");
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    question,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(this.urlSelect100, params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

LifeformResearch.prototype.onClickSelectRandom = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
    slotNumber: $(e.currentTarget).data("slotNumber"),
    planetId: $(e.currentTarget).data("planetId"),
  };
  let that = this;
  this.loadingIndicator.show();
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.LOCA_LIFEFORM_RESEARCH_SELECT_TECHNOLOGY_RANDOM,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(this.urlSelectRandom, params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

LifeformResearch.prototype.onClickSelectArtifacts = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
    slotNumber: $(e.currentTarget).data("slotNumber"),
    planetId: $(e.currentTarget).data("planetId"),
    technologyId: $(e.currentTarget).data("technology-id"),
  };
  let that = this;
  this.loadingIndicator.show();
  let question =
    this.loca.LOCA_LIFEFORM_RESEARCH_SELECT_TECHNOLOGY +
    $(e.currentTarget).data("name") +
    "<br/>" +
    this.loca.LOCA_LIFEFORM_ARTIFACTS_SELECT_RESEARCH.replace(
      "#artifactcost#",
      $(e.currentTarget).data("artifacts-cost"),
    );
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    question,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(this.urlSelectArtifacts, params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

LifeformResearch.prototype.onClickResetTree = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
    tier: $(e.currentTarget).data("tier"),
    planetId: $(e.currentTarget).data("planetId"),
  };
  let that = this;
  this.loadingIndicator.show();
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.LOCA_LIFEFORM_RESET_TECHNOLOGY_RESEARCH_TREE + " " + $(e.currentTarget).data("tier"),
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post($(e.currentTarget).attr("href"), params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

LifeformResearch.prototype.onClickRestoreTree = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
    tier: $(e.currentTarget).data("tier"),
    planetId: $(e.currentTarget).data("planetId"),
  };
  let that = this;
  this.loadingIndicator.show();
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.LOCA_LIFEFORM_RESTORE_TECHNOLOGY_RESEARCH_TREE + " " + $(e.currentTarget).data("tier"),
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post($(e.currentTarget).attr("href"), params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};
