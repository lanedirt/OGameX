function HappyEdit(cfg) {
  this.tab = cfg.tab || "";
  this.urlSubmitPlayer = cfg.urlSubmitPlayer;
  this.urlSubmitBuildings = cfg.urlSubmitBuildings;
  this.urlSubmitResearch = cfg.urlSubmitResearch;
  this.urlSubmitShips = cfg.urlSubmitShips;
  this.urlSubmitDefense = cfg.urlSubmitDefense;
  this.urlSubmitPlanet = cfg.urlSubmitPlanet;
  this.urlSubmitWreckfield = cfg.urlSubmitWreckfield;
  this.urlSubmitFleet = cfg.urlSubmitFleet;
  this.urlSubmitRewards = cfg.urlSubmitRewards;
  this.urlRestartEventHandler = cfg.urlRestartEventHandler;
  this.urlTriggerNews = cfg.urlTriggerNews;
  this.urlSubmitBuffs = cfg.urlSubmitBuffs;
  this.tabs = {
    player: cfg.urlFetchPlayerData || null,
    buildings: cfg.urlFetchBuildingsData || null,
    research: cfg.urlFetchResearchData || null,
    ships: cfg.urlFetchShipsData || null,
    defenses: cfg.urlFetchDefensesData || null,
    wreckfield: cfg.urlFetchWreckfieldData || null,
    rewards: cfg.urlFetchRewardsData || null,
    trader: cfg.urlFetchTraderData || null,
    planet: cfg.urlFetchPlanetData || null,
    fleet: cfg.urlFetchFleetData || null,
    eventHandler: cfg.urlFetchEventHandlerData || null,
    lifeform: cfg.urlFetchLifeformData || null,
    lfbuilding: cfg.urlFetchLifeformBuildingData || null,
    lfresearch: cfg.urlFetchLifeformResearchData || null,
    buffs: cfg.urlFetchBuffData || null,
    ipi: cfg.urlFetchIpiData || null,
  };
  this.initItemsCommon(cfg);
  this.fetchData(this.tab);
}

HappyEdit.prototype.onAjaxDone = function () {
  this.loadingIndicator.hide();
  let that = this;
  let lvlOfAll = $("#lvlOfAllBuilding");

  switch (this.tab) {
    case "player":
      $(".triggerNews").bind("click", that.triggerNews.bind(that));
      $(".playerSubmit").bind("click", that.onClickSavePlayerData.bind(that));
      break;

    case "buildings":
      $(".buildingsSubmit").bind("click", that.onClickSaveBuildingsData.bind(that));
      break;

    case "research":
      $(".researchSubmit").bind("click", that.onClickSaveResearchData.bind(that));
      break;

    case "ships":
      $(".shipsSubmit").bind("click", that.onClickSaveShipsData.bind(that));
      break;

    case "defenses":
      $(".defenseSubmit").bind("click", that.onClickSaveDefensesData.bind(that));
      break;

    case "planet":
      $(".planetSubmit").bind("click", that.onClickSavePlanetData.bind(that));
      $("#resetBashing .ajax").bind("click", that.onClickResetBashing.bind(that));
      break;

    case "wreckfield":
      $(".wreckfieldSubmit").bind("click", that.onClickSaveWreckfieldData.bind(that));
      break;

    case "rewards":
      $(".rewardsSubmit").bind("click", that.onClickSaveRewardsData.bind(that));
      break;

    case "trader":
      $("#traderEdit .ajax").each(function () {
        $(this).bind("click", that.onItemClick.bind(that));
      });
      break;

    case "fleet":
      $(".finishFleet").bind("click", that.onClickFinishFleet.bind(that));
      break;

    case "lifeform":
      $("#lifeformSettings .lfdiscover").bind("click", that.submitDiscoverData.bind(that));
      $(".lifeformSettingsSubmit").bind("click", that.onClickSaveLifeformData.bind(that));
      break;

    case "lfbuilding":
      lvlOfAll.bind("focus", function (e) {
        $(this).val("");
      });
      lvlOfAll.bind("keyup", function (e) {
        let that = this;
        let inputVal = $(that).val();

        if ($.isNumeric(inputVal) === false) {
          $(that).val("level of all");
        } else {
          $("#lfbuilding input.textInput").each(function () {
            $(this).val(inputVal);
          });
        }
      });
      $(".lifeformBuildingSubmit").bind("click", that.onClickSaveLifeformBuildingData.bind(that));
      break;

    case "lfresearch":
      lvlOfAll.bind("focus", function (e) {
        $(this).val("");
      });
      lvlOfAll.bind("keyup", function (e) {
        let that = this;
        let inputVal = $(that).val();

        if ($.isNumeric(inputVal) === false) {
          $(that).val("level of all");
        } else {
          $("#lfresearch input.textInput").each(function () {
            $(this).val(inputVal);
          });
        }
      });
      $("#lfresearch .radioResearch").bind("focus", that.onClickSelectResearch.bind(that));
      $("#lfresearch .classSelector").bind("click", that.onClickSelectAllResearch.bind(that));
      $(".lifeformResearchSubmit").bind("click", that.onClickSaveLifeformResearchData.bind(that));
      break;

    case "eventHandler":
      $("#eventHandlerSubmit").bind("click", that.onClickRestartEventHandler.bind(that));
      break;

    case "buffs":
      $("#buffEditForm button.submit").bind("click", that.onClickSaveBuffs.bind(that));
      $('#buffEditForm input[type="datetime-local"]').bind("change", that.onChangeBuffTime.bind(that));
      break;

    case "ipi":
      $("#ipiEdit .ajax").bind("click", that.onItemClick.bind(that));
      break;
  }
};

