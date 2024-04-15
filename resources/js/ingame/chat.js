ogame.chat = {
    socket: null,
    connected: false,
    connecting: false,
    timeout: null,
    retryInterval: 5000,
    playerId: null,
    associationId: null,
    data: {association: {}},
    playernames: {},
    playerList: null,
    isLoadingPlayerList: false,
    playerListSelector: new Array,
    initConnection: function () {
        console.log('ogame.chat init');
        var c = ogame.chat;
        if (c.connecting || c.connected || c.isMobile) {
            c.socket.disconnect()
        }
        c.connecting = true;
        try {
            c.socket = io.connect("/chat", nodeParams);
            c.socket.on("connect", function () {
                clearTimeout(this.timeout);
                c.socket.emit("authorize", session, function (a) {
                    c.connecting = false;
                    if (a) {
                        c.connected = true
                    } else {
                        c.socket.disconnect()
                    }
                })
            });
            c.socket.on("chat", function (a) {
                c.messageReceived(a)
            });
            c.socket.on("disconnect", function () {
                c.connected = false;
                c.connecting = false
            })
        } catch (d) {
            c.connecting = false
        }
    },
    initialize: function () {
        var b = ogame.chat;
        loadScript(nodeUrl, b.initConnection);
        $(".new_msg_count[data-playerid]").each(function () {
            b.saveMessageCounter($(this).data("new-messages"), $(this).data("playerid"))
        });
        this.updateTotalNewChatCounter();
        $(".js_playerlist").on("click", ".playerlist_item", function () {
            var a = $(this).hasClass("nothingThere");
            if (!a) {
                var d = $(this).data("msgid");
                if (d) {
                    b.loadChatLogWithPlayer(this, d)
                } else {
                    b.loadChatLogWithPlayer(this)
                }
            }
        });
        $(".js_playerlist").on("click", ".openAssociationChat", function () {
            var a = $(this).data("msgid");
            if (a) {
                b.loadChatLogWithAssociation(this, a)
            } else {
                b.loadChatLogWithAssociation(this)
            }
        });
        $("#chatMsgList").on("click", ".msg", function () {
            var e = $(this).data("playerid");
            var a = $(this).data("associationid");
            if (e !== undefined && e > 0) {
                b.saveMessageCounter(0, e);
                ogame.messagemarker.setPartnerId(e);
                ogame.messagemarker.updateNewMarker();
                ogame.chat.updateTotalNewChatCounter();
                var f = $(".playerlist .playerlist_item[data-playerId=" + e + "]").data("msgid");
                if (f) {
                    b.loadChatLogWithPlayer(this, f)
                } else {
                    b.loadChatLogWithPlayer(this)
                }
            } else {
                var f = $(".playerlist .playerlist_item[data-associationId=" + a + "]").data("msgid");
                b.saveMessageCounterAssociation(0, a);
                if (f) {
                    b.loadChatLogWithAssociation(this, f)
                } else {
                    b.loadChatLogWithAssociation(this)
                }
            }
        });
        $(".chat").on("click", ".sys_msg", function (f) {
            var h = $(this).data("foreign-player-id");
            var g = $(this).data("foreign-association-id");
            var a = {playerId: h, associationId: g, ajax: 1};
            console.log('chatLoadMoreMessages()');
            $.ajax({
                url: chatUrlLoadMoreMessages,
                type: "POST",
                dataType: "html",
                data: a,
                success: function (c) {
                    $(".chat").each(function (e, d) {
                        if (h !== undefined && h == $(d).data("foreign-player-id")) {
                            $(d).html(c)
                        } else {
                            if (g !== undefined && g == $(d).data("foreign-association-id")) {
                                $(d).html(c)
                            }
                        }
                    })
                },
                error: function (e, c, d) {
                }
            })
        });
        $("body").on("click", ".js_openChat", function () {
            b.loadChatLogWithPlayer(this)
        });
        if (typeof $.cookie("maximizeId") == "string" || typeof $.cookie("maximizeId") == "number") {
            $('#chatMsgList .msg[data-playerid="' + $.cookie("maximizeId") + '"]').trigger("click");
            $.cookie("maximizeId", null)
        }
    },
    getTotalNewChatCounter: function () {
        return ogame.messagecounter.sumNewChatMessages
    },
    updateTotalNewChatCounter: function () {
        var b = 0;
        if ($(".msg .new_msg_count").length > 0) {
            $(".msg .new_msg_count").each(function () {
                b += Number($(this).data("new-messages"))
            })
        } else {
            if ($("#chatBarPlayerList .new_msg_count").length > 0) {
                $("#chatBarPlayerList .new_msg_count").each(function () {
                    b += Number($(this).data("new-messages"))
                })
            }
        }
        ogame.messagecounter.initialize(ogame.messagecounter.type_chat, ogame.chat.playerId);
        if (ogame.messagecounter.sumNewChatMessages !== b) {
            ogame.messagecounter.initChatCounter(b);
            ogame.messagecounter.sumNewChatMessages = b;
            ogame.messagecounter.update()
        }
        return b
    },
    retryConnection: function () {
        var b = ogame.chat;
        setTimeout(function () {
            b.initConnection()
        }, 5000)
    },
    sendMessage: function (u, r, n, m) {
        var o = ogame.chat;
        if ($.trim(n).length == 0) {
            s("TEXT_EMPTY");
            return
        }
        if (u > 0) {
            var w = {playerId: u, text: n, mode: 1, ajax: 1}
        } else {
            var w = {associationId: r, text: n, mode: 3, ajax: 1}
        }
        if (typeof m !== "undefined" && typeof m.id !== "undefined") {
            w.msg2reply = m.id
        }
        function v() {
            console.log('v()');
            $.ajax({
                url: chatUrl,
                type: "POST",
                dataType: "json",
                data: w,
                success: function (a) {
                    p(a)
                },
                error: function (a, b, c) {
                }
            })
        }

        function q(a) {
            if (typeof a.refAuthor !== "undefined" && typeof a.refContent !== "undefined") {
                $refData = {author: a.refAuthor, text: a.refContent}
            } else {
                $refData = 0
            }
            if (a.targetId !== undefined) {
                o.addChatItem(a.targetId, 0, a.text, a.id, false, $refData, a.date)
            } else {
                o.addChatItem(u, a.targetAssociationId, a.text, a.id, false, $refData, a.date)
            }
        }

        function s(a) {
            if (chatLoca[a] !== undefined) {
                errorBoxNotify(LocalizationStrings.error, chatLoca[a], LocalizationStrings.ok)
            } else {
                errorBoxNotify(LocalizationStrings.error, a, LocalizationStrings.ok)
            }
        }

        function p(a) {
            switch (a.status) {
                case"NOT_AUTHORIZED":
                    v();
                    break;
                case"OK":
                    q(a);
                    ogame.chat.cleanupUrl();
                    break;
                default:
                    s(a.status)
            }
        }

        v()
    },
    messageReceived: function (h) {
        var g = ogame.chat;
        if (typeof h.refAuthor !== "undefined" && typeof h.refText !== "undefined") {
            $refData = {author: h.refAuhtor, text: h.refText}
        } else {
            $refData = 0
        }
        if (h.senderName !== undefined && h.senderId !== undefined) {
            g.playernames[h.senderId] = h.senderName
        }
        if ($(".chat_bar_list").length) {
            if (h.associationId !== undefined && h.associationId > 0) {
                if (g.data.association[h.associationId] === undefined) {
                    g.loadChatLogWithAssociation(h.associationId, null, function () {
                        g.addChatItem(h.senderId, h.associationId, h.text, h.id, true, $refData, h.date)
                    }, false)
                } else {
                    g.addChatItem(h.senderId, h.associationId, h.text, h.id, true, $refData, h.date)
                }
            } else {
                if (g.data[h.senderId] === undefined) {
                    g.loadChatLogWithPlayer(h.senderId, null, function () {
                        g.addChatItem(h.senderId, 0, h.text, h.id, true, $refData, h.date)
                    }, false)
                } else {
                    g.addChatItem(h.senderId, 0, h.text, h.id, true, $refData, h.date)
                }
            }
        }
        if (h.associationId !== undefined && h.associationId > 0) {
            if ($('.chat_bar_list_item.open[data-associationid="' + h.associationId + '"]').length <= 0) {
                var e = $('.new_msg_count[data-associationid="' + h.associationId + '"]').data("new-messages");
                if (isNaN(e)) {
                    e = 0
                }
                e++;
                g.saveMessageCounterAssociation(e, h.associationId);
                g.updateTotalNewChatCounter()
            } else {
                var f = {
                    associationId: h.associationId,
                    mode: 4,
                    ajax: 1,
                    updateUnread: 1
                };
                console.log('chatUrl()');
                $.ajax({
                    url: chatUrl,
                    type: "POST",
                    data: f,
                    success: function (a) {
                    },
                    error: function (c, a, b) {
                    }
                })
            }
        } else {
            if (h.senderId !== undefined && h.senderId > 0) {
                ogame.messagemarker.setPartnerId(h.senderId);
                if (!g.isOpen(h.senderId)) {
                    ogame.messagecounter.initialize(ogame.messagecounter.type_chat, h.senderId);
                    var e = parseInt(ogame.messagecounter.newChats[h.senderId]);
                    if (isNaN(e)) {
                        e = 0
                    }
                    e++;
                    g.saveMessageCounter(e, h.senderId);
                    ogame.messagemarker.updateNewMarker()
                } else {
                    g.saveMessageCounter(0, $(this).data("playerid"));
                    ogame.messagemarker.updateNewMarker()
                }
            }
        }
    },
    cleanupUrl: function () {
        var k = window.location.href;
        var h = k.indexOf("&");
        if (h > 0) {
            var g = k.indexOf("?");
            var l = k.substring(0, g);
            var f = l + "?page=chat";
            window.history.pushState({}, "", f)
        }
    },
    saveMessageCounter: function (c, d) {
        if (isNaN(d) || d === 0) {
            return false
        }
        $('.new_msg_count[data-playerid="' + d + '"]').data("new-messages", c);
        ogame.messagecounter.newChats[d] = c
    },
    saveMessageCounterAssociation: function (c, d) {
        if (isNaN(d) || d === 0) {
            return false
        }
        $('.new_msg_count[data-associationid="' + d + '"]').data("new-messages", c);
        $('.new_msg_count[data-associationid="' + d + '"]').text(c);
        ogame.messagemarker.updateNewMarker()
    },
    isOpen: function (e) {
        var f = false;
        var d = $(".chatContent").data("chatplayerid");
        if (d != "undefined" && d == e) {
            f = true
        } else {
            $(".chat_box").each(function () {
                if ($(this).attr("data-playerid") == e) {
                    if ($(this).css("display") == "block") {
                        f = true
                    }
                }
            })
        }
        return f
    },
    loadChatLogWithPlayer: function (p, n, l, h) {
        var m = ogame.chat;
        var o;
        if (typeof h == "undefined") {
            h = true
        }
        if (typeof p == "number") {
            o = p
        } else {
            o = $(p).attr("data-playerId")
        }
        var k = {playerId: o, mode: 2, ajax: 1, updateUnread: (h ? 1 : 0)};
        if (typeof n == "number") {
            k.msg2reply = n
        }
        console.log('chatUrl POST()');
        $.ajax({
            url: chatUrl, type: "POST", data: k, success: function (a) {
                a = JSON.parse(a);
                m.data[a.playerId] = {
                    playerstatus: a.playerstatus,
                    playerName: a.playerName,
                    playerId: a.playerId,
                    chatItems: a.chatItems,
                    chatItemsByDateAsc: a.chatItemsByDateAsc
                };
                if (typeof l == "function") {
                    l()
                } else {
                    if ($(p).parents("#chatBarPlayerList").length || $("body")[0].id != "chat") {
                        m.showChat(a)
                    } else {
                        m.showChatHistory(a)
                    }
                }
                var b = $(".chat_bar_list").find("[data-playerid='" + a.playerId + "']");
                m.updateCustomScrollbar(b.find(".chat_box_ctn"))
            }, error: function (c, a, b) {
            }
        })
    },
    loadChatLogWithAssociation: function (o, n, l, h) {
        var m = ogame.chat;
        var p;
        if (typeof h == "undefined") {
            h = true
        }
        if (typeof o == "number") {
            p = o
        } else {
            p = $(o).attr("data-associationid")
        }
        var k = {associationId: p, mode: 4, ajax: 1, updateUnread: (h ? 1 : 0)};
        if (typeof n == "number") {
            k.msg2reply = n
        }
        console.log('chaturl POST()');
        $.ajax({
            url: chatUrl, type: "POST", data: k, success: function (a) {
                a = JSON.parse(a);
                m.data.association[a.associationId] = {
                    playerstatus: a.playerstatus,
                    associationName: a.associationName,
                    associationId: a.associationId,
                    chatItems: a.chatItems,
                    chatItemsByDateAsc: a.chatItemsByDateAsc
                };
                if (typeof l == "function") {
                    l()
                } else {
                    if ($(o).parents("#chatBarPlayerList").length || $("body")[0].id != "chat") {
                        m.showChat(a)
                    } else {
                        m.showChatHistory(a)
                    }
                }
                var b = $(".chat_bar_list").find("[data-associationid='" + a.associationId + "']");
                m.updateCustomScrollbar(b.find(".chat_box_ctn"))
            }, error: function (c, a, b) {
            }
        })
    },
    initChat: function (c, d) {
        ogame.chat.playerId = c;
        ogame.chat.isMobile = d;
        ogame.chat.initPlayerlist();
        ogame.chat.initialize();
        ogame.chat.toggleVisibility();
        ogame.chat.setVisibilityState();
        ogame.chat.initMaximize();
        ogame.chat.getInMaxChat()
    },
    getLastChatItemData: function () {
        var l = ogame.chat;
        var h = null;
        $(".chat_box_ctn .mCustomScrollBox .mCSB_container").each(function () {
            var a = $(this).children("ul.chat").children("li:last");
            if (h === null || a.attr("data-chat-id") > h.attr("data-chat-id")) {
                h = a
            }
        });
        if (h === null) {
            $("ul.largeChat").each(function () {
                var a = $(this).children("li:last");
                if (h === null || a.attr("data-chat-id") > h.attr("data-chat-id")) {
                    h = a
                }
            })
        }
        if (h === null) {
            return null
        }
        var k = h.children(".msg_head").find(".msg_date").html();
        var f = h.find(".msg_content").html();
        var g = {date: k, text: f};
        return g
    },
    addChatItem: function (F, x, B, D, v, A, r) {
        var u = ogame.chat;
        var q;
        if (x > 0) {
            q = $(".chat_bar_list").find("[data-associationid='" + x + "']")
        } else {
            q = $(".chat_bar_list").find("[data-playerid='" + F + "']")
        }
        var y = {};
        y.date = r;
        y.newClass = "new";
        if (v) {
            if (u.data[F] !== undefined) {
                y.playerName = u.data[F].playerName
            } else {
                y.playerName = u.playernames[F]
            }
            y.altClass = ""
        } else {
            y.playerName = playerName;
            y.altClass = "odd"
        }
        y.chatID = D;
        y.chatContent = B;
        if (typeof A == "object") {
            y.refData = A
        }
        if (!q.length) {
            var E = u.createChatBarContainer(F);
            u.updateChatBar(E);
            q = $(".chat_bar_list").find("[data-playerid='" + F + "']")
        }
        var w = u.createChatItem(y);
        var s = u.getLastChatItemData();
        if (s !== null && (y.date != s.date || y.chatContent != s.text)) {
            q.find(".chat").append(w);
            u.updateCustomScrollbar(q.find(".chat_box_ctn"));
            var C = $(".js_chatHistory");
            if (C.length && (C.data("chatplayerid") == F || C.data("associationid") == x)) {
                C.find(".chat.clearfix").append(w.clone());
                u.updateCustomScrollbar($(".largeChatContainer"))
            }
        }
    },
    addToMoreBox: function (m) {
        var l = ogame.chat;
        var k = m.length;
        if (k && $(".more_chat_bar_items").length < 1) {
            $(".chat_bar_list").append(l.createMoreBox("more_chat_bar_items"))
        }
        var g = $(".more_chat_bar_items .more_items");
        var h = $(".more_chat_bar_items .chat_box");
        for (var n = 0; n <= k; n++) {
            g.append(m.pop())
        }
        l.updateCustomScrollbar(h)
    },
    createChatBarContainer: function (f) {
        var g = ogame.chat;
        if (!f) {
            return
        }
        var h = g.data[f];
        g.data.playerId = f;
        var e = $('<li class="chat_bar_list_item open" data-playerid="' + f + '"></li>');
        e.append('<span class="playerstatus ' + h.playerstatus + '"></span>');
        e.append('<span class="cb_playername">' + h.playerName + "</span>");
        e.append('<span class="icon icon_close fright"></span>');
        e.prepend(g.createChatBox(f));
        return e
    },
    createChatBarContainerForAssociations: function (f) {
        var g = ogame.chat;
        if (!f) {
            return
        }
        var h = g.data.association[f];
        g.data.associationId = f;
        var e = $('<li class="chat_bar_list_item open" data-associationId="' + f + '"></li>');
        e.append('<span class="playerstatus ' + h.playerstatus + '"></span>');
        e.append('<span class="cb_playername">' + h.associationName + "</span>");
        e.append('<span class="icon icon_close fright"></span>');
        e.prepend(g.createChatBoxForAssociations(f));
        return e
    },
    closeChatBox: function (f, d) {
        var e = $(".chat_bar_list_item");
        $.each(e, function (b, a) {
            if (f !== undefined && $(a).data("playerid") == f) {
                $(a).addClass("outOfChatbar");
                $(a).removeClass("open")
            } else {
                if (d !== undefined && $(a).data("associationid") == d) {
                    $(a).addClass("outOfChatbar");
                    $(a).removeClass("open")
                }
            }
        })
    },
    getVisibleChats: function () {
        if (typeof visibleChats == "undefined") {
            visibleChats = {chatbar: false, players: [], associations: []}
        }
        return visibleChats
    },
    getVisibleChatPlayerIds: function () {
        var k = ogame.chat;
        var l = k.getVisibleChats();
        var h = {};
        var g = 0;
        for (var f = 0; f < l.players.length; f++) {
            if ($.inArray(l.players[f]["partnerId"], h) == -1) {
                h[g] = l.players[f]["partnerId"];
                g++
            }
        }
        return h
    },
    getVisibleChatAssociationIds: function () {
        var h = ogame.chat;
        var k = h.getVisibleChats();
        var l = {};
        var g = 0;
        for (var f = 0; f < k.associations.length; f++) {
            if ($.inArray(k.associations[f]["partnerId"], l) == -1) {
                l[g] = k.associations[f];
                g++
            }
        }
        return l
    },
    setVisibilityState: function () {
        var n = ogame.chat;
        var s = n.getVisibleChatPlayerIds();
        var m = n.getVisibleChatAssociationIds();
        var o = $("#chatBar .chat_bar_list .chat_bar_list_item");
        for (var q = 0; q < o.length; q++) {
            var l = o.get(q);
            var u = $(l).data("playerid");
            var r = $(l).data("associationid");
            if (u !== undefined && !n.isInJson(u, s)) {
                n.closeChatBox(u, 0)
            } else {
                if (r !== undefined && !n.isInJson(r, m)) {
                    n.closeChatBox(0, r)
                } else {
                    l.style.display = "inline";
                    if ($(l).hasClass("open")) {
                        var p = $(l).find("div.chat_box")[0];
                        p.style.display = "inline";
                        n.updateCustomScrollbar($(l).find(".chat_box_ctn"), 1)
                    }
                }
            }
        }
    },
    isInJson: function (f, d) {
        var e = null;
        if ($.isEmptyObject(d)) {
            e = false
        }
        if (e !== false) {
            $.each(d, function (a, b) {
                if (b == f) {
                    e = true
                }
            });
            if (e !== true) {
                false
            }
        }
        return e
    },
    toggleVisibility: function () {
        $(".chat_bar_list_item .icon_close").on("click", function (f) {
            var e = $(this).parent().data("playerid");
            var d = $(this).closest(".chat_box");
            if (!d.length) {
                d = $(this).parent()[0];
                d.style.display = "none"
            }
            if (e > 0) {
                console.log('toggleVisibilityChat()');
                $.ajax({
                    type: "POST",
                    url: "/game/index.php?page=ajaxChatToggleVisibility",
                    data: {from: playerId, to: e, showState: 0},
                    success: function (a) {
                    },
                    error: function (c, a, b) {
                    }
                })
            }
        });
        $(".cb_playerlist_box .playerlist_item").on("click", function () {
            var b = $(this).data("playerid");
            if (b) {
                console.log('cb_playerlist_box()');
                $.ajax({
                    type: "POST",
                    url: "/game/index.php?page=ajaxChatToggleVisibility",
                    data: {from: playerId, to: b, showState: 1},
                    success: function (a) {
                    },
                    error: function (a, e, f) {
                    }
                })
            }
        })
    },
    initMaximize: function () {
        $(".chat_bar_list").on("click.chatBar", ".chat_box .chat_box_title .icon_maximize", function () {
            var c = $(this).parent();
            var d = $(c).parent().data("playerid");
            $.cookie("maximizeId", d);
            $(".chat_bar_list_item.open .chat_box_title .icon_close").trigger("click");
            window.location = bigChatLink + "&playerId=" + d
        })
    },
    getInMaxChat: function () {
        var b = location.href;
        if (typeof bigChatLink == "undefined") {
            bigChatLink = ""
        }
        if (bigChatLink == b) {
            if ($.cookie("maximizeId") !== null) {
                $("#chatMsgList .msg[data-playerId=" + $.cookie("maximizeId") + "]").trigger("click")
            }
        }
        $.cookie("maximizeId", null)
    },
    createChatBox: function (l) {
        var n = ogame.chat;
        if (!l) {
            return
        }
        var p = n.data[l];
        var r = $('<div class="chat_box_title"></div>');
        r.append('<span class="icon icon_close fright"></span>');
        r.append('<span class="icon icon_maximize fright"></span>');
        var m = $('<div class="chat_box_ctn"><ul class="chat clearfix"></ul></div>');
        var k = {};
        for (var q = 0; q < p.chatItemsByDateAsc.length; q++) {
            k = p.chatItems[p.chatItemsByDateAsc[q]];
            m.find(".chat").append(n.createChatItem(k))
        }
        var o = $('<div class="chat_box" data-playerid="' + l + '"></div>');
        o.append(r);
        o.append(m);
        o.append('<textarea name="text" class="chat_box_textarea"></textarea>');
        return o
    },
    createChatBoxForAssociations: function (r) {
        var n = ogame.chat;
        if (!r) {
            return
        }
        var p = n.data.association[r];
        var k = $('<div class="chat_box_title"></div>');
        k.append('<span class="icon icon_close fright"></span>');
        k.append('<span class="icon icon_maximize fright"></span>');
        var m = $('<div class="chat_box_ctn"><ul class="chat clearfix"></ul></div>');
        var l = {};
        for (var q = 0; q < p.chatItemsByDateAsc.length; q++) {
            l = p.chatItems[p.chatItemsByDateAsc[q]];
            m.find(".chat").append(n.createChatItem(l))
        }
        var o = $('<div class="chat_box" data-associationId="' + r + '"></div>');
        o.append(k);
        o.append(m);
        o.append('<textarea name="text" class="chat_box_textarea"></textarea>');
        return o
    },
    createChatItem: function (m) {
        if (!m) {
            console.warn("no chatItem given");
            return
        }
        var g = $('<div class="msg_head"></div>');
        g.append('<span class="msg_date fright">' + getFormatedDate(m.date, "[d].[m].[Y] <span>[H]:[i]:[s]</span>") + "</span>");
        g.append('<span class="msg_title blue_txt ' + m.newClass + '">' + m.playerName + "</span>");
        var h = $('<li class="chat_msg ' + m.altClass + '" data-chat-id="' + m.chatID + '"></li>');
        h.append(g);
        if (typeof m.refData !== "undefined") {
            var k = $('<div class="referenceMsg"></div>');
            var n = '<div class="refAuthor">' + m.refData.author + "</div>";
            var l = '<div class="refText new">' + m.refData.text + "</div>";
            k.append(n);
            k.append(l);
            h.append(k)
        }
        h.append('<span class="msg_content">' + m.chatContent + "</span>");
        h.append('<div class="speechbubble_arrow"></div>');
        return h
    },
    createMoreBox: function (d) {
        var c = $('<li class="chat_bar_list_item ' + d + '">' + chatLoca.MORE_USERS + '<span class="icon icon_close fright"></span></li>');
        c.prepend($('<div class="chat_box"><ul class="more_items clearfix"></ul></div>'));
        return c
    },
    filterPlayerlist: function () {
        var l = [];
        var h;
        var k = $("#playerlistFilters").find('input[type="checkbox"]');
        k.each(function () {
            l.push($(this).attr("id"))
        });
        $(".playerlist_item").show();
        h = false;
        k.each(function () {
            if ($(this).prop("checked")) {
                h = true
            }
        });
        if (!h) {
            return
        }
        var f;
        var g;
        $(".playerlist_item").filter(function () {
            f = false;
            g = $(this);
            $.each(l, function (b, a) {
                if (g.data(a) === "off" && $("#" + a).prop("checked")) {
                    f = true
                }
            });
            (f === true) ? g.hide() : g.show()
        })
    },
    initChatBar: function (d) {
        var c = ogame.chat;
        ogame.chat.playerId = d;
        $("html").off(".chatBar");
        $(window).resize(function () {
            c.updateChatBar()
        });
        $(".chat_bar_list").on("click.chatBar", "#chatBarPlayerList", function (a) {
            if ($(a.target).attr("id") !== "chatBarPlayerList" && !$(a.target).hasClass("onlineCount")) {
                return
            }
            $(".cb_playerlist_box").toggle();
            c.updateCustomScrollbar($(".scrollContainer"), true);
            console.log('initChatBar()');
            $.ajax({
                url: chatUrl,
                type: "POST",
                dataType: "json",
                data: {action: "toggleChatBar"},
                success: function (b) {
                },
                error: function (h, b, g) {
                }
            })
        }).on("click.chatBar", ".chat_bar_list_item", function (a) {
            a.stopPropagation();
            if (!isNaN($(this).data("playerid"))) {
                ogame.messagemarker.toggle(ogame.messagemarker.action_remove, ogame.messagemarker.type_chattab, $(this).data("playerid"));
                ogame.messagemarker.toggle(ogame.messagemarker.action_remove, ogame.messagemarker.type_chatbar, $(this).data("playerid"));
                c.saveMessageCounter(0, $(this).data("playerid"));
                ogame.messagemarker.setPartnerId($(this).data("playerid"));
                ogame.messagemarker.updateNewMarker();
                ogame.chat.updateTotalNewChatCounter()
            } else {
                if (!isNaN($(this).data("associationid") > 0)) {
                    c.saveMessageCounterAssociation(0, $(this).data("associationid"))
                }
            }
            console.log('chatUrl POST chatBarListRead()');
            $.ajax({
                url: chatUrl,
                type: "POST",
                dataType: "json",
                data: {
                    playerId: $(this).data("playerid"),
                    action: "chatBarListRead"
                },
                success: function (b) {
                },
                error: function (h, b, g) {
                }
            });
            if ($(this).closest(".more_items").length) {
                c.swapChatBarItem($(this))
            } else {
                c.toggleChatBox($(a.target), $(this))
            }
            c.updateVisibleState()
        }).on("click.chatBar", ".chat_bar_list_item > .icon_close", function (a) {
            a.stopPropagation();
            var b = $(this).closest(".chat_bar_list_item");
            ogame.chat.closeChatBox(b.attr("data-playerid"), b.attr("data-associationid"));
            b.remove("open");
            c.updateChatBar()
        }).on("keyup.chatBar", ".chat_box_textarea", function (a) {
            if ((a.ctrlKey || a.keyCode == 10) && a.keyCode == 13) {
                a.preventDefault();
                var b = $(this).val();
                $(this).val(b + "\n")
            } else {
                if ($.trim($(this).val().length > 0)) {
                    a.preventDefault();
                    c.submitChatBarMsg($(a.currentTarget), a.which, a.shiftKey, a.delegateTarget.scrollHeight)
                }
            }
        }).on("click.chatBar", ".chat_box_textarea", function (a) {
            ogame.messagemarker.toggle(ogame.messagemarker.action_remove, ogame.messagemarker.type_chattab, $(this).parent().parent().parent().data("playerid"));
            ogame.messagemarker.toggle(ogame.messagemarker.action_remove, ogame.messagemarker.type_chatbar, $(this).parent().parent().parent().data("playerid"));
            if ($(this).data("playerid") > 0) {
                c.saveMessageCounter(0, $(this).data("playerid"))
            } else {
                if ($(this).data("associationid") > 0) {
                    c.saveMessageCounterAssociation(0, $(this).data("associationid"))
                }
            }
        })
    },
    initPlayerlist: function () {
        var c = ogame.chat;
        var d = ogame.tools;
        $(".js_accordion").accordion({
            collapsible: true,
            heightStyle: "content"
        });
        $(".playerlist_item:odd").addClass("odd");
        d.addHover(".playerlist_item, .msg, .playerlist_top_box .playerlist");
        $(".js_playerlist").on("click.playerList", ".pl_filter_set", function () {
            c.filterPlayerlist()
        });
        c.filterPlayerlist()
    },
    showChat: function (h) {
        var e = false;
        var g = ogame.chat;
        $(".chat_bar_list_item").each(function () {
            var a = $(this);
            if ((h.playerId !== undefined && a.data("playerid") === h.playerId) || (h.associationId !== undefined && a.data("associationid") === h.associationId)) {
                e = true;
                if (a.hasClass("outOfChatbar")) {
                    a.removeClass("outOfChatbar")
                }
                if (!a.hasClass("open")) {
                    a.click();
                    a[0].style.display = "inline"
                } else {
                    a.fadeTo("400", 0.3).fadeTo("400", 1)
                }
                a.find("textarea").focus()
            }
        });
        if (!e) {
            var f;
            if (h.playerId !== undefined) {
                f = g.createChatBarContainer(h.playerId)
            } else {
                f = g.createChatBarContainerForAssociations(h.associationId)
            }
            g.updateChatBar(f)
        }
    },
    showChatHistory: function (e) {
        var d = $(".js_chatHistory");
        var f = e.data;
        if (d.length) {
            d.remove()
        }
        $("#chatList").remove();
        $(f).insertAfter("#planet");
        $("li.playerlist_item").removeClass("active");
        $("li.playerlist_item[data-playerid='" + e.playerId + "']").addClass("active");
        initBBCodeEditor(locaKeys, itemNames, false, ".new_msg_textarea", 2000, true)
    },
    submitChatBarMsg: function (p, l, h, m) {
        var o = ogame.chat;
        var n = parseInt($(".chat_box_textarea").css("max-height"));
        var k = parseInt($(".chat_box_textarea").css("padding-top")) + parseInt($(".chat_box_textarea").css("padding-bottom"));
        if (l === 13 && h) {
            if (m <= (n + k)) {
                p.css("height", m - k)
            }
            return
        }
        if (l === 13) {
            if (p.parent(".chat_box").data("playerid") !== undefined) {
                o.sendMessage(p.parent(".chat_box").data("playerid"), 0, p.val())
            } else {
                if (p.parent(".chat_box").data("associationid") !== undefined) {
                    o.sendMessage(0, p.parent(".chat_box").data("associationid"), p.val())
                }
            }
            p.val("")
        }
    },
    swapChatBarItem: function (d) {
        var f = ogame.chat;
        var e = $(".more_chat_bar_items").prev();
        e.removeClass("open").find(".icon_close").hide().end().find(".chat_box").hide();
        e.remove();
        d.addClass("open").find(".icon_close").show().end().find(".chat_box").show().end().insertBefore(".more_chat_bar_items");
        f.addToMoreBox([e]);
        f.updateChatBar();
        f.updateCustomScrollbar(d.find(".chat_box_ctn"))
    },
    toggleChatBox: function (f, l) {
        var h = ogame.chat;
        if (f.parents(".chat_box").length && !f.hasClass("icon_close")) {
            return
        }
        var k = l.children(".chat_box");
        if (k.is(":visible")) {
            k.hide();
            l.removeClass("open")
        } else {
            if (!l.hasClass("more_chat_bar_items")) {
                l.addClass("open");
                h.updateChatBar()
            }
            k.show();
            var g = k.find(".chat_box_ctn");
            if (l.hasClass("more_chat_bar_items")) {
                g = k
            }
            h.updateCustomScrollbar(g);
            k.find("textarea").focus()
        }
        ogame.messagecounter.resetCounterByType(ogame.messagecounter.type_chat)
    },
    handleTooMuchWindows: function (o, l, u, s, p, r) {
        var q = ogame.chat;
        var n = true;
        var m = [];
        $($(".chat_bar_list > .chat_bar_list_item").get().reverse()).each(function () {
            var a = $(this);
            if (n) {
                if (a.hasClass("more_chat_bar_items") || a.attr("id") === "chatBarPlayerList") {
                    return
                }
                if (a.hasClass("open")) {
                    o--
                } else {
                    l--
                }
                a.removeClass("open").find(".icon_close").hide().end().find(".chat_box").hide();
                m.push(a);
                a.remove();
                widthTotal = s * l + u * o + p;
                n = (widthTotal >= r) ? true : false
            }
        });
        q.addToMoreBox(m)
    },
    getItemFromMorelist2Chatbar: function () {
        var c = $(".more_items .chat_bar_list_item").first().remove();
        var d = ogame.chat;
        c.addClass("open").find(".icon_close").show().end().find(".chat_box").show().end().insertBefore(".more_chat_bar_items");
        if ($(".more_items .chat_bar_list_item").length <= 0) {
            $(".more_chat_bar_items").remove()
        }
        d.updateCustomScrollbar($(".more_chat_bar_items>.chat_box"));
        d.updateCustomScrollbar(c.find(".chat_box_ctn"))
    },
    updateChatBar: function (n) {
        var r = ogame.chat;
        var o = $(".chat_bar_list > .chat_bar_list_item.open").length;
        var v = $(".more_chat_bar_items").length;
        var m = $(".chat_bar_list").children().length - o - v;
        var u = 190;
        var w = 270;
        var q = 190;
        var s = $("body").innerWidth();
        if (n) {
            o++
        }
        var p = u * m + w * o + q * v;
        if (p >= s) {
            r.handleTooMuchWindows(o, m, w, u, q, s)
        } else {
            if ((p + w) <= s && $(".more_chat_bar_items").length > 0) {
                r.getItemFromMorelist2Chatbar()
            }
        }
        if (n) {
            n.insertAfter("#chatBarPlayerList");
            r.updateCustomScrollbar(n.find(".chat_box_ctn"))
        }
    },
    updateCustomScrollbar: function (c, d) {
        if (!c || c.length == 0) {
            return
        }
        if (c.hasClass("mCustomScrollbar")) {
            c.mCustomScrollbar("update")
        } else {
            c.mCustomScrollbar({theme: "ogame"})
        }
        if (d !== true) {
            c.mCustomScrollbar("scrollTo", "bottom", {scrollInertia: 0})
        }
        c.each(function () {
            if ($(this).height() + "px" == $(this).css("max-height")) {
                $(this).addClass("scrollbarPresent")
            }
        })
    },
    updateVisibleState: function () {
        var b = {chatbar: false, players: [], associations: []};
        $(".chat_bar_list>.chat_bar_list_item").each(function () {
            var a = $(this);
            if (a.attr("id") === "chatBarPlayerList" && a.children(".cb_playerlist_box").is(":visible")) {
                b.chatbar = true
            } else {
                if (a.data("playerid") && a.children(".chat_box").is(":visible")) {
                    b.players.push(a.data("playerid"))
                } else {
                    if (a.data("associationid") && a.children(".chat_box").is(":visible")) {
                        b.associations.push(a.data("associationid"))
                    }
                }
            }
        });
        $.cookie("visibleChats", JSON.stringify(b), {expires: 7})
    },
    showPlayerList: function (d) {
        // TODO: this code is part of "0 Contact(s) online." chat system.
        // TODO: re-enable this code when working on this feature. For now its disabled.
        return;
        var c = ogame.chat;
        if ($.inArray(d, c.playerListSelector) === -1) {
            c.playerListSelector.push(d)
        }
        if (c.isLoadingPlayerList === false && c.playerList === null) {
            c.isLoadingPlayerList = true;
            console.log('showPlayerList()');
            $.ajax({
                url: chatUrl,
                type: "POST",
                dataType: "json",
                data: {action: "showPlayerList"},
                success: function (a) {
                    c.playerList = a.content;
                    c.isLoadingPlayerList = false;
                    c._showPlayerList()
                },
                error: function (f, a, b) {
                    c.isLoadingPlayerList = false
                }
            })
        } else {
            c._showPlayerList()
        }
    },
    _showPlayerList: function () {
        var b = ogame.chat;
        $.each(b.playerListSelector, function (a, d) {
            $(d).html(b.playerList)
        })
    }
};