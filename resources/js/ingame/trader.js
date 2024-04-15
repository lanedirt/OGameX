function initTrader() {
    var d = false;
    var e = $.deparam.fragment();
    if (typeof(e.animation) != "undefined") {
        if (e.animation == "false") {
            d = true
        }
    }
    var f = {
        $link: null, $panel: null, close: function () {
            this.$panel.hide()
        }, setPanel: function () {
            this.$panel = $("#js_togglePanel" + traderObj.traderId.replace(/#div_trader/, ""))
        }, init: function (k) {
            var c = $(k + " .selectWrapper");
            k = k.replace(/#div_trader/, "");
            if (!c) {
                return
            }
            this.$link = $("#js_toggleLink" + k);
            this.$panel = $("#js_togglePanel" + k);
            var a = this.$panel;
            a.hide();
            this.$link.unbind("click.selectLink").bind("click.selectLink", function (g) {
                k = traderObj.traderId.replace(/#div_trader/, "");
                if ($(this).hasClass("honor")) {
                    return false
                }
                if (a.find("ul.active").has("li").length) {
                    a.toggle()
                }
                return false
            });
            $(".selectWrapper .source").unbind("click.selectPlanetOrMoon").bind("click.selectPlanetOrMoon", function (g) {
                f.selectPlanetOrMoon(this)
            });
            $("#" + a.attr("id")).find("li").unbind("click.selectSource").bind("click.selectSource", function (g) {
                traderObj.selectSource(this);
                return false
            });
            f.outerClick(c, a);
            var b = null, l = 8000;
            c.unbind("mouseout.closeSelect").bind("mouseout.closeSelect", function (g) {
                g = g || window.event;
                var h = (g.relatedTarget) ? g.relatedTarget : g.toElement;
                if (h == c || f.isChildOf(h, c)) {
                    return
                }
                b = setTimeout(function () {
                    a.hide()
                }, l)
            }).unbind("mouseover.clearTimeout").bind("mouseover.clearTimeout", function () {
                if (b) {
                    clearTimeout(b)
                }
            })
        }, isChildOf: function (a, b) {
            b = b[0];
            while (a && a != b) {
                a = a.parentNode
            }
            return a == b
        }, outerClick: function (b, a) {
            $("body").bind("click.outerClick", function (c) {
                if (!c) {
                    c = window.event
                }
                if (!($(c.target).closest(".selectWrapper") == b) && a.is(":visible") != false) {
                    a.toggle()
                }
            })
        }, selectPlanetOrMoon: function (b) {
            var o;
            if ($(b).hasClass("selected")) {
                return false
            }
            var p = "", a = null;
            if ($(b).hasClass("js_honor")) {
                $(traderObj.traderId + " .selectWrapper .source").removeClass("selected");
                $(traderObj.traderId + " .js_honor").addClass("selected");
                $(traderObj.traderId + " .toggleLink").addClass("honor");
                var p = '<img height="18" src="/img/icons/f35675179214f8f6f0f8d75740d7db.png" alt="' + loca.honorPoints + '"/><span class="option_source">' + loca.honorPoints + "</span>";
                $(traderObj.traderId + " .js_valSourcePlanet").html(p);
                $(traderObj.traderId + " .normalResource").hide();
                $(traderObj.traderId + " .honorResource").show();
                return false
            }
            var q = $(traderObj.traderId + " .togglePanel");
            var c = "planet";
            if ($(b).hasClass("js_moon")) {
                var n = 0;
                c = "moon";
                for (o in traderObj.planets) {
                    if (traderObj.planets[o].isMoon) {
                        n++
                    }
                }
                if (n == 0) {
                    return false
                }
            }
            var r = traderObj.planets[traderObj.current.planet];
            q.find("ul").hide().removeClass("active");
            q.find("ul." + c).show().addClass("active");
            $(traderObj.traderId + " .toggleLink").removeClass("honor");
            $(traderObj.traderId + " .selectWrapper .source").removeClass("selected");
            $(traderObj.traderId + " .js_" + c).addClass("selected");
            if ($(b).hasClass("js_moon") ? r.isMoon : !r.isMoon) {
                a = traderObj.current.planet
            } else {
                if (r.otherPlanet != null && typeof(traderObj.planets[r.otherPlanet]) != "undefined") {
                    a = r.otherPlanet
                } else {
                    a = q.find("ul." + c + " li:first").attr("id")
                }
            }
            $(traderObj.traderId + " .normalResource").show();
            $(traderObj.traderId + " .honorResource").hide();
            q.find("ul li#" + a).click();
            return false
        }, setToggleLink: function (b) {
            var c = traderObj.traderId.replace(/#div_trader/, ""), h = $(b).find("span"), a = traderObj.planets[$(b).attr("id")].name;
            if (a != h.text()) {
                h.attr("title", a.replace(/\|/g, "&#124;"))
            }
            this.$link = $("#js_toggleLink" + c);
            this.$link.html($(b).html())
        }
    };
    traderObj = {
        traderBGPos: {
            "#div_traderResources": "0px 0px",
            "#div_traderAuctioneer": "-546px 0px",
            "#div_traderScrap": "0px -220px",
            "#div_traderImportExport": "-546px -220px"
        },
        timer: 500,
        planets: {},
        honorOutput: 0,
        price: 0,
        deficit: 0,
        priceImportExport: 0,
        sumResources: 0,
        traderId: null,
        resources: ["Metal", "Crystal", "Deuterium"],
        current: {planet: currentPlanetId, resource: "", sliderValue: ""},
        barXPos: -180,
        barYPos: 0,
        percentPaid: 0,
        switchingTrader: false,
        checkOverbidden: function () {
            if (playerBid == false || playerBid >= getValue($(".detail_value.currentSum").html())) {
                $(".overbid_text").hide()
            } else {
                $(".overbid_text").show()
            }
        },
        refresh: function () {
            var c = traderObj.traderId;
            if ("#" + $(this).closest(".div_trader").attr("id") !== c) {
                return
            }
            var b = $(this).attr("class");
            var a = new RegExp(/\b(js_slider\w*)\b/);
            b = (a.test(b)) ? RegExp.$1 : false;
            if (!b) {
                return
            }
            traderObj.current.sliderValue = $(this).slider("value");
            traderObj.current.resource = b.replace("js_slider", "").toLowerCase();
            if (traderObj.current.resource == "honor") {
                traderObj.honorOutput = traderObj.current.sliderValue
            } else {
                traderObj.planets[traderObj.current.planet].output[traderObj.current.resource] = traderObj.current.sliderValue
            }
            formatNumber($(c + " .js_amount.js_" + traderObj.current.resource), traderObj.current.sliderValue);
            if (c == "#div_traderAuctioneer") {
                traderObj.price = getValue($(c + " .js_price").html());
                traderObj.sumAuctioneer();
                traderObj.checkOverbidden()
            } else {
                if (c == "#div_traderImportExport") {
                    traderObj.sumImportExport()
                }
            }
        },
        resetValues: function (b, c) {
            b = b || traderObj.traderId;
            c = c || false;
            for (var h in traderObj.planets) {
                for (var a in traderObj.planets[h].output) {
                    traderObj.planets[h].output[a] = 0
                }
            }
            traderObj.honorOutput = 0;
            $(".js_amount").val(0);
            if (traderObj.traderId == "#div_traderAuctioneer") {
                traderObj.sumAuctioneer()
            } else {
                if (traderObj.traderId == "#div_traderImportExport") {
                    traderObj.sumImportExport()
                }
            }
            if (c) {
                b = b.replace(/#div_trader/, "");
                $("#js_togglePanel" + b).find("li#" + currentPlanetId).click()
            }
            f.close()
        },
        resetMaxAmount: function (c, r) {
            var b = traderObj.traderId;
            var s = traderObj.resources;
            for (var o in traderObj.planets) {
                for (var p = 0; p < s.length; p++) {
                    var a = s[p].toLowerCase();
                    traderObj.planets[o].input[a] = c[o][a]
                }
            }
            for (var q = 0; q < s.length; q++) {
                a = s[q].toLowerCase();
                var u = traderObj.planets[traderObj.current.planet].input[a];
                $(b + " .max_planet_" + a).html(number_format(u, 0))
            }
            honorScore = r;
            $(b + " .max_planet_honor").html(number_format(Math.max(0, r), 0));
            f.close()
        },
        selectSource: function (a) {
            traderObj.current.planet = $(a).attr("id");
            f.close();
            f.setToggleLink($(a));
            var o = traderObj.traderId;
            var r = traderObj.current.planet;
            var s = traderObj.resources;
            for (var q = 0; q < s.length; q++) {
                var b = s[q].toLowerCase();
                var u = traderObj.planets[r].input[b];
                if (o == "#div_traderImportExport") {
                    var c = (traderObj.priceImportExport / multiplier[b]) - traderObj.sumResources + traderObj.planets[r].output[b];
                    var p = Math.min(u, c)
                }
                $(o + " .max_planet_" + b).html(number_format(u, 0));
                $(o + " .js_amount.js_" + b).val(number_format(traderObj.planets[r].output[b], 0))
            }
        },
        selectTrader: function (o, a, m) {
            a = a || traderObj.timer;
            $.bbq.pushState({page: o, animation: "false"});
            $(".planetlink, .moonlink").fragment({page: o, animation: "false"});
            traderObj.traderId = "#div_" + o;
            var n = traderObj.traderId, p = $(".back_to_overview");
            var c = function () {
                if (n == "#div_traderAuctioneer" || n == "#div_traderImportExport") {
                    traderObj.resetValues(null, true)
                }
                var g = function () {
                    $("#traderOverview").find(".c-left, .c-right").addClass("c-small");
                    p.show();
                    if (n == "#div_traderAuctioneer" || n == "#div_traderImportExport") {
                        p.addClass("left");
                        p.removeClass("right")
                    } else {
                        if (n == "#div_traderResources" || n == "#div_traderScrap") {
                            p.addClass("right");
                            p.removeClass("left")
                        }
                    }
                    $("#planet #header_text h2").html(loca[o]).parent().show()
                };
                if (animation && !d) {
                    $("#traderOverview").find(".c-left, .c-right").hide();
                    $("#planet").animate({
                        backgroundPosition: traderObj.traderBGPos[n],
                        height: "250px"
                    }, a, function () {
                        $("#planet").addClass("detail");
                        $("#traderOverview").find(".c-left, .c-right").show();
                        g();
                        if (n == "#div_traderResources") {
                            showTradeNowButton()
                        }
                    })
                } else {
                    d = false;
                    $("#planet").css("background-position", traderObj.traderBGPos[n]).css("height", "250px");
                    g();
                    if (n == "#div_traderResources") {
                        showTradeNowButton()
                    }
                }
                f.setPanel();
                $("#planet").addClass("detail");
                $(".js_trader").hide();
                $(n).show();
                if (n == "#div_traderResources" && typeof(m) != "undefined") {
                    $(n + " .ui-tabs").tabs("option", "active", m)
                }
                traderObj.switchingTrader = false
            };
            if ($(traderObj.traderId).length == 0) {
                var b = o.toLowerCase().replace(/^trader/, "");
                $.ajax({
                    url: traderUrl,
                    type: "POST",
                    data: {show: b, ajax: 1},
                    beforeSend: function () {
                        $("#loadingOverlay").addClass(b).show()
                    },
                    success: function (g) {
                        $("#inhalt").append(g);
                        $("#loadingOverlay").hide().removeClass(b);
                        c()
                    },
                    error: function () {
                        fadeBox(loca.error, true);
                        $("#loadingOverlay").hide().removeClass(b)
                    }
                })
            } else {
                c()
            }
        },
        submitAuction: function () {
            var c = traderObj.traderId;
            var b = getValue($(c + " .js_auctioneerSum").html());
            if (!$(c + " .right_box .pay").hasClass("disabled") && traderObj.price > 0 && traderObj.deficit <= 0) {
                $(c + " .right_box .pay").addClass("disabled");
                var h = {planets: {}, honor: traderObj.honorOutput};
                for (var a in traderObj.planets) {
                    h.planets[a] = traderObj.planets[a].output
                }
                $.ajax({
                    url: auctionUrl,
                    type: "POST",
                    data: {bid: h, token: auctioneerToken, ajax: 1},
                    dataType: "json",
                    success: function (g) {
                        auctioneerToken = g.newToken;
                        fadeBox(g.message, g.error);
                        if (!g.error) {
                            traderObj.resetValues(c, false);
                            traderObj.resetMaxAmount(g.planetResources, g.honor);
                            traderObj.reloadResources()
                        }
                    },
                    error: function () {
                        fadeBox(loca.error, true)
                    }
                })
            }
            return false
        },
        submitImportExport: function () {
            if (!$(traderObj.traderId + " .right_box .pay").hasClass("disabled")) {
                $(traderObj.traderId + " .right_box .pay").addClass("disabled");
                var a = {planets: {}, honor: traderObj.honorOutput};
                for (planetId in traderObj.planets) {
                    a.planets[planetId] = traderObj.planets[planetId].output
                }
                $.ajax({
                    url: importUrl,
                    type: "POST",
                    data: {
                        action: "trade",
                        bid: a,
                        token: importToken,
                        ajax: 1
                    },
                    dataType: "json",
                    success: function (b) {
                        importToken = b.newToken;
                        fadeBox(b.message, b.error);
                        if (!b.error) {
                            for (planetId in traderObj.planets) {
                                traderObj.planets[planetId].output = {
                                    metal: 0,
                                    crystal: 0,
                                    deuterium: 0
                                }
                            }
                            $(traderObj.traderId + " .bargain_overlay").show();
                            $(traderObj.traderId + " .payment").hide();
                            $(traderObj.traderId + " .image_140px a").addClass("slideIn");
                            traderObj.reloadResources();
                            traderObj.updateImportItem(b.item);
                            traderObj.refresh()
                        }
                    },
                    error: function () {
                        fadeBox(loca.error, true)
                    }
                })
            }
            return false
        },
        reloadResources: function (a) {
            getAjaxResourcebox(a)
        },
        changeImportItem: function () {
            if ($(traderObj.traderId + " .import_bargain.change").hasClass("disabled")) {
                if (darkMatter < importChangeCost) {
                    errorBoxDecision(LocalizationStrings.error, loca.errorNotEnoughDM, LocalizationStrings.yes, LocalizationStrings.no, redirectPremium)
                }
            } else {
                $(traderObj.traderId + " .import_bargain.change").addClass("disabled");
                $.ajax({
                    url: importUrl,
                    type: "POST",
                    data: {action: "bargain", token: importToken, ajax: 1},
                    dataType: "json",
                    success: function (a) {
                        importToken = a.newToken;
                        fadeBox(a.message, a.error);
                        if (!a.error) {
                            traderObj.updateImportItem(a.item);
                            traderObj.reloadResources(function () {
                                if (a.item.offersLeft > 0 && darkMatter >= importChangeCost) {
                                    $(traderObj.traderId + " .import_bargain.change").removeClass("disabled")
                                } else {
                                    $(traderObj.traderId + " .import_bargain.change").addClass("disabled")
                                }
                            });
                            traderObj.refresh()
                        }
                    },
                    error: function () {
                        fadeBox(loca.error, true)
                    }
                })
            }
            return false
        },
        updateImportItem: function (a) {
            $(traderObj.traderId + " .got_item_text").html(a.itemText);
            $(traderObj.traderId + " .bargain_text").html(a.bargainText);
            $(traderObj.traderId + " .bargain_cost").html(a.bargainCostText);
            importChangeCost = a.bargainCost;
            $(traderObj.traderId + " .image_140px img").attr("src", "/img/icons/items/" + a.image + "-140x.png");
            removeTooltip($(traderObj.traderId + " .image_140px a"));
            $(traderObj.traderId + " .image_140px a").attr("ref", a.uuid).removeClass("tooltip").addClass("tooltipHTML").attr("title", a.tooltip);
            initTooltips($(traderObj.traderId + " .image_140px a"));
            $(traderObj.traderId + " .detail_button .amount").text(a.amount)
        },
        takeImportItem: function () {
            if (!$(traderObj.traderId + " .import_bargain.take").hasClass("disabled")) {
                $(traderObj.traderId + " .import_bargain.change").addClass("disabled");
                $(traderObj.traderId + " .import_bargain.take").addClass("disabled");
                $(traderObj.traderId + " .import_bargain.change").addClass("hidden");
                $(traderObj.traderId + " .import_bargain.take").addClass("hidden");
                $(traderObj.traderId + " .bargain_cost").addClass("hidden");
                $.ajax({
                    url: importUrl,
                    type: "POST",
                    data: {action: "takeItem", token: importToken, ajax: 1},
                    dataType: "json",
                    success: function (a) {
                        importToken = a.newToken;
                        fadeBox(a.message, a.error);
                        if (!a.error) {
                            var b = a.item.ref;
                            changeTooltip($(".detail_button[ref='" + b + "']"), a.item.title);
                            $(".detail_button[ref='" + b + "'] span.amount, #itemDetails[data-uuid='" + b + "'] span.amount").html(tsdpkt(a.item.amount));
                            if (a.item.canBeActivated) {
                                $('#itemDetails[data-uuid="' + b + '"] a.activateItem').removeClass("build-it_disabled").addClass("build-it")
                            } else {
                                $('#itemDetails[data-uuid="' + b + '"] a.activateItem').addClass("build-it_disabled").removeClass("build-it")
                            }
                            if (a.item.newOffer == false) {
                                $(traderObj.traderId + " .bargain_text").html(a.item.noOfferMessage)
                            } else {
                                traderObj.resetImport(a.item.newOffer)
                            }
                        }
                    },
                    error: function () {
                        fadeBox(loca.error, true)
                    }
                })
            }
            return false
        },
        resetImport: function (a) {
            importChangeCost = a.bargainCost;
            if (darkMatter >= importChangeCost) {
                $(traderObj.traderId + " .import_bargain.change").removeClass("disabled")
            } else {
                $(traderObj.traderId + " .import_bargain.change").addClass("disabled")
            }
            $(traderObj.traderId + " .import_bargain.take").removeClass("disabled");
            $(traderObj.traderId + " .import_bargain.change").removeClass("hidden");
            $(traderObj.traderId + " .import_bargain.take").removeClass("hidden");
            $(traderObj.traderId + " .bargain_cost").removeClass("hidden");
            $(traderObj.traderId + " .bargain_overlay").hide();
            $(traderObj.traderId + " .payment").show();
            $(traderObj.traderId + " .image_140px img").attr("src", "/cdn/img/trader/container_" + a.rarity + ".jpg");
            $(traderObj.traderId + " .image_140px a").removeClass("slideIn").attr("ref", "").removeClass("tooltipHTML").addClass("tooltip").removeClass("r_common_140px").removeClass("r_uncommon_140px").removeClass("r_rare_140px").removeClass("r_epic_140px").removeClass("r_buddy_140px").addClass("r_" + a.rarity + "_140px");
            changeTooltip($(traderObj.traderId + " .image_140px a"), a.tooltip);
            $(traderObj.traderId + " .js_import_price").removeClass("green_text").text(number_format(a.price, 0));
            $(traderObj.traderId + " .image_140px .amount").text("?");
            traderObj.priceImportExport = getValue($(".js_import_price").html());
            traderObj.resetValues(null, true);
            traderObj.init()
        },
        sumAuctioneer: function () {
            var k = traderObj.traderId;
            var a = traderObj.price;
            if (a == 0) {
                $("#div_traderAuctioneer .js_amount").attr("disabled", "disabled")
            } else {
                $("#div_traderAuctioneer .js_amount").removeAttr("disabled")
            }
            var c = 0;
            for (var b in traderObj.planets) {
                var l = traderObj.planets[b].output;
                c += parseInt(l.metal) * multiplier.metal + parseInt(l.crystal) * multiplier.crystal + parseInt(l.deuterium) * multiplier.deuterium
            }
            c += parseInt(traderObj.honorOutput) * multiplier.honor;
            c = Math.floor(c);
            traderObj.deficit = (Number(auctioneer.calculateDeficit()) - Number(c));
            if (traderObj.deficit > 0) {
                $(" .js_deficit").html(number_format(traderObj.deficit, 0))
            } else {
                $(" .js_deficit").html(number_format(0, 0))
            }
            if (c > 0) {
                $("#div_traderAuctioneer .js_auctioneerSum").html("+ " + number_format(c, 0))
            } else {
                $("#div_traderAuctioneer .js_auctioneerSum").html("")
            }
            $("#div_traderAuctioneer .js_alreadyBidden").html(number_format(Math.floor(playerBid + c), 0));
            if (a > 0 && traderObj.deficit <= 0) {
                $("#div_traderAuctioneer .right_box .pay").removeClass("disabled")
            } else {
                $("#div_traderAuctioneer .right_box .pay").addClass("disabled")
            }
        },
        sumImportExport: function () {
            var n = traderObj.traderId;
            var m = 0;
            var a = 0;
            var b = 0;
            traderObj.sumResources = 0;
            for (var c in traderObj.planets) {
                var o = traderObj.planets[c].output;
                m += parseInt(o.metal) * multiplier.metal;
                a += parseInt(o.crystal) * multiplier.crystal;
                b += parseInt(o.deuterium) * multiplier.deuterium
            }
            var p = traderObj.honorOutput * multiplier.honor;
            traderObj.sumResources += m + a + b + p;
            if (traderObj.sumResources >= traderObj.priceImportExport) {
                traderObj.sumResources = traderObj.priceImportExport
            }
            $(n + " .js_sum_price").html(number_format(Math.floor(traderObj.sumResources), 0));
            if (traderObj.sumResources >= traderObj.priceImportExport) {
                $(n + " .js_import_price").addClass("green_text");
                $(n + " .right_box .pay").removeClass("disabled")
            } else {
                $(n + " .js_import_price").removeClass("green_text");
                $(n + " .right_box .pay").addClass("disabled")
            }
        },
        updateValues: function (w) {
            var c = traderObj.traderId;
            if (c !== "#" + w.closest(".div_trader").attr("id")) {
                return
            }
            var r = w.attr("class");
            var a = new RegExp(/\b(js_slider\w*)\b/);
            r = (a.test(r)) ? RegExp.$1 : false;
            if (!r) {
                return
            }
            var s = traderObj.current.planet;
            var p, b, v;
            if (r.indexOf("More") != -1) {
                v = r.replace("More", "");
                p = "More"
            } else {
                if (r.indexOf("Max") != -1) {
                    v = r.replace("Max", "");
                    p = "Max"
                }
            }
            traderObj.current.resource = v.replace("js_slider", "").toLowerCase() || null;
            var u = traderObj.current.resource;
            var q = 0;
            if (u == "honor") {
                q = Math.max(0, honorScore)
            } else {
                q = traderObj.planets[s].input[u]
            }
            b = getValue($(c + " ." + v + "Input").val());
            if (p == "More") {
                if (c == "#div_traderImportExport") {
                    if (traderObj.sumResources <= traderObj.priceImportExport - 1000 * multiplier[u]) {
                        b += 1000
                    } else {
                        if (traderObj.sumResources < traderObj.priceImportExport) {
                            b += Math.ceil((traderObj.priceImportExport - traderObj.sumResources) / multiplier[u])
                        }
                    }
                } else {
                    if (c == "#div_traderAuctioneer" && traderObj.price > 0) {
                        b += 1000
                    }
                }
                if (b >= q) {
                    b = Math.max(0, q)
                }
            } else {
                if (p == "Max") {
                    if (c == "#div_traderImportExport") {
                        if (traderObj.sumResources == 0) {
                            b = Math.min(q, Math.ceil(traderObj.priceImportExport / multiplier[u]))
                        } else {
                            if (traderObj.sumResources.isBetween(0, traderObj.priceImportExport - 1)) {
                                b = Math.min(q, b + Math.ceil((traderObj.priceImportExport - traderObj.sumResources) / multiplier[u]));
                                b = Math.max(0, b)
                            }
                        }
                    } else {
                        if (c == "#div_traderAuctioneer" && traderObj.price > 0) {
                            b = Math.min(q, Math.ceil(getValue($(c + " .js_deficit").html()) / multiplier[u] + b))
                        }
                    }
                    if (u == "honor" && b < 0) {
                        b = 0
                    }
                }
            }
            $(c + " .js_amount." + v + "Input").val(number_format(b, 0));
            if (u == "honor") {
                traderObj.honorOutput = b
            } else {
                traderObj.planets[s].output[u] = b
            }
            if (c == "#div_traderImportExport") {
                traderObj.sumImportExport()
            } else {
                if (c == "#div_traderAuctioneer" && traderObj.price > 0) {
                    traderObj.sumAuctioneer();
                    traderObj.checkOverbidden()
                }
            }
        },
        updateValuesInputCanged: function (B) {
            var c = traderObj.traderId;
            if (c !== "#" + B.closest(".div_trader").attr("id")) {
                return
            }
            var v = B.attr("class");
            var a = new RegExp(/\b(js_slider\w*)\b/);
            v = (a.test(v)) ? RegExp.$1 : false;
            if (!v) {
                return
            }
            var A = v.replace("Input", "");
            var x = A.replace("js_slider", "").toLowerCase();
            var w = traderObj.current.planet;
            var u = 0;
            if (x == "honor") {
                u = Math.max(0, honorScore)
            } else {
                u = parseInt(traderObj.planets[w].input[x])
            }
            var b = 0;
            if (c == "#div_traderImportExport") {
                var s = 0;
                for (var r in traderObj.planets) {
                    var y = traderObj.planets[r].output;
                    if (x != "metal") {
                        s += Math.floor(parseInt(y.metal) * multiplier.metal)
                    }
                    if (x != "crystal") {
                        s += Math.floor(parseInt(y.crystal) * multiplier.crystal)
                    }
                    if (x != "deuterium") {
                        s += Math.floor(parseInt(y.deuterium) * multiplier.deuterium)
                    }
                }
                b = Math.min(getValue(B.val()), Math.ceil((traderObj.priceImportExport - s) / multiplier[x]))
            } else {
                if (c == "#div_traderAuctioneer") {
                    b = getValue(B.val())
                }
            }
            b = Math.min(b, u);
            traderObj.planets[w].output[x] = b;
            if (x == "honor") {
                traderObj.honorOutput = b
            } else {
                traderObj.planets[w].output[x] = b
            }
            if (c == "#div_traderImportExport") {
                traderObj.sumImportExport()
            } else {
                if (c == "#div_traderAuctioneer") {
                    traderObj.sumAuctioneer();
                    traderObj.checkOverbidden()
                }
            }
            formatNumber(c + " .js_amount." + A + "Input", b)
        },
        init: function () {
            $(".honorResource").hide();
            $("#menuTable a.trader").unbind("click.gotoTrader").bind("click.gotoTrader", function (a) {
                a.preventDefault();
                traderObj.switchTrader("traderResources")
            });
            $(window).unbind("hashchange.switchTrader").bind("hashchange.switchTrader", function (a) {
                var b = $.deparam.fragment(a.fragment);
                if (typeof(b.page) == "undefined" || b.page == "" && traderObj.traderId != null) {
                    traderObj.returnToOverview()
                } else {
                    traderObj.switchTrader(b.page)
                }
            });
            $(".small_back_to_overview").unbind("mouseenter").unbind("mouseout").bind("mouseenter", function () {
                $("#header_text").css("background-position", "0 -250px")
            }).bind("mouseout", function () {
                $("#header_text").css("background-position", "0 0")
            })
        },
        initSliderTrader: function (a) {
            $(a + " .js_valButton").unbind("click.valControl");
            $(a + " .js_amount").unbind("keyup.inputVal");
            f.init(a);
            $(a + " .js_valButton").bind("click.valControl", function (b) {
                traderObj.updateValues($(this));
                b.stopPropagation()
            });
            $(a + " .js_amount").bind("keyup.inputVal", function (b) {
                traderObj.updateValuesInputCanged($(this));
                b.stopPropagation()
            })
        },
        initImportExport: function () {
            traderObj.planets = planetResources;
            traderObj.priceImportExport = getValue($(".js_import_price").html());
            traderObj.initSliderTrader("#div_traderImportExport");
            $("#div_traderImportExport .right_box .pay").bind("click", function () {
                traderObj.submitImportExport()
            });
            $("#div_traderImportExport .import_bargain.change").bind("click", function () {
                traderObj.changeImportItem()
            });
            $("#div_traderImportExport .import_bargain.take").bind("click", function () {
                traderObj.takeImportItem()
            })
        },
        switchTrader: function (a) {
            if (traderObj.switchingTrader) {
                return
            }
            traderObj.switchingTrader = true;
            Tipped.hideAll();
            $("#planet .close_details:visible").click();
            if ("#div_" + a == traderObj.traderId) {
                return
            }
            if (traderObj.traderId != null || a == "" || a == null) {
                traderObj.returnToOverview();
                if (animation && !d) {
                    setTimeout(function () {
                        traderObj.selectTrader(a)
                    }, 500)
                } else {
                    traderObj.selectTrader(a)
                }
            } else {
                traderObj.selectTrader(a)
            }
        },
        returnToOverview: function () {
            Tipped.hideAll();
            $("#planet #header_text h2").empty().parent().hide();
            $("#traderOverview").find(".c-left, .c-right").hide();
            var a = traderObj.traderId;
            if (!a) {
                return
            }
            $(a).hide();
            $("#callTrader").hide();
            if (animation && !d) {
                $("#planet h2").hide();
                $("#planet").animate({
                    backgroundPosition: "-273px 0px",
                    height: "470px"
                }, 500, function () {
                    $("#planet h2").show();
                    $("#planet").removeClass("detail");
                    $("#traderOverview").find(".c-left, .c-right").show();
                    $(".js_trader").show()
                })
            } else {
                $("#planet").removeClass("detail").css("background-position", "-273px 0px").css("height", "470px");
                $(".js_trader").show()
            }
            $("#planet a").show();
            $("#planet .back_to_overview").hide();
            removeTooltip($("#planet .back_to_overview"));
            $("#traderOverview").find(".c-left, .c-right").removeClass("c-small");
            traderObj.traderId = null;
            traderObj.switchingTrader = false
        }
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
            var a = this;
            $("#js_anythingSliderShips, #js_anythingSliderDefense").anythingSlider({
                startStopped: true,
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
                onInitialized: function (u, y) {
                    if (isMobile) {
                        var x = 1000, v = 50, s = 0, c = 0, w = "ontouchend" in document, b = (w) ? "touchstart" : "mousedown", r = (w) ? "touchmove" : "mousemove", A = (w) ? "touchend" : "mouseup";
                        y.$window.bind(b, function (g) {
                            c = (new Date()).getTime();
                            s = g.originalEvent.touches ? g.originalEvent.touches[0].pageX : g.pageX
                        }).bind(A, function (g) {
                            c = 0;
                            s = 0
                        }).bind(r, function (h) {
                            h.preventDefault();
                            var g = h.originalEvent.touches ? h.originalEvent.touches[0].pageX : h.pageX, k = (s === 0) ? 0 : Math.abs(g - s), l = (new Date()).getTime();
                            if (c !== 0 && l - c < x && k > v) {
                                if (g < s) {
                                    y.goForward()
                                }
                                if (g > s) {
                                    y.goBack()
                                }
                                c = 0;
                                s = 0
                            }
                        })
                    }
                }
            });
            $("#js_anythingSliderDefense").parent().parent().hide();
            $(".scrap_defense").bind("click.tabDefense", function () {
                $(".scrap_ships").removeClass("selected");
                $(this).addClass("selected");
                $("#js_anythingSliderShips").parent().parent().hide();
                $("#js_anythingSliderDefense").parent().parent().show()
            });
            $(".scrap_ships").bind("click.tabShips", function () {
                $(".scrap_defense").removeClass("selected");
                $(this).addClass("selected");
                $("#js_anythingSliderDefense").parent().parent().hide();
                $("#js_anythingSliderShips").parent().parent().show()
            });
            $(".buildingimg a").each(function () {
                var h = $(this).attr("ref").substr(6, 3);
                var b = $(this).find(".level");
                var c = b.contents().filter(function () {
                    return this.nodeType == 3
                });
                a.ships[h] = c.text().replace(/^\s+|\s+$/g, "");
                c.remove();
                b.append(tsdpkt(a.ships[h]))
            });
            $("#js_scrapBargain").unbind("click").bind("click", function () {
                if (!$(this).hasClass("disabled")) {
                    a.bargain(a)
                }
                return false
            });
            $("#js_scrapScrapIT").unbind("click").bind("click", function () {
                if (!$(this).hasClass("disabled")) {
                    a.trade(a)
                }
                return false
            });
            $("input.ship_amount").unbind("focus").bind("focus", function () {
                a.lastTechId = $(this).attr("name").substr(2, 3);
                $(this).val("")
            });
            $("input.ship_amount").unbind("keyup change").bind("keyup change", function (c) {
                a.lastTechId = $(this).attr("name").substr(2, 3);
                formatNumber(this, $(this).val());
                var b = $(this);
                clearTimeout(b.data("timer"));
                b.data("timer", setTimeout(function () {
                    b.removeData("timer");
                    a.updateResources(a)
                }, 300))
            });
            $(".buildingimg a").unbind("click").bind("click", function () {
                return false
            });
            $(".js_maxShips").unbind("click").bind("click", function () {
                if (!isMobile) {
                    $($(this).attr("ref")).focus()
                }
                var b = a.ships[$(this).attr("ref").substr(6, 3)];
                $($(this).attr("ref")).val(tsdpkt(b)).trigger("change");
                a.updateResources(a);
                return false
            });
            $(".sendAll").unbind("click").bind("click", function () {
                $(".anythingSlider ul:visible input").each(function () {
                    a.lastTechId = $(this).attr("name").substr(2, 3);
                    var b = a.ships[a.lastTechId];
                    $(this).val(tsdpkt(b))
                });
                a.updateResources(a, function (b) {
                    if (b.error) {
                        $(".anythingSlider ul:visible input").val("");
                        $("#div_traderScrap .resource_amount").text(0);
                        a.checkShips(a)
                    }
                })
            });
            $(".sendNone").unbind("click").bind("click", function () {
                $(".anythingSlider ul:visible input").each(function () {
                    a.lastTechId = $(this).attr("name").substr(2, 3);
                    $(this).val("")
                });
                a.updateResources(a)
            });
            $("#js_bargainCost").text(tsdpkt(this.costs));
            this.checkMoney(this);
            this.checkShips(this);
            this.updateBargain(this)
        },
        bargain: function (a) {
            $("#js_scrapBargain").addClass("disabled");
            $.ajax({
                url: breakerCallLink,
                type: "POST",
                dataType: "json",
                data: {bargain: 1, token: breakerToken},
                beforeSend: function () {
                    a.lock(a)
                },
                success: function (b) {
                    a.unlock(a);
                    breakerToken = b.newToken;
                    fadeBox(b.message, b.error);
                    if (!b.error) {
                        a.costs = b.bargainPrice;
                        a.offer = b.percentage;
                        darkMatter = b.resources.dm;
                        a.updateBargain(a);
                        a.updateResources(a);
                        traderObj.reloadResources(function () {
                            a.checkMoney(a);
                            Tipped.show($("#js_scrapBargain")[0])
                        })
                    }
                    $(".scrap_trader_quote").text(b.quote)
                },
                error: function () {
                    a.unlock(a)
                }
            })
        },
        trade: function (a) {
            a.lock(a);
            var b = a.getTradeArray();
            var c = function c() {
                var k = loca.breakerQuestion + '<br/><br/><div style="text-align: left; margin-left: 30px">';
                var l = 0;
                $.each(b, function (g) {
                    k += this + "x " + loca.shipNames[g] + ", ";
                    l++;
                    if (l % 2 == 0) {
                        k += "<br/>"
                    }
                });
                k = k.replace(/, (<br\/>)?$/, "");
                k += "</div>";
                return k
            };
            errorBoxDecision(loca.breaker, c(), LocalizationStrings.yes, LocalizationStrings.no, function () {
                $.ajax({
                    url: breakerCallLink,
                    type: "POST",
                    dataType: "json",
                    data: {
                        lastTechId: a.lastTechId,
                        finishTrade: 1,
                        trade: b,
                        token: breakerToken
                    },
                    success: function (h) {
                        a.unlock(a);
                        breakerToken = h.newToken;
                        if (h.error) {
                            fadeBox(h.message, true)
                        } else {
                            fadeBox(h.message, false);
                            a.offer = h.percentage;
                            a.costs = h.bargainPrice;
                            a.resetForm();
                            a.updateBargain(a);
                            $("#js_scrapAmountMetal").html(0);
                            $("#js_scrapAmountCrystal").html(0);
                            $("#js_scrapAmountDeuterium").html(0);
                            traderObj.reloadResources(function () {
                                a.updateShips(a)
                            })
                        }
                        $(".scrap_trader_quote").text(h.quote)
                    },
                    error: function () {
                        a.unlock(a);
                        fadeBox(loca.error, true)
                    }
                })
            }, function () {
                a.unlock(a)
            })
        },
        updateResources: function (b, a) {
            if (b.locked) {
                return
            }
            $.ajax({
                url: breakerCallLink,
                type: "POST",
                dataType: "json",
                data: {
                    lastTechId: b.lastTechId,
                    trade: b.getTradeArray(),
                    token: breakerToken
                },
                beforeSend: function () {
                    b.lock(b)
                },
                success: function (c) {
                    breakerToken = c.newToken;
                    if (c.error) {
                        fadeBox(c.message, true)
                    }
                    b.locked = false;
                    var k = false;
                    for (var l in c.techAmount) {
                        $("#ship_" + l).val(tsdpkt(c.techAmount[l]));
                        if (!k && $("#ship_" + l).val() != b.ships[l]) {
                            k = true
                        }
                    }
                    $("#js_scrapAmountMetal").html(tsdpkt(c.resources.metal));
                    $("#js_scrapAmountCrystal").html(tsdpkt(c.resources.crystal));
                    $("#js_scrapAmountDeuterium").html(tsdpkt(c.resources.deuterium));
                    if (!b.notFirstOffer) {
                        $(".scrap_trader_quote").text(loca.breakerFirstOffer);
                        b.notFirstOffer = true
                    }
                    if (k) {
                        b.updateShips(b)
                    } else {
                        b.unlock(b)
                    }
                    if (typeof(a) == "function") {
                        a(c)
                    }
                },
                error: function () {
                    b.unlock(b)
                }
            })
        },
        updateShips: function (a) {
            $.ajax({
                url: techUrl,
                type: "POST",
                dataType: "json",
                beforeSend: function () {
                    a.lock(a)
                },
                success: function (b) {
                    $("#div_traderScrap .item").each(function () {
                        var n = $(this).attr("id").substr(6, 3);
                        if (typeof(b[n]) != "undefined") {
                            var m = 0;
                            if (b[n] != null) {
                                m = getValue(b[n])
                            }
                            a.ships[n] = m;
                            var c = $(this).find(".level");
                            c.contents().filter(function () {
                                return this.nodeType == 3
                            }).remove();
                            c.append(tsdpkt(m));
                            if (b[n] != null) {
                                var l = $("#button" + n);
                                l.removeClass("on").removeClass("off");
                                if (m > 0) {
                                    l.addClass("on")
                                } else {
                                    l.addClass("off")
                                }
                            }
                        }
                    });
                    a.unlock(a)
                },
                error: function () {
                    a.unlock(a)
                }
            })
        },
        getTradeArray: function () {
            var a = {};
            $("input.ship_amount").each(function () {
                var b = $(this).attr("name").substr(2, 3);
                if (getValue($(this).val()) != 0) {
                    a[b] = getValue($(this).val())
                }
            });
            return a
        },
        resetForm: function () {
            $("input.ship_amount").each(function () {
                $(this).val("0")
            });
            removeTooltip($("#js_scrapBargain"));
            $("#js_scrapBargain").removeClass("tooltip").removeAttr("title")
        },
        checkMoney: function (a) {
            if (darkMatter < a.costs) {
                $("#js_scrapBargain").addClass("disabled")
            } else {
                if (breakerMaximumPercent <= a.offer) {
                    $("#js_scrapBargain").addClass("disabled").addClass("tooltip").attr("title", loca.infoMaxBargain);
                    initTooltips($("#js_scrapBargain"))
                } else {
                    $("#js_scrapBargain").removeClass("disabled")
                }
            }
        },
        checkShips: function (a) {
            var b = false;
            $("input.ship_amount").each(function () {
                if ($(this).val().length > 0 && getValue($(this).val()) > 0) {
                    b = true
                }
            });
            if (!b) {
                $("#js_scrapScrapIT").addClass("disabled")
            } else {
                $("#js_scrapScrapIT").removeClass("disabled")
            }
        },
        updateBargain: function (a) {
            $(".scrap_offer_amount").css("height", a.offer / 100 * $(".scrap_offer_amount").parent().css("height").replace("px", ""));
            $(".scrap_offer_amount").html(a.offer + "%");
            $(".js_bargainCost").text(tsdpkt(a.costs))
        },
        lock: function (a) {
            $("#js_scrapBargain").addClass("disabled");
            $("#js_scrapScrapIT").addClass("disabled");
            a.locked = true
        },
        unlock: function (a) {
            a.locked = false;
            a.checkShips(a);
            a.checkMoney(a)
        }
    };
    auctioneer = {
        socket: null,
        connected: false,
        timeout: null,
        retryInterval: 5000,
        historyShown: false,
        initConnection: function () {
            try {
                var a = auctioneer;
                this.socket = new io.connect("/auctioneer", nodeParams);
                console.log('auctioneer ioConnect()');
                this.socket.on("connect", function () {
                    a.connected = true;
                    clearTimeout(this.timeout)
                });
                this.socket.on("disconnect", function () {
                    a.connected = false;
                    a.retryConnection()
                });
                this.socket.on("new auction", function (n) {
                    auctionId = n.auctionId;
                    var o = $("#div_traderAuctioneer .detail_value.currentPlayer").html();
                    if (n.oldAuction.player == null) {
                        o = loca.auctionNotSold
                    } else {
                        o = '<a href="' + n.oldAuction.player.link + '">' + n.oldAuction.player.name + "</a>"
                    }
                    removeTooltip($("#div_traderAuctioneer .image_140px .detail_button"));
                    var c = $("#div_traderAuctioneer .image_140px .detail_button").attr("title");
                    var p = $(".auction_history li:first").hasClass("even") ? "odd" : "even";
                    var q = '                        <li class="' + p + '" style="display: none">                            <a href="javascript:void(0);"                               class="slideIn"                               ref="' + n.oldAuction.item.uuid + '">                                <img height="30" width="30"                                     src="/img/icons/items/' + n.oldAuction.item.imageSmall + '-small.png"                                     alt="" title="' + c + '"                                     class="item_img tooltipHTML tooltipLeft r_' + n.oldAuction.item.rarity + '"/>                            </a>                            <span class="detail sum">' + number_format(n.oldAuction.sum, 0) + '</span>                            <span class="detail player">' + o + '</span>                            <span class="detail date_time">' + n.oldAuction.time + "</span>                        </li>";
                    $(".auction_history .history_content ul").prepend(q);
                    $(".auction_history .history_content li:first").slideDown("slow");
                    var r = $("#div_traderAuctioneer .auction_history li").length;
                    if (r > 3) {
                        $(".auction_history .history_content li:last").slideUp("slow", function () {
                            $(".auction_history .history_content li:eq(21)").remove();
                            var g = $(".auction_history .history_content li:eq(3)");
                            g.addClass("more_auctions_li");
                            if (auctioneer.historyShown) {
                                g.show()
                            }
                        });
                        $("#div_traderAuctioneer .auction_history .more").show()
                    }
                    $("#div_traderAuctioneer .image_140px .detail_button").attr("ref", n.item.uuid).attr("title", "").removeClass("r_common_140px").removeClass("r_uncommon_140px").removeClass("r_rare_140px").removeClass("r_epic_140px").addClass("r_" + n.item.rarity + "_140px");
                    $("#div_traderAuctioneer .image_140px img").attr("src", "/img/icons/items/" + n.item.image + "-140x.png");
                    $("#div_traderAuctioneer .left_header h2").html(loca.auctionRunning);
                    a.setItemTooltip($("#div_traderAuctioneer .image_140px .detail_button"));
                    a.setData({
                        price: 1000,
                        sum: 0,
                        player: null,
                        bids: 0,
                        info: n.info
                    });
                    $("#div_traderAuctioneer .js_alreadyBidden").html(number_format(0, 0));
                    $(".noAuctionOverlay").hide();
                    traderObj.resetValues("#div_traderAuctioneer", false);
                    traderObj.checkOverbidden()
                });
                this.socket.on("new bid", function (c) {
                    if (c.player.id == playerId) {
                        playerBid = c.sum;
                        AuctionIdOflastPlayerBid = c.auctionId;
                        $("#div_traderAuctioneer .js_alreadyBidden").html(number_format(Math.floor(playerBid), 0))
                    }
                    a.setData({
                        price: c.price,
                        sum: c.sum,
                        player: c.player,
                        bids: c.bids
                    });
                    traderObj.checkOverbidden()
                });
                this.socket.on("auction finished", function (c) {
                    a.setData({
                        price: 0,
                        player: c.player,
                        bids: c.bids,
                        info: c.info
                    });
                    traderObj.resetValues("#div_traderAuctioneer", false);
                    $("#div_traderAuctioneer .js_alreadyBidden").html(number_format(0, 0));
                    $("#div_traderAuctioneer .js_auctioneerSum").html("");
                    $("#div_traderAuctioneer .left_header h2").html(loca.auctionFinished);
                    if (c.player != null) {
                        if (c.player.id == playerId) {
                            a.setItemTooltip($("#div_traderAuctioneer .image_140px .detail_button"))
                        }
                    }
                    $(".noAuctionOverlay").show();
                    traderObj.checkOverbidden()
                });
                this.socket.on("timeLeft", function (c) {
                    a.setData({info: c})
                })
            } catch (b) {
            }
        },
        setItemTooltip: function (a) {
            $.ajax({
                url: detailUrl,
                data: {getDetails: 1, type: $(a).attr("ref"), ajax: 1},
                dataType: "json",
                success: function (b) {
                    changeTooltip(a, b.title);
                    $("#itemDetails[data-uuid='" + $(a).attr("ref") + "'] .amount,a.detail_button[ref='" + $(a).attr("ref") + "'] .amount").html(tsdpkt(b.amount))
                },
                error: function () {
                    fadeBox(loca.error, true)
                }
            })
        },
        initialize: function () {
            traderObj.initSliderTrader("#div_traderAuctioneer");
            traderObj.planets = planetResources;
            traderObj.price = getValue($(".js_price").html());
            $("#div_traderAuctioneer .right_box .pay").bind("click", function () {
                traderObj.submitAuction()
            });
            $("#div_traderAuctioneer .auction_history .more").bind("click", function () {
                if (auctioneer.historyShown) {
                    $(this).text("[" + loca.auctionShowMore + "]")
                } else {
                    $(this).text("[" + loca.auctionShowLess + "]")
                }
                auctioneer.historyShown = !auctioneer.historyShown;
                $("#div_traderAuctioneer .auction_history .more_auctions_li").slideToggle("slow")
            });
            traderObj.sumAuctioneer();
            traderObj.checkOverbidden();
            this.initCountdown();
            loadScript(nodeUrl, this.initConnection)
        },
        retryConnection: function () {
            var a = this;
            setTimeout(function () {
                a.initConnection()
            }, 5000)
        },
        setData: function (a) {
            var b = false;
            if (typeof(a.player) != "undefined") {
                if (a.player == null) {
                    $("#div_traderAuctioneer .detail_value.currentPlayer").text("");
                    $("#div_traderAuctioneer .detail_value.currentPlayer").attr("href", "")
                } else {
                    $("#div_traderAuctioneer .detail_value.currentPlayer").text(a.player.name);
                    $("#div_traderAuctioneer .detail_value.currentPlayer").attr("href", a.player.link);
                    $("#div_traderAuctioneer .detail_value.currentPlayer").attr("data-player-id", a.player.id);
                    $("#div_traderAuctioneer .detail_value.currentPlayer").data("playerId", a.player.id)
                }
                b = true
            }
            if (typeof(a.price) !== "undefined") {
                traderObj.price = a.price;
                $("#div_traderAuctioneer .js_price").html(number_format(Math.floor(a.price), 0));
                b = true
            }
            if (typeof(a.sum) !== "undefined") {
                $("#div_traderAuctioneer .detail_value.currentSum").html(number_format(Math.floor(a.sum), 0));
                b = true
            }
            if (typeof(a.bids) !== "undefined") {
                $("#div_traderAuctioneer .detail_value.numberOfBids").html(number_format(a.bids, 0));
                b = true
            }
            if (typeof(a.info) !== "undefined" && $.trim($("#div_traderAuctioneer .auction_info").html()) != a.info) {
                $("#div_traderAuctioneer .auction_info").html(a.info);
                this.initCountdown();
                b = true
            }
            if (b) {
                this.flash();
                traderObj.sumAuctioneer()
            }
        },
        initCountdown: function () {
            if (typeof(this.nextAuctionTimer) == "object") {
                timerHandler.removeCallback(this.nextAuctionTimer.timer)
            }
            if ($(".nextAuction").length > 0) {
                this.nextAuctionTimer = new simpleCountdown($(".nextAuction").get(0), $(".nextAuction").text())
            }
        },
        flash: function () {
            if (traderObj.traderId == "#div_traderAuctioneer") {
                $("#div_traderAuctioneer .overlay").fadeIn("normal", function () {
                    $(this).fadeOut("normal")
                })
            }
        },
        calculateDeficit: function () {
            var a = 0;
            if (Math.floor(traderObj.price) == 0) {
                a = 0
            } else {
                if (auctionId != AuctionIdOflastPlayerBid) {
                    a = Math.floor(traderObj.price)
                } else {
                    a = Math.floor(traderObj.price) - Math.floor(playerBid)
                }
            }
            return Math.floor(a)
        }
    };
    $(".js_trader").hover(function () {
        var a = $(this).attr("id").replace("js_trader", "").toLowerCase();
        $(this).addClass(a + "_link_hover")
    }, function () {
        var a = $(this).attr("id").replace("js_trader", "").toLowerCase();
        $(".trader_link").each(function (c, b) {
            $(this).removeClass(a + "_link_hover")
        })
    });
    $(".right_box .pay, .value-control, .ui-slider-handle, .bargain, .scrap_it, .source").hover(function () {
        $(this).addClass("hover")
    }, function () {
        $(this).removeClass("hover")
    });
    traderObj.init();
    $(document).undelegate(".js_trader", "click").delegate(".js_trader", "click", function () {
        var a = $(this).attr("id").replace("js_", "");
        traderObj.switchTrader(a)
    }).undelegate("#planet .js_backToOverview", "click").delegate("#planet .js_backToOverview", "click", function () {
        $.bbq.pushState({page: "", animation: ""});
        $(".planetlink, .moonlink").fragment({page: "", animation: ""})
    });
    var e = $.deparam.fragment();
    if (typeof(e.page) != "undefined" && e.page != "") {
        traderObj.selectTrader(e.page, undefined, e.tab)
    }
};