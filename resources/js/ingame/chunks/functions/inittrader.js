function initTrader() {
  var disableAnimationOnce = false;
  var url = $.deparam.fragment();

  if (typeof url["animation"] != "undefined") {
    if (url["animation"] == "false") {
      disableAnimationOnce = true;
    }
  } // toggle overview-panel

  var toggleDisplay = {
    $link: null,
    $panel: null,
    close: function () {
      this.$panel.hide();
    },
    setPanel: function () {
      this.$panel = $("#js_togglePanel" + traderObj.traderId.replace(/#div_trader/, ""));
    },
    init: function (traderId) {
      var $wrapper = $(traderId + " .selectWrapper");
      traderId = traderId.replace(/#div_trader/, "");
      if (!$wrapper) return;
      this.$link = $("#js_toggleLink" + traderId);
      this.$panel = $("#js_togglePanel" + traderId);
      var $panel = this.$panel; // hide panel initially

      $panel.hide(); // bind toggle fn

      this.$link.unbind("click.selectLink").bind("click.selectLink", function (e) {
        traderId = traderObj.traderId.replace(/#div_trader/, "");

        if ($(this).hasClass("honor")) {
          return false;
        }

        if ($panel.find("ul.active").has("li").length) {
          $panel.toggle();
        }

        return false;
      }); //event for planet or moon select

      $(".selectWrapper .source")
        .unbind("click.selectPlanetOrMoon")
        .bind("click.selectPlanetOrMoon", function (e) {
          toggleDisplay.selectPlanetOrMoon(this);
        }); // event for list item, use live instead of bind, because list elements can be created dynamically

      $("#" + $panel.attr("id"))
        .find("li")
        .unbind("click.selectSource")
        .bind("click.selectSource", function (e) {
          traderObj.selectSource(this);
          return false;
        }); // click outside of panel

      toggleDisplay.outerClick($wrapper, $panel); // delayed panel self-closing

      var timeoutID = null,
        delay = 8000;
      $wrapper
        .unbind("mouseout.closeSelect")
        .bind("mouseout.closeSelect", function (e) {
          // custom mouseleave event
          e = e || window.event;
          var reltg = e.relatedTarget ? e.relatedTarget : e.toElement;

          if (reltg == $wrapper || toggleDisplay.isChildOf(reltg, $wrapper)) {
            return;
          } // hide after timeout

          timeoutID = setTimeout(function () {
            $panel.hide();
          }, delay);
        })
        .unbind("mouseover.clearTimeout")
        .bind("mouseover.clearTimeout", function () {
          if (timeoutID) {
            clearTimeout(timeoutID);
          }
        });
    },
    // helper function for custom mouseleave
    //TODO: is this still needed?
    isChildOf: function (child, parent) {
      parent = parent[0];

      while (child && child != parent) {
        child = child.parentNode;
      }

      return child == parent;
    },
    outerClick: function ($wrapper, $panel) {
      $("body").bind("click.outerClick", function (e) {
        if (!e) {
          e = window.event;
        }

        if (!($(e.target).closest(".selectWrapper") == $wrapper) && $panel.is(":visible") != false) {
          $panel.toggle();
        }
      });
    },
    selectPlanetOrMoon: function (elem) {
      var planet;

      if ($(elem).hasClass("selected")) {
        return false;
      }

      var selectedContent = "",
        selectedPlanetId = null;

      if ($(elem).hasClass("js_honor")) {
        $(traderObj.traderId + " .selectWrapper .source").removeClass("selected");
        $(traderObj.traderId + " .js_honor").addClass("selected");
        $(traderObj.traderId + " .toggleLink").addClass("honor");
        var selectedContent =
          '<img height="18" src="/img/icons/f35675179214f8f6f0f8d75740d7db.png" alt="' +
          loca.honorPoints +
          '"/>' +
          '<span class="option_source">' +
          loca.honorPoints +
          "</span>";
        $(traderObj.traderId + " .js_valSourcePlanet").html(selectedContent);
        $(traderObj.traderId + " .normalResource").hide();
        $(traderObj.traderId + " .honorResource").show();
        return false;
      }

      var $togglePanel = $(traderObj.traderId + " .togglePanel");
      var className = "planet";

      if ($(elem).hasClass("js_moon")) {
        var moonCounter = 0;
        className = "moon";

        for (planet in traderObj.planets) {
          if (traderObj.planets[planet].isMoon) moonCounter++;
        }

        if (moonCounter == 0) return false;
      }

      var currentPlanet = traderObj.planets[traderObj.current.planet];
      $togglePanel.find("ul").hide().removeClass("active");
      $togglePanel
        .find("ul." + className)
        .show()
        .addClass("active");
      $(traderObj.traderId + " .toggleLink").removeClass("honor");
      $(traderObj.traderId + " .selectWrapper .source").removeClass("selected");
      $(traderObj.traderId + " .js_" + className).addClass("selected");

      if ($(elem).hasClass("js_moon") ? currentPlanet.isMoon : !currentPlanet.isMoon) {
        selectedPlanetId = traderObj.current.planet;
      } else if (
        currentPlanet.otherPlanetId != null &&
        typeof traderObj.planets[currentPlanet.otherPlanetId] != "undefined"
      ) {
        selectedPlanetId = currentPlanet.otherPlanetId;
      } else {
        selectedPlanetId = $togglePanel.find("ul." + className + " li:first").attr("id");
      }

      $(traderObj.traderId + " .normalResource").show();
      $(traderObj.traderId + " .honorResource").hide();
      $togglePanel.find("ul li#" + selectedPlanetId).click();
      return false;
    },
    setToggleLink: function (link) {
      // we need to redefine the traderId here so we act on the right $link and $panel:
      var traderId = traderObj.traderId.replace(/#div_trader/, ""),
        $span = $(link).find("span"),
        planetName = traderObj.planets[$(link).attr("id")].name;

      if (planetName != $span.text()) {
        $span.attr("title", planetName.replace(/\|/g, "&#124;"));
      }

      this.$link = $("#js_toggleLink" + traderId);
      this.$link.html($(link).html());
    },
  };
  /*
   * Object for Auctioneer and ImportExport Slider
   */

  traderObj = {
    traderBGPos: {
      "#div_traderResources": "0px 0px",
      "#div_traderAuctioneer": "-546px 0px",
      "#div_traderScrap": "0px -220px",
      "#div_traderImportExport": "-546px -220px",
    },
    timer: 500,
    // Wenn direkt auf Unterseite -> timer = 0
    planets: {},
    honorOutput: 0,
    price: 0,
    deficit: 0,
    priceImportExport: 0,
    sumResources: 0,
    traderId: null,
    resources: ["Metal", "Crystal", "Deuterium"],
    current: {
      planet: currentPlanetId,
      resource: "",
      sliderValue: "",
    },
    barXPos: -180,
    // start Position
    barYPos: 0,
    percentPaid: 0,
    switchingTrader: false,
    checkOverbidden: function () {
      if (playerBid == false || playerBid >= getValue($(".detail_value.currentSum").html())) {
        $(".overbid_text").hide();
      } else {
        $(".overbid_text").show();
      }
    },

    /*
     * This function updates only the current slider.
     * It gets called whenever a slider was changed so we will
     * calculate the total sum here:
     */
    refresh: function () {
      var traderId = traderObj.traderId; // Check that we're in the right context:

      if ("#" + $(this).closest(".div_trader").attr("id") !== traderId) return; // Get the css-class that identifies the current slider:

      var myClass = $(this).attr("class");
      var re = new RegExp(/\b(js_slider\w*)\b/);
      myClass = re.test(myClass) ? RegExp.$1 : false;
      if (!myClass) return; // Update current values:

      traderObj.current.sliderValue = $(this).slider("value");
      traderObj.current.resource = myClass.replace("js_slider", "").toLowerCase();

      if (traderObj.current.resource == "honor") {
        traderObj.honorOutput = traderObj.current.sliderValue;
      } else {
        traderObj.planets[traderObj.current.planet].output[traderObj.current.resource] = traderObj.current.sliderValue;
      }

      formatNumber($(traderId + " .js_amount.js_" + traderObj.current.resource), traderObj.current.sliderValue);

      if (traderId == "#div_traderAuctioneer") {
        traderObj.price = getValue($(traderId + " .js_price").html()); //traderObj.deficit = getValue($( traderId + ' .js_deficit' ).html());

        traderObj.sumAuctioneer();
        traderObj.checkOverbidden();
      } else if (traderId == "#div_traderImportExport") {
        traderObj.sumImportExport();
      }
    },
    // 	refresh: function() end

    /*
     * Whenever the view changes to another trader, all inputFields, Sliders, outputvalues
     * and selected planed have to be resetted:
     */
    resetValues: function (traderId, resetCurrentPlanet) {
      traderId = traderId || traderObj.traderId;
      resetCurrentPlanet = resetCurrentPlanet || false;
      /* for( planet in traderObj.planets ) funktioniert im IE7/8 nicht!! */
      // Reset output values:

      for (var planet in traderObj.planets) {
        for (var resource in traderObj.planets[planet].output) {
          traderObj.planets[planet].output[resource] = 0;
        }
      }

      traderObj.honorOutput = 0; // Reset input fields:

      $(".js_amount").val(0); // reset sums

      if (traderObj.traderId == "#div_traderAuctioneer") {
        traderObj.sumAuctioneer();
      } else if (traderObj.traderId == "#div_traderImportExport") {
        traderObj.sumImportExport();
      } // Reset display of selected planet to currentPlanetId

      if (resetCurrentPlanet) {
        traderId = traderId.replace(/#div_trader/, "");
        $("#js_togglePanel" + traderId)
          .find("li#" + currentPlanetId)
          .click();
      }

      toggleDisplay.close();
    },

    /*
     * Update the shown max-amount of planet resource
     */
    resetMaxAmount: function (planetResources, honor) {
      var traderId = traderObj.traderId;
      var resources = traderObj.resources;

      for (var planetId in traderObj.planets) {
        for (var i = 0; i < resources.length; i++) {
          var resToLower = resources[i].toLowerCase();
          traderObj.planets[planetId].input[resToLower] = planetResources[planetId][resToLower];
        }
      }

      for (var j = 0; j < resources.length; j++) {
        resToLower = resources[j].toLowerCase();
        var planetMax = traderObj.planets[traderObj.current.planet].input[resToLower];
        $(traderId + " .max_planet_" + resToLower).html(number_format(planetMax, 0));
      }

      honorScore = honor;
      $(traderId + " .max_planet_honor").html(number_format(Math.max(0, honor), 0));
      toggleDisplay.close();
    },

    /*
     * Update all fields with selected source:
     */
    selectSource: function (selectedSource) {
      // save selected source:
      traderObj.current.planet = $(selectedSource).attr("id"); //set first source to selected source

      toggleDisplay.close();
      toggleDisplay.setToggleLink($(selectedSource));
      var traderId = traderObj.traderId;
      var resources = traderObj.resources;
      $.ajax({
        url: urlRefreshPlanet,
        type: "POST",
        data: {
          planetId: traderObj.current.planet,
          _token: token,
          ajax: 1,
        },
        dataType: "json",
        success: function (data) {
          if (data.status === "success") {
            token = data.newAjaxToken; // update maximum and current values of all sliders and input fields:

            for (var i = 0; i < resources.length; i++) {
              var resToLower = resources[i].toLowerCase();
              var planetMax = data.refreshPlanet.input[resToLower];
              var currMax = planetMax;

              if (traderId == "#div_traderImportExport") {
                var outputMax =
                  traderObj.priceImportExport / multiplier[resToLower] -
                  traderObj.sumResources +
                  data.refreshPlanet.output[resToLower];
                var currMax = Math.min(planetMax, outputMax);
              }

              $(traderId + " .max_planet_" + resToLower).html(number_format(currMax, 0));
              $(traderId + " .js_amount.js_" + resToLower).val(number_format(data.refreshPlanet.output[resToLower], 0));
            }
          } else {
            for (var i = 0; i < resources.length; i++) {
              var resToLower = resources[i].toLowerCase();
              $(traderId + " .max_planet_" + resToLower).html(number_format(0, 0));
              $(traderId + " .js_amount.js_" + resToLower).val(number_format(0, 0));
            }

            fadeBox(loca["error"] + ": " + data.errors[0].message, true);
          }
        },
        error: function (data) {
          fadeBox(loca["error"] + ": " + loca["ajaxError"], true);
        },
      });
    },

    /*
     * Overview Image Animation
     */
    selectTrader: function (myTrader, timer, tab) {
      timer = timer || traderObj.timer;
      $.bbq.pushState({
        page: myTrader,
        animation: "false",
      });
      $(".planetlink, .moonlink").fragment({
        page: myTrader,
        animation: "false",
      });
      traderObj.traderId = "#div_" + myTrader;
      var traderId = traderObj.traderId,
        $backToOverview = $(".back_to_overview");

      var showTrader = function () {
        if (traderId == "#div_traderAuctioneer" || traderId == "#div_traderImportExport") {
          traderObj.resetValues(null, true);
        }

        var changeTraderFunction = function () {
          $("#traderOverview").find(".c-left, .c-right").addClass("c-small");
          $backToOverview.show();

          if (traderId == "#div_traderAuctioneer" || traderId == "#div_traderImportExport") {
            $backToOverview.addClass("left");
            $backToOverview.removeClass("right");
          } else if (traderId == "#div_traderResources" || traderId == "#div_traderScrap") {
            $backToOverview.addClass("right");
            $backToOverview.removeClass("left");
          }

          $("#planet #header_text h2").html(loca[myTrader]).parent().show();
        };

        if (animation && !disableAnimationOnce) {
          $("#traderOverview").find(".c-left, .c-right").hide();
          $("#planet").animate(
            {
              backgroundPosition: traderObj.traderBGPos[traderId],
              height: "250px",
            },
            timer,
            function () {
              $("#planet").addClass("detail");
              $("#traderOverview").find(".c-left, .c-right").show();
              changeTraderFunction();

              if (traderId == "#div_traderResources") {
                showTradeNowButton();
              }
            },
          );
        } else {
          disableAnimationOnce = false;
          $("#planet").css("background-position", traderObj.traderBGPos[traderId]).css("height", "250px");
          changeTraderFunction();

          if (traderId == "#div_traderResources") {
            showTradeNowButton();
          }
        }

        toggleDisplay.setPanel();
        $("#planet").addClass("detail");
        $(".js_trader").hide();
        $(traderId).show();

        if (traderId == "#div_traderResources" && typeof tab != "undefined") {
          $(traderId + " .ui-tabs").tabs("option", "active", tab);
        }

        traderObj.switchingTrader = false;
      };

      if ($(traderObj.traderId).length == 0) {
        var traderString = myTrader.toLowerCase().replace(/^trader/, "");
        let traderUrl = traderUrls[traderString];
        $.ajax({
          url: traderUrl,
          type: "POST",
          data: {
            show: traderString,
            ajax: 1,
          },
          beforeSend: function () {
            $("#loadingOverlay").addClass(traderString).show();
          },
          success: function (data) {
            $("#inhalt").append(data);
            $("#loadingOverlay").hide().removeClass(traderString);
            showTrader();
          },
          error: function () {
            fadeBox(loca["error"], true);
            $("#loadingOverlay").hide().removeClass(traderString);
          },
        });
      } else {
        showTrader();
      }
    },
    submitAuction: function () {
      var traderId = traderObj.traderId;
      var sum = getValue($(traderId + " .js_auctioneerSum").html());

      if (!$(traderId + " .right_box .pay").hasClass("disabled") && traderObj.price > 0 && traderObj.deficit <= 0) {
        $(traderId + " .right_box .pay").addClass("disabled");
        var bidArray = {
          planets: {},
          honor: traderObj.honorOutput,
        };

        for (var planetId in traderObj.planets) {
          bidArray.planets[planetId] = traderObj.planets[planetId].output;
        }

        $.ajax({
          url: auctionUrl,
          type: "POST",
          data: {
            bid: bidArray,
            _token: token,
            ajax: 1,
          },
          dataType: "json",
          success: function (data) {
            token = data.newAjaxToken;
            fadeBox(data.message, data.error);

            if (!data.error) {
              traderObj.resetValues(traderId, false);
              traderObj.resetMaxAmount(data.planetResources, data.honor);
              traderObj.reloadResources();
            }
          },
          error: function () {
            fadeBox(loca["error"], true);
          },
        });
      }

      return false;
    },
    submitImportExport: function () {
      if (!$(traderObj.traderId + " .right_box .pay").hasClass("disabled")) {
        $(traderObj.traderId + " .right_box .pay").addClass("disabled");
        var bidArray = {
          planets: {},
          honor: traderObj.honorOutput,
        };

        for (planetId in traderObj.planets) {
          bidArray.planets[planetId] = traderObj.planets[planetId].output;
        }

        $.ajax({
          url: importUrlTrade,
          type: "POST",
          data: {
            action: "trade",
            bid: bidArray,
            _token: token,
            ajax: 1,
          },
          dataType: "json",
          success: function (data) {
            token = data.newAjaxToken;
            fadeBox(data.message, data.error);

            if (!data.error) {
              for (planetId in traderObj.planets) {
                traderObj.planets[planetId].output = {
                  metal: 0,
                  crystal: 0,
                  deuterium: 0,
                };
              }

              $(traderObj.traderId + " .bargain_overlay").show();
              $(traderObj.traderId + " .payment").hide();
              $(traderObj.traderId + " .image_140px a").addClass("slideIn");
              traderObj.reloadResources();
              traderObj.updateImportItem(data.item);
              traderObj.refresh();
            }
          },
          error: function () {
            fadeBox(loca["error"], true);
          },
        });
      }

      return false;
    },
    reloadResources: function (callback) {
      getAjaxResourcebox(callback);
    },
    changeImportItem: function () {
      if ($(traderObj.traderId + " .import_bargain.change").hasClass("disabled")) {
        if (darkMatter < importChangeCost) {
          errorBoxDecision(
            LocalizationStrings.error,
            loca.errorNotEnoughDM,
            LocalizationStrings.yes,
            LocalizationStrings.no,
            redirectBuyPremium,
          );
        }
      } else {
        $(traderObj.traderId + " .import_bargain.change").addClass("disabled");
        $.ajax({
          url: importUrlBargain,
          type: "POST",
          data: {
            action: "bargain",
            _token: token,
            ajax: 1,
          },
          dataType: "json",
          success: function (data) {
            token = data.newAjaxToken;
            fadeBox(data.message, data.error);

            if (!data.error) {
              traderObj.updateImportItem(data.item);
              traderObj.reloadResources(function () {
                if (data.item.offersLeft > 0 && darkMatter >= importChangeCost) {
                  $(traderObj.traderId + " .import_bargain.change").removeClass("disabled");
                } else {
                  $(traderObj.traderId + " .import_bargain.change").addClass("disabled");
                }
              });
              traderObj.refresh();
            }
          },
          error: function () {
            fadeBox(loca["error"], true);
          },
        });
      }

      return false;
    },
    updateImportItem: function (itemData) {
      $(traderObj.traderId + " .got_item_text").html(itemData.itemText);
      $(traderObj.traderId + " .bargain_text").html(itemData.bargainText);
      $(traderObj.traderId + " .bargain_cost").html(itemData.bargainCostText);
      importChangeCost = itemData.bargainCost;
      $(traderObj.traderId + " .image_140px img").attr("src", "/cdn/img/item-images/" + itemData.image + "-140x.png");
      removeTooltip($(traderObj.traderId + " .image_140px a"));
      $(traderObj.traderId + " .image_140px a")
        .attr("ref", itemData.uuid)
        .removeClass("tooltip")
        .addClass("tooltipHTML")
        .attr("title", itemData.tooltip);
      initTooltips($(traderObj.traderId + " .image_140px a"));
      $(traderObj.traderId + " .detail_button .amount").text(itemData.amount);
    },
    takeImportItem: function () {
      if (!$(traderObj.traderId + " .import_bargain.take").hasClass("disabled")) {
        $(traderObj.traderId + " .import_bargain.change").addClass("disabled");
        $(traderObj.traderId + " .import_bargain.take").addClass("disabled");
        $(traderObj.traderId + " .import_bargain.change").addClass("hidden");
        $(traderObj.traderId + " .import_bargain.take").addClass("hidden");
        $(traderObj.traderId + " .bargain_cost").addClass("hidden");
        $.ajax({
          url: importUrlTakeItem,
          type: "POST",
          data: {
            action: "takeItem",
            _token: token,
            ajax: 1,
          },
          dataType: "json",
          success: function (data) {
            token = data.newAjaxToken;
            fadeBox(data.message, data.error);

            if (!data.error) {
              var uuid = data.item.ref;
              changeTooltip($(".detail_button[ref='" + uuid + "']"), data.item.title);
              $(
                ".detail_button[ref='" +
                  uuid +
                  "'] span.amount, " +
                  "#itemDetails[data-uuid='" +
                  uuid +
                  "'] span.amount",
              ).html(tsdpkt(data.item.amount));

              if (data.item.canBeActivated) {
                $('#itemDetails[data-uuid="' + uuid + '"] a.activateItem')
                  .removeClass("build-it_disabled")
                  .addClass("build-it");
              } else {
                $('#itemDetails[data-uuid="' + uuid + '"] a.activateItem')
                  .addClass("build-it_disabled")
                  .removeClass("build-it");
              }

              if (data.item.newOffer == false) {
                $(traderObj.traderId + " .bargain_text").html(data.item.noOfferMessage);
              } else {
                traderObj.resetImport(data.item.newOffer);
              }
            }
          },
          error: function () {
            fadeBox(loca["error"], true);
          },
        });
      }

      return false;
    },
    resetImport: function (importData) {
      importChangeCost = importData.bargainCost;

      if (darkMatter >= importChangeCost) {
        $(traderObj.traderId + " .import_bargain.change").removeClass("disabled");
      } else {
        $(traderObj.traderId + " .import_bargain.change").addClass("disabled");
      }

      $(traderObj.traderId + " .import_bargain.take").removeClass("disabled");
      $(traderObj.traderId + " .import_bargain.change").removeClass("hidden");
      $(traderObj.traderId + " .import_bargain.take").removeClass("hidden");
      $(traderObj.traderId + " .bargain_cost").removeClass("hidden");
      $(traderObj.traderId + " .bargain_overlay").hide();
      $(traderObj.traderId + " .payment").show();
      $(traderObj.traderId + " .image_140px img").attr(
        "src",
        "/cdn/img/trader/container_" + importData.rarity + ".jpg",
      );
      $(traderObj.traderId + " .image_140px a")
        .removeClass("slideIn")
        .attr("ref", "")
        .removeClass("tooltipHTML")
        .addClass("tooltip")
        .removeClass("r_common_140px")
        .removeClass("r_uncommon_140px")
        .removeClass("r_rare_140px")
        .removeClass("r_epic_140px")
        .removeClass("r_buddy_140px")
        .addClass("r_" + importData.rarity + "_140px");
      changeTooltip($(traderObj.traderId + " .image_140px a"), importData.tooltip);
      $(traderObj.traderId + " .js_import_price")
        .removeClass("green_text")
        .text(number_format(importData.price, 0));
      $(traderObj.traderId + " .image_140px .amount").text("?");
      traderObj.priceImportExport = getValue($(".js_import_price").html());
      traderObj.resetValues(null, true);
      traderObj.init();
    },

    /*
     * add all values from all sources:
     * sum has to be initialized with 0 every time, because we add the total amount of resources every time, not just the change
     */
    sumAuctioneer: function () {
      var traderId = traderObj.traderId;
      var price = traderObj.price;

      if (price == 0) {
        $("#div_traderAuctioneer .js_amount").attr("disabled", "disabled");
      } else {
        $("#div_traderAuctioneer .js_amount").removeAttr("disabled");
      }

      var sum = 0;

      for (var planetId in traderObj.planets) {
        var output = traderObj.planets[planetId].output;
        sum +=
          parseInt(output.metal) * multiplier.metal +
          parseInt(output.crystal) * multiplier.crystal +
          parseInt(output.deuterium) * multiplier.deuterium;
      }

      sum += parseInt(traderObj.honorOutput) * multiplier.honor;
      sum = Math.floor(sum);
      traderObj.deficit = Number(auctioneer.calculateDeficit()) - Number(sum);

      if (traderObj.deficit > 0) {
        $(" .js_deficit").html(number_format(traderObj.deficit, 0));
      } else {
        $(" .js_deficit").html(number_format(0, 0));
      }

      if (sum > 0) {
        $("#div_traderAuctioneer .js_auctioneerSum").html("+ " + number_format(sum, 0));
      } else {
        $("#div_traderAuctioneer .js_auctioneerSum").html("");
      }

      $("#div_traderAuctioneer .js_alreadyBidden").html(number_format(Math.floor(playerBid + sum), 0)); //bid ok?

      if (price > 0 && traderObj.deficit <= 0) {
        $("#div_traderAuctioneer .right_box .pay").removeClass("disabled");
      } else {
        $("#div_traderAuctioneer .right_box .pay").addClass("disabled");
      }
    },
    sumImportExport: function () {
      var traderId = traderObj.traderId;
      var sumMetal = 0;
      var sumCrystal = 0;
      var sumDeuterium = 0;
      traderObj.sumResources = 0;

      for (var planetId in traderObj.planets) {
        var output = traderObj.planets[planetId].output;
        sumMetal += parseInt(output.metal) * multiplier.metal;
        sumCrystal += parseInt(output.crystal) * multiplier.crystal;
        sumDeuterium += parseInt(output.deuterium) * multiplier.deuterium;
      }

      var sumHonor = traderObj.honorOutput * multiplier.honor;
      traderObj.sumResources += sumMetal + sumCrystal + sumDeuterium + sumHonor;

      if (traderObj.sumResources >= traderObj.priceImportExport) {
        traderObj.sumResources = traderObj.priceImportExport;
      }

      $(traderId + " .js_sum_price").html(number_format(Math.floor(traderObj.sumResources), 0)); //price ok?

      if (traderObj.sumResources >= traderObj.priceImportExport) {
        $(traderId + " .js_import_price").addClass("green_text");
        $(traderId + " .right_box .pay").removeClass("disabled");
      } else {
        $(traderId + " .js_import_price").removeClass("green_text");
        $(traderId + " .right_box .pay").addClass("disabled");
      }
    },

    /*
     * Update all values of current slider depending on which button was clicked:
     */
    updateValues: function ($elem) {
      var traderId = traderObj.traderId;
      if (traderId !== "#" + $elem.closest(".div_trader").attr("id")) return;
      var myClass = $elem.attr("class");
      var re = new RegExp(/\b(js_slider\w*)\b/);
      myClass = re.test(myClass) ? RegExp.$1 : false;
      if (!myClass) return;
      var currPlanet = traderObj.current.planet;
      var action, value, sliderId;

      if (myClass.indexOf("More") != -1) {
        sliderId = myClass.replace("More", "");
        action = "More";
      } else if (myClass.indexOf("Max") != -1) {
        sliderId = myClass.replace("Max", "");
        action = "Max";
      }

      traderObj.current.resource = sliderId.replace("js_slider", "").toLowerCase() || null;
      var currResource = traderObj.current.resource;
      var currInputValue = 0;

      if (currResource == "honor") {
        currInputValue = Math.max(0, honorScore);
      } else {
        currInputValue = traderObj.planets[currPlanet].input[currResource];
      }

      value = getValue($(traderId + " ." + sliderId + "Input").val());

      if (action == "More") {
        if (traderId == "#div_traderImportExport") {
          if (traderObj.sumResources <= traderObj.priceImportExport - 1000 * multiplier[currResource]) {
            value += 1000;
          } else if (traderObj.sumResources < traderObj.priceImportExport) {
            value += Math.ceil((traderObj.priceImportExport - traderObj.sumResources) / multiplier[currResource]);
          }
        } else if (traderId == "#div_traderAuctioneer" && traderObj.price > 0) {
          value += 1000;
        }

        if (value >= currInputValue) {
          value = Math.max(0, currInputValue);
        }
      } else if (action == "Max") {
        if (traderId == "#div_traderImportExport") {
          if (traderObj.sumResources == 0) {
            value = Math.min(currInputValue, Math.ceil(traderObj.priceImportExport / multiplier[currResource]));
          } else if (traderObj.sumResources.isBetween(0, traderObj.priceImportExport - 1)) {
            value = Math.min(
              currInputValue,
              value + Math.ceil((traderObj.priceImportExport - traderObj.sumResources) / multiplier[currResource]),
            );
            value = Math.max(0, value);
          }
        } else if (traderId == "#div_traderAuctioneer" && traderObj.price > 0) {
          value = Math.min(
            currInputValue,
            Math.ceil(getValue($(traderId + " .js_deficit").html()) / multiplier[currResource] + value),
          );
        }

        if (currResource == "honor" && value < 0) value = 0;
      } // update valueObject and slider with the new value:

      $(traderId + " .js_amount." + sliderId + "Input").val(number_format(value, 0));

      if (currResource == "honor") {
        traderObj.honorOutput = value;
      } else {
        traderObj.planets[currPlanet].output[currResource] = value;
      }

      if (traderId == "#div_traderImportExport") {
        traderObj.sumImportExport();
      } else if (traderId == "#div_traderAuctioneer" && traderObj.price > 0) {
        traderObj.sumAuctioneer();
        traderObj.checkOverbidden();
      }
    },
    updateValuesInputCanged: function ($elem) {
      var traderId = traderObj.traderId;
      if (traderId !== "#" + $elem.closest(".div_trader").attr("id")) return;
      var myClass = $elem.attr("class");
      var re = new RegExp(/\b(js_slider\w*)\b/);
      myClass = re.test(myClass) ? RegExp.$1 : false;
      if (!myClass) return;
      var sliderId = myClass.replace("Input", "");
      var currResource = sliderId.replace("js_slider", "").toLowerCase();
      var currPlanet = traderObj.current.planet;
      var currInputValue = 0;

      if (currResource == "honor") {
        currInputValue = Math.max(0, honorScore);
      } else {
        currInputValue = parseInt(traderObj.planets[currPlanet].input[currResource]);
      } //update traderObj
      // value may be max either max ress on planet or the price devided by multiplier

      var value = 0;

      if (traderId == "#div_traderImportExport") {
        var sum = 0;

        for (var planetId in traderObj.planets) {
          var output = traderObj.planets[planetId].output;

          if (currResource != "metal") {
            sum += Math.floor(parseInt(output.metal) * multiplier.metal);
          }

          if (currResource != "crystal") {
            sum += Math.floor(parseInt(output.crystal) * multiplier.crystal);
          }

          if (currResource != "deuterium") {
            sum += Math.floor(parseInt(output.deuterium) * multiplier.deuterium);
          } //if(currResource != 'honor') { sum += Math.floor(parseInt(output.honor) * multiplier.honor); }
        }

        value = Math.min(
          getValue($elem.val()),
          Math.ceil((traderObj.priceImportExport - sum) / multiplier[currResource]),
        );
      } else if (traderId == "#div_traderAuctioneer") {
        value = getValue($elem.val());
      }

      value = Math.min(value, currInputValue);
      traderObj.planets[currPlanet].output[currResource] = value;

      if (currResource == "honor") {
        traderObj.honorOutput = value;
      } else {
        traderObj.planets[currPlanet].output[currResource] = value;
      }

      if (traderId == "#div_traderImportExport") {
        traderObj.sumImportExport();
      } else if (traderId == "#div_traderAuctioneer") {
        traderObj.sumAuctioneer();
        traderObj.checkOverbidden();
      }

      formatNumber(traderId + " .js_amount." + sliderId + "Input", value);
    },
    init: function () {
      $(".honorResource").hide();
      $("#menuTable a.trader")
        .unbind("click.gotoTrader")
        .bind("click.gotoTrader", function (e) {
          e.preventDefault();
          traderObj.switchTrader("traderResources");
        });
      $(window)
        .unbind("hashchange.switchTrader")
        .bind("hashchange.switchTrader", function (e) {
          var url = $.deparam.fragment(e.fragment);

          if (typeof url["page"] == "undefined" || (url["page"] == "" && traderObj.traderId != null)) {
            traderObj.returnToOverview();
          } else {
            traderObj.switchTrader(url["page"]);
          }
        });
      $(".small_back_to_overview")
        .unbind("mouseenter")
        .unbind("mouseout")
        .bind("mouseenter", function () {
          $("#header_text").css("background-position", "0 -250px");
        })
        .bind("mouseout", function () {
          $("#header_text").css("background-position", "0 0");
        });
    },
    initSliderTrader: function (traderId) {
      // Unbind the events:
      $(traderId + " .js_valButton").unbind("click.valControl");
      $(traderId + " .js_amount").unbind("keyup.inputVal");
      toggleDisplay.init(traderId);
      $(traderId + " .js_valButton").bind("click.valControl", function (e) {
        traderObj.updateValues($(this));
        e.stopPropagation();
      });
      $(traderId + " .js_amount").bind("keyup.inputVal", function (e) {
        traderObj.updateValuesInputCanged($(this));
        e.stopPropagation();
      });
    },
    initImportExport: function () {
      traderObj.planets = planetResources;
      traderObj.priceImportExport = getValue($(".js_import_price").html());
      traderObj.initSliderTrader("#div_traderImportExport");
      $("#div_traderImportExport .right_box .pay").bind("click", function () {
        traderObj.submitImportExport();
      });
      $("#div_traderImportExport .import_bargain.change").bind("click", function () {
        traderObj.changeImportItem();
      });
      $("#div_traderImportExport .import_bargain.take").bind("click", function () {
        traderObj.takeImportItem();
      });
    },
    switchTrader: function (traderId) {
      if (traderObj.switchingTrader) {
        return;
      }

      traderObj.switchingTrader = true;
      Tipped.hideAll();
      $("#planet .close_details:visible").click();

      if ("#div_" + traderId == traderObj.traderId) {
        return;
      }

      if (traderObj.traderId != null || traderId == "" || traderId == null) {
        traderObj.returnToOverview();

        if (animation && !disableAnimationOnce) {
          setTimeout(function () {
            traderObj.selectTrader(traderId);
          }, 500);
        } else {
          traderObj.selectTrader(traderId);
        }
      } else {
        traderObj.selectTrader(traderId);
      }
    },
    returnToOverview: function () {
      // reset trader header
      $("#planet #header_text h2").empty().parent().hide();
      $("#traderOverview").find(".c-left, .c-right").hide();
      var traderId = traderObj.traderId;
      if (!traderId) return;
      $(traderId).hide();
      $("#callTrader").hide();

      if (animation && !disableAnimationOnce) {
        $("#planet h2").hide();
        $("#planet").animate(
          {
            backgroundPosition: "-273px 0px",
            height: "470px",
          },
          500,
          function () {
            $("#planet h2").show();
            $("#planet").removeClass("detail");
            $("#traderOverview").find(".c-left, .c-right").show();
            $(".js_trader").show();
          },
        );
      } else {
        $("#planet").removeClass("detail").css("background-position", "-273px 0px").css("height", "470px");
        $(".js_trader").show();
      }

      $("#planet a").show();
      $("#planet .back_to_overview").hide();
      removeTooltip($("#planet .back_to_overview"));
      $("#traderOverview").find(".c-left, .c-right").removeClass("c-small");
      traderObj.traderId = null;
      traderObj.switchingTrader = false;
    },
  };
  breakerObj = {
    costs: null,
    offer: null,
    ships: {},
    locked: false,
    lastTechId: null,
    initialize: function () {
      this.offer = parseInt($(".scrap_offer_amount").html());
      this.costs = breakerCosts;
      var thisObj = this;
      /* ****** Scrotthändler AnythingSlider ******* */

      $("#js_anythingSliderShips, #js_anythingSliderDefense").anythingSlider({
        startStopped: true,
        // If autoPlay is on, this can force it to start stopped
        buildStartStop: false,
        expand: true,
        resizeContents: false,
        theme: "default",
        infiniteSlides: false,
        autoPlay: false,
        easing: "swing",
        resizeContents: true,
        stopAtEnd: true,
        playRtl: isRTLEnabled,
        buildNavigation: false,
        // Callback when the plugin finished initializing (for IPad Swipe Event)
        onInitialized: function (e, slider) {
          if (isMobile) {
            var time = 1000,
              // allow movement if < 1000 ms (1 sec)
              range = 50,
              // swipe movement of 50 pixels triggers the slider
              x = 0,
              t = 0,
              touch = "ontouchend" in document,
              st = touch ? "touchstart" : "mousedown",
              mv = touch ? "touchmove" : "mousemove",
              en = touch ? "touchend" : "mouseup";
            slider.$window
              .bind(st, function (e) {
                // prevent image drag (Firefox)
                //e.preventDefault();
                t = new Date().getTime();
                x = e.originalEvent.touches ? e.originalEvent.touches[0].pageX : e.pageX;
              })
              .bind(en, function (e) {
                t = 0;
                x = 0;
              })
              .bind(mv, function (e) {
                e.preventDefault();
                var newx = e.originalEvent.touches ? e.originalEvent.touches[0].pageX : e.pageX,
                  r = x === 0 ? 0 : Math.abs(newx - x),
                  // allow if movement < 1 sec
                  ct = new Date().getTime();

                if (t !== 0 && ct - t < time && r > range) {
                  if (newx < x) {
                    slider.goForward();
                  }

                  if (newx > x) {
                    slider.goBack();
                  }

                  t = 0;
                  x = 0;
                }
              });
          }
        },
      }); // end slider 1 initialize

      $("#js_anythingSliderDefense").parent().parent().hide();
      $(".scrap_defense").bind("click.tabDefense", function () {
        $(".scrap_ships").removeClass("selected");
        $(this).addClass("selected");
        $("#js_anythingSliderShips").parent().parent().hide();
        $("#js_anythingSliderDefense").parent().parent().show();
      });
      $(".scrap_ships").bind("click.tabShips", function () {
        $(".scrap_defense").removeClass("selected");
        $(this).addClass("selected");
        $("#js_anythingSliderDefense").parent().parent().hide();
        $("#js_anythingSliderShips").parent().parent().show();
      });
      $(".image a").each(function () {
        var techId = $(this).attr("ref").substr(6, 3);
        var $level = $(this).find(".level");
        var $amount = $level.contents().filter(function () {
          return this.nodeType == 3;
        });
        thisObj.ships[techId] = $amount.text().replace(/^\s+|\s+$/g, "");
        $amount.remove();
        $level.append(tsdpkt(thisObj.ships[techId]));
      });
      $("#js_scrapBargain")
        .unbind("click")
        .bind("click", function () {
          if (!$(this).hasClass("disabled")) {
            thisObj.bargain(thisObj);
          } else {
            if (breakerMaximumPercent > thisObj.offer) {
              errorBoxDecision(
                LocalizationStrings.error,
                loca.errorNotEnoughDM,
                LocalizationStrings.yes,
                LocalizationStrings.no,
                redirectBuyPremium,
              );
            }
          }

          return false;
        });
      $("#js_scrapScrapIT")
        .unbind("click")
        .bind("click", function () {
          if (!$(this).hasClass("disabled")) {
            thisObj.trade(thisObj);
          }

          return false;
        });
      $("input.ship_amount")
        .unbind("focus")
        .bind("focus", function () {
          thisObj.lastTechId = $(this).attr("name").substr(2, 3);
          $(this).val("");
        });
      $("input.ship_amount")
        .unbind("keyup change")
        .bind("keyup change", function (event) {
          thisObj.lastTechId = $(this).attr("name").substr(2, 3);
          formatNumber(this, $(this).val());
          var $this = $(this);
          clearTimeout($this.data("timer"));
          $this.data(
            "timer",
            setTimeout(function () {
              $this.removeData("timer");
              thisObj.updateResources(thisObj);
            }, 300),
          );
        });
      $(".buildingimg a")
        .unbind("click")
        .bind("click", function () {
          return false;
        });
      $(".js_maxShips")
        .unbind("click")
        .bind("click", function () {
          if (!isMobile) {
            $($(this).attr("ref")).focus();
          }

          var shipAmount = thisObj.ships[$(this).attr("ref").substr(6, 3)];
          $($(this).attr("ref")).val(tsdpkt(shipAmount)).trigger("change");
          thisObj.updateResources(thisObj);
          return false;
        });
      $(".sendAll")
        .unbind("click")
        .bind("click", function () {
          $(".anythingSlider ul:visible input").each(function () {
            thisObj.lastTechId = $(this).attr("name").substr(2, 3);
            var shipAmount = thisObj.ships[thisObj.lastTechId];

            if (shipAmount > 0) {
              $(this).val(tsdpkt(shipAmount));
            }
          });
          thisObj.updateResources(thisObj, function (data) {
            if (data.error) {
              $(".anythingSlider ul:visible input").val("");
              $("#div_traderScrap .resource_amount").text(0);
              thisObj.checkShips(thisObj);
            }
          });
        });
      $(".sendNone")
        .unbind("click")
        .bind("click", function () {
          $(".anythingSlider ul:visible input").each(function () {
            thisObj.lastTechId = $(this).attr("name").substr(2, 3);
            $(this).val("");
          });
          thisObj.updateResources(thisObj);
        });
      $("#js_bargainCost").text(tsdpkt(this.costs));
      this.checkMoney(this);
      this.checkShips(this);
      this.updateBargain(this);
    },
    bargain: function (thisObj) {
      $("#js_scrapBargain").addClass("disabled");
      $.ajax({
        url: breakerLinkBargain,
        type: "POST",
        dataType: "json",
        data: {
          bargain: 1,
          _token: token,
        },
        beforeSend: function () {
          thisObj.lock(thisObj);
        },
        success: function (response) {
          let data = response.data;
          thisObj.unlock(thisObj);
          token = response.newAjaxToken;
          fadeBox(data.message, data.error);

          if (!data.error) {
            thisObj.costs = data.bargainPrice;
            thisObj.offer = data.percentage;
            darkMatter = data.resources.dm;
            thisObj.updateBargain(thisObj);
            thisObj.updateResources(thisObj);
            traderObj.reloadResources(function () {
              thisObj.checkMoney(thisObj);
              Tipped.show($("#js_scrapBargain")[0]);
            });
          }

          $(".scrap_trader_quote").text(data.quote);
        },
        error: function () {
          thisObj.unlock(thisObj);
        },
      });
    },
    trade: function (thisObj) {
      thisObj.lock(thisObj);
      var tradeArray = thisObj.getTradeArray();

      var getBreakerQuestion = function getBreakerQuestion() {
        var questionString = loca.breakerQuestion + '<br/><br/><div style="text-align: left; margin-left: 30px">';
        var counter = 0;
        $.each(tradeArray, function (techId) {
          questionString += this + "x " + breakerTechs[techId].name + ", ";
          counter++;

          if (counter % 2 == 0) {
            questionString += "<br/>";
          }
        });
        questionString = questionString.replace(/, (<br\/>)?$/, "");
        questionString += "</div>";
        return questionString;
      };

      errorBoxDecision(
        loca.breaker,
        getBreakerQuestion(),
        LocalizationStrings.yes,
        LocalizationStrings.no,
        function () {
          $.ajax({
            url: breakerLinkTrade,
            type: "POST",
            dataType: "json",
            data: {
              lastTechId: thisObj.lastTechId,
              finishTrade: 1,
              trade: tradeArray,
              _token: token,
            },
            success: function (response) {
              let data = response.data;
              thisObj.unlock(thisObj);
              token = response.newAjaxToken;

              if (data.error) {
                fadeBox(data.message, true);
              } else {
                fadeBox(data.message, false);
                thisObj.offer = data.percentage;
                thisObj.costs = data.bargainPrice;
                thisObj.resetForm();
                thisObj.updateBargain(thisObj);
                $("#js_scrapAmountMetal").html(0);
                $("#js_scrapAmountCrystal").html(0);
                $("#js_scrapAmountDeuterium").html(0);
                traderObj.reloadResources(function () {
                  thisObj.updateShips(thisObj);
                });
              }

              $(".scrap_trader_quote").text(data.quote);
            },
            error: function () {
              thisObj.unlock(thisObj);
              fadeBox(loca["error"], true);
            },
          });
        },
        function () {
          thisObj.unlock(thisObj);
        },
      );
    },
    updateResources: function (thisObj, callback) {
      if (thisObj.locked) {
        return;
      }

      $.ajax({
        url: breakerLinkTrade,
        type: "POST",
        dataType: "json",
        data: {
          lastTechId: thisObj.lastTechId,
          trade: thisObj.getTradeArray(),
          _token: token,
        },
        beforeSend: function () {
          thisObj.lock(thisObj);
        },
        success: function (response) {
          let data = response.data;
          token = response.newAjaxToken;

          if (data.error) {
            fadeBox(data.message, true);
          }

          thisObj.locked = false;
          var reloadShips = false;

          for (var techId in data.techAmount) {
            $("#ship_" + techId).val(tsdpkt(data.techAmount[techId]));

            if (!reloadShips && $("#ship_" + techId).val() != thisObj.ships[techId]) {
              reloadShips = true;
            }
          }

          $("#js_scrapAmountMetal").html(tsdpkt(round(data.resources.metal, 2)));
          $("#js_scrapAmountCrystal").html(tsdpkt(round(data.resources.crystal, 2)));
          $("#js_scrapAmountDeuterium").html(tsdpkt(round(data.resources.deuterium, 2)));

          if (!thisObj.notFirstOffer) {
            $(".scrap_trader_quote").text(loca.breakerFirstOffer);
            thisObj.notFirstOffer = true;
          }

          if (reloadShips) {
            thisObj.updateShips(thisObj);
          } else {
            thisObj.unlock(thisObj);
          }

          if (typeof callback == "function") {
            callback(data);
          }
        },
        error: function () {
          thisObj.unlock(thisObj);
        },
      });
    },
    updateShips: function (thisObj) {
      $.ajax({
        url: techUrl,
        type: "POST",
        dataType: "json",
        beforeSend: function () {
          thisObj.lock(thisObj);
        },
        success: function (data) {
          $("#div_traderScrap .item").each(function () {
            var techId = $(this).attr("id").substr(6, 3);

            if (typeof data[techId] != "undefined") {
              // Sometimes, somehow we get null in the arrays. Workaround: shipcount shall be 0
              var shipCount = 0;

              if (data[techId] != null) {
                shipCount = getValue(data[techId]);
              }

              thisObj.ships[techId] = shipCount;
              var $level = $(this).find(".level");
              $level
                .contents()
                .filter(function () {
                  return this.nodeType == 3;
                })
                .remove();
              $level.append(tsdpkt(shipCount)); // if we've got that magical null, we don't want to touch the button colors
              // because we don't want to grey out any buttons that possibly were colored before

              if (data[techId] != null) {
                var $button = $("#button" + techId);
                $button.removeClass("on").removeClass("off");

                if (shipCount > 0) {
                  $button.addClass("on");
                } else {
                  $button.addClass("off");
                }
              }
            }
          });
          thisObj.unlock(thisObj);
        },
        error: function () {
          thisObj.unlock(thisObj);
        },
      });
    },
    getTradeArray: function () {
      var tradeArray = {};
      $("input.ship_amount").each(function () {
        var techId = $(this).attr("name").substr(2, 3);

        if (getValue($(this).val()) != 0) {
          tradeArray[techId] = getValue($(this).val());
        }
      });
      return tradeArray;
    },
    resetForm: function () {
      $("input.ship_amount").each(function () {
        $(this).val("0");
      });
      removeTooltip($("#js_scrapBargain"));
      $("#js_scrapBargain").removeClass("tooltip").removeAttr("title");
    },
    checkMoney: function (thisObj) {
      if (darkMatter < thisObj.costs) {
        $("#js_scrapBargain").addClass("disabled");
      } else if (breakerMaximumPercent <= thisObj.offer) {
        $("#js_scrapBargain").addClass("disabled").addClass("tooltip").attr("title", loca.infoMaxBargain);
        initTooltips($("#js_scrapBargain"));
      } else {
        $("#js_scrapBargain").removeClass("disabled");
      }
    },
    checkShips: function (thisObj) {
      var hasValue = false;
      $("input.ship_amount").each(function () {
        if ($(this).val().length > 0 && getValue($(this).val()) > 0) {
          hasValue = true;
        }
      });

      if (!hasValue) {
        $("#js_scrapScrapIT").addClass("disabled");
      } else {
        $("#js_scrapScrapIT").removeClass("disabled");
      }
    },
    updateBargain: function (thisObj) {
      $(".scrap_offer_amount").css(
        "height",
        (thisObj.offer / 100) * $(".scrap_offer_amount").parent().css("height").replace("px", ""),
      );
      $(".scrap_offer_amount").html(thisObj.offer + "%");
      $(".js_bargainCost").text(tsdpkt(thisObj.costs));
    },
    lock: function (thisObj) {
      $("#js_scrapBargain").addClass("disabled");
      $("#js_scrapScrapIT").addClass("disabled");
      thisObj.locked = true;
    },
    unlock: function (thisObj) {
      thisObj.locked = false;
      thisObj.checkShips(thisObj);
      thisObj.checkMoney(thisObj);
    },
  };
  /*
   * Auctioneer socket functions
   */

  auctioneer = {
    socket: null,
    connected: false,
    timeout: null,
    retryInterval: 5000,
    historyShown: false,
    initConnection: function () {
      try {
        var thisObj = auctioneer;
        this.socket = new io.connect(":" + nodePort + "/auctioneer", nodeParams);
        this.socket.on("connect", function () {
          thisObj.connected = true;
          clearTimeout(this.timeout);
        });
        this.socket.on("disconnect", function () {
          thisObj.connected = false;
          thisObj.retryConnection();
        });
        this.socket.on("new auction", function (data) {
          auctionId = data.auctionId; // put last auction into history

          var playerName = $("#div_traderAuctioneer .detail_value.currentPlayer").html();

          if (data.oldAuction.player == null) {
            playerName = loca["auctionNotSold"];
          } else {
            playerName = '<a href="' + data.oldAuction.player.link + '">' + data.oldAuction.player.name + "</a>";
          }

          removeTooltip($("#div_traderAuctioneer .image_140px .detail_button"));
          var title = $("#div_traderAuctioneer .image_140px .detail_button").attr("title");
          var className = $(".auction_history li:first").hasClass("even") ? "odd" : "even";
          var newAuctionElement =
            '\
                        <li class="' +
            className +
            '" style="display: none">\
                            <a href="javascript:void(0);"\
                               class="slideIn"\
                               ref="' +
            data.oldAuction.item.uuid +
            '">\
                                <img height="30" width="30"\
                                     src="/cdn/img/item-images/' +
            data.oldAuction.item.imageSmall +
            '-small.png"\
                                     alt="" title="' +
            title +
            '"\
                                     class="item_img tooltipHTML tooltipLeft r_' +
            data.oldAuction.item.rarity +
            '"/>\
                            </a>\
                            <span class="detail sum">' +
            number_format(data.oldAuction.sum, 0) +
            '</span>\
                            <span class="detail player">' +
            playerName +
            '</span>\
                            <span class="detail date_time">' +
            data.oldAuction.time +
            "</span>\
                        </li>";
          $(".auction_history .history_content ul").prepend(newAuctionElement);
          $(".auction_history .history_content li:first").slideDown("slow"); // remove tha last history entry

          var historyLength = $("#div_traderAuctioneer .auction_history li").length;

          if (historyLength > 3) {
            $(".auction_history .history_content li:last").slideUp("slow", function () {
              $(".auction_history .history_content li:eq(21)").remove();
              var $thirdAuction = $(".auction_history .history_content li:eq(3)");
              $thirdAuction.addClass("more_auctions_li");

              if (auctioneer.historyShown) {
                $thirdAuction.show();
              }
            });
            $("#div_traderAuctioneer .auction_history .more").show();
          } // set new auction

          $("#div_traderAuctioneer .image_140px .detail_button")
            .attr("ref", data.item.uuid)
            .attr("title", "")
            .removeClass("r_common_140px")
            .removeClass("r_uncommon_140px")
            .removeClass("r_rare_140px")
            .removeClass("r_epic_140px")
            .addClass("r_" + data.item.rarity + "_140px");
          $("#div_traderAuctioneer .image_140px img").attr(
            "src",
            "/cdn/img/item-images/" + data.item.image + "-140x.png",
          );
          $("#div_traderAuctioneer .left_header h2").html(loca.auctionRunning);
          thisObj.setItemTooltip($("#div_traderAuctioneer .image_140px .detail_button"));
          thisObj.setData({
            price: 1000,
            sum: 0,
            player: null,
            bids: 0,
            info: data.info,
          });
          $("#div_traderAuctioneer .js_alreadyBidden").html(number_format(0, 0));
          $(".noAuctionOverlay").hide();
          traderObj.resetValues("#div_traderAuctioneer", false);
          traderObj.checkOverbidden();
        });
        this.socket.on("new bid", function (data) {
          if (data.player.id == playerId) {
            playerBid = data.sum; // set auctionid for function "calculateDeficit", otherwise the actual minimum bid is shown

            AuctionIdOflastPlayerBid = data.auctionId;
            $("#div_traderAuctioneer .js_alreadyBidden").html(number_format(Math.floor(playerBid), 0));
          }

          thisObj.setData({
            price: data.price,
            sum: data.sum,
            player: data.player,
            bids: data.bids,
          });
          traderObj.checkOverbidden();
        });
        this.socket.on("auction finished", function (data) {
          thisObj.setData({
            price: 0,
            player: data.player,
            bids: data.bids,
            info: data.info,
          });
          traderObj.resetValues("#div_traderAuctioneer", false);
          $("#div_traderAuctioneer .js_alreadyBidden").html(number_format(0, 0));
          $("#div_traderAuctioneer .js_auctioneerSum").html("");
          $("#div_traderAuctioneer .left_header h2").html(loca.auctionFinished);

          if (data.player != null) {
            if (data.player.id == playerId) {
              thisObj.setItemTooltip($("#div_traderAuctioneer .image_140px .detail_button"));
            }
          }

          $(".noAuctionOverlay").show();
          traderObj.checkOverbidden();
        });
        this.socket.on("timeLeft", function (data) {
          thisObj.setData({
            info: data,
          });
        });
      } catch (e) {}
    },
    setItemTooltip: function (object) {
      $.ajax({
        url: detailUrl,
        data: {
          getDetails: 1,
          type: $(object).attr("ref"),
          ajax: 1,
        },
        dataType: "json",
        success: function (data) {
          changeTooltip(object, data.title);
          $(
            "#itemDetails[data-uuid='" +
              $(object).attr("ref") +
              "'] .amount," +
              "a.detail_button[ref='" +
              $(object).attr("ref") +
              "'] .amount",
          ).html(tsdpkt(data.amount));
        },
        error: function () {
          fadeBox(loca["error"], true);
        },
      });
    },
    initialize: function () {
      if (typeof nodeUrl === "undefined") {
        return;
      }

      traderObj.initSliderTrader("#div_traderAuctioneer");
      traderObj.planets = planetResources;
      traderObj.price = getValue($(".js_price").html());
      $("#div_traderAuctioneer .right_box .pay").bind("click", function () {
        traderObj.submitAuction();
      });
      $("#div_traderAuctioneer .auction_history .more").bind("click", function () {
        if (auctioneer.historyShown) {
          $(this).text("[" + loca["auctionShowMore"] + "]");
        } else {
          $(this).text("[" + loca["auctionShowLess"] + "]");
        }

        auctioneer.historyShown = !auctioneer.historyShown;
        $("#div_traderAuctioneer .auction_history .more_auctions_li").slideToggle("slow");
      });
      traderObj.sumAuctioneer();
      traderObj.checkOverbidden();
      this.initCountdown();
      loadScript(nodeUrl, this.initConnection);
    },
    retryConnection: function () {
      var thisObj = this;
      setTimeout(function () {
        thisObj.initConnection();
      }, 5000);
    },
    setData: function (data) {
      var somethingChanged = false;

      if (typeof data.player != "undefined") {
        if (data.player == null) {
          $("#div_traderAuctioneer .detail_value.currentPlayer").text("");
          $("#div_traderAuctioneer .detail_value.currentPlayer").attr("href", "");
        } else {
          $("#div_traderAuctioneer .detail_value.currentPlayer").text(data.player.name);
          $("#div_traderAuctioneer .detail_value.currentPlayer").attr("href", data.player.link);
          $("#div_traderAuctioneer .detail_value.currentPlayer").attr("data-player-id", data.player.id);
          $("#div_traderAuctioneer .detail_value.currentPlayer").data("playerId", data.player.id);
        }

        somethingChanged = true;
      }

      if (typeof data.price !== "undefined") {
        traderObj.price = data.price;
        $("#div_traderAuctioneer .js_price").html(number_format(Math.floor(data.price), 0));
        somethingChanged = true;
      }

      if (typeof data.sum !== "undefined") {
        $("#div_traderAuctioneer .detail_value.currentSum").html(number_format(Math.floor(data.sum), 0));
        somethingChanged = true;
      }

      if (typeof data.bids !== "undefined") {
        $("#div_traderAuctioneer .detail_value.numberOfBids").html(number_format(data.bids, 0));
        somethingChanged = true;
      }

      if (typeof data.info !== "undefined" && $.trim($("#div_traderAuctioneer .auction_info").html()) != data.info) {
        $("#div_traderAuctioneer .auction_info").html(data.info);
        this.initCountdown();
        somethingChanged = true;
      }

      if (somethingChanged) {
        this.flash();
        traderObj.sumAuctioneer();
      }
    },
    initCountdown: function () {
      if (typeof this.nextAuctionTimer == "object") {
        timerHandler.removeCallback(this.nextAuctionTimer.timer);
      }

      if ($(".nextAuction").length > 0) {
        this.nextAuctionTimer = new simpleCountdown($(".nextAuction").get(0), $(".nextAuction").text());
      }
    },
    flash: function () {
      if (traderObj.traderId == "#div_traderAuctioneer") {
        $("#div_traderAuctioneer .overlay").fadeIn("normal", function () {
          $(this).fadeOut("normal");
        });
      }
    },
    calculateDeficit: function () {
      var deficit = 0;

      if (Math.floor(traderObj.price) == 0) {
        deficit = 0;
      } else if (auctionId != AuctionIdOflastPlayerBid) {
        deficit = Math.floor(traderObj.price);
      } else {
        deficit = Math.floor(traderObj.price) - Math.floor(playerBid);
      }

      return Math.floor(deficit);
    },
  };
  /* TraderOverview Image Hover Styles */

  $(".js_trader").hover(
    function () {
      var clickedTrader = $(this).attr("id").replace("js_trader", "").toLowerCase();
      $(this).addClass(clickedTrader + "_link_hover");
    },
    function () {
      var clickedTrader = $(this).attr("id").replace("js_trader", "").toLowerCase();
      $(".trader_link").each(function (index, element) {
        $(this).removeClass(clickedTrader + "_link_hover");
      });
    },
  );
  /* **** Hover Stile ******* */

  $(".right_box .pay, .value-control, .ui-slider-handle, .bargain, .scrap_it, .source").hover(
    function () {
      $(this).addClass("hover");
    },
    function () {
      $(this).removeClass("hover");
    },
  );
  traderObj.init();
  /*
   * Click event: back to trader overview:
   */

  $(document)
    .undelegate(".js_trader", "click")
    .delegate(".js_trader", "click", function () {
      var id = $(this).attr("id").replace("js_", "");
      traderObj.switchTrader(id);
    })
    .undelegate("#planet .js_backToOverview", "click")
    .delegate("#planet .js_backToOverview", "click", function () {
      $.bbq.pushState({
        page: "",
        animation: "",
      });
      $(".planetlink, .moonlink").fragment({
        page: "",
        animation: "",
      });
    });
  var url = $.deparam.fragment();

  if (typeof url["page"] != "undefined" && url["page"] != "") {
    traderObj.selectTrader(url["page"], undefined, url["tab"]);
  }
}
