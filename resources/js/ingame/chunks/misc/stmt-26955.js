ogame.messages = {
  data: {
    initActions: {
      "tabs-nfFleets": "initTabFleets",
      "tabs-nfCommunication": "initTabCommunication",
      "tabs-nfMarket": "initTabMarket",
      "subtabs-nfCommunicationMessages": "initSubTabMessages",
    },
  },

  /**
   * Adds a message to the given tab or subtab (whithout check of timestamp,
   * because this will in almost all cases be the newest message)
   *
   **/
  addMessage: function ($tabOrSubtab, msgData, insertAtBeginning) {
    if (insertAtBeginning !== false) {
      insertAtBeginning = true;
    }

    if ($tabOrSubtab.attr("aria-selected") !== "true") {
      console.warn("addMessage: not correct Tab, aria-selected = ", $tabOrSubtab.attr("aria-selected"), $tabOrSubtab);
      return;
    }

    if (!msgData) {
      console.warn("addMessage: msgData is ", msgData);
      return;
    }

    var tabContentObject = $("#" + $tabOrSubtab.attr("aria-controls")).find(".tab_inner");
    var addBefore = false; //get id of active Tab, create the message and add it

    if (insertAtBeginning) {
      addBefore = true;
    }

    ogame.messages.createMessageItem(msgData, tabContentObject, addBefore);
  },

  /**
   *  Creates a message for tab messages
   *  That can then be inserted upon sending the message
   * @param before
   * @param messageData
   * @param tabObject
   */
  createMessageItem: function (messageData, tabObject, before) {
    var idArray = {};

    for (var msgData in messageData) {
      var data = messageData[msgData];
      idArray[msgData] = data.msgID;
    }

    var ids = JSON.stringify(idArray); // fetch message.tpl.php

    $.ajax({
      url: "?page=messages",
      type: "POST",
      dataType: "html",
      data: {
        messageId: ids,
        //details: messageData.detailURL, WTF?
        tabid: this.getCurrentMessageTab(),
        _token: token,
        action: 121,
        // \OGame\Messages\Message::ACTION_ADD
        ajax: 1,
      },
      success: function (data) {
        token = data.newAjaxToken;

        if (before) {
          tabObject.prepend(data);
        } else {
          var favCount = tabObject.find(".favoriteCount");

          if (favCount.length > 0) {
            $(data).insertBefore(favCount);
          } else {
            tabObject.append(data);
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {},
    });
  },

  /**
   *  Creates a message (=Rundmail) for the subtab messages
   *  That can then be inserted upon sending the message
   *  @param {String} msgData - Data to fill the message with
   */
  createBroadcastMsgItem: function (msgData) {
    if (!msgData) {
      console.warn("createMessageItem: msgData is missing!");
      return undefined;
    }

    var $msgItemHeader = $('<div class="msg_head"></div>');
    $msgItemHeader.append('<span class="msg_title blue_txt">' + msgData.title + "</span>");
    $msgItemHeader.append('<span class="msg_date fright">' + msgData.date + "</span><br>");
    $msgItemHeader.append('<span class="msg_sender_label">' + loca.LOCA_WRITE_MSG_FROM + ": </span>");
    $msgItemHeader.append('<span class="msg_sender">' + msgData.senderName + "</span>");
    var $msgItemFooter = $('<div class="msg_actions clearfix"></div>');
    $msgItemFooter.append(
      '<a class="fright txt_link overlay" href="' +
        msgData.detailURL +
        '" ' +
        'data-overlay-title="' +
        loca.broadcasts +
        '">' +
        loca.details +
        "</a>",
    );
    $msgItemFooter.append(
      '<a class="fright txt_link comments_link overlay" href="' +
        msgData.commentsURL +
        '" ' +
        'data-overlay-title="' +
        loca.broadcasts +
        '">' +
        msgData.commentsCount +
        ' <span class="comments"></span></a></a>',
    );
    var $msgItem = $('<li class="msg ' + msgData.newClass + '" data-msg-id="' + msgData.msgID + '"></li>');
    $msgItem.append('<div class="msg_status"></div>');
    $msgItem.append($msgItemHeader);
    $msgItem.append('<span class="msg_content">' + msgData.msgContent + "</span>");
    $msgItem.append($msgItemFooter);
    return $msgItem;
  },

  /**
   * Creates the html elements for the recipients
   * @param {String} recipientId
   * @param {String} recipientCat
   * @param {String} name
   */
  createRecipient: function (recipientId, recipientCat, name) {
    var $inputReplacement, pattern;
    $(".input_replacement").each(function () {
      pattern = new RegExp($(this).data("recipient-cat"));

      if (pattern.test(recipientCat)) {
        $inputReplacement = $(this);
      }
    }); // stop if the corresponding inputbox cannot be found

    if ($inputReplacement === undefined) return;
    var found = $inputReplacement.children(".recipient_txt").filter(function () {
      return $(this).data("recipient-id") === recipientId;
    }); // the element only gets created if it was not there before

    if (found.length === 0) {
      if (!$inputReplacement.hasClass("focus")) {
        $inputReplacement.addClass("focus");
      }

      $inputReplacement.append(
        '<div class="recipient_txt" data-recipient-id="' +
          recipientId +
          '" data-recipient-cat="' +
          recipientCat +
          '">' +
          name +
          '<a role="button" class="remove_recipient"></a></div>',
      );
    }
  },

  /**
   * checks if the given action is valid and calls it
   * @param {String} action - determines which action shall be performed
   * @returns {unresolved}
   */
  doInitAction: function (action) {
    if (typeof ogame.messages[ogame.messages.data.initActions[action]] === "function") {
      return ogame.messages[ogame.messages.data.initActions[action]]();
    }
  },
  initCombatReportDetails: function () {
    if ($("select").length > 0) {
      $("select").ogameDropDown();
    }
  },

  /**
   * Initializes detail messages
   * @param {Boolean} commentsAllowed - if true, initialize comments as well
   * @returns {undefined}
   */
  initDetailMessages: function (commentsAllowed) {
    $(".detail_list_el:nth-of-type(4n + 3), .detail_list_el:nth-of-type(4n + 4)").addClass("odd"); //max Height of the overlay is browsersize - (header of overlay + a little puffer)

    var newHeight = $(window).height() - 200;
    $(".detail_msg_ctn").css("height", newHeight);
    $(".detail_msg_ctn").mCustomScrollbar({
      theme: "ogame",
    });

    if (commentsAllowed) {
      // we need a a scroll to event, because anker won't work with customScrollbar
      $("#scrollToComments").on("click", function () {
        $(".detail_msg_ctn").mCustomScrollbar("scrollTo", "bottom");
      });
      initBBCodeEditor(locaKeys, itemNames, false, ".comment_textarea", 2000);
    }

    $("#messages ul.pagination").on("click", "li.p_li a", function () {
      var currentTab = $(this).data("tabid");
      var messageId = $(this).data("messageid");
      $.post(
        "?page=messages",
        {
          tabid: currentTab,
          messageId: messageId,
          _token: token,
          ajax: 1,
        },
        function (data) {
          var ajaxTableContent = $(data).find("#messages .ui-dialog");
          $(".overlayDiv").html(data);
          token = data.newAjaxToken;
          $("[name='_token']").val(data.newAjaxToken);
        },
      );
    });
  },
  initMessages: function (messageToken = null) {
    if (messageToken !== null) {
      ogame.messages.token = messageToken;
    }

    $(".js_tabs .tabs_btn_img").each(function () {
      if ($(this).attr("rel")) {
        $(this).attr("href", $(this).attr("rel"));
      }
    });
    ogame.messages.initTabs($(".js_tabs"));
    let currentTab = ogame.messages.getCurrentMessageTab();
    $("#contentWrapper #buttonz div.js_tabs.tabs_wrap.ui-tabs").on("click", "ul li.list_item", function () {
      currentTab = ogame.messages.getCurrentMessageTab();
    });

    const messageActionAjax = (data, success = null, error = null) => {
      if (!data["_token"]) data["_token"] = $("[name='_token']").val();
      if (!data["ajax"]) data["ajax"] = 1;
      if (window.location.href.indexOf("page=standalone") > -1) data["standalonePage"] = 1;
      $.ajax({
        type: "POST",
        url: "?page=messages",
        dataType: "json",
        data: data,
        success: function (returnData) {
          if (returnData["newAjaxToken"]) {
            token = returnData.newAjaxToken;
            $("[name='_token']").val(returnData.newAjaxToken);
          }

          if (success) {
            success(returnData);
          }
        },
        error: function () {
          if (error !== null) {
            error();
          }
        },
      });
    };

    $("body")
      .on("click", ".msg_actions .icon_not_favorited", function (event) {
        var messageId = $(this).parents("li.msg").data("msg-id") || $(this).parents("div.detail_msg").data("msg-id"); // \OGame\Messages\Message::ACTION_ARCHIVE == 101

        messageActionAjax(
          {
            tabid: currentTab,
            messageId: messageId,
            _token: token,
            action: 101,
          },
          (data) => {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);

            if (data[messageId]["result"] == true) {
              let realTarget;

              if ($(event.target).is("img")) {
                $(event.target).attr("src", $(event.target).attr("src").replace("not_favorited", "favorited"));
                $(event.target).parent().removeClass("icon_not_favorited").addClass("icon_favorited");
                realTarget = $(event.target).parent();
              } else {
                $(event.target).removeClass("icon_not_favorited").addClass("icon_favorited");
                $(event.target)
                  .find("img")
                  .attr("src", $(event.target).find("img").attr("src").replace("not_favorited", "favorited"));
                realTarget = $(event.target);
              }

              changeTooltip($(realTarget), loca.LOCA_MSG_DELETE_FAV);
              var $counter = $(".favoriteTabFreeSlotCount");
              $counter.html(parseInt($counter.html()) - 1);
            } else if (data[messageId]["reason"] !== "undefined") {
              fadeBox(data[messageId]["reason"], 1);
            } else {
              fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
            }
          },
        );
      })
      .on("click", ".msg_actions .icon_favorited", function (event) {
        var messageId = $(this).parents("li.msg").data("msg-id") || $(this).parents("div.detail_msg").data("msg-id"); // \OGame\Messages\Message::ACTION_ARCHIVE_REMOVE == 102
        messageActionAjax(
          {
            tabid: currentTab,
            messageId: messageId,
            _token: token,
            action: 102,
          },
          (data) => {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);

            if (data[messageId]["result"] == true) {
              let realTarget;

              if ($(event.target).is("img")) {
                $(event.target).attr("src", $(event.target).attr("src").replace("favorited", "not_favorited"));
                $(event.target).parent().removeClass("icon_favorited").addClass("icon_not_favorited");
                realTarget = $(event.target).parent();
              } else {
                $(event.target).removeClass("icon_favorited").addClass("icon_not_favorited");
                $(event.target)
                  .find("img")
                  .attr("src", $(event.target).find("img").attr("src").replace("favorited", "not_favorited"));
                realTarget = $(event.target);
              }

              changeTooltip($(realTarget), loca.LOCA_MSG_ADD_FAV);
              var $counter = $(".favoriteTabFreeSlotCount");
              $counter.html(parseInt($counter.html()) + 1);
            } else if (data[messageId]["reason"] !== "undefined") {
              fadeBox(data[messageId]["reason"], 1);
            } else {
              fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
            }
          },
        );
      })
      .on("click", ".js_actionKill", function (event) {
        var messageId = $(this).parents("li.msg").data("msg-id"); // \OGame\Messages\Message::ACTION_KILL == 103

        messageActionAjax(
          {
            messageId: messageId,
            _token: token,
            action: 103,
          },
          (data) => {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);

            if (data[messageId] == true) {
              $(event.target).parents("li.msg").remove();
            } else {
              fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
            }
          },
        );
      })
      .on("click", ".js_actionKillAll", function (event) {
        // \OGame\Messages\Message::ACTION_KILL == 103
        messageActionAjax(
          {
            tabid: ogame.messages.getCurrentMessageTab(),
            messageId: -1,
            _token: token,
            action: 103,
          },
          (data) => {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);

            if (data["result"] == true) {
              location.reload();
            } else {
              fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
            }
          },
        );
      })
      .on("click", ".js_actionKillDetail", function (event) {
        var messageId = $(".detail_msg").data("msg-id"); // \OGame\Messages\Message::ACTION_KILL == 103

        messageActionAjax(
          {
            messageId: messageId,
            _token: token,
            action: 103,
          },
          (data) => {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);

            if (data[messageId] == true) {
              location.reload();
            } else {
              fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
            }
          },
        );
      })
      .on("click", ".js_actionRevive", function (event) {
        var messageId = $(this).parents("li.msg").data("msg-id");

        if (messageId === undefined) {
          messageId = $(this).parents("div.detail_msg").data("msg-id");
        } // \OGame\Messages\Message::ACTION_REVIVE == 104

        messageActionAjax(
          {
            tabid: currentTab,
            messageId: messageId,
            _token: token,
            action: 104,
          },
          (data) => {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);

            if (data[messageId] == true) {
              //in message list view
              $(event.target).parents("li.msg").remove(); //in message detail view

              $(event.target).parents("div.ui-dialog").remove();
              $("li.msg[data-msg-id=" + messageId + "]").remove();

              if (window.location.href.indexOf("page=standalone") > -1) {
                location.reload();
              }
            } else {
              fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
            }
          },
        );
      })
      .on("click", ".js_actionReviveAll", function (event) {
        // \OGame\Messages\Message::ACTION_REVIVE == 104
        messageActionAjax(
          {
            tabid: ogame.messages.getCurrentMessageTab(),
            messageId: -1,
            _token: token,
            action: 104,
          },
          (data) => {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);

            if (data["result"] == true) {
              location.reload();
            } else {
              fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
            }
          },
        );
      })
      .on("click", ".js_actionDelete", function (event) {
        var messageId = $(this).parents("li.msg").data("msg-id");

        if (!messageId) {
          messageId = $(this).parents("div.detail_msg").data("msg-id");
        } // \OGame\Messages\Message::ACTION_DELETE == 105

        messageActionAjax(
          {
            tabid: currentTab,
            messageId: messageId,
            _token: token,
            action: 105,
          },
          (data) => {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);

            if (data[messageId] == true) {
              $(event.target).parents("li.msg").remove();

              if (window.location.href.indexOf("page=standalone") > -1) {
                location.replace("index.php?page=ingame&component=overview");
              }
            } else {
              fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
            }
          },
        );
      })
      .on("click", ".js_actionDeleteAll", function (event) {
        var messageId = $(this).parents("li.msg").data("msg-id"); // \OGame\Messages\Message::ACTION_DELETE == 105

        messageActionAjax(
          {
            tabid: ogame.messages.getCurrentMessageTab(),
            messageId: -1,
            _token: token,
            action: 105,
          },
          (data) => {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);

            if (data["result"] == true) {
              location.reload();
            } else {
              fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1);
            }
          },
        );
      })
      .on("click", ".paginator", function (event) {
        // Skip if pagination button is disabled
        if ($(this).hasClass("disabled")) {
          return false;
        }

        var page = $(this).data("page");
        var bla = $(this).closest('div[class^="ui-tabs-panel"]');

        // Find the active subtab to get the tab/subtab parameters
        var activeSubtab = $(".subtabs .ui-state-active:visible");
        var activeTabUrl;

        if (activeSubtab.length > 0) {
          // We're in a subtab view (e.g., fleets or communication)
          activeTabUrl = activeSubtab.find("a").attr("href");
        } else {
          // We're in a main tab view (e.g., economy, system, universe)
          var activeTab = $(".js_tabs .ui-state-active:visible");
          activeTabUrl = activeTab.find("a").attr("href");
        }

        if (activeTabUrl) {
          // Append pagination parameter to the URL
          var separator = activeTabUrl.indexOf("?") > -1 ? "&" : "?";
          var url = activeTabUrl + separator + "pagination=" + page;

          $.ajax({
            type: "GET",
            url: url,
            dataType: "html",
            success: function (data) {
              bla.html(data);
            },
            error: function () {},
          });
        }
      })
      .on("click", ".jumpToAllianceApplications", function (event) {
        location.href = "index.php?page=ingame&component=alliance&tab=applications";
      })
      .on("click", "a.js_actionCollect", function (event) {
        event.preventDefault();
        let url = $(event.currentTarget).attr("href");
        $.ajax({
          type: "POST",
          url: url,
          dataType: "json",
          data: {
            new_token: ogame.messages.marketToken || "",
          },
          success: function (data) {
            let status = data.status || "failure";
            let statusMessage = data.statusMessage || "";

            if (status === "success") {
              ogame.messages.marketToken = data.newToken;
              $(event.currentTarget).hide();
              $(event.currentTarget).replaceWith(statusMessage);
              fadeBox(data.message || "", false);
              getAjaxResourcebox();
            }

            if (status === "failure") {
              let error = data.errors[0] || undefined;

              if (error) {
                fadeBox(error.message, true);
              }
            }
          },
          error: function (data) {},
        });
      });
  },
  selectCurrentMessageTab: function () {
    var currentTab = $(".subtabs .ui-state-active:visible");

    if (!currentTab.length) {
      // none of the subTabs is visible
      currentTab = $(".js_tabs .ui-state-active:visible");
    }

    return currentTab;
  },
  getCurrentMessageTab: function () {
    var currentTab = $(".subtabs .ui-state-active:visible").attr("data-tabid");

    if (!currentTab) {
      // none of the subTabs is visible
      currentTab = $(".js_tabs .ui-state-active:visible").attr("data-tabid");
    }

    return currentTab;
  },
  getCurrentEarliestMessage: function () {
    //console.log($('.ui-tabs-panel .tab_inner .msg:visible'));
    return $(".ui-tabs-panel .tab_inner .msg:visible").last().attr("data-msg-id");
  },
  initCommentForm: function () {
    ogame.messages.initWriteNewMsgBox($("#newCommentForm"));
    $("#newCommentForm").on("click", ".js_send_comment", function (e) {
      let newToken = $("[name='_token']").val();
      e.preventDefault();
      var $myForm = $(this).closest("form");
      var $messageId = $myForm.find("input[name=messageId]").val();
      $.ajax({
        type: "POST",
        url: $myForm.attr("action"),
        dataType: "json",
        data: {
          messageId: $messageId,
          ajax: 1,
          action: 108,
          // \OGame\Messages\Message::ACTION_COMMENT
          text: $myForm.find("textarea[name=text]").val(),
          _token: newToken,
          standalonePage: window.location.href.indexOf("page=standalone") > -1 ? 1 : 0,
        },
        success: function (data) {
          if (data["newAjaxToken"]) {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);
          }

          fadeBox(data.message, data.error);
          $myForm.find("textarea[name=text]").val("");
          $myForm
            .parent()
            .after(
              '<li class="msg"><div class="msg_status"></div><div class="msg_head">' +
                data.commentheader +
                '</div><div class="msg_content">' +
                data.commentcontent +
                "</div></li>",
            );
          $("#scrollToComments").text(data.commentcount);
        },
        error: function (data) {},
      });
    });
  },

  /**
   * initializes the overlay for sharing reports
   * @returns {undefined}
   */
  initShareReportOverlay: function () {
    ogame.messages.initWriteNewMsgBox($("#newSharedReportForm"));
    $("#newSharedReportForm").on("click", ".js_send_msg_share", function (e) {
      e.preventDefault();
      var $myForm = $(this).closest("form");
      var $messageId = $myForm.find("input[name=messageId]").val();
      let newToken = $("[name='_token']").val();
      var $empfaenger = $myForm.find("li.select2-selection__choice");
      var $myData = [];
      $empfaenger.each(function () {
        $myData.push($(this).attr("title"));
      });
      $.ajax({
        type: "POST",
        url: $myForm.attr("action"),
        dataType: "json",
        data: {
          messageId: $messageId,
          empfaenger: $myData,
          ajax: 1,
          action: 106,
          // \OGame\Messages\Message::ACTION_SHARE
          text: $myForm.find("textarea[name=text]").val(),
          _token: newToken,
          standalonePage: window.location.href.indexOf("page=standalone") > -1 ? 1 : 0,
        },
        success: function (data) {
          if (data["newAjaxToken"]) {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);
          }

          fadeBox(data.message, data.error);
          $myForm.closest('div[class^="overlayDiv"]').remove();
        },
        error: function (data) {},
      });
    });
  },

  /**
   * initializes the subtab messages where user can write messages (=Rundmails)
   * to members of their allies and federates
   * @returns {undefined}
   */
  initSubTabMessages: function () {
    // accordion initialized here, because this is the only tab where it is needed
    $(".js_accordion").accordion({
      collapsible: true,
      heightStyle: "content",
      active: false, // @TODO: this needs to be updated when the accordion was open on tab/page change
    });
    ogame.messages.initWriteNewMsgBox($("#newMsgForm"));
    $("html").off(".subtabmessages");
    $("#newMsgForm").on("click.subtabmessages", ".js_send_msg", function (e) {
      e.preventDefault();
      var $myForm = $(this).parents("form");
      let newToken = $("[name='_token']").val();
      var recipientIDs = {};
      $(".input_replacement")
        .children()
        .each(function () {
          if (typeof recipientIDs[$(this).data("recipient-cat")] == "undefined") {
            recipientIDs[$(this).data("recipient-cat")] = [];
          }

          recipientIDs[$(this).data("recipient-cat")].push($(this).data("recipient-id"));
        });
      $.ajax({
        type: "POST",
        url: $myForm.attr("action"),
        dataType: "json",
        data: {
          empfaenger: recipientIDs,
          _token: newToken,
          text: $myForm.find(".new_msg_textarea").val(),
        },
        success: function (data) {
          if (data["newAjaxToken"]) {
            token = data.newAjaxToken;
            $("[name='_token']").val(data.newAjaxToken);
          }

          fadeBox(data.message, data.error); // add message to this tab if successful todo real data for inserted message needed from php

          if (!data.error) {
            ogame.messages.sendSubtabMsg($(".new_msg_textarea").val(), recipientIDs);
          }
        },
        error: function () {},
      }); // @TODO: clear input from message box and recipient fields
    });
  },
  initTabCommunication: function () {
    ogame.messages.initTabs($(".js_subtabs_communication"));
  },
  initTabFleets: function () {
    // using a callback function here, because it is the same function for every subtab of fleets
    ogame.messages.initTabs($(".js_subtabs_fleets"), ogame.messages.initTrash);
  },
  initTabMarket: function () {
    ogame.messages.initTabs($(".js_subtabs_market"), ogame.messages.initTrash);
  },

  /**
   * Wrapper for the initialization of tabs
   * @param {JQuery Object} $el - Element to attach tabs to
   * @param {Function} callbackFunc - a Function to be called after the tabs are loaded
   * @returns {undefined}
   */
  initTabs: function ($el, callbackFunc) {
    $el.tabs({
      beforeLoad: function () {
        $(".ajax_load_shadow").show();
      },
      load: function (e, el) {
        //console.info("load",el.tab.attr('id'));
        // select functions to initzialize based on id of active tab
        ogame.messages.doInitAction(el.tab.attr("id"));
        $(".ajax_load_shadow").hide(); // call the additional callback if one was given

        if (typeof callbackFunc === "function") {
          callbackFunc(el.tab);
        }
      },
      // create is only called once when the tabs are created
      create: function (e, el) {
        ogame.messages.doInitAction(el.tab.attr("id"));
      },
    });
  },

  /**
   *
   */
  initTrash: function ($activeTab) {
    if (!$activeTab) return;
    $(".js_active_tab").html($activeTab.data("subtabname"));

    if ($activeTab.attr("id") === "subtabs-nfFleetTrash" || $activeTab.attr("id") === "subtabs-nfMarketTrash") {
      $(".trash_box").addClass("trash_open");
      $(".in_trash").show();
      $(".not_in_trash").hide();
    } else {
      $(".trash_box").removeClass("trash_open");
      $(".in_trash").hide();
      $(".not_in_trash").show();
    }
  },

  /**
   * Initializes the functions and events that are necessary for writing a new message
   * (Used on newsfeed for broadcast messages and new shared reports)
   * @param {Jquery Object} $newMsgForm - Element to attach the new message events to
   * @returns {undefined}
   */
  initWriteNewMsgBox: function ($newMsgForm) {
    initBBCodeEditor(locaKeys, itemNames, false, ".new_msg_textarea", 2000);
    $("html").off(".writeNewMsgBox"); // SubTabMessages - close any open recipient select boxes when clicking outside the box:

    $("html").on("click.writeNewMsgBox", function (e) {
      if ($(".new_msg_label").hasClass("open") && $(e.target).parents(".recipient_select_box").length < 1) {
        $(".input_replacement").removeClass("focus");
        $(".new_msg_label").removeClass("open");
        $(".new_msg_label").siblings(".recipient_select_box").hide();
      }
    });
    $newMsgForm
      .on("click.writeNewMsgBox", ".input_replacement", function (e) {
        e.stopPropagation();
        ogame.messages.toggleRecipientSelectBox($(e.target).data("recipient-cat"));
      })
      .on("click.writeNewMsgBox", ".new_msg_label", function (e) {
        e.stopPropagation();
        ogame.messages.toggleRecipientSelectBox($(e.currentTarget).data("recipient-cat"));
      })
      .on("click.writeNewMsgBox", ".recipient_select_box .ally_rank", function () {
        ogame.messages.toggleRecipientSelection($(this));
      })
      /*           .on('click.writeNewMsgBox', '.js_all_recipients', function() {
                   ogame.messages.toggleSelectAllRecipients($(this));
               })*/
      .on("click.writeNewMsgBox", ".remove_recipient", function () {
        ogame.messages.removeRecipient($(this).closest(".recipient_txt").data("recipient-id"));
      });
  },

  /**
   * Remove Recipient from input field and remove corresponding selection
   * @param {type} recipientId
   * @returns {undefined}
   */
  removeRecipient: function (recipientId) {
    $(".ally_rank").filter(function () {
      if ($(this).data("recipient-id") === recipientId) {
        $(this).removeClass("selected");
      }
    });
    $(".recipient_txt").filter(function () {
      if ($(this).data("recipient-id") === recipientId) {
        $(this).remove();
      }
    });
  },
  sendSubtabMsg: function (msg, recipientIDs) {
    if (!msg) {
      // @TODO: show warning
      console.warn("sendSubtabMsg: msg was empty");
      return;
    }

    if (!recipientIDs) {
      // @TODO: show warning
      console.warn("sendSubtabMsg: msg had no recipients");
      return;
    }

    var msgData = {};
    msgData.date = getFormatedDate(serverTime.getTime(), "[d].[m].[Y] <span>[H]:[i]:[s]</span>");
    msgData.newClass = "msg_new";
    msgData.title = recipientIDs;
    msgData.senderName = "100011"; // needs to be id of current player, where do we get it from?

    msgData.msgID = "111"; // this is probably some unique number

    msgData.msgContent = msg;
    msgData.commentsURL = "";
    msgData.detailURL = "";
    msgData.commentsCount = 0;
    var data = array(msgData); // @TODO: Tell Backend that theres a new message that has to be sent and processed

    ogame.messages.addMessage($("#subtabs-nfCommunicationMessages"), data);
  },

  /**
   * toggle the box for recipient selection with the correct category
   * @param {String} recipientCat - category of recipients
   * @returns {undefined}
   */
  toggleRecipientSelectBox: function (recipientCat) {
    $(".input_replacement").filter(function () {
      if ($(this).data("recipient-cat") === recipientCat && !$(this).hasClass("focus")) {
        $(this).addClass("focus");
      }
    });
    $(".new_msg_label").filter(function () {
      var $currNewMsgLabel = $(this);

      if ($currNewMsgLabel.data("recipient-cat") === recipientCat) {
        if ($currNewMsgLabel.hasClass("open")) {
          //hide current
          $currNewMsgLabel.removeClass("open").siblings(".recipient_select_box").hide();
        } else {
          var $recipientSelectBox = $currNewMsgLabel.siblings(".recipient_select_box"),
            $scrollBox = $recipientSelectBox.find(".scroll_box"); // hide all

          $(".new_msg_label").removeClass("open").siblings(".recipient_select_box").hide(); // show current

          $currNewMsgLabel.addClass("open");
          $recipientSelectBox.show();
          $scrollBox.hasClass("mCustomScrollbar")
            ? $scrollBox.mCustomScrollbar("update")
            : $scrollBox.mCustomScrollbar();
        }
      }
    });
  },

  /**
   * add the clicked element to the corresponding list of recipients if it is
   * not yet in the list, remove it from the list otherwise.
   * @param $recipientLiEl
   */
  toggleRecipientSelection: function ($recipientLiEl) {
    var recipientCat = $recipientLiEl.data("recipient-cat"),
      recipientId = $recipientLiEl.data("recipient-id"); // some ranks (i.e. "founder of the ally") cannot be deselected

    if ($recipientLiEl.hasClass("always_selected")) return;

    if ($recipientLiEl.hasClass("complete_ally")) {
      ogame.messages.toggleSelectAllRecipients(recipientCat);
      return;
    }

    if (!$recipientLiEl.hasClass("selected")) {
      ogame.messages.createRecipient(recipientId, recipientCat, $recipientLiEl.html());
      $recipientLiEl.addClass("selected");
    } else {
      ogame.messages.removeRecipient(recipientId);
    }
  },

  /**
   * toggle the selection of all recipients from a given list
   */
  toggleSelectAllRecipients: function (currCat) {
    var cASelected = $(".complete_ally").hasClass("selected"),
      recipientId = cASelected ? "255" : "1",
      name = cASelected ? "loca.founder" : "loca.completeAlliance";
    $(".input_replacement").children().remove();
    ogame.messages.createRecipient(recipientId, currCat, name);
    $(".recipient_list").filter(function () {
      if ($(this).data("recipient-cat") === currCat) {
        $(this)
          .find(".ally_rank")
          .each(function () {
            if (!$(this).hasClass("always_selected")) {
              cASelected ? $(this).removeClass("selected") : $(this).addClass("selected");
            }
          });
      }
    });
  },
}; // Old Messages Code below this line:
