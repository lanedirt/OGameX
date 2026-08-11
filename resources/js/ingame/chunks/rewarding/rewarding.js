function Rewarding(cfg) {
  this.tab = cfg.tab || "";
  this.token = cfg.token;
  this.tiers = cfg.tiers;
  this.currentTier = cfg.currentTier;
  this.urlFetchTasks = cfg.urlFetchTasks || null;
  this.urlFetchRewards = cfg.urlFetchRewards || null;
  this.selectedTier = cfg.selectedTier || 1;
  this.initMap = {
    tasks: this.initTabTasks.bind(this),
    rewards: this.initTabRewards.bind(this),
  };

  if (this.initMap[this.tab]) {
    this.initMap[this.tab](cfg);
  }
}

Rewarding.prototype.onAjaxTasksDone = function () {
  this.loadingIndicator.hide();
};

Rewarding.prototype.onAjaxSelectDone = function () {
  this.loadingIndicator.hide();
};

Rewarding.prototype.onAjaxRewardsDone = function () {
  this.loadingIndicator.hide();
  let that = this;
  that.urlSubmitReward = urlSubmitReward;
  $("#rewardings .normalRewards .singleReward").each(function () {
    this.urlSubmitReward = that.urlSubmitReward;
    this.token = that.token;
    $(this).bind("click", that.onClickButtonSelectReward.bind(that));
    $(this).hover(
      function () {
        $(this).find(".select-button").css("background-position", "0 -214px");
      },
      function () {
        $(this).find(".select-button").css("background-position", "0 -188px");
      },
    );
  });
};

Rewarding.prototype.displayErrors = function (errors) {
  // only display the first error
  let error = errors[0] || undefined;

  if (error) {
    fadeBox(error.message, true);
  }
};

Rewarding.prototype.onAjaxError = function () {}; // general

Rewarding.prototype.initCommon = function (cfg) {
  this.taskWrapper = $("#rewardings .rewardlist_wrapper");
  this.loadingIndicator = this.taskWrapper.ogameLoadingIndicator();
  this.taskContent = $("#rewardings .rewardContent");
  this.titlebar = $("#rewardings .titlebar");
  this.tierButton = $("#rewardings #tothetier");
};

Rewarding.prototype.refreshContent = function (htmlItems) {
  this.taskContent.html(htmlItems);
}; //tab tasks

Rewarding.prototype.initTabTasks = function (cfg) {
  this.initCommon(cfg);
  this.titlebar.on("click", "#buttonTasks", this.onClickButtonTasks.bind(this));

  for (var key in this.tiers) {
    if (this.tiers.hasOwnProperty(key)) {
      this.titlebar.on("click", 'div[data-tier="' + key + '"]', this.onClickButtonRewards.bind(this));
    }
  }

  this.tierButton.on("click", ".tier-button", this.onClickButtonCurrentTier.bind(this));
  this.fetchTasks();
};

Rewarding.prototype.onClickButtonTasks = function (e) {
  e.preventDefault();

  if (!e.currentTarget.hasAttribute("disabled")) {
    this.fetchTasks();
    $("#rewardings .titlebar #buttonTasks").attr("disabled", "disabled");

    for (var key in this.tiers) {
      if (this.tiers.hasOwnProperty(key)) {
        $('#rewardings .titlebar div[data-tier="' + key + '"]').removeAttr("disabled");
      }
    }
  }
};

Rewarding.prototype.onClickButtonRewards = function (e) {
  e.preventDefault();
  let targetTier = $(e.currentTarget).data("tier");

  if (!e.currentTarget.hasAttribute("disabled")) {
    this.fetchRewards(targetTier);
  }
};

Rewarding.prototype.onClickButtonCurrentTier = function (e) {
  e.preventDefault();
  let targetTier = this.currentTier;
  this.fetchRewards(targetTier);
};

