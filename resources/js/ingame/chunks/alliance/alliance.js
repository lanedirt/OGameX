function Alliance(cfg) {
  this.tab = cfg.tab || "";
  this.token = cfg.token;
  this.loca = cfg.loca;
  this.tabs = [
    "overview",
    "management",
    "broadcast",
    "applications",
    "classselection",
    "createNewAlliance",
    "handleApplication",
    "allianceOverview",
    "allianceMembers",
  ];
  this.initMap = {
    overview: this.initOverview.bind(this),
    management: this.initManagement.bind(this),
    applications: this.initApplication.bind(this),
    broadcast: this.initBroadcast.bind(this),
    classselection: this.initClasses.bind(this),
    createNewAlliance: this.initCreateAlliance.bind(this),
    handleApplication: this.initHandleApplication.bind(this),
    allianceOverview: this.initAllianceOverview.bind(this),
    allianceMembers: this.initAllianceMembers.bind(this),
  };

  if (this.initMap[this.tab]) {
    this.initMap[this.tab](cfg);
  }
} // general

Alliance.prototype.displayErrors = function (errors) {
  // only display the first error
  if (!errors || !Array.isArray(errors) || errors.length === 0) {
    fadeBox("An error occurred", true);
    return;
  }

  let error = errors[0];
  if (error && error.message) {
    fadeBox(error.message, true);
  } else {
    fadeBox("An error occurred", true);
  }
};

Alliance.prototype.initCommon = function (cfg) {
  this.taskWrapper = $("#alliancecomponent .alliance_wrapper");
  this.loadingIndicator = this.taskWrapper.ogameLoadingIndicator();
  this.allianceContent = $("#alliancecomponent .allianceContent");
  this.titlebar = $("#alliancecomponent #tab-ally");
  this.titlebar.on("click", ".overview", this.onClickTab.bind(this));
  this.titlebar.on("click", ".management", this.onClickTab.bind(this));
  this.titlebar.on("click", ".broadcast", this.onClickTab.bind(this));
  this.titlebar.on("click", ".applications", this.onClickTab.bind(this));
  this.titlebar.on("click", ".classselection", this.onClickTab.bind(this));
};

Alliance.prototype.initCommonWithout = function (cfg) {
  this.taskWrapper = $("#alliancecomponent .alliance_wrapper");
  this.loadingIndicator = this.taskWrapper.ogameLoadingIndicator();
  this.allianceContent = $("#alliancecomponent .allianceContent");
  this.titlebar = $("#alliancecomponent #tab-ally");
  this.titlebar.on("click", "#isNewApplication", this.onClickTab.bind(this));
};

Alliance.prototype.refreshContent = function (htmlItems) {
  this.allianceContent.html(htmlItems);
};

