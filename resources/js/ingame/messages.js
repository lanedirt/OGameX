ogame.messagecounter = {
    countData: {chat: 0, messages: 0, buddy: 0},
    newChats: Array(),
    type_chat: 10,
    type_message: 11,
    type_buddy: 12,
    currentLinkSelector: null,
    currentType: 0,
    currentPlayer: null,
    sumNewChatMessages: 0,
    initialize: function (d, e) {
        var f = ogame.messagecounter;
        if (typeof e == "undefined" && d !== f.type_chat) {
            f.currentPlayer = 0
        }
        if (typeof e == "undefined" && d == f.type_chat) {
            return false
        }
        if (typeof e !== "undefined") {
            f.currentPlayer = e
        }
        f.currentType = d;
        switch (d) {
            case f.type_chat:
                f.currentLinkSelector = $("a.comm_menu.chat");
                break;
            case f.type_message:
                f.currentLinkSelector = $("a.comm_menu.messages");
                break;
            case f.type_buddy:
                f.currentLinkSelector = $("a.comm_menu.buddies");
                break;
            default:
                return false
        }
        f.update()
    },
    initChatCounter: function (d) {
        var c = ogame.messagecounter;
        c.currentLinkSelector = $("a.comm_menu.chat");
        c.currentType = c.type_chat;
        c.setCount(d);
        c.update()
    },
    update: function (f) {
        var d = ogame.messagecounter;
        var e;
        if (f === undefined) {
            e = chatLoca.X_NEW_CHATS
        } else {
            e = f
        }
        changeTooltip(d.currentLinkSelector, e.replace("#+#", d.getCount()))
    },
    resetCounterByType: function (l, h) {
        var k = ogame.messagecounter;
        var g = k.getIconSelectorByType(l);
        var f;
        if (h === undefined) {
            f = chatLoca.X_NEW_CHATS
        } else {
            f = h
        }
        changeTooltip(g, f.replace("#+#", 0))
    },
    getCountSelectorByType: function (e) {
        var f = ogame.messagecounter;
        var d = "";
        switch (e) {
            case f.type_chat:
                d = $("a.comm_menu.chat .new_msg_count");
                break;
            case f.type_message:
                d = $("a.comm_menu.messages .new_msg_count");
                break;
            case f.type_buddy:
                d = $("a.comm_menu.buddies .new_msg_count")
        }
        return d
    },
    getIconSelectorByType: function (d) {
        var f = ogame.messagecounter;
        var e = "";
        switch (d) {
            case f.type_chat:
                e = $("a.comm_menu.chat");
                break;
            case f.type_message:
                e = $("a.comm_menu.messages");
                break;
            case f.type_buddy:
                e = $("a.comm_menu.buddies")
        }
        return e
    },
    getCounterHtml: function (c) {
        var d = '<span class="new_msg_count">' + c + "</span>";
        return d
    },
    getCount: function () {
        var b = ogame.messagecounter;
        switch (b.currentType) {
            case b.type_chat:
                return b.countData.chat;
            case b.type_message:
                return b.countData.messages;
            case b.type_buddy:
                return b.countData.buddy
        }
    },
    setCount: function (d) {
        var c = ogame.messagecounter;
        switch (c.currentType) {
            case c.type_chat:
                c.countData.chat = d;
                break;
            case c.type_message:
                c.countData.messages = d;
                break;
            case c.type_buddy:
                c.countData.buddy = d;
                break
        }
    },
    updateCountData: function () {
        var f = ogame.messagecounter;
        if (f.isOpen()) {
            f.setCount(0)
        } else {
            if (f.shouldAddCounter()) {
                var d = 1
            } else {
                var e = f.getCountSelectorByType(f.currentType);
                var d = e.html();
                d = parseInt(d) + 1
            }
            f.setCount(d)
        }
    },
    shouldAddCounter: function () {
        var g = ogame.messagecounter;
        var e = g.getCountSelectorByType(g.currentType);
        var f = e.html();
        var h = false;
        if (typeof f == "undefined") {
            h = true
        }
        return h
    },
    setNewCounter: function (d, c) {
        d.html(c)
    },
    isOpen: function () {
        var c = ogame.messagecounter;
        var d = false;
        switch (c.currentType) {
            case c.type_chat:
                d = ogame.chat.isOpen(c.currentPlayer);
                break;
            case c.type_message:
                d = (location.href.indexOf("page=messages") > -1);
                break;
            case c.type_buddy:
                d = (location.href.indexOf("page=buddies") > -1);
                break
        }
        return d
    }
};
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
    effect: "none",
    initialize: function () {
        $(".new_msg_count[data-playerid]").each(function () {
            var c = ogame.messagemarker;
            var d = $(this).data("playerid");
            if (d && $.inArray(d, c.playerlist) === -1) {
                c.playerlist.push(d);
                c.setPartnerId(d);
                c.updateNewMarker()
            }
        });
        ogame.messagemarker.effect = "highlight"
    },
    initMarker: function (e) {
        var d = ogame.messagemarker;
        var f = 0;
        $.each(e, function (b, a) {
            d.setPartnerId(a);
            var c = $('.new_msg_count[data-playerid="' + a + '"]').data("new-messages");
            if (c != null && c > 0) {
                d.setSelectorByType(d.type_chatbar);
                d.mark(d.currentSelector, d.currentPlayernameObject, c);
                d.mark(d.currentListItemSelector, d.currentListPlayernameObject, c);
                d.setSelectorByType(d.type_chattab);
                d.mark(d.currentSelector, d.currentPlayernameObject, c)
            }
            f = f + 1
        });
        return f
    },
    setCounter: function (c, d) {
        this.setPartnerId(c);
        $('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]').data("new-messages", d);
        this.updateNewMarker()
    },
    toggle: function (h, e, g, f) {
        this.setPartnerId(g);
        this.currentCount = parseInt($('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]').data("new-messages"));
        if (h === this.action_add) {
            this.updateNewMarker()
        }
        if (h === this.action_remove) {
            this.removeNewMarker()
        }
    },
    mark: function (e, d, f) {
        $('.playerlist_item[data-playerid="' + this.currentPartnerId + '"] .playername').css("font-weight", "bold");
        $('.cb_playername[data-playerid="' + this.currentPartnerId + '"]').css("font-weight", "bold")
    },
    addNewMarker: function () {
        var b = false;
        if (!$(this.currentSelector).find(".newMsgMarker").length) {
            this.mark(this.currentSelector, this.currentPlayernameObject, this.currentCount);
            b = true
        }
        if (!$(this.currentListItemSelector).find(".newMsgMarker").length) {
            this.mark(this.currentListItemSelector, this.currentListPlayernameObject, this.currentCount);
            b = true
        }
        if (!b) {
            this.updateNewMarker()
        }
    },
    removeNewMarker: function () {
        $('.playerlist_item[data-playerid="' + this.currentPartnerId + '"] .playername').css("font-weight", "normal");
        $('.cb_playername[data-playerid="' + this.currentPartnerId + '"]').css("font-weight", "normal")
    },
    updateNewMarker: function () {
        var d = parseInt($('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]').data("new-messages"));
        var e = $(".new_msg_count.totalChatMessages").text();
        var f = ogame.chat.updateTotalNewChatCounter();
        if (d === 0) {
            if (isNaN(this.currentPartnerId)) {
                $(".new_msg_count.totalMessages.news").text(d).addClass("noMessage")
            } else {
                $('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]').text(d).addClass("noMessage");
                if (f === 0) {
                    $(".new_msg_count.totalChatMessages").text(f).addClass("noMessage")
                } else {
                    if (e != f) {
                        $(".new_msg_count.totalChatMessages").text(f).removeClass("noMessage").effect(ogame.messagemarker.effect, {}, 500)
                    }
                }
            }
        } else {
            if (isNaN(this.currentPartnerId)) {
                if (isNaN(d)) {
                    $(".new_msg_count.totalMessages.news").text(0).addClass("noMessage")
                } else {
                    $(".new_msg_count.totalMessages.news").text(d).removeClass("noMessage").effect(ogame.messagemarker.effect, {}, 500)
                }
            } else {
                $('.msg[data-playerid="' + this.currentPartnerId + '"]').addClass("msg_new");
                $('.new_msg_count[data-playerid="' + this.currentPartnerId + '"]').text(d).removeClass("noMessage").effect(ogame.messagemarker.effect, {}, 500);
                if (e != f) {
                    $(".new_msg_count.totalChatMessages").text(f).removeClass("noMessage").effect(ogame.messagemarker.effect, {}, 500)
                }
            }
        }
    },
    setSelectorByType: function (b) {
        selector = "";
        if (b == this.type_chatbar) {
            selector = 'ul.chat_bar_list li.chat_bar_list_item[data-playerid="' + this.currentPartnerId + '"]'
        }
        if (b == this.type_chattab) {
            selector = 'ul#chatMsgList li.msg[data-playerid="' + this.currentPartnerId + '"]'
        }
        this.currentListItemSelector = '.js_playerlist ul.playerlist li.playerlist_item[data-playerid="' + this.currentPartnerId + '"]';
        this.currentSelector = selector;
        this.currentPlayernameObject = $(selector).find(".cb_playername");
        this.currentListPlayernameObject = $(this.currentListItemSelector).find(".playername")
    },
    setPartnerId: function (b) {
        this.currentPartnerId = b
    }
};
ogame.messages = {
    data: {
        initActions: {
            "tabs-nfFleets": "initTabFleets",
            "tabs-nfCommunication": "initTabCommunication",
            "subtabs-nfCommunicationMessages": "initSubTabMessages"
        }
    }, addMessage: function (l, f, g) {
        if (g !== false) {
            g = true
        }
        if (l.attr("aria-selected") !== "true") {
            console.warn("addMessage: not correct Tab, aria-selected = ", l.attr("aria-selected"), l);
            return
        }
        if (!f) {
            console.warn("addMessage: msgData is ", f);
            return
        }
        var h = $("#" + l.attr("aria-controls")).find(".tab_inner");
        var k = false;
        if (g) {
            k = true
        }
        ogame.messages.createMessageItem(f, h, k)
    }, createMessageItem: function (o, l, m) {
        var p = {};
        for (var k in o) {
            var n = o[k];
            p[k] = n.msgID
        }
        var h = JSON.stringify(p);
        $.ajax({
            url: "?page=messages",
            type: "POST",
            dataType: "html",
            data: {
                messageId: h,
                tabid: this.getCurrentMessageTab(),
                action: 121,
                ajax: 1
            },
            success: function (a) {
                if (m) {
                    l.prepend(a)
                } else {
                    var b = l.find(".favoriteCount");
                    if (b.length > 0) {
                        $(a).insertBefore(b)
                    } else {
                        l.append(a)
                    }
                }
            },
            error: function (c, a, b) {
            }
        })
    }, createBroadcastMsgItem: function (f) {
        if (!f) {
            console.warn("createMessageItem: msgData is missing!");
            return undefined
        }
        var g = $('<div class="msg_head"></div>');
        g.append('<span class="msg_title blue_txt">' + f.title + "</span>");
        g.append('<span class="msg_date fright">' + f.date + "</span><br>");
        g.append('<span class="msg_sender_label">' + loca.LOCA_WRITE_MSG_FROM + ": </span>");
        g.append('<span class="msg_sender">' + f.senderName + "</span>");
        var h = $('<div class="msg_actions clearfix"></div>');
        h.append('<a class="fright txt_link overlay" href="' + f.detailURL + '" data-overlay-title="' + loca.broadcasts + '">' + loca.details + "</a>");
        h.append('<a class="fright txt_link comments_link overlay" href="' + f.commentsURL + '" data-overlay-title="' + loca.broadcasts + '">' + f.commentsCount + ' <span class="comments"></span></a></a>');
        var e = $('<li class="msg ' + f.newClass + '" data-msg-id="' + f.msgID + '"></li>');
        e.append('<div class="msg_status"></div>');
        e.append(g);
        e.append('<span class="msg_content">' + f.msgContent + "</span>");
        e.append(h);
        return e
    }, createRecipient: function (m, g, n) {
        var h, k;
        $(".input_replacement").each(function () {
            k = new RegExp($(this).data("recipient-cat"));
            if (k.test(g)) {
                h = $(this)
            }
        });
        if (h === undefined) {
            return
        }
        var l = h.children(".recipient_txt").filter(function () {
            return ($(this).data("recipient-id") === m)
        });
        if (l.length === 0) {
            if (!h.hasClass("focus")) {
                h.addClass("focus")
            }
            h.append('<div class="recipient_txt" data-recipient-id="' + m + '" data-recipient-cat="' + g + '">' + n + '<a role="button" class="remove_recipient"></a></div>')
        }
    }, doInitAction: function (b) {
        if (typeof ogame.messages[ogame.messages.data.initActions[b]] === "function") {
            return ogame.messages[ogame.messages.data.initActions[b]]()
        } else {
            return console.warn("These is no function defined for action: ", b)
        }
    }, initCombatReportDetails: function () {
        if ($("select").length > 0) {
            $("select").ogameDropDown()
        }
    }, initDetailMessages: function (c) {
        $(".detail_list_el:nth-of-type(4n + 3), .detail_list_el:nth-of-type(4n + 4)").addClass("odd");
        var d = $(window).height() - 200;
        $(".detail_msg_ctn").css("height", d);
        $(".detail_msg_ctn").mCustomScrollbar({theme: "ogame"});
        if (c) {
            $("#scrollToComments").on("click", function () {
                $(".detail_msg_ctn").mCustomScrollbar("scrollTo", "bottom")
            });
            initBBCodeEditor(locaKeys, itemNames, false, ".comment_textarea", 2000)
        }
        $("#messages ul.pagination").on("click", "li.p_li a", function () {
            var a = $(this).data("tabid");
            var b = $(this).data("messageid");
            console.log('initDetailMessages()');
            $.post("?page=messages", {
                tabid: a,
                messageId: b,
                ajax: 1
            }, function (h) {
                var g = $(h).find("#messages .ui-dialog");
                $(".overlayDiv").html(h)
            })
        })
    }, initMessages: function () {
        $(".js_tabs .tabs_btn_img").each(function () {
            if ($(this).attr("rel")) {
                $(this).attr("href", $(this).attr("rel"))
            }
        });
        ogame.messages.initTabs($(".js_tabs"));
        var b = ogame.messages.getCurrentMessageTab();
        $("#contentWrapper #buttonz div.js_tabs.tabs_wrap.ui-tabs").on("click", "ul li.list_item", function () {
            var a = ogame.messages.getCurrentMessageTab()
        });
        $("body").on("click", ".msg_actions .icon_not_favorited", function (d) {
            var a = $(this).parents("li.msg").data("msg-id") || $(this).parents("div.detail_msg").data("msg-id");
            $.ajax({
                type: "POST",
                url: "?page=messages",
                dataType: "json",
                data: {tabid: b, messageId: a, action: 101, ajax: 1},
                success: function (f) {
                    if (f[a]["result"] == true) {
                        $(d.target).removeClass("icon_not_favorited").addClass("icon_favorited");
                        changeTooltip($(d.target), loca.DELETE_FAV);
                        var c = $(".favoriteTabFreeSlotCount");
                        c.html(parseInt(c.html()) - 1)
                    } else {
                        if (f[a]["reason"] !== "undefined") {
                            fadeBox(f[a]["reason"], 1)
                        } else {
                            fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1)
                        }
                    }
                },
                error: function () {
                }
            })
        }).on("click", ".msg_actions .icon_favorited", function (d) {
            var a = $(this).parents("li.msg").data("msg-id") || $(this).parents("div.detail_msg").data("msg-id");
            $.ajax({
                type: "POST",
                url: "?page=messages",
                dataType: "json",
                data: {tabid: b, messageId: a, action: 102, ajax: 1},
                success: function (f) {
                    if (f[a]["result"] == true) {
                        $(d.target).removeClass("icon_favorited").addClass("icon_not_favorited");
                        changeTooltip($(d.target), loca.ADD_FAV);
                        var c = $(".favoriteTabFreeSlotCount");
                        c.html(parseInt(c.html()) + 1)
                    } else {
                        if (f[a]["reason"] !== "undefined") {
                            fadeBox(f[a]["reason"], 1)
                        } else {
                            fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1)
                        }
                    }
                },
                error: function () {
                }
            })
        }).on("click", ".js_actionKill", function (d) {
            var a = $(this).parents("li.msg").data("msg-id");
            console.log(a);
            console.log('this is the message id');
            $.ajax({
                type: "POST",
                url: "", // Self
                dataType: "json",
                data: {messageId: a, action: 103, ajax: 1},
                success: function (c) {
                    if (c[a] == true) {
                        $(d.target).parents("li.msg").remove()
                    } else {
                        fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1)
                    }
                },
                error: function () {
                }
            })
        }).on("click", ".js_actionKillAll", function (a) {
            console.log(ogame.messages.getCurrentMessageTab());
            return;
            $.ajax({
                type: "POST",
                url: "", // Self
                dataType: "json",
                data: {
                    tabid: ogame.messages.getCurrentMessageTab(),
                    messageId: -1,
                    action: 103,
                    ajax: 1
                },
                success: function (d) {
                    if (d.result == true) {
                        location.reload()
                    } else {
                        fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1)
                    }
                },
                error: function () {
                }
            })
        }).on("click", ".js_actionKillDetail", function (d) {
            var a = $(".overlayDiv .detail_msg").data("msg-id");
            $.ajax({
                type: "POST",
                url: "?page=messages",
                dataType: "json",
                data: {messageId: a, action: 103, ajax: 1},
                success: function (c) {
                    if (c[a] == true) {
                        location.reload()
                    } else {
                        fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1)
                    }
                },
                error: function () {
                }
            })
        }).on("click", ".js_actionRevive", function (d) {
            var a = $(this).parents("li.msg").data("msg-id");
            if (a === undefined) {
                a = $(this).parents("div.detail_msg").data("msg-id")
            }
            $.ajax({
                type: "POST",
                url: "?page=messages",
                dataType: "json",
                data: {tabid: b, messageId: a, action: 104, ajax: 1},
                success: function (c) {
                    if (c[a] == true) {
                        $(d.target).parents("li.msg").remove();
                        $(d.target).parents("div.ui-dialog").remove();
                        $("li.msg[data-msg-id=" + a + "]").remove()
                    } else {
                        fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1)
                    }
                },
                error: function () {
                }
            })
        }).on("click", ".js_actionReviveAll", function (a) {
            $.ajax({
                type: "POST",
                url: "?page=messages",
                dataType: "json",
                data: {
                    tabid: ogame.messages.getCurrentMessageTab(),
                    messageId: -1,
                    action: 104,
                    ajax: 1
                },
                success: function (d) {
                    if (d.result == true) {
                        location.reload()
                    } else {
                        fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1)
                    }
                },
                error: function () {
                }
            })
        }).on("click", ".js_actionDelete", function (d) {
            var a = $(this).parents("li.msg").data("msg-id");
            $.ajax({
                type: "POST",
                url: "?page=messages",
                dataType: "json",
                data: {tabid: b, messageId: a, action: 105, ajax: 1},
                success: function (c) {
                    if (c[a] == true) {
                        $(d.target).parents("li.msg").remove()
                    } else {
                        fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1)
                    }
                },
                error: function () {
                }
            })
        }).on("click", ".js_actionDeleteAll", function (d) {
            var a = $(this).parents("li.msg").data("msg-id");
            $.ajax({
                type: "POST",
                url: "?page=messages",
                dataType: "json",
                data: {
                    tabid: ogame.messages.getCurrentMessageTab(),
                    messageId: -1,
                    action: 105,
                    ajax: 1
                },
                success: function (c) {
                    if (c.result == true) {
                        location.reload()
                    } else {
                        fadeBox(loca.LOCA_GALAXY_ERROR_OCCURED, 1)
                    }
                },
                error: function () {
                }
            })
        }).on("click", ".paginator", function (h) {
            var f = $(this).data("tab");
            var g = $(this).data("page");
            var a = $(this).closest('div[class^="ui-tabs-panel"]');
            $.ajax({
                type: "POST",
                url: "?page=messages",
                dataType: "html",
                data: {
                    messageId: -1,
                    tabid: f,
                    action: 107,
                    pagination: g,
                    ajax: 1
                },
                success: function (c) {
                    a.html(c)
                },
                error: function () {
                }
            })
        }).on("click", ".jumpToAllianceApplications", function (a) {
            location.href = "index.php?page=alliance&tab=applications"
        })
    }, selectCurrentMessageTab: function () {
        var b = $(".subtabs .ui-state-active:visible");
        if (!b.length) {
            b = $(".js_tabs .ui-state-active:visible")
        }
        return b
    }, getCurrentMessageTab: function () {
        var b = $(".subtabs .ui-state-active:visible").attr("data-tabid");
        if (!b) {
            b = $(".js_tabs .ui-state-active:visible").attr("data-tabid")
        }
        return b
    }, getCurrentEarliestMessage: function () {
        return $(".ui-tabs-panel .tab_inner .msg:visible").last().attr("data-msg-id")
    }, initCommentForm: function () {
        ogame.messages.initWriteNewMsgBox($("#newCommentForm"));
        $("#newCommentForm").on("click", ".js_send_comment", function (d) {
            d.preventDefault();
            var f = $(this).closest("form");
            var e = f.find("input[name=messageId]").val();
            $.ajax({
                type: "POST",
                url: f.attr("action"),
                dataType: "json",
                data: {
                    messageId: e,
                    ajax: 1,
                    action: 108,
                    text: f.find("textarea[name=text]").val()
                },
                success: function (a) {
                    fadeBox(a.message, a.error);
                    f.find("textarea[name=text]").val("");
                    f.parent().after('<li class="msg"><div class="msg_status"></div><div class="msg_head">' + a.commentheader + '</div><div class="msg_content">' + a.commentcontent + "</div></li>");
                    $("#scrollToComments").text(a.commentcount)
                },
                error: function (a) {
                }
            })
        })
    }, initShareReportOverlay: function () {
        ogame.messages.initWriteNewMsgBox($("#newSharedReportForm"));
        $("#newSharedReportForm").on("click", ".js_send_msg_share", function (e) {
            e.preventDefault();
            var l = $(this).closest("form");
            var g = l.find("input[name=messageId]").val();
            var h = l.find("li.select2-selection__choice");
            var k = [];
            h.each(function () {
                k.push($(this).attr("title"))
            });
            $.ajax({
                type: "POST",
                url: l.attr("action"),
                dataType: "json",
                data: {
                    messageId: g,
                    empfaenger: k,
                    ajax: 1,
                    action: 106,
                    text: l.find("textarea[name=text]").val()
                },
                success: function (a) {
                    fadeBox(a.message, a.error);
                    l.closest('div[class^="overlayDiv"').remove()
                },
                error: function (a) {
                }
            })
        })
    }, initSubTabMessages: function () {
        $(".js_accordion").accordion({
            collapsible: true,
            heightStyle: "content",
            active: false
        });
        ogame.messages.initWriteNewMsgBox($("#newMsgForm"));
        $("html").off(".subtabmessages");
        $("#newMsgForm").on("click.subtabmessages", ".js_send_msg", function (e) {
            e.preventDefault();
            var f = $(this).parents("form");
            var d = {};
            $(".input_replacement").children().each(function () {
                if (typeof d[$(this).data("recipient-cat")] == "undefined") {
                    d[$(this).data("recipient-cat")] = []
                }
                d[$(this).data("recipient-cat")].push($(this).data("recipient-id"))
            });
            $.ajax({
                type: "POST",
                url: f.attr("action"),
                dataType: "json",
                data: {empfaenger: d, text: f.find(".new_msg_textarea").val()},
                success: function (a) {
                    fadeBox(a.message, a.error);
                    if (!a.error) {
                        ogame.messages.sendSubtabMsg($(".new_msg_textarea").val(), d)
                    }
                },
                error: function () {
                }
            })
        })
    }, initTabCommunication: function () {
        console.info("-------------initTabCommunication");
        ogame.messages.initTabs($(".js_subtabs_communication"))
    }, initTabFleets: function () {
        console.info("-------------initTabFleets");
        ogame.messages.initTabs($(".js_subtabs_fleets"), ogame.messages.initTrash)
    }, initTabs: function (c, d) {
        c.tabs({
            beforeLoad: function () {
                $(".ajax_load_shadow").show()
            }, load: function (a, b) {
                console.info("load", b.tab.attr("id"));
                ogame.messages.doInitAction(b.tab.attr("id"));
                $(".ajax_load_shadow").hide();
                if (typeof d === "function") {
                    d(b.tab)
                }
            }, create: function (a, b) {
                ogame.messages.doInitAction(b.tab.attr("id"))
            }
        })
    }, initTrash: function (b) {
        if (!b) {
            return
        }
        $(".js_active_tab").html(b.data("subtabname"));
        if (b.attr("id") === "subtabs-nfFleetTrash") {
            $(".trash_box").addClass("trash_open");
            $(".in_trash").show();
            $(".not_in_trash").hide()
        } else {
            $(".trash_box").removeClass("trash_open");
            $(".in_trash").hide();
            $(".not_in_trash").show()
        }
    }, initWriteNewMsgBox: function (b) {
        initBBCodeEditor(locaKeys, itemNames, false, ".new_msg_textarea", 2000);
        $("html").off(".writeNewMsgBox");
        $("html").on("click.writeNewMsgBox", function (a) {
            if ($(".new_msg_label").hasClass("open") && $(a.target).parents(".recipient_select_box").length < 1) {
                $(".input_replacement").removeClass("focus");
                $(".new_msg_label").removeClass("open");
                $(".new_msg_label").siblings(".recipient_select_box").hide()
            }
        });
        b.on("click.writeNewMsgBox", ".input_replacement", function (a) {
            a.stopPropagation();
            ogame.messages.toggleRecipientSelectBox($(a.target).data("recipient-cat"))
        }).on("click.writeNewMsgBox", ".new_msg_label", function (a) {
            a.stopPropagation();
            ogame.messages.toggleRecipientSelectBox($(a.currentTarget).data("recipient-cat"))
        }).on("click.writeNewMsgBox", ".recipient_select_box .ally_rank", function () {
            ogame.messages.toggleRecipientSelection($(this))
        }).on("click.writeNewMsgBox", ".remove_recipient", function () {
            ogame.messages.removeRecipient($(this).closest(".recipient_txt").data("recipient-id"))
        })
    }, removeRecipient: function (b) {
        $(".ally_rank").filter(function () {
            if ($(this).data("recipient-id") === b) {
                $(this).removeClass("selected")
            }
        });
        $(".recipient_txt").filter(function () {
            if ($(this).data("recipient-id") === b) {
                $(this).remove()
            }
        })
    }, sendSubtabMsg: function (g, h) {
        if (!g) {
            console.warn("sendSubtabMsg: msg was empty");
            return
        }
        if (!h) {
            console.warn("sendSubtabMsg: msg had no recipients");
            return
        }
        var f = {};
        f.date = getFormatedDate(serverTime.getTime(), "[d].[m].[Y] <span>[H]:[i]:[s]</span>");
        f.newClass = "msg_new";
        f.title = h;
        f.senderName = "100011";
        f.msgID = "111";
        f.msgContent = g;
        f.commentsURL = "";
        f.detailURL = "";
        f.commentsCount = 0;
        var e = array(f);
        ogame.messages.addMessage($("#subtabs-nfCommunicationMessages"), e)
    }, toggleRecipientSelectBox: function (b) {
        $(".input_replacement").filter(function () {
            if ($(this).data("recipient-cat") === b && !$(this).hasClass("focus")) {
                $(this).addClass("focus")
            }
        });
        $(".new_msg_label").filter(function () {
            var e = $(this);
            if (e.data("recipient-cat") === b) {
                if (e.hasClass("open")) {
                    e.removeClass("open").siblings(".recipient_select_box").hide()
                } else {
                    var a = e.siblings(".recipient_select_box"), f = a.find(".scroll_box");
                    $(".new_msg_label").removeClass("open").siblings(".recipient_select_box").hide();
                    e.addClass("open");
                    a.show();
                    (f.hasClass("mCustomScrollbar")) ? f.mCustomScrollbar("update") : f.mCustomScrollbar()
                }
            }
        })
    }, toggleRecipientSelection: function (d) {
        var e = d.data("recipient-cat"), f = d.data("recipient-id");
        if (d.hasClass("always_selected")) {
            return
        }
        if (d.hasClass("complete_ally")) {
            ogame.messages.toggleSelectAllRecipients(e);
            return
        }
        if (!d.hasClass("selected")) {
            ogame.messages.createRecipient(f, e, d.html());
            d.addClass("selected")
        } else {
            ogame.messages.removeRecipient(f)
        }
    }, toggleSelectAllRecipients: function (h) {
        var g = $(".complete_ally").hasClass("selected"), e = (g) ? "255" : "1", f = (g) ? "loca.founder" : "loca.completeAlliance";
        $(".input_replacement").children().remove();
        ogame.messages.createRecipient(e, h, f);
        $(".recipient_list").filter(function () {
            if ($(this).data("recipient-cat") === h) {
                $(this).find(".ally_rank").each(function () {
                    if (!$(this).hasClass("always_selected")) {
                        (g) ? $(this).removeClass("selected") : $(this).addClass("selected")
                    }
                })
            }
        })
    }
};
function initShowMessage() {
    var b = $('.overlayDiv[data-page="showmessage"]');
    $(".answerHeadline", b).click(function () {
        $(this).toggleClass("open");
        if ($(this).hasClass("open")) {
            $(".answerForm", b).show();
            $(".textWrapper", b).addClass("textWrapperSmall");
            $(".textWrapper", b).removeClass("textWrapper")
        } else {
            $(".answerForm", b).hide();
            $(".textWrapperSmall", b).addClass("textWrapper");
            $(".textWrapperSmall", b).removeClass("textWrapperSmall")
        }
    });
    $(".note > div:first-child", b).addClass("newMessage");
    $(".info:odd", b).css("margin-left", "40px");
    $("div.note p:first").after('<span class="seperator">');
    $(".answerHeadline", b).hover(function () {
        $(this).addClass("pushable")
    }, function () {
        $(this).removeClass("pushable")
    });
    $(".melden", b).click(function () {
        manageErrorbox($(this).attr("rel"), 1)
    })
}
function initNetworkAjax() {
    var e = $(".reiter");
    if (!$.isFunction(f)) {
        var f = function () {
            e.removeClass("active");
            $(this).addClass("active");
            ajaxLoad($(this).attr("id"), 1)
        }
    }
    e.off("click");
    e.click(f);
    $("#checkAll").off("click").click(function () {
        $(".checker").prop("checked", $(this).is(":checked"))
    });
    function d(a) {
        $("#TR" + a).hide()
    }

    $(".overlay").click(function () {
        var a = $(this).attr("id");
        markAsRead(a)
    });
    $("#messageContent select").change(function () {
        if (typeof($("select option:selected").attr("id")) == "undefined") {
            $(".buttonOK").hide();
            mod = ""
        } else {
            $(".buttonOK").show();
            mod = $("select option:selected").attr("id")
        }
    });
    $(".del").click(function () {
        mod = $(this).attr("id")
    });
    $(".underlined").click(function () {
        $(".buttonOK").hide()
    });
    reduceMsgCount(aktCat)
}
function MessageSlider(d) {
    var f = this;
    f.htmlobject = d;
    var e = document.documentElement.clientHeight - 160;
    this.open = function () {
        if (!this.inAction) {
            f.startTime = new Date().getTime();
            f.inAction = true;
            f.slideInStep()
        }
    }, this.slideInStep = function () {
        time = new Date().getTime();
        height = parseInt(f.currHeight * ((time - f.startTime) / 500));
        if (height < f.currHeight) {
            f.htmlobject.style.height = height + "px";
            window.setTimeout(f.slideInStep, 10)
        } else {
            f.htmlobject.style.height = f.currHeight + "px";
            f.inAction = false
        }
    }, this.close = function () {
        if (!f.inAction) {
            f.startTime = new Date().getTime();
            f.inAction = true;
            f.htmlobject.style.height = "0px";
            f.inAction = false
        }
    }, f.inAction = false;
    if (document.getElementById("messages")) {
        f.currHeight = Math.min(document.getElementById("messages").offsetHeight, e)
    } else {
        f.currHeight = e
    }
}
ogame.messages.combatreport = {
    data: [{combatReportId: $(".detailReport").attr("data-combatreportid")}],
    loca: [{weapon: "", shield: "", cover: ""}],
    getCombatValueByCombatMember: function () {
        var h = ogame.messages.combatreport;
        var g = h.data.activeMember;
        var f = {armorPercentage: 0, weaponPercentage: 0, shieldPercentage: 0};
        var e = 0;
        $.each(h.data.combatArray, function (b, a) {
            if (h.check(true, g, {values: {is: {0: "all"}}})) {
                f.armorPercentage += a.armorPercentage;
                f.weaponPercentage += a.weaponPercentage;
                f.shieldPercentage += a.shieldPercentage;
                e++
            } else {
                if (a.ownerName == g) {
                    f.armorPercentage = a.armorPercentage;
                    f.weaponPercentage = a.weaponPercentage;
                    f.shieldPercentage = a.shieldPercentage;
                    e = 1
                }
            }
        });
        f.armorPercentage = Math.round(f.armorPercentage / e);
        f.weaponPercentage = Math.round(f.weaponPercentage / e);
        f.shieldPercentage = Math.round(f.shieldPercentage / e);
        return f
    },
    setCombatValue: function () {
        var c = ogame.messages.combatreport;
        var d = c.getCombatValueByCombatMember();
        $("." + c.data.combatside + "Weapon").text(c.loca.weapon + " " + d.weaponPercentage + "%");
        $("." + c.data.combatside + "Shield").text(c.loca.shield + " " + d.shieldPercentage + "%");
        $("." + c.data.combatside + "Cover").text(c.loca.cover + " " + d.armorPercentage + "%")
    },
    setCombatLoca: function (f, e, g) {
        var h = ogame.messages.combatreport;
        h.loca.weapon = f;
        h.loca.shield = e;
        h.loca.cover = g
    },
    isActive: function (g, e) {
        var h = ogame.messages.combatreport;
        for (var f in e) {
            if (f == "length") {
                continue
            }
            if (h.check(true, e[f][g])) {
                return "on"
            }
        }
        if (h.check(true, e[g])) {
            return "on"
        }
        return "off"
    },
    setActiveFlag4Fleet: function (f, h) {
        var e = ogame.messages.combatreport;
        var g = e.getAllShipClasses(f);
        $.each(g, function (d, a) {
            var p = e.getShipIdByClass(a);
            var r = e.isActive(p, h);
            var q = e.getShipSelectors(p);
            e.changeShipState(r, q.ship);
            if (r == "off") {
                var b = q.shipCount;
                var c = q.loss;
                var o = {ships: {}, losses: {}};
                o.ships[b] = 0;
                o.losses[c] = "";
                e.setShipCount(o)
            }
            e.toggleShipShowState(q.ship)
        })
    },
    search4Class: function (e, g) {
        for (var f in e) {
            if ($("." + e[f] + g)[0]) {
                var h = e[f] + g;
                return h
            }
        }
        return false
    },
    getShipSelectors: function (k) {
        var m = ogame.messages.combatreport;
        var r = ["military", "civil", "defense"];
        var n = m.search4Class(r, k);
        var p = "." + m.data.combatside + " ." + n;
        var o = "." + m.data.combatside + " ." + n + " .ecke";
        var q = "." + m.data.combatside + " ." + n + " .lost_ships";
        var l = {ship: p, shipCount: o, loss: q};
        return l
    },
    getShipIdByClass: function (f) {
        var e = f.length;
        var d = f.substr(e - 3);
        return d
    },
    changeShipState: function (c, d) {
        if ($(d).hasClass("off") && c != "off") {
            $(d).removeClass("off")
        }
        if ($(d).hasClass("on") && c != "on") {
            $(d).removeClass("on")
        }
        if (!$(d).hasClass(c)) {
            $(d).addClass(c)
        }
    },
    toggleShipShowState: function (b) {
        if ($(b).hasClass("off")) {
            $(b).parent().hide()
        }
        if ($(b).hasClass("on")) {
            $(b).parent().show()
        }
    },
    getShipsByMembers: function (f) {
        var d = ogame.messages.combatreport;
        var e = [];
        if (!d.check(true, d.data.combatArray.shipDetails)) {
            $.each(d.data.combatArray, function (b, a) {
                if (typeof a == "object" && typeof a.shipDetails != "undefined") {
                    if (typeof e[a.ownerName] != "undefined") {
                        $.extend(e[a.ownerName], a.shipDetails)
                    } else {
                        e[a.ownerName] = a.shipDetails
                    }
                } else {
                    if (typeof e[a.ownerName] == "undefined") {
                        e[a.ownerName] = {}
                    }
                }
            })
        } else {
            if (typeof e[d.data.combatArray.ownerName] != "undefined") {
                $.extend(e[d.data.combatArray.ownerName], d.data.combatArray.shipDetails)
            } else {
                e[d.data.combatArray.ownerName] = d.data.combatArray.shipDetails
            }
        }
        if (f == "all") {
            return e
        } else {
            return e[f]
        }
    },
    getShipsByMembersAndCoords: function (h, l, f) {
        var k = ogame.messages.combatreport;
        var g = [];
        if (!k.check(true, k.data.combatArray.shipDetails)) {
            $.each(k.data.combatArray, function (b, a) {
                if (l !== 0) {
                    if (typeof a == "object" && typeof a.shipDetails != "undefined") {
                        if (a.ownerCoordinates === l && a.ownerName == h && a.ownerPlanetType == f) {
                            if (typeof g[a.ownerName] != "undefined") {
                                $.extend(g[a.ownerName], a.shipDetails)
                            } else {
                                g[a.ownerName] = a.shipDetails
                            }
                        }
                    }
                } else {
                    if (typeof a == "object" && typeof a.shipDetails != "undefined") {
                        if (typeof g[a.ownerName] != "undefined") {
                            $.extend(g[a.ownerName], a.shipDetails)
                        } else {
                            g[a.ownerName] = a.shipDetails
                        }
                    }
                }
            })
        } else {
            if (typeof g[k.data.combatArray.ownerName] != "undefined") {
                $.extend(g[k.data.combatArray.ownerName], k.data.combatArray.shipDetails)
            } else {
                g[k.data.combatArray.ownerName] = k.data.combatArray.shipDetails
            }
        }
        if (h == "all") {
            return g
        } else {
            return g[h]
        }
    },
    getShipCountArray: function (o, u) {
        var q = ogame.messages.combatreport;
        var y = q.check(true, u, {types: {0: "isEmpty"}}) ? false : true;
        var p = {};
        var r = {};
        for (var n in o) {
            var v = q.getShipSelectors(n);
            var w = v.shipCount;
            p[w] = parseInt(o[n]);
            if (!y) {
                var s = v.loss;
                if (q.check(true, u[n])) {
                    r[s] = parseInt(u[n])
                }
            }
        }
        var x = {ships: p, losses: r};
        return x
    },
    setShipCount4All: function (u) {
        var w = ogame.messages.combatreport;
        var v = u.ships;
        var A = w.check(true, u.losses) ? u.losses : {};
        var H = {};
        for (var y in v) {
            var F = v[y];
            var s = (w.check(true, A, {types: {0: "isEmpty"}}) && w.check(true, A[y])) ? A[y] : {};
            H[y] = w.getShipCountArray(F, s)
        }
        var B = {};
        var E = {};
        for (var x in H) {
            var G = H[x]["ships"];
            var C = H[x]["losses"];
            for (var D in G) {
                if (w.check(true, B[D])) {
                    B[D] = B[D] + G[D]
                } else {
                    B[D] = G[D]
                }
            }
            for (var D in C) {
                if (w.check(true, E[D])) {
                    E[D] = E[D] + C[D]
                } else {
                    E[D] = C[D]
                }
            }
        }
        var r = {ships: B, losses: E};
        w.setShipCount(r)
    },
    setShipCountByActiveMember: function (s, w, B, u, q) {
        u = u || 0;
        q = q || 1;
        var v = ogame.messages.combatreport;
        var E = {};
        var F = {};
        for (var D in w) {
            var r = w[D]["ownerName"];
            var C = w[D]["ownerCoordinates"];
            var A = w[D]["ownerPlanetType"];
            if (r == B) {
                for (var x in s.ships[D]) {
                    if (u !== 0) {
                        if (u == C && q == A) {
                            if (!(E.hasOwnProperty(x))) {
                                E[x] = s.ships[D][x]
                            } else {
                                E[x] = E[x] + s.ships[D][x]
                            }
                        }
                    } else {
                        if (!(E.hasOwnProperty(x))) {
                            E[x] = s.ships[D][x]
                        } else {
                            E[x] = E[x] + s.ships[D][x]
                        }
                    }
                }
                if (v.check(true, s.losses)) {
                    for (var x in s.losses[D]) {
                        if (u !== 0) {
                            if (u == C && q == A) {
                                if (!(F.hasOwnProperty(x))) {
                                    F[x] = parseInt(s.losses[D][x])
                                } else {
                                    F[x] = F[x] + parseInt(s.losses[D][x])
                                }
                            }
                        } else {
                            if (!(F.hasOwnProperty(x))) {
                                F[x] = parseInt(s.losses[D][x])
                            } else {
                                F[x] = F[x] + parseInt(s.losses[D][x])
                            }
                        }
                    }
                }
            }
        }
        if (v.check(true, E, {types: {0: "isEmpty"}})) {
            var y = v.getShipCountArray(E, F);
            v.setShipCount(y)
        }
    },
    setShipCount: function (k) {
        var h = ogame.messages.combatreport.data.combatside;
        var f = k.ships;
        var l = k.losses;
        if (!$.isEmptyObject(f)) {
            for (var g in f) {
                $(g).text(f[g].toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."))
            }
        } else {
            $("." + h + " .ecke").text("0")
        }
        if (!$.isEmptyObject(l)) {
            for (var g in l) {
                $(g).text("-" + l[g].toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."))
            }
        } else {
            $("." + h + " .lost_ships").text("-")
        }
    },
    getAllShipClasses: function (e) {
        var f = e.map(function () {
            var a = $(this).attr("class");
            return a
        }).get().join();
        var d = [];
        $.each(f.split(","), function (b, a) {
            var c = a.split(" ");
            d.push(c[1])
        });
        return d
    },
    displayShipData: function (k, l, m, p, o, h) {
        o = typeof o !== "undefined" ? o : 0;
        var n = ogame.messages.combatreport;
        if (n.check(true, k, {values: {isNot: {0: "all"}}})) {
            n.setShipCountByActiveMember(m, p, k, o, h)
        } else {
            n.setShipCount4All(n.data.combatRounds[l][n.data.combatRounds[l].length - 1])
        }
    },
    initCombatText: function (f) {
        var d = ogame.messages.combatreport;
        var e = f.combatRounds.length - 1;
        $(".combat_round_list .round_id").find("a").removeClass("active");
        $(".combat_round_list .round_id[data-round=" + e + "]").find("a").addClass("active");
        d.loadDataBySelectedRound(f.attackerJSON, f.defenderJSON, e)
    },
    setCombatText: function (v, G, s) {
        var x = ogame.messages.combatreport;
        var A = ".statistic_attacker";
        var u = ".statistic_defender";
        var C = ".hits";
        var I = ".strength";
        var E = ".absorbed";
        var J = 0;
        var B = 0;
        var H = 0;
        var F = 0;
        var w = 0;
        var D = 0;
        if (x.check(true, v, {length: s})) {
            for (var y in v) {
                J = J + parseInt(v[y]["statistic"]["hits"]);
                B = B + parseInt(v[y]["statistic"]["absorbedDamage"]);
                H = H + parseInt(v[y]["statistic"]["fullStrength"]);
                F = F + parseInt(G[y]["statistic"]["hits"]);
                w = w + parseInt(G[y]["statistic"]["absorbedDamage"]);
                D = D + parseInt(G[y]["statistic"]["fullStrength"])
            }
        } else {
            J = parseInt(v[s]["statistic"]["hits"]);
            B = parseInt(v[s]["statistic"]["absorbedDamage"]);
            H = parseInt(v[s]["statistic"]["fullStrength"]);
            F = parseInt(G[s]["statistic"]["hits"]);
            w = parseInt(G[s]["statistic"]["absorbedDamage"]);
            D = parseInt(G[s]["statistic"]["fullStrength"])
        }
        $(A + C).text(J.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        $(A + E).text(B.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        $(A + I).text(H.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        $(u + C).text(F.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        $(u + E).text(w.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        $(u + I).text(D.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."))
    },
    setCombatArray: function (f, g) {
        var e = jQuery.extend(true, {}, f);
        var h = ogame.messages.combatreport;
        h.data.combatside = g;
        h.data.memberSelection = false;
        if ($("#" + h.data.combatside + "_select_combatreport").find(":selected").length > 0) {
            h.data.memberSelection = true
        }
        if (h.data.memberSelection) {
            h.data.activeMember = "" + $("#" + h.data.combatside + "_select_combatreport").find(":selected").val()
        } else {
            h.data.activeMember = "" + $("#" + h.data.combatside + "_select_combatreport").data("memberName")
        }
        h.data.activeMember = h.data.activeMember.split("|", 1)[0];
        h.data.combatArray = e.member;
        h.data.combatRounds = [];
        h.data.combatRounds[g] = e.combatRounds
    },
    setCombatside: function (c) {
        var d = ogame.messages.combatreport;
        d.data.combatside = c
    },
    check: function (M, N, P) {
        var P = (P != null && typeof P != "undefined" && typeof P == "object") ? P : false;
        var F = false;
        var D = 0;
        var B = 0;
        var I = 0;
        var J = 0;
        var C = false;
        if (P != false) {
            var K = P.values != null && typeof P.values != "undefined" && typeof P.values == "object" ? P.values : {};
            var y = P.types != null && typeof P.types != "undefined" && typeof P.types == "object" ? P.types : {};
            var x = P.length != null && typeof P.length != "undefined" ? P.length : false;
            if (!jQuery.isEmptyObject(K)) {
                for (G in K) {
                    var O = K[G];
                    if (G == "is" && !jQuery.isEmptyObject(O)) {
                        for (var A in O) {
                            if (N != O[A]) {
                                D = D + 1
                            }
                        }
                    }
                    if (G == "isNot" && !jQuery.isEmptyObject(O)) {
                        for (var H in O) {
                            if (N == O[H]) {
                                D = D + 1
                            }
                        }
                    }
                }
            }
            if (!jQuery.isEmptyObject(y)) {
                if (y.isEmpty) {
                    if (jQuery.isEmptyObject(N)) {
                        B = B + 1
                    }
                }
                for (var G in y) {
                    if (typeof N == y[G]) {
                        B = B + 1
                    }
                }
            }
            if (typeof N == "object") {
                for (var G in N) {
                    if (typeof N[G] != "undefined") {
                        J = J + 1
                    }
                }
            } else {
                var L = N + "";
                J = L.length
            }
            if (x !== false && typeof x == "number" && typeof J == "number") {
                if (J != x) {
                    I = I + 1
                }
                C = true
            }
        }
        if (typeof N == "undefined" || N == null) {
            B = B + 1
        }
        var w = D + B;
        if (C != false) {
            w = w + I
        }
        if (w == 0) {
            F = true
        }
        var E = {
            success: F,
            allErrors: w,
            valueErrors: D,
            typeErrors: B,
            type: typeof N,
            length: J,
            lengthChecked: C,
            lengthError: I
        };
        if (M) {
            return F
        } else {
            return E
        }
    },
    loadData: function (e, f) {
        var d = ogame.messages.combatreport;
        d.setCombatArray(e, f);
        d.loadDataBySelectedCombatMember(e, f);
        d.setCombatValue()
    },
    loadDataBySelectedRound: function (E, F, B) {
        var A = ogame.messages.combatreport;
        var D = parseInt(B);
        var K = $(".attacker .participant_select option:selected").val();
        var G = $(".defender .participant_select option:selected").val();
        var J = $(".attacker .participant_select option:selected").data("coords");
        var L = $(".defender .participant_select option:selected").data("coords");
        var I = [];
        var H = [];
        if (typeof J != "undefined") {
            ids = K.split("|")[1].split(":");
            I.push(ids);
            K = K.split("|")[0]
        } else {
            if (K != "all" && typeof K != "undefined") {
                J = 0;
                $(".attacker .participant_select option").each(function () {
                    if (K == $(this).val().split("|")[0] && typeof $(this).val().split("|")[1] != "undefined") {
                        I.push($(this).val().split("|")[1].split(":"))
                    }
                })
            } else {
                I = []
            }
        }
        if (typeof L != "undefined") {
            ids = G.split("|")[1].split(":");
            H.push(ids);
            G = G.split("|")[0]
        } else {
            if (G != "all" && typeof G != "undefined") {
                L = 0;
                $(".defender .participant_select option").each(function () {
                    if (G == $(this).val().split("|")[0] && typeof $(this).val().split("|")[1] != "undefined") {
                        H.push($(this).val().split("|")[1].split(":"))
                    }
                })
            } else {
                H = []
            }
        }
        if (I.length > 0) {
            for (var x in E) {
                if (x == "combatRounds") {
                    for (var y in E[x][D]) {
                        if (y == "ships") {
                            for (var u in E[x][D][y]) {
                                var C = $.inArray(u, I[0]);
                                if (C == -1) {
                                    delete E[x][D][y][u]
                                }
                            }
                        }
                    }
                }
            }
        }
        if (H.length > 0) {
            for (var x in F) {
                if (x == "combatRounds") {
                    for (var y in F[x][D]) {
                        if (y == "ships") {
                            for (var u in F[x][D][y]) {
                                var C = $.inArray(u, H[0]);
                                if (C == -1) {
                                    delete F[x][D][y][u]
                                }
                            }
                        }
                    }
                }
            }
        }
        var w = E.combatRounds;
        var v = F.combatRounds;
        if (A.check(true, w, {length: B})) {
            D = D - 1
        }
        D = D + "";
        A.setCombatside("attacker");
        A.setShipCount4All(w[D]);
        A.setCombatside("defender");
        A.setShipCount4All(v[D]);
        if (A.data.memberSelection) {
        }
        A.setCombatText(w, v, B)
    },
    resetDropDowns: function () {
        $("#attacker_select_combatreport").val("all").ogameDropDown("refresh");
        $("#defender_select_combatreport").val("all").ogameDropDown("refresh")
    },
    loadDataBySelectedCombatMember: function (u, q, n, l) {
        var m = jQuery.extend(true, {}, u);
        n = n || 0;
        l = l || 1;
        var p = ogame.messages.combatreport;
        p.setCombatArray(m, q);
        var o = $("." + p.data.combatside + " .buildingimg");
        var r = $("." + p.data.combatside + " .defenseimg");
        o = $.merge(o, r);
        var s = p.getShipsByMembersAndCoords(p.data.activeMember, n, l);
        p.setActiveFlag4Fleet(o, s);
        p.displayShipData(p.data.activeMember, p.data.combatside, m.combatRounds[m.combatRounds.length - 1], m.member, n, l);
        p.setCombatValue()
    }
};
function closeDetails(f, d) {
    var e = $("#fleet" + f);
    e.children(".openDetails").children().children().attr("src", "/img/icons/de1e5f629d9e47d283488eee0c0ede.gif");
    e.children(".quantity").show();
    e.removeClass("detailsOpened");
    e.addClass("detailsClosed");
    currentMovementTabExtensionStates[f] = [0, d];
    updateCookieStatus(currentMovementTabExtensionStates)
}
function openDetails(f, d) {
    var e = $("#fleet" + f);
    e.children(".openDetails").children().children().attr("src", "/img/icons/577565fadab7780b0997a76d0dca9b.gif");
    e.children(".quantity").hide();
    e.removeClass("detailsClosed");
    e.addClass("detailsOpened");
    currentMovementTabExtensionStates[f] = [1, d];
    updateCookieStatus(currentMovementTabExtensionStates)
}
function updateCookieStatus(d) {
    var e = JSON.stringify(d);
    var f = JSON.stringify({expires: Math.round(new Date().getTime() / 1000) + 7 * 86400});
    $.cookie("tabBoxFleets", e, f)
}
function openCloseDetails(c, d) {
    if ($("#fleet" + c).attr("class") == "fleetDetails detailsOpened") {
        closeDetails(c, d)
    } else {
        openDetails(c, d)
    }
}