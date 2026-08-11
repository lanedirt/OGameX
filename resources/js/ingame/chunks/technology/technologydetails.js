function TechnologyDetails(params) {
  this.setParams(params);
}

TechnologyDetails.prototype.init = function () {
  $(document).on("click", ".technology.hasDetails:not(.showsDetails) .icon", this.onClickShow.bind(this));
  $(document).on("click", "#technologydetails .close, .showsDetails", this.onClickHide.bind(this));
  $(document).on("change", "#technologydetails .build_amount", this.onChangeAmount.bind(this));
  $(document).on("keypress", "#technologydetails .build_amount", this.onKeyPressAmount.bind(this));
  $(document).on("click", "#technologydetails button.upgrade", this.onClickUpgrade.bind(this));
  $(document).on("click", "#technologydetails a.upgrade", this.onClickUpgradeStorage.bind(this));
  $(document).on("click", "#technologydetails a.build-it_premium", this.onClickBuyCommander.bind(this));
  $(document).on("click", "#technologydetails button.downgrade", this.onClickDowngrade.bind(this));
  $(document).on("click", "#technologydetails button.maximum", this.onClickMaximum.bind(this));
  $(document).on("click", "#technologydetails .button.select_class", this.onClickSelectClass.bind(this));
  $(document).on("click", "#technologydetails .button.deselect_class", this.onClickDeselectClass.bind(this));
  $(document).on(
    "click",
    '#technologydetails button[data-target]:not(#technologydetails button[data-target*="overlay=1"], #technologydetails button.overlay)',
    function () {},
  );
};

TechnologyDetails.prototype.setParams = function (params) {
  this.loca = params.loca;
  this.technologyDetailsEndpoint = params.technologyDetailsEndpoint;
  this.selectCharacterClassEndpoint = params.selectCharacterClassEndpoint;
  this.deselectCharacterClassEndpoint = params.deselectCharacterClassEndpoint;
};

TechnologyDetails.prototype.onChangeAmount = function (e) {
  let $input = $(e.currentTarget);
  let minVal = parseInt($input.attr("min"));
  let maxVal = parseInt($input.attr("max"));
  let val = parseInt(getValue($input.val())) || 0;

  if (typeof minVal !== "undefined") {
    val = Math.max(val, minVal);
  }

  if (typeof maxVal !== "undefined") {
    val = Math.min(val, maxVal);
  }

  $input.val(val);
};

TechnologyDetails.prototype.onKeyPressAmount = function (e) {
  if (getKeyCode(e) == 13) {
    $("#technologydetails button.upgrade").click();
  }
};

TechnologyDetails.prototype.onClickUpgrade = function (e) {
  let technologyId = $(e.currentTarget).data("technology");
  let amount = $("#build_amount").val() || 1;

  if (planetMoveInProgress) {
    return errorBoxDecision(
      LocalizationStrings.attention,
      LocalizationStrings.planetMoveBreakUpWarning,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      function () {
        buildListActionBuild(technologyId, amount);
      },
    );
  }

  if (lastBuildingSlot.shouldWarnForTechnologyId(technologyId)) {
    return errorBoxDecision(
      LocalizationStrings.notice,
      lastBuildingSlot.slotWarning,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      function () {
        buildListActionBuild(technologyId, amount);
      },
    );
  }

  if (showLifeformBonusCapReached) {
    return errorBoxDecision(
      LocalizationStrings.attention,
      this.loca.LOCA_LIFEFORM_BONUS_CAP_REACHED_WARNING,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      function () {
        buildListActionBuild(technologyId, amount);
      },
    );
  }

  buildListActionBuild(technologyId, amount);
};

TechnologyDetails.prototype.onClickUpgradeStorage = function (e) {
  let technologyUrl = $(e.currentTarget).data("url");
  let technologyQuestion1 = $(e.currentTarget).data("title");
  let technologyQuestion2 = $(e.currentTarget).data("question");
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    technologyQuestion1 + " - " + technologyQuestion2,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      document.location.replace(technologyUrl);
    },
  );
};

TechnologyDetails.prototype.onClickBuyCommander = function (e) {
  let technologyId = $(e.currentTarget).data("technology");
  let technologyUrl = $(e.currentTarget).data("url");
  let technologyQuestion = $(e.currentTarget).data("question");
  let buyResourceOverlayHref = $(e.currentTarget).attr("href");
  let amount = $("#build_amount").val();

  if (technologyUrl && technologyQuestion) {
    errorBoxDecision(
      this.loca.LOCA_ALL_NOTICE,
      technologyQuestion,
      this.loca.LOCA_ALL_YES,
      this.loca.LOCA_ALL_NO,
      function () {
        document.location.replace(technologyUrl);
      },
    );
  } else if (buyResourceOverlayHref) {
    $(e.currentTarget).attr("href", buyResourceOverlayHref + "&amount=" + amount + "&techID=" + technologyId);
  }
};

TechnologyDetails.prototype.handleDowngrade = function (technologyId, technologyName) {
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.locaDemolishStructureQuestion.replace("TECHNOLOGY_NAME", technologyName) +
      "<br><br>" +
      $("#demolition_costs_tooltip").html(),
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      buildListActionDemolish(technologyId);
    },
  );
};

