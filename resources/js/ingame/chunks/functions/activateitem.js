function activateItem(uuid) {
  if (startedActivation) {
    return;
  }

  startedActivation = true;
  var dataObject = {
    _token: token,
    itemUuid: uuid,
    referrerPage: $.deparam.querystring().page,
  };
  var item = getItem(uuid);
  let action = "buyAndActivate";

  if (item.amount > 0) {
    action = "activate";
  }

  function updateItemAjax() {
    $.ajax({
      cache: false,
      url: inventoryObj.inventoryUrl + `&itemUuid=${uuid}&action=${action}`,
      data: dataObject,
      type: "GET",
      dataType: "json",
      success: function (data, textStatus, jqXHR) {
        token = data.newAjaxToken;

        if (data.status != "success") {
          fadeBox(data.message, true);
          return;
        }

        if (data.reload) {
          location.href = getRedirectLink();
          return;
        }

        fadeBox(data.message, false);

        if (data.buff !== false) {
          // animate buff activation
          var item = getItem(data.buff);
          var isNew = true;
          var $activeItems = $(".active_items");
          var activeItemsSlider = $activeItems.data("AnythingSlider");
          var $buffElement;
          $activeItems.find("li a").removeClass("active");
          $activeItems.find("li a.activate_item").addClass("active"); // Prolong existing buff

          $activeItems.find("div[data-id=" + data.buffId + "]").each(function () {
            $(this).hide().show("pulsate").find("a").addClass("active");
            isNew = false;
            $buffElement = $(this);
          }); // Add a new buff

          if (isNew == true) {
            var $newItem = $(
              '<div data-uuid="' +
                data.buff +
                '" data-id="' +
                data.buffId +
                '">' +
                '<div class="js_duration" style="display: none;"></div>' +
                '<a href="javascript:void(0);" ref="' +
                data.buff +
                '" class="detail_button slideIn active_item active r_' +
                data.item.rarity +
                " border3px tooltipHTML\" title='" +
                data.tooltip +
                "'>" +
                '<div class="pusher" style="height: 0%; "></div>' +
                '<img src="/cdn/img/item-images/' +
                data.item.image +
                '-small.png" alt=""/>' +
                "</a>" +
                "</div>",
            );
            var numberOfVisibleItemsInARow = 14; // gleicher Name in PHP
            // Store the number of the currently viewed page
            // In case the new item is appended to the page we already see, do not animate the slider
            // when switching to the new last page after repagination

            var currentSliderPage = activeItemsSlider.currentPage;
            var sliderAnimationTime = activeItemsSlider.options.animationTime; // If the new item leads to removal of existing buffs, handle that first

            if (data.upgraded) {
              $activeItems.find("div[data-id=" + data.upgraded + "]").remove();
            } // Add new item

            var numberOfBuffsInLastPage = $activeItems.children().last().children().length;

            if (numberOfBuffsInLastPage < numberOfVisibleItemsInARow) {
              // Item can be appended to existing last page
              $newItem.hide().appendTo($activeItems.children().last()).show("pulsate");

              if (currentSliderPage == activeItemsSlider.pages) {
                activeItemsSlider.options.animationTime = 0;
              }

              activeItemsSlider.gotoPage(activeItemsSlider.pages);

              if (currentSliderPage == activeItemsSlider.pages) {
                activeItemsSlider.options.animationTime = sliderAnimationTime;
              }
            } else {
              // Item does not fit into last page, so create new page
              $newItem = $newItem.hide().wrap("<li/>");
              $newItem.parent().appendTo($activeItems).children().last();
              activeItemsSlider.updateSlider();

              if (currentSliderPage == activeItemsSlider.pages) {
                activeItemsSlider.options.animationTime = 0;
              }

              activeItemsSlider.gotoPage(activeItemsSlider.pages);

              if (currentSliderPage == activeItemsSlider.pages) {
                activeItemsSlider.options.animationTime = sliderAnimationTime;
              }

              $newItem.show("pulsate");
            }

            $buffElement = $newItem;
          } else {
            var $oldItem = $(
              '<div data-uuid="' +
                item.ref +
                '" data-id="' +
                data.buffId +
                '">' +
                '<div class="js_duration" style="display: none;"></div>' +
                '<a href="javascript:void(0);" ref="' +
                item.ref +
                '" class="detail_button slideIn active_item active r_' +
                data.item.rarity +
                ' border3px tooltipHTML" title="' +
                data.item.toolTip +
                '">' +
                '<div class="pusher" style="height: 0%; "></div>' +
                '<img src="/cdn/img/item-images/' +
                item.image +
                '-small.png" alt=""/>' +
                "</a>" +
                "</div>",
            );
            var upgradedUuid = item.ref;
            $activeItems.find("div[data-uuid=" + upgradedUuid + "]").remove();
            $oldItem.hide().appendTo($activeItems.children().last()).show("pulsate");
            $buffElement = $oldItem;
          }

          var $pusherElement = $buffElement.find(".pusher");
          var $durationElement = $buffElement.find(".js_duration");
          $durationElement.attr("data-total-duration", data.duration).text(data.item.timeLeft);
          startCooldown($durationElement, $pusherElement, 32); // renew item buff box

          getAjaxResourcebox();
          $.ajax({
            type: "POST",
            url: detailUrl,
            data: {
              type: uuid,
            },
            beforeSend: function () {
              $("#detailWrapper .detail_screen").html('<div id="techDetailLoading"></div>');
            },
            success: function (data) {
              $("#detailWrapper .detail_screen").html(data);
            },
          });
        }

        startedActivation = false;
      },
      error: function (data) {
        fadeBox("Error!", true);
        startedActivation = false;
      },
    });
  }

  if (item.isAnUpgrade) {
    errorBoxDecision(
      LocalizationStrings.activateItem.upgradeItemQuestionHeader,
      LocalizationStrings.activateItem.upgradeItemQuestion,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      updateItemAjax,
      noHandlerItemActivation,
    );
  } // character class items need extra confirmation box
  else if (item.isCharacterClassItem) {
    if (item.amount > 0) {
      errorBoxDecision(
        LocalizationStrings.notice,
        LocalizationStrings.characterClassItem.activateItemQuestion.replace("#characterClassName#", item.name),
        LocalizationStrings.yes,
        LocalizationStrings.no,
        updateItemAjax,
        noHandlerItemActivation,
      );
    } else {
      errorBoxDecision(
        LocalizationStrings.notice,
        LocalizationStrings.characterClassItem.buyAndActivateItemQuestion
          .replace("#characterClassName#", item.name)
          .replace("#darkmatter#", tsdpkt(item.costs)),
        LocalizationStrings.yes,
        LocalizationStrings.no,
        updateItemAjax,
        noHandlerItemActivation,
      );
    }
  } // same as character class items
  else if (item.isAllianceClassItem) {
    if (item.amount > 0) {
      fetchDataAboutCurrentAllianceClass(item.name, updateItemAjax, "activateItemQuestion", null);
    } else {
      fetchDataAboutCurrentAllianceClass(item.name, updateItemAjax, "buyAndActivateItemQuestion", item.costs);
    }
  } else {
    updateItemAjax();
  }
}
