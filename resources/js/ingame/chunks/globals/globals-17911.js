/**/
// ogame.chat implementation has been moved to resources/js/ingame/chat.js
var _ogame_chat_removed = {
  socket: null,
  connected: false,
  connecting: false,
  timeout: null,
  retryInterval: 5000,
  playerId: null,
  associationId: null,
  data: {
    association: {},
  },
  playernames: {},
  playerList: null,
  isLoadingPlayerList: false,
  playerListSelector: new Array(),

  /**
   * Initialize connection to the socket
   */
  initConnection: function () {
    var $this = ogame.chat; // if its connected or on connecting do nothing

    if ($this.connecting || $this.connected || $this.isMobile) {
      if (this.socket) {
        $this.socket.disconnect();
      }
    }

    $this.connecting = true; // try to connect

    try {
      $this.socket = io.connect(":" + nodePort + "/chat", nodeParams);
      $this.socket.on("connect", function () {
        clearTimeout(this.timeout); // send session for authorize... on success set it to connected else disconnect socket

        $this.socket.emit("authorize", session, function (success) {
          $this.connecting = false;

          if (success) {
            $this.connected = true;
          } else {
            $this.socket.disconnect();
          }
        });
      });
      $this.socket.on("chat", function (data) {
        $this.messageReceived(data);
      });
      $this.socket.on("disconnect", function () {
        $this.connected = false;
        $this.connecting = false; //$this.retryConnection();
      });
    } catch (e) {
      // TODO: Ursache fuer doppelten Aufruf auf der Chat-Seite finden
      $this.connecting = false; // wichtig, damit 2. Aufruf durch geht
    }
  },

  /**
   * initialize connection and chatlog
   */
  initialize: function () {
    if (typeof nodeUrl === "undefined") {
      return;
    }

    var $this = ogame.chat;
    loadScript(nodeUrl, $this.initConnection);
    $(".new_msg_count[data-playerid]").each(function () {
      $this.saveMessageCounter($(this).data("new-messages"), $(this).data("playerid"));
    });
    this.updateTotalNewChatCounter(); // Aufrufe von Chats ueber die Spielerliste

    $(".js_playerlist").on("click", ".playerlist_item", function () {
      var nothing2load = $(this).hasClass("nothingThere");

      if (!nothing2load) {
        var msgid = $(this).data("msgid");

        if (msgid) {
          $this.loadChatLogWithPlayer(this, msgid);
        } else {
          $this.loadChatLogWithPlayer(this);
        }
      }
    }); // Aufruf von Alli/Kolaitions-Chats ueber die Spielerliste

    $(".js_playerlist").on("click", ".openAssociationChat", function () {
      var msgid = $(this).data("msgid");

      if (msgid) {
        $this.loadChatLogWithAssociation(this, msgid);
      } else {
        $this.loadChatLogWithAssociation(this);
      }
    }); // Aufrufe von Chats ueber die Chatliste

    $("#chatMsgList").on("click", ".msg", function () {
      var msgPlayerId = $(this).data("playerid");
      var msgAssociationId = $(this).data("associationid");

      if (msgPlayerId !== undefined && msgPlayerId > 0) {
        $this.saveMessageCounter(0, msgPlayerId);
        ogame.messagemarker.setPartnerId(msgPlayerId);
        ogame.messagemarker.updateNewMarker();
        ogame.chat.updateTotalNewChatCounter();
        var msgid = $(".playerlist .playerlist_item[data-playerId=" + msgPlayerId + "]").data("msgid");

        if (msgid) {
          $this.loadChatLogWithPlayer(this, msgid);
        } else {
          $this.loadChatLogWithPlayer(this);
        }
      } else {
        var msgid = $(".playerlist .playerlist_item[data-associationId=" + msgAssociationId + "]").data("msgid");
        $this.saveMessageCounterAssociation(0, msgAssociationId);

        if (msgid) {
          $this.loadChatLogWithAssociation(this, msgid);
        } else {
          $this.loadChatLogWithAssociation(this);
        }
      }
    });
    $(".chat").on("click", ".sys_msg", function (event) {
      var playerIdOfclickedChat = $(this).data("foreign-player-id");
      var associationIdOfclickedChat = $(this).data("foreign-association-id");
      var ajaxData = {
        playerId: playerIdOfclickedChat,
        associationId: associationIdOfclickedChat,
        ajax: 1,
      };
      $.ajax({
        url: chatUrlLoadMoreMessages,
        type: "POST",
        dataType: "html",
        data: ajaxData,
        success: function (data) {
          //Finding the right chat window
          $(".chat").each(function (index, element) {
            if (playerIdOfclickedChat !== undefined && playerIdOfclickedChat == $(element).data("foreign-player-id")) {
              $(element).html(data); //Replace the content of the chat-window with the new data from the backend.
            } else if (
              associationIdOfclickedChat !== undefined &&
              associationIdOfclickedChat == $(element).data("foreign-association-id")
            ) {
              $(element).html(data); //Replace the content of the chat-window with the new data from the backend.
            }
          });
        },
        error: function (jqXHR, textStatus, errorThrown) {},
      });
    });
    $("body").on("click", ".js_openChat", function () {
      $this.loadChatLogWithPlayer(this);
    }); //maximize Chat set

    if (typeof $.cookie("maximizeId") == "string" || typeof $.cookie("maximizeId") == "number") {
      $('#chatMsgList .msg[data-playerid="' + $.cookie("maximizeId") + '"]').trigger("click");
      $.cookie("maximizeId", null);
    }
  },

  /**
   * gets the total new chat counter.
   *
   * @returns {Number}
   */
  getTotalNewChatCounter: function () {
    return ogame.messagecounter.sumNewChatMessages;
  },

  /**
   * calc total new chat counter and updates it.
   *
   * @returns {Number} total new chat counter
   */
  updateTotalNewChatCounter: function () {
    var sumNewChatMessages = 0;

    if ($(".msg .new_msg_count").length > 0) {
      $(".msg .new_msg_count").each(function () {
        sumNewChatMessages += Number($(this).data("new-messages"));
      });
    } else if ($("#chatBarPlayerList .new_msg_count").length > 0) {
      $("#chatBarPlayerList .new_msg_count").each(function () {
        sumNewChatMessages += Number($(this).data("new-messages"));
      });
    }

    ogame.messagecounter.initialize(ogame.messagecounter.type_chat, ogame.chat.playerId);

    if (ogame.messagecounter.sumNewChatMessages !== sumNewChatMessages) {
      ogame.messagecounter.initChatCounter(sumNewChatMessages);
      ogame.messagecounter.sumNewChatMessages = sumNewChatMessages;
      ogame.messagecounter.update();
    }

    return sumNewChatMessages;
  },

  /**
   * retry to initialize Connection
   */
  retryConnection: function () {
    var $this = ogame.chat;
    setTimeout(function () {
      $this.initConnection();
    }, 5000);
  },

  /**
   * sending the message
   *
   * @param {number} playerId >> id of the chat partner
   * @param {number} associationId >> id of the association (ally or coalition)
   * @param {string} messageText >> text to send
   *
   */
  sendMessage: function (playerId, associationId, messageText, refData) {
    var $this = ogame.chat;

    if ($.trim(messageText).length == 0) {
      showError("TEXT_EMPTY");
      return;
    }

    var ajaxData;

    if (playerId > 0) {
      ajaxData = {
        playerId: playerId,
        text: messageText,
        mode: 1,
        ajax: 1,
        _token: window.ajaxChatToken,
      };
    } else {
      ajaxData = {
        associationId: associationId,
        text: messageText,
        mode: 3,
        ajax: 1,
        _token: window.ajaxChatToken,
      };
    }

    if (typeof refData !== "undefined" && typeof refData.id !== "undefined") {
      ajaxData.msg2reply = refData.id;
    }

    function sendMessageViaAjax() {
      // send ajax Request
      $.ajax({
        url: chatUrl,
        type: "POST",
        dataType: "json",
        data: ajaxData,
        success: function (data) {
          addChatMessage(data);
          window.ajaxChatToken = data.newToken;
        },
        error: function (jqXHR, textStatus, errorThrown) {},
      });
    }

    function messageSent(data) {
      if (typeof data.refAuthor !== "undefined" && typeof data.refContent !== "undefined") {
        $refData = {
          author: data.refAuthor,
          text: data.refContent,
        };
      } else {
        $refData = 0;
      }

      if (data.targetId !== undefined) {
        $this.addChatItem(data.targetId, 0, data.text, data.id, false, $refData, data.date);
      } else {
        $this.addChatItem(playerId, data.targetAssociationId, data.text, data.id, false, $refData, data.date);
      }
    }

    function showError(errorText) {
      if (chatLoca[errorText] !== undefined) {
        errorBoxNotify(LocalizationStrings.error, chatLoca[errorText], LocalizationStrings.ok);
      } else {
        errorBoxNotify(LocalizationStrings.error, errorText, LocalizationStrings.ok);
      }
    }

    function addChatMessage(result) {
      switch (result.status) {
        case "NOT_AUTHORIZED":
          // do not show this error, try to send with ajax instead
          sendMessageViaAjax();
          break;

        case "OK":
          messageSent(result);
          ogame.chat.cleanupUrl();
          break;

        default:
          showError(result.status);
      }
    }

    sendMessageViaAjax();
  },

  /**
   * get new message in chat
   *
   * @param {object} data >> message data
   *
   */
  messageReceived: function (data) {
    var $this = ogame.chat;

    if (typeof data.refAuthor !== "undefined" && typeof data.refText !== "undefined") {
      $refData = {
        author: data.refAuhtor,
        text: data.refText,
      };
    } else {
      $refData = 0;
    } // for the association chats we need to get the player names from the backend.

    if (data.senderName !== undefined && data.senderId !== undefined) {
      //Save names, you get from the backend for later use in addChatItem
      $this.playernames[data.senderId] = data.senderName;
    }

    if ($(".chat_bar_list").length) {
      //only if chat bar is active
      if (data.associationId !== undefined && data.associationId > 0) {
        if ($this.data.association[data.associationId] === undefined) {
          $this.loadChatLogWithAssociation(
            data.associationId,
            null,
            function () {
              $this.addChatItem(data.senderId, data.associationId, data.text, data.id, true, $refData, data.date);
            },
            false,
          );
        } else {
          $this.addChatItem(data.senderId, data.associationId, data.text, data.id, true, $refData, data.date);
        }
      } else {
        if ($this.data[data.senderId] === undefined) {
          $this.loadChatLogWithPlayer(
            data.senderId,
            null,
            function () {
              $this.addChatItem(data.senderId, 0, data.text, data.id, true, $refData, data.date);
            },
            false,
          );
        } else {
          $this.addChatItem(data.senderId, 0, data.text, data.id, true, $refData, data.date);
        }
      }
    }

    if (data.associationId !== undefined && data.associationId > 0) {
      // count new mesages only if chat closed
      if ($('.chat_bar_list_item.open[data-associationid="' + data.associationId + '"]').length <= 0) {
        var newCount = $('.new_msg_count[data-associationid="' + data.associationId + '"]').data("new-messages");

        if (isNaN(newCount)) {
          newCount = 0;
        }

        newCount++;
        $this.saveMessageCounterAssociation(newCount, data.associationId);
        $this.updateTotalNewChatCounter();
      } else {
        // Set received message as read
        var ajaxData = {
          associationId: data.associationId,
          mode: 4,
          ajax: 1,
          updateUnread: 1,
        };
        $.ajax({
          url: chatUrl,
          type: "POST",
          data: ajaxData,
          success: function (data) {},
          error: function (jqXHR, textStatus, errorThrown) {},
        });
      }
    } else if (data.senderId !== undefined && data.senderId > 0) {
      ogame.messagemarker.setPartnerId(data.senderId);

      if (!$this.isOpen(data.senderId)) {
        ogame.messagecounter.initialize(ogame.messagecounter.type_chat, data.senderId);
        var newCount = parseInt(ogame.messagecounter.newChats[data.senderId]);

        if (isNaN(newCount)) {
          newCount = 0;
        }

        newCount++;
        $this.saveMessageCounter(newCount, data.senderId);
        ogame.messagemarker.updateNewMarker();
      } else {
        $this.saveMessageCounter(0, $(this).data("playerid"));
        ogame.messagemarker.updateNewMarker();
      }
    }
  },
  cleanupUrl: function () {
    // @todo what is the purpose?
    // since twig components always contain a & in their path I need to comment it out
    /*
    var currentUrl = window.location.href;
    var indexOfMsgid = currentUrl.indexOf('&');
      	if(indexOfMsgid > 0) {
    var paramIndex = currentUrl.indexOf('?');
    var firstPartOfUrl = currentUrl.substring(0, paramIndex);
    var newUrl = firstPartOfUrl+'?page=chat';

    	window.history.pushState({}, "", newUrl);
    }
    */
  },
  saveMessageCounter: function (count, playerId) {
    if (isNaN(playerId) || playerId === 0) {
      return false;
    }

    $('.new_msg_count[data-playerid="' + playerId + '"]').data("new-messages", count);
    ogame.messagecounter.newChats[playerId] = count;
  },
  saveMessageCounterAssociation: function (count, associationId) {
    if (isNaN(associationId) || associationId === 0) {
      return false;
    }

    $('.new_msg_count[data-associationid="' + associationId + '"]').data("new-messages", count);
    $('.new_msg_count[data-associationid="' + associationId + '"]').text(count);
    ogame.messagemarker.updateNewMarker();
  },

  /**
   * proof if the chat of the player is open
   * @param {string} playerid
   * @returns {boolean}
   */
  isOpen: function (playerid) {
    var isChatOpen = false;
    var chatPartnerId = $(".chatContent").data("chatplayerid");

    if (chatPartnerId != "undefined" && chatPartnerId == playerid) {
      isChatOpen = true;
    } else {
      $(".chat_box").each(function () {
        if ($(this).attr("data-playerid") == playerid) {
          if ($(this).css("display") == "block") {
            isChatOpen = true;
          }
        }
      });
    }

    return isChatOpen;
  },

  /**
   * Load chatlog of player and playerinfo
   *
   * @param {mixed} element >> playerid of the other player
   * @param function callback
   *
   */
  loadChatLogWithPlayer: function (element, msg2reply, callback, updateUnread) {
    var $this = ogame.chat;
    var foreignPlayerId;

    if (typeof updateUnread == "undefined") {
      updateUnread = true;
    }

    if (typeof element == "number") {
      foreignPlayerId = element;
    } else {
      foreignPlayerId = $(element).attr("data-playerId");
    }

    var ajaxData = {
      playerId: foreignPlayerId,
      mode: 2,
      ajax: 1,
      updateUnread: updateUnread ? 1 : 0,
    };

    if (typeof msg2reply == "number") {
      ajaxData.msg2reply = msg2reply;
    }

    $.ajax({
      url: chatUrl,
      type: "POST",
      data: ajaxData,
      success: function (data) {
        data = JSON.parse(data);
        $this.data[data.playerId] = {
          playerstatus: data.playerstatus,
          playerName: data.playerName,
          playerId: data.playerId,
          chatItems: data.chatItems,
          chatItemsByDateAsc: data.chatItemsByDateAsc,
        };

        if (typeof callback == "function") {
          callback();
        } else if ($(element).parents("#chatBarPlayerList").length || $("body")[0].id != "chat") {
          // use chat bar
          $this.showChat(data);
        } else {
          $this.showChatHistory(data);
        }

        var currentChat = $(".chat_bar_list").find("[data-playerid='" + data.playerId + "']");
        $this.updateCustomScrollbar(currentChat.find(".chat_box_ctn"));
      },
      error: function (jqXHR, textStatus, errorThrown) {},
    });
  },

  /**
   * Load chatlog of association chat
   *
   * @param {mixed} element >> associationid of the other player
   * @param function callback
   *
   */
  loadChatLogWithAssociation: function (element, msg2reply, callback, updateUnread) {
    var $this = ogame.chat;
    var associationId;

    if (typeof updateUnread == "undefined") {
      updateUnread = true;
    }

    if (typeof element == "number") {
      associationId = element;
    } else {
      associationId = $(element).attr("data-associationid");
    }

    var ajaxData = {
      associationId: associationId,
      mode: 4,
      ajax: 1,
      updateUnread: updateUnread ? 1 : 0,
    };

    if (typeof msg2reply == "number") {
      ajaxData.msg2reply = msg2reply;
    }

    $.ajax({
      url: chatUrl,
      type: "POST",
      data: ajaxData,
      success: function (data) {
        data = JSON.parse(data);
        $this.data.association[data.associationId] = {
          playerstatus: data.playerstatus,
          associationName: data.associationName,
          associationId: data.associationId,
          chatItems: data.chatItems,
          chatItemsByDateAsc: data.chatItemsByDateAsc,
        };

        if (typeof callback == "function") {
          callback();
        } else if ($(element).parents("#chatBarPlayerList").length || $("body")[0].id != "chat") {
          // use chat bar
          $this.showChat(data);
        } else {
          $this.showChatHistory(data);
        }

        var currentChat = $(".chat_bar_list").find("[data-associationid='" + data.associationId + "']");
        $this.updateCustomScrollbar(currentChat.find(".chat_box_ctn"));
      },
      error: function (jqXHR, textStatus, errorThrown) {},
    });
  },

  /**
   * This initializes the node.js chat
   * @returns {undefined}
   */
  initChat: function (playerId, isMobile) {
    ogame.chat.playerId = playerId;
    ogame.chat.isMobile = isMobile;
    ogame.chat.initPlayerlist();
    ogame.chat.initialize();
    ogame.chat.toggleVisibility();
    ogame.chat.setVisibilityState();
    ogame.chat.initMaximize();
    ogame.chat.getInMaxChat();
  },

  /**
   * get data of a specific message in dom
   *
   * @return {object} itemData
   */
  getLastChatItemData: function () {
    var $this = ogame.chat;
    var newestChatMessage = null;
    $(".chat_box_ctn .mCustomScrollBox .mCSB_container").each(function () {
      var lastMessage = $(this).children("ul.chat").children("li:last");

      if (newestChatMessage === null || lastMessage.attr("data-chat-id") > newestChatMessage.attr("data-chat-id")) {
        newestChatMessage = lastMessage;
      }
    }); // Ugly hack to enable chatting when chatbar is disabled

    if (newestChatMessage === null) {
      $("ul.largeChat").each(function () {
        var lastMessage = $(this).children("li:last");

        if (newestChatMessage === null || lastMessage.attr("data-chat-id") > newestChatMessage.attr("data-chat-id")) {
          newestChatMessage = lastMessage;
        }
      });
    }

    if (newestChatMessage === null) return null;
    var msgDate = newestChatMessage.children(".msg_head").find(".msg_date").html();
    var msgText = newestChatMessage.find(".msg_content").html();
    var itemData = {
      date: msgDate,
      text: msgText,
    };
    return itemData;
  },

  /**
   * Adds a chat item to a chat in the chat bar and to the Detail Chat Page ( = chatHistory)
   * if we are on that page
   *
   * @param {String} foreignPlayerId
   * @param {String} associationId
   * @param {String} msg
   * @param {int} msgId
   * @param {bool} received
   * @param $refData
   * @param srvTime
   * @return {undefined}
   */
  addChatItem: function (foreignPlayerId, associationId, msg, msgId, received, $refData, srvTime) {
    var $this = ogame.chat;
    var currentChat;

    if (associationId > 0) {
      currentChat = $(".chat_bar_list").find("[data-associationid='" + associationId + "']");
    } else {
      currentChat = $(".chat_bar_list").find("[data-playerid='" + foreignPlayerId + "']");
    }

    var chatItemData = {};
    chatItemData.date = srvTime; //will be formated later in createchatitem

    chatItemData.newClass = "new";

    if (received) {
      // the class odd should only be added in case a message was received
      if ($this.data[foreignPlayerId] !== undefined) {
        chatItemData.playerName = $this.data[foreignPlayerId].playerName;
      } else {
        chatItemData.playerName = $this.playernames[foreignPlayerId];
      }

      chatItemData.altClass = "";
    } else {
      chatItemData.playerName = playerName;
      /* global own playerName */

      chatItemData.altClass = "odd";
    }

    chatItemData.chatID = msgId; // this is probably some unique number

    chatItemData.chatContent = msg;

    if (typeof $refData == "object") {
      chatItemData.refData = $refData;
    }

    if (!currentChat.length) {
      // someone new wants to talk to us || we want to talk to someone new \\
      var chatBoxContainer = $this.createChatBarContainer(foreignPlayerId);
      $this.updateChatBar(chatBoxContainer);
      currentChat = $(".chat_bar_list").find("[data-playerid='" + foreignPlayerId + "']");
    }

    var chatItem = $this.createChatItem(chatItemData);
    var lastItem = $this.getLastChatItemData(); // if message already exists

    if (lastItem !== null && (chatItemData.date != lastItem.date || chatItemData.chatContent != lastItem.text)) {
      // chatbar
      currentChat.find(".chat").append(chatItem);
      $this.updateCustomScrollbar(currentChat.find(".chat_box_ctn"));
      var chatHistory = $(".js_chatHistory");

      if (
        chatHistory.length &&
        (chatHistory.data("chatplayerid") == foreignPlayerId || chatHistory.data("associationid") == associationId)
      ) {
        //chat page
        chatHistory.find(".chat.clearfix").append(chatItem.clone());
        $this.updateCustomScrollbar($(".largeChatContainer"));
      }
    }
  },

  /**
   * Add items to moreBox
   *
   * @param {Object} chatbarListItems - Array of chatbar list items
   * @return {undefined}
   */
  addToMoreBox: function (chatbarListItems) {
    var $this = ogame.chat;
    var listLength = chatbarListItems.length;

    if (listLength && $(".more_chat_bar_items").length < 1) {
      $(".chat_bar_list").append($this.createMoreBox("more_chat_bar_items"));
    }

    var moreItemsList = $(".more_chat_bar_items .more_items");
    var moreItemsBox = $(".more_chat_bar_items .chat_box");

    for (var i = 0; i <= listLength; i++) {
      moreItemsList.append(chatbarListItems.pop());
    }

    $this.updateCustomScrollbar(moreItemsBox);
  },

  /**
   * Creates a new chatbar container element.
   *
   * A chatbar container consists of the chatbar list element for the player with the given playerid
   * and the corresponing chatbox (the chatbox is initially open)
   *
   * @param {String} playerid
   * @returns {DOM Object}
   */
  createChatBarContainer: function (playerid) {
    var $this = ogame.chat;

    if (!playerid) {
      return;
    }

    var chatBarData = $this.data[playerid];
    $this.data.playerId = playerid;
    var chatBarItem = $('<li class="chat_bar_list_item open" data-playerid="' + playerid + '"></li>');
    chatBarItem.append('<span class="playerstatus ' + chatBarData.playerstatus + '"></span>');
    chatBarItem.append('<span class="cb_playername">' + chatBarData.playerName + "</span>");
    chatBarItem.append('<span class="icon icon_close fright"></span>'); // adding the chat to the box

    chatBarItem.prepend($this.createChatBox(playerid));
    return chatBarItem;
  },

  /**
   * Creates a new chatbar container element for group chats.
   *
   *
   * @param {String} associationId
   * @returns {DOM Object}
   */
  createChatBarContainerForAssociations: function (associationId) {
    var $this = ogame.chat;

    if (!associationId) {
      return;
    }

    var chatBarData = $this.data.association[associationId];
    $this.data.associationId = associationId;
    var chatBarItem = $('<li class="chat_bar_list_item open" data-associationId="' + associationId + '"></li>');
    chatBarItem.append('<span class="playerstatus ' + chatBarData.playerstatus + '"></span>');
    chatBarItem.append('<span class="cb_playername">' + chatBarData.associationName + "</span>");
    chatBarItem.append('<span class="icon icon_close fright"></span>'); // adding the chat to the box

    chatBarItem.prepend($this.createChatBoxForAssociations(associationId));
    return chatBarItem;
  },

  /**
   * Make it Invisible in the Chatbar
   */
  closeChatBox: function (playerid, associationid) {
    var chatbaritems = $(".chat_bar_list_item");
    $.each(chatbaritems, function (key, item) {
      if (playerid !== undefined && $(item).data("playerid") == playerid) {
        $(item).addClass("outOfChatbar");
        $(item).removeClass("open");
      } else if (associationid !== undefined && $(item).data("associationid") == associationid) {
        $(item).addClass("outOfChatbar");
        $(item).removeClass("open");
      }
    });
  },

  /**
   * Get ONLY the chats that should be shown
   *
   * @returns {Array} visibleChats
   */
  getVisibleChats: function () {
    if (typeof visibleChats == "undefined")
      visibleChats = {
        chatbar: false,
        players: [],
        associations: [],
      };
    return visibleChats;
  },

  /**
   * get playerids from visible chats
   *
   * @returns {Array} playerIDs
   */
  getVisibleChatPlayerIds: function () {
    var $this = ogame.chat;
    var visibles = $this.getVisibleChats();
    var playerIDs = {};
    var jsonIndex = 0;

    for (var i = 0; i < visibles.players.length; i++) {
      if ($.inArray(visibles.players[i]["partnerId"], playerIDs) == -1) {
        playerIDs[jsonIndex] = visibles.players[i]["partnerId"];
        jsonIndex++;
      }
    }

    return playerIDs;
  },

  /**
   * get associationIds from visible chats
   *
   * @returns {Array} playerIDs
   */
  getVisibleChatAssociationIds: function () {
    var $this = ogame.chat;
    var visibles = $this.getVisibleChats();
    var associationIDs = {};
    var jsonIndex = 0;

    for (var i = 0; i < visibles.associations.length; i++) {
      if ($.inArray(visibles.associations[i]["partnerId"], associationIDs) == -1) {
        associationIDs[jsonIndex] = visibles.associations[i];
        jsonIndex++;
      }
    }

    return associationIDs;
  },

  /**
   * Set visibility state of chats
   */
  setVisibilityState: function () {
    var $this = ogame.chat;
    var visiblesPlayers = $this.getVisibleChatPlayerIds();
    var visiblesAssociations = $this.getVisibleChatAssociationIds();
    var listitems = $("#chatBar .chat_bar_list .chat_bar_list_item");

    for (var i = 0; i < listitems.length; i++) {
      var item = listitems.get(i);
      var playerid = $(item).data("playerid");
      var associationid = $(item).data("associationid"); //if its not on the visiblelist - close it instant

      if (playerid !== undefined && !$this.isInJson(playerid, visiblesPlayers)) {
        $this.closeChatBox(playerid, 0);
      } else if (associationid !== undefined && !$this.isInJson(associationid, visiblesAssociations)) {
        $this.closeChatBox(0, associationid);
      } else {
        item.style.display = "inline";

        if ($(item).hasClass("open")) {
          var box = $(item).find("div.chat_box")[0];
          box.style.display = "inline";
          $this.updateCustomScrollbar($(item).find(".chat_box_ctn"), 1);
        }
      }
    }
  },

  /**
   * Search 4 value in json-object
   */
  isInJson: function (value, json) {
    var returningValue = null;

    if ($.isEmptyObject(json)) {
      returningValue = false;
    }

    if (returningValue !== false) {
      $.each(json, function (key, jsonValue) {
        if (jsonValue == value) {
          returningValue = true;
        }
      });

      if (returningValue !== true) {
        false;
      }
    }

    return returningValue;
  },
  toggleVisibility: function () {
    $(".chat_bar_list_item .icon_close").on("click", function (clickObject) {
      var toPlayerId = $(this).parent().data("playerid");
      var closeThis = $(this).closest(".chat_box");

      if (!closeThis.length) {
        closeThis = $(this).parent()[0];
        closeThis.style.display = "none";
      }

      if (toPlayerId > 0) {
        $.ajax({
          type: "POST",
          url: "/game/index.php?page=ajaxChatToggleVisibility",
          data: {
            from: playerId,
            to: toPlayerId,
            showState: 0,
          },
          success: function (e) {},
          error: function (jqXHR, textStatus, errorThrown) {},
        });
      }
    });
    $(".cb_playerlist_box .playerlist_item").on("click", function () {
      var toPlayerId = $(this).data("playerid");

      if (toPlayerId) {
        $.ajax({
          type: "POST",
          url: "/game/index.php?page=ajaxChatToggleVisibility",
          data: {
            from: playerId,
            to: toPlayerId,
            showState: 1,
          },
          success: function (e) {},
          error: function (jqXHR, textStatus, errorThrown) {},
        });
      }
    });
  },
  initMaximize: function () {
    $(".chat_bar_list").on("click.chatBar", ".chat_box .chat_box_title .icon_maximize", function () {
      var chatTitle = $(this).parent();
      var playerid = $(chatTitle).parent().data("playerid");
      $.cookie("maximizeId", playerid);
      $(".chat_bar_list_item.open .chat_box_title .icon_close").trigger("click");
      window.location = bigChatLink + "&playerId=" + playerid;
    });
  },
  getInMaxChat: function () {
    var currentLocation = location.href;
    if (typeof bigChatLink == "undefined") bigChatLink = "";

    if (bigChatLink == currentLocation) {
      if ($.cookie("maximizeId") !== null) {
        $("#chatMsgList .msg[data-playerId=" + $.cookie("maximizeId") + "]").trigger("click");
      }
    }

    $.cookie("maximizeId", null);
  },

  /**
   * Create a chatbox item to be shown in the chatbar.
   * A Chatbox contains chatitems
   *
   * @param {String} playerid
   * @returns {DOM Object} $chatBox
   */
  createChatBox: function (playerid) {
    var $this = ogame.chat;

    if (!playerid) {
      return;
    }

    var chatBarData = $this.data[playerid];
    var chatBoxHeader = $('<div class="chat_box_title"></div>');
    chatBoxHeader.append('<span class="icon icon_close fright"></span>');
    chatBoxHeader.append('<span class="icon icon_maximize fright"></span>');
    var chatBoxCtn = $('<div class="chat_box_ctn"><ul class="chat clearfix"></ul></div>');
    var chatItem = {};

    for (var i = 0; i < chatBarData.chatItemsByDateAsc.length; i++) {
      chatItem = chatBarData.chatItems[chatBarData.chatItemsByDateAsc[i]];
      chatBoxCtn.find(".chat").append($this.createChatItem(chatItem));
    }

    var chatBox = $('<div class="chat_box" data-playerid="' + playerid + '"></div>');
    chatBox.append(chatBoxHeader);
    chatBox.append(chatBoxCtn);
    chatBox.append('<textarea name="text" class="chat_box_textarea"></textarea>');
    return chatBox;
  },

  /**
   * Create a chatbox item to be shown in the chatbar.
   * A Chatbox contains chatitems
   *
   * @param {String} playerid
   * @returns {DOM Object} $chatBox
   */
  createChatBoxForAssociations: function (associationId) {
    var $this = ogame.chat;

    if (!associationId) {
      return;
    }

    var chatBarData = $this.data.association[associationId];
    var chatBoxHeader = $('<div class="chat_box_title"></div>');
    chatBoxHeader.append('<span class="icon icon_close fright"></span>');
    chatBoxHeader.append('<span class="icon icon_maximize fright"></span>');
    var chatBoxCtn = $('<div class="chat_box_ctn"><ul class="chat clearfix"></ul></div>');
    var chatItem = {};

    for (var i = 0; i < chatBarData.chatItemsByDateAsc.length; i++) {
      chatItem = chatBarData.chatItems[chatBarData.chatItemsByDateAsc[i]];
      chatBoxCtn.find(".chat").append($this.createChatItem(chatItem));
    }

    var chatBox = $('<div class="chat_box" data-associationId="' + associationId + '"></div>');
    chatBox.append(chatBoxHeader);
    chatBox.append(chatBoxCtn);
    chatBox.append('<textarea name="text" class="chat_box_textarea"></textarea>');
    return chatBox;
  },

  /**
   * Creates a chatitem
   *
   * A Chatitem can be added to a chatbox in the chatbar or to an existing chat in the chatcontent page.
   *
   * @param {Object} chatData - data needed for the chat item
   * @returns {DOM Object} $chatItem
   */
  createChatItem: function (chatData) {
    if (!chatData) {
      console.warn("no chatItem given");
      return;
    }

    var chatItemHeader = $('<div class="msg_head"></div>');
    chatItemHeader.append(
      '<span class="msg_date fright">' +
        getFormatedDate(chatData.date, "[d].[m].[Y] <span>[H]:[i]:[s]</span>") +
        "</span>",
    );
    chatItemHeader.append(
      '<span class="msg_title blue_txt ' + chatData.newClass + '">' + chatData.playerName + "</span>",
    );
    var chatItem = $('<li class="chat_msg ' + chatData.altClass + '" data-chat-id="' + chatData.chatID + '"></li>');
    chatItem.append(chatItemHeader);

    if (typeof chatData.refData !== "undefined") {
      var refMsgItem = $('<div class="referenceMsg"></div>');
      var refAuthorItem = '<div class="refAuthor">' + chatData.refData.author + "</div>";
      var refTextItem = '<div class="refText new">' + chatData.refData.text + "</div>";
      refMsgItem.append(refAuthorItem);
      refMsgItem.append(refTextItem);
      chatItem.append(refMsgItem);
    }

    chatItem.append('<span class="msg_content">' + chatData.chatContent + "</span>");
    chatItem.append('<div class="speechbubble_arrow"></div>');
    return chatItem;
  },

  /**
   * Create a a chatbar item of the type moreBox that will be a container for
   * all items that don't fit into the chatbar directly
   *
   * @param {String} itemClass
   * @returns {DOM Object} $moreBox
   */
  createMoreBox: function (itemClass) {
    var moreBox = $(
      '<li class="chat_bar_list_item ' +
        itemClass +
        '">' +
        chatLoca["MORE_USERS"] +
        '<span class="icon icon_close fright"></span></li>',
    );
    moreBox.prepend($('<div class="chat_box"><ul class="more_items clearfix"></ul></div>'));
    return moreBox;
  },

  /**
   * Filters the playerlist after the criteria that are selected in the
   * filter form
   *
   * @returns {undefined}
   */
  filterPlayerlist: function () {
    var filters = [];
    var isChecked;
    var filterCheckboxes = $("#playerlistFilters").find('input[type="checkbox"]'); // get filters to check

    filterCheckboxes.each(function () {
      filters.push($(this).attr("id"));
    });
    $(".playerlist_item").show(); // we don't need to filter anything if no filter is active

    isChecked = false;
    filterCheckboxes.each(function () {
      if ($(this).prop("checked")) isChecked = true;
    });

    if (!isChecked) {
      return;
    }

    var doFilter;
    var listElement;
    $(".playerlist_item").filter(function () {
      doFilter = false;
      listElement = $(this); // for every filter

      $.each(filters, function (i, filter) {
        // check if the element matches filter criteria
        // and should be filtered by that criteria
        if (listElement.data(filter) === "off" && $("#" + filter).prop("checked")) {
          doFilter = true;
        }
      }); // hide the element if it should be filtered out

      doFilter === true ? listElement.hide() : listElement.show();
    });
  },

  /**
   * initializes the functions needed for the display of the chatbar
   *
   * @returns {undefined}
   */
  initChatBar: function (playerId) {
    var $this = ogame.chat;
    ogame.chat.playerId = playerId;
    $("html").off(".chatBar");
    $(window).resize(function () {
      $this.updateChatBar();
    }); // var playeridsInList = [];
    //
    // $('#chatBarPlayerList li.playerlist_item').each(function(index) {
    // 	if(!$(this).hasClass('nothingThere')) {
    // 		playeridsInList[index] = $(this).data('playerid');
    // 	}
    // });
    //
    // if(playeridsInList.length > 0) {
    // 	ogame.messagemarker.initMarker(playeridsInList);
    // }
    //        var chatsum = 0;
    //
    //        $('#chatBarPlayerList li.playerlist_item .newMsgMarker').each(function() {
    //        	chatsum = chatsum + 1;
    //        });
    //
    //        ogame.messagecounter.initChatCounter(chatsum);
    // $(".new_msg_count").each(function() {
    //     $this.saveMessageCounter($(this).data('new-messages'), $(this).data('playerid'));
    // });
    //
    // this.updateTotalNewChatCounter();

    $(".chat_bar_list")
      .on("click.chatBar", "#chatBarPlayerList", function (e) {
        if ($(e.target).attr("id") !== "chatBarPlayerList" && !$(e.target).hasClass("onlineCount")) {
          return;
        }

        $(".cb_playerlist_box").toggle();
        $this.updateCustomScrollbar($(".scrollContainer"), true);
        $.ajax({
          url: chatUrl,
          type: "POST",
          dataType: "json",
          data: {
            action: "toggleChatBar",
          },
          success: function (data) {},
          error: function (jqXHR, textStatus, errorThrown) {},
        });
      })
      .on("click.chatBar", ".chat_bar_list_item", function (e) {
        e.stopPropagation();

        if (!isNaN($(this).data("playerid"))) {
          ogame.messagemarker.toggle(
            ogame.messagemarker.action_remove,
            ogame.messagemarker.type_chattab,
            $(this).data("playerid"),
          );
          ogame.messagemarker.toggle(
            ogame.messagemarker.action_remove,
            ogame.messagemarker.type_chatbar,
            $(this).data("playerid"),
          );
          $this.saveMessageCounter(0, $(this).data("playerid"));
          ogame.messagemarker.setPartnerId($(this).data("playerid"));
          ogame.messagemarker.updateNewMarker();
          ogame.chat.updateTotalNewChatCounter();
        } else if (!isNaN($(this).data("associationid") > 0)) {
          $this.saveMessageCounterAssociation(0, $(this).data("associationid"));
        }

        $.ajax({
          url: chatUrl,
          type: "POST",
          dataType: "json",
          data: {
            playerId: $(this).data("playerid"),
            action: "chatBarListRead",
          },
          success: function (data) {},
          error: function (jqXHR, textStatus, errorThrown) {},
        });

        if ($(this).closest(".more_items").length) {
          $this.swapChatBarItem($(this));
        } else {
          $this.toggleChatBox($(e.target), $(this));
        }

        $this.updateVisibleState();
      })
      .on("click.chatBar", ".chat_bar_list_item > .icon_close", function (e) {
        e.stopPropagation();
        var chatbarItem = $(this).closest(".chat_bar_list_item");
        ogame.chat.closeChatBox(chatbarItem.attr("data-playerid"), chatbarItem.attr("data-associationid"));
        chatbarItem.remove("open");
        $this.updateChatBar();
      })
      .on("keyup.chatBar", ".chat_box_textarea", function (e) {
        if ((e.ctrlKey || e.keyCode == 10) && e.keyCode == 13) {
          e.preventDefault();
          var s = $(this).val();
          $(this).val(s + "\n");
        } else {
          if ($.trim($(this).val().length > 0)) {
            e.preventDefault();
            $this.submitChatBarMsg($(e.currentTarget), e.which, e.shiftKey, e.delegateTarget.scrollHeight);
          }
        }
      })
      .on("click.chatBar", ".chat_box_textarea", function (e) {
        ogame.messagemarker.toggle(
          ogame.messagemarker.action_remove,
          ogame.messagemarker.type_chattab,
          $(this).parent().parent().parent().data("playerid"),
        );
        ogame.messagemarker.toggle(
          ogame.messagemarker.action_remove,
          ogame.messagemarker.type_chatbar,
          $(this).parent().parent().parent().data("playerid"),
        );

        if ($(this).data("playerid") > 0) {
          $this.saveMessageCounter(0, $(this).data("playerid"));
        } else if ($(this).data("associationid") > 0) {
          $this.saveMessageCounterAssociation(0, $(this).data("associationid"));
        }
      })
      .on("keydown.chatBar", ".chat_box_textarea", function (e) {
        if (e.keyCode == 13) {
          if (e.shiftKey == false) {
            e.preventDefault();
          }
        }
      }); //        $this.updateCustomScrollbar($('.chat_box_ctn'));
    //        $this.updateCustomScrollbar($(".scrollContainer"), true);
  },

  /**
   * initializes the functions needed for the display of the playerlist
   *
   * @returns {undefined}
   */
  initPlayerlist: function () {
    var $this = ogame.chat;
    var $tools = ogame.tools; // for playerlist toggle

    $(".js_accordion").accordion({
      collapsible: true,
      heightStyle: "content",
    }); // adding "zebra" to list items

    $(".playerlist_item:odd").addClass("odd");
    $tools.addHover(".playerlist_item, .msg, .playerlist_top_box .playerlist"); // playerlist events

    $(".js_playerlist").on("click.playerList", ".pl_filter_set", function () {
      $this.filterPlayerlist();
    });
    $this.filterPlayerlist(); // Do this once, to restore filter settings.
  },

  /**
   * Shows a chat in the chatbar
   *
   * @param data {Object} - the chat data to show
   * @returns {undefined}
   */
  showChat: function (data) {
    var found = false;
    var $this = ogame.chat;
    $(".chat_bar_list_item").each(function () {
      var $item = $(this); // trigger opening if we found the desired chat

      if (
        (data["playerId"] !== undefined && $item.data("playerid") === data["playerId"]) ||
        (data["associationId"] !== undefined && $item.data("associationid") === data["associationId"])
      ) {
        found = true;

        if ($item.hasClass("outOfChatbar")) {
          $item.removeClass("outOfChatbar");
        }

        if (!$item.hasClass("open")) {
          $item.click(); // trigger opening

          $item[0].style.display = "inline";
        } else {
          $item.fadeTo("400", 0.3).fadeTo("400", 1.0);
        }

        $item.find("textarea").focus();
      }
    }); // add the chat if is not yet in chatbar or more list

    if (!found) {
      var chatBarContainer;

      if (data["playerId"] !== undefined) {
        chatBarContainer = $this.createChatBarContainer(data["playerId"]);
      } else {
        chatBarContainer = $this.createChatBarContainerForAssociations(data["associationId"]);
      }

      $this.updateChatBar(chatBarContainer);
    }
  },

  /**
   * Shows the (complete) chat history in the detail chat page
   *
   * @param data {Object} - the chat data to show
   * @returns {undefined}
   */
  showChatHistory: function (data) {
    var history = $(".js_chatHistory");
    var documentContent = data["data"];

    if (history.length) {
      history.remove();
    }

    $("#chatList").remove();
    $(documentContent).insertAfter("#planet");
    $("li.playerlist_item").removeClass("active");
    $("li.playerlist_item[data-playerid='" + data.playerId + "']").addClass("active");
    initBBCodeEditor(locaKeys, itemNames, false, ".new_msg_textarea", 2000, true);
  },

  /**
   * Handles the display of a sent message in a chat on the chat bar
   * @TODO: Actually send the message as well.
   * @param clickedElement {Object} -
   * @param pressedKey {Object} -
   * @param shiftKey {Object} - if shiftkey was pressed simultaneously
   * @param msgScrollHeight {Object} -
   * @returns {undefined}
   */
  submitChatBarMsg: function (clickedElement, pressedKey, shiftKey, msgScrollHeight) {
    var $this = ogame.chat;
    var msgMaxHeight = parseInt($(".chat_box_textarea").css("max-height"));
    var msgPadding =
      parseInt($(".chat_box_textarea").css("padding-top")) + parseInt($(".chat_box_textarea").css("padding-bottom")); // enter pressed while shift already was pressed
    // == new line without sending the message

    if (pressedKey === 13 && shiftKey) {
      if (msgScrollHeight <= msgMaxHeight + msgPadding) {
        clickedElement.css("height", msgScrollHeight - msgPadding);
      }

      return;
    }

    if (pressedKey === 13) {
      if (clickedElement.parent(".chat_box").data("playerid") !== undefined) {
        $this.sendMessage(clickedElement.parent(".chat_box").data("playerid"), 0, clickedElement.val());
      } else if (clickedElement.parent(".chat_box").data("associationid") !== undefined) {
        $this.sendMessage(0, clickedElement.parent(".chat_box").data("associationid"), clickedElement.val());
      } // @TODO: adding the element can only actually happen when the message was sent successfully!

      clickedElement.val("");
    }
  },

  /**
   * Removes an item from the list of more items and inserts it as the last item in the chat bar list
   *
   * @param $itemToSwap {DOM Object} - the item that will be swapped
   * @returns {undefined}
   */
  swapChatBarItem: function (itemToSwapIn) {
    var $this = ogame.chat; // remove item to swap out from the chatbar

    var itemToSwapOut = $(".more_chat_bar_items").prev();
    itemToSwapOut.removeClass("open").find(".icon_close").hide().end().find(".chat_box").hide();
    itemToSwapOut.remove(); // swap item back to chatbar

    itemToSwapIn
      .addClass("open")
      .find(".icon_close")
      .show()
      .end()
      .find(".chat_box")
      .show()
      .end()
      .insertBefore(".more_chat_bar_items");
    $this.addToMoreBox([itemToSwapOut]); // if after the insertion we would not have enough space we need to correct that

    $this.updateChatBar();
    $this.updateCustomScrollbar(itemToSwapIn.find(".chat_box_ctn"));
  },

  /**
   * show the minimal chat for the clicked player on correct position
   * expand/shrink width accordingly
   *
   * @param clickedTarget {DOM Object} - clicked element
   * @param originalTarget {DOM Object} - element that was originally targeted
   * @returns {undefined}
   */
  toggleChatBox: function (clickedTarget, originalTarget) {
    var $this = ogame.chat; // some elements should not trigger toggle:

    if (clickedTarget.parents(".chat_box").length && !clickedTarget.hasClass("icon_close")) {
      return;
    }

    var chatBox = originalTarget.children(".chat_box"); // toggling the display of chat contents

    if (chatBox.is(":visible")) {
      chatBox.hide();
      originalTarget.removeClass("open");
    } else {
      if (!originalTarget.hasClass("more_chat_bar_items")) {
        originalTarget.addClass("open");
        $this.updateChatBar();
      }

      chatBox.show();
      var chatboxClass = chatBox.find(".chat_box_ctn");

      if (originalTarget.hasClass("more_chat_bar_items")) {
        chatboxClass = chatBox;
      }

      $this.updateCustomScrollbar(chatboxClass);
      chatBox.find("textarea").focus();
    }

    ogame.messagecounter.resetCounterByType(ogame.messagecounter.type_chat);
  },

  /**
   * move item to the morelist
   *
   * @see important for ogame.chat.updateChatBar
   * @param chatOpenLength, chatClosedLength, widthOpen, widthClosed, widthMoreItems, widthWindow @see ogame.chat.updateChatBar
   */
  handleTooMuchWindows: function (
    chatOpenLength,
    chatClosedLength,
    widthOpen,
    widthClosed,
    widthMoreItems,
    widthWindow,
  ) {
    var $this = ogame.chat;
    var isTooWide = true;
    var chatbarListItems = [];
    $($(".chat_bar_list > .chat_bar_list_item").get().reverse()).each(function () {
      var listItem = $(this);

      if (isTooWide) {
        // these element should never be removed:
        if (listItem.hasClass("more_chat_bar_items") || listItem.attr("id") === "chatBarPlayerList") {
          return;
        } // decide what kind of element shall be removed (swapped out)

        if (listItem.hasClass("open")) {
          chatOpenLength--;
        } else {
          chatClosedLength--;
        } // prepare the item and add it to the more list

        listItem.removeClass("open").find(".icon_close").hide().end().find(".chat_box").hide();
        chatbarListItems.push(listItem);
        listItem.remove(); // check if more elements need to be removed

        widthTotal = widthClosed * chatClosedLength + widthOpen * chatOpenLength + widthMoreItems;
        isTooWide = widthTotal >= widthWindow ? true : false;
      }
    }); // update morebox with contents of chatbarListItems

    $this.addToMoreBox(chatbarListItems);
  },

  /**
   * move item back to the chatbar
   *
   * @see important for ogame.chat.updateChatBar
   */
  getItemFromMorelist2Chatbar: function () {
    // get item from more list then remove it from more list.
    var swappedChatBarItem = $(".more_items .chat_bar_list_item").first().remove();
    var $this = ogame.chat; // add swapped item back to chat bar

    swappedChatBarItem
      .addClass("open")
      .find(".icon_close")
      .show()
      .end()
      .find(".chat_box")
      .show()
      .end()
      .insertBefore(".more_chat_bar_items"); // if more list is now empty remove it as well

    if ($(".more_items .chat_bar_list_item").length <= 0) {
      $(".more_chat_bar_items").remove();
    }

    $this.updateCustomScrollbar($(".more_chat_bar_items>.chat_box"));
    $this.updateCustomScrollbar(swappedChatBarItem.find(".chat_box_ctn"));
  },

  /**
   * Calculates available horizontal space and updates how many items are shown in the chatbar
   * and moves items to/from more list accordingly
   *
   * @param {DOM Object} chatBarItem - new item to add to chatbar
   */
  updateChatBar: function (chatBarItem) {
    var $this = ogame.chat;
    var chatOpenLength = $(".chat_bar_list > .chat_bar_list_item.open").length;
    var moreItemsLength = $(".more_chat_bar_items").length;
    var chatClosedLength = $(".chat_bar_list").children().length - chatOpenLength - moreItemsLength;
    var widthClosed = 190;
    var widthOpen = 270;
    var widthMoreItems = 190; // 180 + 10 px space

    var widthWindow = $("body").innerWidth(); // new items will be directly open

    if (chatBarItem) {
      chatOpenLength++;
    }

    var widthTotal = widthClosed * chatClosedLength + widthOpen * chatOpenLength + widthMoreItems * moreItemsLength; // if there are more elements than fit in the window

    if (widthTotal >= widthWindow) {
      $this.handleTooMuchWindows(chatOpenLength, chatClosedLength, widthOpen, widthClosed, widthMoreItems, widthWindow);
    } else if (widthTotal + widthOpen <= widthWindow && $(".more_chat_bar_items").length > 0) {
      // if widthTotal is small enough that another open element will fit an we have one in the more list
      $this.getItemFromMorelist2Chatbar();
    }

    if (chatBarItem) {
      chatBarItem.insertAfter("#chatBarPlayerList");
      $this.updateCustomScrollbar(chatBarItem.find(".chat_box_ctn"));
    }
  },

  /**
   * update the custom scrollbar or add it if the element has none
   *
   * @param element {DOM Object} - the element that gets the scrollbar
   * @param skipScrolling bool Skip scrolling to the bottom
   */
  updateCustomScrollbar: function (element, skipScrolling) {
    if (!element || element.length == 0) {
      return;
    }

    if (element.hasClass("mCustomScrollbar")) {
      element.mCustomScrollbar("update");
    } else {
      element.mCustomScrollbar({
        theme: "ogame",
      });
    }

    if (skipScrolling !== true) {
      element.mCustomScrollbar("scrollTo", "bottom", {
        scrollInertia: 0,
      });
    }

    element.each(function () {
      if ($(this).height() + "px" == $(this).css("max-height")) {
        $(this).addClass("scrollbarPresent");
      }
    });
  },
  updateVisibleState: function () {
    var visibleChats = {
      chatbar: false,
      players: [],
      associations: [],
    };
    $(".chat_bar_list>.chat_bar_list_item").each(function () {
      var $item = $(this);

      if ($item.attr("id") === "chatBarPlayerList" && $item.children(".cb_playerlist_box").is(":visible")) {
        visibleChats.chatbar = true;
      } else if ($item.data("playerid") && $item.children(".chat_box").is(":visible")) {
        visibleChats.players.push($item.data("playerid"));
      } else if ($item.data("associationid") && $item.children(".chat_box").is(":visible")) {
        visibleChats.associations.push($item.data("associationid"));
      }
    });
    $.cookie("visibleChats", JSON.stringify(visibleChats), {
      expires: 7,
    });
  },
  showPlayerList: function (selector) {
    var $this = ogame.chat;

    if (window.deactivateChatBecauseOfLogout) {
      return;
    }

    if ($.inArray(selector, $this.playerListSelector) === -1) {
      $this.playerListSelector.push(selector);
    }

    if ($this.isLoadingPlayerList === false && $this.playerList === null) {
      $this.isLoadingPlayerList = true;
      $.ajax({
        url: "/buddies/online",
        type: "GET",
        dataType: "json",
        success: function (response) {
          // Build the HTML for the player list matching original game structure
          var html = '<div class="js_playerlist pl_container contentbox fleft">';
          html += '<h2 class="header"><span class="c-right"></span><span class="c-left"></span>Player list</h2>';
          html += '<div class="content">';

          // Buddies section
          html += '<div class="playerlist_box js_accordion ui-accordion ui-widget ui-helper-reset" role="tablist">';
          html +=
            '<h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-header-active ui-state-active ui-accordion-icons" role="tab">';
          html += '<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>Buddies</h3>';
          html +=
            '<div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" role="tabpanel">';
          html += '<div class="playerlist_top_box"></div>';
          html += '<div class="scrollContainer"><ul class="playerlist">';

          if (response.success && response.buddies && response.buddies.length > 0) {
            response.buddies.forEach(function (buddy, index) {
              // TODO: Clicking on a buddy should open a chat window with that player
              var statusClass = buddy.isOnline ? "online" : "offline";
              var statusTitle = buddy.isOnline ? "online" : "offline";

              html +=
                '<li class="playerlist_item ' + (index % 2 === 0 ? "" : "odd") + '" data-playerid="' + buddy.id + '">';
              html += '<p class="playername">';
              html +=
                '<span class="playerstatus tooltip ' +
                statusClass +
                '" data-tooltip-title="' +
                statusTitle +
                '"></span>';
              html += buddy.username + "</p>";
              html +=
                '<span class="new_msg_count noMessage" data-playerid="' + buddy.id + '" data-new-messages="0">0</span>';
              html += '<span class="chatstatus cs_active fright"></span>';
              html += "</li>";
            });
          } else {
            html += '<li class="no_buddies">No buddies</li>';
          }

          html += "</ul></div></div></div>";

          // TODO: Alliance section - implement alliance chat and member list
          html += '<div class="playerlist_box js_accordion ui-accordion ui-widget ui-helper-reset" role="tablist">';
          html += '<h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-icons" role="tab">';
          html += '<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span>Alliance</h3>';
          html +=
            '<div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content" role="tabpanel" style="display: none;">';
          html += '<div class="playerlist_top_box"></div>';
          html += '<div class="scrollContainer"><ul class="playerlist">';
          html += '<li class="no_buddies">Alliance chat not yet implemented</li>';
          html += "</ul></div></div></div>";

          // TODO: Strangers section - implement strangers/other players list
          html += '<div class="playerlist_box js_accordion ui-accordion ui-widget ui-helper-reset" role="tablist">';
          html += '<h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-icons" role="tab">';
          html += '<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span>Strangers</h3>';
          html +=
            '<div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content" role="tabpanel" style="display: none;">';
          html += '<div class="playerlist_top_box"></div>';
          html += '<div class="scrollContainer"><ul class="playerlist">';
          html += '<li class="no_buddies">Strangers list not yet implemented</li>';
          html += "</ul></div></div></div>";

          html += "</div>";
          html += '<div class="footer"><div class="c-right"></div><div class="c-left"></div></div>';
          html += "</div>";

          // IMPORTANT: Always set playerList so initChatBar() can be called
          $this.playerList = html;
          $this.isLoadingPlayerList = false;
          $this._showPlayerList();
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("showPlayerList() - Error loading buddies:", textStatus, errorThrown);
          $this.isLoadingPlayerList = false;
          $this.playerList = '<div class="content"><p>Error loading buddies</p></div>';
          $this._showPlayerList();
        },
      });
    } else {
      $this._showPlayerList();
    }
  },
  _showPlayerList: function () {
    var $this = ogame.chat;
    $.each($this.playerListSelector, function (index, value) {
      $(value).html($this.playerList);
    });
  },
};
let characterClassArr = ["neutral", "miner", "warrior", "explorer"];
let allianceClassArr = ["neutral", "warrior", "trader", "explorer"];
let characterClassBonuses = {
  warrior: {
    109: 2,
    110: 2,
    111: 2,
  },
};
let allianceClassBonuses = {
  warrior: {
    109: 1,
    110: 1,
    111: 1,
  },
};