TechnologyDetails.prototype.onClickDowngrade = function (e) {
  let technologyId = $(e.currentTarget).data("technology");
  let technologyName = $(e.currentTarget).data("name");

  if (planetMoveInProgress) {
    errorBoxDecision(
      LocalizationStrings.attention,
      LocalizationStrings.planetMoveBreakUpWarning,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      this.handleDowngrade.bind(this, technologyId, technologyName),
    );
  } else {
    this.handleDowngrade(technologyId, technologyName);
  }
};

TechnologyDetails.prototype.onClickMaximum = function (e) {
  this.setMaximumBuildableAmount();
};

TechnologyDetails.prototype.onClickSelectClass = function (e) {
  e.preventDefault();
  let characterClassId = $(e.currentTarget).data("characterclassid");
  this.selectClass(characterClassId);
};

TechnologyDetails.prototype.onClickDeselectClass = function (e) {
  e.preventDefault();
  let characterClassId = $(e.currentTarget).data("characterclassid");
  this.deselectClass(characterClassId);
};

TechnologyDetails.prototype.onClickShow = function (e) {
  this.show($(e.currentTarget).closest(".hasDetails").data("technology"));
};

TechnologyDetails.prototype.onClickHide = function (e) {
  this.hide($(e.currentTarget));
};

TechnologyDetails.prototype.selectClass = function (characterClassId) {
  let that = this;
  let selectCharacterClassEndpoint = this.selectCharacterClassEndpoint.replace("CHARACTERCLASSID", characterClassId);
  $.post(selectCharacterClassEndpoint).done(function (data) {
    var json = $.parseJSON(data);

    if (json.status === "success") {
      that.show(37); //        document.location.replace(json.redirectUrl)
    } else {
      that.displayErrors(json);
    }
  });
};

TechnologyDetails.prototype.deselectClass = function (characterClassId) {
  let that = this;
  let deselectCharacterClassEndpoint = this.deselectCharacterClassEndpoint.replace(
    "CHARACTERCLASSID",
    characterClassId,
  );
  $.post(deselectCharacterClassEndpoint).done(function (data) {
    var json = $.parseJSON(data);

    if (json.status === "success") {
      that.show(37); //document.location.replace(json.redirectUrl)
    } else {
      that.displayErrors(json);
    }
  });
};

TechnologyDetails.prototype.displayErrors = function (data) {
  let errorCode = data.errorCode || 0;
  let errorMessage = data.errorMessage || "";
  fadeBox(errorMessage, true);
};

TechnologyDetails.prototype.show = function (technologyId) {
  let that = this;
  let element = $(".technology.hasDetails[data-technology=" + technologyId + "]");
  let elemTechnologyDetailsWrapper = $("#technologydetails_wrapper");
  let elemTechnologyDetailsContent = $("#technologydetails_content");
  let elemTechnologyDetails = $("#technologydetails");
  let locationIndicator = elemTechnologyDetailsContent.ogameLoadingIndicator();
  locationIndicator.show();
  $.ajax({
    url: this.technologyDetailsEndpoint,
    data: {
      technology: technologyId,
    },
  }).done(function (json) {
    if (json.status === "failure") {
      that.displayErrors(json.errors);
      locationIndicator.hide();
    } else {
      elemTechnologyDetailsWrapper.toggleClass("slide-up", true);
      elemTechnologyDetailsWrapper.toggleClass("slide-down", false);
      $(".showsDetails").removeClass("showsDetails");
      element.closest(".hasDetails").addClass("showsDetails");
      locationIndicator.hide();
      let anchor = $("header[data-anchor=technologyDetails]");

      if (elemTechnologyDetails.length > 0) {
        removeTooltip(elemTechnologyDetails.find(getTooltipSelector()));
        elemTechnologyDetails.replaceWith(json.content[json.target]);
        elemTechnologyDetails.addClass(anchor.data("technologydetails-size")).offset(anchor.offset());
      } else {
        elemTechnologyDetailsContent.append(json.content[json.target]);
        elemTechnologyDetails.addClass(anchor.data("technologydetails-size")).offset(anchor.offset());
      } //techID is magically defined by setting data in the .html; not always so we check it
      // and set it to harmless 0 as default. This is used for the repair dock.

      $(document).trigger("ajaxShowElement", typeof technologyId === "undefined" ? 0 : technologyId);
    }
  });
};

TechnologyDetails.prototype.hide = function () {
  let elemTechnologyDetails = $("#technologydetails");
  removeTooltip(elemTechnologyDetails.find(getTooltipSelector()));
  $("#technologydetails_wrapper").removeClass("slide-up");
  this.startSlideDown();
  $(".showsDetails").removeClass("showsDetails");
};

TechnologyDetails.prototype.startSlideDown = function () {
  $("#technologydetails_wrapper").toggleClass("slide-down", true);
  this.timerSlideDownEnd = setTimeout(
    function () {
      this.stopSlideDown();
    }.bind(this),
    500,
  );
};

TechnologyDetails.prototype.stopSlideDown = function () {
  $("#technologydetails_wrapper").removeClass("slide-down");
  clearTimeout(this.timerSlideDownEnd);
  this.timerSlideDownEnd = null;
};

TechnologyDetails.prototype.setMaximumBuildableAmount = function () {
  var $buildAmount = $("#technologydetails #build_amount");
  $buildAmount.val($buildAmount.attr("max"));
};
