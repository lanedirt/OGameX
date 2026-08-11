// moved to global scope due to CDN issues
inventoryObj = {
  currentPage: null,
  currentItems: null,
  currentItem: null,
  currentCategory: null,
  activatingItem: false,
  initalizeSlider: function (items, slider, width, height, doSlideIn, force, titleClass, small, buildNavigation) {
    if ((inventoryObj.currentItems == items && typeof force == "undefined") || force == false) {
      return;
    }

    inventoryObj.currentItems = items; // don't use slideIn on Overview

    doSlideIn = doSlideIn || "slideIn";
    titleClass = titleClass || "tooltipHTML js_hideTipOnMobile";

    if (typeof small == "undefined") {
      small = true;
    }

    if (typeof buildNavigation == "undefined") {
      buildNavigation = true;
    }

    $("#" + slider + "Box").remove(".anythingSlider");
    var newItems = [];
    var counter = 0;

    for (var key in items) {
      var item = items[key];

      if (typeof item.hide != "undefined" && item.hide) {
        continue;
      }

      if (inventoryObj.currentPage == "shop" || inventoryObj.currentPage == "inventory") {
        // switch sorting horizontal <=> vertical
        var modulo = counter % inventoryObj.itemsPerSlide;
        newItems[counter + 2 * (modulo % 3) - 2 * Math.floor(modulo / 3)] = item;
      } else {
        // buff bar
        newItems[counter] = item;
      }

      counter++;
    }

    var slideCounter = 0,
      i = 0,
      $newSlider = $('<ul id="' + slider + '" />');

    for (var length = newItems.length; i < length; i++) {
      if (typeof newItems[i] == "undefined") {
        $lastSlide.append('<div class="item_img"><div class="empty border5px"></div></div>');
        continue;
      }

      var item = newItems[i];

      if (i % inventoryObj.itemsPerSlide == 0) {
        var $lastSlide = $('<li class="slide_' + slideCounter + '" />').appendTo($newSlider);
        slideCounter++;
      }

      var amount, amountClass, saleBadge;

      if (inventoryObj.currentPage == "shop") {
        amount = getNumberFormatShort(item.costs, null) + " " + loca.currency[item.currency];
        amountClass = "price";
      } else {
        amount = getNumberFormatShort(item.amount);
        amountClass = "amount";
      }

      var imageName;

      if (small) {
        imageName = item.imageLarge + "-75x.png";
      } else {
        imageName = item.imageLarge + "-100x.png";
      }

      var activationClass;

      if (item.canBeActivated || item.canBeBoughtAndActivated) {
        activationClass = "enabled";
      } else {
        activationClass = "disabled";
      }

      if (item.isReduced) {
        saleBadge = '<div class="sale_badge ' + activationClass + '"></div>';
      } else {
        saleBadge = "";
      }

      var isActiveClass = item.timeLeft != null ? " js_is_active " : "";
      var remainingTime = "";
      var itemTitle = item.title;

      if (slider.indexOf("js_activeItemSlider") != -1) {
        itemTitle = ""; // only show remaining time on active item slider AND if the item is active:

        remainingTime =
          item.timeLeft != null
            ? '<span class="js_duration undermark" data-total-duration="' +
              item.totalTime +
              '">' +
              item.timeLeft +
              "</span>"
            : "";
      }

      var pusher = ""; // @TODO: add the logic for this condition:
      // only show pusher if we're on overview AND item is active:

      if (item.timeLeft != null && slider.indexOf("js_activeItemSlider") != -1) {
        pusher = '<div class="pusher"></div>';
      }

      var birthdayDiv = "";

      if ($.inArray(birthdayCategory, item.category) != -1) {
        birthdayDiv = '<div class="event_active_hint"></div>';
      }

      $lastSlide.append(
        '<div class="item_img r_' +
          item.rarity +
          '" style="background-image: url(/cdn/img/item-images/' +
          imageName +
          ');">' +
          '<div class="item_img_box">' +
          birthdayDiv +
          '<div class="activation ' +
          activationClass +
          isActiveClass +
          '"></div>' +
          '<a href="javascript:void(0);" tabindex="1" title="' +
          itemTitle +
          '" class="detail_button ' +
          titleClass +
          " " +
          doSlideIn +
          '" ref="' +
          item.ref +
          '">' +
          saleBadge +
          '<span class="ecke"><span class="level ' +
          amountClass +
          '">' +
          amount +
          "</span></span></a></div>" +
          remainingTime +
          pusher +
          "</div>",
      );
    }

    $("#" + slider + "Box").prepend($newSlider); //Fill up empty Item Slots:

    if (i % inventoryObj.itemsPerSlide != 0) {
      for (var j = i % inventoryObj.itemsPerSlide; j < inventoryObj.itemsPerSlide; j++) {
        $("#" + slider + " li:last").append('<div class="item_img"><div class="empty border5px"></div></div>');
      }
    } //Fill empty Items End

    return (mySlider = $("#" + slider).anythingSlider({
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
      hashTags: true,
      buildNavigation: buildNavigation,
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
              //                            e.preventDefault();
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
    }));
  },
  // End initalizeSlider
  initShop: function () {
    var thisObj = this;
    $(window).unbind(".shop");
    $(document)
      .undelegate(".slideIn", "click.shop")
      .delegate(".slideIn", "click.shop", function () {
        if (thisObj.currentItem == $(this).attr("ref")) {
          thisObj.currentItem = null;
          $.bbq.pushState({
            item: "",
          });
        } else {
          thisObj.currentItem = $(this).attr("ref");
          $.bbq.pushState({
            item: $(this).attr("ref"),
          });
        }
      }); //Buttons Start:

    $("button.to_shop").bind("click.shop", function () {
      $.bbq.pushState({
        page: "shop",
      });
    });
    $("button.to_inventory").bind("click.shop", function () {
      $.bbq.pushState({
        page: "inventory",
      });
    });
    $("button.buyResourcesLink").bind("click", function () {
      reload_page($(this).data("link"));
    });
    $(".to_shop, .to_inventory").hover(
      function () {
        $(this).addClass("hover");
      },
      function () {
        $(this).removeClass("hover");
      },
    ); // End Buttons
    // Rebuild the Slider if another Category is selected:

    $(".categoryFilter li a").bind("click.shop", function () {
      $.bbq.pushState({
        category: $(this).attr("rel"),
      });
    });
    $(window)
      .unbind("hashchange.shop")
      .bind("hashchange.shop", function (e) {
        thisObj.onHashChange($.deparam.fragment(e.fragment));
      });
    thisObj.onHashChange($.deparam.fragment());
    inventoryObj.refreshResources();
  },
  onHashChange: function (url) {
    if (typeof url["page"] == "undefined") {
      var pushArray = {
        page: "shop",
        category: $(".categoryFilter a:first").attr("rel"),
      };

      if (typeof url["item"] != "undefined" && url["item"] != "") {
        var item = inventoryObj.items_shop[url["item"]];

        if (item.category.length > 0) {
          pushArray.category = item.category[item.category.length - 1];
        }
      }

      $.bbq.pushState(pushArray);
      return;
    }

    var changePage = this.currentPage != url["page"];

    if (changePage) {
      if (url["page"] == "inventory") {
        this.openInventory();
        $(".planetlink, .moonlink").fragment({
          page: url["inventory"],
        });
      } else {
        this.openShop();
        $(".planetlink, .moonlink").fragment({
          page: url["shop"],
        });
      }

      this.updateCategoryAmount();
    }

    if (typeof url["category"] == "undefined") {
      $.bbq.pushState({
        category: $(".categoryFilter a:first").attr("rel"),
      });
      return;
    } else {
      if (url["category"] != this.currentCategory || changePage) {
        this.changeCategory(url["category"]);
      }
    }

    if (typeof url["item"] == "undefined" || (url["item"] == "" && this.currentItem != null)) {
      $("#itemDetails a.close_details").click();
      $(".planetlink, .moonlink").fragment({
        item: "",
      });
    } else if (this.currentItem != url["item"]) {
      var $itemAnchor = $(".slideIn[ref='" + url["item"] + "']");

      if ($itemAnchor.length) {
        $itemAnchor.click();
      } else {
        // item could not be found in shop
        gfSlider.slideIn(getElementByIdWithCache("detail"), url["item"]);
      }

      $(".planetlink, .moonlink").fragment({
        item: url["item"],
      });
    }
  },
  initShopDetails: function () {
    var thisObj = this;
    var referrerPage = $.deparam.querystring().page;
    $(document)
      .undelegate("#itemDetails .close_details", "click")
      .delegate("#itemDetails .close_details", "click", function () {
        gfSlider.hide(getElementByIdWithCache("itemDetails"));
      })
      .undelegate("#itemDetails a.item.build-it", "click")
      .delegate("#itemDetails a.item.build-it", "click", function () {
        $.ajax({
          url: $(this).attr("rel"),
          data: {
            _token: token,
          },
          type: "POST",
          dataType: "json",
          error: function () {
            fadeBox(translation["buyError"], true);
          },
          success: function (data) {
            token = data.newAjaxToken;

            if (data.error || data.status === "failure") {
              if (data.message) {
                fadeBox(data.message, true);
              } else {
                fadeBox(data.errors[0].message, true);
              }

              $("#itemDetails a.item").removeClass("build-it").addClass("build-it_disabled");
            } else {
              fadeBox(data.message, false);
              inventoryObj.refreshResources();
              inventoryObj.refreshItemData(data.item);
            }
          },
        });
        return false;
      })
      .undelegate("#itemDetails a.item.build-it_disabled.dm", "click")
      .delegate("#itemDetails a.item.build-it_disabled.dm", "click", function () {
        errorBoxDecision(
          LocalizationStrings.error,
          loca.buyDMDecision,
          LocalizationStrings.yes,
          LocalizationStrings.no,
          function () {
            if ($("a.dm_button").length > 0) {
              $("a.dm_button").click();
            } else {
              window.location.href = $("#darkmatter_box a").attr("href");
            }
          },
        );
      })
      .undelegate("#itemDetails a.activateItem.build-it", "click")
      .delegate("#itemDetails a.activateItem.build-it", "click", function () {
        var $thisObj = $(this);

        function upgradeItemAjax() {
          $.ajax({
            url: $thisObj.attr("rel"),
            data: {
              _token: token,
              referrerPage: referrerPage,
            },
            type: "POST",
            dataType: "json",
            error: function () {
              fadeBox(translation["buyError"], true);
              $("#itemDetails a.activateItem").removeClass("build-it").addClass("build-it_disabled");
            },
            success: function (data) {
              token = data.newAjaxToken;

              if (data.error || data.status === "failure") {
                if (data.message) {
                  fadeBox(data.message, true);
                } else {
                  fadeBox(data.errors[0].message, true);
                }

                $("#itemDetails a.activateItem").removeClass("build-it").addClass("build-it_disabled");
              } else {
                fadeBox(data.message, false);

                if (data.reload) {
                  location.reload();
                  return;
                }

                inventoryObj.refreshResources();
                inventoryObj.refreshItemData(data.item);
              }
            },
          });
        }

        if ($thisObj.hasClass("isUpgrade")) {
          errorBoxDecision(
            LocalizationStrings.activateItem.upgradeItemQuestionHeader,
            LocalizationStrings.activateItem.upgradeItemQuestion,
            LocalizationStrings.yes,
            LocalizationStrings.no,
            upgradeItemAjax,
          );
        } else if ($thisObj.hasClass("isCharacterClassItem")) {
          var name = $thisObj.data("itemName");
          errorBoxDecision(
            LocalizationStrings.notice,
            LocalizationStrings.characterClassItem.activateItemQuestion.replace("#characterClassName#", name),
            LocalizationStrings.yes,
            LocalizationStrings.no,
            upgradeItemAjax,
          );
        } else if ($thisObj.hasClass("isAllianceClassItem")) {
          thisObj.fetchDataAboutCurrentAllianceClass(
            $thisObj.data("itemName"),
            upgradeItemAjax,
            "activateItemQuestion",
            null,
          );
        } else {
          upgradeItemAjax();
        }

        return false;
      })
      .undelegate("#itemDetails a.buyAndActivate.build-it", "click")
      .delegate("#itemDetails a.buyAndActivate.build-it", "click", function () {
        var $thisObj = $(this);

        function upgradeItemAjax() {
          $.ajax({
            url: $thisObj.attr("rel"),
            data: {
              _token: token,
              referrerPage: referrerPage,
            },
            type: "POST",
            dataType: "json",
            error: function () {
              fadeBox(translation["buyError"], true);
              $("#itemDetails a.activateItem").removeClass("build-it").addClass("build-it_disabled");
            },
            success: function (data) {
              token = data.newAjaxToken;

              if (data.error || data.status === "failure") {
                if (data.message) {
                  fadeBox(data.message, true);
                } else {
                  fadeBox(data.errors[0].message, true);
                }

                $("#itemDetails a.activateItem").removeClass("build-it").addClass("build-it_disabled");
              } else {
                if (data.reload) {
                  location.reload();
                  return;
                }

                fadeBox(data.message, false);
                inventoryObj.refreshResources();
                inventoryObj.refreshItemData(data.item);
              }
            },
          });
        }

        if ($thisObj.hasClass("isUpgrade")) {
          errorBoxDecision(
            LocalizationStrings.activateItem.upgradeItemQuestionHeader,
            LocalizationStrings.activateItem.upgradeItemQuestion,
            LocalizationStrings.yes,
            LocalizationStrings.no,
            upgradeItemAjax,
          );
        } else if ($thisObj.hasClass("isCharacterClassItem")) {
          var name = $thisObj.data("itemName");
          var price = $thisObj.data("itemPrice");
          errorBoxDecision(
            LocalizationStrings.notice,
            LocalizationStrings.characterClassItem.buyAndActivateItemQuestion
              .replace("#characterClassName#", name)
              .replace("#darkmatter#", tsdpkt(price)),
            LocalizationStrings.yes,
            LocalizationStrings.no,
            upgradeItemAjax,
          );
        } else if ($thisObj.hasClass("isAllianceClassItem")) {
          thisObj.fetchDataAboutCurrentAllianceClass(
            $thisObj.data("itemName"),
            upgradeItemAjax,
            "buyAndActivateItemQuestion",
            $thisObj.data("itemPrice"),
          );
        } else {
          upgradeItemAjax();
        }

        return false;
      })
      .undelegate("#itemDetails a.buyAndActivate.build-it_disabled.showGetMoreDmPopup", "click")
      .delegate("#itemDetails a.buyAndActivate.build-it_disabled.showGetMoreDmPopup", "click", function () {
        errorBoxDecision(
          LocalizationStrings.error,
          loca.buyDMDecision,
          LocalizationStrings.yes,
          LocalizationStrings.no,
          function () {
            window.location.href = $("#darkmatter_box a").attr("href");
          },
        );
      });
  },
  refreshResources: function () {
    getAjaxResourcebox(function (resources) {
      $(".to_dark_matter .level").text(gfNumberGetHumanReadable(resources.darkmatter.amount, isMobile));
    });
  },
  refreshItemData: function (itemData) {
    var uuid = itemData.ref;
    changeTooltip($(".detail_button[ref='" + uuid + "']"), itemData.title);
    $(".detail_button[ref='" + uuid + "'] span.amount, " + "#itemDetails[data-uuid='" + uuid + "'] span.amount").html(
      tsdpkt(itemData.amount),
    );

    if (typeof inventoryObj.items_inventory != "undefined") {
      if (inventoryObj.items_inventory.length == 0) {
        inventoryObj.items_inventory = {};
      } else if (itemData.amount <= 0) {
        delete inventoryObj.items_inventory[uuid];
      } else {
        inventoryObj.items_inventory[uuid] = itemData;
      }
    }

    if (typeof inventoryObj.items_shop != "undefined") {
      if (inventoryObj.items_shop.length == 0) {
        inventoryObj.items_shop = {};
      }

      inventoryObj.items_shop[uuid] = itemData;
    }

    changeTooltip(
      $(
        '#itemDetails[data-uuid="' +
          uuid +
          '"] a.activateItem, #itemDetails[data-uuid="' +
          uuid +
          '"] a.buyAndActivate',
      ),
      itemData.activationTitle,
    );

    if (itemData.hasEnoughCurrency) {
      $('#itemDetails[data-uuid="' + uuid + '"] a.item')
        .addClass("build-it")
        .removeClass("build-it_disabled");
    } else {
      $('#itemDetails[data-uuid="' + uuid + '"] a.item')
        .removeClass("build-it")
        .addClass("build-it_disabled");
    }

    if (itemData.amount > 0) {
      $('#itemDetails[data-uuid="' + uuid + '"] a.activateItem').show();
      $('#itemDetails[data-uuid="' + uuid + '"] a.buyAndActivate').hide();

      if (itemData.canBeActivated) {
        $('#itemDetails[data-uuid="' + uuid + '"] a.activateItem')
          .removeClass("build-it_disabled")
          .addClass("build-it");
      } else {
        $('#itemDetails[data-uuid="' + uuid + '"] a.activateItem')
          .addClass("build-it_disabled")
          .removeClass("build-it");
      }
    } else {
      $('#itemDetails[data-uuid="' + uuid + '"] a.activateItem').hide();
      $('#itemDetails[data-uuid="' + uuid + '"] a.buyAndActivate').show();

      if (itemData.canBeBoughtAndActivated && itemData.hasEnoughCurrency) {
        $('#itemDetails[data-uuid="' + uuid + '"] a.buyAndActivate')
          .removeClass("build-it_disabled")
          .addClass("build-it");
      } else {
        $('#itemDetails[data-uuid="' + uuid + '"] a.buyAndActivate')
          .addClass("build-it_disabled")
          .removeClass("build-it");
      }
    }

    if (isMobile) {
      var infoText = "";

      if (
        $(
          '#itemDetails[data-uuid="' +
            uuid +
            '"] a.activateItem:visible,' +
            '#itemDetails[data-uuid="' +
            uuid +
            '"] a.buyAndActivate:visible',
        ).hasClass("build-it_disabled")
      ) {
        infoText += itemData.activationTitle;
      }

      if (itemData.buyTitle.length && itemData.buyTitle != itemData.activationTitle) {
        infoText += itemData.buyTitle;
      }

      $('#itemDetails[data-uuid="' + uuid + '"] .info_txt').text(infoText);
    }

    if (itemData.timeLeft > 0 && itemData.extendable) {
      $('#itemDetails[data-uuid="' + uuid + '"] a.activateItem span').html(loca.extend);
      $('#itemDetails[data-uuid="' + uuid + '"] a.buyAndActivate span').html(loca.buyAndExtend);
    } else {
      $('#itemDetails[data-uuid="' + uuid + '"] a.activateItem span').html(loca.activate);
      $('#itemDetails[data-uuid="' + uuid + '"] a.buyAndActivate span').html(loca.buyAndActivate);
    }

    if (itemData.isAnUpgrade) {
      $(
        '#itemDetails[data-uuid="' +
          uuid +
          '"] a.activateItem, #itemDetails[data-uuid="' +
          uuid +
          '"] a.buyAndActivate',
      ).addClass("isUpgrade");
    } else {
      $(
        '#itemDetails[data-uuid="' +
          uuid +
          '"] a.activateItem, #itemDetails[data-uuid="' +
          uuid +
          '"] a.buyAndActivate',
      ).removeClass("isUpgrade");
    }

    if (this.inShop === true) {
      this.changeCategory($(".categoryFilter a.active").attr("rel"));
    }

    this.updateCategoryAmount();
  },
  boughtItemHint: function () {
    $(".to_inventory .bought_item_notice").show().fadeOut(1000);
  },
  openShop: function () {
    this.currentPage = "shop";
    $("#js_inventorySliderBox").hide();
    $("#js_shopSliderBox").show();
    $(".to_inventory").removeClass("active");
    $(".to_shop").addClass("active");
    $("#buttonz h2").text(loca.LOCA_PREMIUM_SHOP);

    if (isMobile) {
      $(".js_shopCurrentPage").html(loca.shopText);
    }
  },
  openInventory: function () {
    this.currentPage = "inventory";
    $("#js_shopSliderBox").hide();
    $("#js_inventorySliderBox").show();
    $(".to_shop").removeClass("active");
    $(".to_inventory").addClass("active");
    $("#buttonz h2").text(loca.LOCA_PREMIUM_INVENTORY);

    if (isMobile) {
      $(".js_shopCurrentPage").html(loca.inventoryText);
    }
  },
  changeCategory: function (category) {
    inventoryObj.currentCategory = category;
    $(".planetlink, .moonlink").fragment({
      category: category,
    });
    $(".categoryFilter li, .categoryFilter li a").removeClass("active");
    $('.categoryFilter li a[rel="' + category + '"]')
      .addClass("active")
      .parent()
      .addClass("active"); // remove all items from active slider:

    $(".anythingSlider").remove();

    var changeItems = function (items, slider) {
      // select the items to rebuild the slider with:
      var newItems2 = [];
      var newItems = [];
      var highestIndex = 0;
      $.each(items, function (index) {
        if (this.category != null) {
          var joinedArray = "$" + this.category.join("$") + "$";

          if (joinedArray.toLowerCase().indexOf("$" + category + "$") != -1) {
            newItems2[inventoryObj.item_orders[category][this.ref]] = this;

            if (inventoryObj.item_orders[category][this.ref] > highestIndex) {
              highestIndex = inventoryObj.item_orders[category][this.ref];
            }
          }
        }
      });

      for (var i = 0; i <= highestIndex; ++i) {
        // in anderes Array umschaufeln fuer IE8 noetig.
        // dieser merkt sich zwar die Indizes, mit denen die Items eingefuegt wurden
        // wird aber mit for(x in y) darueber iteriert, sind diese in der Einfuege-Reihenfolge.
        if (newItems2[i]) {
          newItems[i] = newItems2[i];
        }
      }

      inventoryObj.initalizeSlider(newItems, slider, 340, 340, null, null, null, false);
    };

    if (inventoryObj.currentPage == "shop") {
      changeItems(inventoryObj.items_shop, "js_shopSlider");
    } else if (inventoryObj.currentPage == "inventory") {
      changeItems(inventoryObj.items_inventory, "js_inventorySlider");
    }
  },
  updateCategoryAmount: function () {
    var items;

    if (inventoryObj.currentPage == "shop") {
      items = inventoryObj.items_shop;
    } else if (inventoryObj.currentPage == "inventory") {
      items = inventoryObj.items_inventory;
    } else {
      return;
    }

    var filter = $(".categoryFilter");
    filter.find(".amount").text(0);
    $.each(items, function (index) {
      if (this.category != null) {
        for (var categoryIndex in this.category) {
          var uuid = this.category[categoryIndex];
          var amountSpan = filter.find('a[rel="' + uuid + '"] .amount');
          var amount;

          if (inventoryObj.currentPage == "shop") {
            amount = 1;
          } else if (inventoryObj.currentPage == "inventory") {
            amount = this.amount;
          }

          amountSpan.text(tsdpkt(getValue(amountSpan.text()) + amount));
        }
      }
    });
    $.each(filter.find("li"), function (index) {
      var pageFirstUpper = inventoryObj.currentPage.slice(0, 1).toUpperCase() + inventoryObj.currentPage.slice(1);

      if ($(this).hasClass("in" + pageFirstUpper)) {
        $(this).show();
      } else {
        $(this).hide();

        if (!filter.find("li:visible .active").length) {
          filter.find("li:visible:first a").click();
        }
      }
    });
  },
  fetchDataAboutCurrentAllianceClass: function (newClassName, upgradeItemAjax, questionType, price) {
    if (!this.activatingItem) {
      this.activatingItem = true;
      let that = this;
      $.ajax({
        url: inventoryObj.ingameUrl,
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
  },
  promptUserForAllianceClassChange: function (newClassName, upgradeItemAjax, questionType, price, response) {
    this.activatingItem = false;

    if (response.userDoesNotHaveAlliance) {
      return 0;
    }

    let localizationString = LocalizationStrings.allianceClassItem[questionType];
    localizationString = localizationString.replace("#allianceClassName#", newClassName);

    if (questionType === "buyAndActivateItemQuestion") {
      localizationString = localizationString.replace("#darkmatter#", tsdpkt(price));
    }

    if (response && response.currentAllianceClass && response.dateOfLastAllianceClassChange) {
      localizationString += LocalizationStrings.allianceClassItem.appendCurrentClassQuestion;
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
  },
}; // var shopObj = {} end