HappyEdit.prototype.onItemClick = function (e) {
  e.preventDefault();
  $.post($(e.currentTarget).data("link"), {}, this.handleSubmitResponse.bind(this));
};

HappyEdit.prototype.onClickResetBashing = function (e) {
  e.preventDefault();
  let galaxy,
    system,
    position = 0;
  galaxy = document.getElementById("bashingGalaxy").value ?? -1;
  system = document.getElementById("bashingSystem").value ?? -1;
  position = document.getElementById("bashingPosition").value ?? -1;
  $.post(
    $(e.currentTarget).data("link"),
    {
      galaxy: galaxy,
      system: system,
      position: position,
    },
    this.handleSubmitResponse.bind(this),
  );
};

HappyEdit.prototype.fetchData = function (tab) {
  this.loadingIndicator.show();
  $.getJSON(this.tabs[tab], {}, this.onFetch.bind(this)).done(this.onAjaxDone.bind(this));
};

HappyEdit.prototype.displayErrors = function (errors) {
  // only display the first error
  let error = errors[0] || undefined;

  if (error) {
    fadeBox(error.message, true);
  }
}; //
// Item tabs general
//

HappyEdit.prototype.onClickTab = function (e) {
  e.preventDefault();

  if ($(e.currentTarget).parent().attr("disabled") !== "disabled") {
    this.loadingIndicator.show();
    this.tab = $(e.currentTarget).data("tab");
    this.fetch(this.tab);
  }
};

HappyEdit.prototype.onFetch = function (data) {
  let htmlItems = data.content[data.target];
  this.refreshItems(htmlItems);
};

HappyEdit.prototype.fetch = function (targetTab) {
  const target = $("#happyeditcomponent .tabs ." + targetTab);

  if (target.attr("rel") !== "") {
    $.getJSON(target.attr("rel"), {}, this.onFetch.bind(this)).done(this.onAjaxDone.bind(this));
    Object.keys(this.tabs).forEach((item) => {
      const element = $("#happyeditcomponent .tabs ." + item).parent();
      element.removeClass("active");

      if (item === targetTab) {
        element.addClass("active");
      }
    });
  }
};

HappyEdit.prototype.initItemsCommon = function (cfg) {
  this.itemsWrapper = $("#happyeditcomponent .wrapper");
  this.loadingIndicator = this.itemsWrapper.ogameLoadingIndicator();
  this.happyeditContent = $("#happyeditcomponent .content");
  this.titlebar = $("#happyeditcomponent .tabs");
  Object.keys(this.tabs).forEach((initTab) => this.titlebar.on("click", "." + initTab, this.onClickTab.bind(this)));
};