Alliance.prototype.onAjaxDone = function () {
  this.loadingIndicator.hide();
  let that = this;

  switch (this.tab) {
    case "createNewAlliance":
      $("#form_createAlly .createAlly").bind("click", that.onClickCreateAlliance.bind(that));
      // URL is now set in onFetch from AJAX response
      break;

    case "handleApplication":
      // URLs are now set in onFetch from AJAX response
      $("#writeapplication .sendNewApplication").bind("click", that.onClickSendApplication.bind(that));
      $(".bewerbung .cancelApplication").bind("click", that.onClickCancelApplication.bind(that));
      break;

    case "overview":
      $(".kickMemberButton").each(function () {
        $(this).bind("click", that.onClickKickMember.bind(that));
      });
      $("#kickMemberForm .cancel").bind("click", that.onClickKickMemberCancel.bind(that));
      $("#kickMemberForm .submit").bind("click", that.onClickKickMemberSubmit.bind(that));
      $("#form_assignRank .assignRank").bind("click", that.onClickAssignRankSubmit.bind(that));
      $("#leaveAlly .leaveAlly").bind("click", that.onClickLeaveAlliance.bind(that));
      // URLs are now set in onFetch from AJAX response
      break;

    case "management":
      $("#form_newRank .createRank").bind("click", that.onClickCreateRank.bind(that));
      $("#form_allyRankRights .editRank").bind("click", that.onClickUpdateRank.bind(that));
      $(".delete-rank .deleteRank").each(function () {
        $(this).bind("click", that.onClickDeleteRank.bind(that));
      });
      $("#form_internAllyText .submitText").bind("click", that.onClickUpdateAllianceText.bind(that));
      $("#form_externAllyText .submitText").bind("click", that.onClickUpdateAllianceText.bind(that));
      $("#form_candidacyText .submitText").bind("click", that.onClickUpdateAllianceText.bind(that));
      $("#allySettings .saveSetting").bind("click", that.onClickUpdateSettings.bind(that));
      $("#form_newTag .newTag").bind("click", that.onClickSubmitTag.bind(that));
      $("#form_newName .newName").bind("click", that.onClickSubmitName.bind(that));
      $("#dissolveally .dissolve").bind("click", that.onClickSubmitDisolve.bind(that));
      $("#assignally .transferLeadership").bind("click", that.onClickSubmitTransferLeadership.bind(that));
      $("#assignally .takeoverLeadership").bind("click", that.onClickSubmitTakeoverLeadership.bind(that));
      // URLs are now set in onFetch from AJAX response
      break;

    case "applications":
      $(".action_icons .action").each(function () {
        switch ($(this).data("type")) {
          case "deny":
            $(this).bind("click", that.onClickDenyApplication.bind(that));
            break;

          case "accept":
            $(this).bind("click", that.onClickAcceptApplication.bind(that));
            break;
        }
      });
      $(".members form").each(function () {
        $(this).find(".accept").bind("click", that.onFormClickAcceptApplication.bind(that));
        $(this).find(".deny").bind("click", that.onFormClickDenyApplication.bind(that));
      });
      // URLs are now set in onFetch from AJAX response
      break;

    case "broadcast":
      $("#submitMail").bind("click", that.onFormClickBroadcastButton.bind(that));
      // URLs are now set in onFetch from AJAX response
      break;
  }
};

Alliance.prototype.initCreateAlliance = function (cfg) {
  this.initCommonWithout(cfg);
  this.urlCreateAlliance = cfg.urlCreateAlliance;
  this.fetchNewAlliance();
};

Alliance.prototype.initHandleApplication = function (cfg) {
  this.initCommonWithout(cfg);
  this.appliedAllyId = cfg.appliedAllyId;
  this.urlSendApplication = cfg.urlSendApplication;
  this.urlCancelApplication = cfg.urlCancelApplication;
  this.fetchNewApplication();
};

Alliance.prototype.initAllianceOverview = function (cfg) {
  this.initCommon(cfg);
  this.fetch(this.tab);
};

Alliance.prototype.initAllianceMembers = function (cfg) {
  this.initCommon(cfg);
  this.fetch(this.tab);
};

Alliance.prototype.initOverview = function (cfg) {
  this.initCommon(cfg);
  this.urlKickMember = cfg.urlKickMember;
  this.urlSubmitRanks = cfg.urlSubmitRanks;
  this.urlLeaveAlliance = cfg.urlLeaveAlliance;
  this.fetch(this.tab);
};

Alliance.prototype.initManagement = function (cfg) {
  this.initCommon(cfg);
  this.urlCreateRank = cfg.urlCreateRank;
  this.urlUpdateRank = cfg.urlUpdateRank;
  this.urlDeleteRank = cfg.urlDeleteRank;
  this.urlUpdateAllianceText = cfg.urlUpdateAllianceText;
  this.urlUpdateSettings = cfg.urlUpdateSettings;
  this.urlUpdateTag = cfg.urlUpdateTag;
  this.urlUpdateName = cfg.urlUpdateName;
  this.urlDissolve = cfg.urlDissolve;
  this.urlTransferLeadership = cfg.urlTransferLeadership;
  this.urlTakeoverLeadership = cfg.urlTakeoverLeadership;
  this.fetch(this.tab);
};

Alliance.prototype.initApplication = function (cfg) {
  this.initCommon(cfg);
  this.urlAccept = cfg.urlAccept;
  this.urlDeny = cfg.urlDeny;
  this.urlReport = cfg.urlReport;
  this.fetch(this.tab);
};

Alliance.prototype.initBroadcast = function (cfg) {
  this.initCommon(cfg);
  this.urlSend = cfg.urlSend;
  this.fetch(this.tab);
};

Alliance.prototype.initClasses = function (cfg) {
  this.initCommon(cfg);
  this.fetch(this.tab);
};

