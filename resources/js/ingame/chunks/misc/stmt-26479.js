ogame.messagecounter = {
  countData: {
    chat: 0,
    messages: 0,
    buddy: 0,
  },
  newChats: Array(),
  type_chat: 10,
  type_message: 11,
  type_buddy: 12,
  currentLinkSelector: null,
  currentType: 0,
  currentPlayer: null,
  sumNewChatMessages: 0,
  initialize: function (type, player) {
    var $this = ogame.messagecounter;

    if (typeof player == "undefined" && type !== $this.type_chat) {
      $this.currentPlayer = 0;
    }

    if (typeof player == "undefined" && type == $this.type_chat) {
      return false;
    }

    if (typeof player !== "undefined") {
      $this.currentPlayer = player;
    }

    $this.currentType = type;

    switch (type) {
      case $this.type_chat:
        $this.currentLinkSelector = $("a.comm_menu.chat");
        break;

      case $this.type_message:
        $this.currentLinkSelector = $("a.comm_menu.messages");
        break;

      case $this.type_buddy:
        $this.currentLinkSelector = $("a.comm_menu.buddies");
        break;

      default:
        return false;
    }

    $this.update();
  },
  initChatCounter: function (counter) {
    var $this = ogame.messagecounter;
    $this.currentLinkSelector = $("a.comm_menu.chat");
    $this.currentType = $this.type_chat;
    $this.setCount(counter); //        if(counter > 0) {

    $this.update(); //        }
  },

  /**
   * Update Count of new Chatmessages
   */
  update: function (locakey) {
    var $this = ogame.messagecounter; //        $this.updateCountData();

    /*
    if ($this.shouldAddCounter() && $this.getCount() > 0) {
        $this.setNewCounter($this.currentLinkSelector, $this.getCounterHtml($this.getCount()));
    } else {
        if ($this.getCount() == 0) {
            $this.resetCounterByType($this.currentType)
        } else {
            $this.setNewCounter($this.getCountSelectorByType($this.currentType), $this.getCount());
        }
    } */

    var loca;

    if (locakey === undefined) {
      loca = chatLoca.X_NEW_CHATS;
    } else {
      loca = locakey;
    }

    changeTooltip($this.currentLinkSelector, loca.replace("#+#", $this.getCount()));
  },
  resetCounterByType: function (type, locakey) {
    var $this = ogame.messagecounter;
    var selector = $this.getIconSelectorByType(type); //        $this.setNewCounter(selector, '');

    var loca;

    if (locakey === undefined) {
      loca = ""; //chatLoca.X_NEW_CHATS;
    } else {
      loca = locakey;
    }

    changeTooltip(selector, loca.replace("#+#", 0));
  },

  /**
   * Get the Selector of the right counterbox
   *
   * @param int type -> right codes see@top
   *
   * @return jquery-object unreadSelector
   */
  getCountSelectorByType: function (type) {
    var $this = ogame.messagecounter;
    var unreadSelector = "";

    switch (type) {
      case $this.type_chat:
        unreadSelector = $("a.comm_menu.chat .new_msg_count");
        break;

      case $this.type_message:
        unreadSelector = $("a.comm_menu.messages .new_msg_count");
        break;

      case $this.type_buddy:
        unreadSelector = $("a.comm_menu.buddies .new_msg_count");
    }

    return unreadSelector;
  },

  /**
   * Get the Selector of the right icon
   *
   * @param int type -> right codes see@top
   *
   * @return jquery-object unreadSelector
   */
  getIconSelectorByType: function (type) {
    var $this = ogame.messagecounter;
    var selector = "";

    switch (type) {
      case $this.type_chat:
        selector = $("a.comm_menu.chat");
        break;

      case $this.type_message:
        selector = $("a.comm_menu.messages");
        break;

      case $this.type_buddy:
        selector = $("a.comm_menu.buddies");
    }

    return selector;
  },

  /**
   * Get the Html-String to create a counter
   *
   * @param string count
   *
   * @return string counter
   */
  getCounterHtml: function (count) {
    var counter = '<span class="new_msg_count">' + count + "</span>";
    return counter;
  },

  /**
   * Get count from type
   *
   * @return int $this.countData.[type]
   */
  getCount: function () {
    var $this = ogame.messagecounter;

    switch ($this.currentType) {
      case $this.type_chat:
        return $this.countData.chat;

      case $this.type_message:
        return $this.countData.messages;

      case $this.type_buddy:
        return $this.countData.buddy;
    }
  },

  /**
   * Set count for type
   *
   * @param mixed value
   */
  setCount: function (value) {
    var $this = ogame.messagecounter;

    switch ($this.currentType) {
      case $this.type_chat:
        $this.countData.chat = value;
        break;

      case $this.type_message:
        $this.countData.messages = value;
        break;

      case $this.type_buddy:
        $this.countData.buddy = value;
        break;
    }
  },

  /**
   * Update Count for type
   */
  updateCountData: function () {
    var $this = ogame.messagecounter;

    if ($this.isOpen()) {
      $this.setCount(0);
    } else {
      if ($this.shouldAddCounter()) {
        var unreadMessages = 1;
      } else {
        var unreadMessagesSelector = $this.getCountSelectorByType($this.currentType);
        var unreadMessages = unreadMessagesSelector.html();
        unreadMessages = parseInt(unreadMessages) + 1;
      }

      $this.setCount(unreadMessages);
    }
  },

  /**
   * Returns if u should add the counter first
   *
   * @return boolean addCounter
   */
  shouldAddCounter: function () {
    var $this = ogame.messagecounter;
    var unreadMessagesSelector = $this.getCountSelectorByType($this.currentType);
    var unreadMessages = unreadMessagesSelector.html();
    var addCounter = false;

    if (typeof unreadMessages == "undefined") {
      addCounter = true;
    }

    return addCounter;
  },

  /**
   * Set counter for new chatmessages
   *
   * @param jquery-object selector
   * @param string html    >> html for the counter on the chatsymbole
   */
  setNewCounter: function (selector, html) {
    selector.html(html);
  },

  /**
   * proof if the chat of the player is open
   */
  isOpen: function () {
    var $this = ogame.messagecounter;
    var returnValue = false;

    switch ($this.currentType) {
      case $this.type_chat:
        returnValue = ogame.chat.isOpen($this.currentPlayer); //                $('.chat_box').each(function () {
        //                    if ($(this).attr('data-playerid') == $this.currentPlayer) {
        //                        if ($(this).css('display') == 'block') {
        //                            returnValue = true;
        //                        }
        //                    }
        //                });

        break;

      case $this.type_message:
        returnValue = location.href.indexOf("page=messages") > -1;
        break;

      case $this.type_buddy:
        returnValue = location.href.indexOf("page=ingame&component=buddies") > -1;
        break;
    }

    return returnValue;
  },
};
