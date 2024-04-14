function reloadEventbox(m) {
    var n;
    if (typeof(m) == "string") {
        n = $.parseJSON(m)
    } else {
        n = m
    }
    var o = typeof n.eventText;
    var l = parseInt(n.friendly) + parseInt(n.neutral) + parseInt(n.hostile);
    if (l > 0) {
        if ($("#eventboxFilled .event_list").length > 0) {
            return
        }
        var k;
        var p = $('<p class="event_list">');
        if ($("body").attr("id") === "galaxy") {
            p.append('<span class="next_event">' + eventboxLoca.nextEvent + ': <span class="countdown" id="tempcounter" name="countdown"></span></span>').append('<span class="next_event">' + eventboxLoca.nextEventText + ': <span class="' + n.eventType + '">' + n.eventText + "</span></span>");
            k = p
        } else {
            var h = (l === 1) ? eventboxLoca.mission : eventboxLoca.missions;
            k = $('<p class="event_list">' + l + " " + h + ": </p>");
            if (n.friendly) {
                k.append('<span class="undermark">' + n.friendly + " " + eventboxLoca.friendly + "</span>")
            }
            if (n.neutral) {
                if (n.friendly) {
                    k.append(", ")
                }
                k.append('<span class="middlemark">' + n.neutral + " " + eventboxLoca.neutral + "</span>")
            }
            if (n.hostile) {
                if (n.friendly || n.neutral) {
                    k.append(", ")
                }
                k.append('<span class="overmark">' + n.hostile + " " + eventboxLoca.hostile + "</span>")
            }
            p.append('<span class="next_event">' + eventboxLoca.nextEvent + ': <span class="countdown" id="tempcounter" name="countdown"></span></span>').append('<span class="next_event">' + eventboxLoca.nextEventText + ': <span class="' + n.eventType + '">' + n.eventText + "</span></span>");
            k.append(p)
        }
        $("#eventboxFilled").prepend(k)
    }
    if (o === "string" || o === "undefined") {
        $("#eventboxLoading").hide();
        if (l > 0) {
            $("#eventboxBlank").hide();
            $("#eventboxFilled").show();
            new simpleCountdown(getElementByIdWithCache("tempcounter"), n.eventTime, function () {
                setTimeout(getAjaxEventbox, 3000)
            })
        } else {
            $("#eventboxBlank").show();
            $("#eventboxFilled").hide()
        }
    }
}
function reloadResources(d, f) {
    var e;
    if (typeof(d) == "string") {
        e = $.parseJSON(d)
    } else {
        e = d
    }
    reloadResourceTicker(e);
    $("#resources_metal").html(e.metal.resources["actualFormat"]);
    $("#resources_metal").attr("class", e.metal["class"]);
    changeTooltip($("#metal_box"), e.metal.tooltip);
    $("#resources_crystal").html(e.crystal.resources["actualFormat"]);
    $("#resources_crystal").attr("class", e.crystal["class"]);
    changeTooltip($("#crystal_box"), e.crystal.tooltip);
    $("#resources_deuterium").html(e.deuterium.resources["actualFormat"]);
    $("#resources_deuterium").attr("class", e.deuterium["class"]);
    changeTooltip($("#deuterium_box"), e.deuterium.tooltip);
    $("#resources_energy").html(e.energy.resources["actualFormat"]);
    $("#resources_energy").attr("class", e.energy["class"]);
    changeTooltip($("#energy_box"), e.energy.tooltip);
    $("#resources_darkmatter").html(e.darkmatter.resources["actualFormat"]);
    $("#resources_darkmatter").attr("class", e.darkmatter["class"]);
    changeTooltip($("#darkmatter_box"), e.darkmatter.tooltip);
    honorScore = e.honorScore;
    darkMatter = e.darkmatter.resources["actual"];
    if (typeof(f) == "function") {
        f(e)
    }
}
function reloadResourceTicker(b) {
    if (typeof(b) == "string") {
        b = $.parseJSON(b)
    }
    resourceTickerMetal = {
        available: b.metal.resources["actual"],
        limit: b.metal.resources["max"],
        production: b.metal.resources["production"],
        valueElem: "resources_metal"
    };
    resourceTickerCrystal = {
        available: b.crystal.resources["actual"],
        limit: b.crystal.resources["max"],
        production: b.crystal.resources["production"],
        valueElem: "resources_crystal"
    };
    resourceTickerDeuterium = {
        available: b.deuterium.resources["actual"],
        limit: b.deuterium.resources["max"],
        production: b.deuterium.resources["production"],
        valueElem: "resources_deuterium"
    };
    if (!vacation) {
        if (typeof(metalTicker) != "undefined") {
            timerHandler.removeCallback(metalTicker.intervalObj)
        }
        if (typeof(crystalTicker) != "undefined") {
            timerHandler.removeCallback(crystalTicker.intervalObj)
        }
        if (typeof(deuteriumTicker) != "undefined") {
            timerHandler.removeCallback(deuteriumTicker.intervalObj)
        }
        metalTicker = new resourceTicker(resourceTickerMetal);
        crystalTicker = new resourceTicker(resourceTickerCrystal);
        deuteriumTicker = new resourceTicker(resourceTickerDeuterium)
    }
}
function reloadRightmenu(b) {
    $.get(b, {}, displayRightmenu)
}
function displayRightmenu(b) {
    $("#rechts").replaceWith(b);
    initPlanetSorting()
}
function ajaxFormSubmit(l, f, k) {
    var h = $("#" + l + "").serialize();
    var g = null;
    if (k != null && typeof k == "function") {
        g = k
    }
    $.ajax({type: "POST", url: f, data: h, success: g})
}
function initConnectionErrorFunction() {
    if (isMobile) {
        document.addEventListener("deviceready", function () {
            $(document).ajaxError(function (h, g, e, f) {
                HostApp.ShowNoConnectionScreen()
            })
        }, false)
    }
}
function initAllianceInfo() {
    initConnectionErrorFunction();
    initTooltips();
    initOverlays();
    initHideElements()
}
function manageTabs(f) {
    var d = "#" + f;
    var e = $(d).attr("rel");
    if ($(d).hasClass("opened")) {
        $(d).addClass("closed");
        $(d).removeClass("opened");
        $("#" + e).hide()
    } else {
        $(d).removeClass("closed");
        $(d).addClass("opened");
        $("#" + e).show();
        $(".alliancetexts").keyup()
    }
}
function ajaxLoad(b) {
    $("#eins select").ogameDropDown("destroy");
    $("#eins").html('<div class="ajaxLoad">' + LocalizationStrings.loading + "</div>");
    console.log('AjaxLoad()');
    $.post(b, {ajax: 1}, function (a) {
        $("#eins").html(a).find("select").ogameDropDown()
    })
}
function initAlliance() {
    initToggleHeader("alliance");
    $(".navi").click(function () {
        var b = $(this).attr("rel");
        $("#tab-ally li").removeClass("aktiv");
        $(this).parent().addClass("aktiv");
        hideTipsOnTabChange();
        ajaxLoad(b)
    })
}
function initAllianceBroadcast() {
    $("#allianceBroadCast").submit(function () {
        var b = $(this);
        if ($.trim(b.find('textarea[name="text"]').val()).length == 0) {
            errorBoxNotify(LocalizationStrings.error, chatLoca.TEXT_EMPTY, LocalizationStrings.ok);
            return false
        }
        $.post(b.attr("action"), b.serialize() + "&ajax=1", function (f) {
            try {
                var a = $.parseJSON(f);
                if (typeof a.message != "undefined") {
                    $('textarea[name="text"]').val("");
                    errorBoxNotify(LocalizationStrings.error, a.message, LocalizationStrings.ok)
                }
            } catch (e) {
                $("#eins").html(f)
            }
        });
        return false
    })
}
function initAllianceManagement() {
    $(".contentz").tabs({activate: hideTipsOnTabChange});
    $(".allyTagChange").tabs({activate: hideTipsOnTabChange});
    $(".quitAlly").tabs({activate: hideTipsOnTabChange});
    $(".dissolve").bind("click", dissolve);
    $(".deleteRank").bind("click", deleteRank);
    $(".transferLeadership").bind("click", transferLeadership);
    $(".takeoverLeadership").bind("click", takeoverLeadership)
}
function initBBCodeEditor(s, H, C, E, D, v) {
    var A;
    if (typeof(E) == "undefined" || E == null) {
        A = $("textarea:not(.markItUpEditor)")
    } else {
        A = $(E).filter(":not(.markItUpEditor)")
    }
    if (A.length == 0) {
        return
    }
    var C = C || false;
    var u = [];
    for (var B = 6; B <= 30; B += 2) {
        u.push({
            name: B,
            openWith: "[size=" + B + "]",
            closeWith: "[/size]",
            placeHolder: "Text",
            className: "fontSize" + B
        })
    }
    var G = [];
    $.each(H, function (a, b) {
        G.push({name: b, replaceWith: "[item]" + a + "[/item]"})
    });
    function J(b) {
        if (b.selection.length == 0) {
            return
        }
        var a = $(b.textarea);
        var e = a.getSelection();
        var g = a.val().indexOf(b.openWith, e.start);
        var c = g + b.openWith.length;
        var f = typeof(b.placeHolder) == "string" ? b.placeHolder.length : 0;
        var d = {start: c, end: c + f};
        a.setSelection(d)
    }

    function I(b) {
        var d = b.openWith;
        var a = $(b.textarea);
        var e = a.getSelection();
        a.siblings(".colorpicker").val("").colorpicker("open").colorpicker("option", "close", function (g, f) {
            var k = d.replace(/%colorCode%/, f.formatted);
            var h = "%colorCode%".length - f.formatted.length;
            e.start = e.start - h;
            e.end = e.end - h;
            a.val(a.val().replace(d, k)).setSelection(e)
        });
        var c = a.siblings(".markItUpHeader").find("." + b.className);
        $(".ui-colorpicker:visible").css("top", c.offset().top + c.height()).css("left", c.offset().left);
        $(".ui-colorpicker").draggable();
        $(".ui-colorpicker-rgb-r .ui-colorpicker-number").focus();
        return b
    }

    function w(a) {
        var f = $(a.textarea);
        var e = f.getSelection();
        var d = a.openWith;
        var b = $("#backgroundImagePicker");
        if (b.is(":visible")) {
            b.dialog("close")
        }
        b.find("input:checked").prop("checked", false);
        b.find(".url").val("http://").focus();
        openOverlay(b, {
            type: "inline",
            title: s.backgroundImage,
            close: function () {
                var g = b.find(".url").val();
                var l = b.find(".repeatX:checked").length;
                var m = b.find(".repeatY:checked").length;
                var h = "";
                if (l && m) {
                    h = "yes"
                } else {
                    if (l) {
                        h = "yes-x"
                    } else {
                        if (m) {
                            h = "yes-y"
                        }
                    }
                }
                if (h.length) {
                    g += " image-repeat=" + h
                }
                var k = d.replace(/%image%/, g);
                var n = "%image%".length - g.length;
                e.start = e.start - n;
                e.end = e.end - n;
                f.val(f.val().replace(d, k)).setSelection(e)
            }
        });
        var c = f.siblings(".markItUpHeader").find("." + a.className);
        b.parent().css("top", c.offset().top + c.height()).css("left", c.offset().left);
        return a
    }

    var y = v ? [{
        name: s.bold,
        key: "B",
        openWith: "[b]",
        closeWith: "[/b]",
        className: "bold"
    }, {
        name: s.italic,
        key: "I",
        openWith: "[i]",
        closeWith: "[/i]",
        className: "italic"
    }, {
        name: s.fontColor,
        afterInsert: I,
        openWith: "[color=%colorCode%]",
        closeWith: "[/color]",
        placeHolder: s.textPlaceHolder,
        className: "fontColor"
    }, {name: s.fontSize, className: "fontSize", dropMenu: u}, {
        name: s.list,
        openWith: "[*]",
        multiline: true,
        openBlockWith: "[list]\n",
        closeBlockWith: "\n[/list]",
        className: "list",
        afterMultiInsert: J,
        placeHolder: s.textPlaceHolder
    }, {
        name: s.coordinates,
        openWith: "[coordinates]",
        closeWith: "[/coordinates]",
        placeHolder: s.coordinatePlaceHolder,
        className: "coordinates"
    }] : [{
        name: s.bold,
        key: "B",
        openWith: "[b]",
        closeWith: "[/b]",
        className: "bold"
    }, {
        name: s.italic,
        key: "I",
        openWith: "[i]",
        closeWith: "[/i]",
        className: "italic"
    }, {
        name: s.fontColor,
        afterInsert: I,
        openWith: "[color=%colorCode%]",
        closeWith: "[/color]",
        placeHolder: s.textPlaceHolder,
        className: "fontColor"
    }, {name: s.fontSize, className: "fontSize", dropMenu: u}, {
        name: s.list,
        openWith: "[*]",
        multiline: true,
        openBlockWith: "[list]\n",
        closeBlockWith: "\n[/list]",
        className: "list",
        afterMultiInsert: J,
        placeHolder: s.textPlaceHolder
    }, {
        name: s.link,
        key: "L",
        openWith: "[url=[![" + s.link + ":!:http://]!]]",
        closeWith: "[/url]",
        placeHolder: s.textPlaceHolder,
        className: "link"
    }, {
        name: s.coordinates,
        openWith: "[coordinates]",
        closeWith: "[/coordinates]",
        placeHolder: s.coordinatePlaceHolder,
        className: "coordinates"
    }];
    var F = v ? [{
        name: s.underline,
        key: "U",
        openWith: "[u]",
        closeWith: "[/u]",
        className: "underline"
    }, {
        name: s.stroke,
        key: "S",
        openWith: "[s]",
        closeWith: "[/s]",
        className: "strikeThrough"
    }, {
        name: s.sub,
        openWith: "[sub]",
        closeWith: "[/sub]",
        className: "sub"
    }, {
        name: s.sup,
        openWith: "[sup]",
        closeWith: "[/sup]",
        className: "sup"
    }, {separator: "-"}, {
        name: s.item,
        className: "item",
        dropMenu: G
    }, {
        name: s.player,
        openWith: "[player]",
        closeWith: "[/player]",
        placeHolder: s.playerPlaceHolder,
        className: "player"
    }, {separator: "-"}, {
        name: s.alignLeft,
        openWith: "[align=left]",
        closeWith: "[/align]",
        className: "leftAlign"
    }, {
        name: s.alignCenter,
        openWith: "[align=center]",
        closeWith: "[/align]",
        className: "centerAlign"
    }, {
        name: s.alignRight,
        openWith: "[align=right]",
        closeWith: "[/align]",
        className: "rightAlign"
    }, {
        name: s.alignJustify,
        openWith: "[align=justify]",
        closeWith: "[/align]",
        className: "justifyAlign"
    }, {separator: "-"}, {
        name: s.code,
        openWith: "[code]",
        closeWith: "[/code]",
        className: "code"
    }, {separator: "-"}, {
        name: s.email,
        key: "E",
        openWith: "[email=[![" + s.email + ":!:]!]]",
        closeWith: "[/email]",
        placeHolder: s.textPlaceHolder,
        className: "email"
    }, {
        name: s.preview,
        className: "preview",
        call: "preview"
    }] : [{
        name: s.underline,
        key: "U",
        openWith: "[u]",
        closeWith: "[/u]",
        className: "underline"
    }, {
        name: s.stroke,
        key: "S",
        openWith: "[s]",
        closeWith: "[/s]",
        className: "strikeThrough"
    }, {
        name: s.sub,
        openWith: "[sub]",
        closeWith: "[/sub]",
        className: "sub"
    }, {
        name: s.sup,
        openWith: "[sup]",
        closeWith: "[/sup]",
        className: "sup"
    }, {
        name: s.backgroundColor,
        afterInsert: I,
        openWith: "[background color=%colorCode%]",
        closeWith: "[/background]",
        placeHolder: s.textPlaceHolder,
        className: "backgroundColor"
    }, {
        name: s.backgroundImage,
        afterInsert: w,
        openWith: "[background image=%image%]",
        closeWith: "[/background]",
        placeHolder: s.textPlaceHolder,
        className: "backgroundImage"
    }, {separator: "-"}, {
        name: s.item,
        className: "item",
        dropMenu: G
    }, {
        name: s.player,
        openWith: "[player]",
        closeWith: "[/player]",
        placeHolder: s.playerPlaceHolder,
        className: "player"
    }, {
        name: s.tooltip,
        openWith: '[tooltip position="top" text="[![Tooltip Text:!:Tooltip Text]!]"]',
        closeWith: "[/tooltip]",
        placeHolder: s.textPlaceHolder,
        className: "tooltip"
    }, {separator: "-"}, {
        name: s.alignLeft,
        openWith: "[align=left]",
        closeWith: "[/align]",
        className: "leftAlign"
    }, {
        name: s.alignCenter,
        openWith: "[align=center]",
        closeWith: "[/align]",
        className: "centerAlign"
    }, {
        name: s.alignRight,
        openWith: "[align=right]",
        closeWith: "[/align]",
        className: "rightAlign"
    }, {
        name: s.alignJustify,
        openWith: "[align=justify]",
        closeWith: "[/align]",
        className: "justifyAlign"
    }, {separator: "-"}, {
        name: s.block,
        openWith: "[p]",
        closeWith: "[/p]",
        className: "block"
    }, {
        name: s.code,
        openWith: "[code]",
        closeWith: "[/code]",
        className: "code"
    }, {
        name: s.spoiler,
        openWith: "[spoiler]",
        closeWith: "[/spoiler]",
        className: "spoiler"
    }, {separator: "-"}, {
        name: s.hr,
        openWith: "[hr]",
        className: "hr"
    }, {separator: "-"}, {
        name: s.picture,
        key: "Z",
        replaceWith: "[img][![" + s.picture + ":!:http://]!][/img]",
        className: "picture"
    }, {
        name: s.email,
        key: "E",
        openWith: "[email=[![" + s.email + ":!:]!]]",
        closeWith: "[/email]",
        placeHolder: s.textPlaceHolder,
        className: "email"
    }, {name: s.preview, className: "preview", call: "preview"}];
    if (!C) {
        $.each(F, function (a) {
            if (this.className == "picture" || this.className == "backgroundImage") {
                F.splice(a, 1)
            }
        })
    }
    if (isMobile) {
        $.each(y, function (a) {
            if (this.className == "fontColor" || this.className == "backgroundColor") {
                y.splice(a, 1)
            }
        });
        $.each(F, function (a) {
            if (this.className == "fontColor" || this.className == "backgroundColor") {
                F.splice(a, 1)
            }
        })
    }
    var x = {
        onShiftEnter: {keepDefault: false, replaceWith: "\n"},
        onCtrlEnter: {keepDefault: false, openWith: "\n[p]", closeWith: "[/p]"},
        onTab: {keepDefault: false, replaceWith: "\t"},
        markupSet: [y, F],
        resizeHandle: false,
        previewParserPath: bbcodePreviewUrl + "&imgAllowed=" + (C ? 1 : 0),
        previewAutoRefresh: true,
        previewParserVar: "text",
        previewInElement: $('<div class="miu_preview_container"></div>'),
        afterInsert: function (a) {
            $(a.textarea).trigger("keyup")
        }
    };
    $.colorpicker.regional.custom = s.colorPicker;
    A.each(function () {
        var a = $(this);
        a.markItUp(x).after($('<input type="hidden" class="colorpicker"/>').colorpicker({
            color: "#000000",
            colorFormat: "#HEX",
            hsv: false,
            parts: "popup",
            regional: "custom",
            showCancelButton: false
        }));
        x.previewInElement.insertAfter(a);
        $(".miu_advanced .preview").hide();
        var b = $('<div class="miu_footer clearfix"></div>');
        b.append('<a role="button" class="fright txt_link preview_link">' + s.preview + "</a>").append('<span class="fleft"><span class="cnt_chars">' + D + "</span> " + s.charsLeft + "</span>");
        b.insertAfter(a);
        a.on("keyup.bbCodeEditor", function () {
            if (a.val().length > D) {
                a.val(a.val().substr(0, D))
            }
            a.closest(".markItUpContainer").find(".cnt_chars").html(D - a.val().length)
        });
        $(".miu_preview_container").hide();
        $(".preview_link").on("click.bbCodeEditor", function () {
            $(".miu_preview_container").show();
            $(".preview").click()
        });
        if (isMobile) {
            a.siblings(".markItUpHeader").find("a").attr("title", "");
            a.siblings(".markItUpHeader").find("li:not(.markItUpDropMenu) a").bind("mouseup", function () {
                a.focus()
            })
        } else {
            a.siblings(".markItUpHeader").find("ul ul a").attr("title", "")
        }
        if ($("#backgroundImagePicker").length == 0) {
            $("body").append($('<div id="backgroundImagePicker" style="display: none;"><table><tr><td>' + s.backgroundImage + ':</td><td><input type="text" class="url"/></td></tr><tr><td>' + s.backgroundImagePicker.repeatX + ':</td><td><input type="checkbox" class="repeatX"/></td></tr><tr><td>' + s.backgroundImagePicker.repeatY + ':</td><td><input type="checkbox" class="repeatY"/></td></tr></table><div><a href="javascript:void(0);" class="btn_blue">' + s.backgroundImagePicker.ok + "</a></div></div>"));
            $("#backgroundImagePicker").find("a").bind("click", function () {
                $("#backgroundImagePicker").dialog("close")
            })
        }
    });
    $(".toggle_miu_advanced").on("click.bbCodeEditor", function () {
        if ($(".miu_advanced").is(":visible")) {
            $(".miu_advanced").hide();
            $(this).removeClass("hide_miu_advanced").addClass("show_miu_advanced")
        } else {
            $(".miu_advanced").show();
            $(this).removeClass("show_miu_advanced").addClass("hide_miu_advanced")
        }
    })
}
function initBBCodes() {
    $(document).undelegate(".spoilerHeader", "click").delegate(".spoilerHeader", "click", function () {
        var b = this;
        $(this).next(".spoilerText").toggle(0, function () {
            Tipped.refresh(b)
        })
    })
}
ogame.buddies = {
    initBuddyList: function () {
        $.tablesorter.addParser({
            id: "coordinates", is: function (b) {
                return false
            }, format: function (f) {
                var d = $.trim(f).slice(1, -1).split(":");
                var e = (d[0] * 16384) + (d[1] * 32) + d[2] * 1;
                return e
            }, type: "numeric"
        });
        $.tablesorter.addParser({
            id: "commaDigit", is: function (e, c) {
                var f = c.config;
                return jQuery.tablesorter.isDigit(e.replace(/\./g, ""), f)
            }, format: function (b) {
                return jQuery.tablesorter.formatFloat(b.replace(/\./g, ""))
            }, type: "numeric"
        });
        $(".content_table").tablesorter({
            widgets: ["zebra"],
            headers: {
                0: {sorter: false},
                2: {sorter: "commaDigit"},
                5: {sorter: "coordinates"},
                6: {sorter: false}
            },
            cssHeader: "ct_sortable_title",
            cssAsc: "ct_sort_asc",
            cssDesc: "ct_sort_desc"
        });
        $("#buddylist").off(".buddyList");
        $("#buddylist").on("click.buddyList", ".deleteBuddy", deleteBuddy);
        $("#chatContent").on("click.chat_info", ".deleteBuddy", deleteBuddy)
    }, initBuddies: function () {
        $(".zebra tr").mouseover(function () {
            $(this).addClass("over")
        }).mouseout(function () {
            $(this).removeClass("over")
        });
        $(".js_accordion").accordion({
            collapsible: true,
            heightStyle: "content"
        });
        $(".js_tabs").tabs();
        $(".js_scrollbar").mCustomScrollbar({theme: "ogame"})
    }
};
function initInviteCodes() {
    $("#buddyinvite .code").click(function () {
        $(this).selectText()
    })
}
function incrementBuddyCount() {
    var c = parseInt($("#buddyCount").text()) + 1;
    var d = parseInt($("#newRequestCount").text()) - 1;
    updateRequestCount(d);
    updateBuddyCount(c)
}
function updateRequestCount(b) {
    $("#newRequestCount").html(b)
}
function updateBuddyCount(b) {
    $("#buddyCount").html(b)
}
function reportMessage(f, d, e) {
    $.ajax({
        type: "POST",
        url: "?page=reportSpam_ajax",
        dataType: "json",
        data: {messageId: f, from: d, to: e},
        success: function (a) {
            fadeBox(a.message, !a.result)
        },
        error: function () {
        }
    })
}
function reportAllyMessage(c, d) {
    $.ajax({
        type: "POST",
        url: "?page=reportSpam_ajax",
        dataType: "json",
        data: {messageId: c, from: d},
        success: function (a) {
            fadeBox(a.message, !a.result)
        },
        error: function () {
        }
    })
}
var elem, messageId, senderId;
function reportAllyRoundMessage(h, e, g, f) {
    elem = h;
    messageId = e;
    senderId = g;
    errorBoxDecision(LocalizationStrings.attention, f, LocalizationStrings.yes, LocalizationStrings.no, reportMessageCallback)
}
function reportMessageCallback() {
    elem.hide();
    reportAllyMessage(messageId, senderId)
}
function requestsReady() {
    $(document).on("click", ".acceptRequest", acceptRequest);
    $(document).on("click", ".rejectRequest", rejectRequest);
    $(document).on("click", ".cancelRequest", cancelRequest);
    $(document).on("click", ".reportRequest", reportRequest)
}
function initBuddyRequestForm() {
    $(".overlayDiv .buddyRequest form").unbind("submit").bind("submit", function (c) {
        var d = $(this);
        c.preventDefault();
        $.post(d.attr("action"), d.serialize(), "html").done(function (a) {
            document.open();
            document.write(a);
            document.close()
        }).fail(function () {
            var a = window.location.href;
            window.location = a.substring(0, a.indexOf("?")) + "?page=buddies"
        }).always(function () {
            return false
        })
    });
    $(".buddyRequest").each(function () {
        var c = $(this);
        var d = c.parents(".ui-dialog").find(".ui-dialog-title");
        if (!d.find("span.buddyName").length) {
            d.append($(document.createElement("span")).addClass("buddyName").text(" (" + c.attr("data-title") + ")"))
        }
    })
}
function initBuyResourceOverlay(g, e, h) {
    $(".close_buyResourceOverlay").on("click", function () {
        $("#buyResourceOverlayBody").closest(".ui-dialog").find(".ui-icon-closethick").click()
    });
    var f = function () {
        if (g && e) {
            errorBoxDecision(h.allNetworkAttention, h.slotWarning, h.allYes, h.allNo, sendDMAcceptanceForm)
        } else {
            sendDMAcceptanceForm()
        }
    };
    $("#premiumConfirmButton").on("click", function (a) {
        a.preventDefault();
        f()
    });
    $(document).on("ajaxShowOverlay", function (a) {
        $("#premiumConfirmButton").focus()
    });
    $("#premiumConfirmButton").on("keyup", function (a) {
        a.stopPropagation();
        if (a.keyCode == 13) {
            f()
        }
    })
}
function sendDMAcceptanceForm() {
    $("#premiumAcceptForm").submit();
    return
}
function openBuyResourceDialog(b) {
    openOverlay(b, {"class": "buyResourceDialog"})
}
function abortBuyResource() {
    $(".overlaydiv .abort_button").on("click", function () {
        $(".overlaydiv").dialog("close")
    })
}
function initBuyResources() {
    refreshBars("bar_container", "filllevel_bar");
    $(".fill_resource").on("click", ".fillup", onChangeToPremium).on("click", ".btn_premium", submitBuyRequest)
}
function onChangeToPremium(g) {
    var f = $(g.currentTarget).find(".btn_blue"), h = f.closest(".fillup"), e = f.closest(".fill_resource_ctn").find(".premium_bar");
    $(".fillup").removeClass("premium").parent().find(".current_stock span").removeClass("premium_txt").each(function () {
        var a = $(this);
        a.text(a.data("currentAmount"))
    });
    $(".fill_resource .btn_premium").html(loca.fillUpResource).attr("class", "btn_blue");
    $(".premium_bar").css("width", "0%").data("premiumPercent", 0);
    if (f.attr("disabled") == "disabled") {
        return
    }
    f.html(loca.buyNow).attr("class", "btn_premium small");
    h.addClass("premium");
    h.parent().find(".current_stock span").addClass("premium_txt").text(f.data("newValueFormatted"));
    e.data("premiumPercent", f.data("premiumPercent"));
    changeTooltip(e, "+" + tsdpkt(Math.floor(f.data("premiumValue"))));
    refreshBars("bar_container", "filllevel_bar", "premium_bar")
}
function submitBuyRequest(l) {
    var n = $(l.currentTarget), m = n.data("resource"), e = n.data("premiumCosts"), k = n.data("packageType"), h = n.data("traderBuyResource");
    $.ajax({
        url: "index.php?page=traderOverview",
        data: {buyResource: m, buyPackage: k, costs: e, token: h, ajax: 1},
        type: "POST",
        dataType: "json",
        success: function (a) {
            refreshToken = function (b) {
                $(".btn_blue").each(function () {
                    $(this).data("traderBuyResource", b)
                })
            };
            refreshToken(a.buyResourceToken);
            if (!a.success) {
                fadeBox(a.message, true);
                return
            }
            getAjaxResourcebox(function (b) {
                $(".fill_resource .bar_container").each(function () {
                    var c = $(this);
                    c.data("currentAmount", b[c.data("resource")]["resources"]["actual"]);
                    c.parent().find(".current_stock span").html(a[c.data("resource")]).data("currentAmount", a[c.data("resource")]);
                    refreshBars("fill_resource .bar_container", "filllevel_bar")
                })
            });
            $(".fill_resource .fillup").each(function () {
                var c = $(this);
                var d = c.find("a");
                var g = d.data("resource");
                var b = d.data("packageType");
                if (!a.possiblePackages[g][b]["isBuyable"]) {
                    d.attr("disabled", "disabled");
                    c.attr("disabled", "disabled");
                    c.addClass("disabled")
                }
                d.data("premiumCosts", a.possiblePackages[g][b]["costs"]);
                d.data("premiumValue", a.possiblePackages[g][b]["resources"]);
                d.data("newValueFormatted", a.possiblePackages[g][b]["newValueFormatted"]);
                if (a.possiblePackages[g][b]["displayCosts"]) {
                    c.find(".fillup_cost .premium_txt").html(a.possiblePackages[g][b]["formattedCosts"])
                } else {
                    c.find(".fillup_cost").addClass("overmark").html("-")
                }
                if (b == 3) {
                    var f = d.data("buyButtonClass");
                    if (!a.possiblePackages[g][b]["isCapped"] && c.children("." + f).length) {
                        c.children("." + f).removeClass(f).addClass("fillup_100percent");
                        c.children(".fillup_txt").html(loca.fillUpTo)
                    }
                }
            })
        },
        error: function () {
        }
    })
}
function countdown(l, g, f) {
    if (g == null || g == "") {
        g = 2
    }
    var h = this;
    h.countValue = parseInt(f) || -1;
    h.timestamp = 0;
    h.maxDigits = parseInt(g);
    h.delimiter = " ";
    h.approx = "";
    h.showunits = true;
    h.zerofill = false;
    var k = new Date();
    h.startTime = k.getTime();
    h.startLeftoverTime = parseInt(l);
    this.getCurrentTimestring = function () {
        return h.formatTime(h.getLeftoverTime())
    };
    this.getLeftoverTime = function () {
        var a = new Date();
        return Math.round((h.startLeftoverTime + ((a.getTime() - h.startTime) * h.countValue) / 1000))
    };
    this.formatTime = function (a) {
        return formatTimeWrapper(a, h.maxDigits, h.showunits, h.delimiter, h.zerofill, h.approx)
    }
}
function formatTimeWrapper(o, u, q, v, s, n) {
    var k = [];
    k.week = 604800;
    k.day = 86400;
    k.hour = 3600;
    k.minute = 60;
    k.second = 1;
    var w = "";
    for (var r in k) {
        var p = Math.floor(o / k[r]);
        if (u > 0 && (p > 0 || s && w != "")) {
            o = o - p * k[r];
            if (w != "") {
                w += v;
                if (p < 10 && p > 0 && s) {
                    p = "0" + p
                }
                if (p == 0) {
                    p = "00"
                }
            }
            w += p + (q ? LocalizationStrings.timeunits["short"][r] : "");
            u--
        }
    }
    if (o > 0) {
        w = n + w
    }
    return w
}
function bauCountdown(k, l, f, g) {
    if (typeof(k) == "object") {
        var h = this;
        h.totalTime = f;
        h.startHeight = k.offsetHeight;
        h.htmlObj = $(k);
        h.timeHtmlObj = h.htmlObj.siblings(".timeLink").find(".time");
        this.updateCountdown = function () {
            h.countdown.getCurrentTimestring();
            timestamp = h.countdown.getLeftoverTime();
            timestring = h.countdown.getCurrentTimestring();
            h.timeHtmlObj.text(timestring);
            var a = Math.max(0, timestamp) / h.totalTime;
            if (a > 0) {
                var b = Math.round(h.startHeight * a);
                h.htmlObj.css({
                    height: b + "px",
                    marginTop: (h.startHeight - b) + "px"
                })
            } else {
                h.timeHtmlObj.text(LocalizationStrings.status.ready);
                h.htmlObj.css({height: h.startHeight + "px", marginTop: 0});
                if (timestamp <= -1 && timestamp > -10 && !isOverlayOpen()) {
                    reload_page(g)
                }
            }
        };
        if (h.timeHtmlObj.length) {
            h.countdown = new countdown(l);
            timerHandler.appendCallback(h.updateCountdown);
            h.updateCountdown()
        } else {
            window.status = "kein timeHtmlObj"
        }
    }
}
function schiffbauCountdown(o, k, m, q, r, l) {
    if (typeof(o) == "object") {
        var n = this;
        n.totalTime = r;
        n.oneShipTime = r;
        n.shipCount = k;
        n.currentShips = m;
        n.startHeight = o.offsetHeight;
        n.htmlObj = $(o);
        var p = n.htmlObj.siblings(".timeLink");
        n.timeHtmlObj = p.find(".time");
        n.countHtmlObj = $("#shipcount");
        n.shipsHtmlObj = $("#bestand");
        this.updateCountdown = function () {
            n.countdown.getCurrentTimestring();
            timestamp = n.countdown.getLeftoverTime();
            timestring = n.countdown.getCurrentTimestring();
            n.timeHtmlObj.text(timestring);
            var a = Math.max(0, timestamp) / n.totalTime;
            if (a > 0) {
                var b = Math.round(n.startHeight * a);
                n.htmlObj.css({
                    height: b + "px",
                    marginTop: (n.startHeight - b) + "px"
                })
            } else {
                if (n.shipCount > 0) {
                    n.shipCount--;
                    n.currentShips++
                }
                if (n.shipCount >= 0) {
                    n.countHtmlObj.text(n.shipCount);
                    $("#shipcount").text(n.shipCount)
                }
                n.shipsHtmlObj.text(gfNumberGetHumanReadable(n.currentShips));
                if (n.shipCount > 0) {
                    n.countdown = new countdown(r);
                    n.timeHtmlObj.text("-")
                } else {
                    if (timestamp <= -1 && timestamp > -10) {
                        if (l != null && timestamp > -10 && !isOverlayOpen()) {
                            reload_page(l)
                        }
                        timerHandler.removeCallback(n.timer)
                    }
                    n.timeHtmlObj.text(LocalizationStrings.status.ready)
                }
            }
        };
        if (n.timeHtmlObj && n.countHtmlObj && n.shipsHtmlObj) {
            totalTime = Math.floor(k * r);
            n.countdown = new countdown(q);
            n.timer = timerHandler.appendCallback(n.updateCountdown);
            n.updateCountdown()
        } else {
            window.status = "kein: timeHtmlObj oder countHtmlObj oder shipsHtmlObj"
        }
    }
}
function baulisteCountdown(k, f, g, l) {
    if (typeof(k) == "object") {
        var h = this;
        h.timeHtmlObj = k;
        h.updateCountdown = function () {
            var a = h.countdown.getLeftoverTime();
            if (a > 0) {
                $(h.timeHtmlObj).html(h.countdown.getCurrentTimestring())
            } else {
                $(h.timeHtmlObj).html(LocalizationStrings.status.ready);
                if (typeof(l) == "function") {
                    l()
                }
                if (a <= -1 && a > -10) {
                    if (g != null && a > -10 && !isOverlayOpen()) {
                        reload_page(g)
                    }
                    timerHandler.removeCallback(h.timer)
                }
            }
        };
        if (h.timeHtmlObj) {
            h.countdown = new countdown(f, 3);
            h.timer = timerHandler.appendCallback(h.updateCountdown);
            h.updateCountdown()
        }
    }
}
function eventboxCountdown(l, n, h, m, g) {
    if (typeof(l) == "object") {
        var k = this;
        k.timeHtmlObj = l;
        this.updateCountdown = function () {
            k.countdown.getCurrentTimestring();
            var a = k.countdown.getLeftoverTime();
            var b = k.countdown.getCurrentTimestring();
            if (a > 0) {
                $(k.timeHtmlObj).html(b)
            } else {
                timerHandler.removeCallback(k.timer);
                $(k.timeHtmlObj).html(LocalizationStrings.status.ready);
                if (!timerHandler.checkEventsAlreadyQueued) {
                    timerHandler.checkEventsAlreadyQueued = true;
                    setTimeout(function () {
                        console.log('eventboxCountdown()');
                        $.post(m, {ids: g}, function (c) {
                            var e = $.parseJSON(c);
                            for (var d in e.rows) {
                                $(h).find("#eventRow-" + e.rows[d]).remove();
                                $(".union" + e.rows[d]).remove()
                            }
                            $(".eventFleet").removeClass("odd");
                            $(".partnerInfo").removeClass("part-even");
                            $(".eventFleet:odd").addClass("odd");
                            $(".partnerInfo:even").addClass("part-even");
                            timerHandler.checkEventsAlreadyQueued = false
                        })
                    }, 2500)
                }
            }
        };
        if (k.timeHtmlObj) {
            k.countdown = new countdown(n, 3);
            k.timer = timerHandler.appendCallback(k.updateCountdown);
            k.updateCountdown()
        }
    }
}
function simpleCountdown(k, g, l, f) {
    if (typeof(k) == "object") {
        var h = this;
        h.timeHtmlObj = $(k)[0];
        this.updateCountdown = function () {
            var a = h.countdownObject.getLeftoverTime();
            var b = h.countdownObject.getCurrentTimestring();
            if (a > 0) {
                $("#" + h.timeHtmlObj.id).text(b);
                if (typeof f == "string" && $.isFunction(window[f])) {
                    window[f]()
                } else {
                    if ($.isFunction(f)) {
                        f()
                    }
                }
            } else {
                timerHandler.removeCallback(h.timer);
                $("#" + h.timeHtmlObj.id).text(LocalizationStrings.status.ready);
                if (typeof l == "string" && $.isFunction(window[l])) {
                    window[l]()
                } else {
                    if ($.isFunction(l)) {
                        l()
                    }
                }
            }
        };
        if (typeof(h.timer) != "undefined") {
            timerHandler.removeCallback(h.timer)
        }
        if (h.timeHtmlObj) {
            h.countdownObject = new countdown(g, 3);
            h.timer = timerHandler.appendCallback(h.updateCountdown);
            h.updateCountdown()
        }
    }
}
function countdownWithTickFunction(m, p, h, n, o, k) {
    if (typeof(m) == "object") {
        var l = this;
        l.timeHtmlObj = m;
        if (typeof($(m).attr("data-countdown")) != "undefined") {
            timerHandler.removeCallback($(m).attr("data-countdown"))
        }
        this.updateCountdown = function () {
            timestamp = l.countdown.getLeftoverTime();
            timestring = l.countdown.getCurrentTimestring();
            if (timestamp > 0) {
                l.timeHtmlObj.innerHTML = timestring;
                if (typeof o == "string" && $.isFunction(window[o])) {
                    window[o](timestamp, h)
                } else {
                    if ($.isFunction(o)) {
                        o(timestamp, h)
                    }
                }
            } else {
                timerHandler.removeCallback(l.timer);
                l.timeHtmlObj.innerHTML = LocalizationStrings.status.ready;
                if (typeof n == "string" && $.isFunction(window[n])) {
                    window[n]()
                } else {
                    if ($.isFunction(n)) {
                        n()
                    }
                }
            }
        };
        if (l.timeHtmlObj) {
            l.countdown = new countdown(p, k);
            l.timer = timerHandler.appendCallback(l.updateCountdown);
            l.updateCountdown();
            $(m).attr("data-countdown", l.timer)
        }
        return l
    }
}
function reloadCountdown(h, e, f) {
    if (typeof(h) == "object") {
        var g = this;
        g.timeHtmlObj = h;
        this.updateCountdown = function () {
            g.countdown.getCurrentTimestring();
            timestamp = g.countdown.getLeftoverTime();
            timestring = g.countdown.getCurrentTimestring();
            if (timestamp > 0) {
                g.timeHtmlObj.innerHTML = timestring
            } else {
                g.timeHtmlObj.innerHTML = LocalizationStrings.status.ready;
                if (timestamp <= -2 && timestamp > -10 && !isOverlayOpen()) {
                    reload_page(f)
                }
            }
        };
        if (g.timeHtmlObj) {
            g.countdown = new countdown(e, 3);
            timerHandler.appendCallback(g.updateCountdown);
            g.updateCountdown()
        }
    }
}
function movementImageCountdown(m, p, n, k, h, o) {
    if (typeof(m) == "object") {
        var l = this;
        l.timeHtmlObj = m;
        this.updateCountdown = function () {
            l.countdown.getCurrentTimestring();
            var a = l.countdown.getLeftoverTime();
            var b = l.countdown.getCurrentTimestring();
            if (a > 0) {
                percent = 100 - a / (n / 100);
                if (!k) {
                    pixel = Math.abs((o / 100) * percent)
                } else {
                    pixel = (o * 2) - Math.abs(((o * 2) / 100) * percent)
                }
                pixel = Math.round(pixel);
                if (h) {
                    l.timeHtmlObj.style.marginRight = pixel + "px"
                } else {
                    l.timeHtmlObj.style.marginLeft = pixel + "px"
                }
            }
        };
        if (l.timeHtmlObj) {
            l.countdown = new countdown(p, 3);
            timerHandler.appendCallback(l.updateCountdown);
            l.updateCountdown()
        }
    }
}
function shipCountdown(r, o, l, q, u, m, n, p) {
    if ((typeof(r) == "object") && (typeof(o) == "object") && (typeof(l) == "object")) {
        var s = this;
        s.totalTimeHtmlObj = r;
        s.unitTimeHtmlObj = o;
        s.sumCountHtmlObj = l;
        this.updateCountdown = function () {
            s.unitCountdown.getCurrentTimestring();
            unitTimestamp = s.unitCountdown.getLeftoverTime();
            unitTimestring = s.unitCountdown.getCurrentTimestring();
            s.totalCountdown.getCurrentTimestring();
            totalTimestamp = s.totalCountdown.getLeftoverTime();
            totalTimestring = s.totalCountdown.getCurrentTimestring();
            if (unitTimestamp > 0) {
                s.unitTimeHtmlObj.innerHTML = unitTimestring
            } else {
                n--;
                s.unitTimeHtmlObj.innerHTML = LocalizationStrings.status.ready;
                if (n > 0) {
                    s.unitCountdown = new countdown(q);
                    s.sumCountHtmlObj.innerHTML = n
                } else {
                    s.sumCountHtmlObj.innerHTML = 0
                }
            }
            if (n > 0) {
                s.totalTimeHtmlObj.innerHTML = totalTimestring
            } else {
                s.totalTimeHtmlObj.innerHTML = LocalizationStrings.status.ready;
                if (!isOverlayOpen()) {
                    reload_page(p)
                }
            }
        };
        if ((s.totalTimeHtmlObj) && (s.unitTimeHtmlObj) && (s.sumCountHtmlObj)) {
            s.totalCountdown = new countdown(m);
            s.unitCountdown = new countdown(u);
            timerHandler.appendCallback(s.updateCountdown);
            s.updateCountdown()
        }
    }
}
function reload_page(b) {
    if (timerHandler && timerHandler.pageReloadAlreadyTriggered == false) {
        timerHandler.pageReloadAlreadyTriggered = true;
        openParentLocation(b)
    }
}
function initItemActivation(c) {
    inventoryObj.initalizeSlider(inventoryObj.items_inventory, "js_activeItemSlider", 395, 172, " ", true, " ", true, true);
    if (typeof(c) == "undefined" || c.length == 0 || c == 1) {
        var d = $(".item_img_box .detail_button").filter(":first");
        if (d.length === 0) {
            $("#noItems").show();
            $("#itemDetailBox").hide();
            return
        } else {
            $.bbq.pushState({item: d.attr("ref")})
        }
    } else {
        $.bbq.pushState({item: c});
        $(window).trigger("hashchange")
    }
    $("#activeBuffDetails .js_is_active").each(function () {
        $durationEl = $(this).parent().siblings(".js_duration");
        $pusherEl = $(this).parent().siblings(".pusher");
        startCooldown($durationEl, $pusherEl, 75)
    });
    $(document).undelegate("#activeBuffDetails .detail_button", "click.updateItemDetails").delegate("#activeBuffDetails .detail_button", "click.updateItemDetails", function () {
        if ($(this).hasClass("active")) {
            return
        }
        $("#activeBuffDetails .detail_button").removeClass("active");
        $(this).addClass("active");
        $.bbq.pushState({item: $(this).attr("ref")})
    }).undelegate("#activeBuffDetails .build-it", "click.activateItem").delegate("#activeBuffDetails .build-it", "click.activateItem", function () {
        activateItem($(this).attr("ref"))
    }).undelegate("#activeBuffDetails .buyAndActivate.dm.build-it_disabled", "click.activateItem").delegate("#activeBuffDetails .buyAndActivate.dm.build-it_disabled", "click.activateItem", function () {
        if (vacation) {
            return
        }
        var a = $(this).attr("ref");
        if ($("#js_activeItemSlider>li a[ref='" + a + "']").length == 0) {
            return
        }
        errorBoxDecision(LocalizationStrings.error, translation.buyDMDecision, LocalizationStrings.yes, LocalizationStrings.no, function () {
            window.location.href = $("#darkmatter_box a").attr("href")
        })
    }).undelegate("#activeBuffDetails .close_detail", "click.changeHash").delegate("#activeBuffDetails .close_detail", "click.changeHash", function () {
        $.bbq.pushState({item: ""})
    })
}
function initBuffBar() {
    $(".sliderWrapper .active_items").anythingSlider({
        resizeContents: false,
        buildNavigation: false,
        buildStartStop: false,
        infiniteSlides: false,
        stopAtEnd: true
    });
    $(".sliderWrapper .active_items").removeClass("hidden");
    $(window).unbind("hashchange.openBuffBar").bind("hashchange.openBuffBar", function (k) {
        var l = $.deparam.fragment(k.fragment);
        if (typeof(l.item) != "undefined") {
            var f = $("#buffBar .activate_item");
            $("#buffBar a").removeClass("active");
            var h = l.item;
            if (h != "") {
                if ($("#activeBuffDetails:visible").length) {
                    if (typeof(inventoryObj.items_inventory[h]) == "undefined") {
                        var g = $("#activeBuffDetails .detail_button").filter(":first");
                        $.bbq.pushState({item: g.attr("ref")});
                        return
                    }
                    $("#buffBar a[ref='" + l.item + "']").addClass("active");
                    f.addClass("active");
                    $("#noItems").hide();
                    $("#itemDetailBox").show();
                    updateItemDetails(h)
                } else {
                    $("#buffBar a[ref='" + l.item + "']").addClass("active");
                    f.addClass("active");
                    gfSlider.slideIn(getElementByIdWithCache("detail"), h)
                }
            } else {
                if ($("#activeBuffDetails .detail_button").filter(":first").length === 0) {
                    $("#noItems").show();
                    $("#itemDetailBox").hide()
                }
                $("#activeBuffDetails .close_details").click();
                f.removeClass("active")
            }
        }
    });
    $(document).undelegate("#buffBar a", "click.openDetails").delegate("#buffBar a", "click.openDetails", function () {
        if ($(this).hasClass("active")) {
            $.bbq.pushState({item: ""})
        } else {
            $.bbq.pushState({item: $(this).attr("ref")})
        }
    });
    $("#buffBar").unbind("click.openDetails").bind("click.openDetails", function (b) {
        if (!$(b.target).is("#buffBar .activate_item") && !$(b.target).is(".arrow a")) {
            $("#buffBar .activate_item").click()
        }
    });
    $("#buffBar .active_items div:not(.activate_item)").each(function () {
        $durationEl = $(this).find(".js_duration");
        $pusherEl = $(this).find(".pusher");
        startCooldown($durationEl, $pusherEl, 32)
    });
    $(window).trigger("hashchange")
}
function getItem(b) {
    if (typeof(inventoryObj.items_inventory[b]) != "undefined") {
        return inventoryObj.items_inventory[b]
    }
    return null
}
function updateItemDetails(e) {
    var h = getItem(e);
    $("#activeBuffDetails .detail_button").removeClass("active");
    $('#activeBuffDetails .detail_button[ref="' + e + '"]').addClass("active");
    $("#activeBuffDetails .js_itemName").html(h.name);
    $("#activeBuffDetails .js_itemEffect").html(h.effect);
    $("#activeBuffDetails .js_itemAmount").html(h.amount);
    if (h.firstStatus) {
        $("#activeBuffDetails .js_itemDurationStatus").html(translation.durationType[h.firstStatus])
    } else {
        $("#activeBuffDetails .js_itemDurationStatus").html(translation.durationType.effecting)
    }
    if (h.duration) {
        if (h.durationExtension) {
            $("#activeBuffDetails .js_itemDuration").html(formatTimeWrapper(h.duration, 2, true, " ", false, "") + h.durationExtension)
        } else {
            $("#activeBuffDetails .js_itemDuration").html(formatTimeWrapper(h.duration, 2, true, " ", false, ""))
        }
    } else {
        if (h.duration === null) {
            if (h.moonOnlyItem) {
                $("#activeBuffDetails .js_itemDuration").html(translation.permanentMoon)
            } else {
                $("#activeBuffDetails .js_itemDuration").html(translation.permanent)
            }
        } else {
            $("#activeBuffDetails .js_itemDuration").html(translation.now)
        }
    }
    if (h.timeLeft) {
        $("#activeBuffDetails .js_itemTimeLeftTxt").show();
        $("#activeBuffDetails .js_itemTimeLeft").html(formatTimeWrapper(h.timeLeft, 2, true, " ", false, ""))
    } else {
        $("#activeBuffDetails .js_itemTimeLeftTxt").hide()
    }
    var g = $("#activationButton");
    g.attr("ref", e);
    g.removeClass("buyAndActivate activateItem build-it_disabled build-it dm bp").addClass(h.currency);
    if (h.amount > 0) {
        g.addClass("activateItem").html("<span>" + ((h.timeLeft > 0 && h.extendable) ? translation.extend : translation.activate) + "</span>").addClass(h.canBeActivated ? "build-it" : "build-it_disabled")
    } else {
        var f = (h.timeLeft > 0 && h.extendable) ? translation.buyAndExtend : translation.buyAndActivate;
        f = f.replace(/%price%/, tsdpkt(h.costs));
        f = f.replace(/%currency%/, translation.currencies[h.currency]);
        g.addClass("buyAndActivate").html("<span>" + f + "</span>").addClass(h.canBeBoughtAndActivated && h.hasEnoughCurrency ? "build-it" : "build-it_disabled")
    }
}
function activateItem(e) {
    var f = {
        ajax: 1,
        token: activateToken,
        referrerPage: $.deparam.querystring().page
    };
    var h = getItem(e);
    if (h.amount > 0) {
        f.item = e
    } else {
        f.buyAndActivate = e
    }
    function g() {
        $.ajax({
            cache: false,
            url: inventoryObj.inventoryUrl,
            data: f,
            type: "POST",
            dataType: "json",
            success: function (F, P, D) {
                activateToken = F.newToken;
                if (F.error) {
                    fadeBox(F.message, true);
                    return
                }
                if (F.message.reload) {
                    location.href = getRedirectLink();
                    return
                }
                fadeBox(F.message.message, false);
                if (F.message.buff != false) {
                    var a = getItem(F.message.buff);
                    var H = true;
                    var Q = $(".active_items");
                    var c = Q.data("AnythingSlider");
                    var I;
                    Q.find("li a").removeClass("active");
                    Q.find("li a.activate_item").addClass("active");
                    Q.find("div[data-id=" + F.message.buffId + "]").each(function () {
                        $(this).hide().show("pulsate").find("a").addClass("active");
                        H = false;
                        I = $(this)
                    });
                    if (H == true) {
                        var N = $('<div data-uuid="' + a.ref + '" data-id="' + F.message.buffId + '"><div class="js_duration" style="display: none;"></div><a href="javascript:void(0);" ref="' + a.ref + '" class="detail_button slideIn active_item active r_' + F.message.item.rarity + ' border3px tooltipHTML" title="' + F.message.item.toolTip + '"><div class="pusher" style="height: 0%; "></div><img src="/img/icons/items/' + a.image + '-small.png" alt=""/></a></div>');
                        var E = 14;
                        var d = c.currentPage;
                        var b = c.options.animationTime;
                        if (typeof(F.message.upgraded) != "undefined") {
                            for (var R in F.message.upgraded) {
                                var G = F.message.upgraded[R];
                                Q.find("div[data-uuid=" + G + "]").remove()
                            }
                            var K = $(document.createDocumentFragment());
                            var O = Q.find("li > div");
                            O.each(function (l, k) {
                                if (l % E == 0) {
                                }
                                $(this).appendTo(K.children().last())
                            });
                            c.updateSlider()
                        }
                        var J = Q.children().last().children().length;
                        if (J < E) {
                            N.hide().appendTo(Q.children().last()).show("pulsate");
                            if (d == c.pages) {
                                c.options.animationTime = 0
                            }
                            c.gotoPage(c.pages);
                            if (d == c.pages) {
                                c.options.animationTime = b
                            }
                        } else {
                            N = N.hide().wrap("<li/>");
                            N.parent().appendTo(Q).children().last();
                            c.updateSlider();
                            if (d == c.pages) {
                                c.options.animationTime = 0
                            }
                            c.gotoPage(c.pages);
                            if (d == c.pages) {
                                c.options.animationTime = b
                            }
                            N.show("pulsate")
                        }
                        I = N
                    } else {
                        console.log(a.ref);
                        var L = $('<div data-uuid="' + a.ref + '" data-id="' + F.message.buffId + '"><div class="js_duration" style="display: none;"></div><a href="javascript:void(0);" ref="' + a.ref + '" class="detail_button slideIn active_item active r_' + F.message.item.rarity + ' border3px tooltipHTML" title="' + F.message.item.toolTip + '"><div class="pusher" style="height: 0%; "></div><img src="/img/icons/items/' + a.image + '-small.png" alt=""/></a></div>');
                        var G = a.ref;
                        Q.find("div[data-uuid=" + G + "]").remove();
                        L.hide().appendTo(Q.children().last()).show("pulsate");
                        I = L
                    }
                }
                var C = I.find(".pusher");
                var M = I.find(".js_duration");
                M.attr("data-total-duration", F.message.totalDuration).text(F.message.item.timeLeft);
                startCooldown(M, C, 32);
                getAjaxResourcebox();
                $.ajax({
                    type: "POST",
                    url: detailUrl,
                    data: {type: e},
                    beforeSend: function () {
                        $("#detailWrapper .detail_screen").html('<p class="ajaxLoad">' + LocalizationStrings.loading + "</p>")
                    },
                    success: function (k) {
                        $("#detailWrapper .detail_screen").html(k)
                    }
                })
            },
            error: function (a) {
                fadeBox("Error!", true)
            }
        })
    }

    if (h.isAnUpgrade) {
        errorBoxDecision(LocalizationStrings.activateItem.upgradeItemQuestionHeader, LocalizationStrings.activateItem.upgradeItemQuestion, LocalizationStrings.yes, LocalizationStrings.no, g);
        return
    }
    g()
}
function initEmpireEquipment() {
    $(".overview_equipment .item_img_box .hidden").each(function () {
        startCooldown($(this), $(this).parent().parent().find(".pusher"), 32)
    })
}
function startCooldown(e, f, g) {
    if ($.trim(e.text()).match(/^\d+$/)) {
        var h = new countdownWithTickFunction(e[0], parseInt($.trim(e.text())), parseInt(e.attr("data-total-duration")), function () {
            location.href = getRedirectLink()
        }, function (b, a) {
            var d = 1 - (b / a);
            var c = Math.floor(g * d);
            f.css("height", c + "px")
        })
    }
}
function errorBoxAsArray(b) {
    if (b.type == "notify") {
        notifyBoxAsArray(b)
    } else {
        if (b.type == "decision") {
            decisionBoxAsArray(b)
        } else {
            if (b.type == "fadeBox") {
                fadeBox(b.text, b.failed)
            }
        }
    }
}
function notifyBoxAsArray(b) {
    errorBoxNotify(b.title, b.text, b.buttonOk, String(b.okFunction), b.removeOpen, b.modal)
}
function fadeBox(d, e, f) {
    if (e) {
        $("#fadeBoxStyle").attr("class", "failed")
    } else {
        $("#fadeBoxStyle").attr("class", "success")
    }
    $("#fadeBoxContent").html(d);
    $("#fadeBox").stop(false, true).show().fadeOut(10000, f)
}
function decisionBoxAsArray(b) {
    errorBoxDecision(b.title, b.text, b.buttonOk, b.buttonNOk, String(b.okFunction), String(b.nokFunction), b.removeOpen)
}
function errorBoxDecision(q, s, y, n, v, x, w) {
    var r = getIEVersion() <= 9 && (w || false);
    var p = $("#errorBoxDecision");
    p.find("#errorBoxDecisionHead").html(q);
    p.find("#errorBoxDecisionContent").html(s);
    var o = function (a) {
        a.stopPropagation();
        if (typeof(p.data("uiDialog")) != "undefined") {
            p.dialog("destroy")
        }
        if (typeof(v) == "function") {
            v()
        }
        if (v == "submit_form") {
            $("#planetMaintenanceDelete").submit()
        }
    };
    var u = function (a) {
        a.stopPropagation();
        if (typeof(p.data("uiDialog")) != "undefined") {
            p.dialog("destroy")
        }
        if (x == "checkPassword") {
            location.reload()
        }
        if (typeof(x) == "function") {
            x()
        }
    };
    p.find(".yes, .no").unbind("click");
    p.unbind("keydown.yesHandler");
    setTimeout(function () {
        var b = p.find(".yes");
        var a = p.find(".no");
        b.unbind("click").bind("click", o).focus().find("#errorBoxDecisionYes").html(y);
        a.unbind("click").bind("click", u).find("#errorBoxDecisionNo").html(n);
        if (r) {
            b.attr("href", "#");
            a.attr("href", "#")
        } else {
            b.attr("href", "javascript:void(0);");
            a.attr("href", "javascript:void(0);")
        }
        p.bind("keydown.yesHandler", function (c) {
            if (c.which == KeyEvent.DOM_VK_RETURN) {
                p.find(".yes").trigger("click")
            }
        })
    }, 100);
    Tipped.hideAll();
    p.dialog({
        resizable: false,
        modal: true,
        title: q,
        close: u,
        width: 400,
        dialogClass: "errorBox"
    })
}
function errorBoxNotify(n, p, m, q, r) {
    var o = getIEVersion() <= 9 && (r || false);
    var l = $("#errorBoxNotify");
    l.find("#errorBoxNotifyHead").html(n);
    l.find("#errorBoxNotifyContent").html(p);
    var u = function (a) {
        a.stopPropagation();
        l.dialog("destroy");
        if (typeof(q) == "function") {
            q()
        } else {
            if (typeof(window[q]) == "function") {
                window[q]()
            }
        }
    };
    var s = l.find(".ok");
    s.unbind("click").bind("click", u).find("#errorBoxNotifyOk").html(m);
    if (o) {
        s.attr("href", "#")
    } else {
        s.attr("href", "javascript:void(0);")
    }
    Tipped.hideAll();
    l.dialog({
        resizable: false,
        modal: true,
        title: n,
        close: u,
        width: 400,
        dialogClass: "errorBox"
    })
}
function eventBDayInitGalaxy() {
    if (isMobile) {
        $(".js_bday_details").hide();
        $(".bdaySlotBox .name").click(function (b) {
            $(".row").children().each(function () {
                if ($(this).html().trim()) {
                    $(this).removeClass("active")
                }
            });
            $(".bdaySlotBox .name").removeClass("active");
            $(".js_detailRow").hide();
            $(this).toggleClass("active");
            if ($(b.target).attr("class").indexOf("planet") !== -1) {
                $(".js_detailRowPlanet17").toggle()
            } else {
                if ($(b.target).attr("class").indexOf("debris") !== -1) {
                    $(".js_detailRowDebris17").toggle()
                }
            }
        })
    }
}
function eventBDayInit() {
    var b = $(".event_box");
    $("#eventBDayWrapper").click(function (a) {
        if ($(a.target).attr("id") === "BDayContent" || $(a.target).attr("id") === "BDayEventClose" || $(a.target).closest("#BDayHeader").length > 0) {
            b.hide();
            $(".bday_box").removeClass("active")
        }
        a.stopPropagation()
    });
    $(".bday_box").click(function (d) {
        $(".bday_box").removeClass("active");
        var a = $(d.target).closest(".bday_box").attr("id").replace("box", "");
        b.removeClass().addClass("event_box " + eventBoxData[a].eventTypeClass);
        if (eventBoxData[a] !== undefined) {
            b.attr("id", "eventBox" + a).show();
            $("#box" + a).addClass("active");
            if (eventBoxData[a].eventTypeClass !== "future") {
                $("#eventBox" + a + " #BDayEventDate").html(eventBoxData[a].date + ": ")
            } else {
                $("#eventBox" + a + " #BDayEventDate").html("")
            }
            $("#eventBox" + a + " #BDayEventTitle").html(eventBoxData[a].title);
            $("#eventBox" + a + " #BDayofficerImg").attr("src", eventBoxData[a].eventOfficerImgSrc);
            if (eventBoxData[a].eventTypeClass !== "future" || eventBoxData[a].txtChronic !== undefined) {
                $("#eventBox" + a + " #BDayEventTxtChronic").html(eventBoxData[a].txtChronic);
                $("#eventBox" + a + " #BDayChronic").show()
            } else {
                $("#eventBox" + a + " #BDayChronic").hide()
            }
            if (eventBoxData[a].eventImgSrc === undefined && eventBoxData[a].eventTypeClass === "future") {
                $("#eventBox" + a + " #BDayEventImg").attr("src", "/img/icons/d995359d038c9a0c21aed16b3cc162.png")
            } else {
                $("#eventBox" + a + " #BDayEventImg").attr("src", eventBoxData[a].eventImgSrc)
            }
            if (eventBoxData[a].eventTxtDesc === undefined) {
                $("#eventBox" + a + " #BDayEventTxtDesc").html("")
            } else {
                $("#eventBox" + a + " #BDayEventTxtDesc").html(eventBoxData[a].eventTxtDesc)
            }
            $("#eventBox" + a + " #BDayEventPastTxt").hide();
            if (eventBoxData[a].eventTypeClass === "past") {
                $("#eventBox" + a + " #BDayEventPastTxt").html(eventPastTxt).show()
            }
            $("#eventBox" + a + " #BDayEventBtnCTA").hide();
            if ((eventBoxData[a].eventTypeClass !== ("past" || "future")) && eventBoxData[a].btnCTA !== undefined) {
                $("#eventBox" + a + " #BDayEventBtnCTA").html(eventBoxData[a].btnCTA).attr("href", eventBoxData[a].btnCTALink).show()
            }
        }
    })
}
function initBDayEventHints() {
    $(document).undelegate(".event_build_faster, .event_active_hint", "click").delegate(".event_build_faster, .event_active_hint", "click", function (b) {
        b.stopPropagation();
        if ($(this).parent().attr("id") === "expeditionbutton") {
            doExpedition()
        } else {
            $(this).siblings(".detail_button").click()
        }
    })
}
function initEventTable() {
    $(".eventFleet:odd").addClass("odd");
    $(".partnerInfo:even").addClass("part-even");
    $(".partnerInfo").hide();
    $(document).undelegate(".toggleDetails", "click").delegate(".toggleDetails", "click", function () {
        var b = $(".partnerInfo." + $(this).attr("rel"));
        if ($(b).is(":hidden")) {
            $(b).show();
            $(this).parents("tr").addClass("detailsOpened").removeClass("detailsClosed")
        } else {
            $(b).hide();
            $(this).parents("tr").addClass("detailsClosed").removeClass("detailsOpened")
        }
    })
}
function initExodus() {
    if ($("#exodus-indicator").length && exodus.ready == false) {
        exodus.ready = true;
        exodus.createIndicator();
        var d = function (a) {
            if ($("#exodus-timer").hasClass("end-soon")) {
                return
            }
            if (a < 86400) {
                $("#exodus-timer").addClass("end-soon")
            }
        };
        var c = parseInt($("#exodus-timer").attr("data-timeleft"));
        d(c);
        new simpleCountdown(getElementByIdWithCache("exodus-timer"), c, function () {
            $("#exodus-timer").remove()
        }, function (a) {
            d(a)
        })
    }
}
var exodus = {
    ready: false,
    noServers: true,
    secondaryShown: false,
    createIndicator: function () {
        exodus.indicator = $("#exodus-indicator");
        if (exodus.indicator.length > 0) {
            exodus.indicator.bind("click", exodus.showDialog)
        }
    },
    appendServerRow: function (h, l, m, o) {
        var p = (exodus.data["migration-host"] == l.host);
        var k = $('<li class="' + (exodus.odd++ % 2 ? "odd" : "even") + '" id="server-' + l.id + '"/>').bind("click", function () {
            if ($(this).hasClass("disabled")) {
                return
            } else {
                if ($(this).hasClass("row-selected")) {
                    exodus.confirmButton.removeClass("confirm");
                    $(this).removeClass("row-selected");
                    exodus.markedServer = null
                } else {
                    exodus.markedServer = l;
                    if (exodus.markedServer != exodus.selectedServer) {
                        exodus.confirmButton.addClass("confirm")
                    } else {
                        if (exodus.confirmButton.hasClass("confirm")) {
                            exodus.confirmButton.removeClass("confirm")
                        }
                    }
                    h.find("li").removeClass("row-selected");
                    $(this).addClass("row-selected")
                }
            }
        }).hide().append($('<span class="exodus-universe-treeview-column-checkbox"/>').append($("<span/>"))).append($('<span class="exodus-universe-treeview-column-name"/>').text(l.name)).append($('<span class="exodus-universe-treeview-column-startdate"/>').text(l.since)).append($('<span class="exodus-universe-treeview-column-free-spaces"/>')).append($('<span class="exodus-universe-treeview-column-features"/>').text(l.features));
        h.append(k);
        var n = $('<li id="wait-server-' + l.id + '"/>').append($('<img alt="' + exodus.data.translations.loading + '" src="' + exodus.data["load-image"] + '"/>'));
        h.append(n);
        if (p) {
            exodus.markedServer = l;
            exodus.selectedServer = l;
            k.addClass("server-selected");
            k.addClass("row-selected")
        }
        $.jsonp({
            url: "https://" + l.host + "/service/exodus.php?action=getServerInfo&data=" + l.data + "&callback=?",
            error: function () {
                exodus.handleError(l.id, exodus.data.translations["error-no-server"]);
                $("#server-" + l.id).show();
                $("#wait-server-" + l.id).remove();
                if (exodus.noServers && m) {
                    exodus.showSecondaryServers(h)
                }
            },
            success: function (a) {
                if (!a.currentPlayerCount) {
                    exodus.handleError(l.id, exodus.data.translations["error-no-server"]);
                    $("#server-" + l.id).show();
                    $("#wait-server-" + l.id).remove();
                    return
                }
                $("#server-" + l.id + " .exodus-universe-treeview-column-free-spaces").text(a.freeSlots);
                if (a.alreadyMigrating) {
                    exodus.handleSuccess(l.id, exodus.data.translations["success-server-selected"]);
                    if (!o) {
                        exodus.noServers = false
                    }
                } else {
                    if (a.alreadyRegistered) {
                        exodus.handleError(l.id, exodus.data.translations["error-account"])
                    } else {
                        if (a.usernameInUse) {
                            exodus.handleError(l.id, exodus.data.translations["error-name-in-use"])
                        } else {
                            if (a.freeSlots <= 0) {
                                exodus.handleError(l.id, exodus.data.translations["error-no-free-slots"])
                            } else {
                                if (!o) {
                                    exodus.noServers = false
                                }
                            }
                        }
                    }
                }
                if (exodus.noServers && m) {
                    exodus.showSecondaryServers(h)
                }
                $("#server-" + l.id).show();
                $("#wait-server-" + l.id).remove()
            }
        })
    },
    handleError: function (d, c) {
        $("#server-" + d).find("span.exodus-success").remove();
        $("#server-" + d).find("span.exodus-error").remove();
        $("#server-" + d).append('<span class="exodus-error"><strong>' + exodus.data.translations["error-impossible"] + "</strong><br/>" + c + "</span>");
        $("#server-" + d).addClass("disabled")
    },
    handleSuccess: function (d, c) {
        $("#server-" + d).find("span.exodus-success").remove();
        $("#server-" + d).find("span.exodus-error").remove();
        $("#server-" + d).append('<span class="exodus-success"><strong>' + c + "</strong></span>")
    },
    showSecondaryServers: function (d) {
        for (var c = 0; c < exodus.data["secondary-servers"].length; c++) {
            exodus.appendServerRow(d, exodus.data["secondary-servers"][c], false, true)
        }
        exodus.secondaryShown = true
    },
    showDialog: function () {
        if (exodus.isOpen != null) {
            if (exodus.isOpen == false) {
                exodus.overlay.show();
                exodus.dialog.show();
                $("html, body").animate({scrollTop: 0}, 100)
            }
            return
        }
        exodus.data = {
            "load-image": exodus.indicator.attr("data-load-image"),
            "exodus-start": exodus.indicator.attr("data-exodus-start"),
            "exodus-end": exodus.indicator.attr("data-exodus-end"),
            "next-migration": exodus.indicator.attr("data-next-migration"),
            translations: $.parseJSON(exodus.indicator.attr("data-translations")),
            "migration-host": exodus.indicator.attr("data-migration-host"),
            "primary-servers": $.parseJSON(exodus.indicator.attr("data-primary-servers")),
            "secondary-servers": $.parseJSON(exodus.indicator.attr("data-secondary-servers")),
            "user-data": exodus.indicator.attr("data-user-data"),
            "user-validation": exodus.indicator.attr("data-user-validation"),
            "see-dialog": exodus.indicator.attr("data-user-see-dialog"),
            "ally-id": exodus.indicator.attr("data-user-ally-id"),
            "buddy-amount": exodus.indicator.attr("data-user-buddy-amount")
        };
        if (exodus.data["user-validation"] == 0) {
            errorBoxNotify(LocalizationStrings.error, exodus.data.translations["error-user-not-validated"], LocalizationStrings.ok);
            return
        }
        exodus.isOpen = true;
        exodus.selectedServer = null;
        exodus.markedServer = null;
        exodus.dialog = $('<div id="exodus-dialog"/>');
        exodus.overlay = $('<div id="exodus-overlay"/>');
        var h = $('<div id="exodus-header"/>');
        var f = $('<div id="exodus-close" class="ui-icon ui-icon-closethick"/>');
        var k = $('<div id="exodus-content"/>');
        var l = $('<div id="exodus-top-content"/>');
        var g = $('<ul id="exodus-tab-bar"/>');
        exodus.overlay.bind("click", exodus.closeDialog);
        f.bind("click", exodus.closeDialog);
        h.append($('<div id="exodus-header-content"/>').text(exodus.data.translations["exodus-dialog-title"]));
        l.text(exodus.data.translations["exodus-period"] + " " + exodus.data["exodus-start"] + " – " + exodus.data["exodus-end"]);
        if (exodus.data["next-migration"] != "") {
            l.append($('<span id="exodus-next-migration"/>').text(exodus.data["next-migration"]));
            l.append($("<span/>").text(": " + exodus.data.translations["next-migration-processing"]))
        }
        allyView = $('<li id="ally-show"/>').text(exodus.data.translations["tab-ally-mebers"]);
        allyView.bind("click", function () {
            exodus.showAllyMemberTab()
        });
        buddyView = $('<li id="buddy-show"/>').text(exodus.data.translations["tab-buddies"]);
        buddyView.bind("click", function () {
            exodus.showBuddyTab()
        });
        serverView = $('<li id="uni-select"/>').text(exodus.data.translations["tab-uni-selection"]);
        serverView.bind("click", function () {
            exodus.showServerSelection()
        });
        rulesView = $('<li id="rules-show"/>').text(exodus.data.translations["tab-rules"]);
        rulesView.bind("click", function () {
            exodus.showRulesTab()
        });
        g.append(serverView);
        if (exodus.data["ally-id"] > 0) {
            g.append(allyView)
        }
        if (exodus.data["buddy-amount"] > 0) {
            g.append(buddyView)
        }
        g.append(rulesView);
        k.append(l).append(g);
        exodus.dialog.append(h).append(k).append(f);
        $("body").append(exodus.overlay).append(exodus.dialog);
        if (exodus.data["see-dialog"] == "0") {
            exodus.showRulesTab();
            exodus.data["see-dialog"] = "1";
            exodus.indicator.attr("data-user-see-dialog", 1);
            $.jsonp({
                url: "/service/exodus.php?action=seeDialog&data=" + exodus.data["user-data"] + "&callback=?",
                error: function (a) {
                    errorBoxNotify(LocalizationStrings.error, "TEST: " + exodus.data["user-data"], LocalizationStrings.ok)
                },
                success: ""
            })
        } else {
            exodus.showServerSelection()
        }
        $("html, body").animate({scrollTop: 0}, 100)
    },
    closeDialog: function () {
        exodus.dialog.hide();
        exodus.overlay.hide();
        exodus.isOpen = false
    },
    createButton: function (b) {
        return $('<div class="exodus-button"/>').append($('<div class="exodus-button-left"/>')).append($('<div class="exodus-button-label"/>').text(b)).append($('<div class="exodus-button-right"/>'))
    },
    showServerSelection: function () {
        this.resetView("#uni-select");
        if (exodus.tabContent != null) {
            exodus.tabContent.show()
        } else {
            exodus.tabContent = $('<div id="exodus-tab-content"/>');
            exodus.universeTreeviewHeader = $('<div id="exodus-universe-treeview-header"/>').append($('<span class="exodus-universe-treeview-column-checkbox"/>').text("\u2714").css("visibility", "hidden")).append($('<span class="exodus-universe-treeview-column-name"/>').text(exodus.data.translations["uni-selection-name"])).append($('<span class="exodus-universe-treeview-column-startdate"/>').text(exodus.data.translations["uni-selection-start"])).append($('<span class="exodus-universe-treeview-column-free-spaces"/>').text(exodus.data.translations["uni-selection-free-spaces"])).append($('<span class="exodus-universe-treeview-column-features"/>').text(exodus.data.translations["uni-selection-specifics"]));
            var e = $('<ul id="exodus-universe-treeview"/>');
            exodus.confirmButton = exodus.createButton(exodus.data.translations["uni-selection-confirm"]);
            exodus.revertButton = exodus.createButton(exodus.data.translations["uni-selection-revert"]);
            exodus.confirmButton.bind("click", function () {
                if (!$(this).hasClass("confirm") || exodus.markedServer == null) {
                    return
                }
                var a = exodus.markedServer;
                $.jsonp({
                    url: "https://" + a.host + "/service/exodus.php?action=requestMigration&data=" + a.data + "&callback=?",
                    error: function () {
                        exodus.handleError(a.id, exodus.data.translations["error-no-server"])
                    },
                    success: function (b) {
                        if (b.errorMessage) {
                            exodus.handleError(a.id, exodus.data.translations["error-no-server"])
                        } else {
                            if (exodus.selectedServer != null) {
                                e.find("li").removeClass("row-selected");
                                e.find("li").removeClass("server-selected");
                                $("#server-" + exodus.selectedServer.id).find("span.exodus-success").remove();
                                $("#server-" + exodus.selectedServer.id).find("span.exodus-error").remove()
                            }
                            exodus.indicator.attr("data-migration-host", a.host);
                            exodus.data["migration-host"] = a.host;
                            $("#exodus-indicator-waiting").attr("id", "exodus-indicator-acknowledged");
                            exodus.handleSuccess(a.id, exodus.data.translations["success-server-selected"]);
                            exodus.revertButton.addClass("cancel");
                            $("#server-" + a.id).addClass("server-selected");
                            exodus.selectedServer = a;
                            var c = $("#server-" + a.id).find("span.exodus-universe-treeview-column-free-players");
                            c.text(parseInt(c.text()) - 1);
                            exodus.confirmButton.removeClass("confirm")
                        }
                    }
                })
            });
            exodus.revertButton.bind("click", function () {
                if (!$(this).hasClass("cancel") || exodus.selectedServer == null) {
                    return
                }
                var a = exodus.selectedServer;
                $.jsonp({
                    url: "https://" + a.host + "/service/exodus.php?action=cancelMigration&data=" + a.data + "&callback=?",
                    error: function () {
                        exodus.closeDialog()
                    },
                    success: function (b) {
                        if (b.errorMessage) {
                            exodus.closeDialog()
                        } else {
                            exodus.indicator.attr("data-migration-host", "");
                            $("#exodus-indicator-acknowledged").attr("id", "exodus-indicator-waiting");
                            e.find("li").removeClass("row-selected");
                            e.find("li").removeClass("server-selected");
                            $("#server-" + a.id).find("span.exodus-success").remove();
                            $("#server-" + a.id).find("span.exodus-error").remove();
                            exodus.confirmButton.removeClass("confirm");
                            exodus.revertButton.removeClass("cancel");
                            var c = $("#server-" + a.id).find("span.exodus-universe-treeview-column-free-players");
                            c.text(parseInt(c.text()) + 1);
                            exodus.markedServer = null;
                            exodus.selectedServer = null
                        }
                    }
                })
            });
            if (exodus.data["migration-host"] != "") {
                exodus.revertButton.addClass("cancel")
            }
            exodus.tabContent.append(exodus.universeTreeviewHeader).append(e).append(exodus.confirmButton).append(exodus.revertButton);
            exodus.odd = 0;
            for (var d = 0; d < exodus.data["primary-servers"].length; d++) {
                var f = (exodus.data["primary-servers"].length == d + 1);
                exodus.appendServerRow(e, exodus.data["primary-servers"][d], f, false)
            }
            if (!exodus.secondaryShown) {
                for (var d = 0; d < exodus.data["secondary-servers"].length; d++) {
                    if (exodus.data["migration-host"] == exodus.data["secondary-servers"][d]["host"]) {
                        exodus.appendServerRow(e, exodus.data["secondary-servers"][d], false, true)
                    }
                }
                exodus.secondaryShown = true
            }
            $("#exodus-content").append(exodus.tabContent)
        }
    },
    showAllyMemberTab: function () {
        this.resetView("#ally-show");
        if (exodus.allyMemberContent != null) {
            exodus.allyMemberContent.show()
        } else {
            exodus.allyMemberContent = $('<div id="exodus-ally-content"/>');
            $.jsonp({
                url: "/service/exodus.php?action=getAllyMemberSelection&data=" + exodus.data["user-data"] + "&callback=?",
                error: function () {
                },
                success: function (g) {
                    if (g.errorMessage) {
                        exodus.closeDialog()
                    } else {
                        var h = $('<div id="exodus-universe-treeview-header"/>').append($('<span class="exodus-universe-treeview-column-member-name"/>').text(exodus.data.translations["ally-members-name"])).append($('<span class="exodus-universe-treeview-column-server-name"/>').text(exodus.data.translations["ally-members-server"]));
                        var l = $('<div id="exodus-universe-treeview"/>');
                        for (var f = 0; f < g.length; f++) {
                            var k = $('<li id="server-' + f + '" class="' + (f % 2 ? "odd" : "even") + '"/>').append($('<span class="exodus-universe-treeview-column-member-name"/>').text(g[f].db_character));
                            if (g[f].server_name != null) {
                                k.append($('<span class="exodus-universe-treeview-column-server-name"/>').text(g[f].server_name))
                            } else {
                                k.append($('<span class="exodus-universe-treeview-column-server-name"/>').text(exodus.data.translations["ally-member-no-selction"]))
                            }
                            l.append(k)
                        }
                        exodus.allyMemberContent.append(h);
                        exodus.allyMemberContent.append(l);
                        $("#exodus-content").append(exodus.allyMemberContent)
                    }
                }
            })
        }
    },
    showBuddyTab: function () {
        this.resetView("#buddy-show");
        if (exodus.buddyContent != null) {
            exodus.buddyContent.show()
        } else {
            exodus.buddyContent = $('<div id="exodus-buddy-content"/>');
            $.jsonp({
                url: "/service/exodus.php?action=getBuddySelection&data=" + exodus.data["user-data"] + "&callback=?",
                error: function () {
                },
                success: function (d) {
                    if (d.errorMessage) {
                        exodus.closeDialog()
                    } else {
                        header = $('<div id="exodus-universe-treeview-header"/>').append($('<span class="exodus-universe-treeview-column-member-name"/>').text(exodus.data.translations["ally-members-name"])).append($('<span class="exodus-universe-treeview-column-server-name"/>').text(exodus.data.translations["ally-members-server"]));
                        var c = $('<div id="exodus-universe-treeview"/>');
                        for (i = 0; i < d.length; i++) {
                            row = $('<li id="server-' + i + '" class="' + (i % 2 ? "odd" : "even") + '"/>').append($('<span class="exodus-universe-treeview-column-member-name"/>').text(d[i].db_character));
                            if (d[i].server_name != null) {
                                row.append($('<span class="exodus-universe-treeview-column-server-name"/>').text(d[i].server_name))
                            } else {
                                row.append($('<span class="exodus-universe-treeview-column-server-name"/>').text(exodus.data.translations["ally-member-no-selction"]))
                            }
                            c.append(row)
                        }
                        exodus.buddyContent.append(header);
                        exodus.buddyContent.append(c);
                        $("#exodus-content").append(exodus.buddyContent)
                    }
                }
            })
        }
    },
    showRulesTab: function () {
        this.resetView("#rules-show");
        rules = new Array("uni-fusion", "pre-exodus", "exodus-phase", "post-exodus", "exodus-uni", "target-uni");
        if (exodus.rulesContent != null) {
            exodus.rulesContent.show()
        } else {
            exodus.rulesContent = $('<div id="exodus-rules-content"/>');
            rule = $('<div class="exodus_rule"/>');
            for (var b = 0; b < rules.length; b++) {
                rule.append($('<h2 class="exodus-rule-title" />').text(exodus.data.translations["title-" + rules[b]]));
                rule.append($('<p class="exodus-rule-description" />').text(exodus.data.translations["description-" + rules[b]]));
                exodus.rulesContent.append(rule)
            }
            $("#exodus-content").append(exodus.rulesContent)
        }
    },
    resetView: function (b) {
        if ($(b).hasClass("selected")) {
            return
        }
        if ($("#uni-select").hasClass("selected")) {
            exodus.tabContent.hide()
        } else {
            if ($("#buddy-show").hasClass("selected")) {
                exodus.buddyContent.hide()
            } else {
                if ($("#ally-show").hasClass("selected")) {
                    exodus.allyMemberContent.hide()
                } else {
                    if ($("#rules-show").hasClass("selected")) {
                        exodus.rulesContent.hide()
                    }
                }
            }
        }
        $("#uni-select").removeClass("selected");
        $("#ally-show").removeClass("selected");
        $("#buddy-show").removeClass("selected");
        $("#rules-show").removeClass("selected");
        $(b).addClass("selected")
    }
};
function initFleet1() {
    checkShips("shipsChosen");
    initToggleHeader("fleet1");
    $(".combatunits").unbind("change").bind("change", function () {
        setTemplate(shipTemplates[$(this).val()], maxShips);
        checkShips("shipsChosen")
    });
    focusOnTabChange("#continue", true)
}
function setTemplate(d, e) {
    for (var f in d) {
        if (!$("#ship_" + f).attr("disabled")) {
            $("#ship_" + f).val(d[f])
        }
        if (typeof e[f] == "undefined") {
            e[f] = 0
        }
        checkIntInput("#ship_" + f, 0, e[f])
    }
}
function resetForm() {
    $(".fleetValues").val("")
}
function checkShips(D) {
    if (document.forms[D] != undefined) {
        var s = 0;
        var r = document.forms[D].length;
        for (var w = 0; w < r; w++) {
            var C = document.forms[D].elements[w].id;
            if (typeof C != "undefined") {
                expl = C.split("_");
                if (expl[0] == "ship") {
                    value = document.forms[D].elements[w].value;
                    if (!$("#" + C).attr("disabled") && value != "") {
                        s = s + value
                    }
                }
            }
        }
        var p = "";
        if (s == 0 || !sendingEnabled) {
            validated = false;
            $("#continue").attr("class", "off");
            p = loca.fleetNoSelection;
            if (!sendingEnabled) {
                p = loca.fleetNoFreeSlots
            }
        } else {
            validated = true;
            $("#continue").attr("class", "on")
        }
        var y = createAPIFleet(D);
        if (!$("#FLEETAPI").val()) {
            var x = $(".show_fleet_apikey").attr("title");
            var A = x.indexOf("value='") + 7;
            var u = x.indexOf("' readonly");
            var q = x.slice(0, A);
            var B = x.slice(u, x.length);
            var v = q + y + B;
            $(".show_fleet_apikey").attr("title", v)
        } else {
            $("#FLEETAPI").val(y)
        }
        $("#allornone .info").html(p)
    }
}
function createAPIFleet(h) {
    var k = "";
    if (document.forms[h] != undefined) {
        var g = document.forms[h].length;
        for (var l = 0; l < g; l++) {
            var f = document.forms[h].elements[l].id;
            expl = f.split("_");
            if (expl[0] == "ship") {
                value = document.forms[h].elements[l].value;
                if (value > 0 || value != "") {
                    type = expl[1];
                    k = k + type + ";" + value + "|"
                }
            }
        }
    }
    k = k.substring(0, k.length - 1);
    return techData + k
}
function initStandardFleet() {
    $(".list tr:even").addClass("alt");
    $(".standardFleetSubmit").unbind("click").bind("click", function () {
        $(this).parents("form").submit()
    });
    $(".standardFleetReset").unbind("click").bind("click", function () {
        $(this).parents("form")[0].reset()
    });
    $(".changeFleet").unbind("click").bind("click", function () {
        $(".combatunits").val($(this).attr("rel")).trigger("change");
        $(this).parents(".ui-dialog").find(".ui-dialog-titlebar-close").click()
    })
}
function setShipsFleet(e, h, f) {
    $("#template_id").val(f);
    $("#template_name").val(h);
    for (var g in e) {
        $("#ship" + g).val(e[g])
    }
}
function toggleMaxShips(h, g, f) {
    var e = $(h).find("#ship_" + g);
    if (parseInt(e.val()) !== f) {
        e.val(f)
    } else {
        e.val("")
    }
    $("#continue").focus()
}
function initFleet2() {
    updateVariables();
    timerHandler.appendCallback(updateTimesFleet2);
    if (!isMobile) {
        $("#galaxy").focus()
    }
    initToggleHeader("fleet2");
    initBackButton();
    focusOnTabChange("#continue", true);
    detachEventHandlers();
    attachEventHandlers();
    if ($(".combatunits>option:selected").length) {
        $(".combatunits>option:selected").change()
    }
}
function attachEventHandlers() {
    var b = $("#speed");
    $(document).on("mouseover focus", "#speedLinks>a", function (a) {
        b.val($(this).attr("data-value"));
        updateVariables()
    }).on("mouseleave blur", "#speedLinks>a", function (a) {
        resetSpeedToSelected()
    }).on("mouseup touchstart", "#speedLinks>a", function (a) {
        $("#speedLinks>a.selected").removeClass("selected");
        $(this).addClass("selected").focus();
        b.val($(this).attr("data-value"));
        updateVariables()
    }).on("keyup", "#speedLinks>a", function (a) {
        if (a.keyCode == KeyEvent.DOM_VK_LEFT) {
            $(this).prev().focus()
        } else {
            if (a.keyCode == KeyEvent.DOM_VK_RIGHT) {
                $(this).next().focus()
            } else {
                if (a.keyCode == KeyEvent.DOM_VK_RETURN) {
                    a.stopPropagation();
                    a.preventDefault();
                    if ($(this).hasClass("selected")) {
                        trySubmit()
                    } else {
                        $(this).trigger("mouseup")
                    }
                }
            }
        }
    }).on("keypress", "#speedLinks>a", function (a) {
        if (a.keyCode == KeyEvent.DOM_VK_RETURN) {
            a.stopPropagation();
            a.preventDefault();
            if ($(this).hasClass("selected")) {
                trySubmit()
            } else {
                $(this).click()
            }
        }
    }).on("keypress", "#contentWrapper #inhalt", submitOnEnter)
}
function detachEventHandlers() {
    $(document).off("mouseover focus mouseleave blur mouseup touchstart keyup keypress", "#speedLinks>a").off("keypress", "#contentWrapper #inhalt")
}
function resetSpeedToSelected() {
    if (currentPage == "fleet2") {
        $("#speed").val($("#speedLinks>a.selected").attr("data-value"));
        updateVariables()
    }
}
function initBackButton() {
    $("#back").unbind("click").bind("click", function () {
        resetSpeedToSelected();
        $(this).parents("form").attr("action", returnLink).submit()
    })
}
function prepareVariables() {
    checkCoords();
    speed = $("#speed").val()
}
function setTType(b) {
    b = b == null ? $("#type").val() : b;
    if (b == 1) {
        $("#pbutton").attr("class", "planet_selected")
    } else {
        $("#pbutton").attr("class", "planet")
    }
    if (b == 2) {
        $("#dbutton").attr("class", "debris_selected")
    } else {
        $("#dbutton").attr("class", "debris")
    }
    if (b == 3) {
        $("#mbutton").attr("class", "moon_selected")
    } else {
        $("#mbutton").attr("class", "moon")
    }
    if (b == 1) {
        $("#targetPlanetName").html(locaAllPlanet)
    } else {
        if (b == 2) {
            $("#targetPlanetName").html(locaAllDebris)
        } else {
            if (b == 3) {
                $("#targetPlanetName").html(locaAllMoon)
            }
        }
    }
    $("#type").val(b)
}
function shortLinkChange(d) {
    var e;
    if (d != null) {
        e = $("#aksbox").val()
    } else {
        e = $("#slbox").val()
    }
    if (e != "-") {
        var f = e.split("#");
        $("#galaxy").val(f[0]);
        $("#system").val(f[1]);
        $("#position").val(f[2]);
        setTType(f[3]);
        $("#targetPlanetName").html(f[4]);
        targetGalaxy = f[0];
        targetSystem = f[1];
        targetPosition = f[2]
    }
    focusContinueButton()
}
function focusContinueButton() {
    setTimeout(function () {
        $("#continue").focus()
    }, 100)
}
function handleUnion() {
    if ($("#aksbox").val() == "-") {
        document.details.union.value = 0;
        document.details.mission.value = mission
    } else {
        parts = $("#aksbox").val().split("#");
        document.details.union.value = parts[5];
        document.details.mission.value = missionUnionattack
    }
}
function hideStatusBar() {
    if ($(".fleetStatus") && (targetGalaxy != defaultTargetGalaxy || targetSystem != defaultTargetSystem || targetPosition != defaultTargetPosition)) {
        $(".fleetStatus").hide();
        $("input[name='mission']").value = ""
    }
}
function checkCoords() {
    checkIntInput("#galaxy", 1, maxGalaxy);
    checkIntInput("#system", 1, maxSystem);
    checkIntInput("#position", 1, maxPosition);
    targetGalaxy = $("#galaxy").val();
    targetSystem = $("#system").val();
    targetPosition = $("#position").val();
    if (targetPosition == 16) {
        setTType(1);
        modifyPlanetName();
        checkOk()
    }
}
function checkTarget() {
    var d = checkUrl;
    var c = {};
    c.galaxy = $("#galaxy").val();
    c.system = $("#system").val();
    c.planet = $("#position").val();
    c.type = $("#type").val();
    if ($("form[name='details'] input[name='am" + colonizationID + "']").length > 0 && $("form[name='details'] input[name='am" + colonizationID + "']").val() > 0) {
        c.cs = 1
    }
    if ($("form[name='details'] input[name='am" + recyclerID + "']").length > 0 && $("form[name='details'] input[name='am" + recyclerID + "']").val() > 0) {
        c.recycler = 1
    }
    console.log('checkTarget()');
    $.post(d, c, displayError)
}
function updateTimesFleet2() {
    var d = getFormatedDate(serverTime.getTime() + 1000 * duration, "[d].[m].[y] [G]:[i]:[s]");
    var c = getFormatedDate(serverTime.getTime() + 1000 * 2 * duration, "[d].[m].[y] [G]:[i]:[s]");
    if (document.getElementById) {
        $("#arrivalTime").html(d);
        $("#returnTime").html(c)
    }
}
function initFleet3() {
    timerHandler.appendCallback(updateTimesFleet3);
    if (!isMobile) {
        $("#metal").focus()
    }
    initToggleHeader("fleet3");
    initBackButton();
    updatePrioButtons();
    if ($("#missions .on .selected").length) {
        $("#missions .on .selected")[0].onclick()
    }
    $("#contentWrapper #inhalt").unbind("keydown.continue").bind("keydown.continue", function (b) {
        if (b.keyCode == KeyEvent.DOM_VK_RETURN) {
            trySubmit()
        }
    })
}
function updatePrioButtons() {
    $("#prioM1").attr("src", "/img/icons/4b53e83f8b8583ea279fd26f3a55a5.gif");
    $("#prioM2").attr("src", "/img/icons/8afbd59ffe091239a7c6f1e961b267.gif");
    $("#prioM3").attr("src", "/img/icons/4acc67e1ca4d8debb1b114abcb7c1e.gif");
    switch (prioMetal) {
        case 1:
            $("#prioM1").attr("src", "/img/icons/b357323b99e20a46fc0b2495728351.gif");
            break;
        case 2:
            $("#prioM2").attr("src", "/img/icons/f8959fe540cd329f3a764ad9aeaf93.gif");
            break;
        case 3:
            $("#prioM3").attr("src", "/img/icons/823b3270ed0f4a243287c12d4ee5f8.gif");
            break
    }
    $("#prioC1").attr("src", "/img/icons/4b53e83f8b8583ea279fd26f3a55a5.gif");
    $("#prioC2").attr("src", "/img/icons/8afbd59ffe091239a7c6f1e961b267.gif");
    $("#prioC3").attr("src", "/img/icons/4acc67e1ca4d8debb1b114abcb7c1e.gif");
    switch (prioCrystal) {
        case 1:
            $("#prioC1").attr("src", "/img/icons/b357323b99e20a46fc0b2495728351.gif");
            break;
        case 2:
            $("#prioC2").attr("src", "/img/icons/f8959fe540cd329f3a764ad9aeaf93.gif");
            break;
        case 3:
            $("#prioC3").attr("src", "/img/icons/823b3270ed0f4a243287c12d4ee5f8.gif");
            break
    }
    $("#prioD1").attr("src", "/img/icons/4b53e83f8b8583ea279fd26f3a55a5.gif");
    $("#prioD2").attr("src", "/img/icons/8afbd59ffe091239a7c6f1e961b267.gif");
    $("#prioD3").attr("src", "/img/icons/4acc67e1ca4d8debb1b114abcb7c1e.gif");
    switch (prioDeuterium) {
        case 1:
            $("#prioD1").attr("src", "/img/icons/b357323b99e20a46fc0b2495728351.gif");
            break;
        case 2:
            $("#prioD2").attr("src", "/img/icons/f8959fe540cd329f3a764ad9aeaf93.gif");
            break;
        case 3:
            $("#prioD3").attr("src", "/img/icons/823b3270ed0f4a243287c12d4ee5f8.gif");
            break
    }
}
function selectPrio(d, c) {
    switch (d) {
        case"metal":
            if (prioMetal == c) {
                break
            }
            if (prioCrystal == c) {
                prioCrystal = prioMetal
            }
            if (prioDeuterium == c) {
                prioDeuterium = prioMetal
            }
            prioMetal = c;
            break;
        case"crystal":
            if (prioCrystal == c) {
                break
            }
            if (prioMetal == c) {
                prioMetal = prioCrystal
            }
            if (prioDeuterium == c) {
                prioDeuterium = prioCrystal
            }
            prioCrystal = c;
            break;
        case"deuterium":
            if (prioDeuterium == c) {
                break
            }
            if (prioMetal == c) {
                prioMetal = prioDeuterium
            }
            if (prioCrystal == c) {
                prioCrystal = prioDeuterium
            }
            prioDeuterium = c;
            break
    }
    $('form input[name="prioMetal"]').val(prioMetal);
    $('form input[name="prioCrystal"]').val(prioCrystal);
    $('form input[name="prioDeuterium"]').val(prioDeuterium);
    updatePrioButtons()
}
function updateHoldingOrExpTime() {
    if ($('form input[name="mission"]').val() == missionHold) {
        $('form input[name="holdingOrExpTime"]').val($('form input[name="holdingtime"]').val());
        holdingTime = $('form [name="holdingtime"]').val()
    } else {
        if ($('form input[name="mission"]').val() == missionExpedition) {
            $('form input[name="holdingOrExpTime"]').val($('form input[name="expeditiontime"]').val());
            holdingTime = $('form [name="expeditiontime"]').val()
        } else {
            $('form input[name="holdingOrExpTime"]').val(0);
            holdingTime = 0
        }
    }
}
function checkRessourceByType(h) {
    var g = getValue($("#" + h).val());
    var f = 0;
    var k = 0;
    if (h == "metal") {
        f = metalOnPlanet;
        k = cargoSpace - crystal - deuterium
    } else {
        if (h == "crystal") {
            f = crystalOnPlanet;
            k = cargoSpace - metal - deuterium
        } else {
            if (h == "deuterium") {
                f = deuteriumOnPlanet;
                k = cargoSpace - metal - crystal
            } else {
                f = metalOnPlanet;
                k = cargoSpace - crystal - deuterium
            }
        }
    }
    if (g != "") {
        var l = parseInt(g);
        l = isNaN(l) ? 0 : l;
        l = Math.abs(l);
        l = Math.min(l, f);
        if (l > k) {
            l = k
        }
        updateRessVarByType(h, l);
        formatNumber($("#" + h), l)
    } else {
        updateRessVarByType(h, 0)
    }
}
function updateRessVarByType(d, c) {
    if (d == "metal") {
        metal = c
    } else {
        if (d == "crystal") {
            crystal = c
        } else {
            if (d == "deuterium") {
                deuterium = c
            } else {
                metal = c
            }
        }
    }
}
function resetByRessourceType(b) {
    if (b == "metal") {
        metal = 0
    }
    if (b == "crystal") {
        crystal = 0
    }
    if (b == "deuterium") {
        deuterium = 0
    }
    $("#" + b).val(0);
    updateVariables()
}
function maxMetal() {
    var b;
    b = cargoSpace - crystal - deuterium;
    b = Math.max(b, 0);
    b = Math.min(b, metalOnPlanet);
    metal = Math.max(metal, b);
    formatNumber($("#metal"), metal)
}
function maxCrystal() {
    var b;
    b = cargoSpace - metal - deuterium;
    b = Math.max(b, 0);
    b = Math.min(b, crystalOnPlanet);
    crystal = Math.max(crystal, b);
    formatNumber($("#crystal"), crystal)
}
function maxDeuterium() {
    var b;
    b = cargoSpace - metal - crystal;
    b = Math.max(b, 0);
    b = Math.min(b, deuteriumOnPlanet - consumption);
    b = Math.max(b, 0);
    deuterium = Math.max(deuterium, b);
    formatNumber($("#deuterium"), deuterium)
}
function maxAll() {
    metal = 0;
    crystal = 0;
    deuterium = 0;
    maxDeuterium();
    maxCrystal();
    maxMetal()
}
function updateTimesFleet3() {
    durationAKS = durationAKS - 1;
    checkTimes()
}
function checkTimes() {
    var f = duration;
    if ($("#missionButton2").hasClass("selected")) {
        f = Math.max(durationAKS, duration)
    }
    var e = serverTime.getTime() + 1000 * f;
    var d = e + 1000 * duration + holdingTime * 3600 * 1000;
    if (durationAKS >= duration) {
        $("#durationAKS").html(getFormatedTime(durationAKS))
    }
    $("#arrivalTime").text(getFormatedDate(e, "[d].[m].[y] [G]:[i]:[s]"));
    $("#returnTime").text(getFormatedDate(d, "[d].[m].[y] [G]:[i]:[s]"))
}
function setSelected(b) {
    $("#missions>li>a.selected").removeClass("selected");
    $("#missionButton" + b).addClass("selected");
    isAggressiveMission = ((b == 1) || (b == 2) || (b == 6) || (b == 9));
    $("#fightAfterRetreat,#aks,#holdtimeline,#expeditiontimeline,.prioButton").hide();
    switch (b) {
        case 1:
            $("#fightAfterRetreat").show();
            $(".prioButton").show();
            break;
        case 2:
            $("#aks").show();
            $(".prioButton").show();
            break;
        case 5:
            $("#holdtimeline").show();
            break;
        case 8:
            $("#expeditiontimeline").hide();
            break;
        case 15:
            $("#expeditiontimeline").show();
            break
    }
    checkTimes()
}
function updateMission(g, e, f, h) {
    $(".missionName").text(g);
    $(".mission_description").text(e);
    $("#missionNameWrapper").removeClass("off");
    $(".briefing_overlay").hide();
    if (f == "off") {
        $("#missionNameWrapper").addClass("off");
        $(".briefing_overlay").show()
    }
    $('form input[name="mission"]').val(h);
    updateHoldingOrExpTime();
    updateVariables()
}
var validated = false;
var sendingEnabled = true;
var shipIDs = [];
var speeds = [];
var completeConsumptions = [];
var maxSpeed = 0;
var speedFactor = 1;
var maxGalaxy = null;
var maxSystem = null;
var maxPosition = null;
var DONUT_GALAXY = null;
var DONUT_SYSTEM = null;
var currentGalaxy = 1;
var currentSystem = 1;
var currentPosition = 1;
var currentPlanetType = 1;
var targetGalaxy = 1;
var targetSystem = 1;
var targetPosition = 1;
var defaultTargetGalaxy = 1;
var defaultTargetSystem = 1;
var defaultTargetPosition = 1;
var speed = 0;
var holdingTime = 0;
var metal = 0;
var crystal = 0;
var deuterium = 0;
var currentDeuterium = 0;
var returnLink = "";
var checkUrl = "";
var colonizationID = 0;
var missionNone = null;
var missionUnionattack = null;
var distance = 5;
var duration = 0;
var consumption = 0;
var cargoSpace = 0;
var cargoLeft = 0;
var locaAllPlanet = "";
var locaAllMoon = "";
var locaAllDebris = "";
var metal = 0;
var crystal = 0;
var deuterium = 0;
var distance = 5;
var duration = 0;
var consumption = 0;
var cargoCapacity = 0;
var fuelCapacity = 0;
var metalOnPlanet = 0;
var crystalOnPlanet = 0;
var deuteriumOnPlanet = 0;
var returnLink = "";
var missionHold = null;
var missionExpedition = null;
var durationAKS = 0;
var prioMetal;
var prioCrystal;
var prioDeuterium;
var isAggressiveMission = false;
function getDistance() {
    var e;
    var f;
    var h;
    var g;
    e = Math.abs(currentGalaxy - targetGalaxy);
    f = Math.abs(currentSystem - targetSystem);
    h = Math.abs(currentPosition - targetPosition);
    if (e != 0) {
        g = Math.abs(e - maxGalaxy);
        if (DONUT_GALAXY == 1 && (g < e)) {
            return g * 20000
        } else {
            return e * 20000
        }
    } else {
        if (f != 0) {
            g = Math.abs(f - maxSystem);
            if (DONUT_SYSTEM == 1 && (g < f)) {
                return g * 5 * 19 + 2700
            } else {
                return f * 5 * 19 + 2700
            }
        } else {
            if (h != 0) {
                return h * 5 + 1000
            } else {
                return 5
            }
        }
    }
}
function getDuration() {
    return Math.round(((35000 / speed * Math.sqrt(distance * 10 / maxSpeed) + 10) / speedFactor))
}
function getConsumption(h) {
    var k = 0;
    var f = 0;
    var g = 0;
    var l = Math.max(1, duration * speedFactor - 10);
    for (i = 0, maxI = shipIDs.length; i < maxI; i++) {
        if (h == 0 || h == null || shipIDs[i] == h) {
            g++;
            shipSpeedValue = 35000 / l * Math.sqrt(distance * 10 / speeds[i]);
            f += completeConsumptions[i] * holdingTime;
            k += completeConsumptions[i] * distance / 35000 * ((shipSpeedValue / 10) + 1) * ((shipSpeedValue / 10) + 1)
        }
    }
    if (g > 0) {
        k = Math.round(k) + 1;
        if (holdingTime > 0) {
            k += Math.max(Math.floor(f / 10), 1)
        }
        k = Math.ceil(Math.round(k * fleetDeutSaveFactor));
        return k
    } else {
        return 0
    }
}
function getFreeStorage() {
    return cargoCapacity
}
function updateVariables() {
    if (currentPage == "fleet2") {
        prepareVariables()
    }
    distance = getDistance();
    duration = getDuration();
    consumption = getConsumption();
    cargoSpace = getFreeStorage();
    cargoLeft = cargoSpace - metal - crystal - deuterium;
    refreshFormData()
}
function refreshFormData() {
    $("#duration").html(formatTime(duration) + " h");
    $("#distanceValue").html(tsdpkt(distance));
    styleClass = cargoSpace < 0 ? "overmark" : "undermark";
    $("#storage").html('<span class="' + styleClass + '">' + tsdpkt(cargoSpace) + "</span>");
    styleClass = ((consumption > fuelCapacity) || (consumption > $("#resources_deuterium").text().replace(/\./g, ""))) ? "overmark" : "undermark";
    $("#consumption").html('<span class="' + styleClass + '">' + tsdpkt(consumption) + " (" + Math.ceil(100 * consumption / fuelCapacity) + "%)</span>");
    styleClass = cargoLeft < 0 ? "overmark" : "undermark";
    $("#remainingresources").html('<span class="' + styleClass + '">' + tsdpkt(cargoLeft) + "</style>");
    $(".bar_container").data("currentAmount", cargoSpace - cargoLeft);
    $(".bar_container").data("capacity", cargoSpace);
    refreshBars("bar_container", "filllevel_bar");
    setTType();
    if (currentPage == "fleet2") {
        modifyPlanetName()
    }
    checkOk()
}
function modifyPlanetName() {
    newName = getOwnName();
    if (newName != "") {
        $("#targetPlanetName").html(newName);
        $("#statusTarget").html(newName)
    }
    hideStatusBar()
}
function checkOk() {
    switch (currentPage) {
        case"fleet2":
            if ((currentGalaxy == $("#galaxy").val() && currentSystem == $("#system").val() && currentPosition == $("#position").val() && currentPlanetType == $("#type").val()) || $("#galaxy").val() == "" || $("#system").val() == "" || $("#position").val() == "" || consumption > currentDeuterium || consumption > fuelCapacity || cargoSpace < 0) {
                validated = false;
                $("#continue").attr("class", "off")
            } else {
                validated = true;
                $("#continue").attr("class", "on")
            }
            break;
        case"fleet3":
            if (isMissionValid() && cargoLeft >= 0) {
                validated = true;
                $("#start").attr("class", "on")
            } else {
                validated = false;
                $("#start").attr("class", "off")
            }
            break
    }
}
if (typeof Object.assign !== "function") {
    Object.assign = function (g) {
        if (g == null) {
            throw new TypeError("Cannot convert undefined or null to object")
        }
        g = Object(g);
        for (var e = 1; e < arguments.length; e++) {
            var h = arguments[e];
            if (h != null) {
                for (var f in h) {
                    if (Object.prototype.hasOwnProperty.call(h, f)) {
                        g[f] = h[f]
                    }
                }
            }
        }
        return g
    }
}
if (typeof Number.isInteger !== "function") {
    Number.isInteger = function (b) {
        return (typeof b === "number" && isFinite(b) && b > -9007199254740992 && b < 9007199254740992 && Math.floor(b) === b)
    }
}
var Formatter = {
    timeUnits: {
        second: 1,
        minute: 60,
        hour: 60 * 60,
        day: 24 * 60 * 60,
        week: 7 * 24 * 60 * 60
    }, secondsToTime: function (k, h) {
        var l = {
            segments: -1,
            delimiter: " ",
            units: {
                weeks: "w",
                days: "d",
                hours: "h",
                minutes: "m",
                seconds: "s"
            },
            now: "now"
        };
        h = Object.assign(l, h);
        if (!Number.isInteger(k)) {
            throw"Integer expected for parameter seconds"
        }
        if (k <= 0) {
            return h.now
        }
        var m = {
            weeks: Math.floor(k / Formatter.timeUnits.week),
            days: Math.floor((k % Formatter.timeUnits.week) / Formatter.timeUnits.day),
            hours: Math.floor((k % Formatter.timeUnits.day) / Formatter.timeUnits.hour),
            minutes: Math.floor((k % Formatter.timeUnits.hour) / Formatter.timeUnits.minute),
            seconds: k % Formatter.timeUnits.minute
        };
        var g = [];
        for (var n in m) {
            if ((m[n] > 0) && (h.segments == -1 || g.length < h.segments)) {
                g.push(m[n] + h.units[n])
            }
        }
        return g.join(h.delimiter)
    }, secondsToPeriod: function (h) {
        if (!Number.isInteger(h)) {
            throw"Integer expected for parameter seconds"
        }
        if (h <= 0) {
            return "PT0H0M0S"
        }
        var k = {
            days: {value: Math.floor(h / Formatter.timeUnits.day), unit: "D"},
            hours: {
                value: Math.floor((h % Formatter.timeUnits.day) / Formatter.timeUnits.hour),
                unit: "H"
            },
            minutes: {
                value: Math.floor((h % Formatter.timeUnits.hour) / Formatter.timeUnits.minute),
                unit: "M"
            },
            seconds: {value: h % Formatter.timeUnits.minute, unit: "S"}
        };
        var f = [], g = [];
        for (var l in k) {
            if (k[l].value > 0) {
                if (l == "days") {
                    f.push(k[l].value + k[l].unit)
                } else {
                    g.push(k[l].value + k[l].unit)
                }
            }
        }
        return "P" + f.join("") + (g.length > 0 ? "T" + g.join("") : "")
    }, numberToString: function (M, K) {
        var L = 0;
        var J = 3;
        var v = 1;
        var F = 2;
        var y = 3;
        var w = function (a, b) {
            if (typeof a !== "number") {
                throw"Numeric expected for parameter number"
            }
            if (!Number.isInteger(b)) {
                throw"Integer expected for parameter precision"
            }
            if (b <= 0) {
                return Math.round(a)
            }
            return Math.round(a * Math.pow(10, b)) / Math.pow(10, b)
        };
        var N = function (a, b) {
            if (typeof a !== "number") {
                throw"Numeric expected for parameter number"
            }
            if (!Number.isInteger(b)) {
                throw"Integer expected for parameter precision"
            }
            if (b <= 0) {
                return Math.ceil(a)
            }
            return Math.ceil(a * Math.pow(10, b)) / Math.pow(10, b)
        };
        var C = function (a, b) {
            if (typeof a !== "number") {
                throw"Numeric expected for parameter number"
            }
            if (!Number.isInteger(b)) {
                throw"Integer expected for parameter precision"
            }
            if (b <= 0) {
                return Math.floor(a)
            }
            return Math.floor(a * Math.pow(10, b)) / Math.pow(10, b)
        };
        var G = function (b, a, c) {
            switch (a) {
                case v:
                    b = w(b, c);
                    break;
                case F:
                    b = N(b, c);
                    break;
                case y:
                    b = C(b, c);
                    break
            }
            return b
        };
        var I = {
            transform: v,
            precision: {minimum: L, maximum: J},
            separators: {thousands: ",", decimals: "."},
            boundaries: {1000000: "M", 1000000000: "Bn"}
        };
        K = Object.assign(I, K);
        if (typeof M !== "number") {
            throw"Numeric expected for parameter number"
        }
        if (typeof K.precision === "undefined" || typeof K.precision.minimum === "undefined") {
            throw"Minimum precision not specified"
        }
        if (typeof K.precision === "undefined" || typeof K.precision.maximum === "undefined") {
            throw"Maximum precision not specified"
        }
        if (K.precision.minimum > K.precision.maximum) {
            throw"Minimum precision larger than maximum precision"
        }
        var x = Object.keys(K.boundaries).sort(function (a, b) {
            return parseFloat(b) - parseFloat(a)
        });
        var H = "";
        for (var D = 0; D < x.length; D++) {
            var B = parseInt(x[D]);
            if (B === 0) {
                continue
            }
            if (Math.abs(M) >= B) {
                M /= B;
                H = K.boundaries[x[D]];
                break
            }
        }
        var A = K.precision.maximum;
        while ((M = G(M, K.transform, A)) == G(M, K.transform, A - 1) && A > K.precision.minimum) {
            A--
        }
        M = M.toFixed(A);
        var E = M.toString().split(".");
        E[0] = E[0].replace(/\B(?=(\d{3})+(?!\d))/g, K.separators.thousands);
        return E.join(K.separators.decimals) + H
    }
};
function submitOnKey(b) {
    if (b == 37) {
        system = system > 1 ? parseInt(system) - 1 : maxSystems;
        loadContent(galaxy, system, true)
    } else {
        if (b == 39) {
            system = system < maxSystems ? parseInt(system) + 1 : 1;
            loadContent(galaxy, system, true)
        } else {
            if (b == 40) {
                galaxy = galaxy > 1 ? parseInt(galaxy) - 1 : maxGalaxies;
                loadContent(galaxy, system, true)
            } else {
                if (b == 38) {
                    galaxy = galaxy < maxGalaxies ? parseInt(galaxy) + 1 : 1;
                    loadContent(galaxy, system, true)
                }
            }
        }
    }
}
function keyevent(d) {
    var c;
    if ($(":focus").closest(".ui-dialog").length) {
        return true
    }
    if ($(":focus").closest(".chat_box_textarea").length) {
        return true
    }
    if (window.event) {
        c = window.event.keyCode
    } else {
        if (d) {
            c = d.which
        } else {
            return true
        }
    }
    submitOnKey(c)
}
function launchMissiles(b) {
    var b = $.parseJSON(b);
    if (b.status) {
        $("#missileValue").html(b.rockets)
    }
    errorBoxAsArray(b.errorbox);
    $("#rocketattack").parent().dialog("close")
}
function errorBoxSubmitYes() {
    location.href = officersLink
}
function movePlanet(d, e) {
    function f() {
        console.log('movePlanet()');
        $.post(d, function (a) {
            if ((a == "") || (a == null)) {
                fadeBox(galaxyLoca.reservationSuccess, false);
                setTimeout('reload_page("' + e + '")', 3000)
            } else {
                fadeBox(a, true)
            }
        })
    }

    errorBoxDecision(galaxyLoca.questionTitle, galaxyLoca.question, LocalizationStrings.yes, LocalizationStrings.no, f)
}
function addToTable(n, q, m) {
    var l = n;
    if (m != null) {
        l += " (" + tsdpkt(m) + ") " + LocalizationStrings.ok
    }
    if (isMobile) {
        fadeBox(l, q != "success");
        return
    }
    var r = new Date();
    var s = "fleetstatus" + r.getTime();
    var o = 'id="' + s + '"';
    var p = 'class="' + q + '"';
    var u = "<div " + o + " " + p + ">" + l + "</div>";
    if ($("#fleetstatusrow").has("div").length) {
        $("#fleetstatusrow").empty()
    }
    $(u).prependTo("#fleetstatusrow").fadeOut(3000, function () {
        $(this).remove()
    })
}
function setShips(d, e) {
    var f = document.getElementById(d);
    f.innerHTML = e
}
function displayContentGalaxy(c) {
    dataObject = c; // JSON.parse(c);
    var d = getTooltipSelector("#inhalt");
    removeTooltip(d);
    $("#galaxyContent").html(dataObject.galaxy);
    tabletInitGalaxyDetails();
    eventBDayInitGalaxy();
    $("#galaxyLoading").hide();
    if (preserveSystemOnPlanetChange) {
        $(".planetlink, .moonlink").querystring({
            galaxy: galaxy,
            system: system
        })
    }
    updateAjaxResourcebox(dataObject.resources)
}
function submitForm() {
    galaxy = $("#galaxy_input").val();
    system = $("#system_input").val();
    if (0 === galaxy.length || !$.isNumeric(+galaxy)) {
        galaxy = 1
    }
    if (0 === system.length || !$.isNumeric(+system)) {
        system = 1
    }
    loadContent(galaxy, system, true)
}
function loadContent(f, d, e) {
    $("#galaxyLoading").show();
    if (0 === f.length || !$.isNumeric(+f)) {
        f = 1
    }
    if (0 === d.length || !$.isNumeric(+d)) {
        d = 1
    }
    $("#galaxy_input").val(f);
    $("#system_input").val(d);
    removeTooltip($("#galaxytable tbody *"));
    if (e) {
        $.each(buildListCountdowns, function () {
            timerHandler.removeCallback(this.timer)
        })
    }
    console.log('loadContent()');
    $.post(contentLink, {galaxy: f, system: d}, displayContentGalaxy)
}
function errorBoxDeuterium() {
    fadeBox(galaxyLoca.deuteriumNeeded, true)
}
function doScan(d, c) {
    sendShips(constants.espionage, galaxy, system, d, c, spionageAmount)
}
function initPhalanx() {
    getAjaxResourcebox();
    $(".eventFleet:odd").addClass("odd");
    $(".partnerInfo:even").addClass("part-even");
    $(".toggleInfos").click(function () {
        id = $(this).attr("rel");
        if ($(this).attr("class") == "toggleInfos infosOpen") {
            $(this).removeClass("infosOpen");
            $(this).addClass("infosClosed");
            $(this).children().attr("src", "/img/icons/de1e5f629d9e47d283488eee0c0ede.gif");
            $("." + id).attr("style", "display: none;")
        } else {
            $(this).addClass("infosOpen");
            $(this).removeClass("infosClosed");
            $(this).children().attr("src", "/img/icons/577565fadab7780b0997a76d0dca9b.gif");
            $("." + id).attr("style", "display: block;")
        }
    });
    var b = $(".overlayDiv.phalanx").siblings(".ui-dialog-titlebar");
    if (b.find(".refreshPhalanxLink").length) {
        $("#phalanxWrap .refreshPhalanxLink").remove()
    } else {
        b.find(".ui-dialog-title").append($("#phalanxWrap .refreshPhalanxLink"))
    }
}
function initMissleAttackLayer() {
    $("#rocketattack").parent().dialog("option", "title", $("#rocketattack").attr("data-title"));
    $("#rocketattack input#anz").keyup(function () {
        checkIntInput($(this), 1, $(this).data("max"))
    }).change(function () {
        checkIntInput($(this), 1, $(this).data("max"))
    }).focus();
    $("#rocketattack #number").bind("click", function () {
        var a = $("#rocketattack input#anz");
        if (parseInt(a.val()) != a.data("max")) {
            a.val(a.data("max"))
        } else {
            a.val("1")
        }
    });
    $("#rocketattack #priority a").bind("click", function () {
        var d = $(this);
        var a = $("#primaryTarget");
        $("#rocketattack #priority a").not(d).removeClass("active");
        if (d.hasClass("active")) {
            d.removeClass("active");
            a.val("");
            $("#noPriorityInfo").show()
        } else {
            d.addClass("active");
            a.val(d.attr("ref"));
            $("#noPriorityInfo").hide()
        }
    });
    $("form#rocketForm").submit(function () {
        ajaxFormSubmit("rocketForm", $(this).attr("action"), launchMissiles);
        return false
    });
    function b() {
        var a = $("#rocketattack #arrivalTime #timer");
        a.html(getFormatedDate(serverTime.getTime() + 1000 * a.data("duration"), "[d].[m].[y] [G]:[i]:[s]"))
    }

    timerHandler.appendCallback(b);
    b()
}
function displayMiniFleetMessage(d) {
    var f = d.message;
    if (typeof(d.coordinates) != "undefined") {
        f += " [" + d.coordinates.galaxy + ":" + d.coordinates.system + ":" + d.coordinates.position + "]"
    }
    if (d.success) {
        var e = "#ownFleetStatus_" + d.coordinates.position + "_" + d.planetType;
        switch (d.type) {
            case 1:
                $(e).removeClass("fleetNeutral");
                $(e).attr("title", galaxyLoca.fleetAttacking).addClass("fleetHostile").addClass("tooltip");
                break;
            case 2:
                $(e).attr("title", galaxyLoca.fleetUnderway).addClass("fleetNeutral").addClass("tooltip");
                break
        }
        addToTable(f, "success", d.shipsSent);
        $("#slotUsed").html(tsdpkt(d.slots));
        setShips("probeValue", tsdpkt(d.probes));
        setShips("recyclerValue", tsdpkt(d.recyclers));
        setShips("missileValue", tsdpkt(d.missiles))
    } else {
        addToTable(f, "error")
    }
    shipsendingDone = 1
}
function filterToggle(d) {
    var e = d.target;
    var f;
    switch (e.id) {
        case"filter_empty":
            f = ".empty_filter";
            break;
        case"filter_inactive":
            f = ".inactive_filter";
            break;
        case"filter_vacation":
            f = ".vacation_filter";
            break;
        case"filter_strong":
            f = ".strong_filter";
            break;
        case"filter_newbie":
            f = ".newbie_filter";
            break
    }
    e = $(e);
    if (e.hasClass("filter_active")) {
        e.removeClass("filter_active");
        $(f).each(function (b, a) {
            $(this).removeClass("filtered_" + $(d.target)[0].id)
        });
        sendFilterToggle($(d.target)[0].id, 0);
        d.stopPropagation()
    } else {
        e.addClass("filter_active");
        $(f).each(function (b, a) {
            $(this).addClass("filtered_" + $(d.target)[0].id)
        });
        sendFilterToggle($(d.target)[0].id, 1);
        d.stopPropagation()
    }
}
function sendFilterToggle(c, d) {
    $.ajax({
        type: "POST",
        url: "?page=togglefilter",
        dataType: "json",
        data: {id: c, state: d},
        success: function (a) {
        },
        error: function () {
        }
    })
}
var DOM_GET_ELEMENT_BY_ID_CACHE = [];
function getElementByIdWithCache(b) {
    if (!DOM_GET_ELEMENT_BY_ID_CACHE[b]) {
        DOM_GET_ELEMENT_BY_ID_CACHE[b] = document.getElementById(b)
    }
    return DOM_GET_ELEMENT_BY_ID_CACHE[b]
}
function number_format(v, x, o, w) {
    o = o || LocalizationStrings.decimalPoint;
    w = w || LocalizationStrings.thousandSeperator;
    var q = "";
    var p = v.toString();
    var r = p.indexOf("e");
    if (r > -1) {
        q = p.substring(r);
        v = parseFloat(p.substring(0, r))
    }
    if (x != null) {
        var n = Math.pow(10, x);
        v = Math.round(v * n) / n
    }
    var y = v < 0 ? "-" : "";
    var s = (v > 0 ? Math.floor(v) : Math.abs(Math.ceil(v))).toString();
    var u = v.toString().substring(s.length + y.length);
    o = o != null ? o : ".";
    u = x != null && x > 0 || u.length > 1 ? (o + u.substring(1)) : "";
    if (x != null && x > 0) {
        for (i = u.length - 1, z = x; i < z; ++i) {
            u += "0"
        }
    }
    w = (w != o || u.length == 0) ? w : null;
    if (w != null && w != "") {
        for (i = s.length - 3; i > 0; i -= 3) {
            s = s.substring(0, i) + w + s.substring(i)
        }
    }
    return y + s + u + q
}
function gfNumberGetHumanReadable(g, e, f) {
    g = Math.floor(g);
    e = e || false;
    var h = "";
    var f = f || 3;
    if (e) {
        if (g >= 1000000000) {
            h = LocalizationStrings.unitMilliard;
            g = g / 1000000000
        } else {
            if (g >= 1000000) {
                h = LocalizationStrings.unitMega;
                g = g / 1000000
            }
        }
    }
    floorWithPrecision = function (a, b) {
        return Math.floor(a * Math.pow(10, b)) / Math.pow(10, b)
    };
    g = floorWithPrecision(g, f);
    while (f >= 0) {
        if (floorWithPrecision(g, f - 1) != g) {
            break
        }
        f = f - 1
    }
    return number_format(g, f, LocalizationStrings.decimalPoint, LocalizationStrings.thousandSeperator) + h
}
function getNumberFormatShort(f, e) {
    if (typeof(e) == "undefined") {
        e = 0
    }
    f = Math.floor(f);
    var d = "";
    if (f >= 1000000000) {
        d = LocalizationStrings.unitMilliard;
        f = f / 1000000000
    }
    if (f >= 1000000) {
        d = LocalizationStrings.unitMega;
        f = f / 1000000
    }
    if (f >= 1000) {
        d = LocalizationStrings.unitKilo;
        f = f / 1000
    }
    return number_format(f, e, LocalizationStrings.decimalPoint, LocalizationStrings.thousandSeperator) + d
}
function createExpireTime(c) {
    var d = new Date();
    c = c * 1000;
    d.setTime(c);
    return d
}
function initHighscore() {
    $("a.navButton, a.subnavButton").click(function () {
        var h = $(this).attr("rel");
        var a = $(this).parent();
        var l = "";
        if (a.attr("id") == "typeButtons") {
            $("#typeButtons > a.active").removeClass("active")
        } else {
            if (a.attr("id") == "categoryButtons") {
                $("#categoryButtons > a.active").removeClass("active");
                $("#typeButtons a.active").each(function () {
                    h = $(this).attr("rel")
                })
            }
        }
        if (searchRelId != null && a.attr("id") == "typeButtons" || a.attr("id") == "subnav_fleet") {
            l = "&searchRelId=" + searchRelId
        }
        $(".subnavButton[rel!=" + h + "]").removeClass("active");
        $("#stat_list_content").html(LocalizationStrings.loading);
        $(this).addClass("active");
        var b = $("#categoryButtons > a.active").attr("rel");
        var k = highscoreContentUrl + "?category=" + b + "&type=" + h + l;
        if ((a.attr("id") == "typeButtons" || $(this).hasClass("subnavButton")) && searchSite != site) {
            k = k + "&site=" + site
        }
        removeTooltip(getTooltipSelector("#highscoreContent #ranks"));
        ajaxSumbit(k, "send", "stat_list_content");
        if ($(".navButton.active").attr("rel") == 1) {
            $("#highscoreContent .header h2").text(highscoreLoca.playerHighscore)
        } else {
            $("#highscoreContent .header h2").text(highscoreLoca.allianceHighscore)
        }
    });
    $(".stat_filter").click(function () {
        var a = $(this).attr("id");
        $(".subnav").hide();
        $("#subnav_" + a).fadeIn("slow")
    });
    var d = $("#ranks").offset().top;
    var c = $("#scrollToTop");
    $(window).unbind("scroll.highscoreTop").bind("scroll.highscoreTop", function (a) {
        var b = $(this).scrollTop();
        if (b > d) {
            c.css("visibility", "visible")
        } else {
            c.css("visibility", "hidden")
        }
    });
    $(document).undelegate(".scrollToTop", "click").delegate(".scrollToTop", "click", function () {
        $("html, body").animate({scrollTop: 0}, 50)
    })
}
function initHighscoreContent() {
    if (userWantsFocus) {
        if ($("#position" + searchPosition).length > 0) {
            $("html, body").animate({scrollTop: Math.max(0, $("#position" + searchPosition).offset().top - 200)}, 1000)
        }
    }
    $(".changeSite").change(function () {
        var a = $(this).val();
        $("#stat_list_content").html('<div class="ajaxLoad">' + LocalizationStrings.loading + "</div>");
        ajaxCall(highscoreContentUrl + "?category=" + currentCategory + "&type=" + currentType + "&page=" + a, "stat_list_content")
    });
    var d = $("#scrollToTop");
    var e = $("#ranks thead .score");

    function f() {
        if (e.length) {
            d.css("left", e.offset().left)
        }
    }

    f();
    $(window).unbind("resize.highscoreTop").bind("resize.highscoreTop", f)
}
function saveImperiumOrder(e, d) {
    var f = "impSortOrder";
    if (d) {
        f = "impSortOrderMoon"
    }
    $.ajax({
        url: saveUrl,
        method: "post",
        dataType: "json",
        data: {ajax: 1, type: f, planets: $(e).sortable("toArray")}
    })
}
function clearImperiumOrder() {
    $.ajax({
        url: saveUrl,
        method: "post",
        dataType: "json",
        data: {ajax: 1, type: "reset"},
        success: function (b) {
            if (!b.error) {
                location.reload()
            }
        }
    })
}
function createSummaryHtml(n) {
    var q = "";
    var l = null;
    var o = 0;
    var r = 0;
    var k = "";
    q = q + '<div class="planetHead"><div class="planetname">' + n.translations.summary + '</div><div class="planetImg"><img src="/img/icons/7efb2e73ca11d2344bbed43668da10.jpg"/></div><div class="planetData"><ul><li class="coords textLeft"></li><li class="fields textRight"></li></ul></div><div class="clearfloat"/></div>';
    for (group in n.groups) {
        q = q + '<div class="row" /><div class="values ' + group + " group" + group + '">';
        for (r = 0; k = n.groups[group][r]; r++) {
            if (n.translations.planets[k] == null) {
                continue
            }
            var m = {hourly: 0, daily: 0, weekly: 0};
            if (k == "name") {
                o = n.translations.summary
            } else {
                o = 0;
                if (group == "research") {
                    if (!isNaN(n.planets[0][k])) {
                        o = n.planets[0][k]
                    }
                } else {
                    $.each(n.planets, function () {
                        l = this;
                        if (!isNaN(l[k])) {
                            o = o + parseInt(l[k]);
                            if (group == "supply" && !isNaN(l.production.hourly[k - 1])) {
                                m.hourly += l.production.hourly[k - 1];
                                m.daily += l.production.daily[k - 1];
                                m.weekly += l.production.weekly[k - 1]
                            }
                        }
                    })
                }
            }
            if (group == "supply" || group == "station") {
                o = "&#x00F8; " + tsdpkt(round(o / n.planets.length, 1))
            } else {
                if (group == "items") {
                    o = "&nbsp;"
                } else {
                    o = tsdpkt(o)
                }
            }
            if (group == "supply" && k != "name" && m.hourly > 0) {
                var p = "<table><tr><td>" + n.translations.production.hourly + ":</td><td style=&quot;text-align: right;&quot;>" + tsdpkt(m.hourly) + "</td></tr><tr><td>" + n.translations.production.daily + ":</td><td style=&quot;text-align: right;&quot;>" + tsdpkt(m.daily) + "</td></tr><tr><td>" + n.translations.production.weekly + ":</td><td style=&quot;text-align: right;&quot;>" + tsdpkt(m.weekly) + "</td></tr></table>";
                p = p.replace(/</, "&lt;").replace(/>/, "&gt;");
                q = q + '<div class="tooltipRight ' + k + '" title="' + p + '">' + o + "</div>"
            } else {
                q = q + '<div class="' + k + '">' + o + "</div>"
            }
        }
        q = q + "</div>"
    }
    q = '<div id="planet0" class="planet summary">' + q + "</div>";
    return q
}
function createHeaderHtml(g) {
    var h = "";
    var e = 0;
    var f = "";
    h = h + '<div id="wrapTL"><div id="tab-left"><a id="planetsTab" href="javascript:void(0);" class="active" title=""><span>' + g.translations.planetsTab + '</span></a><a id="moonsTab" href="javascript:void(0);" title="" class=""><span>' + g.translations.moonsTab + "</span></a></div></div>";
    for (group in g.groups) {
        h = h + '<div id="' + group + '" class="firstCat headers ' + group + " headers" + group + '" group="' + group + '"><h3 class="open"><span>' + g.translations.groups[group] + '</span></h3><ul class="secondCat ' + group + " group" + group + '">';
        for (e = 0; f = g.groups[group][e]; e++) {
            if (g.translations.planets[f] == null) {
                continue
            }
            h += '<li class="' + f + '">';
            if (g.translations.planets[f + "_full"] != g.translations.planets[f]) {
                h += '<span class="tooltipLeft" title="' + g.translations.planets[f + "_full"] + '">' + g.translations.planets[f] + "</span>"
            } else {
                h += "<span>" + g.translations.planets[f] + "</span>"
            }
            h += "</li>"
        }
        h = h + "</ul></div>"
    }
    h = '<div id="empireTab"><div class="wrapTab"><div class="tab-part01"></div><h2>' + g.translations.header + '</h2><span class="reset"><img src="/img/icons/f805c477d15ae3131b7c39c7d70e48.gif" width="16" height="16"><a href="javascript:void(0);" onClick="clearImperiumOrder(); return false;">' + g.translations.reset + '</a></span><div class="wrapCorner"></div><br class="clearfloat"/></div></div><div class="header">' + h + "</div>";
    return h
}
function createPlanetsHtml(m) {
    var r = "";
    var q = "";
    var n = "";
    var l = "";
    var k = "";
    var o = 0;
    var p = "";
    $.each(m.planets, function () {
        r = this;
        n = "";
        l = "";
        q = (r.name.length > 13) ? r.name.substr(0, 11) + "..." : r.name;
        n += '<div class="planetHead">';
        if (r.name != q) {
            n += '<div class="planetname tooltip" title="' + r.name + '">' + q + "</div>"
        } else {
            n += '<div class="planetname">' + q + "</div>"
        }
        if (isMobile) {
            n += '<div class="planetImg"><img class="' + r.border + '" src="' + r.image + '"/></div><div class="planetData"><div class="planetDataTop odd"><ul><li class="coords textLeft"><a class="dark_highlight_tablet" href="' + r.coordinatesLink + '" >' + r.coordinates + '</a></li><li class="coords"><span class="dark_highlight_tablet energy tooltipRight" title="' + (r.type == 3 ? r.diameterTooltip : r.energyTooltip) + '">' + (r.type == 3 ? "\u2300: " + r.diameter : r.energyDescr + r.energy) + '</span></li></ul></div><div class="planetDataTop"><ul class="planet_data_2"><li class="fields textLeft">' + r.fieldUsed + "/" + r.fieldMax + '</li><li class="fields textLeft">' + r.temperature + '</li></ul></div></div><div class="clearfloat"/></div>'
        } else {
            n += '<div class="planetImg"><img class="' + r.border + '" src="' + r.image + '"/></div><div class="planetData"><div class="planetDataTop odd"><ul><li class="coords textLeft"><a href="' + r.coordinatesLink + '" >' + r.coordinates + '</a></li><li class="fields textRight">' + r.fieldUsed + "/" + r.fieldMax + '</li></ul></div><div class="planetDataTop"><ul><li class="coords textLeft">' + (r.type == 3 ? r.diameterDescr : r.energyDescr) + '</li><li class="coords textRight">' + (r.type == 3 ? r.diameter : r.energy) + '</li></ul></div><div class="planetDataBottom odd"><ul><li class="fields textCenter">' + r.temperature + '</li></ul></div></div><div class="clearfloat"/></div>'
        }
        for (var a in m.groups) {
            n = n + '<div class="row" /><div class="values ' + a + " group" + a + '">';
            for (o = 0; p = m.groups[a][o]; o++) {
                p = String(p);
                if (r[p + "_html"] != null) {
                    p = p + "_html"
                }
                l = p;
                if (p.substring(p.length - 5) == "_html") {
                    l = p.substring(0, p.length - 5)
                }
                n = n + '<div class="' + l + '">' + r[p] + "</div>"
            }
            n = n + "</div>"
        }
        k = k + '<div id="planet' + this.id + '" class="planet">' + n + "</div>"
    });
    return k
}
function createImperiumHtml(n, r, p, m) {
    var u = 0;
    if (typeof p.planets != "undefined") {
        u = p.planets.length
    }
    var q = 345 + u * 165;
    $("#mainWrapper").attr("style", "width: " + q + "px");
    $(r).show();
    var o = createHeaderHtml(p) + '<div class="planetWrapper">' + createPlanetsHtml(p) + createSummaryHtml(p) + '</div><br class="clearfloat"/>';
    $(n).append(o);
    $(n + " .planetWrapper").sortable({
        start: function () {
            removeTooltip(getTooltipSelector())
        }, update: function () {
            saveImperiumOrder(n + " .planetWrapper", m)
        }
    }).disableSelection();
    for (group in p.groups) {
        $(n + " .headers" + group).click(function () {
            var b = n + " .group" + $(this).attr("group");
            var a = "#" + $(this).attr("group") + " h3";
            $(b).toggle();
            $(a).removeClass("openhover").removeClass("closehover").removeClass("close").removeClass("open");
            if ($(b).attr("style").toLowerCase().substr(9, 4) == "none") {
                $(a).addClass("close");
                $.cookie("impToggleState" + $(this).attr("group"), "1", {expires: 365})
            } else {
                $(a).addClass("open");
                $.cookie("impToggleState" + $(this).attr("group"), "0", {expires: 365})
            }
        });
        var s = $.cookie("impToggleState" + group);
        if (s != null && s == "1") {
            $("#" + group + " > h3").removeClass("open");
            $("#" + group + " > h3").addClass("close");
            $(n + " .group" + group).toggle()
        }
    }
    $(r).hide();
    var l = $.inArray(0, empireOrder);
    if (l > -1) {
        $(".planetWrapper .planet:eq(" + l + "):not(:last-child)").before($("#planet0"))
    }
    initTooltips()
}
function displayMessage(b) {
    location.reload(true)
}
function doUpgrade(m, q, p, l, n) {
    if (forcedToVacation) {
        errorBoxNotify(LocalizationStrings.error, LocalizationStrings.forcedVacationWarning, LocalizationStrings.ok);
        return
    }
    var o = {};
    o.modus = p;
    o.techid = m;
    o.listid = l;
    o.planet = q;
    var r = LocalizationStrings.lastSlotWarningPlanet;
    var k = upgradeUrl;
    if (p == 2) {
        k += "&token=" + cancelBuildingToken
    }
    if (planetType == 1) {
        r = LocalizationStrings.lastSlotWarningMoon
    }
    console.log('doUpgrade()');
    if (n) {
        errorBoxDecision(LocalizationStrings.attention, r, LocalizationStrings.yes, LocalizationStrings.no, function () {
            $.post(k, o, displayMessage)
        })
    } else {
        $.post(k, o, displayMessage)
    }
}
function initEmpire() {
    initConnectionErrorFunction();
    $(".secondCat").each(function () {
        $(this).find("li:last").addClass("catbox-end")
    });
    $(".values").each(function () {
        if (!$(this).hasClass("groupitems")) {
            $(this).find("div:even").addClass("even");
            $(this).find("div:odd").addClass("odd");
            $(this).find("div:last").addClass("box-end")
        }
        if ($(this).children().hasClass("equipment")) {
            $(this).children(".equipment").addClass("box-end")
        }
    });
    $("#settings li:last").addClass("set-end");
    $(".header h3").hover(function () {
        $(this).addClass($(this).attr("class") + "hover")
    }, function () {
        $(this).removeClass("openhover").removeClass("closehover")
    });
    $(".header h3").click(function () {
        $(this).removeClass("openhover").removeClass("closehover");
        var b = $(this).attr("class");
        if (b == "open") {
            $(this).addClass("close")
        } else {
            $(this).addClass("open")
        }
        $(this).removeClass(b)
    });
    $(".planet").hover(function () {
        $(this).addClass("move")
    }, function () {
        $(this).removeClass("move")
    });
    $(".values div img").hover(function () {
        $(this).addClass("imghover")
    }, function () {
        $(this).removeClass("imghover")
    });
    $("#planetsTab").click(function () {
        window.location.href = empireUrl + "&planetType=0"
    });
    if (moonCount > 0) {
        $("#moonsTab").click(function () {
            window.location.href = empireUrl + "&planetType=1"
        })
    } else {
        $("#moonsTab").addClass("nomoons")
    }
    if (planetType == 1) {
        $("#planetsTab").removeClass("active");
        $("#moonsTab").addClass("active")
    }
    initBuffBarEmpire()
}
function initBuffBarEmpire() {
    var b = $(".empireItems");
    b.anythingSlider({
        resizeContents: false,
        buildNavigation: false,
        buildStartStop: false,
        infiniteSlides: false,
        stopAtEnd: true
    });
    b.removeClass("hidden")
}
function toggleEvents(b) {
    if ($("#eventboxContent").is(":hidden")) {
        $("#eventboxContent").slideDown("fast");
        $("#js_eventDetailsClosed").hide();
        $("#js_eventDetailsOpen").show();
        if (typeof toggleEvents.loaded == "undefined" || !toggleEvents.loaded) {
            $("#eventboxContent").html('<img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif" />');
            $.get(eventlistLink, function (a) {
                $("#eventboxContent").html(a);
                toggleEvents.loaded = true
            })
        }
    } else {
        if (b) {
            return
        }
        $("#eventboxContent").slideUp("fast");
        $("#js_eventDetailsClosed").show();
        $("#js_eventDetailsOpen").hide()
    }
    $("#contentWrapper select").ogameDropDown("hide")
}
function changeSetting(l, k, h, f, g) {
    $.ajax({
        type: "POST",
        url: changeSettingsLink,
        dataType: "json",
        data: {token: changeSettingsToken, key: l, value: k},
        success: function (a) {
            changeSettingsToken = a.newToken;
            if (a.message.length > 0) {
                fadeBox(a.message, a.error)
            }
            if (!a.error && typeof(h) == "function") {
                h()
            } else {
                if (a.error && typeof(f) == "function") {
                    f()
                }
            }
        },
        error: function (a) {
            if (typeof(g) == "undefined" || g) {
                fadeBox(LocalizationStrings.error, true)
            }
            if (a.error && typeof(f) == "function") {
                f()
            }
        }
    })
}
function getOverlayText(b) {
    if (b.hasClass("building") && b.children().hasClass("build-faster-img")) {
        return locaPremium.buildingHalfOverlay
    } else {
        if (b.hasClass("building") && b.children().hasClass("build-finish-img")) {
            return locaPremium.buildingFullOverlay
        } else {
            if (b.hasClass("ships") && b.children().hasClass("build-faster-img")) {
                return locaPremium.shipsHalfOverlay
            } else {
                if (b.hasClass("ships") && b.children().hasClass("build-finish-img")) {
                    return locaPremium.shipsFullOverlay
                } else {
                    if (b.hasClass("research") && b.children().hasClass("build-faster-img")) {
                        return locaPremium.researchHalfOverlay
                    } else {
                        if (b.hasClass("research") && b.children().hasClass("build-finish-img")) {
                            return locaPremium.researchFullOverlay
                        }
                    }
                }
            }
        }
    }
}
function getFastBuildPrice(b) {
    if (b.hasClass("building")) {
        return priceBuilding
    } else {
        if (b.hasClass("research")) {
            return priceResearch
        } else {
            if (b.hasClass("ships")) {
                return priceShips
            }
        }
    }
}
function getRedirectLink(f) {
    var d = {};
    if (f != undefined) {
        for (var e in f) {
            d[e] = f[e]
        }
        return $.param.fragment($.param.querystring(window.location.href, d), {})
    } else {
        return window.location.href.split("#")[0]
    }
}
function sendShips(h, k, l, g, n, m) {
    if (shipsendingDone == 1) {
        shipsendingDone = 0;
        params = {
            mission: h,
            galaxy: k,
            system: l,
            position: g,
            type: n,
            shipCount: m,
            token: miniFleetToken
        };
        $.ajax(miniFleetLink, {
            data: params,
            dataType: "json",
            type: "POST",
            success: function (a) {
                if (typeof(a.newToken) != "undefined") {
                    miniFleetToken = a.newToken
                }
                displayMiniFleetMessage(a.response)
            }
        })
    }
}
function sendShipsWithPopup(h, k, l, g, n, m) {
    params = {
        mission: h,
        galaxy: k,
        system: l,
        position: g,
        type: n,
        shipCount: m,
        token: miniFleetToken
    };
    $.ajax(miniFleetLink, {
        data: params,
        dataType: "json",
        type: "POST",
        success: function (a) {
            if (typeof(a.newToken) != "undefined") {
                miniFleetToken = a.newToken
            }
            fadeBox(a.response.message + " " + a.response.coordinates.galaxy + ":" + a.response.coordinates.system + ":" + a.response.coordinates.position, !a.response.success)
        }
    })
}
function outlawWarning(q, u, s, p, n, l, m) {
    if (typeof(m) != "function") {
        if (q == constants.espionage) {
            m = r
        } else {
            if (q == constants.missleattack) {
                m = o
            }
        }
    }
    if (showOutlawWarning) {
        errorBoxDecision(LocalizationStrings.attention, LocalizationStrings.outlawWarning, LocalizationStrings.yes, LocalizationStrings.no, m)
    } else {
        m()
    }
    function r() {
        sendShips(q, u, s, p, n, l)
    }

    function o() {
        openOverlay(missleAttackLink + "&galaxy=" + u + "&system=" + s + "&position=" + p + "&planetType=" + n, {modal: true})
    }
}
function initThousandSeparator() {
    $(".checkThousandSeparator").unbind("keyup.checkThousandSeparator").bind("keyup.checkThousandSeparator", function (e) {
        if (e.which == 8 || e.which == 46) {
            var f = $(this).getSelection();
            var g = $(this).val();
            if (g.substr(f.start - 1, 1) == LocalizationStrings.thousandSeperator) {
                var h;
                if (e.which == 8) {
                    h = g.substr(0, f.start - 2) + g.substr(f.start - 1)
                } else {
                    h = g.substr(0, f.start) + g.substr(f.start + 1)
                }
                formatNumber($(this), h)
            }
        }
    })
}
function initIndex() {
    initConnectionErrorFunction();
    timerHandler.appendCallback(function () {
        localTime = new Date();
        serverTime = new Date(localTime.valueOf() + timeDiff);
        $(".OGameClock").html(getFormatedDate(serverTime.getTime(), "[d].[m].[Y] <span>[H]:[i]:[s]</span>"))
    });
    $("select").ogameDropDown();
    $("#planet .slot").hover(function () {
        $(this).addClass("slot-hover")
    }, function () {
        $(this).removeClass("slot-hover")
    });
    $("#eventboxFilled").hover(function () {
        $(this).addClass("eventboxHover")
    }, function () {
        $(this).removeClass("qeventboxHover")
    });
    $(document).undelegate(".eventToggle", "click").delegate(".eventToggle", "click", function () {
        toggleEvents();
        return false
    }).undelegate("a.build-faster", "click").delegate("a.build-faster", "click", function () {
        var d = $(this);
        if (darkMatter < getFastBuildPrice(d)) {
            errorBoxDecision(LocalizationStrings.error, loca.errorNotEnoughDM, LocalizationStrings.yes, LocalizationStrings.no, redirectPremium);
            return
        }
        var a = $.deparam.querystring().page;
        errorBoxDecision(loca.notice, getOverlayText(d), LocalizationStrings.yes, LocalizationStrings.no, function () {
            $.ajax({
                url: d.attr("rel"),
                data: {ajax: 1, token: activateToken, referrerPage: a},
                type: "POST",
                dataType: "json",
                error: function () {
                    fadeBox(LocalizationStrings.error, true);
                    d.addClass("disabled")
                },
                success: function (c) {
                    activateToken = c.newToken;
                    if (c.error) {
                        fadeBox(c.message, true);
                        d.addClass("disabled")
                    } else {
                        location.href = getRedirectLink()
                    }
                }
            });
            return false
        })
    }).undelegate(".slideIn", "click").delegate(".slideIn", "click", function () {
        $(".slideIn").removeClass("active");
        var a = $(this).attr("ref");
        $("a[ref='" + a + "']").addClass("active");
        Tipped.hideAll();
        $("html, body").animate({scrollTop: 0}, 500);
        gfSlider.slideIn(getElementByIdWithCache("detail"), a)
    });
    if ($("#eventboxContent").is(":visible")) {
        toggleEvents.loaded = true;
        $("#js_eventDetailsClosed").hide();
        $("#js_eventDetailsOpen").show()
    }
    $("#banner_skyscraper a.close_details").click(function () {
        changeSetting("hideBanner", $(this).attr("ref"), function () {
            $("#banner_skyscraper").remove()
        })
    });
    var b = $("#wreckFieldCountDown");
    if (b) {
    }
    initHideElements();
    initOverlays();
    initThousandSeparator();
    initExodus();
    initTooltips();
    initAjaxResourcebox();
    initPlanetSorting();
    initRetinaImages();
    initBDayEventHints()
}
function initPlanetSorting() {
    $("#planetList.sortable").sortable({
        start: function () {
            Tipped.hideAll()
        }, stop: function () {
            Tipped.hideAll();
            changeSetting("customPlanetOrder", $(this).sortable("toArray"))
        }
    });
    if ($(".lockPlanets").hasClass("closed")) {
        $("#planetList.sortable").sortable("disable")
    }
    $(".lockPlanets").unbind("click").bind("click", function () {
        var b = $(this);
        changeSetting("planetOrderLocked", b.hasClass("open") ? 1 : 0, function () {
            var a;
            if (b.hasClass("open")) {
                b.removeClass("open").addClass("closed");
                $("#planetList.sortable").sortable("disable");
                a = LocalizationStrings.planetOrder.unlock
            } else {
                b.removeClass("closed").addClass("open");
                $("#planetList.sortable").sortable("enable");
                a = LocalizationStrings.planetOrder.lock
            }
            changeTooltip(b, a)
        })
    })
}
function initHideElements() {
    $(document).undelegate("html", "touchstart.hideElem click.hideElem").delegate("html", "touchstart.hideElem click.hideElem", function (l) {
        l.stopPropagation();
        if ($(this).data("noclick")) {
            return
        }
        if (isMobile) {
            var h = l.target.tagName.toUpperCase();
            if (!(h === "TEXTAREA" || h === "INPUT" || h === "SELECT")) {
                document.activeElement.blur()
            }
            if (!$(l.target).parents(".markItUpHeader ul").length) {
                $(".markItUpHeader ul ul").hide()
            }
        }
        if (!$(l.target).parents(".t_Tooltip").length) {
            Tipped.hideAll()
        }
        if (!isMobile) {
            if ($(l.target).parents(".ui-dialog").length || $(l.target).parents(".t_Tooltip").length) {
                return
            }
            var k = $(".overlayDiv");
            if (typeof(k.data("uiDialog")) != "undefined") {
                var n = k.find(".markItUpDropMenu[id]");
                for (var e = 0; e < n.length; ++e) {
                    var m = $("body>ul[rel=" + n[e].id + "]");
                    m.hide()
                }
                k.dialog("close")
            }
        }
    })
}
function initJumpgate() {
    $("select").ogameDropDown();
    $(".list tr:even").addClass("alt");
    $(document).undelegate("#jumpgateForm .ship_input_row .textinput", "keyup change input").delegate("#jumpgateForm .ship_input_row .textinput", "keyup change input", function () {
        checkIntInput(this, 0, $(this).attr("rel"))
    }).undelegate("#jumpgateForm .ship_input_row .textinput", "focus").delegate("#jumpgateForm .ship_input_row .textinput", "focus", function () {
        $(this).val("")
    });
    $("#jumpgate .answerHeadline, .js_openStandardMoonMenu").click(function () {
        if (!player.hasCommander) {
            errorBoxNotify(LocalizationStrings.error, translation.changeSettingOnlyWithCommander, LocalizationStrings.ok, null, false)
        } else {
            $("#jumpgate").find(".answerHeadline").toggleClass("open");
            $(".thirdCol").toggleClass("hidden")
        }
    });
    $(".js_executeJumpButton").click(function () {
        var d = $("#jumpgateForm").find('select[name="zm"]').val();
        if (d != 0) {
            var c = true;
            $(".ship_selection_table input").each(function () {
                if ($(this).val() > 0) {
                    c = false
                }
            });
            if (!c) {
                ajaxFormSubmit("jumpgateForm", $(this).attr("data-url"), jumpgateDone)
            } else {
                fadeBox(translation.noShipsWereSelected, true)
            }
        } else {
            fadeBox(translation.validTargetNeeded, true)
        }
    })
}
function jumpgateDone(b) {
    var b = $.parseJSON(b);
    if (b.status) {
        planet = b.targetMoon;
        $(".overlayDiv").dialog("destroy")
    }
    errorBoxAsArray(b.errorbox);
    if (typeof(b.newToken) != "undefined") {
        setNewTokenData(b.newToken)
    }
}
function jumpgateDefaultTargetSelectionCallback(f) {
    var f = $.parseJSON(f);
    if (f.status) {
        var e = $("#jumpgateForm").find('select[name="zm"]');
        e.find("option").removeAttr("selected");
        var d = e.find('option[value="' + f.targetMoon + '"]');
        if (d.length) {
            d.attr("selected", "selected")
        } else {
            if (e.find('option[value="0"]').length == 0) {
                e.append($(document.createElement("option")).attr("value", 0).attr("selected", "selected").text("--"))
            } else {
                e.find('option[value="0"]').attr("selected", "selected")
            }
        }
        e.trigger("change");
        e.ogameDropDown("refresh")
    }
    errorBoxAsArray(f.errorbox);
    if (typeof(f.newToken) != "undefined") {
        setNewTokenData(f.newToken)
    }
}
function setNewTokenData(b) {
    $('#jumpgateForm input[name="token"]').val(b);
    $('#jumpgateDefaultTargetSelectionForm input[name="token"]').val(b)
}
if (typeof KeyEvent == "undefined") {
    var KeyEvent = {
        DOM_VK_CANCEL: 3,
        DOM_VK_HELP: 6,
        DOM_VK_BACK_SPACE: 8,
        DOM_VK_TAB: 9,
        DOM_VK_CLEAR: 12,
        DOM_VK_RETURN: 13,
        DOM_VK_ENTER: 14,
        DOM_VK_SHIFT: 16,
        DOM_VK_CONTROL: 17,
        DOM_VK_ALT: 18,
        DOM_VK_PAUSE: 19,
        DOM_VK_CAPS_LOCK: 20,
        DOM_VK_ESCAPE: 27,
        DOM_VK_SPACE: 32,
        DOM_VK_PAGE_UP: 33,
        DOM_VK_PAGE_DOWN: 34,
        DOM_VK_END: 35,
        DOM_VK_HOME: 36,
        DOM_VK_LEFT: 37,
        DOM_VK_UP: 38,
        DOM_VK_RIGHT: 39,
        DOM_VK_DOWN: 40,
        DOM_VK_PRINTSCREEN: 44,
        DOM_VK_INSERT: 45,
        DOM_VK_DELETE: 46,
        DOM_VK_0: 48,
        DOM_VK_1: 49,
        DOM_VK_2: 50,
        DOM_VK_3: 51,
        DOM_VK_4: 52,
        DOM_VK_5: 53,
        DOM_VK_6: 54,
        DOM_VK_7: 55,
        DOM_VK_8: 56,
        DOM_VK_9: 57,
        DOM_VK_SEMICOLON: 59,
        DOM_VK_EQUALS: 61,
        DOM_VK_A: 65,
        DOM_VK_B: 66,
        DOM_VK_C: 67,
        DOM_VK_D: 68,
        DOM_VK_E: 69,
        DOM_VK_F: 70,
        DOM_VK_G: 71,
        DOM_VK_H: 72,
        DOM_VK_I: 73,
        DOM_VK_J: 74,
        DOM_VK_K: 75,
        DOM_VK_L: 76,
        DOM_VK_M: 77,
        DOM_VK_N: 78,
        DOM_VK_O: 79,
        DOM_VK_P: 80,
        DOM_VK_Q: 81,
        DOM_VK_R: 82,
        DOM_VK_S: 83,
        DOM_VK_T: 84,
        DOM_VK_U: 85,
        DOM_VK_V: 86,
        DOM_VK_W: 87,
        DOM_VK_X: 88,
        DOM_VK_Y: 89,
        DOM_VK_Z: 90,
        DOM_VK_CONTEXT_MENU: 93,
        DOM_VK_NUMPAD0: 96,
        DOM_VK_NUMPAD1: 97,
        DOM_VK_NUMPAD2: 98,
        DOM_VK_NUMPAD3: 99,
        DOM_VK_NUMPAD4: 100,
        DOM_VK_NUMPAD5: 101,
        DOM_VK_NUMPAD6: 102,
        DOM_VK_NUMPAD7: 103,
        DOM_VK_NUMPAD8: 104,
        DOM_VK_NUMPAD9: 105,
        DOM_VK_MULTIPLY: 106,
        DOM_VK_ADD: 107,
        DOM_VK_SEPARATOR: 108,
        DOM_VK_SUBTRACT: 109,
        DOM_VK_DECIMAL: 110,
        DOM_VK_DIVIDE: 111,
        DOM_VK_F1: 112,
        DOM_VK_F2: 113,
        DOM_VK_F3: 114,
        DOM_VK_F4: 115,
        DOM_VK_F5: 116,
        DOM_VK_F6: 117,
        DOM_VK_F7: 118,
        DOM_VK_F8: 119,
        DOM_VK_F9: 120,
        DOM_VK_F10: 121,
        DOM_VK_F11: 122,
        DOM_VK_F12: 123,
        DOM_VK_F13: 124,
        DOM_VK_F14: 125,
        DOM_VK_F15: 126,
        DOM_VK_F16: 127,
        DOM_VK_F17: 128,
        DOM_VK_F18: 129,
        DOM_VK_F19: 130,
        DOM_VK_F20: 131,
        DOM_VK_F21: 132,
        DOM_VK_F22: 133,
        DOM_VK_F23: 134,
        DOM_VK_F24: 135,
        DOM_VK_NUM_LOCK: 144,
        DOM_VK_SCROLL_LOCK: 145,
        DOM_VK_COMMA: 188,
        DOM_VK_PERIOD: 190,
        DOM_VK_SLASH: 191,
        DOM_VK_BACK_QUOTE: 192,
        DOM_VK_OPEN_BRACKET: 219,
        DOM_VK_BACK_SLASH: 220,
        DOM_VK_CLOSE_BRACKET: 221,
        DOM_VK_QUOTE: 222,
        DOM_VK_META: 224
    }
}
(function (b) {
    b.fn.extend({
        ogameDropDown: function (l) {
            if (b("body.showOldDropdowns").length) {
                return this
            }
            function a(e) {
                var d = e.outerHeight();
                var g = b('.dropdown.currentlySelected[rel="' + e.attr("id") + '"]');
                if (g.length) {
                    var p = Math.min(b(window).innerWidth() + b(window).scrollLeft() - e.width() - 3, g.offset().left);
                    var c;
                    var f = Math.ceil(g.offset().top);
                    if (f + g.height() + d + b("#siteFooter").outerHeight() >= b(window).innerHeight() + b(window).scrollTop()) {
                        c = f - d + 1
                    } else {
                        c = f + g.height() + 1
                    }
                    e.css("left", p).css("top", c).css("min-width", g.width())
                }
            }

            var k = {
                destroy: function () {
                    b(this).filter("select.dropdownInitialized").each(function () {
                        var c = b(this);
                        b('.dropdown[rel="' + c.data("dropdownId") + '"]').remove();
                        b("ul#" + c.data("dropdownId")).remove();
                        c.removeClass("dropdownInitialized").data("dropdownId", "").show()
                    })
                }, hide: function () {
                    b(this).filter("select.dropdownInitialized").each(function () {
                        var c = b('.currentlySelected[rel="' + b(this).data("dropdownId") + '"]');
                        c.find("a").removeClass("hover");
                        b(".dropdownList#" + c.attr("rel")).hide()
                    })
                }, reposition: function () {
                    b(this).filter("select.dropdownInitialized").each(function () {
                        a(b("#" + b(this).data("dropdownId")))
                    })
                }, refresh: function () {
                    var d = b(this).find("option[selected]");
                    var e = getIEVersion() < 999 ? "#" : "javascript:void(0);";
                    var c = b('a[class="' + d.attr("class") + '"][rel="' + b(this).data("dropdownId") + '"]').text(d.text())
                }
            };
            if (typeof(l) == "string") {
                if (typeof(k[l]) == "function") {
                    k[l].call(this)
                }
                return this
            }
            b(this).filter("select:not(.dropdownInitialized)").each(function () {
                var r = b(this);
                var e = "dropdown" + Math.floor(Math.random() * 1000);
                var u = r.find("option[selected]");
                if (u.length == 0) {
                    u = r.find("option:first-child")
                }
                var d = getIEVersion() < 999 ? "#" : "javascript:void(0);";
                var g = b('<a class="' + u.attr("class") + '" data-value="' + u.val() + '" rel="' + e + '" href="' + d + '">' + u.text() + "</a>");
                var s = b('<span class="dropdown currentlySelected ' + r.attr("class") + '" rel="' + e + '"></span>').append(g).width(r.css("width").length ? r.css("width") : r.width()).data("selectElement", r);
                if (r.is("[readonly]") || r.is(":disabled")) {
                    s.addClass("disabled")
                }
                r.after(s).hide().addClass("dropdownInitialized").data("dropdownId", e);
                var c = b('<ul class="dropdown dropdownList" id="' + e + '"></ul>').delegate("a", "click", function (o) {
                    o.stopPropagation();
                    g.html(b(this).html()).attr("class", b(this).attr("class")).attr("data-value", b(this).attr("data-value"));
                    c.hide().find("a").removeClass("focus");
                    b(this).addClass("focus");
                    var p = b(this).attr("data-value");
                    r.val(b(this).attr("data-value")).trigger("change").find('option[value="' + p + '"], option:contains("' + p + '")').trigger("click")
                });

                function f() {
                    if (r.is(r.is("[readonly]") || ":disabled")) {
                        return
                    }
                    if (!c.hasClass("initialized")) {
                        c.addClass("initialized");
                        r.find("option").each(function () {
                            var o = b(this).html();
                            if (typeof(b(this).attr("data-html")) != "undefined") {
                                o = b(this).attr("data-html")
                            }
                            if (typeof(b(this).attr("data-html-prepend")) != "undefined") {
                                o = b(this).attr("data-html-prepend") + o
                            }
                            if (typeof(b(this).attr("data-html-append")) != "undefined") {
                                o += b(this).attr("data-html-append")
                            }
                            $li = b('<li><a class="' + b(this).attr("class") + '" data-value="' + b(this).val() + '">' + o + "</a></li>");
                            c.append($li);
                            if (b(this).is(":selected")) {
                                $li.find("a").addClass("focus")
                            }
                        })
                    }
                }

                g.bind("focus", function (o) {
                    b(".dropdownList").not(c).hide();
                    b(".dropdown.currentlySelected").removeClass("focus");
                    b(this).addClass("hover");
                    s.addClass("focus");
                    f()
                }).bind("mousewheel", function (o) {
                    f();
                    b(this).unbind("mousewheel")
                }).bind("click", function (o) {
                    o.preventDefault();
                    if (r.is(r.is("[readonly]") || ":disabled")) {
                        return
                    }
                    b(".dropdownList").not(c).hide();
                    b(".dropdown.currentlySelected").removeClass("focus");
                    s.addClass("focus");
                    if (c.is(":hidden")) {
                        b(this).addClass("hover");
                        f();
                        a(c);
                        b(window).unbind("resize.dropdown" + e).bind("resize.dropdown" + e, function () {
                            a(c)
                        });
                        c.show();
                        if (c.hasScrollbar()) {
                            c.find("a").css("padding-right", 22)
                        }
                    } else {
                        b(this).removeClass("hover");
                        b(window).unbind("resize.dropdown" + e);
                        c.hide()
                    }
                });
                b("body").append(c)
            });
            var m = "";
            var h;

            function n(g, q) {
                m = g;
                clearTimeout(h);
                h = setTimeout(function () {
                    m = ""
                }, 1500);
                var d = b(".dropdownList:visible");
                var r = false;
                if (d.length == 0) {
                    var e = b(".dropdown.currentlySelected.focus");
                    if (e.length) {
                        d = b("#" + e.attr("rel"));
                        r = true
                    } else {
                        return
                    }
                }
                var c = m.toLowerCase();
                var f = d.find("a").filter(function () {
                    if (b(this).attr("data-value").toLowerCase().indexOf(c) == 0) {
                        return true
                    }
                    return b(this).text().trim().toLowerCase().indexOf(c) == 0
                });
                if (f.length) {
                    q.preventDefault();
                    d.find("a").removeClass("focus");
                    b(f.get(0)).addClass("focus").focus();
                    if (r) {
                        f.click()
                    }
                } else {
                    clearTimeout(h);
                    m = ""
                }
            }

            b(document).undelegate("html", "touchstart.dropdown click.dropdown").delegate("html", "touchstart.dropdown click.dropdown", function (c) {
                if (b(c.target).closest(".dropdown").length == 0) {
                    b(".dropdownList").hide();
                    b(".currentlySelected a").removeClass("hover");
                    b(".currentlySelected").removeClass("focus")
                }
            }).undelegate(".dropdown", "mousewheel.dropdown").delegate(".dropdown", "mousewheel.dropdown", function (e, B) {
                e.preventDefault();
                var x = b(e.target).closest(".dropdown");
                var f;
                if (x.hasClass("currentlySelected")) {
                    f = b("#" + x.attr("rel"))
                } else {
                    f = x
                }
                var d = b('[rel="' + f.attr("id") + '"] a');
                var y = f.find("a:focus");
                if (y.length == 0) {
                    y = f.find("a.focus")
                }
                if (y.length == 0) {
                    y = f.find('a[data-value="' + d.attr("data-value") + '"]')
                }
                var v = y.parent();
                var A = null;
                var g = Math.abs(B);
                if (B > 0) {
                    for (var c = 0; c < g; c++) {
                        if (v.is(":first-child")) {
                            A = v.find("a");
                            break
                        } else {
                            A = v.prev().find("a")
                        }
                        v = A.parent()
                    }
                } else {
                    for (var c = 0; c < g; c++) {
                        if (v.is(":last-child")) {
                            A = v.find("a");
                            break
                        } else {
                            A = v.next().find("a")
                        }
                        v = A.parent()
                    }
                }
                if (A != null) {
                    v = A.parent();
                    f.find("a").removeClass("focus");
                    A.addClass("focus").focus();
                    if (f.is(":hidden")) {
                        A.click()
                    } else {
                        var w = v.position().top;
                        if (w < 0) {
                            f.scrollTop(f.scrollTop() + w)
                        } else {
                            if (w + v.outerHeight() > f.innerHeight()) {
                                f.scrollTop(f.scrollTop() + w + v.outerHeight() - f.innerHeight())
                            }
                        }
                    }
                }
            }).undelegate("*", "focus.dropdown").delegate("*", "focus.dropdown", function (c) {
                if (b(c.target).closest(".dropdown").length == 0) {
                    b(".currentlySelected a").removeClass("hover");
                    b(".currentlySelected").removeClass("focus")
                }
            }).unbind("keydown.dropdown").bind("keydown.dropdown ", function (f) {
                if (b(":focus").length > 0 && b(":focus").parents(".dropdown").length == 0) {
                    return
                }
                var e = false;
                var s = b(".dropdownList:visible");
                if (s.length == 0) {
                    var g = b(".dropdown.currentlySelected.focus");
                    if (g.length) {
                        s = b("#" + g.attr("rel"));
                        e = true
                    } else {
                        return
                    }
                }
                var d = b('[rel="' + s.attr("id") + '"] a');
                var w = s.find("a:focus");
                if (w.length == 0) {
                    w = s.find("a.focus")
                }
                if (w.length == 0) {
                    w = s.find('a[data-value="' + d.attr("data-value") + '"]')
                }
                var u = w.parent();
                var c = null;
                if (f.keyCode == KeyEvent.DOM_VK_UP || f.keyCode == KeyEvent.DOM_VK_DOWN || typeof(delta) != "undefined") {
                    if (f.keyCode == KeyEvent.DOM_VK_UP) {
                        if (u.is(":first-child")) {
                            c = u.find("a")
                        } else {
                            c = u.prev().find("a")
                        }
                        u = c.parent()
                    } else {
                        if (u.is(":last-child")) {
                            c = u.find("a")
                        } else {
                            c = u.next().find("a")
                        }
                        u = c.parent()
                    }
                    f.preventDefault()
                } else {
                    if (f.keyCode == KeyEvent.DOM_VK_BACK_SPACE) {
                        n(m.substring(0, m.length - 1), f)
                    } else {
                        if (f.keyCode == KeyEvent.DOM_VK_RETURN) {
                            f.preventDefault();
                            if (e) {
                                d.parents("form").submit();
                                return
                            } else {
                                e = true;
                                c = w
                            }
                        } else {
                            if (f.keyCode == KeyEvent.DOM_VK_ESCAPE) {
                                c = s.find('[data-value="' + d.attr("data-value") + '"]');
                                e = true
                            }
                        }
                    }
                }
                if (c != null) {
                    u = c.parent();
                    s.find("a").removeClass("focus");
                    c.addClass("focus").focus();
                    if (e) {
                        c.click()
                    } else {
                        var v = u.position().top;
                        if (v < 0) {
                            s.scrollTop(s.scrollTop() + v)
                        } else {
                            if (v + u.outerHeight() > s.innerHeight()) {
                                s.scrollTop(s.scrollTop() + v + u.outerHeight() - s.innerHeight())
                            }
                        }
                    }
                }
            }).unbind("keypress.dropdown").bind("keypress.dropdown", function (c) {
                if (b(":focus").length > 0 && b(":focus").parents(".dropdown").length == 0 || c.charCode == 0) {
                    return
                }
                n(m + String.fromCharCode(c.charCode), c)
            });
            return b(this)
        }, selectText: function () {
            var a, k, g = this[0], l = {
                func: "function",
                obj: "object"
            }, h = function (d, c) {
                return typeof c === d
            };
            if (h(l.obj, g.ownerDocument) && h(l.obj, g.ownerDocument.defaultView) && h(l.func, g.ownerDocument.defaultView.getSelection)) {
                k = g.ownerDocument.defaultView.getSelection();
                if (h(l.func, k.setBaseAndExtent)) {
                    k.setBaseAndExtent(g, 0, g, b(g).contents().size())
                } else {
                    if (h(l.func, g.ownerDocument.createRange)) {
                        a = g.ownerDocument.createRange();
                        if (h(l.func, a.selectNodeContents) && h(l.func, k.removeAllRanges) && h(l.func, k.addRange)) {
                            a.selectNodeContents(g);
                            k.removeAllRanges();
                            k.addRange(a)
                        }
                    }
                }
            } else {
                if (h(l.obj, document.body) && h(l.obj, document.body.createTextRange)) {
                    a = document.body.createTextRange();
                    if (h(l.obj, a.moveToElementText) && h(l.obj, a.select)) {
                        a.moveToElementText(g);
                        a.select()
                    }
                }
            }
            return this
        }, hasScrollbar: function () {
            return this.get(0).scrollHeight > this.innerHeight()
        }
    })
})(jQuery);