Rewarding.prototype.onClickButtonSelectReward = function (e) {
  e.preventDefault();

  if (!e.currentTarget.hasAttribute("disabled")) {
    this.submitReward($(e.currentTarget).data("id"));
  }
};

Rewarding.prototype.fetchTasks = function (targetTab) {
  this.loadingIndicator.show();
  let data = {
    _token: this.token,
  };
  $.getJSON(this.urlFetchTasks, data, this.onFetchTasks.bind(this)).done(this.onAjaxTasksDone.bind(this));
};

Rewarding.prototype.onFetchTasks = function (data) {
  let htmlItems = data.content[data.target];
  this.token = data.newAjaxToken;
  this.refreshContent(htmlItems);
}; //tabs rewards

Rewarding.prototype.initTabRewards = function (cfg) {
  this.initCommon(cfg);
  this.titlebar.on("click", "#buttonTasks", this.onClickButtonTasks.bind(this));

  for (let key in this.tiers) {
    if (this.tiers.hasOwnProperty(key)) {
      this.titlebar.on("click", 'div[data-tier="' + key + '"]', this.onClickButtonRewards.bind(this));
    }
  }

  this.tierButton.on("click", ".tier-button", this.onClickButtonCurrentTier.bind(this));
  this.fetchRewards(this.selectedTier);
};

Rewarding.prototype.fetchRewards = function (targetTier) {
  this.loadingIndicator.show();
  let data = {
    tier: targetTier,
    _token: this.token,
  };
  this.selectedTier = targetTier;
  $.getJSON(this.urlFetchRewards, data, this.onFetchRewards.bind(this)).done(this.onAjaxRewardsDone.bind(this));

  for (var key in this.tiers) {
    if (this.tiers.hasOwnProperty(key)) {
      $('#rewardings .titlebar div[data-tier="' + key + '"]').removeAttr("disabled");
    }
  }

  $("#rewardings .titlebar #buttonTasks").removeAttr("disabled");
  $('#rewardings .titlebar div[data-tier="' + targetTier + '"]').attr("disabled", "disabled");
};

Rewarding.prototype.onFetchRewards = function (data) {
  let htmlItems = data.content[data.target];
  this.token = data.newAjaxToken;
  this.refreshContent(htmlItems);
};

Rewarding.prototype.submitReward = function (selectedReward) {
  this.loadingIndicator.show();
  let params = {
    selectedReward: selectedReward,
    selectedTier: this.selectedTier,
    _token: this.token,
  };
  $.post(this.urlSubmitReward, params, this.handleSubmitRewardResponse.bind(this)).done(
    this.onAjaxSelectDone.bind(this),
  );
};

Rewarding.prototype.handleSubmitRewardResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  this.token = data.newAjaxToken;

  if (status === "success") {
    let selectOne = $("#rewardings #select_one");
    selectOne.removeClass("overmark");
    selectOne.addClass("undermark");
    selectOne.html(data.rewardSelected);
    let titleBarButton = $('#rewardings .titlebar .btn_blue[data-tier="' + data.selectedTier + '"]');
    titleBarButton.addClass("undermark");
    $("#rewardings .normalRewards .singleReward").each(function () {
      $(this).attr("disabled", "disabled");

      if ($(this).data("id") !== data.selectedReward) {
        $("#rewardings .normalRewards #itemBox" + $(this).data("id") + " .thumbnail")
          .data("status", "off")
          .attr("data-status", "off");
      }
    });
    $("#rewardings .normalRewards .select-button").each(function () {
      $(this).remove();
    });

    if (data.allOfficers === false) {
      $("#rewardings .additionalRewards .singleReward").each(function () {
        $("#rewardings .additionalRewards #itemBox" + $(this).data("id") + " .thumbnail")
          .data("status", "off")
          .attr("data-status", "off");
      });
    }

    fadeBox(data.message, false);
  } else {
    this.displayErrors(data.errors);
  }
};
