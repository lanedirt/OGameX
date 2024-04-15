inventoryObj = {
    currentPage: null,
    currentItems: null,
    currentItem: null,
    currentCategory: null,
    initalizeSlider: function (R, N, P, U, O, al, ac, ai, S) {
        if (inventoryObj.currentItems == R && typeof(al) == "undefined" || al == false) {
            return
        }
        inventoryObj.currentItems = R;
        O = O || "slideIn";
        ac = ac || "tooltipHTML js_hideTipOnMobile";
        if (typeof(ai) == "undefined") {
            ai = true
        }
        if (typeof(S) == "undefined") {
            S = true
        }
        $("#" + N + "Box").remove(".anythingSlider");
        var ag = [];
        var af = 0;
        for (var M in R) {
            var W = R[M];
            if (typeof(W.hide) != "undefined" && W.hide) {
                continue
            }
            if (inventoryObj.currentPage == "shop" || inventoryObj.currentPage == "inventory") {
                var T = af % inventoryObj.itemsPerSlide;
                ag[af + (2 * (T % 3)) - 2 * Math.floor(T / 3)] = W
            } else {
                ag[af] = W
            }
            af++
        }
        var Q = 0, I = 0, L = $('<ul id="' + N + '" />');
        for (var ak = ag.length; I < ak; I++) {
            if (typeof(ag[I]) == "undefined") {
                K.append('<div class="item_img"><div class="empty border5px"></div></div>');
                continue
            }
            var W = ag[I];
            if (I % inventoryObj.itemsPerSlide == 0) {
                var K = $('<li class="slide_' + Q + '" />').appendTo(L);
                Q++
            }
            var aj, V, Z;
            if (inventoryObj.currentPage == "shop") {
                aj = getNumberFormatShort(W.costs, null) + " " + loca.currency[W.currency];
                V = "price"
            } else {
                aj = getNumberFormatShort(W.amount);
                V = "amount"
            }
            var Y;
            if (ai) {
                Y = W.imageLarge + "-75x.png"
            } else {
                Y = W.imageLarge + "-100x.png"
            }
            var X;
            if (W.canBeActivated || W.canBeBoughtAndActivated) {
                X = "enabled"
            } else {
                X = "disabled"
            }
            if (W.isReduced) {
                Z = '<div class="sale_badge ' + X + '"></div>'
            } else {
                Z = ""
            }
            var ad = (W.timeLeft != null) ? " js_is_active " : "";
            var ae = "";
            var aa = W.title;
            if (N.indexOf("js_activeItemSlider") != -1) {
                aa = "";
                ae = (W.timeLeft != null) ? '<span class="js_duration undermark" data-total-duration="' + W.totalTime + '">' + W.timeLeft + "</span>" : ""
            }
            var ab = "";
            if (W.timeLeft != null && N.indexOf("js_activeItemSlider") != -1) {
                ab = '<div class="pusher"></div>'
            }
            var ah = "";
            if ($.inArray(birthdayCategory, W.category) != -1) {
                ah = '<div class="event_active_hint"></div>'
            }
            K.append('<div class="item_img r_' + W.rarity + '" style="background-image: url(/img/icons/items/' + Y + ');"><div class="item_img_box">' + ah + '<div class="activation ' + X + ad + '"></div><a href="javascript:void(0);" tabindex="1" title="' + aa + '" class="detail_button ' + ac + " " + O + '" ref="' + W.ref + '">' + Z + '<span class="ecke"><span class="level ' + V + '">' + aj + "</span></span></a></div>" + ae + ab + "</div>")
        }
        $("#" + N + "Box").prepend(L);
        if (I % inventoryObj.itemsPerSlide != 0) {
            for (var J = I % inventoryObj.itemsPerSlide; J < inventoryObj.itemsPerSlide; J++) {
                $("#" + N + " li:last").append('<div class="item_img"><div class="empty border5px"></div></div>')
            }
        }
        return mySlider = $("#" + N).anythingSlider({
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
            hashTags: true,
            buildNavigation: S,
            onInitialized: function (e, k) {
                if (isMobile) {
                    var h = 1000, f = 50, d = 0, b = 0, g = "ontouchend" in document, a = (g) ? "touchstart" : "mousedown", c = (g) ? "touchmove" : "mousemove", l = (g) ? "touchend" : "mouseup";
                    k.$window.bind(a, function (m) {
                        b = (new Date()).getTime();
                        d = m.originalEvent.touches ? m.originalEvent.touches[0].pageX : m.pageX
                    }).bind(l, function (m) {
                        b = 0;
                        d = 0
                    }).bind(c, function (n) {
                        var m = n.originalEvent.touches ? n.originalEvent.touches[0].pageX : n.pageX, o = (d === 0) ? 0 : Math.abs(m - d), p = (new Date()).getTime();
                        if (b !== 0 && p - b < h && o > f) {
                            if (m < d) {
                                k.goForward()
                            }
                            if (m > d) {
                                k.goBack()
                            }
                            b = 0;
                            d = 0
                        }
                    })
                }
            }
        })
    },
    initShop: function () {
        var b = this;
        $(window).unbind(".shop");
        $(document).undelegate(".slideIn", "click.shop").delegate(".slideIn", "click.shop", function () {
            if (b.currentItem == $(this).attr("ref")) {
                b.currentItem = null;
                $.bbq.pushState({item: ""})
            } else {
                b.currentItem = $(this).attr("ref");
                $.bbq.pushState({item: $(this).attr("ref")})
            }
        });
        $("button.to_shop").bind("click.shop", function () {
            $.bbq.pushState({page: "shop"})
        });
        $("button.to_inventory").bind("click.shop", function () {
            $.bbq.pushState({page: "inventory"})
        });
        $("button.buyResourcesLink").bind("click", function () {
            reload_page($(this).data("link"))
        });
        $(".to_shop, .to_inventory").hover(function () {
            $(this).addClass("hover")
        }, function () {
            $(this).removeClass("hover")
        });
        $(".categoryFilter li a").bind("click.shop", function () {
            $.bbq.pushState({category: $(this).attr("rel")})
        });
        $(window).unbind("hashchange.shop").bind("hashchange.shop", function (a) {
            b.onHashChange($.deparam.fragment(a.fragment))
        });
        b.onHashChange($.deparam.fragment());
        inventoryObj.refreshResources()
    },
    onHashChange: function (l) {
        if (typeof(l.page) == "undefined") {
            var h = {
                page: "shop",
                category: $(".categoryFilter a:first").attr("rel")
            };
            if (typeof(l.item) != "undefined" && l.item != "") {
                var k = inventoryObj.items_shop[l.item];
                if (k.category.length > 0) {
                    h.category = k.category[k.category.length - 1]
                }
            }
            $.bbq.pushState(h);
            return
        }
        var f = this.currentPage != l.page;
        if (f) {
            if (l.page == "inventory") {
                this.openInventory();
                $(".planetlink, .moonlink").fragment({page: l.inventory})
            } else {
                this.openShop();
                $(".planetlink, .moonlink").fragment({page: l.shop})
            }
            this.updateCategoryAmount()
        }
        if (typeof(l.category) == "undefined") {
            $.bbq.pushState({category: $(".categoryFilter a:first").attr("rel")});
            return
        } else {
            if (l.category != this.currentCategory || f) {
                this.changeCategory(l.category)
            }
        }
        if (typeof(l.item) == "undefined" || l.item == "" && this.currentItem != null) {
            $("#itemDetails a.close_details").click();
            $(".planetlink, .moonlink").fragment({item: ""})
        } else {
            if (this.currentItem != l.item) {
                var g = $(".slideIn[ref='" + l.item + "']");
                if (g.length) {
                    g.click()
                } else {
                    gfSlider.slideIn(getElementByIdWithCache("detail"), l.item)
                }
                $(".planetlink, .moonlink").fragment({item: l.item})
            }
        }
    },
    initShopDetails: function () {
        var c = this;
        var d = $.deparam.querystring().page;
        $(document).undelegate("#itemDetails .close_details", "click").delegate("#itemDetails .close_details", "click", function () {
            $("a.slideIn[ref=" + $(this).attr("ref") + "]:first").click()
        }).undelegate("#itemDetails a.item.build-it", "click").delegate("#itemDetails a.item.build-it", "click", function () {
            $.ajax({
                url: $(this).attr("rel"),
                data: {ajax: 1, token: buyToken},
                type: "POST",
                dataType: "json",
                error: function () {
                    fadeBox(translation.buyError, true)
                },
                success: function (a) {
                    buyToken = a.newToken;
                    if (a.error) {
                        fadeBox(a.message, true)
                    } else {
                        fadeBox(a.message, false);
                        inventoryObj.refreshResources();
                        inventoryObj.refreshItemData(a.item)
                    }
                }
            });
            return false
        }).undelegate("#itemDetails a.item.build-it_disabled.dm", "click").delegate("#itemDetails a.item.build-it_disabled.dm", "click", function () {
            errorBoxDecision(LocalizationStrings.error, loca.buyDMDecision, LocalizationStrings.yes, LocalizationStrings.no, function () {
                if ($("a.dm_button").length > 0) {
                    $("a.dm_button").click()
                } else {
                    window.location.href = $("#darkmatter_box a").attr("href")
                }
            })
        }).undelegate("#itemDetails a.activateItem.build-it", "click").delegate("#itemDetails a.activateItem.build-it", "click", function () {
            var a = $(this);

            function b() {
                $.ajax({
                    url: a.attr("rel"),
                    data: {ajax: 1, token: activateToken, referrerPage: d},
                    type: "POST",
                    dataType: "json",
                    error: function () {
                        fadeBox(translation.buyError, true);
                        $("#itemDetails a.activateItem").removeClass("build-it").addClass("build-it_disabled")
                    },
                    success: function (f) {
                        activateToken = f.newToken;
                        if (f.error) {
                            fadeBox(f.message, true);
                            $("#itemDetails a.activateItem").removeClass("build-it").addClass("build-it_disabled")
                        } else {
                            if (f.message.reload) {
                                location.href = getRedirectLink();
                                return
                            }
                            f = f.message;
                            fadeBox(f.message, false);
                            inventoryObj.refreshResources();
                            inventoryObj.refreshItemData(f.item)
                        }
                    }
                })
            }

            if (a.hasClass("isUpgrade")) {
                errorBoxDecision(LocalizationStrings.activateItem.upgradeItemQuestionHeader, LocalizationStrings.activateItem.upgradeItemQuestion, LocalizationStrings.yes, LocalizationStrings.no, b)
            } else {
                b()
            }
            return false
        }).undelegate("#itemDetails a.buyAndActivate.build-it", "click").delegate("#itemDetails a.buyAndActivate.build-it", "click", function () {
            var a = $(this);

            function b() {
                $.ajax({
                    url: a.attr("rel"),
                    data: {ajax: 1, token: activateToken, referrerPage: d},
                    type: "POST",
                    dataType: "json",
                    error: function () {
                        fadeBox(translation.buyError, true);
                        $("#itemDetails a.activateItem").removeClass("build-it").addClass("build-it_disabled")
                    },
                    success: function (f) {
                        activateToken = f.newToken;
                        if (f.error) {
                            fadeBox(f.message, true);
                            $("#itemDetails a.activateItem").removeClass("build-it").addClass("build-it_disabled")
                        } else {
                            if (f.message.reload) {
                                location.href = getRedirectLink();
                                return
                            }
                            f = f.message;
                            fadeBox(f.message, false);
                            inventoryObj.refreshResources();
                            inventoryObj.refreshItemData(f.item)
                        }
                    }
                })
            }

            if (a.hasClass("isUpgrade")) {
                errorBoxDecision(LocalizationStrings.activateItem.upgradeItemQuestionHeader, LocalizationStrings.activateItem.upgradeItemQuestion, LocalizationStrings.yes, LocalizationStrings.no, b)
            } else {
                b()
            }
            return false
        })
    },
    refreshResources: function () {
        getAjaxResourcebox(function (b) {
            $(".to_dark_matter .level").text(b.darkmatter.resources["actualFormat"])
        })
    },
    refreshItemData: function (e) {
        var d = e.ref;
        changeTooltip($(".detail_button[ref='" + d + "']"), e.title);
        $(".detail_button[ref='" + d + "'] span.amount, #itemDetails[data-uuid='" + d + "'] span.amount").html(tsdpkt(e.amount));
        if (typeof(inventoryObj.items_inventory) != "undefined") {
            if (inventoryObj.items_inventory.length == 0) {
                inventoryObj.items_inventory = {}
            } else {
                if (e.amount <= 0) {
                    delete inventoryObj.items_inventory[d]
                } else {
                    inventoryObj.items_inventory[d] = e
                }
            }
        }
        if (typeof(inventoryObj.items_shop) != "undefined") {
            if (inventoryObj.items_shop.length == 0) {
                inventoryObj.items_shop = {}
            }
            inventoryObj.items_shop[d] = e
        }
        changeTooltip($('#itemDetails[data-uuid="' + d + '"] a.activateItem, #itemDetails[data-uuid="' + d + '"] a.buyAndActivate'), e.activationTitle);
        if (e.hasEnoughCurrency) {
            $('#itemDetails[data-uuid="' + d + '"] a.item').addClass("build-it").removeClass("build-it_disabled")
        } else {
            $('#itemDetails[data-uuid="' + d + '"] a.item').removeClass("build-it").addClass("build-it_disabled")
        }
        if (e.amount > 0) {
            $('#itemDetails[data-uuid="' + d + '"] a.activateItem').show();
            $('#itemDetails[data-uuid="' + d + '"] a.buyAndActivate').hide();
            if (e.canBeActivated) {
                $('#itemDetails[data-uuid="' + d + '"] a.activateItem').removeClass("build-it_disabled").addClass("build-it")
            } else {
                $('#itemDetails[data-uuid="' + d + '"] a.activateItem').addClass("build-it_disabled").removeClass("build-it")
            }
        } else {
            $('#itemDetails[data-uuid="' + d + '"] a.activateItem').hide();
            $('#itemDetails[data-uuid="' + d + '"] a.buyAndActivate').show();
            if (e.canBeBoughtAndActivated && e.hasEnoughCurrency) {
                $('#itemDetails[data-uuid="' + d + '"] a.buyAndActivate').removeClass("build-it_disabled").addClass("build-it")
            } else {
                $('#itemDetails[data-uuid="' + d + '"] a.buyAndActivate').addClass("build-it_disabled").removeClass("build-it")
            }
        }
        if (isMobile) {
            var f = "";
            if ($('#itemDetails[data-uuid="' + d + '"] a.activateItem:visible,#itemDetails[data-uuid="' + d + '"] a.buyAndActivate:visible').hasClass("build-it_disabled")) {
                f += e.activationTitle
            }
            if (e.buyTitle.length && e.buyTitle != e.activationTitle) {
                f += e.buyTitle
            }
            $('#itemDetails[data-uuid="' + d + '"] .info_txt').text(f)
        }
        if (e.timeLeft > 0 && e.extendable) {
            $('#itemDetails[data-uuid="' + d + '"] a.activateItem span').html(loca.extend);
            $('#itemDetails[data-uuid="' + d + '"] a.buyAndActivate span').html(loca.buyAndExtend)
        } else {
            $('#itemDetails[data-uuid="' + d + '"] a.activateItem span').html(loca.activate);
            $('#itemDetails[data-uuid="' + d + '"] a.buyAndActivate span').html(loca.buyAndActivate)
        }
        if (e.isAnUpgrade) {
            $('#itemDetails[data-uuid="' + d + '"] a.activateItem, #itemDetails[data-uuid="' + d + '"] a.buyAndActivate').addClass("isUpgrade")
        } else {
            $('#itemDetails[data-uuid="' + d + '"] a.activateItem, #itemDetails[data-uuid="' + d + '"] a.buyAndActivate').removeClass("isUpgrade")
        }
        if (this.inShop === true) {
            this.changeCategory($(".categoryFilter a.active").attr("rel"))
        }
        this.updateCategoryAmount()
    },
    boughtItemHint: function () {
        $(".to_inventory .bought_item_notice").show().fadeOut(1000)
    },
    openShop: function () {
        this.currentPage = "shop";
        $("#js_inventorySliderBox").hide();
        $("#js_shopSliderBox").show();
        $(".to_inventory").removeClass("active");
        $(".to_shop").addClass("active");
        $("#buttonz h2").text(loca.LOCA_PREMIUM_SHOP);
        if (isMobile) {
            $(".js_shopCurrentPage").html(loca.shopText)
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
            $(".js_shopCurrentPage").html(loca.inventoryText)
        }
    },
    changeCategory: function (c) {
        inventoryObj.currentCategory = c;
        $(".planetlink, .moonlink").fragment({category: c});
        $(".categoryFilter li, .categoryFilter li a").removeClass("active");
        $('.categoryFilter li a[rel="' + c + '"]').addClass("active").parent().addClass("active");
        $(".anythingSlider").remove();
        var d = function (m, a) {
            var n = [];
            var l = [];
            var b = 0;
            $.each(m, function (f) {
                if (this.category != null) {
                    var e = "$" + this.category.join("$") + "$";
                    if (e.toLowerCase().indexOf("$" + c + "$") != -1) {
                        n[inventoryObj.item_orders[c][this.ref]] = this;
                        if (inventoryObj.item_orders[c][this.ref] > b) {
                            b = inventoryObj.item_orders[c][this.ref]
                        }
                    }
                }
            });
            for (var k = 0; k <= b; ++k) {
                if (n[k]) {
                    l[k] = n[k]
                }
            }
            inventoryObj.initalizeSlider(l, a, 340, 340, null, null, null, false)
        };
        if (inventoryObj.currentPage == "shop") {
            d(inventoryObj.items_shop, "js_shopSlider")
        } else {
            if (inventoryObj.currentPage == "inventory") {
                d(inventoryObj.items_inventory, "js_inventorySlider")
            }
        }
    },
    updateCategoryAmount: function () {
        var d;
        if (inventoryObj.currentPage == "shop") {
            d = inventoryObj.items_shop
        } else {
            if (inventoryObj.currentPage == "inventory") {
                d = inventoryObj.items_inventory
            } else {
                return
            }
        }
        var c = $(".categoryFilter");
        c.find(".amount").text(0);
        $.each(d, function (l) {
            if (this.category != null) {
                for (var a in this.category) {
                    var b = this.category[a];
                    var h = c.find('a[rel="' + b + '"] .amount');
                    var k;
                    if (inventoryObj.currentPage == "shop") {
                        k = 1
                    } else {
                        if (inventoryObj.currentPage == "inventory") {
                            k = this.amount
                        }
                    }
                    h.text(tsdpkt(getValue(h.text()) + k))
                }
            }
        });
        $.each(c.find("li"), function (b) {
            var a = inventoryObj.currentPage.slice(0, 1).toUpperCase() + inventoryObj.currentPage.slice(1);
            if ($(this).hasClass("in" + a)) {
                $(this).show()
            } else {
                $(this).hide();
                if (!c.find("li:visible .active").length) {
                    c.find("li:visible:first a").click()
                }
            }
        })
    }
};