function preCloseMovements() {
    $.each(currentMovementTabExtensionStates, function (g, h) {
        if (h[0] == 0) {
            var e = $("#fleet" + g + " span.openDetails a");
            var f = e.attr("data-end-time");
            closeDetails(g, f)
        }
    })
}
function initMovement() {
    initToggleHeader("movement");
    preCloseMovements();
    if (b == undefined) {
        var b = 0
    }
    $("a.openCloseDetails").click(function () {
        openCloseDetails($(this).attr("data-mission-id"), $(this).attr("data-end-time"))
    });
    $(".closeAll").click(function () {
        if (b == 0) {
            b = 1;
            $(".closeAll").children().removeClass("all_open").addClass("all_closed")
        } else {
            b = 0;
            $(".closeAll").children().removeClass("all_closed").addClass("all_open")
        }
        $("a.openCloseDetails").each(function () {
            if (b === 1) {
                closeDetails($(this).attr("data-mission-id"), $(this).attr("data-end-time"))
            } else {
                if (b === 0) {
                    openDetails($(this).attr("data-mission-id"), $(this).attr("data-end-time"))
                }
            }
        })
    });
    timerHandler.appendCallback(function () {
    })
}
function addUserToUnion() {
    $("#participantselect").append($("#buddyselect").find("li.ui-selected"))
}
function removeUserFromUnion() {
    $("#buddyselect").append($("#participantselect").find("li.ui-selected"))
}
function addUserToUnionByForm() {
    var d = $("#unionUserSearch").find('[name="addtogroup"]');
    var f = d.val();
    var e = $("#participantselect");
    if (e.find('li[ref="' + f + '"]').length == 0) {
        e.append($(document.createElement("li")).attr("ref", f).text(f))
    }
    d.val("")
}
function setUnionUsers() {
    var b = "";
    $("#participantselect").find("li").each(function () {
        b += $(this).attr("ref") + ";"
    });
    b = b.substring(0, b.length - 1);
    $("#unionUsers").val(b)
}
function unionUser(d) {
    var c = $.parseJSON(d);
    if (c.status) {
        addUserToUnionByForm()
    } else {
        errorBoxAsArray(c.errorbox)
    }
}
function initFederationLayer() {
    $("#switch").click(function () {
        var b = $("#searchFed");
        b.find("> .wrap").toggle();
        b.find("> #honorWarning").toggle()
    });
    $("#buddyselect, #participantselect").selectable({filter: "li:not(.undermark)"});
    $(document).undelegate("ul#buddyselect li", "dblclick").delegate("ul#buddyselect li", "dblclick", function () {
        addUserToUnion()
    }).undelegate("ul#participantselect li", "dblclick").delegate("ul#participantselect li", "dblclick", function () {
        removeUserFromUnion()
    })
}
function submit_unionform() {
    setUnionUsers();
    ajaxFormSubmit("unionform", $("form#unionform").attr("action"), unionEdit)
}
function recallShipCountdown(f, h) {
    var g = this;
    var e = $(".reversal_time[ref='" + f + "']");
    if (isMobile && e.length) {
        this.updateCountdown = function () {
            var b = g.countdown.getLeftoverTime();
            var a = getFormatedDate(new Date(b * 1000 + timeDiff), "[d].[m].[Y] [H]:[i]:[s]");
            e.html(a)
        };
        g.countdown = new countdown(h, 3, 2);
        g.timer = timerHandler.appendCallback(g.updateCountdown);
        g.updateCountdown()
    }
}
function initNotesForm() {
    $("select").ogameDropDown();
    $("#createNote .text").trigger("keyup");
    if ($("#popupContent").length) {
        initNotes()
    }
}
function initNotes() {
    $("select").ogameDropDown();
    function c(a) {
        var b = false;
        $(a).find(":input").each(function () {
            if (typeof($(this).data("value")) != "undefined") {
                if ($(this).data("value") != $(this).val()) {
                    b = true
                }
            }
        });
        return b
    }

    var d = $(".overlayDiv.notices");
    d.find(".openOverlay").unbind("click").bind("click", function () {
        var b = $(this).attr("data-overlay-class");
        var f = {
            title: $(this).attr("data-title"), close: function () {
                var e = $("." + b);
                if (c(e.find("form"))) {
                    errorBoxDecision(LocalizationStrings.question, locaNotes.changesNotSaved + "<br/><br/>" + locaNotes.questionSaveChanges, LocalizationStrings.yes, LocalizationStrings.no, function () {
                        e.find("form").trigger("submit");
                        e.remove()
                    }, function () {
                        e.remove()
                    }, true)
                } else {
                    e.remove()
                }
            }, "class": b
        };
        openOverlay($(this).attr("href"), f);
        if (b.indexOf("newNote-") === 0) {
            var a = parseInt(b.replace(/^newNote-/, "")) + 1;
            $(this).attr("data-overlay-class", "newNote-" + a)
        }
        return false
    });
    $(document).undelegate("#noteList form", "submit").delegate("#noteList form", "submit", function () {
        console.log('noteList submit()');
        $.post($("#noteList").attr("rel"), $(this).serialize(), function (a) {
            d.html(a)
        })
    }).undelegate("#createNote form [type=submit]", "click").delegate("#createNote form [type=submit]", "click", function (a) {
        a.preventDefault();
        $(this).parents("form").submit()
    }).undelegate("#createNote form", "submit").delegate("#createNote form", "submit", function (a) {
        a.preventDefault();
        var b = $(this);
        $.ajax({
            url: $(this).attr("rel"),
            type: "post",
            data: b.serialize(),
            dataType: "json",
            error: function () {
                fadeBox(LocalizationStrings.error, true)
            },
            success: function (f) {
                if (f.error != null) {
                    fadeBox(f.error, true)
                } else {
                    if ($("#popupContent").length) {
                        $(window).unbind("beforeunload.checkChanges");
                        location.href = b.attr("rel") + "&popup=1"
                    } else {
                        if (b.parents(".overlayDiv").is(":visible")) {
                            b.parents(".overlayDiv").dialog("option", "close", function () {
                                $(this).remove()
                            }).dialog("close")
                        }
                        if (f.success != null) {
                            fadeBox(f.success, false)
                        }
                        d.load(d.find("#noteList").attr("rel"))
                    }
                }
            }
        });
        return false
    }).undelegate("#createNote .textBox", "keyup touchstart change").delegate("#createNote .textBox", "keyup touchstart change", function () {
        var a = $(this).val().length;
        var f = $(this).attr("data-max-length");
        if (a > f) {
            var b = $(this).getSelection();
            $(this).val($(this).val().substr(0, f));
            a = f;
            $(this).setSelection(b)
        }
        $(this).parents("form").find(".cntChars").text(a)
    });
    $(window).unbind("beforeunload.checkChanges").bind("beforeunload.checkChanges", function () {
        var a = false;
        $("#createNote form").each(function () {
            if (c(this)) {
                a = true
            }
        });
        if (a) {
            return locaNotes.changesNotSaved
        }
    })
}
function scrollToTopOfDialog(b) {
    $("html, body").stop().animate({scrollTop: Math.max(0, b.offset().top - 300)}, 200)
}
function openOverlay(N, G) {
    console.log('openOverlay start');
    if (typeof(openOverlay.index) == "undefined") {
        openOverlay.index = 0
    } else {
        openOverlay.index++
    }
    var T = openOverlay.index;
    var G = G || {};
    if ((typeof(G.type) == "undefined" || G.type != "inline") && !N.match(new RegExp("^(" + ogameUrl + "|" + startpageUrl + ")"))) {
        console.log('overlay a href does not match regex? opening new tab');
        window.open("redir.php?url=" + encodeURIComponent(N), "_newtab");
        return
    }
    if (typeof(G.height) == "undefined") {
        G.height = "auto"
    }
    if (typeof(G.width) == "undefined") {
        G.width = "auto"
    }
    if (typeof(G.position) == "undefined") {
        if (isMobile && !isMobileApp) {
            G.position = {my: "top", at: "top"}
        } else {
            G.position = {my: "center", at: "center"}
        }
    }
    G.closeText = "";
    if ($(".overlayDiv").length && !isMobile) {
        var K = $(".overlayDiv:last");
        var M = K.offset();
        G.position = {
            my: "left top",
            at: "left+" + (M.left + 10) + " top+" + (M.top + 10)
        }
    }
    function B(a) {
        var b = a.parent(".ui-dialog");
        if (b.length) {
            b.css("top", Math.max(0, parseInt(b.css("top").replace(/px$/, "")))).css("left", Math.max(0, parseInt(b.css("left").replace(/px$/, ""))))
        }
    }

    var Q = G.type;
    delete G.type;
    if (Q != "inline") {
        var P = $(document.createElement("img")).attr("src", "/img/icons/4161a64a933a5345d00cb9fdaa25c7.gif").attr("alt", LocalizationStrings.loading);
        var L = $(document.createElement("div")).css("text-align", "center").css("margin-top", "20px").append(P);
        var C = $(document.createElement("div")).addClass("overlayDiv").css("display", "none").append(L).appendTo("body");
        var H = function () {
            C.find("select").ogameDropDown("destroy");
            C.remove();
            Tipped.hideAll()
        };
        switch (typeof(G.close)) {
            case"function":
                break;
            case"string":
                var A = G.close.split(" ");
                G.close = function () {
                    $.each(A, function (a, b) {
                        if (b == "__default") {
                            H()
                        } else {
                            window[b]()
                        }
                    })
                };
                break;
            default:
                G.close = H;
                break
        }
    }
    if (typeof(N) == "string") {
        var R = $.deparam($.param.querystring(N));
        if (typeof(R.page) != "undefined") {
            C.attr("data-page", R.page)
        }
        if (!isMobile && $.inArray(R.page, popupWindows) != -1) {
            var J = Math.max(0, Math.floor($(window).height() / 2 - G.popupHeight / 2));
            var O = Math.max(0, Math.floor($(window).width() / 2 - G.popupWidth / 2));
            var U = window.open(N + "&popup=1", R.page, "width=" + G.popupWidth + ",height=" + G.popupHeight + ",scrollbars=yes,resizable=yes,top=" + J + ",left=" + O);
            C.remove();
            U.focus();
            return
        }
    }
    if (typeof(G["class"]) != "undefined") {
        var S = G["class"].split(" ").join(".");
        if ($(".overlayDiv." + S).length) {
            $.get(N, {}, function (a) {
                $(".overlayDiv." + G["class"]).empty().append(a).dialog("moveToTop")
            });
            C.remove();
            C = $(".overlayDiv." + S);
            if (typeof(R.page) != "undefined") {
                C.attr("data-page", R.page)
            }
            if (typeof(G.title) != "undefined") {
                C.dialog("option", "title", G.title)
            }
            scrollToTopOfDialog(C);
            return true
        } else {
            C.addClass(G["class"])
        }
    }
    if (Q == "inline") {
        C = $(N)
    }
    if (isNaN(G.dragStart) && isNaN(G.dragStop)) {
        var D;
        G.dragStart = function () {
            $("html").data("noclick", true);
            C.dialog("option", "width", C.width()).dialog("option", "height", C.height());
            D = {
                bg: C.css("background"),
                image: C.css("background-image"),
                x: C.css("background-position-x"),
                y: C.css("background-position-y"),
                position: C.css("background-position")
            };
            C.find("select").ogameDropDown("hide");
            C.children().hide();
            var d = C.find(".markItUpDropMenu[id]");
            for (var e = 0; e < d.length; ++e) {
                var a = $("body>ul[rel=" + d[e].id + "]");
                var c = (typeof a.attr("old_left") == "undefined" ? -18 : 0);
                var b = (typeof a.attr("old_left") == "undefined" ? -6 : 0);
                a.attr("old_top", C.offset()["top"] + c).attr("old_left", C.offset()["left"] + b).hide()
            }
            C.css("background", "#000000")
        };
        G.dragStop = function () {
            setTimeout(function () {
                $("html").data("noclick", false)
            }, 100);
            if (typeof(C.bg) == "undefined" || C.bg.length == 0) {
                C.css("background-image", D.image);
                if (typeof(D.position) == "undefined" || D.position.length == 0) {
                    C.css("background-position-x", D.x).css("background-position-y", D.y)
                } else {
                    C.css("background-position", D.position)
                }
            } else {
                C.css("background", D.bg)
            }
            C.children().show();
            var c = C.find(".markItUpDropMenu[id]");
            for (var a = 0; a < c.length; ++a) {
                var b = $("body>ul[rel=" + c[a].id + "]");
                b.css({
                    top: (parseInt(b.css("top")) - b.attr("old_top") + C.offset()["top"]) + "px",
                    left: (parseInt(b.css("left")) - b.attr("old_left") + C.offset()["left"]) + "px"
                });
                if ($(c[a]).attr("data-opened") == 1) {
                    b.show()
                }
            }
            C.dialog("option", "width", G.width).dialog("option", "height", G.height);
            B(C)
        }
    }
    if (isNaN(G.resizable)) {
        G.resizable = false
    }
    if (isMobile) {
        G.draggable = false
    }
    if (G.modal) {
        G.open = function () {
            $(".ui-widget-overlay").css("height", "").css("width", "")
        }
    }
    switch (Q) {
        case"iframe":
            var E = overlayWidth;
            var I = overlayHeight;
            if (typeof(G.iframeWidth) != "undefined") {
                E = G.iframeWidth;
                delete G.iframeWidth
            }
            if (typeof(G.iframeHeight) != "undefined") {
                I = G.iframeHeight;
                delete G.iframeHeight
            }
            C.html("<iframe allowTransparency='true'frameborder='0' hspace='0' src='" + N + "' id='TB_iframeContent' name='TB_iframeContent" + Math.round(Math.random() * 1000) + "' style='width:" + (E + 25) + "px;height:" + (I + 1) + "px;' ></iframe>").dialog(G).dialog("moveToTop");
            B(C);
            break;
        case"inline":
            var F = $(N);
            var V = F.parent();
            F.addClass("overlayDiv").dialog(G).dialog("moveToTop");
            B(F);
            break;
        default:
            C.dialog(G).dialog("moveToTop");
            $.get(N, {}).done(function (a) {
                C.empty().append(a).dialog("option", "position", C.dialog("option", "position"));
                setTimeout(function () {
                    C.dialog("option", "position", C.dialog("option", "position"));
                    B(C)
                }, 100);
                $(document).trigger("ajaxShowOverlay")
            }).fail(function () {
            })
    }
    Tipped.hideAll();
    $("select").ogameDropDown("hide");
    if (!isMobile) {
        $(window).bind("resize.overlay" + T, function () {
            if (C.is(":data(dialog)")) {
                C.dialog("option", "position", C.dialog("option", "position"));
                B(C)
            } else {
                $(window).unbind("resize.overlay" + T)
            }
        })
    }

    console.log('openOverlay finish');
}
function isOverlayOpen() {
    return $(".overlayDiv").length > 0
}
function initOverlays() {
    $(document).undelegate('a[href*="overlay=1"], button[data-target*="overlay=1"], a.overlay, button.overlay', "click").delegate('a[href*="overlay=1"], button[data-target*="overlay=1"], a.overlay, button.overlay', "click", function (k) {
        k.preventDefault();
        var e = $(this).attr("href") || $(this).attr("data-target");
        if ($(this).data("overlay-same")) {
            var h = $(this).parents(".ui-dialog");
            var l = h.find(".overlayDiv");
            if ($(this).data("overlay-same") && l.length > 0) {
                $.get(e, {}, function (a) {
                    removeTooltip(l.find(getTooltipSelector()));
                    l.empty().append(a).dialog("moveToTop");
                    l.dialog("option", "position", l.dialog("option", "position"));
                    h.hide();
                    h.show()
                });
                return false
            }
        }
        var g = {zIndex: 4000};
        if ($(this).data("overlay-title")) {
            g.title = $(this).data("overlay-title")
        } else {
            if (typeof($(this).attr("title")) != "undefined" && $(this).attr("title").length) {
                g.title = $(this).attr("title")
            } else {
                if ($(this).data("tipped_restore_title")) {
                    g.title = $(this).data("tipped_restore_title").replace(/^.+\|/, "")
                }
            }
        }
        if ($(this).data("overlay-class")) {
            g["class"] = $(this).data("overlay-class")
        }
        if ($(this).data("overlay-width")) {
            g.width = $(this).data("overlay-width")
        }
        if ($(this).data("overlay-height")) {
            g.height = $(this).data("overlay-height")
        }
        if ($(this).data("overlay-popup-width")) {
            g.popupWidth = $(this).data("overlay-popup-width")
        }
        if ($(this).data("overlay-popup-height")) {
            g.popupHeight = $(this).data("overlay-popup-height")
        }
        if ($(this).data("overlay-modal")) {
            g.modal = $(this).data("overlay-modal");
            g.resizable = false;
            g.draggable = false
        }
        if ($(this).data("overlay-iframe")) {
            g.type = "iframe";
            if ($(this).data("iframe-width")) {
                g.iframeWidth = $(this).data("iframe-width")
            }
            if ($(this).data("iframe-height")) {
                g.iframeHeight = $(this).data("iframe-height")
            }
        } else {
            if ($(this).data("overlay-inline")) {
                g.type = "inline";
                e = $(this).data("overlay-inline")
            }
        }
        if ($(this).data("overlay-close")) {
            g.close = $(this).data("overlay-close")
        }
        console.log('openOverlay called?')
        openOverlay(e, g);
        return false
    })
}
function initOverview() {
    $(".cancelMove").click(function () {
        var c = $(this);
        var d = window.location;
        errorBoxDecision(planetMoveLoca.askTitle, planetMoveLoca.askCancel, planetMoveLoca.yes, planetMoveLoca.no, function () {
            $.ajax({
                method: "get",
                url: c.attr("rel"),
                dataType: "json",
                cache: false,
                success: function (a) {
                    if (a.error.length > 0) {
                        fadeBox(a.error, true)
                    } else {
                        location.href = getRedirectLink()
                    }
                },
                error: function () {
                    fadeBox(planetMoveLoca.error, true)
                }
            })
        })
    });
    $(document).undelegate("#planetMaintenanceDelete", "submit").delegate("#planetMaintenanceDelete", "submit", function (b) {
        b.preventDefault();
        ajaxFormSubmit("planetMaintenanceDelete", $(this).attr("action"), planetGivenup)
    }).undelegate("#abandonplanet #block", "click").delegate("#abandonplanet #block", "click", function (c) {
        c.preventDefault();
        if (isMobileOnly || isFacebookUser) {
            var d = $("#giveupHeadline").attr("rel") == 3 ? loca.moonGiveupQuestion : loca.planetGiveupQuestion;
            d = d.replace("%planetName%", $("#giveupName").text()).replace("%planetCoordinates%", $("#giveupCoordinates").text());
            errorBoxDecision($("#giveupHeadline").text(), d, LocalizationStrings.yes, LocalizationStrings.no, function () {
                $("#planetMaintenanceDelete").submit()
            })
        } else {
            show_hide_menus("#validate");
            show_hide_menus("#giveUpNotification")
        }
    }).undelegate(".openPlanetRenameGiveupBox", "click").delegate(".openPlanetRenameGiveupBox", "click", function (b) {
        b.stopPropagation();
        openPlanetRenameGiveupBox()
    })
}
function clearField() {
    currentValue = $("#planetName").val();
    if (defaultName == currentValue) {
        clearInput("#planetName")
    }
}
function fillField() {
    currentValue = $("#planetName").val();
    if (currentValue == "") {
        $("#planetName").val(defaultName)
    }
}
function openBuddyOverlay(d, c) {
    openOverlay(d, {"class": "buddies", title: c})
}
function planetGivenup(b) {
    var b = $.parseJSON(b);
    errorBoxAsArray(b.errorbox);
    if (typeof(b.newToken) == "string") {
        $("#planetMaintenanceDelete input[name='token']").val(b.newToken)
    }
    if (typeof(b.password_checked) != "undefined" && b.password_checked) {
        $("#planetMaintenanceDelete").attr("action", b.intend)
    }
}
function initPreferences() {
    $(".category").click(function () {
        document.prefs.reset()
    });
    $("div.wrap > div.group").hide();
    $("div.wrap > div.group:first,div.wrap:eq(1) > div.group:eq(0),div.wrap:eq(2) > div.group:eq(0),div.wrap:eq(3) > div.group:eq(0)").show();
    $("div.wrap > div.bar").click(function () {
        $(this).next("div.group:hidden").slideDown("fast", function () {
            Tipped.show($(":input:visible"))
        }).siblings("div.group:visible").slideUp("fast", function () {
            Tipped.hide($(":input:not(:visible)"))
        });
        $(".formError").validationEngine("closePrompt")
    });
    $(".content .bar").hover(function () {
        $(this).addClass("bar-hover")
    }, function () {
        $(this).removeClass("bar-hover")
    });
    $("#newpass1").bind("keyup", function () {
        var u = $(this).val();
        var E = u.length;
        var w = u.match(/[^A-Za-z\d]/);
        var x = u.match(/\d/);
        var s = u.match(/[a-z]/) && u.match(/[A-Z]/);
        var D = 0;
        var q = 4;
        var v = {
            length: false,
            "mixed-case": false,
            "special-chars": false,
            numbers: false
        };
        if (E >= passwordMinLength && E <= passwordMaxLength) {
            v.length = true;
            D++
        }
        if (s) {
            v["mixed-case"] = true;
            D++
        }
        if (x) {
            v.numbers = true;
            D++
        }
        if (w) {
            v["special-chars"] = true;
            D++
        }
        for (var F in v) {
            var y = v[F];
            var A = $("#password-meter-status-" + F);
            A.find("img.status-checked").css("visibility", y ? "visible" : "hidden")
        }
        var C = Math.floor(D / q * 2);
        var r = new Array("low", "medium", "high");
        for (var B in r) {
            if (B != C) {
                $("#password-meter-rating-" + r[B]).removeClass("arrow")
            } else {
                $("#password-meter-rating-" + r[B]).addClass("arrow")
            }
        }
    });
    $(".contentzs").tabs({
        beforeActivate: function (d, c) {
            $("input#selectedTab").val($(c.tab).parent().prevAll().length)
        }, activate: function () {
            Tipped.hide($("input:not(:visible)"));
            Tipped.show($("input:visible"))
        }, active: selectedTab
    });
    $("#sortSetting").unbind("change").bind("change", function () {
        var d = "settings_order";
        if ($(this).val() == customSorting) {
            var e = $("#sortOrder");
            e.attr("disabled", "disabled").attr("name", "");
            $("#sortOrderHidden").attr("name", d).val(e.val());
            $("#sortOrder").next(".dropdown").addClass("disabled")
        } else {
            $("#sortOrder").next(".dropdown").removeClass("disabled");
            var f = $("#sortOrderHidden");
            f.attr("name", "");
            $("#sortOrder").removeClass("disabled").attr("name", d).removeAttr("disabled").val(f.val())
        }
    }).trigger("change");
    if (moveInProgress) {
        $("form#prefs").on("submit", function (c) {
            var d = $(this);
            if (d.find("input#urlaubs_modus.notOnVacation:checked").length) {
                errorBoxDecision(LocalizationStrings.attention, preferenceLoca.planetMoveQuestion, LocalizationStrings.yes, LocalizationStrings.no, function () {
                    d.off("submit").submit()
                });
                c.preventDefault();
                return false
            }
        })
    }
    if (isMobileOnly) {
        $("#prefs").bind("submit", function () {
            var b = $(this);
            if (!b.data("asking") && $("#db_character", b).val().length) {
                b.data("asking", true);
                errorBoxDecision(preferenceLoca.changeNameTitle, preferenceLoca.changeNameQuestion.replace("%newName%", $("#db_character", b).val()), LocalizationStrings.yes, LocalizationStrings.no, function () {
                    b.submit();
                    b.data("asking", false)
                }, function () {
                    b.data("asking", false)
                });
                return false
            }
        })
    }
    if (tabsDisabled) {
        $(".contentzs").tabs("option", "disabled", [1, 2]);
        $("#tabGeneral, #tabRepresentation").attr("title", preferenceLoca.tabDisabled).attr("class", "tooltip")
    }
    $("div.wrap:visible > div.bar:eq(" + openGroup + ")").click()
}
function display_info(b) {
    if (document.getElementById("infoInput").innerHTML == "" || document.getElementById("infoInput").innerHTML != get_displayText(b)) {
        document.getElementById("infoInput").innerHTML = get_displayText(b)
    }
}
function display_error(b) {
    if (document.getElementById("errorInput").innerHTML == "" || document.getElementById("errorInput").innerHTML != get_errorText(b)) {
        document.getElementById("errorInput").innerHTML = get_errorText(b);
        document.getElementById("error").style.display = "block"
    }
}
function hide_error(b) {
    document.getElementById("errorInput").innerHTML = "";
    document.getElementById("error").style.display = "none"
}
function checkUsername() {
    var b = document.forms["new"].elements.username.value;
    if (b.length < 3 || b.length >= 20) {
        display_error("username")
    } else {
        hide_error()
    }
}
function checkEmail() {
    var b = document.forms["new"].elements.email.value;
    validate = b.match(/[a-zA-Z0-9]+@+[a-zA-Z0-9]+[.]+[a-zA-Z0-9]{2,4}/);
    if (b.length < 3 || b.length >= 64 || !validate) {
        display_error("email")
    } else {
        hide_error()
    }
}
var TECHID_REPAIR_DOCK = 36;
var burnUpCountDownForStationScreen = {};
var repairTimeDownForStationScreen = {};
function displayBurnUpEnquiry() {
    var b = $("input.burnUpButton");
    errorBoxDecision(b.data("loca_box_text"), b.data("loca_decision_text"), b.data("loca_yes"), b.data("loca_no"), function () {
        $.ajax({
            url: b.data("url"), success: function (a) {
                var d = jQuery.parseJSON(a);
                if (d.success) {
                    fadeBox(d.reason, false, function () {
                    });
                    redirectSpaceDock()
                } else {
                    fadeBox(d.reason, true, function () {
                    })
                }
            }
        })
    }, function () {
    })
}
function startWreckFieldRepairs() {
    var b = $("input.startRepairsButton");
    $.ajax({
        url: b.data("url"), success: function (a) {
            var d = jQuery.parseJSON(a);
            if (d.success) {
                fadeBox(d.reason, false, function () {
                });
                redirectSpaceDock()
            } else {
                fadeBox(d.reason, true, function () {
                })
            }
        }
    })
}
function reCommissionShips() {
    var b = $("input.reCommissionButton");
    $.ajax({
        url: b.data("url"), success: function (a) {
            var d = jQuery.parseJSON(a);
            if (d.success) {
                fadeBox(d.reason, false, function () {
                });
                redirectSpaceDock()
            } else {
                fadeBox(d.reason, true, function () {
                })
            }
        }
    })
}
function registerBurnUpCountDown(e) {
    var f = $(e);
    var d = $(e).data("duration");
    if (d > 0) {
        if (!burnUpCountDownForStationScreen[e]) {
            burnUpCountDownForStationScreen[e] = new simpleCountdown(f, d, function () {
                location.reload()
            })
        }
    }
}
function registerRepairTimeCountDown(e) {
    var d = $(e);
    var f = $(e).data("duration");
    if (f > 0) {
        if (!repairTimeDownForStationScreen[e]) {
            repairTimeDownForStationScreen[e] = new simpleCountdown(d, f, function () {
                location.reload()
            })
        }
    }
}
$(document).on("click", "input.burnUpButton", displayBurnUpEnquiry);
$(document).on("click", "input.startRepairsButton", startWreckFieldRepairs);
$(document).on("click", "input.reCommissionButton", reCommissionShips);
$(document).on("ajaxShowElement", function (d, c) {
    if (c == TECHID_REPAIR_DOCK) {
        registerBurnUpCountDown("#burnUpCountDownForStationScreen");
        registerRepairTimeCountDown("#repairTimeCountDownForStationScreen")
    }
});
$(document).on("ajaxShowOverlay", function (b) {
    registerBurnUpCountDown("#burnUpCountDownForRepairOverlay");
    registerRepairTimeCountDown("#repairTimeCountDownForRepairOverlay")
});
function resourceTicker(e) {
    var f = this;
    f.config = e;
    f.htmlObj = document.getElementById(f.config.valueElem);
    var d = new Date();
    f.startTime = d.getTime();
    f.updateResource = function () {
        var a = new Date().getTime();
        nrResource = f.config.available + f.config.production * (a - f.startTime) / 1000;
        nrResource = Math.max(0, Math.floor(nrResource));
        if (nrResource < f.config.available || nrResource >= f.config.available && nrResource < f.config.limit) {
            f.htmlObj.innerHTML = gfNumberGetHumanReadable(nrResource, isMobile);
            if (nrResource >= f.config.limit[1]) {
                f.htmlObj.className = "overmark"
            } else {
                if (nrResource >= f.config.limit * 0.9 && nrResource < f.config.limit) {
                    f.htmlObj.className = "middlemark"
                } else {
                    if (nrResource <= f.config.limit * 0.9) {
                        f.htmlObj.className = ""
                    }
                }
            }
            if (f.config.production < 0 && nrResource == 0 || f.config.production == 0) {
                timerHandler.removeCallback(f.intervalObj)
            }
        } else {
            if (f.config.production > 0 && nrResource >= f.config.available && nrResource >= f.config.limit) {
                nrResource = gfNumberGetHumanReadable(f.config.limit, isMobile);
                f.htmlObj.innerHTML = nrResource;
                f.htmlObj.className = "overmark";
                timerHandler.removeCallback(f.intervalObj)
            }
        }
    };
    if (f.config.production > 0 && f.config.available >= f.config.limit) {
        return
    }
    if (f.config.production <= 0 && f.config.available == 0) {
        return
    }
    f.intervalObj = timerHandler.appendCallback(f.updateResource)
}
function initResourceTrader() {
    $(".big_tabs").tabs({activate: hideTipsOnTabChange});
    $(".resource_link").on("click", onSelectResource);
    $(".btn_calltrader").on("click", callTrader)
}
function onSelectResource(f) {
    var e = $(f.currentTarget);
    if (!e.hasClass("active")) {
        $(".resource_link").removeClass("active");
        e.addClass("active");
        $(".btn_calltrader").attr("disabled", false).data("offerId", e.data("resourceId"));
        var d = $(".getNewTraderDiv");
        if (d.hasClass("hidden") && !e.hasClass("oldTraderActive") || !d.hasClass("hidden") && e.hasClass("oldTraderActive")) {
            d.parent().children().toggleClass("hidden")
        }
    }
}
function showTradeNowButton() {
    if ($("#callTrader").hasClass("traderActive")) {
        $("#callTrader").show()
    } else {
        $("#callTrader").hide()
    }
}
function calcCosts(c, d) {
    return Math.ceil(d * factor[offer_id] / factor[c])
}
function calcInputFromCosts(c, d) {
    return Math.max(Math.floor(d / factor[offer_id] * factor[c]), 0)
}
function setValue(c, d) {
    if (offer_id == c) {
        $("#" + c + "_value_label").html(number_format(d, 0, loca.decimalPoint, loca.thousandsSeparator))
    } else {
        formatNumber("#" + c + "_value", d)
    }
}
function checkValue(b) {
    setValue(b, Math.min(getValue($("#" + b + "_value").val()), Math.round(storage[b])));
    free_id = 6 - b - offer_id;
    offer_costs = calcCosts(free_id, getValue($("#" + free_id + "_value").val()));
    costs = calcCosts(b, getValue($("#" + b + "_value").val()));
    freeOfferCosts = Math.round(offer_amount - (offer_costs));
    if (costs > freeOfferCosts) {
        setValue(b, calcInputFromCosts(b, freeOfferCosts));
        costs = calcCosts(b, getValue($("#" + b + "_value").val()))
    }
    offer_costs = offer_costs + costs;
    setValue(offer_id, offer_costs);
    document.getElementById(b + "_storage").innerHTML = number_format((storage[b] - getValue($("#" + b + "_value").val())), 0, loca.decimalPoint, loca.thousandsSeparator)
}
function setMaxValue(b) {
    setValue(b, storage[b]);
    checkValue(b)
}
function callTrader(h) {
    if ($(h.currentTarget).attr("disabled") == "disabled") {
        return
    }
    var g = $(h.currentTarget).data("offerId"), e = $(h.currentTarget).data("askOverwrite");
    if (typeof(e) == "undefined") {
        e = true
    }
    if (darkMatter < traderCosts) {
        errorBoxDecision(LocalizationStrings.error, loca.errorNotEnoughDM, LocalizationStrings.yes, LocalizationStrings.no, redirectPremium);
        return
    }
    function f() {
        if (!$(".call_trader_box .getNewTraderDiv").hasClass("hidden")) {
            $(".call_trader_box").children().toggleClass("hidden")
        }
        $(".resource_list .resource_link").removeClass("oldTraderActive").filter(function (a) {
            return $(this).data("resourceId") == g
        }).addClass("oldTraderActive");
        $("#callTrader").show().addClass("traderActive");
        console.log('f() traderCallLink');

        $.post(traderCallLink, {
            offer_id: g,
            token: traderResourcesToken
        }, function (a) {
            a = $.parseJSON(a);
            traderResourcesToken = a.newToken;
            if (a.status == "1") {
                traderObj.reloadResources();
                $("#callTrader").addClass("traderActive").show();
                openOverlay(traderOverlayLink, {"class": "traderlayer"});
                var d = $("#activeTrader");
                var b = "metal";
                var c = loca.traderResourceTitleMetal;
                switch (g) {
                    case 2:
                        b = "crystal";
                        c = loca.traderResourceTitleCrystal;
                        break;
                    case 3:
                        b = "deut";
                        c = loca.traderResourceTitleDeuterium;
                        break
                }
                d.find(".left_content #material").attr("class", b);
                d.find("p.stimulus").html(c);
                d.show();
                $("#boxHeader, #boxFooter").show()
            } else {
                errorBoxAsArray(a.errorbox)
            }
        })
    }

    if (e && $("#callTrader").is(":visible")) {
        errorBoxDecision(loca.traderResourceNewQuestionHeadline, loca.traderResourceNewQuestion, LocalizationStrings.yes, LocalizationStrings.no, f)
    } else {
        f()
    }
}
function tradeDone(b) {
    b = $.parseJSON(b);
    traderResourcesToken = b.newToken;
    if (b.status == "1") {
        $(".overlayDiv.traderlayer").dialog("close");
        $(".call_trader_box").children().toggleClass("hidden");
        traderObj.reloadResources();
        $("#callTrader").removeClass("traderActive").hide();
        $(".resource_link").removeClass("oldTraderActive active");
        $(".btn_calltrader").attr("disabled", true);
        $("#activeTrader").hide()
    }
    errorBoxAsArray(b.errorbox)
}
function closeSearch() {
    if (typeof(currentPage != "undefined")) {
        if (currentPage == "fleet1" || currentPage == "fleet2") {
            $("a#continue").focus()
        } else {
            if (currentPage == "fleet3") {
                $("a#start").focus()
            }
        }
    }
}
function GFSlider(d) {
    var c = this;
    c.duration = 500;
    c.zIndex = 10;
    c.intervalTime = 30;
    c.lastTid = 0;
    c.inAction = false;
    c.isOpen = false;
    c.lastObj = false;
    c.currHeight = d.offsetHeight;
    c.opacity = 1;
    c.header = document.getElementById("header_text");
    c.ressButton = document.getElementById("resources_button");
    c.areaMap = document.getElementById("transImg");
    this.slideIn = function (b, a, f) {
        if (!c.inAction) {
            c.slideInObj = b;
            if (c.lastTid != a || f) {
                b.opacity = 1;
                c.lastTid = a;
                $("#detail").html('<div id="techDetailLoading"></div>');
                if (!c.isOpen) {
                    if (c.ressButton) {
                        c.ressButton.style.display = "none"
                    }
                    b.style.height = "1px";
                    b.style.display = "block";
                    b.style.overflow = "hidden";
                    c.inAction = true;
                    c.startTime = new Date().getTime();
                    c.slideInStep();
                    c.isOpen = true
                } else {
                    loadDetails(c.lastTid)
                }
            } else {
                c.header.style.display = "block";
                c.opacity = 0;
                c.lastTid = 0;
                c.inAction = true;
                c.isOpen = false;
                c.startTime = new Date().getTime();
                c.slideOutObj = c.slideInObj;
                c.slideOutStep()
            }
        }
    };
    this.slideInStep = function () {
        d = c.slideInObj;
        var a = new Date().getTime();
        var b = parseInt(c.currHeight * ((a - c.startTime) / c.duration));
        if (b < c.currHeight) {
            d.style.height = (b) + "px";
            d.style.marginTop = (c.currHeight - 1 - b) + "px";
            window.setTimeout(c.slideInStep, c.intervalTime);
            c.opacity = Math.max(c.opacity - 0.1, 0);
            c.header.style.opacity = c.opacity;
            c.header.style.filter = "Alpha(opacity=" + (0.5 * 100) + ")"
        } else {
            d.style.height = c.currHeight + "px";
            d.style.marginTop = "0px";
            c.inAction = false;
            c.header.style.display = "none";
            loadDetails(c.lastTid);
            if (c.lastObj && d != c.lastObj) {
                c.hideLast()
            }
            c.lastObj = d
        }
    };
    this.slideOutStep = function () {
        d = c.slideInObj;
        var a = new Date().getTime();
        height = parseInt(c.currHeight * ((a - c.startTime) / c.duration));
        if (height < c.currHeight) {
            d.style.height = (c.currHeight - 1 - height) + "px";
            d.style.marginTop = (height) + "px";
            window.setTimeout(c.slideOutStep, c.intervalTime);
            c.opacity = Math.max(c.opacity + 0.1, 0);
            c.header.style.opacity = c.opacity
        } else {
            d.style.height = c.currHeight + "px";
            d.style.marginTop = "0px";
            c.opacity = 1;
            c.header.style.opacity = c.opacity;
            if (c.ressButton) {
                c.ressButton.style.display = "block"
            }
            d.style.display = "none";
            c.inAction = false;
            c.hideLast()
        }
    };
    this.hideLast = function () {
        if (c.lastObj) {
            $(".slideIn").removeClass("active");
            c.lastObj.style.display = "none";
            c.inAction = false
        }
        $(document).trigger("ajaxHideElement", c.lastTid)
    };
    this.hide = function (a) {
        $(".slideIn").removeClass("active");
        c.slideOutObj = a;
        c.opacity = 1;
        c.header.style.opacity = c.opacity;
        c.header.style.display = "block";
        if (c.ressButton) {
            c.ressButton.style.display = "block"
        }
        if (c.areaMap) {
            c.areaMap.style.display = "block"
        }
        c.slideOutObj.style.display = "none";
        c.inAction = false;
        c.lastTid = 0;
        c.isOpen = false
    }
}
function rocketsDestroyed(b) {
    var b = $.parseJSON(b);
    errorBoxAsArray(b.errorbox);
    if (!b.errorbox.failed) {
        $(".rocketlayer").dialog("close");
        gfSlider.slideIn(getElementByIdWithCache("detail"), techID, true)
    }
}
function supplyFleet(b) {
    var b = $.parseJSON(b);
    if (b.status) {
        getAjaxResourcebox();
        supplyTimes[b.id] = b.time;
        new simpleCountdown($("#holdingTime-" + b.id), b.time)
    }
    errorBoxAsArray(b.errorbox)
}
function updateSupplyDetails(e, f, d) {
    $("#shipCount").html(gfNumberGetHumanReadable(e));
    $("#deutCosts").html(gfNumberGetHumanReadable(f));
    $("span.countdown").hide();
    $("#holdingTime-" + d).show()
}
function initAllianceDepot() {
    $(".overlayDiv[data-page='allydepotlayer'] select").ogameDropDown();
    $(".holdingTime:first-child").show();
    for (var b in supplyTimes) {
        new simpleCountdown($("#holdingTime-" + b), supplyTimes[b])
    }
    $("#supplyTimeInput").focus(function () {
        clearInput(this)
    }).keyup(function () {
        var a = getValue($("#resources_deuterium").text());
        var d = getValue($("#deutCosts").text());
        checkIntInput(this, 1, Math.floor(a / d))
    })
}
function tabletInitOverviewAdvice() {
    if (!isMobile) {
        return false
    }
    var f = $(".adviceWrapper");
    var d = f.find("#exodus-indicator, #exodus-timer");
    var e = f.find("#exodus-indicator-processed");
    f.prev().before(d);
    f.prev().before(e);
    $("#planetdata").after(f)
}
function tabletInitGalaxyDetails() {
    if (!isMobile) {
        return false
    }
    var b = ["js_planet", "js_moon", "js_debris", "js_playerName", "js_allyTag"];
    $(".js_detailRow").css("display", "none");
    $(".js_detailRow").each(function (e) {
        e = $(this).attr("rel");
        for (var f = 0; f < b.length; f++) {
            var a = $("." + b[f] + e);
            if (a === undefined || a.length == 0) {
                if (e == 17) {
                    continue
                }
                return
            }
            if (a.attr("class").indexOf("js_no_action") >= 0) {
                continue
            }
            a.unbind();
            a.bind("click.planet", function (c) {
                if ($(this).hasClass("active")) {
                    $(".row *.active").removeClass("active");
                    $(".js_detailRow").css("display", "none")
                } else {
                    $(".row *.active").removeClass("active");
                    if ($(".bdaySlotBox")) {
                        $(".bdaySlotBox .name").removeClass("active")
                    }
                    $(".js_detailRow").css("display", "none");
                    if ($(this).html().trim()) {
                        $(this).addClass("active");
                        if ($(this).attr("class").indexOf("js_planet") >= 0) {
                            elem = ".js_detailRowPlanet" + e
                        } else {
                            if ($(this).attr("class").indexOf("js_moon") >= 0) {
                                elem = ".js_detailRowMoon" + e
                            } else {
                                if ($(this).attr("class").indexOf("js_debris") >= 0) {
                                    elem = ".js_detailRowDebris" + e
                                } else {
                                    if ($(this).attr("class").indexOf("js_playerName") >= 0) {
                                        elem = ".js_detailRowPlayer" + e
                                    } else {
                                        if ($(this).attr("class").indexOf("js_allyTag") >= 0) {
                                            elem = ".js_detailRowAlliance" + e
                                        }
                                    }
                                }
                            }
                        }
                        if (e == 17) {
                            $(elem).css("display", "block")
                        } else {
                            $(elem).css("display", "table-row")
                        }
                    }
                }
            })
        }
    });
    $("a.planetMoveIcons").bind("click", function (a) {
        a.stopPropagation()
    })
}
function tabletToggleTechtreeInfos(f) {
    if (!isMobile) {
        return false
    }
    var e = $("div.techtree[data-id='" + f + "']");
    var d = e.find(".techImage a");
    d.each(function () {
        var b = $(this);
        var c = b.parent().hasClass("built") ? "undermark" : "overmark";
        var a = b.data("tech-name");
        var h = b.data("tech-type");
        b.append('<div class="short_info" style="display: none"><span class="' + c + '">' + a + "</span><br/>" + h + "</div>")
    });
    if (d.length) {
        e.append($('<a id="toggleDetails" href="javascript:void(0)" class="btn_blue">' + LocalizationStrings.moreDetails + "</a>").click(function () {
            var a = e.find(".short_info");
            if (a.is(":visible")) {
                $(this).text(LocalizationStrings.moreDetails)
            } else {
                $(this).text(LocalizationStrings.lessDetails)
            }
            a.toggle()
        }))
    }
}
function tabletInitEmpire() {
    if (!isMobile) {
        return false
    }
    var b = $("#mainWrapper").width();
    b = (b < 1024) ? "1024" : b;
    $("#outerWrapper").width(b);
    $(".reset").hide()
}
function tabletInitPayment() {
    if (!isMobile) {
        return false
    }
    $("#payment").parent(".overlayDiv").dialog("option", "title", paymentLoca.title);
    document.addEventListener("deviceready", function () {
        $("#mobilePayment a.js_buyPacket").unbind("click").bind("click", function () {
            HostApp.StartPayment($(this).attr("ref"), userData.id, constants.name, constants.language)
        });
        HostApp.OnPaymentFinished = function () {
            getAjaxResourcebox(function (b) {
                fadeBox(paymentLoca.success, false);
                $("#payment").parent().dialog("close");
                $("#planet #content .level span").attr("class", "undermark").text(b.darkmatter.string)
            })
        };
        HostApp.OnPaymentFailed = function () {
            fadeBox(paymentLoca.error, true)
        }
    }, false)
}
function tabletInitGalaxy() {
    if (!isMobile) {
        return false
    }
    $("#galaxyContent").wipetouch({
        wipeLeft: function (b) {
            if (!$("#galaxyLoading:visible").length) {
                submitOnKey(39)
            }
        },
        wipeRight: function (b) {
            if (!$("#galaxyLoading:visible").length) {
                submitOnKey(37)
            }
        },
        preventDefault: false,
        preventDefaultWhenTriggering: true,
        moveX: 180,
        moveY: 60
    })
}
function initRetinaImages() {
    if ($(".js_replace2x").css("font-size") == "1px") {
        $("img.js_replace2x").each(function () {
            $(this).attr("src", $(this).attr("rel"))
        })
    }
}
function setupOverlay(e, d, f) {
    $(".build-it_premium").addClass("overlay");
    $(".build-it_premium").attr("href", e);
    $(".build-it_premium").data("overlay-title", d);
    $(".build-it_premium").data("techid", f)
}
function setupBuildinglistForBuildItButton(e, f, d) {
    new baulisteCountdown(document.getElementById("possibleInTime"), buildableAt, pageToReload)
}
function drawErrorbox(p, n, l, h, o, m) {
    var m = m == undefined ? false : m;
    var k = m !== false ? $("." + m) : $(".build-it_disabled");
    if (typeof(o) == "undefined" || o == "") {
        o = document.location.href
    }
    k.click(function () {
        if (l !== undefined && h !== undefined && o !== undefined) {
            if (p == "notify") {
                errorBoxNotify(l, n, h.allOk, function () {
                    window.location.href = o
                })
            }
            if (p == "decision") {
                errorBoxDecision(l, n, h.allYes, h.allNo, function () {
                    window.location.href = o
                })
            }
        }
        if (p == "fadeBox") {
            fadeBox(n, true)
        }
    })
}
function initializeBuildItButton() {
    if (hasCommander && buildableAt > 0) {
        setupBuildinglistForBuildItButton(loca.allDetailNow, techID, showSlotWarning)
    }
    if (buttonState) {
        $(".build-it").on("click", function () {
            sendBuildRequest(null, null, showSlotWarning)
        })
    } else {
        if (couldBeBuild) {
            $errorCount = showErrors()
        } else {
            $errorCount = 99
        }
        if ((error == null || error == 0) && $errorCount == 0) {
            $(".build-it_premium").on("click", function () {
                var c = $("#number").val();
                if (typeof c == "undefined") {
                    c = 1
                }
                var d = links.overlay + "&amount=" + c;
                setupOverlay(d, overlayTitle, techID)
            })
        }
    }
    $(document).off("keypress.submitBuild").on("keypress.submitBuild", "#contentWrapper #inhalt #number", function (c) {
        if (c.keyCode != 13) {
            return
        }
        c.stopPropagation();
        var d = $(".build-it_premium");
        if (d.length) {
            d.trigger("click")
        } else {
            $(".build-it").trigger("click")
        }
    })
}
function showErrors() {
    var b = {allYes: loca.allYes, allNo: loca.allNo, allOk: loca.allOk};
    if (isBuildlistNeeded) {
        if (!hasCommander && !(isShip || isRocket)) {
            drawErrorbox("decision", loca.infoBuildlist, loca.allError, b, links.decisionCommander, "build-it_premium");
            return 1
        }
        if (isRocketAndStorageNotFree) {
            drawErrorbox("notify", loca.noRocketsiloCapacity, loca.allError, b, links.notify);
            return 1
        }
    } else {
        if (error !== null && error !== 0) {
            if (premiumerror) {
                if (showErrorOnPremiumbutton) {
                    drawErrorbox("decision", errorlist[error], loca.allError, b, links[error], buttonClass);
                    return 1
                } else {
                    drawErrorbox("decision", errorlist[error], loca.allError, b, links[error]);
                    return 1
                }
            } else {
                if (isRocketAndStorageNotFree) {
                    drawErrorbox("notify", loca.noRocketsiloCapacity, loca.allError, b, links.notify);
                    return 1
                } else {
                    if (isBusy) {
                        return 1
                    } else {
                        drawErrorbox("notify", errorlist[error], loca.allError, b, "");
                        return 1
                    }
                }
            }
        }
    }
    return 0
}
function initTechDetailsAjax() {
    initializeBuildItButton();
    refreshBars("bar_container", "filllevel_bar")
}
function initGlobalTechtree(c) {
    var d = $("div.techtree[data-id='" + c + "']");
    d.find(".headline").unbind("click").bind("click", function () {
        $(this).next().toggle(function () {
            var a = d.parents(".ui-dialog");
            a.hide();
            $(this).toggleClass("open");
            a.show()
        })
    });
    if (openTree == "all") {
        d.find(".techtree_content").show(0, function () {
            $(this).addClass("open")
        })
    } else {
        if (openTree != null) {
            d.find(".techtree_content_" + openTree).show(0, function () {
                $(this).addClass("open")
            })
        }
    }
}
function initOverlayName() {
    $("div.techtree").each(function () {
        if (typeof($(this).attr("data-title")) != "undefined") {
            $(this).parent(".overlayDiv").dialog("option", "title", $(this).attr("data-title"))
        }
    })
}
function initTechtree(g) {
    var h = $("div.techtree[data-id='" + g + "']");
    var e = h.find(".techImage").outerHeight(true);
    var f = 20;
    h.find(".techWrapper.depth1").each(function () {
        f += $(this).outerWidth()
    });
    h.css("width", f);
    $(document).ready(function () {
        var d = jsPlumb.getInstance();
        d.Defaults.Container = h.find(".graph");
        d.Defaults.Connector = ["Flowchart", {cornerRadius: 20}];
        d.Defaults.Endpoint = ["Rectangle", {
            cssClass: "endpoint",
            width: 1,
            height: 1
        }];
        d.Defaults.Anchors = ["ContinuousTop", "ContinuousBottom"];
        var H = {};
        $.each(endpoints, function () {
            var m = h.find(".tech" + this.toString());
            d.addEndpoint(m);
            var k = Math.floor(m.find("a").offset().left);
            var l = Math.floor(m.find("a").offset().top);
            H[this] = [k, l]
        });
        var D;
        do {
            D = false;
            $.each(connections, function () {
                var k = h.find(".tech" + this.source + " a");
                var l = h.find(".tech" + this.target + " a");
                if (k.offset().top >= l.offset().top - 10 && k.offset().top <= l.offset().top + 10) {
                    k.parent().css("margin-top", parseInt(k.parent().css("margin-top").replace(/px/, "")) + e);
                    k.parent().parent().find("a[data-tech-id]").each(function () {
                        H[$(this).attr("data-tech-id")][1] += e
                    });
                    D = true
                }
            })
        } while (D);
        var B = [];
        var F = [];
        for (var C in H) {
            if (B.indexOf(H[C][0]) == -1) {
                B.push(H[C][0])
            }
            if (F.indexOf(H[C][1]) == -1) {
                F.push(H[C][1])
            }
        }
        B.sort(function (k, l) {
            return (k > l ? 1 : -1)
        });
        F.sort(function (k, l) {
            return (k > l ? 1 : -1)
        });
        var b = {};
        for (var G in H) {
            b[G] = {left: B.indexOf(H[G][0]), top: F.indexOf(H[G][1])}
        }
        connections.sort(function (k, l) {
            return (Math.abs(b[k.source]["left"] - b[k.target]["left"]) < Math.abs(b[l.source]["left"] - b[l.target]["left"])) ? -1 : 1
        });
        var w = {
            hasRequirements: {strokeStyle: "#015100", lineWidth: 3},
            hasNotRequirements: {strokeStyle: " #510009", lineWidth: 3}
        };
        var x = [[0, 0.5, -1, 0], [0, 0.3, -1, 0], [0, 0.7, -1, 0], [0, 0.9, -1, 0]];
        var c = [[1, 0.5, 1, 0], [1, 0.3, 1, 0], [1, 0.7, 1, 0], [1, 0.9, 1, 0]];
        var E = [[0.5, 1, 0, 1], [0.3, 1, 0, 1], [0.7, 1, 0, 1], [0.9, 1, 0, 1]];
        var y = [[0.5, 0, 0, -1], [0.3, 0, 0, -1], [0.7, 0, 0, -1], [0.9, 0, 0, -1]];
        var A = {};

        function a(l, m, n, k) {
            if (!k[l]) {
                k[l] = {}
            }
            if (k[l][m] == undefined) {
                k[l][m] = 0
            }
            ++k[l][m];
            return n[k[l][m] - 1]
        }

        $.each(connections, function () {
            var m = h.find(".tech" + this.source + " a");
            var l = h.find(".tech" + this.target + " a");
            var k = {
                source: m,
                target: l,
                overlays: [["Arrow", {
                    location: -5,
                    paintStyle: w[this.paintStyle],
                    width: 8,
                    length: 8,
                    foldback: 0.8
                }], ["Label", {
                    label: this.label,
                    cssClass: "label " + this.paintStyle,
                    location: 0.85
                }]],
                paintStyle: w[this.paintStyle],
                hoverPaintStyle: {strokeStyle: "rgb(255, 255, 0)"}
            };
            if (b[this.target].left < b[this.source].left) {
                if (!lineInCoordinatesBlocked(b, b[this.source].left, b[this.source].top, b[this.source].left, b[this.target].top) && !positionInCoordinatesBlocked(b, b[this.source].left, b[this.target].top) && !lineInCoordinatesBlocked(b, b[this.source].left, b[this.target].top, b[this.target].left, b[this.target].top)) {
                    k.anchors = [a(this.source, "top", y, A), a(this.target, "right", c, A)]
                } else {
                    k.anchors = [a(this.source, "left", x, A), a(this.target, "bottom", E, A)];
                    k.overlays[1][1] = readableVersionOfLabel(k.overlays[1][1], A[this.target].bottom)
                }
            } else {
                if (b[this.target].left > b[this.source].left) {
                    if (!lineInCoordinatesBlocked(b, b[this.source].left, b[this.source].top, b[this.source].left, b[this.target].top) && !positionInCoordinatesBlocked(b, b[this.source].left, b[this.target].top) && !lineInCoordinatesBlocked(b, b[this.source].left, b[this.target].top, b[this.target].left, b[this.target].top)) {
                        k.anchors = [a(this.source, "top", y, A), a(this.target, "left", x, A)]
                    } else {
                        k.anchors = [a(this.source, "right", c, A), a(this.target, "bottom", E, A)];
                        k.overlays[1][1] = readableVersionOfLabel(k.overlays[1][1], A[this.target].bottom)
                    }
                } else {
                    if (b[this.target].top < b[this.source].top - 1 && lineInCoordinatesBlocked(b, b[this.source].left, b[this.source].top, b[this.target].left, b[this.target].top)) {
                        k.anchors = [a(this.source, "left", x, A), a(this.target, "left", x, A)]
                    } else {
                        k.anchors = [a(this.source, "top", y, A), a(this.target, "bottom", E, A)];
                        k.overlays[1][1] = readableVersionOfLabel(k.overlays[1][1], A[this.target].bottom)
                    }
                }
            }
            d.connect(k)
        })
    });
    tabletToggleTechtreeInfos(g)
}
function readableVersionOfLabel(e, f) {
    e.location = (-0.05 * f + 0.85);
    var d = e.label.indexOf("/");
    if (d) {
    }
    return e
}
function lineInCoordinatesBlocked(n, p, o, m, q) {
    if (p == m) {
        for (var r in n) {
            if (n[r].left == p && o > n[r].top && q < n[r].top) {
                return true
            }
        }
    } else {
        if (o == q && p > m) {
            for (var k in n) {
                if (n[k].top == o && p > n[k].left && m < n[k].left) {
                    return true
                }
            }
        } else {
            if (o == q && p < m) {
                for (var l in n) {
                    if (n[l].top == o && p < n[l].left && m > n[l].left) {
                        return true
                    }
                }
            }
        }
    }
    return false
}
function positionInCoordinatesBlocked(g, h, e) {
    for (var f in g) {
        if (g[f].left == h && g[f].top == e) {
            return true
        }
    }
    return false
}
var javascriptAvailable = true;
var days = new Array("Mon", "Tus", "Wed", "Thu", "Fri", "Sat", "Sun");
var months = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
function getFormatedDate(d, f) {
    var e = new Date();
    e.setTime(d);
    str = f;
    str = str.replace("[d]", dezInt(e.getDate(), 2));
    str = str.replace("[D]", days[e.getDay()]);
    str = str.replace("[m]", dezInt(e.getMonth() + 1, 2));
    str = str.replace("[M]", months[e.getMonth()]);
    str = str.replace("[j]", parseInt(e.getDate()));
    str = str.replace("[Y]", e.getFullYear());
    str = str.replace("[y]", e.getFullYear().toString().substr(2, 4));
    str = str.replace("[G]", e.getHours());
    str = str.replace("[H]", dezInt(e.getHours(), 2));
    str = str.replace("[i]", dezInt(e.getMinutes(), 2));
    str = str.replace("[s]", dezInt(e.getSeconds(), 2));
    return str
}
function dezInt(g, m, k) {
    k = (k) ? k : "0";
    var l = (g < 0) ? "-" : "", h = (k == "0") ? l : "";
    g = Math.abs(parseInt(g, 10));
    m -= ("" + g).length;
    for (var n = 1; n <= m; n++) {
        h += "" + k
    }
    h += ((k != "0") ? l : "") + g;
    return h
}
function getFormatedTime(b) {
    hours = Math.floor(b / 3600);
    timeleft = b % 3600;
    minutes = Math.floor(timeleft / 60);
    timeleft = timeleft % 60;
    seconds = timeleft;
    return dezInt(hours, 2) + ":" + dezInt(minutes, 2) + ":" + dezInt(seconds, 2)
}
function tsdpkt(h) {
    var f = "";
    if (h < 0) {
        f = "-"
    }
    h = Math.abs(h);
    var e = h % 1000;
    while (h >= 1000) {
        var g = "";
        if ((h % 1000) < 100) {
            g = "0"
        }
        if ((h % 1000) < 10) {
            g = "00"
        }
        if ((h % 1000) == 0) {
            g = "00"
        }
        h = Math.abs((h - (h % 1000)) / 1000);
        e = h % 1000 + LocalizationStrings.thousandSeperator + g + e
    }
    e = f + e;
    return e
}
function formatTime(f) {
    var e = Math.floor(f / 3600);
    f -= e * 3600;
    var d = Math.floor(f / 60);
    f -= d * 60;
    if (d < 10) {
        d = "0" + d
    }
    if (f < 10) {
        f = "0" + f
    }
    return e + ":" + d + ":" + f
}
function round(f, g) {
    if (g < 1 || g > 14) {
        return false
    }
    var h = Math.pow(10, g);
    var e = (Math.round(f * h) / h).toString();
    if (e.indexOf(".") == -1) {
        e += "."
    }
    e += h.toString().substring(1);
    return e.substring(0, e.indexOf(".") + g + 1)
}
function show_hide_menus(b) {
    if ($(b).is(":visible")) {
        $(b).hide()
    } else {
        $(b).show()
    }
}
function change_class(b) {
    if (document.getElementById(b).className == "closed") {
        document.getElementById(b).className = "opened"
    } else {
        document.getElementById(b).className = "closed"
    }
}
function show_hide_tbl(f) {
    var e = document.getElementById(f);
    try {
        if (e) {
            e.style.display = (e.style.display == "none" ? "table-row" : "none")
        }
    } catch (d) {
        e.style.display = "block"
    }
}
function cntchar(f, d) {
    var e = $(f);
    if (e.val().length > d) {
        e.val(e.val().substr(0, d))
    }
    e.parents("form").find(".cntChars").text(e.val().length)
}
function getSession() {
    return $('meta[name="ogame-session"]').attr("content")
}
function showGalaxy(f, d, e) {
    openParentLocation("index.php?page=galaxy&no_header=1&galaxy=" + f + "&system=" + d + "&planet=" + e)
}
function openParentLocation(c) {
    try {
        window.opener.document.location.href = c
    } catch (d) {
        try {
            window.parent.document.location.href = c
        } catch (d) {
            document.location.href = c
        }
    }
}
function submitOnEnter(d) {
    var c;
    if (window.event) {
        c = window.event.keyCode
    } else {
        if (d) {
            c = d.which
        } else {
            return true
        }
    }
    if (c == 13) {
        trySubmit();
        return false
    } else {
        return true
    }
}
function formSubmitOnEnter(d, e) {
    var f;
    if (window.event) {
        f = window.event.keyCode
    } else {
        if (e) {
            f = e.which
        } else {
            return true
        }
    }
    if (f == 13) {
        document.forms[d].submit();
        return false
    } else {
        return true
    }
}
function setMaxIntInput(e, d) {
    for (var f in d) {
        if (!$(e).find("#ship_" + f).attr("disabled")) {
            $(e).find("#ship_" + f).val(d[f]);
            checkIntInput($(e).find("ship_" + f), 0, d[f])
        }
    }
}
function clearInput(b) {
    $(b).val("")
}
function checkIntInput(g, f, h) {
    var e = $(g).val();
    if (typeof e != "undefined" && e != "") {
        intVal = Math.abs(getValue(e));
        if (h != null) {
            intVal = Math.min(intVal, h)
        }
        $(g).val(intVal)
    }
}
function handlerToSubmitAjaxForm(c) {
    var d = "submit_" + String(c);
    if ($.isFunction(window[d])) {
        window[d]()
    }
    return false
}
function ajaxCall(d, c) {
    $("#" + c + " select").ogameDropDown("destroy");
    $("#" + c).html('<p class="ajaxLoad"></p>');
    console.log('ajaxCall()');
    $.post(d, function (a) {
        $("#" + c).html(a);
        $("#" + c + " select").ogameDropDown()
    })
}
function ajaxSumbit(e, d, f) {
    $("#" + f + " select").ogameDropDown("destroy");
    $("#" + f).html('<p class="ajaxLoad"><?=LOCA_ALL_AJAXLOAD ?></p>');
    console.log('ajaxSubmit()');
    $.post(e, $("#" + d).serialize(), function (a) {
        $("#" + f).html(a);
        $("#" + f + " select").ogameDropDown()
    })
}
Number.prototype.isBetween = function (c, d) {
    return this >= c && this <= d
};
function getValue(b) {
    result = parseInt(b.toString().replace(/^k$/, "1000").replace(/k/, "000").replace(/^0+/, "").replace(/[^0-9]/g, ""));
    return isNaN(result) ? 0 : result
}
function loadScript(e, g) {
    if (typeof(loadScript.loadedScripts) == "undefined") {
        loadScript.loadedScripts = {}
    }
    if (typeof(loadScript.loadedScripts[e]) == "undefined") {
        loadScript.loadedScripts[e] = true;
        var f = document.createElement("script");
        f.type = "text/javascript";
        if (f.readyState) {
            f.onreadystatechange = function () {
                if (f.readyState == "loaded" || f.readyState == "complete") {
                    f.onreadystatechange = null;
                    g()
                }
            }
        } else {
            f.onload = function () {
                g()
            }
        }
        f.src = e;
        var h = document.getElementsByTagName("head")[0];
        h.appendChild(f)
    } else {
        g()
    }
}
function formatNumber(f, h) {
    var l = number_format(getValue(h), 0, LocalizationStrings.decimalPoint, LocalizationStrings.thousandsSeparator);
    var k = $(f);
    var g = k.getSelection();
    if (k.val() != l) {
        if (k.val().length != l.length) {
            g.start = g.start + (l.length - k.val().length);
            g.end = g.end + (l.length - k.val().length)
        }
    }
    k.val(l);
    if (k.is(":focus")) {
        k.setSelection(g)
    }
}
function initToggleHeader(b) {
    $("a.toggleHeader").click(function () {
        $("#planet").toggleClass("shortHeader");
        $(".c-left").toggleClass("shortCorner");
        $(".c-right").toggleClass("shortCorner");
        changeSetting("headerImage", b + "|" + ($("#planet.shortHeader").length == 1))
    })
}
function initFormValidation() {
    $("form.formValidation").validationEngine({
        validationEventTrigger: "keyup blur",
        promptPosition: "centerRight"
    })
}
Function.prototype.clone = function () {
    var e = this;
    var f = function () {
        return e.apply(this, arguments)
    };
    f.prototype = e.prototype;
    for (var d in e) {
        if (e.hasOwnProperty(d) && d !== "prototype") {
            f[d] = e[d]
        }
    }
    return f
};
jQuery.fn.slideFadeToggle = function (e, f, d) {
    return this.animate({opacity: "toggle", width: "toggle"}, e, f, d)
};
function focusOnTabChange(e, d) {
    var f = function () {
        $(e).focus()
    };
    if (d == true) {
        $(document).ready(f)
    }
    $(window).unbind("blur").bind("blur", f)
}
function getIEVersion() {
    var b = 999;
    if (navigator.appVersion.indexOf("MSIE") != -1) {
        b = parseFloat(navigator.appVersion.split("MSIE")[1])
    }
    return b
}
ogame.tools = {
    addHover: function (b) {
        $(b).on({
            mouseenter: function () {
                $(this).addClass("over")
            }, mouseleave: function () {
                $(this).removeClass("over")
            }
        })
    }, scrollToTop: function () {
        var b = $(".scroll_to_top");
        $(window).on("scroll.scrollToTop", function () {
            $(".scroll_to_top").css({visibility: (b.offset().top > window.innerHeight) ? "visible" : "hidden"}, 600)
        });
        b.on("click.scrollToTop", function () {
            $("body, html").animate({scrollTop: 0}, 600)
        })
    }
};
function refreshBars(f, h, e) {
    var g = $("." + f);
    g.each(function () {
        var a = $(this), o = a.data("currentAmount"), p = a.data("capacity"), n = o / p * 100, b = a.find("." + h);
        if (n > 100) {
            n = 100
        } else {
            if (n == 0) {
                n = 0
            } else {
                if (n < 1.3) {
                    n = 1.3
                }
            }
        }
        b.css("width", n + "%");
        if (n < 90) {
            b.attr("class", h + " filllevel_undermark")
        } else {
            if (n > 90 && n < 100) {
                b.attr("class", h + " filllevel_middlemark")
            } else {
                b.attr("class", h + " filllevel_overmark")
            }
        }
        if (e) {
            var c = a.find("." + e), d = c.data("premiumPercent");
            if ((n + d) > 100) {
                d = 100 - n
            }
            c.css("width", d + "%")
        }
    })
}