Alliance.prototype.onClickCreateRank = function (e) {
  e.preventDefault();
  let rankName = $("#form_newRank #newRankName").val();
  let params = {
    rankName: rankName,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlCreateRank, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickUpdateRank = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
  };
  $('#form_allyRankRights input[type="checkbox"]').each(function () {
    if ($(this).prop("checked")) {
      if (typeof params["rankId_" + $(this).data("rankid")] === "undefined") {
        params["rankId_" + $(this).data("rankid")] = 0;
      }

      params["rankId_" + $(this).data("rankid")] =
        params["rankId_" + $(this).data("rankid")] + $(this).data("rankvalue");
    }
  });
  this.loadingIndicator.show();
  $.post(this.urlUpdateRank, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickDeleteRank = function (e) {
  e.preventDefault();
  let rankId = $(e.currentTarget).data("rankid");
  let params = {
    rankId: rankId,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlDeleteRank, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickUpdateAllianceText = function (e) {
  e.preventDefault();
  let allianceText = $(e.currentTarget).closest("form").find(".alliancetexts").val();
  let submitType = $(e.currentTarget).data("type");
  let params = {
    allianceText: allianceText,
    submitType: submitType,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlUpdateAllianceText, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickUpdateSettings = function (e) {
  e.preventDefault();
  let homepageUrl = $("#allySettings #homepageUrl").val();
  let logoUrl = $("#allySettings #logoUrl").val();
  let state = $("#allySettings #state").val();
  let foundername = $("#allySettings #foundername").val();
  let newcomerrankname = $("#allySettings #newcomerrankname").val();
  let language = $("#allySettings #languageSelectionDropdown").val();
  let params = {
    homepageUrl: homepageUrl,
    logoUrl: logoUrl,
    state: state,
    foundername: foundername,
    newcomerrankname: newcomerrankname,
    language: language,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlUpdateSettings, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickSubmitTag = function (e) {
  e.preventDefault();
  let newTag = $("#form_newTag #newTag").val();
  let params = {
    newTag: newTag,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlUpdateTag, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickSubmitName = function (e) {
  e.preventDefault();
  let newName = $("#form_newName #newName").val();
  let params = {
    newName: newName,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlUpdateName, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickSubmitDisolve = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
  };
  let that = this;
  this.loadingIndicator.show();
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.LOCA_NETWORK_ALLY_GIVEUP,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(this.urlDissolve, params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

Alliance.prototype.onClickSubmitTransferLeadership = function (e) {
  e.preventDefault();
  let newLeaderId = $("#assignally #newLeaderId").val();
  let params = {
    newLeaderId: newLeaderId,
    _token: this.token,
  };
  let that = this;
  this.loadingIndicator.show();
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.LOCA_NETWORK_ALLY_TAKEOVER_ARE_YOU_SURE,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(this.urlTransferLeadership, params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

Alliance.prototype.onClickSubmitTakeoverLeadership = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
  };
  let that = this;
  this.loadingIndicator.show();
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.LOCA_ALLY_TAKEOVER_QUESTION,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(this.urlTakeoverLeadership, params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

Alliance.prototype.onClickLeaveAlliance = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
  };
  let that = this;
  this.loadingIndicator.show();
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.locaAllyLeaveQuestion,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(this.urlLeaveAlliance, params, that.handleResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

Alliance.prototype.onClickKickMember = function (e) {
  e.preventDefault();
  $("#kickMemberReasonText").val("");
  let data = $(e.currentTarget).attr("id").split("-");
  let id = data[1];
  $("#kickMemberId").val(id);
};

Alliance.prototype.onClickKickMemberCancel = function (e) {
  e.preventDefault();
  $("#kickMemberReason").dialog("destroy");
};

Alliance.prototype.onClickKickMemberSubmit = function (e) {
  e.preventDefault();
  let playerId = $("#kickMemberId").val();
  let reasonText = $("#kickMemberReasonText").val();
  this.submitKickMember(playerId, reasonText);
  $("#kickMemberReason").dialog("destroy");
};

Alliance.prototype.onClickAssignRankSubmit = function (e) {
  e.preventDefault();
  let memberRanks = {};
  $('select[name^="memberRanks"]').each(function () {
    memberRanks[$(this).attr("id")] = $(this).val();
  });
  this.submitRanks(memberRanks);
};

Alliance.prototype.submitRanks = function (memberRanks) {
  let params = {
    _token: this.token,
    memberRanks: memberRanks,
  };
  this.loadingIndicator.show();
  $.post(this.urlSubmitRanks, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickCreateAlliance = function () {
  let createTag = $("#allyTagField").val();
  let createName = $("#allyNameField").val();
  let params = {
    tag: createTag,
    name: createName,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlCreateAlliance, params, this.handleResponse.bind(this))
    .done(this.onAjaxDone.bind(this))
    .fail(this.handleResponse.bind(this))
    .always(
      function () {
        this.loadingIndicator.hide();
      }.bind(this),
    );
};

Alliance.prototype.onClickSendApplication = function (e) {
  e.preventDefault();
  let text = $("#writeapplication .alliancetexts").val();
  let params = {
    allianceId: this.appliedAllyId,
    applicationText: text,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlSendApplication, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickCancelApplication = function (e) {
  e.preventDefault();
  let params = {
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlCancelApplication, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.submitKickMember = function (playerId, reasonText) {
  let params = {
    playerId: playerId,
    reasonText: reasonText,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlKickMember, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickNewAlly = function (e) {
  e.preventDefault();

  if ($(e.currentTarget).parent().attr("disabled") !== "disabled") {
    this.fetchNewAlliance();
  }
};

Alliance.prototype.onClickTab = function (e) {
  e.preventDefault();

  if ($(e.currentTarget).parent().attr("disabled") !== "disabled") {
    this.tab = $(e.currentTarget).data("tab");
    this.fetch(this.tab);
  }
};

Alliance.prototype.fetchNewApplication = function () {
  this.tab = "handleApplication";
  this.loadingIndicator.show();
  let data = {
    _token: this.token,
    appliedAllyId: this.appliedAllyId,
  };
  let url = $("#alliancecomponent #isNewApplication").attr("rel");
  $.getJSON(url, data, this.onFetch.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.fetchNewAlliance = function () {
  this.tab = "createNewAlliance";
  this.loadingIndicator.show();
  let data = {
    _token: this.token,
  };
  let url = $("#alliancecomponent .createNewAlliance").attr("rel");
  $.getJSON(url, data, this.onFetch.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.fetch = function (targetTab) {
  let target = $("#alliancecomponent ." + targetTab);

  if (target.attr("rel") !== "") {
    this.loadingIndicator.show();
    let data = {
      _token: this.token,
    };
    $.getJSON(target.attr("rel"), data, this.onFetch.bind(this)).done(this.onAjaxDone.bind(this));
    this.tabs.forEach(function (item) {
      let element = $("#alliancecomponent #tab-ally ." + item).parent();
      element.removeClass("aktiv");

      if (item === targetTab) {
        element.addClass("aktiv");
      }
    });
  }
};

Alliance.prototype.onFetch = function (data) {
  let htmlItems = data.content[data.target];
  this.updateToken(data.newAjaxToken);
  this.refreshContent(htmlItems);

  // Set URLs from AJAX response if available
  if (data.urlCreateAlliance) {
    this.urlCreateAlliance = data.urlCreateAlliance;
  }
  if (data.urlSendApplication) {
    this.urlSendApplication = data.urlSendApplication;
  }
  if (data.urlCancelApplication) {
    this.urlCancelApplication = data.urlCancelApplication;
  }
  // Overview tab URLs
  if (data.urlKickMember) {
    this.urlKickMember = data.urlKickMember;
  }
  if (data.urlSubmitRanks) {
    this.urlSubmitRanks = data.urlSubmitRanks;
  }
  if (data.urlLeaveAlliance) {
    this.urlLeaveAlliance = data.urlLeaveAlliance;
  }
  // Management tab URLs
  if (data.urlCreateRank) {
    this.urlCreateRank = data.urlCreateRank;
  }
  if (data.urlUpdateRank) {
    this.urlUpdateRank = data.urlUpdateRank;
  }
  if (data.urlDeleteRank) {
    this.urlDeleteRank = data.urlDeleteRank;
  }
  if (data.urlUpdateAllianceText) {
    this.urlUpdateAllianceText = data.urlUpdateAllianceText;
  }
  if (data.urlUpdateSettings) {
    this.urlUpdateSettings = data.urlUpdateSettings;
  }
  if (data.urlUpdateTag) {
    this.urlUpdateTag = data.urlUpdateTag;
  }
  if (data.urlUpdateName) {
    this.urlUpdateName = data.urlUpdateName;
  }
  if (data.urlDissolve) {
    this.urlDissolve = data.urlDissolve;
  }
  if (data.urlTransferLeadership) {
    this.urlTransferLeadership = data.urlTransferLeadership;
  }
  if (data.urlTakeoverLeadership) {
    this.urlTakeoverLeadership = data.urlTakeoverLeadership;
  }
  // Applications tab URLs
  if (data.urlAccept) {
    this.urlAccept = data.urlAccept;
  }
  if (data.urlDeny) {
    this.urlDeny = data.urlDeny;
  }
  if (data.urlReport) {
    this.urlReport = data.urlReport;
  }
  // Broadcast tab URLs
  if (data.urlSend) {
    this.urlSend = data.urlSend;
  }
};

Alliance.prototype.updateToken = function (newtoken) {
  this.token = newtoken;
  token = newtoken;
};

Alliance.prototype.refreshTabs = function (tabsObj) {
  if (tabsObj.applications.applicationCount >= 1) {
    $("." + tabsObj.applications.tab + " #applicationTab")
      .removeClass("undermark")
      .addClass("undermark");
    $("." + tabsObj.applications.tab + " #applicationTab span")
      .removeClass("undermark")
      .addClass("undermark")
      .html("(" + tabsObj.applications.applicationCount + ")");
  } else {
    $("." + tabsObj.applications.tab + " #applicationTab").removeClass("undermark");
    $("." + tabsObj.applications.tab + " #applicationTab span")
      .removeClass("undermark")
      .html("");
  }
};

Alliance.prototype.onClickDenyApplication = function (e) {
  e.preventDefault();
  let playerId = $(e.currentTarget).data("playerid");
  this.submitDenyApplication(playerId);
};

Alliance.prototype.submitDenyApplication = function (playerId) {
  let params = {
    playerId: playerId,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlDeny, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onFormClickDenyApplication = function (e) {
  e.preventDefault();
  let playerId = $(e.currentTarget).data("playerid");
  let reasonText = $(e.currentTarget).closest("form").find(".alliancetexts").val();
  let params = {
    playerId: playerId,
    reasonText: reasonText,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlDeny, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onClickAcceptApplication = function (e) {
  e.preventDefault();
  let playerId = $(e.currentTarget).data("playerid");
  this.submitAcceptApplication(playerId);
};

Alliance.prototype.submitAcceptApplication = function (playerId) {
  let params = {
    playerId: playerId,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlAccept, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.onFormClickAcceptApplication = function (e) {
  e.preventDefault();
  let playerId = $(e.currentTarget).data("playerid");
  let reasonText = $(e.currentTarget).closest("form").find(".alliancetexts").val();
  let params = {
    playerId: playerId,
    reasonText: reasonText,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlAccept, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Alliance.prototype.handleResponse = function (response) {
  // Handle both success and error callbacks
  let data;

  // If called from error callback (jqXHR object)
  if (response && response.responseJSON) {
    data = response.responseJSON;
    console.log("Alliance Error Response:", data);
  } else {
    // Handle both string and object responses
    data = typeof response === "string" ? JSON.parse(response) : response;
  }

  let status = data.status || "failure";

  if (data.newAjaxToken) {
    this.updateToken(data.newAjaxToken);
  }

  if (status === "success") {
    if (data.redirectUrl !== undefined) {
      window.location = data.redirectUrl;
    } else {
      if (data.tabs !== undefined) {
        this.refreshTabs(data.tabs);
      }

      fadeBox(data.message, false);
      getAjaxEventbox();
      // Don't call getAjaxResourcebox() - alliance operations don't affect resources
      this.fetch(this.tab);
    }
  } else {
    console.log("Alliance operation failed:", {
      message: data.message,
      errors: data.errors,
      fullResponse: data,
    });

    if (data.tabs !== undefined) {
      this.refreshTabs(data.tabs);
    }

    this.displayErrors(data.errors);
  }
};

Alliance.prototype.onFormClickBroadcastButton = function (e) {
  e.preventDefault();
  let rankIds = $("#selectNew").val();
  let broadcastText = $("#allianceBroadCast").find(".alliancetexts").val();
  let params = {
    rankIds: rankIds,
    broadcastText: broadcastText,
    _token: this.token,
  };
  this.loadingIndicator.show();
  $.post(this.urlSend, params, this.handleResponse.bind(this)).done(this.onAjaxDone.bind(this));
};
