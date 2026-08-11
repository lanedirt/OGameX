ogame.messagemarker = {
  type_chatbar: 10,
  type_chattab: 11,
  action_remove: 20,
  action_add: 21,
  currentCount: "",
  currentSelector: "",
  currentPlayernameObject: "",
  currentListPlayernameObject: "",
  currentPartnerId: "",
  currentListItemSelector: "",
  totalNewMessages: 0,
  playerlist: new Array(),
  newsInitialized: false,
  effect: "highlight",
  initialize: function () {
    $(".new_msg_count[data-playerid]").each(function () {
      var $this = ogame.messagemarker;
      var playerId = $(this).data("playerid");

      if (playerId && $.inArray(playerId, $this.playerlist) === -1) {
        $this.playerlist.push(playerId);
        $this.setPartnerId(playerId);
        $this.updateNewMarker();
      }
    }); //define effect to indicate new messages

    ogame.messagemarker.effect = "highlight"; //        $('.new_msg_count.news').each(function() {
    //            var $this = ogame.messagemarker;
    //
    //            if (!$this.newsInitialized) {
    //                var $this = ogame.messagemarker;
    //                $this.newsInitialized = true;
    //                $this.setPartnerId('News');
    ////                $this.updateNewMarker();
    //            }
    //        });
  },
  initMarker: function (playerids) {
    var $this = ogame.messagemarker;
    var chatCount = 0;
    $.each(playerids, function (index, value) {
      $this.setPartnerId(value);
      var messageCount = $('.new_msg_count[data-playerid="' + value + '"]').data("new-messages");

      if (messageCount != null && messageCount > 0) {
        $this.setSelectorByType($this.type_chatbar);
        $this.mark($this.currentSelector, $this.currentPlayernameObject, messageCount);
        $this.mark($this.currentListItemSelector, $this.currentListPlayernameObject, messageCount);
        $this.setSelectorByType($this.type_chattab);
        $this.mark($this.currentSelector, $this.currentPlayernameObject, messageCount);
      }

      chatCount = chatCount + 1;
    });
    return chatCount;
  },
  setCounter: function (partnerId, counter) {
    this.setPartnerId(partnerId);
    $('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]').data("new-messages", counter);
    this.updateNewMarker();
  },
  toggle: function (action, type, partnerId, currentState) {
    this.setPartnerId(partnerId);
    this.currentCount = parseInt(
      $('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]').data("new-messages"),
    ); //        this.setSelectorByType(type);

    if (action === this.action_add) {
      //            var newState = parseInt(currentState) + 1;
      //            this.currentCount = this.currentCount + 1;
      //            $.cookie('messageCount' + this.currentPartnerId, this.currentCount);
      this.updateNewMarker(); //            this.addNewMarker();
    }

    if (action === this.action_remove) {
      this.removeNewMarker();
    }
  },
  mark: function (selector, playerObject, count) {
    //        var htmlmarker = '<span class="newMsgMarker"><b><span>( <span class="newMsgCount">'+count+'</span> )</span></b></span>';
    //        $(selector).append(htmlmarker);
    //        playerObject.css('font-weight', 'bold');
    $('.playerlist_item[data-playerid="' + this.currentPartnerId + '"] .playername').css("font-weight", "bold");
    $('.cb_playername[data-playerid="' + this.currentPartnerId + '"]').css("font-weight", "bold");
  },
  addNewMarker: function () {
    var added = false;

    if (!$(this.currentSelector).find(".newMsgMarker").length) {
      this.mark(this.currentSelector, this.currentPlayernameObject, this.currentCount);
      added = true;
    }

    if (!$(this.currentListItemSelector).find(".newMsgMarker").length) {
      this.mark(this.currentListItemSelector, this.currentListPlayernameObject, this.currentCount);
      added = true;
    }

    if (!added) {
      this.updateNewMarker();
    }
  },
  removeNewMarker: function () {
    $('.playerlist_item[data-playerid="' + this.currentPartnerId + '"] .playername').css("font-weight", "normal");
    $('.cb_playername[data-playerid="' + this.currentPartnerId + '"]').css("font-weight", "normal"); //        $(this.currentSelector).find('.newMsgMarker').remove();
    //        $(this.currentListItemSelector).find('.newMsgMarker').remove();
  },
  updateNewMarker: function () {
    //        var newMarker = $(this.currentSelector).find('.newMsgCount');
    //        var newlistMarker = $(this.currentListItemSelector).find('.newMsgCount');
    //        var currentValue = parseInt(newMarker.html());
    //        newMarker.html(currentValue+1);
    //        newlistMarker.html(currentValue+1);
    //        $(this.currentSelector + ' .new_msg_count').text(currentValue+1).effect('highlight', {}, 500);
    //        $(this.currentListItemSelector + ' .new_msg_count').text(currentValue+1).effect('highlight', {}, 500);
    var newAmount = parseInt($('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]').data("new-messages"));
    var sumNewMessagesChatBefore = $(".new_msg_count.totalChatMessages").text();
    var sumNewMessagesChat = ogame.chat.updateTotalNewChatCounter();

    if (newAmount === 0) {
      if (isNaN(this.currentPartnerId)) {
        $(".new_msg_count.totalMessages.news").text(newAmount).addClass("noMessage");
      } else {
        $('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]')
          .text(newAmount)
          .addClass("noMessage");

        if (sumNewMessagesChat === 0) {
          $(".new_msg_count.totalChatMessages").text(sumNewMessagesChat).addClass("noMessage");
        } else {
          if (sumNewMessagesChatBefore != sumNewMessagesChat) {
            $(".new_msg_count.totalChatMessages")
              .text(sumNewMessagesChat)
              .removeClass("noMessage")
              .effect(ogame.messagemarker.effect, {}, 500);
          }
        }
      }
    } else {
      if (isNaN(this.currentPartnerId)) {
        if (isNaN(newAmount)) {
          $(".new_msg_count.totalMessages.news").text(0).addClass("noMessage");
        } else {
          $(".new_msg_count.totalMessages.news")
            .text(newAmount)
            .removeClass("noMessage")
            .effect(ogame.messagemarker.effect, {}, 500);
        }
      } else {
        $('.msg[data-playerid="' + this.currentPartnerId + '"]').addClass("msg_new");
        $('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]')
          .text(newAmount)
          .removeClass("noMessage")
          .effect(ogame.messagemarker.effect, {}, 500);

        if (sumNewMessagesChatBefore != sumNewMessagesChat) {
          $(".new_msg_count.totalChatMessages")
            .text(sumNewMessagesChat)
            .removeClass("noMessage")
            .effect(ogame.messagemarker.effect, {}, 500);
        }
      }
    } //        $('.new_msg_count.totalChatMessages').text(this.totalNewMessages).effect('highlight', {}, 500);
  },
  setSelectorByType: function (type) {
    selector = "";

    if (type == this.type_chatbar) {
      selector = 'ul.chat_bar_list li.chat_bar_list_item[data-playerid="' + this.currentPartnerId + '"]'; //evtl muss hier noch um die id gänsefüsschen
    }

    if (type == this.type_chattab) {
      selector = 'ul#chatMsgList li.msg[data-playerid="' + this.currentPartnerId + '"]'; //evtl muss hier noch um die id gänsefüsschen
    }

    this.currentListItemSelector =
      '.js_playerlist ul.playerlist li.playerlist_item[data-playerid="' + this.currentPartnerId + '"]';
    this.currentSelector = selector;
    this.currentPlayernameObject = $(selector).find(".cb_playername");
    this.currentListPlayernameObject = $(this.currentListItemSelector).find(".playername");
  },
  setPartnerId: function (partnerId) {
    this.currentPartnerId = partnerId;
  },
};