HappyEdit.prototype.refreshItems = function (htmlItems) {
  this.happyeditContent.html(htmlItems);
};

HappyEdit.prototype.handleSubmitResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";

  if (status === "success") {
    this.fetchData(this.tab);
    fadeBox(data.message, false);
    getAjaxEventbox();
    getAjaxResourcebox();
  } else {
    this.displayErrors(data.errors);
  }
}; //
// Tab: player
//

HappyEdit.prototype.onClickSavePlayerData = function (e) {
  e.preventDefault();

  if (!e.currentTarget.hasAttribute("disabled")) {
    this.submitPlayerData($("#playerSettings").serializeArray());
  }
};

HappyEdit.prototype.triggerNews = function (e) {
  e.preventDefault();
  $.post(this.urlTriggerNews, {}, this.handleSubmitResponse.bind(this));
};

HappyEdit.prototype.submitPlayerData = function (formData) {
  this.loadingIndicator.show();
  $.post(this.urlSubmitPlayer, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
}; //
// Tab: Buildings
//

HappyEdit.prototype.onClickSaveBuildingsData = function (e) {
  e.preventDefault();
  let allBuilding = $('[name="allBuildings"]').val();

  if (allBuilding.length === 0) {
    let formData = $("#buildingsSettings")
      .serializeArray()
      .filter(function (obj) {
        return obj.name !== "allBuildings";
      });
    this.submitBuildingsData(formData);
  } else {
    this.submitBuildingsData({
      allBuildings: allBuilding,
    });
  }
};

HappyEdit.prototype.submitBuildingsData = function (formData) {
  this.loadingIndicator.show();
  $.post(this.urlSubmitBuildings, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
}; //
// Tab: Research
//

HappyEdit.prototype.onClickSaveResearchData = function (e) {
  e.preventDefault();
  let allResearches = $('[name="allResearches"]').val();

  if (allResearches.length === 0) {
    let formData = $("#researchSettings")
      .serializeArray()
      .filter(function (obj) {
        return obj.name !== "allResearches";
      });
    this.submitResearchData(formData);
  } else {
    this.submitResearchData({
      allResearches: allResearches,
    });
  }
};

HappyEdit.prototype.submitResearchData = function (formData) {
  this.loadingIndicator.show();
  $.post(this.urlSubmitResearch, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
}; //
// Tab: Ships
//

HappyEdit.prototype.onClickSaveShipsData = function (e) {
  e.preventDefault();
  let allShips = $('[name="allShips"]').val();

  if (allShips.length === 0) {
    let formData = $("#shipsSettings")
      .serializeArray()
      .filter(function (obj) {
        return obj.name !== "allShips";
      });
    this.submitShipsData(formData);
  } else {
    this.submitShipsData({
      allShips: allShips,
    });
  }
};

HappyEdit.prototype.submitShipsData = function (formData) {
  this.loadingIndicator.show();
  $.post(this.urlSubmitShips, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
}; //
// Tab: Defenses
//

HappyEdit.prototype.onClickSaveDefensesData = function (e) {
  e.preventDefault();
  let allDefense = $('[name="allDefense"]').val();

  if (allDefense.length === 0) {
    let formData = $("#defenseSettings")
      .serializeArray()
      .filter(function (obj) {
        return obj.name !== "allDefense";
      });
    this.submitDefensesData(formData);
  } else {
    this.submitDefensesData({
      allDefense: allDefense,
    });
  }
};

HappyEdit.prototype.submitDefensesData = function (formData) {
  this.loadingIndicator.show();
  $.post(this.urlSubmitDefense, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
}; //
// Tab: Wreckfield
//

HappyEdit.prototype.onClickSaveWreckfieldData = function (e) {
  e.preventDefault();
  let allShips = $('[name="allShips"]').val();

  if (allShips.length === 0) {
    let formData = $("#wreckfieldSettings")
      .serializeArray()
      .filter(function (obj) {
        return obj.name !== "allShips";
      });
    this.submitWreckfieldData(formData);
  } else {
    this.submitWreckfieldData({
      allShips: allShips,
    });
  }
};

HappyEdit.prototype.submitWreckfieldData = function (formData) {
  this.loadingIndicator.show();
  $.post(this.urlSubmitWreckfield, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
}; //
// Tab: Rewards
//

HappyEdit.prototype.onClickSaveRewardsData = function (e) {
  e.preventDefault();

  if (!e.currentTarget.hasAttribute("disabled")) {
    this.submitRewardsData($("#rewardsSettings").serializeArray());
  }
};

HappyEdit.prototype.submitRewardsData = function (formData) {
  this.loadingIndicator.show();
  $.post(this.urlSubmitRewards, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
}; //
// Tab: Planet
//

HappyEdit.prototype.onClickSavePlanetData = function (e) {
  e.preventDefault();

  if (!e.currentTarget.hasAttribute("disabled")) {
    this.submitPlanetData($("#planetSettings").serializeArray());
  }
};

HappyEdit.prototype.submitPlanetData = function (formData) {
  this.loadingIndicator.show();
  $.post(this.urlSubmitPlanet, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
}; //
// Tab: Fleet
//

HappyEdit.prototype.onClickFinishFleet = function (e) {
  e.preventDefault();
  this.submitFinishFleet($(e.target).attr("data-fleet-id"));
};

HappyEdit.prototype.submitFinishFleet = function (id) {
  this.loadingIndicator.show();
  $.post(
    this.urlSubmitFleet,
    {
      fleetId: id,
    },
    this.handleSubmitResponse.bind(this),
  ).done(this.onAjaxDone.bind(this));
}; //
// Tab: Event handler
//

HappyEdit.prototype.onClickRestartEventHandler = function (e) {
  e.preventDefault();
  $.post(this.urlRestartEventHandler, {}, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
}; // Lifeform

HappyEdit.prototype.submitDiscoverData = function (e) {
  //this.loadingIndicator.show()
  let targetUrl = $(e.currentTarget).data("link");
  let lfId = $(e.currentTarget).data("id");
  let data = {
    lifeformId: lfId,
  };
  $.post(targetUrl, data, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

HappyEdit.prototype.onClickSaveLifeformData = function (e) {
  e.preventDefault();

  if (!e.currentTarget.hasAttribute("disabled")) {
    let formData = $("#lifeformSettings").serializeArray();
    this.loadingIndicator.show();
    let targetUrl = $(e.currentTarget).data("link");
    $.post(targetUrl, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
  }
};

HappyEdit.prototype.onClickSaveLifeformBuildingData = function (e) {
  e.preventDefault();

  if (!e.currentTarget.hasAttribute("disabled")) {
    let formData = $("#lifeformBuilding").serializeArray();
    this.loadingIndicator.show();
    let targetUrl = $(e.currentTarget).data("link");
    $.post(targetUrl, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
  }
};

HappyEdit.prototype.onClickSelectResearch = function (e) {
  e.preventDefault();
  let elemName = $(e.currentTarget).attr("name");
  let selectedElem = $("#lfresearch input[name='" + elemName + "']:checked");
  let previousTechId = selectedElem.data("techid");
  let currentTechId = $(e.currentTarget).data("techid");
  let previousLifeformId = selectedElem.data("lifeformid");
  let currentLifeformId = $(e.currentTarget).data("lifeformid");
  let slot = $(e.currentTarget).data("slot");
  let pic = $("#slotPic" + slot);

  if (currentLifeformId === 0) {
    $(".slotName" + slot).text("None");
    pic
      .removeClass("lifeformsprite")
      .removeClass("queuePic")
      .removeClass("lifeformTech" + previousTechId)
      .addClass("lifeformTech0");
  } else if (previousLifeformId === 0 && currentLifeformId !== 0) {
    $(".slotName" + slot).text(lfResearch[slot][currentLifeformId].name);
    pic
      .removeClass("lifeformTech0")
      .addClass("lifeformsprite")
      .addClass("queuePic")
      .addClass("lifeformTech" + currentTechId);
  } else {
    $(".slotName" + slot).text(lfResearch[slot][currentLifeformId].name);
    pic.removeClass("lifeformTech" + previousTechId).addClass("lifeformTech" + currentTechId);
  }
};

HappyEdit.prototype.onClickSelectAllResearch = function (e) {
  e.preventDefault();
  let lifeformId = $(e.currentTarget).data("lifeformid");
  let selectedElements = $("#lfresearch input[data-lifeformid='" + lifeformId + "']");
  selectedElements.each(function () {
    let elemName = $(this).attr("name");
    let selectedElem = $("#lfresearch input[name='" + elemName + "']:checked");
    let previousTechId = selectedElem.data("techid");
    let currentTechId = $(this).data("techid");
    let previousLifeformId = selectedElem.data("lifeformid");
    let currentLifeformId = $(this).data("lifeformid");
    let slot = $(this).data("slot");
    let pic = $("#slotPic" + slot);

    if (currentLifeformId === 0) {
      $(".slotName" + slot).text("None");
      pic
        .removeClass("lifeformsprite")
        .removeClass("queuePic")
        .removeClass("lifeformTech" + previousTechId)
        .addClass("lifeformTech0");
    } else if (previousLifeformId === 0 && currentLifeformId !== 0) {
      $(".slotName" + slot).text(lfResearch[slot][currentLifeformId].name);
      pic
        .removeClass("lifeformTech0")
        .addClass("lifeformsprite")
        .addClass("queuePic")
        .addClass("lifeformTech" + currentTechId);
    } else {
      $(".slotName" + slot).text(lfResearch[slot][currentLifeformId].name);
      pic.removeClass("lifeformTech" + previousTechId).addClass("lifeformTech" + currentTechId);
    }

    $(this).prop("checked", true);
  });
};

HappyEdit.prototype.onClickSaveLifeformResearchData = function (e) {
  e.preventDefault();

  if (!e.currentTarget.hasAttribute("disabled")) {
    let formData = $("#lifeformResearch").serializeArray();
    this.loadingIndicator.show();
    let targetUrl = $(e.currentTarget).data("link");
    $.post(targetUrl, formData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
  }
};

HappyEdit.prototype.onClickSaveBuffs = function (e) {
  e.preventDefault();
  this.loadingIndicator.show();

  if (e.target.value === "all") {
    const updateData = {
      targets: [],
      buffAction: e.target.name === "buffDeleteAll" ? "delete" : "update",
    };
    document.querySelectorAll("#buffEditForm .happyedit-buffs-buff").forEach((buff) => {
      updateData.targets.push({
        id: buff.dataset.target,
        effectTime: buff.querySelector('input[data-type="buffEffectTime"]').value,
        cooldownTime: buff.querySelector('input[data-type="buffCooldownTime"]').value,
      });
    });
    $.post(this.urlSubmitBuffs, updateData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
    return;
  }

  const updateData = {
    targets: [
      {
        id: e.target.value,
        effectTime: document.querySelector(
          '#buffEditForm input[data-type="buffEffectTime"][data-target="' + e.target.value + '"]',
        ).value,
        cooldownTime: document.querySelector(
          '#buffEditForm input[data-type="buffCooldownTime"][data-target="' + e.target.value + '"]',
        ).value,
      },
    ],
    buffAction: e.target.name === "buffDelete" ? "delete" : "update",
  };
  $.post(this.urlSubmitBuffs, updateData, this.handleSubmitResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

HappyEdit.prototype.onChangeBuffTime = function (e) {
  if (e.target.dataset.type === "buffEffectTime") {
    const cooldownTarget = document.querySelector(
      '#buffEditForm input[data-type="buffCooldownTime"][data-target="' + e.target.dataset.target + '"]',
    );

    if (cooldownTarget.value < e.target.value) {
      cooldownTarget.value = e.target.value;
    }
  }
};
