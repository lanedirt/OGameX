function hideTipsOnTabChange() {
    $("select").ogameDropDown("hide");
    Tipped.hideAll()
}
function changeTooltip(e, f) {
    var d = $(e);
    if (d.length == 0) {
        return
    }
    removeTooltip(d);
    d.attr("title", f);
    initTooltips(d)
}
function removeTooltip(d) {
    var c = $(d);
    c.each(function () {
        if ($(this).data("tooltipLoaded")) {
            $(this).data("tooltipLoaded", false);
            Tipped.remove($(this)[0])
        }
    })
}
function getTooltipOptions(d) {
    var f = $(d);
    var e = {
        skin: "cloud",
        maxWidth: 400,
        closeButton: false,
        hideOn: [{element: "self", event: "mouseleave"}, {
            element: "tooltip",
            event: "mouseenter"
        }]
    };
    if (window.location.href.indexOf("galaxy") !== -1) {
        e.maxWidth = 400
    }
    if (f.hasClass("tooltipPremium")) {
        e.skin = "premium"
    }
    if (f.hasClass("tooltipLeft")) {
        e.hook = {target: "leftmiddle", tooltip: "righttop"}
    } else {
        if (f.hasClass("tooltipRight")) {
            e.hook = {target: "rightmiddle", tooltip: "lefttop"}
        } else {
            if (f.hasClass("tooltipBottom")) {
                e.hook = {target: "bottommiddle", tooltip: "topmiddle"}
            }
        }
    }
    if (f.data("tooltip-width")) {
        e.maxWidth = f.data("tooltip-width")
    }
    if (isMobile || f.hasClass("tooltipClose")) {
        e.hideOthers = true;
        e.hideOn = false
    }
    e.afterUpdate = function (a, c) {
        if (isMobile && f.data("tooltip-button")) {
            var h = $(document.createElement("div")).addClass("tooltipButton");
            $(document.createElement("a")).addClass("btn_blue").attr("href", "javascript:void(0);").html(f.data("tooltip-button")).bind("click", function (g) {
                if ($(c).not("a") && $(c).find("a").length) {
                    c = $(c).find("a")[0]
                }
                var l = document.createEvent("MouseEvents");
                l.initMouseEvent("click", true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
                c.dispatchEvent(l)
            }).appendTo(h);
            $(a).append(h)
        }
        if (isMobile || f.hasClass("tooltipClose")) {
            var b = $(document.createElement("div")).addClass("close-tooltip");
            $(a).prepend(b)
        }
        Tipped.refresh(c)
    };
    return e
}
function getTooltipSelector(e) {
    var f = ".tooltipPremium, .tooltip, .tooltipRight, .tooltipLeft, .tooltipBottom, .tooltipClose, .tooltipHTML, .tooltipRel, .tooltipAJAX, .tooltipCustom, .markItUpButton a";
    if (typeof(e) == "undefined") {
        e = f
    } else {
        if (typeof(e) == "string" && !e.match(/\.tooltip/)) {
            var g = f.split(", ");
            var h = e;
            for (i in g) {
                e += ", " + h + " " + g[i]
            }
        }
    }
    return e
}
function sanitizeTooltip(b) {
    return b.replace(/<\s*script/g, "&lt;script")
}
function initTooltips(d) {
    initTooltipSkins();
    d = getTooltipSelector(d);
    function f(b) {
        var o = {};
        var a = b.split("|");
        var c = $(document.createElement("h1")).html(a[0]);
        var n = $(document.createElement("div")).addClass("splitLine");
        if (typeof a[2] !== "undefined" && typeof a[3] !== "undefined") {
            var m = $(document.createElement("h1")).html(a[2]);
            var p = $(document.createElement("div")).addClass("splitLine");
            o = $(document.createElement("div")).css("display", "none").addClass("htmlTooltip").append(c).append(n).append(a[1] + "</br>").append(m).append(p).append(a[3])
        } else {
            o = $(document.createElement("div")).css("display", "none").addClass("htmlTooltip").append(c).append(n).append(a[1])
        }
        return o[0]
    }

    removeTooltip(d);
    function e(b) {
        var a = $(b);
        if (a.data("tooltipLoaded")) {
            return
        }
        a.data("tooltipLoaded", true);
        if (isMobile && a.hasClass("js_hideTipOnMobile")) {
            a.attr("title", "");
            return
        }
        var c = getTooltipOptions(a);
        if (a.hasClass("tooltipCustom")) {
            if (c.hideOn != false) {
                c.hideOn = [{
                    element: "self",
                    event: "mouseleave"
                }, {element: "tooltip", event: "mouseleave"}]
            }
            c.afterUpdate = function (h) {
                $(h).find(".tooltipCustom").each(function (g, m) {
                    var n = getTooltipOptions(a);
                    if ($(this).hasClass("tooltipHTML")) {
                        n.inline = true;
                        n.hideOthers = false;
                        Tipped.create(this, f(sanitizeTooltip($(this).attr("title"))), n)
                    } else {
                        n.hideOthers = false;
                        Tipped.create(this, sanitizeTooltip($(this).attr("title")), n)
                    }
                })
            }
        }
        if (a.hasClass("tooltipHTML")) {
            if (typeof(a.attr("title")) == "undefined" || a.attr("title").trim().length == 0) {
                return
            }
            Tipped.create(a[0], f(sanitizeTooltip(a.attr("title"))), c);
            return
        }
        if (a.hasClass("tooltipRel")) {
            c.inline = true;
            Tipped.create(a[0], a.attr("rel"), c);
            return
        }
        if (a.hasClass("tooltipAJAX")) {
            $.get(a.attr("rel"), {}, function (h) {
                Tipped.create(a[0], h, c)
            });
            return
        }
        if (typeof(a.attr("title")) == "undefined" || a.attr("title").trim().length == 0) {
            return
        }
        Tipped.create(a[0], sanitizeTooltip(a.attr("title")), c)
    }

    $(document).undelegate(d, "touchstart.tooltipClick").delegate(d, "touchstart.tooltipClick", function (a) {
        if (Tipped.visible(this)) {
            var b = document.createEvent("MouseEvents");
            b.initMouseEvent("click", true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
            this.dispatchEvent(b);
            a.preventDefault();
            a.stopPropagation()
        }
    });
    if (typeof(d) == "string") {
        $(document).undelegate(d, "mouseenter.tooltipLoad touchstart.tooltipLoad").delegate(d, "mouseenter.tooltipLoad touchstart.tooltipLoad", function (a) {
            e(this)
        })
    } else {
        $(d).each(function () {
            e(this)
        })
    }
}
$(function () {
    initTooltips()
});
function initTooltipSkins() {
    jQuery.extend(Tipped.Skins, {
        cloud: {
            border: {
                size: 1,
                color: [{position: 0, color: "#44576b"}, {
                    position: 1,
                    color: "#2f3b47"
                }]
            },
            background: {
                color: [{position: 0, color: "#303a46"}, {
                    position: 0.49,
                    color: "#242d38"
                }, {position: 0.81, color: "#10181f"}, {
                    position: 1,
                    color: "#0d1014"
                }]
            },
            offset: {x: 0, y: -1, mouse: {x: -12, y: -12}},
            stem: {height: 6, width: 11, offset: {x: 5, y: 5}, spacing: 0}
        },
        premium: {
            border: {
                size: 1,
                color: [{position: 0, color: "#000"}, {
                    position: 1,
                    color: "#000"
                }]
            },
            background: {
                color: [{
                    position: 0,
                    color: "#a3e4f0"
                }, {position: 0.15, color: "#1cbad7"}, {
                    position: 1,
                    color: "#0f78b1"
                }]
            },
            offset: {x: 0, y: -1, mouse: {x: -12, y: -12}},
            stem: {height: 6, width: 11, offset: {x: 5, y: 5}, spacing: 0}
        }
    })
}