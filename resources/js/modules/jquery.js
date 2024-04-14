document.createElement("canvas").getContext || function () {
    function q() {
    }

    function n(D) {
        this.type_ = D, this.r1_ = this.y1_ = this.x1_ = this.r0_ = this.y0_ = this.x0_ = 0, this.colors_ = []
    }

    function l(E, F, D) {
        !A(F) || (E.m_ = F, D && (E.lineScale_ = ah(ag(F[0][0] * F[1][1] - F[0][1] * F[1][0]))))
    }

    function A(E) {
        var F = 0;
        for (; F < 3; F++) {
            var D = 0;
            for (; D < 2; D++) {
                if (!isFinite(E[F][D]) || isNaN(E[F][D])) {
                    return !1
                }
            }
        }
        return !0
    }

    function y(F, G, D, E) {
        F.currentPath_.push({
            type: "bezierCurveTo",
            cp1x: G.x,
            cp1y: G.y,
            cp2x: D.x,
            cp2y: D.y,
            x: E.x,
            y: E.y
        }), F.currentX_ = E.x, F.currentY_ = E.y
    }

    function w(D) {
        this.m_ = p(), this.mStack_ = [], this.aStack_ = [], this.currentPath_ = [], this.fillStyle = this.strokeStyle = "#000", this.lineWidth = 1, this.lineJoin = "miter", this.lineCap = "butt", this.miterLimit = a * 1, this.globalAlpha = 1, this.canvas = D;
        var E = D.ownerDocument.createElement("div");
        E.style.width = D.clientWidth + "px", E.style.height = D.clientHeight + "px", E.style.overflow = "hidden", E.style.position = "absolute", D.appendChild(E), this.element_ = E, this.lineScale_ = this.arcScaleY_ = this.arcScaleX_ = 1
    }

    function v(D) {
        switch (D) {
            case"butt":
                return "flat";
            case"round":
                return "round";
            case"square":
            default:
                return "square"
        }
    }

    function u(F) {
        var G, H = 1;
        F = String(F);
        if (F.substring(0, 3) == "rgb") {
            var I = F.indexOf("(", 3), J = F.indexOf(")", I + 1), D = F.substring(I + 1, J).split(",");
            G = "#";
            var E = 0;
            for (; E < 3; E++) {
                G += k[Number(D[E])]
            }
            D.length == 4 && F.substr(3, 1) == "a" && (H = D[3])
        } else {
            G = F
        }
        return {color: G, alpha: H}
    }

    function s(D, E) {
        E.fillStyle = D.fillStyle, E.lineCap = D.lineCap, E.lineJoin = D.lineJoin, E.lineWidth = D.lineWidth, E.miterLimit = D.miterLimit, E.shadowBlur = D.shadowBlur, E.shadowColor = D.shadowColor, E.shadowOffsetX = D.shadowOffsetX, E.shadowOffsetY = D.shadowOffsetY, E.strokeStyle = D.strokeStyle, E.globalAlpha = D.globalAlpha, E.arcScaleX_ = D.arcScaleX_, E.arcScaleY_ = D.arcScaleY_, E.lineScale_ = D.lineScale_
    }

    function r(F, G) {
        var H = p(), I = 0;
        for (; I < 3; I++) {
            var J = 0;
            for (; J < 3; J++) {
                var D = 0, E = 0;
                for (; E < 3; E++) {
                    D += F[I][E] * G[E][J]
                }
                H[I][J] = D
            }
        }
        return H
    }

    function p() {
        return [[1, 0, 0], [0, 1, 0], [0, 0, 1]]
    }

    function h(D) {
        var E = D.srcElement;
        E.firstChild && (E.firstChild.style.width = E.clientWidth + "px", E.firstChild.style.height = E.clientHeight + "px")
    }

    function g(D) {
        var E = D.srcElement;
        switch (D.propertyName) {
            case"width":
                E.style.width = E.attributes.width.nodeValue + "px", E.getContext().clearRect();
                break;
            case"height":
                E.style.height = E.attributes.height.nodeValue + "px", E.getContext().clearRect()
        }
    }

    function e(E, F) {
        var D = d.call(arguments, 2);
        return function () {
            return E.apply(F, D.concat(d.call(arguments)))
        }
    }

    function c() {
        return this.context_ || (this.context_ = new w(this))
    }

    var B = Math, C = B.round, ae = B.sin, af = B.cos, ag = B.abs, ah = B.sqrt, a = 10, b = a / 2, d = Array.prototype.slice, f = {
        init: function (D) {
            if (/MSIE/.test(navigator.userAgent) && !window.opera) {
                var E = D || document;
                E.createElement("canvas"), E.attachEvent("onreadystatechange", e(this.init_, this, E))
            }
        }, init_: function (F) {
            F.namespaces.g_vml_ || F.namespaces.add("g_vml_", "urn:schemas-microsoft-com:vml", "#default#VML"), F.namespaces.g_o_ || F.namespaces.add("g_o_", "urn:schemas-microsoft-com:office:office", "#default#VML");
            if (!F.styleSheets.ex_canvas_) {
                var G = F.createStyleSheet();
                G.owningElement.id = "ex_canvas_", G.cssText = "canvas{display:inline-block;overflow:hidden;text-align:left;width:300px;height:150px}g_vml_\\:*{behavior:url(#default#VML)}g_o_\\:*{behavior:url(#default#VML)}"
            }
            var D = F.getElementsByTagName("canvas"), E = 0;
            for (; E < D.length; E++) {
                this.initElement(D[E])
            }
        }, initElement: function (D) {
            if (!D.getContext) {
                D.getContext = c, D.innerHTML = "", D.attachEvent("onpropertychange", g), D.attachEvent("onresize", h);
                var E = D.attributes;
                E.width && E.width.specified ? D.style.width = E.width.nodeValue + "px" : D.width = D.clientWidth, E.height && E.height.specified ? D.style.height = E.height.nodeValue + "px" : D.height = D.clientHeight
            }
            return D
        }
    };
    f.init();
    var k = [], m = 0;
    for (; m < 16; m++) {
        var o = 0;
        for (; o < 16; o++) {
            k[m * 16 + o] = m.toString(16) + o.toString(16)
        }
    }
    var x = w.prototype;
    x.clearRect = function () {
        this.element_.innerHTML = ""
    }, x.beginPath = function () {
        this.currentPath_ = []
    }, x.moveTo = function (E, F) {
        var D = this.getCoords_(E, F);
        this.currentPath_.push({
            type: "moveTo",
            x: D.x,
            y: D.y
        }), this.currentX_ = D.x, this.currentY_ = D.y
    }, x.lineTo = function (E, F) {
        var D = this.getCoords_(E, F);
        this.currentPath_.push({
            type: "lineTo",
            x: D.x,
            y: D.y
        }), this.currentX_ = D.x, this.currentY_ = D.y
    }, x.bezierCurveTo = function (L, D, E, F, G, H) {
        var I = this.getCoords_(G, H), J = this.getCoords_(L, D), K = this.getCoords_(E, F);
        y(this, J, K, I)
    }, x.quadraticCurveTo = function (F, G, H, I) {
        var J = this.getCoords_(F, G), D = this.getCoords_(H, I), E = {
            x: this.currentX_ + 0.6666666666666666 * (J.x - this.currentX_),
            y: this.currentY_ + 0.6666666666666666 * (J.y - this.currentY_)
        };
        y(this, E, {
            x: E.x + (D.x - this.currentX_) / 3,
            y: E.y + (D.y - this.currentY_) / 3
        }, D)
    }, x.arc = function (L, D, E, F, K, M) {
        E *= a;
        var N = M ? "at" : "wa", O = L + af(F) * E - b, P = D + ae(F) * E - b, Q = L + af(K) * E - b, G = D + ae(K) * E - b;
        O == Q && !M && (O += 0.125);
        var H = this.getCoords_(L, D), I = this.getCoords_(O, P), J = this.getCoords_(Q, G);
        this.currentPath_.push({
            type: N,
            x: H.x,
            y: H.y,
            radius: E,
            xStart: I.x,
            yStart: I.y,
            xEnd: J.x,
            yEnd: J.y
        })
    }, x.rect = function (F, G, D, E) {
        this.moveTo(F, G), this.lineTo(F + D, G), this.lineTo(F + D, G + E), this.lineTo(F, G + E), this.closePath()
    }, x.strokeRect = function (G, H, D, E) {
        var F = this.currentPath_;
        this.beginPath(), this.moveTo(G, H), this.lineTo(G + D, H), this.lineTo(G + D, H + E), this.lineTo(G, H + E), this.closePath(), this.stroke(), this.currentPath_ = F
    }, x.fillRect = function (G, H, D, E) {
        var F = this.currentPath_;
        this.beginPath(), this.moveTo(G, H), this.lineTo(G + D, H), this.lineTo(G + D, H + E), this.lineTo(G, H + E), this.closePath(), this.fill(), this.currentPath_ = F
    }, x.createLinearGradient = function (G, H, D, E) {
        var F = new n("gradient");
        F.x0_ = G, F.y0_ = H, F.x1_ = D, F.y1_ = E;
        return F
    }, x.createRadialGradient = function (F, G, H, I, J, D) {
        var E = new n("gradientradial");
        E.x0_ = F, E.y0_ = G, E.r0_ = H, E.x1_ = I, E.y1_ = J, E.r1_ = D;
        return E
    }, x.drawImage = function (G) {
        var I, K, L, M, N, O, P, Q, R = G.runtimeStyle.width, T = G.runtimeStyle.height;
        G.runtimeStyle.width = "auto", G.runtimeStyle.height = "auto";
        var U = G.width, F = G.height;
        G.runtimeStyle.width = R, G.runtimeStyle.height = T;
        if (arguments.length == 3) {
            I = arguments[1], K = arguments[2], N = O = 0, P = L = U, Q = M = F
        } else {
            if (arguments.length == 5) {
                I = arguments[1], K = arguments[2], L = arguments[3], M = arguments[4], N = O = 0, P = U, Q = F
            } else {
                if (arguments.length == 9) {
                    N = arguments[1], O = arguments[2], P = arguments[3], Q = arguments[4], I = arguments[5], K = arguments[6], L = arguments[7], M = arguments[8]
                } else {
                    throw Error("Invalid number of arguments")
                }
            }
        }
        var H = this.getCoords_(I, K), J = [];
        J.push(" <g_vml_:group", ' coordsize="', a * 10, ",", a * 10, '"', ' coordorigin="0,0"', ' style="width:', 10, "px;height:", 10, "px;position:absolute;");
        if (this.m_[0][0] != 1 || this.m_[0][1]) {
            var D = [];
            D.push("M11=", this.m_[0][0], ",", "M12=", this.m_[1][0], ",", "M21=", this.m_[0][1], ",", "M22=", this.m_[1][1], ",", "Dx=", C(H.x / a), ",", "Dy=", C(H.y / a), "");
            var E = H, S = this.getCoords_(I + L, K), V = this.getCoords_(I, K + M), W = this.getCoords_(I + L, K + M);
            E.x = B.max(E.x, S.x, V.x, W.x), E.y = B.max(E.y, S.y, V.y, W.y), J.push("padding:0 ", C(E.x / a), "px ", C(E.y / a), "px 0;filter:progid:DXImageTransform.Microsoft.Matrix(", D.join(""), ", sizingmethod='clip');")
        } else {
            J.push("top:", C(H.y / a), "px;left:", C(H.x / a), "px;")
        }
        J.push(' ">', '<g_vml_:image src="', G.src, '"', ' style="width:', a * L, "px;", " height:", a * M, 'px;"', ' cropleft="', N / U, '"', ' croptop="', O / F, '"', ' cropright="', (U - N - P) / U, '"', ' cropbottom="', (F - O - Q) / F, '"', " />", "</g_vml_:group>"), this.element_.insertAdjacentHTML("BeforeEnd", J.join(""))
    }, x.stroke = function (K) {
        var M = [], O = u(K ? this.fillStyle : this.strokeStyle), Q = O.color, R = O.alpha * this.globalAlpha;
        M.push("<g_vml_:shape", ' filled="', !!K, '"', ' style="position:absolute;width:', 10, "px;height:", 10, 'px;"', ' coordorigin="0 0" coordsize="', a * 10, " ", a * 10, '"', ' stroked="', !K, '"', ' path="');
        var U = {x: null, y: null}, D = {x: null, y: null}, E = 0;
        for (; E < this.currentPath_.length; E++) {
            var F = this.currentPath_[E];
            switch (F.type) {
                case"moveTo":
                    M.push(" m ", C(F.x), ",", C(F.y));
                    break;
                case"lineTo":
                    M.push(" l ", C(F.x), ",", C(F.y));
                    break;
                case"close":
                    M.push(" x "), F = null;
                    break;
                case"bezierCurveTo":
                    M.push(" c ", C(F.cp1x), ",", C(F.cp1y), ",", C(F.cp2x), ",", C(F.cp2y), ",", C(F.x), ",", C(F.y));
                    break;
                case"at":
                case"wa":
                    M.push(" ", F.type, " ", C(F.x - this.arcScaleX_ * F.radius), ",", C(F.y - this.arcScaleY_ * F.radius), " ", C(F.x + this.arcScaleX_ * F.radius), ",", C(F.y + this.arcScaleY_ * F.radius), " ", C(F.xStart), ",", C(F.yStart), " ", C(F.xEnd), ",", C(F.yEnd))
            }
            if (F) {
                if (U.x == null || F.x < U.x) {
                    U.x = F.x
                }
                if (D.x == null || F.x > D.x) {
                    D.x = F.x
                }
                if (U.y == null || F.y < U.y) {
                    U.y = F.y
                }
                if (D.y == null || F.y > D.y) {
                    D.y = F.y
                }
            }
        }
        M.push(' ">');
        if (K) {
            if (typeof this.fillStyle == "object") {
                var H = this.fillStyle, J = 0, L = {x: 0, y: 0}, N = 0, S = 1;
                if (H.type_ == "gradient") {
                    var V = H.x1_ / this.arcScaleX_, X = H.y1_ / this.arcScaleY_, Z = this.getCoords_(H.x0_ / this.arcScaleX_, H.y0_ / this.arcScaleY_), ak = this.getCoords_(V, X);
                    J = Math.atan2(ak.x - Z.x, ak.y - Z.y) * 180 / Math.PI, J < 0 && (J += 360), J < 0.000001 && (J = 0)
                } else {
                    var Z = this.getCoords_(H.x0_, H.y0_), I = D.x - U.x, al = D.y - U.y;
                    L = {
                        x: (Z.x - U.x) / I,
                        y: (Z.y - U.y) / al
                    }, I /= this.arcScaleX_ * a, al /= this.arcScaleY_ * a;
                    var am = B.max(I, al);
                    N = 2 * H.r0_ / am, S = 2 * H.r1_ / am - N
                }
                var P = H.colors_;
                P.sort(function (ai, aj) {
                    return ai.offset - aj.offset
                });
                var T = P.length, W = P[0].color, Y = P[T - 1].color, aa = P[0].alpha * this.globalAlpha, ab = P[T - 1].alpha * this.globalAlpha, ad = [], E = 0;
                for (; E < T; E++) {
                    var G = P[E];
                    ad.push(G.offset * S + N + " " + G.color)
                }
                M.push('<g_vml_:fill type="', H.type_, '"', ' method="none" focus="100%"', ' color="', W, '"', ' color2="', Y, '"', ' colors="', ad.join(","), '"', ' opacity="', ab, '"', ' g_o_:opacity2="', aa, '"', ' angle="', J, '"', ' focusposition="', L.x, ",", L.y, '" />')
            } else {
                M.push('<g_vml_:fill color="', Q, '" opacity="', R, '" />')
            }
        } else {
            var ac = this.lineScale_ * this.lineWidth;
            ac < 1 && (R *= ac), M.push("<g_vml_:stroke", ' opacity="', R, '"', ' joinstyle="', this.lineJoin, '"', ' miterlimit="', this.miterLimit, '"', ' endcap="', v(this.lineCap), '"', ' weight="', ac, 'px"', ' color="', Q, '" />')
        }
        M.push("</g_vml_:shape>"), this.element_.insertAdjacentHTML("beforeEnd", M.join(""))
    }, x.fill = function () {
        this.stroke(!0)
    }, x.closePath = function () {
        this.currentPath_.push({type: "close"})
    }, x.getCoords_ = function (E, F) {
        var D = this.m_;
        return {
            x: a * (E * D[0][0] + F * D[1][0] + D[2][0]) - b,
            y: a * (E * D[0][1] + F * D[1][1] + D[2][1]) - b
        }
    }, x.save = function () {
        var D = {};
        s(this, D), this.aStack_.push(D), this.mStack_.push(this.m_), this.m_ = r(p(), this.m_)
    }, x.restore = function () {
        s(this.aStack_.pop(), this), this.m_ = this.mStack_.pop()
    }, x.translate = function (D, E) {
        l(this, r([[1, 0, 0], [0, 1, 0], [D, E, 1]], this.m_), !1)
    }, x.rotate = function (E) {
        var F = af(E), D = ae(E);
        l(this, r([[F, D, 0], [-D, F, 0], [0, 0, 1]], this.m_), !1)
    }, x.scale = function (D, E) {
        this.arcScaleX_ *= D, this.arcScaleY_ *= E, l(this, r([[D, 0, 0], [0, E, 0], [0, 0, 1]], this.m_), !0)
    }, x.transform = function (G, H, I, D, E, F) {
        l(this, r([[G, H, 0], [I, D, 0], [E, F, 1]], this.m_), !0)
    }, x.setTransform = function (G, H, I, D, E, F) {
        l(this, [[G, H, 0], [I, D, 0], [E, F, 1]], !0)
    }, x.clip = function () {
    }, x.arcTo = function () {
    }, x.createPattern = function () {
        return new q
    }, n.prototype.addColorStop = function (D, E) {
        E = u(E), this.colors_.push({offset: D, color: E.color, alpha: E.alpha})
    }, G_vmlCanvasManager = f, CanvasRenderingContext2D = w, CanvasGradient = n, CanvasPattern = q
}();
/*
 AnythingSlider v1.8.6
 Original by Chris Coyier: http://css-tricks.com
 Get the latest version: https://github.com/ProLoser/AnythingSlider

 To use the navigationFormatter function, you must have a function that
 accepts two paramaters, and returns a string of HTML text.

 index = integer index (1 based);
 panel = jQuery wrapped LI item this tab references
 @return = Must return a string of HTML/Text

 navigationFormatter: function(index, panel){
 return "Panel #" + index; // This would have each tab with the text 'Panel #X' where X = index
 }
 */
(function (b) {
    b.anythingSlider = function (k, a) {
        var h = this, g, l;
        h.el = k;
        h.$el = b(k).addClass("anythingBase").wrap('<div class="anythingSlider"><div class="anythingWindow" /></div>');
        h.$el.data("AnythingSlider", h);
        h.init = function () {
            h.options = g = b.extend({}, b.anythingSlider.defaults, a);
            h.initialized = false;
            if (b.isFunction(g.onBeforeInitialize)) {
                h.$el.bind("before_initialize", g.onBeforeInitialize)
            }
            h.$el.trigger("before_initialize", h);
            b('<!--[if lte IE 8]><script>jQuery("body").addClass("as-oldie");<\/script><![endif]-->').appendTo("body").remove();
            h.$wrapper = h.$el.parent().closest("div.anythingSlider").addClass("anythingSlider-" + g.theme);
            h.$window = h.$el.closest("div.anythingWindow");
            h.win = window;
            h.$win = b(h.win);
            h.$controls = b('<div class="anythingControls"></div>');
            h.$nav = b('<ul class="thumbNav"><li><a><span></span></a></li></ul>');
            h.$startStop = b('<a href="#" class="start-stop"></a>');
            if (g.buildStartStop || g.buildNavigation) {
                h.$controls.appendTo((g.appendControlsTo && b(g.appendControlsTo).length) ? b(g.appendControlsTo) : h.$wrapper)
            }
            if (g.buildNavigation) {
                h.$nav.appendTo((g.appendNavigationTo && b(g.appendNavigationTo).length) ? b(g.appendNavigationTo) : h.$controls)
            }
            if (g.buildStartStop) {
                h.$startStop.appendTo((g.appendStartStopTo && b(g.appendStartStopTo).length) ? b(g.appendStartStopTo) : h.$controls)
            }
            h.runTimes = b(".anythingBase").length;
            h.regex = new RegExp("panel" + h.runTimes + "-(\\d+)", "i");
            if (h.runTimes === 1) {
                h.makeActive()
            }
            h.flag = false;
            h.playing = g.autoPlay;
            h.slideshow = false;
            h.hovered = false;
            h.panelSize = [];
            h.currentPage = h.targetPage = g.startPanel = parseInt(g.startPanel, 10) || 1;
            g.changeBy = parseInt(g.changeBy, 10) || 1;
            l = (g.mode || "h").toLowerCase().match(/(h|v|f)/);
            l = g.vertical ? "v" : (l || ["h"])[0];
            g.mode = l === "v" ? "vertical" : l === "f" ? "fade" : "horizontal";
            if (l === "f") {
                g.showMultiple = 1;
                g.infiniteSlides = false
            }
            h.adj = (g.infiniteSlides) ? 0 : 1;
            h.adjustMultiple = 0;
            h.width = h.$el.width();
            h.height = h.$el.height();
            h.outerPad = [h.$wrapper.innerWidth() - h.$wrapper.width(), h.$wrapper.innerHeight() - h.$wrapper.height()];
            if (g.playRtl) {
                h.$wrapper.addClass("rtl")
            }
            if (g.expand) {
                h.$outer = h.$wrapper.parent();
                h.$window.css({width: "100%", height: "100%"});
                h.checkResize()
            }
            if (g.buildStartStop) {
                h.buildAutoPlay()
            }
            if (g.buildArrows) {
                h.buildNextBackButtons()
            }
            if (!g.autoPlay) {
                g.autoPlayLocked = false
            }
            h.$lastPage = h.$targetPage = h.$currentPage;
            h.updateSlider();
            if (!b.isFunction(b.easing[g.easing])) {
                g.easing = "swing"
            }
            if (g.pauseOnHover) {
                h.$wrapper.hover(function () {
                    if (h.playing) {
                        h.$el.trigger("slideshow_paused", h);
                        h.clearTimer(true)
                    }
                }, function () {
                    if (h.playing) {
                        h.$el.trigger("slideshow_unpaused", h);
                        h.startStop(h.playing, true)
                    }
                })
            }
            h.slideControls(false);
            h.$wrapper.bind("mouseenter mouseleave", function (d) {
                b(this)[d.type === "mouseenter" ? "addClass" : "removeClass"]("anythingSlider-hovered");
                h.hovered = (d.type === "mouseenter") ? true : false;
                h.slideControls(h.hovered)
            });
            b(document).keyup(function (d) {
                if (g.enableKeyboard && h.$wrapper.hasClass("activeSlider") && !d.target.tagName.match("TEXTAREA|INPUT|SELECT")) {
                    if (g.mode !== "vertical" && (d.which === 38 || d.which === 40)) {
                        return
                    }
                    switch (d.which) {
                        case 39:
                        case 40:
                            h.goForward();
                            break;
                        case 37:
                        case 38:
                            h.goBack();
                            break
                    }
                }
            });
            h.currentPage = h.gotoHash() || g.startPanel || 1;
            h.gotoPage(h.currentPage, false, null, -1);
            var c = "slideshow_paused slideshow_unpaused slide_init slide_begin slideshow_stop slideshow_start initialized swf_completed".split(" ");
            b.each("onShowPause onShowUnpause onSlideInit onSlideBegin onShowStop onShowStart onInitialized onSWFComplete".split(" "), function (e, d) {
                if (b.isFunction(g[d])) {
                    h.$el.bind(c[e], g[d])
                }
            });
            if (b.isFunction(g.onSlideComplete)) {
                h.$el.bind("slide_complete", function () {
                    setTimeout(function () {
                        g.onSlideComplete(h)
                    }, 0);
                    return false
                })
            }
            h.initialized = true;
            h.$el.trigger("initialized", h);
            h.startStop(g.autoPlay)
        };
        h.updateSlider = function () {
            h.$el.children(".cloned").remove();
            h.navTextVisible = h.$nav.find("span:first").css("visibility") !== "hidden";
            h.$nav.empty();
            h.currentPage = h.currentPage || 1;
            h.$items = h.$el.children();
            h.pages = h.$items.length;
            h.dir = (g.mode === "vertical") ? "top" : "left";
            g.showMultiple = (g.mode === "vertical") ? 1 : parseInt(g.showMultiple, 10) || 1;
            g.navigationSize = (g.navigationSize === false) ? 0 : parseInt(g.navigationSize, 10) || 0;
            h.$items.find("a").unbind("focus.AnythingSlider").bind("focus.AnythingSlider", function (d) {
                var f = b(this).closest(".panel"), e = h.$items.index(f) + h.adj;
                h.$items.find(".focusedLink").removeClass("focusedLink");
                b(this).addClass("focusedLink");
                h.$window.scrollLeft(0).scrollTop(0);
                if ((e !== -1 && (e >= h.currentPage + g.showMultiple || e < h.currentPage))) {
                    h.gotoPage(e);
                    d.preventDefault()
                }
            });
            if (g.showMultiple > 1) {
                if (g.showMultiple > h.pages) {
                    g.showMultiple = h.pages
                }
                h.adjustMultiple = (g.infiniteSlides && h.pages > 1) ? 0 : g.showMultiple - 1
            }
            h.$controls.add(h.$nav).add(h.$startStop).add(h.$forward).add(h.$back)[(h.pages <= 1) ? "hide" : "show"]();
            if (h.pages > 1) {
                h.buildNavigation()
            }
            if (g.mode !== "fade" && g.infiniteSlides && h.pages > 1) {
                h.$el.prepend(h.$items.filter(":last").clone().addClass("cloned"));
                if (g.showMultiple > 1) {
                    h.$el.append(h.$items.filter(":lt(" + g.showMultiple + ")").clone().addClass("cloned multiple"))
                } else {
                    h.$el.append(h.$items.filter(":first").clone().addClass("cloned"))
                }
                h.$el.find(".cloned").each(function () {
                    b(this).find("a,input,textarea,select,button,area,form").attr({
                        disabled: "disabled",
                        name: ""
                    });
                    b(this).find("[id]").andSelf().removeAttr("id")
                })
            }
            h.$items = h.$el.addClass(g.mode).children().addClass("panel");
            h.setDimensions();
            if (g.resizeContents) {
                h.$items.css("width", h.width);
                h.$wrapper.css("width", h.getDim(h.currentPage)[0]).add(h.$items).css("height", h.height)
            } else {
                h.$win.load(function () {
                    h.setDimensions();
                    c = h.getDim(h.currentPage);
                    h.$wrapper.css({width: c[0], height: c[1]});
                    h.setCurrentPage(h.currentPage, false)
                })
            }
            if (h.currentPage > h.pages) {
                h.currentPage = h.pages
            }
            h.setCurrentPage(h.currentPage, false);
            h.$nav.find("a").eq(h.currentPage - 1).addClass("cur");
            if (g.mode === "fade") {
                var c = h.$items.eq(h.currentPage - 1);
                if (g.resumeOnVisible) {
                    c.css({opacity: 1}).siblings().css({opacity: 0})
                } else {
                    h.$items.css("opacity", 1);
                    c.fadeIn(0).siblings().fadeOut(0)
                }
            }
        };
        h.buildNavigation = function () {
            if (g.buildNavigation && (h.pages > 1)) {
                var n, c, e, f, d;
                h.$items.filter(":not(.cloned)").each(function (m) {
                    d = b("<li/>");
                    e = m + 1;
                    c = (e === 1 ? " first" : "") + (e === h.pages ? " last" : "");
                    n = '<a class="panel' + e + (h.navTextVisible ? '"' : " " + g.tooltipClass + '" title="@"') + ' href="#"><span>@</span></a>';
                    if (b.isFunction(g.navigationFormatter)) {
                        f = g.navigationFormatter(e, b(this));
                        if (typeof(f) === "string") {
                            d.html(n.replace(/@/g, f))
                        } else {
                            d = b("<li/>", f)
                        }
                    } else {
                        d.html(n.replace(/@/g, e))
                    }
                    d.appendTo(h.$nav).addClass(c).data("index", e)
                });
                h.$nav.children("li").bind(g.clickControls, function (m) {
                    if (!h.flag && g.enableNavigation) {
                        h.flag = true;
                        setTimeout(function () {
                            h.flag = false
                        }, 100);
                        h.gotoPage(b(this).data("index"))
                    }
                    m.preventDefault()
                });
                if (!!g.navigationSize && g.navigationSize < h.pages) {
                    if (!h.$controls.find(".anythingNavWindow").length) {
                        h.$nav.before('<ul><li class="prev"><a href="#"><span>' + g.backText + "</span></a></li></ul>").after('<ul><li class="next"><a href="#"><span>' + g.forwardText + "</span></a></li></ul>").wrap('<div class="anythingNavWindow"></div>')
                    }
                    h.navWidths = h.$nav.find("li").map(function () {
                        return b(this).outerWidth(true) + Math.ceil(parseInt(b(this).find("span").css("left"), 10) / 2 || 0)
                    }).get();
                    h.navLeft = h.currentPage;
                    h.$nav.width(h.navWidth(1, h.pages + 1) + 25);
                    h.$controls.find(".anythingNavWindow").width(h.navWidth(1, g.navigationSize + 1)).end().find(".prev,.next").bind(g.clickControls, function (m) {
                        if (!h.flag) {
                            h.flag = true;
                            setTimeout(function () {
                                h.flag = false
                            }, 200);
                            h.navWindow(h.navLeft + g.navigationSize * (b(this).is(".prev") ? -1 : 1))
                        }
                        m.preventDefault()
                    })
                }
            }
        };
        h.navWidth = function (o, p) {
            var e, d = Math.min(o, p), c = Math.max(o, p), f = 0;
            for (e = d; e < c; e++) {
                f += h.navWidths[e - 1] || 0
            }
            return f
        };
        h.navWindow = function (c) {
            if (!!g.navigationSize && g.navigationSize < h.pages && h.navWidths) {
                var d = h.pages - g.navigationSize + 1;
                c = (c <= 1) ? 1 : (c > 1 && c < d) ? c : d;
                if (c !== h.navLeft) {
                    h.$controls.find(".anythingNavWindow").animate({
                        scrollLeft: h.navWidth(1, c),
                        width: h.navWidth(c, c + g.navigationSize)
                    }, {queue: false, duration: g.animationTime});
                    h.navLeft = c
                }
            }
        };
        h.buildNextBackButtons = function () {
            h.$forward = b('<span class="arrow forward"><a href="#"><span>' + g.forwardText + "</span></a></span>");
            h.$back = b('<span class="arrow back"><a href="#"><span>' + g.backText + "</span></a></span>");
            h.$back.bind(g.clickBackArrow, function (c) {
                if (g.enableArrows && !h.flag) {
                    h.flag = true;
                    setTimeout(function () {
                        h.flag = false
                    }, 100);
                    h.goBack()
                }
                c.preventDefault()
            });
            h.$forward.bind(g.clickForwardArrow, function (c) {
                if (g.enableArrows && !h.flag) {
                    h.flag = true;
                    setTimeout(function () {
                        h.flag = false
                    }, 100);
                    h.goForward()
                }
                c.preventDefault()
            });
            h.$back.add(h.$forward).find("a").bind("focusin focusout", function () {
                b(this).toggleClass("hover")
            });
            h.$back.appendTo((g.appendBackTo && b(g.appendBackTo).length) ? b(g.appendBackTo) : h.$wrapper);
            h.$forward.appendTo((g.appendForwardTo && b(g.appendForwardTo).length) ? b(g.appendForwardTo) : h.$wrapper);
            h.arrowWidth = h.$forward.width();
            h.arrowRight = parseInt(h.$forward.css("right"), 10);
            h.arrowLeft = parseInt(h.$back.css("left"), 10)
        };
        h.buildAutoPlay = function () {
            h.$startStop.html("<span>" + (h.playing ? g.stopText : g.startText) + "</span>").bind(g.clickSlideshow, function (c) {
                if (g.enableStartStop) {
                    h.startStop(!h.playing);
                    h.makeActive();
                    if (h.playing && !g.autoPlayDelayed) {
                        h.goForward(true)
                    }
                }
                c.preventDefault()
            }).bind("focusin focusout", function () {
                b(this).toggleClass("hover")
            })
        };
        h.checkResize = function (c) {
            clearTimeout(h.resizeTimer);
            h.resizeTimer = setTimeout(function () {
                var e = h.$outer.width() - h.outerPad[0], d = (h.$outer[0].tagName === "BODY" ? h.$win.height() : h.$outer.height()) - h.outerPad[1];
                if (h.width * g.showMultiple !== e || h.height !== d) {
                    h.setDimensions();
                    h.gotoPage(h.currentPage, h.playing, null, -1)
                }
                if (typeof(c) === "undefined") {
                    h.checkResize()
                }
            }, 500)
        };
        h.setDimensions = function () {
            var f, u, r, d, s = 0, e = {
                width: "100%",
                height: "100%"
            }, v = (g.showMultiple > 1) ? h.width || h.$window.width() / g.showMultiple : h.$window.width(), c = h.$win.width();
            if (g.expand) {
                f = h.$outer.width() - h.outerPad[0];
                h.height = u = h.$outer.height() - h.outerPad[1];
                h.$wrapper.add(h.$window).add(h.$items).css({
                    width: f,
                    height: u
                });
                h.width = v = (g.showMultiple > 1) ? f / g.showMultiple : f
            }
            h.$items.each(function (m) {
                d = b(this);
                r = d.children();
                if (g.resizeContents) {
                    f = h.width;
                    u = h.height;
                    d.css({width: f, height: u});
                    if (r.length) {
                        if (r[0].tagName === "EMBED") {
                            r.attr(e)
                        }
                        if (r[0].tagName === "OBJECT") {
                            r.find("embed").attr(e)
                        }
                        if (r.length === 1) {
                            r.css(e)
                        }
                    }
                } else {
                    f = d.width() || h.width;
                    if (r.length === 1 && f >= c) {
                        f = (r.width() >= c) ? v : r.width();
                        r.css("max-width", f)
                    }
                    d.css("width", f);
                    u = (r.length === 1 ? r.outerHeight(true) : d.height());
                    if (u <= h.outerPad[1]) {
                        u = h.height
                    }
                    d.css("height", u)
                }
                h.panelSize[m] = [f, u, s];
                s += (g.mode === "vertical") ? u : f
            });
            h.$el.css((g.mode === "vertical" ? "height" : "width"), g.mode === "fade" ? h.width : s)
        };
        h.getDim = function (c) {
            var e, f = h.width, d = h.height;
            if (h.pages < 1 || isNaN(c)) {
                return [f, d]
            }
            c = (g.infiniteSlides && h.pages > 1) ? c : c - 1;
            e = h.panelSize[c];
            if (e) {
                f = e[0] || f;
                d = e[1] || d
            }
            if (g.showMultiple > 1) {
                for (e = 1; e < g.showMultiple; e++) {
                    f += h.panelSize[(c + e)][0];
                    d = Math.max(d, h.panelSize[c + e][1])
                }
            }
            return [f, d]
        };
        h.goForward = function (c) {
            h.gotoPage(h[g.allowRapidChange ? "targetPage" : "currentPage"] + g.changeBy * (g.playRtl ? -1 : 1), c)
        };
        h.goBack = function (c) {
            h.gotoPage(h[g.allowRapidChange ? "targetPage" : "currentPage"] + g.changeBy * (g.playRtl ? 1 : -1), c)
        };
        h.gotoPage = function (e, f, c, d) {
            if (f !== true) {
                f = false;
                h.startStop(false);
                h.makeActive()
            }
            if (/^[#|.]/.test(e) && b(e).length) {
                e = b(e).closest(".panel").index() + h.adj
            }
            if (g.changeBy !== 1) {
                var n = h.pages - h.adjustMultiple;
                if (e < 1) {
                    e = g.stopAtEnd ? 1 : (g.infiniteSlides ? h.pages + e : (g.showMultiple > 1 - e ? 1 : n))
                }
                if (e > h.pages) {
                    e = g.stopAtEnd ? h.pages : (g.showMultiple > 1 - e ? 1 : e -= n)
                } else {
                    if (e >= n) {
                        e = n
                    }
                }
            }
            if (h.pages <= 1) {
                return
            }
            h.$lastPage = h.$currentPage;
            if (typeof(e) !== "number") {
                e = parseInt(e, 10) || g.startPanel;
                h.setCurrentPage(e)
            }
            if (f && g.isVideoPlaying(h)) {
                return
            }
            h.exactPage = e;
            if (e > h.pages + 1 - h.adj) {
                e = (!g.infiniteSlides && !g.stopAtEnd) ? 1 : h.pages
            }
            if (e < h.adj) {
                e = (!g.infiniteSlides && !g.stopAtEnd) ? h.pages : 1
            }
            if (!g.infiniteSlides) {
                h.exactPage = e
            }
            h.currentPage = (e > h.pages) ? h.pages : (e < 1) ? 1 : h.currentPage;
            h.$currentPage = h.$items.eq(h.currentPage - h.adj);
            h.targetPage = (e === 0) ? h.pages : (e > h.pages) ? 1 : e;
            h.$targetPage = h.$items.eq(h.targetPage - h.adj);
            d = typeof d !== "undefined" ? d : g.animationTime;
            if (d >= 0) {
                h.$el.trigger("slide_init", h)
            }
            if (d > 0) {
                h.slideControls(true)
            }
            if (g.buildNavigation) {
                h.setNavigation(h.targetPage)
            }
            if (f !== true) {
                f = false
            }
            if (!f || (g.stopAtEnd && e === h.pages)) {
                h.startStop(false)
            }
            if (d >= 0) {
                h.$el.trigger("slide_begin", h)
            }
            setTimeout(function (m) {
                var p, r = true;
                if (g.allowRapidChange) {
                    h.$wrapper.add(h.$el).add(h.$items).stop(true, true)
                }
                if (!g.resizeContents) {
                    p = h.getDim(e);
                    m = {};
                    if (h.$wrapper.width() !== p[0]) {
                        m.width = p[0] || h.width;
                        r = false
                    }
                    if (h.$wrapper.height() !== p[1]) {
                        m.height = p[1] || h.height;
                        r = false
                    }
                    if (!r) {
                        h.$wrapper.filter(":not(:animated)").animate(m, {
                            queue: false,
                            duration: (d < 0 ? 0 : d),
                            easing: g.easing
                        })
                    }
                }
                if (g.mode === "fade") {
                    if (h.$lastPage[0] !== h.$targetPage[0]) {
                        h.fadeIt(h.$lastPage, 0, d);
                        h.fadeIt(h.$targetPage, 1, d, function () {
                            h.endAnimation(e, c, d)
                        })
                    } else {
                        h.endAnimation(e, c, d)
                    }
                } else {
                    m = {};
                    m[h.dir] = -h.panelSize[(g.infiniteSlides && h.pages > 1) ? e : e - 1][2];
                    h.$el.filter(":not(:animated)").animate(m, {
                        queue: false,
                        duration: d < 0 ? 0 : d,
                        easing: g.easing,
                        complete: function () {
                            h.endAnimation(e, c, d)
                        }
                    })
                }
            }, parseInt(g.delayBeforeAnimate, 10) || 0)
        };
        h.endAnimation = function (e, c, d) {
            if (e === 0) {
                h.$el.css(h.dir, g.mode === "fade" ? 0 : -h.panelSize[h.pages][2]);
                e = h.pages
            } else {
                if (e > h.pages) {
                    h.$el.css(h.dir, g.mode === "fade" ? 0 : -h.panelSize[1][2]);
                    e = 1
                }
            }
            h.exactPage = e;
            h.setCurrentPage(e, false);
            if (g.mode === "fade") {
                h.fadeIt(h.$items.not(":eq(" + (e - h.adj) + ")"), 0, 0)
            }
            if (!h.hovered) {
                h.slideControls(false)
            }
            if (g.hashTags) {
                h.setHash(e)
            }
            if (d >= 0) {
                h.$el.trigger("slide_complete", h)
            }
            if (typeof c === "function") {
                c(h)
            }
            if (g.autoPlayLocked && !h.playing) {
                setTimeout(function () {
                    h.startStop(true)
                }, g.resumeDelay - (g.autoPlayDelayed ? g.delay : 0))
            }
        };
        h.fadeIt = function (e, n, d, c) {
            var f = d < 0 ? 0 : d;
            if (g.resumeOnVisible) {
                e.filter(":not(:animated)").fadeTo(f, n, c)
            } else {
                e.filter(":not(:animated)")[n === 0 ? "fadeOut" : "fadeIn"](f, c)
            }
        };
        h.setCurrentPage = function (d, e) {
            d = parseInt(d, 10);
            if (h.pages < 1 || d === 0 || isNaN(d)) {
                return
            }
            if (d > h.pages + 1 - h.adj) {
                d = h.pages - h.adj
            }
            if (d < h.adj) {
                d = 1
            }
            if (g.buildArrows && !g.infiniteSlides && g.stopAtEnd) {
                h.$forward[d === h.pages - h.adjustMultiple ? "addClass" : "removeClass"]("disabled");
                h.$back[d === 1 ? "addClass" : "removeClass"]("disabled");
                if (d === h.pages && h.playing) {
                    h.startStop()
                }
            }
            if (!e) {
                var c = h.getDim(d);
                h.$wrapper.css({
                    width: c[0],
                    height: c[1]
                }).add(h.$window).scrollLeft(0).scrollTop(0);
                h.$el.css(h.dir, g.mode === "fade" ? 0 : -h.panelSize[(g.infiniteSlides && h.pages > 1) ? d : d - 1][2])
            }
            h.currentPage = d;
            h.$currentPage = h.$items.removeClass("activePage").eq(d - h.adj).addClass("activePage");
            if (g.buildNavigation) {
                h.setNavigation(d)
            }
        };
        h.setNavigation = function (c) {
            h.$nav.find(".cur").removeClass("cur").end().find("a").eq(c - 1).addClass("cur")
        };
        h.makeActive = function () {
            if (!h.$wrapper.hasClass("activeSlider")) {
                b(".activeSlider").removeClass("activeSlider");
                h.$wrapper.addClass("activeSlider")
            }
        };
        h.gotoHash = function () {
            var e = h.win.location.hash, f = e.indexOf("&"), c = e.match(h.regex);
            if (c === null && !/^#&/.test(e) && !/#!?\//.test(e)) {
                e = e.substring(0, (f >= 0 ? f : e.length));
                try {
                    c = (b(e).length && b(e).closest(".anythingBase")[0] === h.el) ? h.$items.index(b(e).closest(".panel")) + h.adj : null
                } catch (d) {
                    c = null
                }
            } else {
                if (c !== null) {
                    c = (g.hashTags) ? parseInt(c[1], 10) : null
                }
            }
            return c
        };
        h.setHash = function (c) {
            var d = "panel" + h.runTimes + "-", e = h.win.location.hash;
            if (typeof e !== "undefined") {
                h.win.location.hash = (e.indexOf(d) > 0) ? e.replace(h.regex, d + c) : e + "&" + d + c
            }
        };
        h.slideControls = function (o) {
            var e = (o) ? "slideDown" : "slideUp", c = (o) ? 0 : g.animationTime, d = (o) ? g.animationTime : 0, p = (o) ? 1 : 0, f = (o) ? 0 : 1;
            if (g.toggleControls) {
                h.$controls.stop(true, true).delay(c)[e](g.animationTime / 2).delay(d)
            }
            if (g.buildArrows && g.toggleArrows) {
                if (!h.hovered && h.playing) {
                    f = 1;
                    p = 0
                }
                h.$forward.stop(true, true).delay(c).animate({
                    right: h.arrowRight + (f * h.arrowWidth),
                    opacity: p
                }, g.animationTime / 2);
                h.$back.stop(true, true).delay(c).animate({
                    left: h.arrowLeft + (f * h.arrowWidth),
                    opacity: p
                }, g.animationTime / 2)
            }
        };
        h.clearTimer = function (c) {
            if (h.timer) {
                h.win.clearInterval(h.timer);
                if (!c && h.slideshow) {
                    h.$el.trigger("slideshow_stop", h);
                    h.slideshow = false
                }
            }
        };
        h.startStop = function (c, d) {
            if (c !== true) {
                c = false
            }
            h.playing = c;
            if (c && !d) {
                h.$el.trigger("slideshow_start", h);
                h.slideshow = true
            }
            if (g.buildStartStop) {
                h.$startStop.toggleClass("playing", c).find("span").html(c ? g.stopText : g.startText);
                if (h.$startStop.find("span").css("visibility") === "hidden") {
                    h.$startStop.addClass(g.tooltipClass).attr("title", c ? g.stopText : g.startText)
                }
            }
            if (c) {
                h.clearTimer(true);
                h.timer = h.win.setInterval(function () {
                    if (!g.isVideoPlaying(h)) {
                        h.goForward(true)
                    } else {
                        if (!g.resumeOnVideoEnd) {
                            h.startStop()
                        }
                    }
                }, g.delay)
            } else {
                h.clearTimer()
            }
        };
        h.init()
    };
    b.anythingSlider.defaults = {
        theme: "default",
        mode: "horiz",
        expand: false,
        resizeContents: true,
        showMultiple: false,
        easing: "swing",
        buildArrows: true,
        buildNavigation: true,
        buildStartStop: true,
        toggleArrows: false,
        toggleControls: false,
        startText: "Start",
        stopText: "Stop",
        forwardText: "&raquo;",
        backText: "&laquo;",
        tooltipClass: "tooltip",
        enableArrows: true,
        enableNavigation: true,
        enableStartStop: true,
        enableKeyboard: true,
        startPanel: 1,
        changeBy: 1,
        hashTags: true,
        infiniteSlides: true,
        navigationFormatter: null,
        navigationSize: false,
        autoPlay: false,
        autoPlayLocked: false,
        autoPlayDelayed: false,
        pauseOnHover: true,
        stopAtEnd: false,
        playRtl: false,
        delay: 3000,
        resumeDelay: 15000,
        animationTime: 600,
        delayBeforeAnimate: 0,
        clickForwardArrow: "click",
        clickBackArrow: "click",
        clickControls: "click focusin",
        clickSlideshow: "click",
        allowRapidChange: false,
        resumeOnVideoEnd: true,
        resumeOnVisible: true,
        addWmodeToObject: "opaque",
        isVideoPlaying: function (a) {
            return false
        }
    };
    b.fn.anythingSlider = function (a, d) {
        return this.each(function () {
            var c, f = b(this).data("AnythingSlider");
            if ((typeof(a)).match("object|undefined")) {
                if (!f) {
                    (new b.anythingSlider(this, a))
                } else {
                    f.updateSlider()
                }
            } else {
                if (/\d/.test(a) && !isNaN(a) && f) {
                    c = (typeof(a) === "number") ? a : parseInt(b.trim(a), 10);
                    if (c >= 1 && c <= f.pages) {
                        f.gotoPage(c, false, d)
                    }
                } else {
                    if (/^[#|.]/.test(a) && b(a).length) {
                        f.gotoPage(a, false, d)
                    }
                }
            }
        })
    }
})(jQuery);
/*
 * jQuery BBQ: Back Button & Query Library - v1.4pre - 1/15/2013
 * http://benalman.com/projects/jquery-bbq-plugin/
 *
 * Copyright (c) 2010-2013 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function (M, Y) {
    var an, ah = Array.prototype.slice, U = decodeURIComponent, av = M.param, al, at, ai, ad, au = M.bbq = M.bbq || {}, W, af, ak, aq = M.event.special, ar = "hashchange", X = "querystring", Q = "fragment", ab = "elemUrlAttr", aj = "href", ag = "src", ac = /^.*\?|#.*$/g, S, N, ao, am, V, R = {};

    function O(a) {
        return typeof a === "string"
    }

    function T(a) {
        var b = ah.call(arguments, 1);
        return function () {
            return a.apply(this, b.concat(ah.call(arguments)))
        }
    }

    function ae(a) {
        return a.replace(N, "$2")
    }

    function aa(a) {
        return a.replace(/(?:^[^?#]*\?([^#]*).*$)?.*/, "$1")
    }

    function ap(h, c, l, g, k) {
        var a, d, e, b, f;
        if (g !== an) {
            e = l.match(h ? N : /^([^#?]*)\??([^#]*)(#?.*)/);
            f = e[3] || "";
            if (k === 2 && O(g)) {
                d = g.replace(h ? S : ac, "")
            } else {
                b = ai(e[2]);
                g = O(g) ? ai[h ? Q : X](g) : g;
                d = k === 2 ? g : k === 1 ? M.extend({}, g, b) : M.extend({}, b, g);
                d = al(d);
                if (h) {
                    d = d.replace(ao, U)
                }
            }
            a = e[1] + (h ? V : d || !e[1] ? "?" : "") + d + f
        } else {
            a = c(l !== an ? l : location.href)
        }
        return a
    }

    av[X] = T(ap, 0, aa);
    av[Q] = at = T(ap, 1, ae);
    av.sorted = al = function (c, b) {
        var d = [], a = {};
        M.each(av(c, b).split("&"), function (e, h) {
            var f = h.replace(/(?:%5B|=).*$/, ""), g = a[f];
            if (!g) {
                g = a[f] = [];
                d.push(f)
            }
            g.push(h)
        });
        return M.map(d.sort(), function (e) {
            return a[e]
        }).join("&")
    };
    at.noEscape = function (a) {
        a = a || "";
        var b = M.map(a.split(""), encodeURIComponent);
        ao = new RegExp(b.join("|"), "g")
    };
    at.noEscape(",/");
    at.ajaxCrawlable = function (a) {
        if (a !== an) {
            if (a) {
                S = /^.*(?:#!|#)/;
                N = /^([^#]*)(?:#!|#)?(.*)$/;
                V = "#!"
            } else {
                S = /^.*#/;
                N = /^([^#]*)#?(.*)$/;
                V = "#"
            }
            am = !!a
        }
        return am
    };
    at.ajaxCrawlable(0);
    M.deparam = ai = function (a, d) {
        var b = {}, c = {"true": !0, "false": !1, "null": null};
        M.each(a.replace(/\+/g, " ").split("&"), function (m, f) {
            var n = f.split("="), g = U(n[0]), o, h = b, l = 0, e = g.split("]["), k = e.length - 1;
            if (/\[/.test(e[0]) && /\]$/.test(e[k])) {
                e[k] = e[k].replace(/\]$/, "");
                e = e.shift().split("[").concat(e);
                k = e.length - 1
            } else {
                k = 0
            }
            if (n.length === 2) {
                o = U(n[1]);
                if (d) {
                    o = o && !isNaN(o) ? +o : o === "undefined" ? an : c[o] !== an ? c[o] : o
                }
                if (k) {
                    for (; l <= k; l++) {
                        g = e[l] === "" ? h.length : e[l];
                        h = h[g] = l < k ? h[g] || (e[l + 1] && isNaN(e[l + 1]) ? {} : []) : o
                    }
                } else {
                    if (M.isArray(b[g])) {
                        b[g].push(o)
                    } else {
                        if (b[g] !== an) {
                            b[g] = [b[g], o]
                        } else {
                            b[g] = o
                        }
                    }
                }
            } else {
                if (g) {
                    b[g] = d ? an : ""
                }
            }
        });
        return b
    };
    function Z(a, c, b) {
        if (c === an || typeof c === "boolean") {
            b = c;
            c = av[a ? Q : X]()
        } else {
            c = O(c) ? c.replace(a ? S : ac, "") : c
        }
        return ai(c, b)
    }

    ai[X] = T(Z, 0);
    ai[Q] = ad = T(Z, 1);
    M[ab] || (M[ab] = function (a) {
        return M.extend(R, a)
    })({
        a: aj,
        base: aj,
        iframe: ag,
        img: ag,
        input: ag,
        form: "action",
        link: aj,
        script: ag
    });
    ak = M[ab];
    function P(a, c, b, d) {
        if (!O(b) && typeof b !== "object") {
            d = b;
            b = c;
            c = an
        }
        return this.each(function () {
            var e = M(this), g = c || ak()[(this.nodeName || "").toLowerCase()] || "", f = g && e.attr(g) || "";
            e.attr(g, av[a](f, b, d))
        })
    }

    M.fn[X] = T(P, X);
    M.fn[Q] = T(P, Q);
    au.pushState = W = function (a, d) {
        if (O(a) && /^#/.test(a) && d === an) {
            d = 2
        }
        var b = a !== an, c = at(location.href, b ? a : {}, b ? d : 2);
        location.href = c
    };
    au.getState = af = function (b, a) {
        return b === an || typeof b === "boolean" ? ad(b) : ad(a)[b]
    };
    au.removeState = function (b) {
        var a = {};
        if (b !== an) {
            a = af();
            M.each(M.isArray(b) ? b : arguments, function (c, d) {
                delete a[d]
            })
        }
        W(a, 2)
    };
    aq[ar] = M.extend(aq[ar], {
        add: function (c) {
            var a;

            function b(d) {
                var e = d[Q] = at();
                d.getState = function (g, f) {
                    return g === an || typeof g === "boolean" ? ai(e, g) : ai(e, f)[g]
                };
                a.apply(this, arguments)
            }

            if (M.isFunction(c)) {
                a = c;
                return b
            } else {
                a = c.handler;
                c.handler = b
            }
        }
    })
})(jQuery, this);
/*
 * jQuery hashchange event - v1.3 - 7/21/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 *
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function (m, q, u) {
    var s = "hashchange", n = document, p, o = m.event.special, l = n.documentMode, r = "on" + s in q && (l === u || l > 7);

    function v(a) {
        a = a || location.href;
        return "#" + a.replace(/^[^#]*#?(.*)$/, "$1")
    }

    m.fn[s] = function (a) {
        return a ? this.bind(s, a) : this.trigger(s)
    };
    m.fn[s].delay = 50;
    o[s] = m.extend(o[s], {
        setup: function () {
            if (r) {
                return false
            }
            m(p.start)
        }, teardown: function () {
            if (r) {
                return false
            }
            m(p.stop)
        }
    });
    p = (function () {
        var b = {}, c, f = v(), a = function (h) {
            return h
        }, g = a, d = a;
        b.start = function () {
            c || e()
        };
        b.stop = function () {
            c && clearTimeout(c);
            c = u
        };
        function e() {
            var h = v(), k = d(f);
            if (h !== f) {
                g(f = h, k);
                m(q).trigger(s)
            } else {
                if (k !== f) {
                    location.href = location.href.replace(/#.*/, "") + k
                }
            }
            c = setTimeout(e, m.fn[s].delay)
        }

        (navigator.userAgent.match(/MSIE/i) !== null) && !r && (function () {
            var k, h;
            b.start = function () {
                if (!k) {
                    h = m.fn[s].src;
                    h = h && h + v();
                    k = m('<iframe tabindex="-1" title="empty"/>').hide().one("load", function () {
                        h || g(v());
                        e()
                    }).attr("src", h || "javascript:0").insertAfter("body")[0].contentWindow;
                    n.onpropertychange = function () {
                        try {
                            if (event.propertyName === "title") {
                                k.document.title = n.title
                            }
                        } catch (w) {
                        }
                    }
                }
            };
            b.stop = a;
            d = function () {
                return v(k.location.href)
            };
            g = function (D, C) {
                var A = k.document, B = m.fn[s].domain;
                if (D !== C) {
                    A.title = n.title;
                    A.open();
                    B && A.write('<script>document.domain="' + B + '"<\/script>');
                    A.close();
                    k.location.hash = D
                }
            }
        })();
        return b
    })()
})(jQuery, this);
(function (h) {
    var k = !!h.Tween;
    if (k) {
        h.Tween.propHooks.backgroundPosition = {
            get: function (a) {
                return f(h(a.elem).css(a.prop))
            }, set: l
        }
    } else {
        h.fx.step.backgroundPosition = l
    }
    function f(a) {
        var b = (a || "").split(/ /);
        var c = {
            center: "50%",
            left: "0%",
            right: "100%",
            top: "0%",
            bottom: "100%"
        };
        var d = function (e) {
            var n = (c[b[e]] || b[e] || "50%").match(/^([+-]=)?([+-]?\d+(\.\d*)?)(.*)$/);
            b[e] = [n[1], parseFloat(n[2]), n[4] || "px"]
        };
        if (b.length == 1 && h.inArray(b[0], ["top", "bottom"]) > -1) {
            b[1] = b[0];
            b[0] = "50%"
        }
        d(0);
        d(1);
        return b
    }

    function l(a) {
        if (!a.set) {
            g(a)
        }
        h(a.elem).css("background-position", ((a.pos * (a.end[0][1] - a.start[0][1]) + a.start[0][1]) + a.end[0][2]) + " " + ((a.pos * (a.end[1][1] - a.start[1][1]) + a.start[1][1]) + a.end[1][2]))
    }

    function g(b) {
        b.start = f(h(b.elem).css("backgroundPosition"));
        b.end = f(b.end);
        for (var a = 0; a < b.end.length; a++) {
            if (b.end[a][0]) {
                b.end[a][1] = b.start[a][1] + (b.end[a][0] == "-=" ? -1 : +1) * b.end[a][1]
            }
        }
        b.set = true
    }
})(jQuery);
(function (v) {
    v.colorpicker = new function () {
        this.regional = [];
        this.regional[""] = {
            ok: "OK",
            cancel: "Cancel",
            none: "None",
            button: "Color",
            title: "Pick a color",
            transparent: "Transparent",
            hsvH: "H",
            hsvS: "S",
            hsvV: "V",
            rgbR: "R",
            rgbG: "G",
            rgbB: "B",
            labL: "L",
            labA: "a",
            labB: "b",
            hslH: "H",
            hslS: "S",
            hslL: "L",
            cmykC: "C",
            cmykM: "M",
            cmykY: "Y",
            cmykK: "K",
            alphaA: "A"
        };
        this.swatches = [];
        this.swatches.html = {
            black: {r: 0, g: 0, b: 0},
            dimgray: {
                r: 0.4117647058823529,
                g: 0.4117647058823529,
                b: 0.4117647058823529
            },
            gray: {
                r: 0.5019607843137255,
                g: 0.5019607843137255,
                b: 0.5019607843137255
            },
            darkgray: {
                r: 0.6627450980392157,
                g: 0.6627450980392157,
                b: 0.6627450980392157
            },
            silver: {
                r: 0.7529411764705882,
                g: 0.7529411764705882,
                b: 0.7529411764705882
            },
            lightgrey: {
                r: 0.8274509803921568,
                g: 0.8274509803921568,
                b: 0.8274509803921568
            },
            gainsboro: {
                r: 0.8627450980392157,
                g: 0.8627450980392157,
                b: 0.8627450980392157
            },
            whitesmoke: {
                r: 0.9607843137254902,
                g: 0.9607843137254902,
                b: 0.9607843137254902
            },
            white: {r: 1, g: 1, b: 1},
            rosybrown: {
                r: 0.7372549019607844,
                g: 0.5607843137254902,
                b: 0.5607843137254902
            },
            indianred: {
                r: 0.803921568627451,
                g: 0.3607843137254902,
                b: 0.3607843137254902
            },
            brown: {
                r: 0.6470588235294118,
                g: 0.16470588235294117,
                b: 0.16470588235294117
            },
            firebrick: {
                r: 0.6980392156862745,
                g: 0.13333333333333333,
                b: 0.13333333333333333
            },
            lightcoral: {
                r: 0.9411764705882353,
                g: 0.5019607843137255,
                b: 0.5019607843137255
            },
            maroon: {r: 0.5019607843137255, g: 0, b: 0},
            darkred: {r: 0.5450980392156862, g: 0, b: 0},
            red: {r: 1, g: 0, b: 0},
            snow: {r: 1, g: 0.9803921568627451, b: 0.9803921568627451},
            salmon: {
                r: 0.9803921568627451,
                g: 0.5019607843137255,
                b: 0.4470588235294118
            },
            mistyrose: {r: 1, g: 0.8941176470588236, b: 0.8823529411764706},
            tomato: {r: 1, g: 0.38823529411764707, b: 0.2784313725490196},
            darksalmon: {
                r: 0.9137254901960784,
                g: 0.5882352941176471,
                b: 0.47843137254901963
            },
            orangered: {r: 1, g: 0.27058823529411763, b: 0},
            coral: {r: 1, g: 0.4980392156862745, b: 0.3137254901960784},
            lightsalmon: {r: 1, g: 0.6274509803921569, b: 0.47843137254901963},
            sienna: {
                r: 0.6274509803921569,
                g: 0.3215686274509804,
                b: 0.17647058823529413
            },
            seashell: {r: 1, g: 0.9607843137254902, b: 0.9333333333333333},
            chocolate: {
                r: 0.8235294117647058,
                g: 0.4117647058823529,
                b: 0.11764705882352941
            },
            saddlebrown: {
                r: 0.5450980392156862,
                g: 0.27058823529411763,
                b: 0.07450980392156863
            },
            sandybrown: {
                r: 0.9568627450980393,
                g: 0.6431372549019608,
                b: 0.3764705882352941
            },
            peachpuff: {r: 1, g: 0.8549019607843137, b: 0.7254901960784313},
            peru: {
                r: 0.803921568627451,
                g: 0.5215686274509804,
                b: 0.24705882352941178
            },
            linen: {
                r: 0.9803921568627451,
                g: 0.9411764705882353,
                b: 0.9019607843137255
            },
            darkorange: {r: 1, g: 0.5490196078431373, b: 0},
            bisque: {r: 1, g: 0.8941176470588236, b: 0.7686274509803922},
            burlywood: {
                r: 0.8705882352941177,
                g: 0.7215686274509804,
                b: 0.5294117647058824
            },
            tan: {
                r: 0.8235294117647058,
                g: 0.7058823529411765,
                b: 0.5490196078431373
            },
            antiquewhite: {
                r: 0.9803921568627451,
                g: 0.9215686274509803,
                b: 0.8431372549019608
            },
            navajowhite: {r: 1, g: 0.8705882352941177, b: 0.6784313725490196},
            blanchedalmond: {r: 1, g: 0.9215686274509803, b: 0.803921568627451},
            papayawhip: {r: 1, g: 0.9372549019607843, b: 0.8352941176470589},
            orange: {r: 1, g: 0.6470588235294118, b: 0},
            moccasin: {r: 1, g: 0.8941176470588236, b: 0.7098039215686275},
            wheat: {
                r: 0.9607843137254902,
                g: 0.8705882352941177,
                b: 0.7019607843137254
            },
            oldlace: {
                r: 0.9921568627450981,
                g: 0.9607843137254902,
                b: 0.9019607843137255
            },
            floralwhite: {r: 1, g: 0.9803921568627451, b: 0.9411764705882353},
            goldenrod: {
                r: 0.8549019607843137,
                g: 0.6470588235294118,
                b: 0.12549019607843137
            },
            darkgoldenrod: {
                r: 0.7215686274509804,
                g: 0.5254901960784314,
                b: 0.043137254901960784
            },
            cornsilk: {r: 1, g: 0.9725490196078431, b: 0.8627450980392157},
            gold: {r: 1, g: 0.8431372549019608, b: 0},
            palegoldenrod: {
                r: 0.9333333333333333,
                g: 0.9098039215686274,
                b: 0.6666666666666666
            },
            khaki: {
                r: 0.9411764705882353,
                g: 0.9019607843137255,
                b: 0.5490196078431373
            },
            lemonchiffon: {r: 1, g: 0.9803921568627451, b: 0.803921568627451},
            darkkhaki: {
                r: 0.7411764705882353,
                g: 0.7176470588235294,
                b: 0.4196078431372549
            },
            beige: {
                r: 0.9607843137254902,
                g: 0.9607843137254902,
                b: 0.8627450980392157
            },
            lightgoldenrodyellow: {
                r: 0.9803921568627451,
                g: 0.9803921568627451,
                b: 0.8235294117647058
            },
            olive: {r: 0.5019607843137255, g: 0.5019607843137255, b: 0},
            yellow: {r: 1, g: 1, b: 0},
            lightyellow: {r: 1, g: 1, b: 0.8784313725490196},
            ivory: {r: 1, g: 1, b: 0.9411764705882353},
            olivedrab: {
                r: 0.4196078431372549,
                g: 0.5568627450980392,
                b: 0.13725490196078433
            },
            yellowgreen: {
                r: 0.6039215686274509,
                g: 0.803921568627451,
                b: 0.19607843137254902
            },
            darkolivegreen: {
                r: 0.3333333333333333,
                g: 0.4196078431372549,
                b: 0.1843137254901961
            },
            greenyellow: {r: 0.6784313725490196, g: 1, b: 0.1843137254901961},
            lawngreen: {r: 0.48627450980392156, g: 0.9882352941176471, b: 0},
            chartreuse: {r: 0.4980392156862745, g: 1, b: 0},
            darkseagreen: {
                r: 0.5607843137254902,
                g: 0.7372549019607844,
                b: 0.5607843137254902
            },
            forestgreen: {
                r: 0.13333333333333333,
                g: 0.5450980392156862,
                b: 0.13333333333333333
            },
            limegreen: {
                r: 0.19607843137254902,
                g: 0.803921568627451,
                b: 0.19607843137254902
            },
            lightgreen: {
                r: 0.5647058823529412,
                g: 0.9333333333333333,
                b: 0.5647058823529412
            },
            palegreen: {
                r: 0.596078431372549,
                g: 0.984313725490196,
                b: 0.596078431372549
            },
            darkgreen: {r: 0, g: 0.39215686274509803, b: 0},
            green: {r: 0, g: 0.5019607843137255, b: 0},
            lime: {r: 0, g: 1, b: 0},
            honeydew: {r: 0.9411764705882353, g: 1, b: 0.9411764705882353},
            mediumseagreen: {
                r: 0.23529411764705882,
                g: 0.7019607843137254,
                b: 0.44313725490196076
            },
            seagreen: {
                r: 0.1803921568627451,
                g: 0.5450980392156862,
                b: 0.3411764705882353
            },
            springgreen: {r: 0, g: 1, b: 0.4980392156862745},
            mintcream: {r: 0.9607843137254902, g: 1, b: 0.9803921568627451},
            mediumspringgreen: {
                r: 0,
                g: 0.9803921568627451,
                b: 0.6039215686274509
            },
            mediumaquamarine: {
                r: 0.4,
                g: 0.803921568627451,
                b: 0.6666666666666666
            },
            aquamarine: {r: 0.4980392156862745, g: 1, b: 0.8313725490196079},
            turquoise: {
                r: 0.25098039215686274,
                g: 0.8784313725490196,
                b: 0.8156862745098039
            },
            lightseagreen: {
                r: 0.12549019607843137,
                g: 0.6980392156862745,
                b: 0.6666666666666666
            },
            mediumturquoise: {
                r: 0.2823529411764706,
                g: 0.8196078431372549,
                b: 0.8
            },
            darkslategray: {
                r: 0.1843137254901961,
                g: 0.30980392156862746,
                b: 0.30980392156862746
            },
            paleturquoise: {
                r: 0.6862745098039216,
                g: 0.9333333333333333,
                b: 0.9333333333333333
            },
            teal: {r: 0, g: 0.5019607843137255, b: 0.5019607843137255},
            darkcyan: {r: 0, g: 0.5450980392156862, b: 0.5450980392156862},
            darkturquoise: {r: 0, g: 0.807843137254902, b: 0.8196078431372549},
            aqua: {r: 0, g: 1, b: 1},
            cyan: {r: 0, g: 1, b: 1},
            lightcyan: {r: 0.8784313725490196, g: 1, b: 1},
            azure: {r: 0.9411764705882353, g: 1, b: 1},
            cadetblue: {
                r: 0.37254901960784315,
                g: 0.6196078431372549,
                b: 0.6274509803921569
            },
            powderblue: {
                r: 0.6901960784313725,
                g: 0.8784313725490196,
                b: 0.9019607843137255
            },
            lightblue: {
                r: 0.6784313725490196,
                g: 0.8470588235294118,
                b: 0.9019607843137255
            },
            deepskyblue: {r: 0, g: 0.7490196078431373, b: 1},
            skyblue: {
                r: 0.5294117647058824,
                g: 0.807843137254902,
                b: 0.9215686274509803
            },
            lightskyblue: {
                r: 0.5294117647058824,
                g: 0.807843137254902,
                b: 0.9803921568627451
            },
            steelblue: {
                r: 0.27450980392156865,
                g: 0.5098039215686274,
                b: 0.7058823529411765
            },
            aliceblue: {r: 0.9411764705882353, g: 0.9725490196078431, b: 1},
            dodgerblue: {r: 0.11764705882352941, g: 0.5647058823529412, b: 1},
            slategray: {
                r: 0.4392156862745098,
                g: 0.5019607843137255,
                b: 0.5647058823529412
            },
            lightslategray: {
                r: 0.4666666666666667,
                g: 0.5333333333333333,
                b: 0.6
            },
            lightsteelblue: {
                r: 0.6901960784313725,
                g: 0.7686274509803922,
                b: 0.8705882352941177
            },
            cornflowerblue: {
                r: 0.39215686274509803,
                g: 0.5843137254901961,
                b: 0.9294117647058824
            },
            royalblue: {
                r: 0.2549019607843137,
                g: 0.4117647058823529,
                b: 0.8823529411764706
            },
            midnightblue: {
                r: 0.09803921568627451,
                g: 0.09803921568627451,
                b: 0.4392156862745098
            },
            lavender: {
                r: 0.9019607843137255,
                g: 0.9019607843137255,
                b: 0.9803921568627451
            },
            navy: {r: 0, g: 0, b: 0.5019607843137255},
            darkblue: {r: 0, g: 0, b: 0.5450980392156862},
            mediumblue: {r: 0, g: 0, b: 0.803921568627451},
            blue: {r: 0, g: 0, b: 1},
            ghostwhite: {r: 0.9725490196078431, g: 0.9725490196078431, b: 1},
            darkslateblue: {
                r: 0.2823529411764706,
                g: 0.23921568627450981,
                b: 0.5450980392156862
            },
            slateblue: {
                r: 0.41568627450980394,
                g: 0.35294117647058826,
                b: 0.803921568627451
            },
            mediumslateblue: {
                r: 0.4823529411764706,
                g: 0.40784313725490196,
                b: 0.9333333333333333
            },
            mediumpurple: {
                r: 0.5764705882352941,
                g: 0.4392156862745098,
                b: 0.8588235294117647
            },
            blueviolet: {
                r: 0.5411764705882353,
                g: 0.16862745098039217,
                b: 0.8862745098039215
            },
            indigo: {r: 0.29411764705882354, g: 0, b: 0.5098039215686274},
            darkorchid: {r: 0.6, g: 0.19607843137254902, b: 0.8},
            darkviolet: {r: 0.5803921568627451, g: 0, b: 0.8274509803921568},
            mediumorchid: {
                r: 0.7294117647058823,
                g: 0.3333333333333333,
                b: 0.8274509803921568
            },
            thistle: {
                r: 0.8470588235294118,
                g: 0.7490196078431373,
                b: 0.8470588235294118
            },
            plum: {
                r: 0.8666666666666667,
                g: 0.6274509803921569,
                b: 0.8666666666666667
            },
            violet: {
                r: 0.9333333333333333,
                g: 0.5098039215686274,
                b: 0.9333333333333333
            },
            purple: {r: 0.5019607843137255, g: 0, b: 0.5019607843137255},
            darkmagenta: {r: 0.5450980392156862, g: 0, b: 0.5450980392156862},
            magenta: {r: 1, g: 0, b: 1},
            fuchsia: {r: 1, g: 0, b: 1},
            orchid: {
                r: 0.8549019607843137,
                g: 0.4392156862745098,
                b: 0.8392156862745098
            },
            mediumvioletred: {
                r: 0.7803921568627451,
                g: 0.08235294117647059,
                b: 0.5215686274509804
            },
            deeppink: {r: 1, g: 0.0784313725490196, b: 0.5764705882352941},
            hotpink: {r: 1, g: 0.4117647058823529, b: 0.7058823529411765},
            palevioletred: {
                r: 0.8588235294117647,
                g: 0.4392156862745098,
                b: 0.5764705882352941
            },
            lavenderblush: {r: 1, g: 0.9411764705882353, b: 0.9607843137254902},
            crimson: {
                r: 0.8627450980392157,
                g: 0.0784313725490196,
                b: 0.23529411764705882
            },
            pink: {r: 1, g: 0.7529411764705882, b: 0.796078431372549},
            lightpink: {r: 1, g: 0.7137254901960784, b: 0.7568627450980392}
        }
    };
    var m = 0, u = '<div class="ui-colorpicker ui-colorpicker-dialog ui-dialog ui-widget ui-widget-content ui-corner-all" style="display: none;"></div>', s = '<div class="ui-colorpicker ui-colorpicker-inline ui-dialog ui-widget ui-widget-content ui-corner-all"></div>', p = {
        full: ["header", "map", "bar", "hex", "hsv", "rgb", "alpha", "lab", "cmyk", "preview", "swatches", "footer"],
        popup: ["map", "bar", "hex", "hsv", "rgb", "alpha", "preview", "footer"],
        draggable: ["header", "map", "bar", "hex", "hsv", "rgb", "alpha", "preview", "footer"],
        inline: ["map", "bar", "hex", "hsv", "rgb", "alpha", "preview"]
    }, o = function (b) {
        var a = Math.round(b).toString(16);
        if (a.length === 1) {
            a = ("0" + a)
        }
        return a.toLowerCase()
    }, r = function (b) {
        var a, c;
        c = /^#?([a-fA-F0-9]{1,6})$/.exec(b);
        if (c) {
            a = parseInt(c[1], 16);
            return new w(((a >> 16) & 255) / 255, ((a >> 8) & 255) / 255, (a & 255) / 255)
        }
        return new w()
    }, n = function (b, K) {
        var h, f, x, l, I, g, H, J, a, d, c, e, k, y;
        b.sort(function (A, B) {
            if (A.pos[1] == B.pos[1]) {
                return A.pos[0] - B.pos[0]
            }
            return A.pos[1] - B.pos[1]
        });
        l = 0;
        I = 0;
        v.each(b, function (A, B) {
            l = Math.max(l, B.pos[0] + B.pos[2]);
            I = Math.max(I, B.pos[1] + B.pos[3])
        });
        h = [];
        for (f = 0; f < l; ++f) {
            h.push([])
        }
        H = [];
        g = [];
        v.each(b, function (A, B) {
            for (f = 0; f < B.pos[2]; f += 1) {
                g[B.pos[0] + f] = true
            }
            for (x = 0; x < B.pos[3]; x += 1) {
                H[B.pos[1] + x] = true
            }
        });
        d = "";
        a = b[J = 0];
        for (x = 0; x < I; ++x) {
            d += "<tr>";
            for (f = 0; f < l; f) {
                if (typeof a !== "undefined" && f == a.pos[0] && x == a.pos[1]) {
                    d += K(a, f, x);
                    for (e = 0; e < a.pos[3]; e += 1) {
                        for (c = 0; c < a.pos[2]; c += 1) {
                            h[f + c][x + e] = true
                        }
                    }
                    f += a.pos[2];
                    a = b[++J]
                } else {
                    k = 0;
                    y = false;
                    while (f < l && h[f][x] === undefined && (a === undefined || x < a.pos[1] || (x == a.pos[1] && f < a.pos[0]))) {
                        if (g[f] === true) {
                            k += 1
                        }
                        y = true;
                        f += 1
                    }
                    if (k > 0) {
                        d += '<td colspan="' + k + '"></td>'
                    } else {
                        if (!y) {
                            f += 1
                        }
                    }
                }
            }
            d += "</tr>"
        }
        return '<table cellspacing="0" cellpadding="0" border="0"><tbody>' + d + "</tbody></table>"
    }, q = {
        header: function (c) {
            var d = this, b = null, a = function () {
                var e = c.options.title || c._getRegional("title"), f = '<span class="ui-dialog-title">' + e + "</span>";
                if (!c.inline && c.options.showCloseButton) {
                    f += '<a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a>'
                }
                return '<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">' + f + "</div>"
            };
            this.init = function () {
                b = v(a()).prependTo(c.dialog);
                var e = v(".ui-dialog-titlebar-close", b);
                c._hoverable(e);
                c._focusable(e);
                e.click(function (f) {
                    f.preventDefault();
                    c.close()
                });
                if (!c.inline && c.options.draggable) {
                    c.dialog.draggable({handle: b})
                }
            }
        }, map: function (c) {
            var e = this, b = null, d = null, h, f, g, a;
            h = function (H) {
                if (!c.opened) {
                    return
                }
                var y = v(".ui-colorpicker-map-layer-pointer", b), G = y.offset(), k = y.width(), l = y.height(), x = H.pageX - G.left, F = H.pageY - G.top;
                if (x >= 0 && x < k && F >= 0 && F < l) {
                    H.stopImmediatePropagation();
                    H.preventDefault();
                    b.unbind("mousedown", h);
                    v(document).bind("mouseup", f);
                    v(document).bind("mousemove", g);
                    g(H)
                }
            };
            f = function (k) {
                k.stopImmediatePropagation();
                k.preventDefault();
                v(document).unbind("mouseup", f);
                v(document).unbind("mousemove", g);
                b.bind("mousedown", h)
            };
            g = function (H) {
                H.stopImmediatePropagation();
                H.preventDefault();
                if (H.pageX === e.x && H.pageY === e.y) {
                    return
                }
                e.x = H.pageX;
                e.y = H.pageY;
                var y = v(".ui-colorpicker-map-layer-pointer", b), G = y.offset(), k = y.width(), l = y.height(), x = H.pageX - G.left, F = H.pageY - G.top;
                x = Math.max(0, Math.min(x / k, 1));
                F = Math.max(0, Math.min(F / l, 1));
                switch (c.mode) {
                    case"h":
                        c.color.setHSV(null, x, 1 - F);
                        break;
                    case"s":
                    case"a":
                        c.color.setHSV(x, null, 1 - F);
                        break;
                    case"v":
                        c.color.setHSV(x, 1 - F, null);
                        break;
                    case"r":
                        c.color.setRGB(null, 1 - F, x);
                        break;
                    case"g":
                        c.color.setRGB(1 - F, null, x);
                        break;
                    case"b":
                        c.color.setRGB(x, 1 - F, null);
                        break
                }
                c._change()
            };
            a = function () {
                var k = '<div class="ui-colorpicker-map ui-colorpicker-border"><span class="ui-colorpicker-map-layer-1">&nbsp;</span><span class="ui-colorpicker-map-layer-2">&nbsp;</span>' + (c.options.alpha ? '<span class="ui-colorpicker-map-layer-alpha">&nbsp;</span>' : "") + '<span class="ui-colorpicker-map-layer-pointer"><span class="ui-colorpicker-map-pointer"></span></span></div>';
                return k
            };
            this.update = function () {
                switch (c.mode) {
                    case"h":
                        v(".ui-colorpicker-map-layer-1", b).css({
                            "background-position": "0 0",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-map-layer-2", b).hide();
                        break;
                    case"s":
                    case"a":
                        v(".ui-colorpicker-map-layer-1", b).css({
                            "background-position": "0 -260px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-map-layer-2", b).css({
                            "background-position": "0 -520px",
                            opacity: ""
                        }).show();
                        break;
                    case"v":
                        v(b).css("background-color", "black");
                        v(".ui-colorpicker-map-layer-1", b).css({
                            "background-position": "0 -780px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-map-layer-2", b).hide();
                        break;
                    case"r":
                        v(".ui-colorpicker-map-layer-1", b).css({
                            "background-position": "0 -1040px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-map-layer-2", b).css({
                            "background-position": "0 -1300px",
                            opacity: ""
                        }).show();
                        break;
                    case"g":
                        v(".ui-colorpicker-map-layer-1", b).css({
                            "background-position": "0 -1560px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-map-layer-2", b).css({
                            "background-position": "0 -1820px",
                            opacity: ""
                        }).show();
                        break;
                    case"b":
                        v(".ui-colorpicker-map-layer-1", b).css({
                            "background-position": "0 -2080px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-map-layer-2", b).css({
                            "background-position": "0 -2340px",
                            opacity: ""
                        }).show();
                        break
                }
                e.repaint()
            };
            this.repaint = function () {
                var k = v(".ui-colorpicker-map-layer-pointer", b), x = 0, l = 0;
                switch (c.mode) {
                    case"h":
                        x = c.color.getHSV().s * k.width();
                        l = (1 - c.color.getHSV().v) * k.width();
                        v(b).css("background-color", c.color.copy().normalize().toCSS());
                        break;
                    case"s":
                    case"a":
                        x = c.color.getHSV().h * k.width();
                        l = (1 - c.color.getHSV().v) * k.width();
                        v(".ui-colorpicker-map-layer-2", b).css("opacity", 1 - c.color.getHSV().s);
                        break;
                    case"v":
                        x = c.color.getHSV().h * k.width();
                        l = (1 - c.color.getHSV().s) * k.width();
                        v(".ui-colorpicker-map-layer-1", b).css("opacity", c.color.getHSV().v);
                        break;
                    case"r":
                        x = c.color.getRGB().b * k.width();
                        l = (1 - c.color.getRGB().g) * k.width();
                        v(".ui-colorpicker-map-layer-2", b).css("opacity", c.color.getRGB().r);
                        break;
                    case"g":
                        x = c.color.getRGB().b * k.width();
                        l = (1 - c.color.getRGB().r) * k.width();
                        v(".ui-colorpicker-map-layer-2", b).css("opacity", c.color.getRGB().g);
                        break;
                    case"b":
                        x = c.color.getRGB().r * k.width();
                        l = (1 - c.color.getRGB().g) * k.width();
                        v(".ui-colorpicker-map-layer-2", b).css("opacity", c.color.getRGB().b);
                        break
                }
                if (c.options.alpha) {
                    v(".ui-colorpicker-map-layer-alpha", b).css("opacity", 1 - c.color.getAlpha())
                }
                v(".ui-colorpicker-map-pointer", b).css({
                    left: x - 7,
                    top: l - 7
                })
            };
            this.init = function () {
                b = v(a()).appendTo(v(".ui-colorpicker-map-container", c.dialog));
                b.bind("mousedown", h)
            }
        }, bar: function (c) {
            var d = this, b = null, g, e, f, a;
            g = function (h) {
                if (!c.opened) {
                    return
                }
                var y = v(".ui-colorpicker-bar-layer-pointer", b), F = y.offset(), k = y.width(), l = y.height(), x = h.pageX - F.left, E = h.pageY - F.top;
                if (x >= 0 && x < k && E >= 0 && E < l) {
                    h.stopImmediatePropagation();
                    h.preventDefault();
                    b.unbind("mousedown", g);
                    v(document).bind("mouseup", e);
                    v(document).bind("mousemove", f);
                    f(h)
                }
            };
            e = function (h) {
                h.stopImmediatePropagation();
                h.preventDefault();
                v(document).unbind("mouseup", e);
                v(document).unbind("mousemove", f);
                b.bind("mousedown", g)
            };
            f = function (y) {
                y.stopImmediatePropagation();
                y.preventDefault();
                if (y.pageY === d.y) {
                    return
                }
                d.y = y.pageY;
                var k = v(".ui-colorpicker-bar-layer-pointer", b), l = k.offset(), B = k.height(), h = y.pageY - l.top;
                h = Math.max(0, Math.min(h / B, 1));
                switch (c.mode) {
                    case"h":
                        c.color.setHSV(1 - h, null, null);
                        break;
                    case"s":
                        c.color.setHSV(null, 1 - h, null);
                        break;
                    case"v":
                        c.color.setHSV(null, null, 1 - h);
                        break;
                    case"r":
                        c.color.setRGB(1 - h, null, null);
                        break;
                    case"g":
                        c.color.setRGB(null, 1 - h, null);
                        break;
                    case"b":
                        c.color.setRGB(null, null, 1 - h);
                        break;
                    case"a":
                        c.color.setAlpha(1 - h);
                        break
                }
                c._change()
            };
            a = function () {
                var h = '<div class="ui-colorpicker-bar ui-colorpicker-border"><span class="ui-colorpicker-bar-layer-1">&nbsp;</span><span class="ui-colorpicker-bar-layer-2">&nbsp;</span><span class="ui-colorpicker-bar-layer-3">&nbsp;</span><span class="ui-colorpicker-bar-layer-4">&nbsp;</span>';
                if (c.options.alpha) {
                    h += '<span class="ui-colorpicker-bar-layer-alpha">&nbsp;</span><span class="ui-colorpicker-bar-layer-alphabar">&nbsp;</span>'
                }
                h += '<span class="ui-colorpicker-bar-layer-pointer"><span class="ui-colorpicker-bar-pointer"></span></span></div>';
                return h
            };
            this.update = function () {
                switch (c.mode) {
                    case"h":
                    case"s":
                    case"v":
                    case"r":
                    case"g":
                    case"b":
                        v(".ui-colorpicker-bar-layer-alpha", b).show();
                        v(".ui-colorpicker-bar-layer-alphabar", b).hide();
                        break;
                    case"a":
                        v(".ui-colorpicker-bar-layer-alpha", b).hide();
                        v(".ui-colorpicker-bar-layer-alphabar", b).show();
                        break
                }
                switch (c.mode) {
                    case"h":
                        v(".ui-colorpicker-bar-layer-1", b).css({
                            "background-position": "0 0",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-2", b).hide();
                        v(".ui-colorpicker-bar-layer-3", b).hide();
                        v(".ui-colorpicker-bar-layer-4", b).hide();
                        break;
                    case"s":
                        v(".ui-colorpicker-bar-layer-1", b).css({
                            "background-position": "0 -260px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-2", b).css({
                            "background-position": "0 -520px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-3", b).hide();
                        v(".ui-colorpicker-bar-layer-4", b).hide();
                        break;
                    case"v":
                        v(".ui-colorpicker-bar-layer-1", b).css({
                            "background-position": "0 -520px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-2", b).hide();
                        v(".ui-colorpicker-bar-layer-3", b).hide();
                        v(".ui-colorpicker-bar-layer-4", b).hide();
                        break;
                    case"r":
                        v(".ui-colorpicker-bar-layer-1", b).css({
                            "background-position": "0 -1560px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-2", b).css({
                            "background-position": "0 -1300px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-3", b).css({
                            "background-position": "0 -780px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-4", b).css({
                            "background-position": "0 -1040px",
                            opacity: ""
                        }).show();
                        break;
                    case"g":
                        v(".ui-colorpicker-bar-layer-1", b).css({
                            "background-position": "0 -2600px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-2", b).css({
                            "background-position": "0 -2340px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-3", b).css({
                            "background-position": "0 -1820px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-4", b).css({
                            "background-position": "0 -2080px",
                            opacity: ""
                        }).show();
                        break;
                    case"b":
                        v(".ui-colorpicker-bar-layer-1", b).css({
                            "background-position": "0 -3640px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-2", b).css({
                            "background-position": "0 -3380px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-3", b).css({
                            "background-position": "0 -2860px",
                            opacity: ""
                        }).show();
                        v(".ui-colorpicker-bar-layer-4", b).css({
                            "background-position": "0 -3120px",
                            opacity: ""
                        }).show();
                        break;
                    case"a":
                        v(".ui-colorpicker-bar-layer-1", b).hide();
                        v(".ui-colorpicker-bar-layer-2", b).hide();
                        v(".ui-colorpicker-bar-layer-3", b).hide();
                        v(".ui-colorpicker-bar-layer-4", b).hide();
                        break
                }
                d.repaint()
            };
            this.repaint = function () {
                var h = v(".ui-colorpicker-bar-layer-pointer", b), k = 0;
                switch (c.mode) {
                    case"h":
                        k = (1 - c.color.getHSV().h) * h.height();
                        break;
                    case"s":
                        k = (1 - c.color.getHSV().s) * h.height();
                        v(".ui-colorpicker-bar-layer-2", b).css("opacity", 1 - c.color.getHSV().v);
                        v(b).css("background-color", c.color.copy().normalize().toCSS());
                        break;
                    case"v":
                        k = (1 - c.color.getHSV().v) * h.height();
                        v(b).css("background-color", c.color.copy().normalize().toCSS());
                        break;
                    case"r":
                        k = (1 - c.color.getRGB().r) * h.height();
                        v(".ui-colorpicker-bar-layer-2", b).css("opacity", Math.max(0, (c.color.getRGB().b - c.color.getRGB().g)));
                        v(".ui-colorpicker-bar-layer-3", b).css("opacity", Math.max(0, (c.color.getRGB().g - c.color.getRGB().b)));
                        v(".ui-colorpicker-bar-layer-4", b).css("opacity", Math.min(c.color.getRGB().b, c.color.getRGB().g));
                        break;
                    case"g":
                        k = (1 - c.color.getRGB().g) * h.height();
                        v(".ui-colorpicker-bar-layer-2", b).css("opacity", Math.max(0, (c.color.getRGB().b - c.color.getRGB().r)));
                        v(".ui-colorpicker-bar-layer-3", b).css("opacity", Math.max(0, (c.color.getRGB().r - c.color.getRGB().b)));
                        v(".ui-colorpicker-bar-layer-4", b).css("opacity", Math.min(c.color.getRGB().r, c.color.getRGB().b));
                        break;
                    case"b":
                        k = (1 - c.color.getRGB().b) * h.height();
                        v(".ui-colorpicker-bar-layer-2", b).css("opacity", Math.max(0, (c.color.getRGB().r - c.color.getRGB().g)));
                        v(".ui-colorpicker-bar-layer-3", b).css("opacity", Math.max(0, (c.color.getRGB().g - c.color.getRGB().r)));
                        v(".ui-colorpicker-bar-layer-4", b).css("opacity", Math.min(c.color.getRGB().r, c.color.getRGB().g));
                        break;
                    case"a":
                        k = (1 - c.color.getAlpha()) * h.height();
                        v(b).css("background-color", c.color.copy().normalize().toCSS());
                        break
                }
                if (c.mode !== "a") {
                    v(".ui-colorpicker-bar-layer-alpha", b).css("opacity", 1 - c.color.getAlpha())
                }
                v(".ui-colorpicker-bar-pointer", b).css("top", k - 3)
            };
            this.init = function () {
                b = v(a()).appendTo(v(".ui-colorpicker-bar-container", c.dialog));
                b.bind("mousedown", g)
            }
        }, preview: function (c) {
            var d = this, b = null, a;
            a = function () {
                return '<div class="ui-colorpicker-preview ui-colorpicker-border"><div class="ui-colorpicker-preview-initial"><div class="ui-colorpicker-preview-initial-alpha"></div></div><div class="ui-colorpicker-preview-current"><div class="ui-colorpicker-preview-current-alpha"></div></div></div>'
            };
            this.init = function () {
                b = v(a()).appendTo(v(".ui-colorpicker-preview-container", c.dialog));
                v(".ui-colorpicker-preview-initial", b).click(function () {
                    c.color = c.currentColor.copy();
                    c._change()
                })
            };
            this.update = function () {
                if (c.options.alpha) {
                    v(".ui-colorpicker-preview-initial-alpha, .ui-colorpicker-preview-current-alpha", b).show()
                } else {
                    v(".ui-colorpicker-preview-initial-alpha, .ui-colorpicker-preview-current-alpha", b).hide()
                }
                this.repaint()
            };
            this.repaint = function () {
                v(".ui-colorpicker-preview-initial", b).css("background-color", c.currentColor.toCSS()).attr("title", c.currentColor.toHex());
                v(".ui-colorpicker-preview-initial-alpha", b).css("opacity", 1 - c.currentColor.getAlpha());
                v(".ui-colorpicker-preview-current", b).css("background-color", c.color.toCSS()).attr("title", c.color.toHex());
                v(".ui-colorpicker-preview-current-alpha", b).css("opacity", 1 - c.color.getAlpha())
            }
        }, hsv: function (c) {
            var d = this, b = null, a;
            a = function () {
                var e = "";
                if (c.options.hsv) {
                    e += '<div class="ui-colorpicker-hsv-h"><input class="ui-colorpicker-mode" type="radio" value="h"/><label>' + c._getRegional("hsvH") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="360" size="10"/><span class="ui-colorpicker-unit">&deg;</span></div><div class="ui-colorpicker-hsv-s"><input class="ui-colorpicker-mode" type="radio" value="s"/><label>' + c._getRegional("hsvS") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="100" size="10"/><span class="ui-colorpicker-unit">%</span></div><div class="ui-colorpicker-hsv-v"><input class="ui-colorpicker-mode" type="radio" value="v"/><label>' + c._getRegional("hsvV") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="100" size="10"/><span class="ui-colorpicker-unit">%</span></div>'
                }
                return '<div class="ui-colorpicker-hsv">' + e + "</div>"
            };
            this.init = function () {
                b = v(a()).appendTo(v(".ui-colorpicker-hsv-container", c.dialog));
                v(".ui-colorpicker-mode", b).click(function () {
                    c.mode = v(this).val();
                    c._updateAllParts()
                });
                v(".ui-colorpicker-number", b).bind("change keyup", function () {
                    c.color.setHSV(v(".ui-colorpicker-hsv-h .ui-colorpicker-number", b).val() / 360, v(".ui-colorpicker-hsv-s .ui-colorpicker-number", b).val() / 100, v(".ui-colorpicker-hsv-v .ui-colorpicker-number", b).val() / 100);
                    c._change()
                })
            };
            this.repaint = function () {
                var e = c.color.getHSV();
                e.h *= 360;
                e.s *= 100;
                e.v *= 100;
                v.each(e, function (g, f) {
                    var h = v(".ui-colorpicker-hsv-" + g + " .ui-colorpicker-number", b);
                    f = Math.round(f);
                    if (h.val() !== f) {
                        h.val(f)
                    }
                })
            };
            this.update = function () {
                v(".ui-colorpicker-mode", b).each(function () {
                    v(this).attr("checked", v(this).val() === c.mode)
                });
                this.repaint()
            }
        }, rgb: function (c) {
            var d = this, b = null, a;
            a = function () {
                var e = "";
                if (c.options.rgb) {
                    e += '<div class="ui-colorpicker-rgb-r"><input class="ui-colorpicker-mode" type="radio" value="r"/><label>' + c._getRegional("rgbR") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="255"/></div><div class="ui-colorpicker-rgb-g"><input class="ui-colorpicker-mode" type="radio" value="g"/><label>' + c._getRegional("rgbG") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="255"/></div><div class="ui-colorpicker-rgb-b"><input class="ui-colorpicker-mode" type="radio" value="b"/><label>' + c._getRegional("rgbB") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="255"/></div>'
                }
                return '<div class="ui-colorpicker-rgb">' + e + "</div>"
            };
            this.init = function () {
                b = v(a()).appendTo(v(".ui-colorpicker-rgb-container", c.dialog));
                v(".ui-colorpicker-mode", b).click(function () {
                    c.mode = v(this).val();
                    c._updateAllParts()
                });
                v(".ui-colorpicker-number", b).bind("change keyup", function () {
                    c.color.setRGB(v(".ui-colorpicker-rgb-r .ui-colorpicker-number", b).val() / 255, v(".ui-colorpicker-rgb-g .ui-colorpicker-number", b).val() / 255, v(".ui-colorpicker-rgb-b .ui-colorpicker-number", b).val() / 255);
                    c._change()
                })
            };
            this.repaint = function () {
                v.each(c.color.getRGB(), function (f, e) {
                    var g = v(".ui-colorpicker-rgb-" + f + " .ui-colorpicker-number", b);
                    e = Math.round(e * 255);
                    if (g.val() !== e) {
                        g.val(e)
                    }
                })
            };
            this.update = function () {
                v(".ui-colorpicker-mode", b).each(function () {
                    v(this).attr("checked", v(this).val() === c.mode)
                });
                this.repaint()
            }
        }, lab: function (b) {
            var c = this, a = null, d = function () {
                var e = "";
                if (b.options.hsv) {
                    e += '<div class="ui-colorpicker-lab-l"><label>' + b._getRegional("labL") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="100"/></div><div class="ui-colorpicker-lab-a"><label>' + b._getRegional("labA") + '</label><input class="ui-colorpicker-number" type="number" min="-128" max="127"/></div><div class="ui-colorpicker-lab-b"><label>' + b._getRegional("labB") + '</label><input class="ui-colorpicker-number" type="number" min="-128" max="127"/></div>'
                }
                return '<div class="ui-colorpicker-lab">' + e + "</div>"
            };
            this.init = function () {
                var e = 0;
                a = v(d()).appendTo(v(".ui-colorpicker-lab-container", b.dialog));
                v(".ui-colorpicker-number", a).on("change keyup", function (f) {
                    b.color.setLAB(parseInt(v(".ui-colorpicker-lab-l .ui-colorpicker-number", a).val(), 10) / 100, (parseInt(v(".ui-colorpicker-lab-a .ui-colorpicker-number", a).val(), 10) + 128) / 255, (parseInt(v(".ui-colorpicker-lab-b .ui-colorpicker-number", a).val(), 10) + 128) / 255);
                    b._change()
                })
            };
            this.repaint = function () {
                var e = b.color.getLAB();
                e.l *= 100;
                e.a = (e.a * 255) - 128;
                e.b = (e.b * 255) - 128;
                v.each(e, function (g, f) {
                    var h = v(".ui-colorpicker-lab-" + g + " .ui-colorpicker-number", a);
                    f = Math.round(f);
                    if (h.val() !== f) {
                        h.val(f)
                    }
                })
            };
            this.update = function () {
                this.repaint()
            }
        }, cmyk: function (b) {
            var c = this, a = null, d = function () {
                var e = "";
                if (b.options.hsv) {
                    e += '<div class="ui-colorpicker-cmyk-c"><label>' + b._getRegional("cmykC") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="100"/><span class="ui-colorpicker-unit">%</span></div><div class="ui-colorpicker-cmyk-m"><label>' + b._getRegional("cmykM") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="100"/><span class="ui-colorpicker-unit">%</span></div><div class="ui-colorpicker-cmyk-y"><label>' + b._getRegional("cmykY") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="100"/><span class="ui-colorpicker-unit">%</span></div><div class="ui-colorpicker-cmyk-k"><label>' + b._getRegional("cmykK") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="100"/><span class="ui-colorpicker-unit">%</span></div>'
                }
                return '<div class="ui-colorpicker-cmyk">' + e + "</div>"
            };
            this.init = function () {
                a = v(d()).appendTo(v(".ui-colorpicker-cmyk-container", b.dialog));
                v(".ui-colorpicker-number", a).on("change keyup", function (e) {
                    b.color.setCMYK(parseInt(v(".ui-colorpicker-cmyk-c .ui-colorpicker-number", a).val(), 10) / 100, parseInt(v(".ui-colorpicker-cmyk-m .ui-colorpicker-number", a).val(), 10) / 100, parseInt(v(".ui-colorpicker-cmyk-y .ui-colorpicker-number", a).val(), 10) / 100, parseInt(v(".ui-colorpicker-cmyk-k .ui-colorpicker-number", a).val(), 10) / 100);
                    b._change()
                })
            };
            this.repaint = function () {
                v.each(b.color.getCMYK(), function (f, e) {
                    var g = v(".ui-colorpicker-cmyk-" + f + " .ui-colorpicker-number", a);
                    e = Math.round(e * 100);
                    if (g.val() !== e) {
                        g.val(e)
                    }
                })
            };
            this.update = function () {
                this.repaint()
            }
        }, alpha: function (c) {
            var d = this, b = null, a;
            a = function () {
                var e = "";
                if (c.options.alpha) {
                    e += '<div class="ui-colorpicker-a"><input class="ui-colorpicker-mode" name="mode" type="radio" value="a"/><label>' + c._getRegional("alphaA") + '</label><input class="ui-colorpicker-number" type="number" min="0" max="100"/><span class="ui-colorpicker-unit">%</span></div>'
                }
                return '<div class="ui-colorpicker-alpha">' + e + "</div>"
            };
            this.init = function () {
                b = v(a()).appendTo(v(".ui-colorpicker-alpha-container", c.dialog));
                v(".ui-colorpicker-mode", b).click(function () {
                    c.mode = v(this).val();
                    c._updateAllParts()
                });
                v(".ui-colorpicker-number", b).bind("change keyup", function () {
                    c.color.setAlpha(v(".ui-colorpicker-a .ui-colorpicker-number", b).val() / 100);
                    c._change()
                })
            };
            this.update = function () {
                v(".ui-colorpicker-mode", b).each(function () {
                    v(this).attr("checked", v(this).val() === c.mode)
                });
                this.repaint()
            };
            this.repaint = function () {
                var f = v(".ui-colorpicker-a .ui-colorpicker-number", b), e = Math.round(c.color.getAlpha() * 100);
                if (!f.is(":focus") && f.val() !== e) {
                    f.val(e)
                }
            }
        }, hex: function (c) {
            var d = this, b = null, a;
            a = function () {
                var e = "";
                if (c.options.alpha) {
                    e += '<input class="ui-colorpicker-hex-alpha" type="text" maxlength="2" size="2"/>'
                }
                e += '<input class="ui-colorpicker-hex-input" type="text" maxlength="6" size="6"/>';
                return '<div class="ui-colorpicker-hex"><label>#</label>' + e + "</div>"
            };
            this.init = function () {
                b = v(a()).appendTo(v(".ui-colorpicker-hex-container", c.dialog));
                v(".ui-colorpicker-hex-input", b).bind("change keydown keyup", function (f, g, e) {
                    if (/[^a-fA-F0-9]/.test(v(this).val())) {
                        v(this).val(v(this).val().replace(/[^a-fA-F0-9]/, ""))
                    }
                });
                v(".ui-colorpicker-hex-input", b).bind("change keyup", function () {
                    c.color = r(v(this).val()).setAlpha(c.color.getAlpha());
                    c._change()
                });
                v(".ui-colorpicker-hex-alpha", b).bind("change keydown keyup", function () {
                    if (/[^a-fA-F0-9]/.test(v(this).val())) {
                        v(this).val(v(this).val().replace(/[^a-fA-F0-9]/, ""))
                    }
                });
                v(".ui-colorpicker-hex-alpha", b).bind("change keyup", function () {
                    c.color.setAlpha(parseInt(v(".ui-colorpicker-hex-alpha", b).val(), 16) / 255);
                    c._change()
                })
            };
            this.update = function () {
                this.repaint()
            };
            this.repaint = function () {
                if (!v(".ui-colorpicker-hex-input", b).is(":focus")) {
                    v(".ui-colorpicker-hex-input", b).val(c.color.toHex(true))
                }
                if (!v(".ui-colorpicker-hex-alpha", b).is(":focus")) {
                    v(".ui-colorpicker-hex-alpha", b).val(o(c.color.getAlpha() * 255))
                }
            }
        }, swatches: function (b) {
            var c = this, a = null, d = function () {
                var e = "";
                v.each(b._getSwatches(), function (h, k) {
                    var f = new w(k.r, k.g, k.b), g = f.toCSS();
                    e += '<div class="ui-colorpicker-swatch" style="background-color:' + g + '" title="' + h + '"></div>'
                });
                return '<div class="ui-colorpicker-swatches ui-colorpicker-border" style="width:' + b.options.swatchesWidth + 'px">' + e + "</div>"
            };
            this.init = function () {
                a = v(d()).appendTo(v(".ui-colorpicker-swatches-container", b.dialog));
                v(".ui-colorpicker-swatch", a).click(function () {
                    b.color = b._parseColor(v(this).css("background-color"));
                    b._change()
                })
            }
        }, footer: function (b) {
            var c = this, e = null, a = "ui-colorpicker-special-transparent-" + m, f = "ui-colorpicker-special-none-" + m, d = function () {
                var g = "";
                if (b.options.alpha || (!b.inline && b.options.showNoneButton)) {
                    g += '<div class="ui-colorpicker-buttonset">';
                    if (b.options.alpha) {
                        g += '<input type="radio" name="ui-colorpicker-special" id="' + a + '" class="ui-colorpicker-special-transparent"/><label for="' + a + '">' + b._getRegional("transparent") + "</label>"
                    }
                    if (!b.inline && b.options.showNoneButton) {
                        g += '<input type="radio" name="ui-colorpicker-special" id="' + f + '" class="ui-colorpicker-special-none"><label for="' + f + '">' + b._getRegional("none") + "</label>"
                    }
                    g += "</div>"
                }
                if (!b.inline) {
                    g += '<div class="ui-dialog-buttonset">';
                    if (b.options.showCancelButton) {
                        g += '<button class="ui-colorpicker-cancel">' + b._getRegional("cancel") + "</button>"
                    }
                    g += '<button class="ui-colorpicker-ok">' + b._getRegional("ok") + "</button>";
                    g += "</div>"
                }
                return '<div class="ui-dialog-buttonpane ui-widget-content">' + g + "</div>"
            };
            this.init = function () {
                e = v(d()).appendTo(b.dialog);
                v(".ui-colorpicker-ok", e).button().click(function () {
                    b.close()
                });
                v(".ui-colorpicker-cancel", e).button().click(function () {
                    b.color = b.currentColor.copy();
                    b._change(b.color.set);
                    b.close()
                });
                v(".ui-colorpicker-buttonset", e).buttonset();
                v(".ui-colorpicker-special-color", e).click(function () {
                    b._change()
                });
                v("#" + f, e).click(function () {
                    b._change(false)
                });
                v("#" + a, e).click(function () {
                    b.color.setAlpha(0);
                    b._change()
                })
            };
            this.repaint = function () {
                if (!b.color.set) {
                    v(".ui-colorpicker-special-none", e).attr("checked", true).button("refresh")
                } else {
                    if (b.color.getAlpha() == 0) {
                        v(".ui-colorpicker-special-transparent", e).attr("checked", true).button("refresh")
                    } else {
                        v("input", e).attr("checked", false).button("refresh")
                    }
                }
                v(".ui-colorpicker-cancel", e).button(b.changed ? "enable" : "disable")
            };
            this.update = function () {
            }
        }
    }, w = function () {
        var F = {
            rgb: {r: 0, g: 0, b: 0},
            hsv: {h: 0, s: 0, v: 0},
            hsl: {h: 0, s: 0, l: 0},
            lab: {l: 0, a: 0, b: 0},
            cmyk: {c: 0, m: 0, y: 0, k: 1}
        }, c = 1, h = arguments, L = function (x) {
            if (isNaN(x) || x === null) {
                return 0
            }
            if (typeof x == "string") {
                x = parseInt(x, 10)
            }
            return Math.max(0, Math.min(x, 1))
        }, J = function (y) {
            var x = "0123456789abcdef", A = y % 16, B = (y - A) / 16, C = x.charAt(B) + x.charAt(A);
            return C
        }, b = function (A) {
            var x = (A.r > 0.04045) ? Math.pow((A.r + 0.055) / 1.055, 2.4) : A.r / 12.92, y = (A.g > 0.04045) ? Math.pow((A.g + 0.055) / 1.055, 2.4) : A.g / 12.92, B = (A.b > 0.04045) ? Math.pow((A.b + 0.055) / 1.055, 2.4) : A.b / 12.92;
            return {
                x: x * 0.4124 + y * 0.3576 + B * 0.1805,
                y: x * 0.2126 + y * 0.7152 + B * 0.0722,
                z: x * 0.0193 + y * 0.1192 + B * 0.9505
            }
        }, d = function (y) {
            var x = {
                r: y.x * 3.2406 + y.y * -1.5372 + y.z * -0.4986,
                g: y.x * -0.9689 + y.y * 1.8758 + y.z * 0.0415,
                b: y.x * 0.0557 + y.y * -0.204 + y.z * 1.057
            };
            x.r = (x.r > 0.0031308) ? 1.055 * Math.pow(x.r, (1 / 2.4)) - 0.055 : 12.92 * x.r;
            x.g = (x.g > 0.0031308) ? 1.055 * Math.pow(x.g, (1 / 2.4)) - 0.055 : 12.92 * x.g;
            x.b = (x.b > 0.0031308) ? 1.055 * Math.pow(x.b, (1 / 2.4)) - 0.055 : 12.92 * x.b;
            return x
        }, k = function (C) {
            var E = Math.min(C.r, C.g, C.b), N = Math.max(C.r, C.g, C.b), x = N - E, A, B, y, D = {
                h: 0,
                s: 0,
                v: N
            };
            if (x === 0) {
                D.h = 0;
                D.s = 0
            } else {
                D.s = x / N;
                A = (((N - C.r) / 6) + (x / 2)) / x;
                B = (((N - C.g) / 6) + (x / 2)) / x;
                y = (((N - C.b) / 6) + (x / 2)) / x;
                if (C.r === N) {
                    D.h = y - B
                } else {
                    if (C.g === N) {
                        D.h = (1 / 3) + A - y
                    } else {
                        if (C.b === N) {
                            D.h = (2 / 3) + B - A
                        }
                    }
                }
                if (D.h < 0) {
                    D.h += 1
                } else {
                    if (D.h > 1) {
                        D.h -= 1
                    }
                }
            }
            return D
        }, f = function (x) {
            var y = {r: 0, g: 0, b: 0}, A, C, B, D, E;
            if (x.s === 0) {
                y.r = y.g = y.b = x.v
            } else {
                A = x.h === 1 ? 0 : x.h * 6;
                C = Math.floor(A);
                B = x.v * (1 - x.s);
                D = x.v * (1 - x.s * (A - C));
                E = x.v * (1 - x.s * (1 - (A - C)));
                if (C === 0) {
                    y.r = x.v;
                    y.g = E;
                    y.b = B
                } else {
                    if (C === 1) {
                        y.r = D;
                        y.g = x.v;
                        y.b = B
                    } else {
                        if (C === 2) {
                            y.r = B;
                            y.g = x.v;
                            y.b = E
                        } else {
                            if (C === 3) {
                                y.r = B;
                                y.g = D;
                                y.b = x.v
                            } else {
                                if (C === 4) {
                                    y.r = E;
                                    y.g = B;
                                    y.b = x.v
                                } else {
                                    y.r = x.v;
                                    y.g = B;
                                    y.b = D
                                }
                            }
                        }
                    }
                }
            }
            return y
        }, I = function (C) {
            var E = Math.min(C.r, C.g, C.b), N = Math.max(C.r, C.g, C.b), x = N - E, A, B, y, D = {
                h: 0,
                s: 0,
                l: (N + E) / 2
            };
            if (x === 0) {
                D.h = 0;
                D.s = 0
            } else {
                D.s = D.l < 0.5 ? x / (N + E) : x / (2 - N - E);
                A = (((N - C.r) / 6) + (x / 2)) / x;
                B = (((N - C.g) / 6) + (x / 2)) / x;
                y = (((N - C.b) / 6) + (x / 2)) / x;
                if (C.r === N) {
                    D.h = y - B
                } else {
                    if (C.g === N) {
                        D.h = (1 / 3) + A - y
                    } else {
                        if (C.b === N) {
                            D.h = (2 / 3) + B - A
                        }
                    }
                }
                if (D.h < 0) {
                    D.h += 1
                } else {
                    if (D.h > 1) {
                        D.h -= 1
                    }
                }
            }
            return D
        }, G = function (y) {
            var A, B, x = function (C, D, E) {
                if (E < 0) {
                    E += 1
                }
                if (E > 1) {
                    E -= 1
                }
                if ((6 * E) < 1) {
                    return C + (D - C) * 6 * E
                }
                if ((2 * E) < 1) {
                    return D
                }
                if ((3 * E) < 2) {
                    return C + (D - C) * ((2 / 3) - E) * 6
                }
                return C
            };
            if (y.s === 0) {
                return {r: y.l, g: y.l, b: y.l}
            }
            B = (y.l < 0.5) ? y.l * (1 + y.s) : (y.l + y.s) - (y.s * y.l);
            A = 2 * y.l - B;
            return {
                r: x(A, B, y.h + (1 / 3)),
                g: x(A, B, y.h),
                b: x(A, B, y.h - (1 / 3))
            }
        }, l = function (A) {
            var B = A.x / 0.95047, x = A.y, y = A.z / 1.08883;
            B = (B > 0.008856) ? Math.pow(B, (1 / 3)) : (7.787 * B) + (16 / 116);
            x = (x > 0.008856) ? Math.pow(x, (1 / 3)) : (7.787 * x) + (16 / 116);
            y = (y > 0.008856) ? Math.pow(y, (1 / 3)) : (7.787 * y) + (16 / 116);
            return {
                l: ((116 * x) - 16) / 100,
                a: ((500 * (B - x)) + 128) / 255,
                b: ((200 * (x - y)) + 128) / 255
            }
        }, K = function (y) {
            var x = {
                l: y.l * 100,
                a: (y.a * 255) - 128,
                b: (y.b * 255) - 128
            }, A = {x: 0, y: (x.l + 16) / 116, z: 0};
            A.x = x.a / 500 + A.y;
            A.z = A.y - x.b / 200;
            A.x = (Math.pow(A.x, 3) > 0.008856) ? Math.pow(A.x, 3) : (A.x - 16 / 116) / 7.787;
            A.y = (Math.pow(A.y, 3) > 0.008856) ? Math.pow(A.y, 3) : (A.y - 16 / 116) / 7.787;
            A.z = (Math.pow(A.z, 3) > 0.008856) ? Math.pow(A.z, 3) : (A.z - 16 / 116) / 7.787;
            A.x *= 0.95047;
            A.y *= 1;
            A.z *= 1.08883;
            return A
        }, a = function (x) {
            return {c: 1 - (x.r), m: 1 - (x.g), y: 1 - (x.b)}
        }, H = function (x) {
            return {r: 1 - (x.c), g: 1 - (x.m), b: 1 - (x.y)}
        }, e = function (x) {
            var y = 1;
            if (x.c < y) {
                y = x.c
            }
            if (x.m < y) {
                y = x.m
            }
            if (x.y < y) {
                y = x.y
            }
            if (y == 1) {
                return {c: 0, m: 0, y: 0, k: 1}
            }
            return {
                c: (x.c - y) / (1 - y),
                m: (x.m - y) / (1 - y),
                y: (x.y - y) / (1 - y),
                k: y
            }
        }, g = function (x) {
            return {
                c: x.c * (1 - x.k) + x.k,
                m: x.m * (1 - x.k) + x.k,
                y: x.y * (1 - x.k) + x.k
            }
        };
        this.set = true;
        this.setAlpha = function (x) {
            if (x !== null) {
                c = L(x)
            }
            return this
        };
        this.getAlpha = function () {
            return c
        };
        this.setRGB = function (x, y, A) {
            F = {rgb: this.getRGB()};
            if (x !== null) {
                F.rgb.r = L(x)
            }
            if (y !== null) {
                F.rgb.g = L(y)
            }
            if (A !== null) {
                F.rgb.b = L(A)
            }
            return this
        };
        this.setHSV = function (x, y, A) {
            F = {hsv: this.getHSV()};
            if (x !== null) {
                F.hsv.h = L(x)
            }
            if (y !== null) {
                F.hsv.s = L(y)
            }
            if (A !== null) {
                F.hsv.v = L(A)
            }
            return this
        };
        this.setHSL = function (x, y, A) {
            F = {hsl: this.getHSL()};
            if (x !== null) {
                F.hsl.h = L(x)
            }
            if (y !== null) {
                F.hsl.s = L(y)
            }
            if (A !== null) {
                F.hsl.l = L(A)
            }
            return this
        };
        this.setLAB = function (x, y, A) {
            F = {lab: this.getLAB()};
            if (x !== null) {
                F.lab.l = L(x)
            }
            if (y !== null) {
                F.lab.a = L(y)
            }
            if (A !== null) {
                F.lab.b = L(A)
            }
            return this
        };
        this.setCMYK = function (x, B, y, A) {
            F = {cmyk: this.getCMYK()};
            if (x !== null) {
                F.cmyk.c = L(x)
            }
            if (B !== null) {
                F.cmyk.m = L(B)
            }
            if (y !== null) {
                F.cmyk.y = L(y)
            }
            if (A !== null) {
                F.cmyk.k = L(A)
            }
            return this
        };
        this.getRGB = function () {
            if (!F.rgb) {
                F.rgb = F.lab ? d(K(F.lab)) : F.hsv ? f(F.hsv) : F.hsl ? G(F.hsl) : F.cmyk ? H(g(F.cmyk)) : {
                    r: 0,
                    g: 0,
                    b: 0
                };
                F.rgb.r = L(F.rgb.r);
                F.rgb.g = L(F.rgb.g);
                F.rgb.b = L(F.rgb.b)
            }
            return v.extend({}, F.rgb)
        };
        this.getHSV = function () {
            if (!F.hsv) {
                F.hsv = F.lab ? k(this.getRGB()) : F.rgb ? k(F.rgb) : F.hsl ? k(this.getRGB()) : F.cmyk ? k(this.getRGB()) : {
                    h: 0,
                    s: 0,
                    v: 0
                };
                F.hsv.h = L(F.hsv.h);
                F.hsv.s = L(F.hsv.s);
                F.hsv.v = L(F.hsv.v)
            }
            return v.extend({}, F.hsv)
        };
        this.getHSL = function () {
            if (!F.hsl) {
                F.hsl = F.rgb ? I(F.rgb) : F.hsv ? I(this.getRGB()) : F.cmyk ? I(this.getRGB()) : F.hsv ? I(this.getRGB()) : {
                    h: 0,
                    s: 0,
                    l: 0
                };
                F.hsl.h = L(F.hsl.h);
                F.hsl.s = L(F.hsl.s);
                F.hsl.l = L(F.hsl.l)
            }
            return v.extend({}, F.hsl)
        };
        this.getCMYK = function () {
            if (!F.cmyk) {
                F.cmyk = F.rgb ? e(a(F.rgb)) : F.hsv ? e(a(this.getRGB())) : F.hsl ? e(a(this.getRGB())) : F.lab ? e(a(this.getRGB())) : {
                    c: 0,
                    m: 0,
                    y: 0,
                    k: 1
                };
                F.cmyk.c = L(F.cmyk.c);
                F.cmyk.m = L(F.cmyk.m);
                F.cmyk.y = L(F.cmyk.y);
                F.cmyk.k = L(F.cmyk.k)
            }
            return v.extend({}, F.cmyk)
        };
        this.getLAB = function () {
            if (!F.lab) {
                F.lab = F.rgb ? l(b(F.rgb)) : F.hsv ? l(b(this.getRGB())) : F.hsl ? l(b(this.getRGB())) : F.cmyk ? l(b(this.getRGB())) : {
                    l: 0,
                    a: 0,
                    b: 0
                };
                F.lab.l = L(F.lab.l);
                F.lab.a = L(F.lab.a);
                F.lab.b = L(F.lab.b)
            }
            return v.extend({}, F.lab)
        };
        this.getChannels = function () {
            return {
                r: this.getRGB().r,
                g: this.getRGB().g,
                b: this.getRGB().b,
                a: this.getAlpha(),
                h: this.getHSV().h,
                s: this.getHSV().s,
                v: this.getHSV().v,
                c: this.getCMYK().c,
                m: this.getCMYK().m,
                y: this.getCMYK().y,
                k: this.getCMYK().k,
                L: this.getLAB().l,
                A: this.getLAB().a,
                B: this.getLAB().b
            }
        };
        this.getSpaces = function () {
            return v.extend(true, {}, F)
        };
        this.setSpaces = function (x) {
            F = x;
            return this
        };
        this.distance = function (B) {
            var y = "lab", D = "get" + y.toUpperCase(), C = this[D](), E = B[D](), x = 0, A;
            for (A in C) {
                x += Math.pow(C[A] - E[A], 2)
            }
            return x
        };
        this.equals = function (x) {
            var y = this.getRGB(), A = x.getRGB();
            return this.getAlpha() == x.getAlpha() && y.r == A.r && y.g == A.g && y.b == A.b
        };
        this.limit = function (y) {
            y -= 1;
            var x = this.getRGB();
            this.setRGB(Math.round(x.r * y) / y, Math.round(x.g * y) / y, Math.round(x.b * y) / y)
        };
        this.toHex = function () {
            var x = this.getRGB();
            return J(x.r * 255) + J(x.g * 255) + J(x.b * 255)
        };
        this.toCSS = function () {
            return "#" + this.toHex()
        };
        this.normalize = function () {
            this.setHSV(null, 1, 1);
            return this
        };
        this.copy = function () {
            var x = this.getSpaces(), y = this.getAlpha();
            return new w(x, y)
        };
        if (h.length == 2) {
            this.setSpaces(h[0]);
            this.setAlpha(h[1] === 0 ? 0 : h[1] || 1)
        }
        if (h.length > 2) {
            this.setRGB(h[0], h[1], h[2]);
            this.setAlpha(h[3] === 0 ? 0 : h[3] || 1)
        }
    };
    v.widget("vanderlee.colorpicker", {
        options: {
            alpha: false,
            altAlpha: true,
            altField: "",
            altOnChange: true,
            altProperties: "background-color",
            autoOpen: false,
            buttonColorize: false,
            buttonImage: "images/ui-colorpicker.png",
            buttonImageOnly: false,
            buttonText: null,
            closeOnEscape: true,
            closeOnOutside: true,
            color: "#00FF00",
            colorFormat: "HEX",
            draggable: true,
            duration: "fast",
            hsv: true,
            inline: true,
            layout: {
                map: [0, 0, 1, 5],
                bar: [1, 0, 1, 5],
                preview: [2, 0, 1, 1],
                hsv: [2, 1, 1, 1],
                rgb: [2, 2, 1, 1],
                alpha: [2, 3, 1, 1],
                hex: [2, 4, 1, 1],
                lab: [3, 1, 1, 1],
                cmyk: [3, 2, 1, 2],
                swatches: [4, 0, 1, 5]
            },
            limit: "",
            modal: false,
            mode: "h",
            parts: "",
            regional: "",
            rgb: true,
            showAnim: "fadeIn",
            showCancelButton: true,
            showNoneButton: false,
            showCloseButton: true,
            showOn: "focus",
            showOptions: {},
            swatches: null,
            swatchesWidth: 84,
            title: null,
            close: null,
            init: null,
            select: null,
            open: null
        }, _create: function () {
            var a = this, b;
            ++m;
            a.widgetEventPrefix = "color";
            a.opened = false;
            a.generated = false;
            a.inline = false;
            a.changed = false;
            a.dialog = null;
            a.button = null;
            a.image = null;
            a.overlay = null;
            a.mode = a.options.mode;
            if (this.element[0].nodeName.toLowerCase() === "input" || !a.options.inline) {
                a._setColor(a.element.val());
                this._callback("init");
                v("body").append(u);
                a.dialog = v(".ui-colorpicker:last");
                v(document).delegate("html", "touchstart click", function (d) {
                    if (!a.opened || d.target === a.element[0] || a.overlay) {
                        return
                    }
                    if (a.dialog.is(d.target) || a.dialog.has(d.target).length > 0) {
                        a.element.blur();
                        return
                    }
                    var c, e = v(d.target).parents();
                    for (c = 0; c <= e.length; ++c) {
                        if (a.button !== null && e[c] === a.button[0]) {
                            return
                        }
                    }
                    if (!a.options.closeOnOutside) {
                        return
                    }
                    a.close()
                });
                v(document).keydown(function (c) {
                    if (c.keyCode == 27 && a.opened && a.options.closeOnEscape) {
                        a.close()
                    }
                });
                if (a.options.showOn === "focus" || a.options.showOn === "both") {
                    a.element.on("focus click", function () {
                        a.open()
                    })
                }
                if (a.options.showOn === "button" || a.options.showOn === "both") {
                    if (a.options.buttonImage !== "") {
                        b = a.options.buttonText || a._getRegional("button");
                        a.image = v("<img/>").attr({
                            src: a.options.buttonImage,
                            alt: b,
                            title: b
                        });
                        a._setImageBackground()
                    }
                    if (a.options.buttonImageOnly && a.image) {
                        a.button = a.image
                    } else {
                        a.button = v('<button type="button"></button>').html(a.image || a.options.buttonText).button();
                        a.image = a.image ? v("img", a.button).first() : null
                    }
                    a.button.insertAfter(a.element).click(function () {
                        a[a.opened ? "close" : "open"]()
                    })
                }
                if (a.options.autoOpen) {
                    a.open()
                }
                a.element.keydown(function (c) {
                    if (c.keyCode === 9) {
                        a.close()
                    }
                }).keyup(function (c) {
                    var d = a._parseColor(a.element.val());
                    if (!a.color.equals(d)) {
                        a.color = d;
                        a._change()
                    }
                })
            } else {
                a.inline = true;
                v(this.element).html(s);
                a.dialog = v(".ui-colorpicker", this.element);
                a._generate();
                a.opened = true
            }
            return this
        }, _setOption: function (a, b) {
            var c = this;
            switch (a) {
                case"disabled":
                    if (b) {
                        c.dialog.addClass("ui-colorpicker-disabled")
                    } else {
                        c.dialog.removeClass("ui-colorpicker-disabled")
                    }
                    break
            }
            v.Widget.prototype._setOption.apply(c, arguments)
        }, _setImageBackground: function () {
            if (this.image && this.options.buttonColorize) {
                this.image.css("background-color", this.color.set ? this._formatColor("RGBA", this.color) : "")
            }
        }, _setAltField: function () {
            if (this.options.altOnChange && this.options.altField && this.options.altProperties) {
                var a, b, c = this.options.altProperties.split(",");
                for (a = 0; a <= c.length; ++a) {
                    b = v.trim(c[a]);
                    switch (b) {
                        case"color":
                        case"background-color":
                        case"outline-color":
                        case"border-color":
                            v(this.options.altField).css(b, this.color.set ? this.color.toCSS() : "");
                            break
                    }
                }
                if (this.options.altAlpha) {
                    v(this.options.altField).css("opacity", this.color.set ? this.color.getAlpha() : "")
                }
            }
        }, _setColor: function (a) {
            this.color = this._parseColor(a);
            this.currentColor = this.color.copy();
            this._setImageBackground();
            this._setAltField()
        }, setColor: function (a) {
            this._setColor(a);
            this._change(this.color.set)
        }, _generate: function () {
            var b = this, d, e, c, a;
            b._setColor(b.inline ? b.options.color : b.element.val());
            if (typeof b.options.parts === "string") {
                if (p[b.options.parts]) {
                    c = p[b.options.parts]
                } else {
                    c = p[b.inline ? "inline" : "popup"]
                }
            } else {
                c = b.options.parts
            }
            b.parts = {};
            v.each(c, function (f, g) {
                if (q[g]) {
                    b.parts[g] = new q[g](b)
                }
            });
            if (!b.generated) {
                a = [];
                v.each(b.options.layout, function (g, f) {
                    if (b.parts[g]) {
                        a.push({part: g, pos: f})
                    }
                });
                v(n(a, function (h, k, f) {
                    var g = ["ui-colorpicker-" + h.part + "-container"];
                    if (k > 0) {
                        g.push("ui-colorpicker-padding-left")
                    }
                    if (f > 0) {
                        g.push("ui-colorpicker-padding-top")
                    }
                    return '<td  class="' + g.join(" ") + '"' + (h.pos[2] > 1 ? ' colspan="' + h.pos[2] + '"' : "") + (h.pos[3] > 1 ? ' rowspan="' + h.pos[3] + '"' : "") + ' valign="top"></td>'
                })).appendTo(b.dialog).addClass("ui-dialog-content ui-widget-content");
                b._initAllParts();
                b._updateAllParts();
                b.generated = true
            }
        }, _effectGeneric: function (e, f, a, c, b) {
            var d = this;
            if (v.effects && v.effects[d.options.showAnim]) {
                e[f](d.options.showAnim, d.options.showOptions, d.options.duration, b)
            } else {
                e[(d.options.showAnim === "slideDown" ? a : (d.options.showAnim === "fadeIn" ? c : f))]((d.options.showAnim ? d.options.duration : null), b);
                if (!d.options.showAnim || !d.options.duration) {
                    b()
                }
            }
        }, _effectShow: function (a, b) {
            this._effectGeneric(a, "show", "slideDown", "fadeIn", b)
        }, _effectHide: function (a, b) {
            this._effectGeneric(a, "hide", "slideUp", "fadeOut", b)
        }, open: function () {
            var c = this, d, f, h, g, e, k, a, b;
            if (!c.opened) {
                c._generate();
                d = c.element.offset();
                f = v(window).height() + v(window).scrollTop();
                h = v(window).width() + v(window).scrollLeft();
                g = c.dialog.outerHeight();
                e = c.dialog.outerWidth();
                k = d.left;
                a = d.top + c.element.outerHeight();
                if (k + e > h) {
                    k = Math.max(0, h - e)
                }
                if (a + g > f) {
                    if (d.top - g >= v(window).scrollTop()) {
                        a = d.top - g
                    } else {
                        a = Math.max(0, f - g)
                    }
                }
                c.dialog.css({left: k, top: a});
                b = 0;
                v(c.element[0]).parents().each(function () {
                    var l = v(this).css("z-index");
                    if ((typeof(l) === "number" || typeof(l) === "string") && l !== "" && !isNaN(l)) {
                        b = parseInt(l);
                        return false
                    }
                });
                c.dialog.css("z-index", b += 2);
                c.overlay = c.options.modal ? new v.ui.dialog.overlay(c) : null;
                c._effectShow(this.dialog);
                c.opened = true;
                c._callback("open", true);
                v(function () {
                    c._repaintAllParts()
                })
            }
        }, close: function () {
            var a = this;
            a.currentColor = a.color.copy();
            a.changed = false;
            a._effectHide(a.dialog, function () {
                a.dialog.empty();
                a.generated = false;
                a.opened = false;
                a._callback("close", true)
            });
            if (a.overlay) {
                a.overlay.destroy()
            }
        }, destroy: function () {
            this.element.unbind();
            if (this.image !== null) {
                this.image.remove()
            }
            if (this.button !== null) {
                this.button.remove()
            }
            if (this.dialog !== null) {
                this.dialog.remove()
            }
            if (this.overlay) {
                this.overlay.destroy()
            }
        }, _callback: function (b, a) {
            var d = this, c, e;
            if (d.color.set) {
                c = {formatted: d._formatColor(d.options.colorFormat, d.color)};
                e = d.color.getLAB();
                e.a = (e.a * 2) - 1;
                e.b = (e.b * 2) - 1;
                if (a === true) {
                    c.a = d.color.getAlpha();
                    c.rgb = d.color.getRGB();
                    c.hsv = d.color.getHSV();
                    c.cmyk = d.color.getCMYK();
                    c.hsl = d.color.getHSL();
                    c.lab = e
                }
                return d._trigger(b, null, c)
            } else {
                return d._trigger(b, null, {formatted: ""})
            }
        }, _initAllParts: function () {
            v.each(this.parts, function (b, a) {
                if (a.init) {
                    a.init()
                }
            })
        }, _updateAllParts: function () {
            v.each(this.parts, function (b, a) {
                if (a.update) {
                    a.update()
                }
            })
        }, _repaintAllParts: function () {
            v.each(this.parts, function (b, a) {
                if (a.repaint) {
                    a.repaint()
                }
            })
        }, _change: function (b) {
            this.color.set = (b !== false);
            this.changed = true;
            switch (this.options.limit) {
                case"websafe":
                    this.color.limit(6);
                    break;
                case"nibble":
                    this.color.limit(16);
                    break;
                case"binary":
                    this.color.limit(2);
                    break;
                case"name":
                    var a = this._getSwatch(this._closestName(this.color));
                    this.color.setRGB(a.r, a.g, a.b);
                    break
            }
            if (!this.inline) {
                if (!this.color.set) {
                    this.element.val("")
                } else {
                    if (!this.color.equals(this._parseColor(this.element.val()))) {
                        this.element.val(this._formatColor(this.options.colorFormat, this.color))
                    }
                }
                this._setImageBackground();
                this._setAltField()
            }
            if (this.opened) {
                this._repaintAllParts()
            }
            this._callback("select")
        }, _hoverable: function (a) {
            a.hover(function () {
                a.addClass("ui-state-hover")
            }, function () {
                a.removeClass("ui-state-hover")
            })
        }, _focusable: function (a) {
            a.focus(function () {
                a.addClass("ui-state-focus")
            }).blur(function () {
                a.removeClass("ui-state-focus")
            })
        }, _getRegional: function (a) {
            return v.colorpicker.regional[this.options.regional][a] !== undefined ? v.colorpicker.regional[this.options.regional][a] : v.colorpicker.regional[""][a]
        }, _getSwatches: function () {
            if (typeof(this.options.swatches) === "string") {
                return v.colorpicker.swatches[this.options.swatches]
            }
            if (v.isPlainObject(this.options.swatches)) {
                return v.colorpicker.swatches
            }
            return v.colorpicker.swatches.html
        }, _getSwatch: function (c) {
            var a = this._getSwatches(), b = false;
            if (a[c] !== undefined) {
                return a[c]
            }
            v.each(a, function (e, d) {
                if (e.toLowerCase() == c.toLowerCase()) {
                    b = d;
                    return false
                }
                return true
            });
            return b
        }, _parseColor: function (b) {
            var a, c;
            if (b == "") {
                return new w()
            }
            a = this._getSwatch(v.trim(b));
            if (a) {
                return new w(a.r, a.g, a.b)
            }
            c = /^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d+(?:\.\d+)?)\s*)?\)$/.exec(b);
            if (c) {
                return new w(c[1] / 255, c[2] / 255, c[3] / 255, parseFloat(c[4]))
            }
            c = /^hsla?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d+(?:\.\d+)?)\s*)?\)$/.exec(b);
            if (c) {
                return (new w()).setHSL(c[1] / 255, c[2] / 255, c[3] / 255).setAlpha(parseFloat(c[4]))
            }
            c = /^rgba?\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d+(?:\.\d+)?)\s*)?\)$/.exec(b);
            if (c) {
                return new w(c[1] / 100, c[2] / 100, c[3] / 100, c[4] / 100)
            }
            c = /^hsla?\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d+(?:\.\d+)?)\s*)?\)$/.exec(b);
            if (c) {
                return (new w()).setHSL(c[1] / 100, c[2] / 100, c[3] / 100).setAlpha(c[4] / 100)
            }
            c = /^#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})$/.exec(b);
            if (c) {
                return new w(parseInt(c[1], 16) / 255, parseInt(c[2], 16) / 255, parseInt(c[3], 16) / 255)
            }
            c = /^#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])$/.exec(b);
            if (c) {
                return new w(parseInt(c[1] + c[1], 16) / 255, parseInt(c[2] + c[2], 16) / 255, parseInt(c[3] + c[3], 16) / 255)
            }
            return r(b)
        }, _exactName: function (a) {
            var b = false;
            v.each(this._getSwatches(), function (c, d) {
                if (a.equals(new w(d.r, d.g, d.b))) {
                    b = c;
                    return false
                }
                return true
            });
            return b
        }, _closestName: function (a) {
            var d = a.getRGB(), b = null, e = false, c;
            v.each(this._getSwatches(), function (f, g) {
                c = a.distance(new w(g.r, g.g, g.b));
                if (c < b || b === null) {
                    e = f;
                    if (c == 0) {
                        return false
                    }
                    b = c
                }
                return true
            });
            return e
        }, _formatColor: function (f, e) {
            var c = this, b = null, d = {
                x: function (g) {
                    return o(g * 255)
                }, d: function (g) {
                    return Math.round(g * 255)
                }, f: function (g) {
                    return g
                }, p: function (g) {
                    return g * 100
                }
            }, a = e.getChannels();
            if (!v.isArray(f)) {
                f = [f]
            }
            v.each(f, function (h, g) {
                if (c._formats[g]) {
                    b = c._formats[g](e, c);
                    return (b === false)
                } else {
                    b = g.replace(/\\?[argbhsvcmykLAB][xdfp]/g, function (k) {
                        if (k.match(/^\\/)) {
                            return k.slice(1)
                        }
                        return d[k.charAt(1)](a[k.charAt(0)])
                    });
                    return false
                }
            });
            return b
        }, _formats: {
            "#HEX": function (a, b) {
                return b._formatColor("#rxgxbx", a)
            }, "#HEX3": function (c, b) {
                var a = b._formats.HEX3(c);
                return a === false ? false : "#" + a
            }, HEX: function (a, b) {
                return b._formatColor("rxgxbx", a)
            }, HEX3: function (f, b) {
                var e = f.getRGB(), c = Math.round(e.r * 255), d = Math.round(e.g * 255), a = Math.round(e.b * 255);
                if (((c >>> 4) == (c &= 15)) && ((d >>> 4) == (d &= 15)) && ((a >>> 4) == (a &= 15))) {
                    return c.toString(16) + d.toString(16) + a.toString(16)
                }
                return false
            }, RGB: function (a, b) {
                return a.getAlpha() >= 1 ? b._formatColor("rgb(rd,gd,bd)", a) : false
            }, RGBA: function (a, b) {
                return b._formatColor("rgba(rd,gd,bd,af)", a)
            }, "RGB%": function (a, b) {
                return a.getAlpha() >= 1 ? b._formatColor("rgb(rp%,gp%,bp%)", a) : false
            }, "RGBA%": function (a, b) {
                return b._formatColor("rgba(rp%,gp%,bp%,af)", a)
            }, HSL: function (a, b) {
                return a.getAlpha() >= 1 ? b._formatColor("hsl(hd,sd,vd)", a) : false
            }, HSLA: function (a, b) {
                return b._formatColor("hsla(hd,sd,vd,af)", a)
            }, "HSL%": function (a, b) {
                return a.getAlpha() >= 1 ? b._formatColor("hsl(hp%,sp%,vp%)", a) : false
            }, "HSLA%": function (a, b) {
                return b._formatColor("hsla(hp%,sp%,vp%,af)", a)
            }, NAME: function (a, b) {
                return b._closestName(a)
            }, EXACT: function (a, b) {
                return b._exactName(a)
            }
        }
    })
}(jQuery));
/*
 * jQuery Cookie Plugin v1.3.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (b) {
    if (typeof define === "function" && define.amd) {
        define(["./jquery.js"], b)
    } else {
        b(jQuery)
    }
}(function (l) {
    var h = /\+/g;

    function m(a) {
        return a
    }

    function g(a) {
        return decodeURIComponent(a.replace(h, " "))
    }

    function k(b) {
        if (b.indexOf('"') === 0) {
            b = b.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\")
        }
        try {
            return n.json ? JSON.parse(b) : b
        } catch (a) {
        }
    }

    var n = l.cookie = function (b, c, C) {
        if (c !== undefined) {
            C = l.extend({}, n.defaults, C);
            if (typeof C.expires === "number") {
                var a = C.expires, D = C.expires = new Date();
                D.setDate(D.getDate() + a)
            }
            c = n.json ? JSON.stringify(c) : String(c);
            return (document.cookie = [n.raw ? b : encodeURIComponent(b), "=", n.raw ? c : encodeURIComponent(c), C.expires ? "; expires=" + C.expires.toUTCString() : "", C.path ? "; path=" + C.path : "", C.domain ? "; domain=" + C.domain : "", C.secure ? "; secure" : ""].join(""))
        }
        var A = n.raw ? m : g;
        var E = document.cookie.split("; ");
        var B = b ? undefined : {};
        for (var d = 0, f = E.length; d < f; d++) {
            var e = E[d].split("=");
            var y = A(e.shift());
            var x = A(e.join("="));
            if (b && b === y) {
                B = k(x);
                break
            }
            if (!b) {
                B[y] = k(x)
            }
        }
        return B
    };
    n.defaults = {};
    l.removeCookie = function (a, b) {
        if (l.cookie(a) !== undefined) {
            l.cookie(a, "", l.extend({}, b, {expires: -1}));
            return true
        }
        return false
    }
}));
(function (h) {
    h.fn.dump = function () {
        return h.dump(this)
    };
    h.dump = function (b) {
        var c = function (d, r) {
            if (!r) {
                r = 0
            }
            var o = "", p = "";
            for (i = 0; i < r; i++) {
                p += "\t"
            }
            t = a(d);
            switch (t) {
                case"string":
                    return '"' + d + '"';
                    break;
                case"number":
                    return d.toString();
                    break;
                case"boolean":
                    return d ? "true" : "false";
                case"date":
                    return "Date: " + d.toLocaleString();
                case"array":
                    o += "Array ( \n";
                    h.each(d, function (k, l) {
                        o += p + "\t" + k + " => " + c(l, r + 1) + "\n"
                    });
                    o += p + ")";
                    break;
                case"object":
                    o += "Object { \n";
                    h.each(d, function (k, l) {
                        o += p + "\t" + k + ": " + c(l, r + 1) + "\n"
                    });
                    o += p + "}";
                    break;
                case"jquery":
                    o += "jQuery Object { \n";
                    h.each(d, function (k, l) {
                        o += p + "\t" + k + " = " + c(l, r + 1) + "\n"
                    });
                    o += p + "}";
                    break;
                case"regexp":
                    return "RegExp: " + d.toString();
                case"error":
                    return d.toString();
                case"document":
                case"domelement":
                    o += "DOMElement [ \n" + p + "\tnodeName: " + d.nodeName + "\n" + p + "\tnodeValue: " + d.nodeValue + "\n" + p + "\tinnerHTML: [ \n";
                    h.each(d.childNodes, function (l, m) {
                        if (l < 1) {
                            var k = 0
                        }
                        if (a(m) == "string") {
                            if (m.textContent.match(/[^\s]/)) {
                                o += p + "\t\t" + (l - (k || 0)) + " = String: " + f(m.textContent) + "\n"
                            } else {
                                k--
                            }
                        } else {
                            o += p + "\t\t" + (l - (k || 0)) + " = " + c(m, r + 2) + "\n"
                        }
                    });
                    o += p + "\t]\n" + p + "]";
                    break;
                case"function":
                    var q = d.toString().match(/^(.*)\(([^\)]*)\)/im);
                    q[1] = f(q[1].replace(new RegExp("[\\s]+", "g"), " "));
                    q[2] = f(q[2].replace(new RegExp("[\\s]+", "g"), " "));
                    return q[1] + "(" + q[2] + ")";
                case"window":
                default:
                    o += "N/A: " + t;
                    break
            }
            return o
        };
        var a = function (d) {
            var l = typeof(d);
            if (l != "object") {
                return l
            }
            switch (d) {
                case null:
                    return "null";
                case window:
                    return "window";
                case document:
                    return "document";
                case window.event:
                    return "event";
                default:
                    break
            }
            if (d.jquery) {
                return "jquery"
            }
            switch (d.constructor) {
                case Array:
                    return "array";
                case Boolean:
                    return "boolean";
                case Date:
                    return "date";
                case Object:
                    return "object";
                case RegExp:
                    return "regexp";
                case ReferenceError:
                case Error:
                    return "error";
                case null:
                default:
                    break
            }
            switch (d.nodeType) {
                case 1:
                    return "domelement";
                case 3:
                    return "string";
                case null:
                default:
                    break
            }
            return "Unknown"
        };
        return c(b)
    };
    function f(a) {
        return g(e(a))
    }

    function g(a) {
        return a.replace(new RegExp("^[\\s]+", "g"), "")
    }

    function e(a) {
        return a.replace(new RegExp("[\\s]+$", "g"), "")
    }
})(jQuery);
(function () {
    var b = {
        getSelection: function () {
            var a = (this.jquery) ? this[0] : this;
            return (("selectionStart" in a && function () {
                var d = a.selectionEnd - a.selectionStart;
                return {
                    start: a.selectionStart,
                    end: a.selectionEnd,
                    length: d,
                    text: a.value.substr(a.selectionStart, d)
                }
            }) || (document.selection && function () {
                a.focus();
                var g = document.selection.createRange();
                if (g === null) {
                    return {start: 0, end: a.value.length, length: 0}
                }
                var h = a.createTextRange();
                var f = h.duplicate();
                h.moveToBookmark(g.getBookmark());
                f.setEndPoint("EndToStart", h);
                return {
                    start: f.text.length,
                    end: f.text.length + g.text.length,
                    length: g.text.length,
                    text: g.text
                }
            }) || function () {
                return null
            })()
        }, setSelection: function () {
            var d = (this.jquery) ? this[0] : this;
            var a = arguments[0] || {};
            return (("selectionStart" in d && function () {
                var c = typeof a == "object" ? a.start : a;
                if (c != undefined) {
                    d.selectionStart = c
                }
                if (a.end != undefined) {
                    d.selectionEnd = a.end
                }
                d.focus();
                return this
            }) || (document.selection && function () {
                d.focus();
                var f = document.selection.createRange();
                if (f === null) {
                    return this
                }
                var c = typeof a == "object" ? a.start : a;
                if (c != undefined) {
                    f.moveStart("character", -d.value.length);
                    f.moveStart("character", c);
                    f.collapse()
                }
                if (a.end != undefined) {
                    f.moveEnd("character", a.end - c)
                }
                f.select();
                return this
            }) || function () {
                d.focus();
                return jQuery(d)
            })()
        }, replaceSelection: function () {
            var a = (this.jquery) ? this[0] : this;
            var d = arguments[0] || "";
            return (("selectionStart" in a && function () {
                a.value = a.value.substr(0, a.selectionStart) + d + a.value.substr(a.selectionEnd, a.value.length);
                return this
            }) || (document.selection && function () {
                a.focus();
                document.selection.createRange().text = d;
                return this
            }) || function () {
                a.value += d;
                return jQuery(a)
            })()
        }
    };
    jQuery.each(b, function (a) {
        jQuery.fn[a] = this
    })
})();
(function (b) {
    b.fn.hoverIntent = function (q, r) {
        var g = {sensitivity: 7, interval: 100, timeout: 0};
        g = b.extend(g, r ? {over: q, out: r} : q);
        var a, f, u, w;
        var v = function (c) {
            a = c.pageX;
            f = c.pageY
        };
        var x = function (c, d) {
            d.hoverIntent_t = clearTimeout(d.hoverIntent_t);
            if ((Math.abs(u - a) + Math.abs(w - f)) < g.sensitivity) {
                b(d).unbind("mousemove", v);
                d.hoverIntent_s = 1;
                return g.over.apply(d, [c])
            } else {
                u = a;
                w = f;
                d.hoverIntent_t = setTimeout(function () {
                    x(c, d)
                }, g.interval)
            }
        };
        var s = function (c, d) {
            d.hoverIntent_t = clearTimeout(d.hoverIntent_t);
            d.hoverIntent_s = 0;
            return g.out.apply(d, [c])
        };
        var y = function (e) {
            var c = jQuery.extend({}, e);
            var d = this;
            if (d.hoverIntent_t) {
                d.hoverIntent_t = clearTimeout(d.hoverIntent_t)
            }
            if (e.type == "mouseenter") {
                u = c.pageX;
                w = c.pageY;
                b(d).bind("mousemove", v);
                if (d.hoverIntent_s != 1) {
                    d.hoverIntent_t = setTimeout(function () {
                        x(c, d)
                    }, g.interval)
                }
            } else {
                b(d).unbind("mousemove", v);
                if (d.hoverIntent_s == 1) {
                    d.hoverIntent_t = setTimeout(function () {
                        s(c, d)
                    }, g.timeout)
                }
            }
        };
        return this.bind("mouseenter", y).bind("mouseleave", y)
    }
})(jQuery);
(function (ai) {
    function ak() {
    }

    function J(a) {
        al = [a]
    }

    function Y(a, c, b) {
        return a && a.apply(c.context || c, b)
    }

    function aa(a) {
        return /\?/.test(a) ? "&" : "?"
    }

    var W = "async", N = "charset", R = "", M = "error", L = "insertBefore", P = "_jqjsp", S = "on", ah = S + "click", ad = S + M, U = S + "load", V = S + "readystatechange", am = "readyState", O = "removeChild", ae = "<script>", T = "success", Q = "timeout", aj = window, an = ai.Deferred, ag = ai("head")[0] || document.documentElement, X = {}, ab = 0, al, ac = {
        callback: P,
        url: location.href
    }, Z = aj.opera, af = !!ai("<div>").html("<!--[if IE]><i><![endif]-->").find("i").length;

    function K(n) {
        n = ai.extend({}, ac, n);
        var p = n.success, g = n.error, q = n.complete, y = n.dataFilter, w = n.callbackParameter, f = n.callback, x = n.cache, r = n.pageCache, o = n.charset, m = n.url, u = n.data, d = n.timeout, h, B = 0, b = ak, e, k, s, v, l, a;
        an && an(function (C) {
            C.done(p).fail(g);
            p = C.resolve;
            g = C.reject
        }).promise(n);
        n.abort = function () {
            !(B++) && b()
        };
        if (Y(n.beforeSend, n, [n]) === !1 || B) {
            return n
        }
        m = m || R;
        u = u ? ((typeof u) == "string" ? u : ai.param(u, n.traditional)) : R;
        m += u ? (aa(m) + u) : R;
        w && (m += aa(m) + encodeURIComponent(w) + "=?");
        !x && !r && (m += aa(m) + "_" + (new Date()).getTime() + "=");
        m = m.replace(/=\?(&|$)/, "=" + f + "$1");
        function A(C) {
            if (!(B++)) {
                b();
                r && (X[m] = {s: [C]});
                y && (C = y.apply(n, [C]));
                Y(p, n, [C, T, n]);
                Y(q, n, [n, T])
            }
        }

        function c(C) {
            if (!(B++)) {
                b();
                r && C != Q && (X[m] = C);
                Y(g, n, [n, C]);
                Y(q, n, [n, C])
            }
        }

        if (r && (h = X[m])) {
            h.s ? A(h.s[0]) : c(h)
        } else {
            aj[f] = J;
            v = ai(ae)[0];
            v.id = P + ab++;
            if (o) {
                v[N] = o
            }
            Z && Z.version() < 11.6 ? ((l = ai(ae)[0]).text = "document.getElementById('" + v.id + "')." + ad + "()") : (v[W] = W);
            if (af) {
                v.htmlFor = v.id;
                v.event = ah
            }
            v[U] = v[ad] = v[V] = function (D) {
                if (!v[am] || !/i/.test(v[am])) {
                    try {
                        v[ah] && v[ah]()
                    } catch (C) {
                    }
                    D = al;
                    al = 0;
                    D ? A(D[0]) : c(M)
                }
            };
            v.src = m;
            b = function (C) {
                a && clearTimeout(a);
                v[V] = v[U] = v[ad] = null;
                ag[O](v);
                l && ag[O](l)
            };
            ag[L](v, (s = ag.firstChild));
            l && ag[L](l, s);
            a = d > 0 && setTimeout(function () {
                c(Q)
            }, d)
        }
        return n
    }

    K.setup = function (a) {
        ai.extend(ac, a)
    };
    ai.jsonp = K
})(jQuery);
(function () {
    var b = function (e, f, a) {
        e = jsPlumbUtil.isArray(e) ? e : [e.x, e.y];
        f = jsPlumbUtil.isArray(f) ? f : [f.x, f.y];
        return a(e, f)
    };
    jsPlumbUtil = {
        isArray: function (a) {
            return Object.prototype.toString.call(a) === "[object Array]"
        },
        isNumber: function (a) {
            return Object.prototype.toString.call(a) === "[object Number]"
        },
        isString: function (a) {
            return typeof a === "string"
        },
        isBoolean: function (a) {
            return typeof a === "boolean"
        },
        isNull: function (a) {
            return a == null
        },
        isObject: function (a) {
            return a == null ? false : Object.prototype.toString.call(a) === "[object Object]"
        },
        isDate: function (a) {
            return Object.prototype.toString.call(a) === "[object Date]"
        },
        isFunction: function (a) {
            return Object.prototype.toString.call(a) === "[object Function]"
        },
        clone: function (f) {
            if (this.isString(f)) {
                return "" + f
            } else {
                if (this.isBoolean(f)) {
                    return !!f
                } else {
                    if (this.isDate(f)) {
                        return new Date(f.getTime())
                    } else {
                        if (this.isFunction(f)) {
                            return f
                        } else {
                            if (this.isArray(f)) {
                                var g = [];
                                for (var a = 0; a < f.length; a++) {
                                    g.push(this.clone(f[a]))
                                }
                                return g
                            } else {
                                if (this.isObject(f)) {
                                    var g = {};
                                    for (var a in f) {
                                        g[a] = this.clone(f[a])
                                    }
                                    return g
                                } else {
                                    return f
                                }
                            }
                        }
                    }
                }
            }
        },
        merge: function (n, o) {
            var a = this.clone(n);
            for (var c in o) {
                if (a[c] == null || this.isString(o[c]) || this.isBoolean(o[c])) {
                    a[c] = o[c]
                } else {
                    if (this.isArray(o[c])) {
                        var m = [];
                        if (this.isArray(a[c])) {
                            m.push.apply(m, a[c])
                        }
                        m.push.apply(m, o[c]);
                        a[c] = m
                    } else {
                        if (this.isObject(o[c])) {
                            if (!this.isObject(a[c])) {
                                a[c] = {}
                            }
                            for (var l in o[c]) {
                                a[c][l] = o[c][l]
                            }
                        }
                    }
                }
            }
            return a
        },
        copyValues: function (h, f, g) {
            for (var a = 0; a < h.length; a++) {
                g[h[a]] = f[h[a]]
            }
        },
        functionChain: function (k, g, l) {
            for (var a = 0; a < l.length; a++) {
                var h = l[a][0][l[a][1]].apply(l[a][0], l[a][2]);
                if (h === g) {
                    return h
                }
            }
            return k
        },
        populate: function (g, h) {
            var a = function (c) {
                var e = c.match(/(\${.*?})/g);
                if (e != null) {
                    for (var l = 0; l < e.length; l++) {
                        var d = h[e[l].substring(2, e[l].length - 1)];
                        if (d != null) {
                            c = c.replace(e[l], d)
                        }
                    }
                }
                return c
            }, f = function (c) {
                if (c != null) {
                    if (jsPlumbUtil.isString(c)) {
                        return a(c)
                    } else {
                        if (jsPlumbUtil.isArray(c)) {
                            var d = [];
                            for (var e = 0; e < c.length; e++) {
                                d.push(f(c[e]))
                            }
                            return d
                        } else {
                            if (jsPlumbUtil.isObject(c)) {
                                var d = {};
                                for (var e in c) {
                                    d[e] = f(c[e])
                                }
                                return d
                            } else {
                                return c
                            }
                        }
                    }
                }
            };
            return f(g)
        },
        convertStyle: function (p, a) {
            if ("transparent" === p) {
                return p
            }
            var k = p, l = function (c) {
                return c.length == 1 ? "0" + c : c
            }, o = function (c) {
                return l(Number(c).toString(16))
            }, n = /(rgb[a]?\()(.*)(\))/;
            if (p.match(n)) {
                var m = p.match(n)[2].split(",");
                k = "#" + o(m[0]) + o(m[1]) + o(m[2]);
                if (!a && m.length == 4) {
                    k = k + o(m[3])
                }
            }
            return k
        },
        gradient: function (d, a) {
            return b(d, a, function (c, f) {
                if (f[0] == c[0]) {
                    return f[1] > c[1] ? Infinity : -Infinity
                } else {
                    if (f[1] == c[1]) {
                        return f[0] > c[0] ? 0 : -0
                    } else {
                        return (f[1] - c[1]) / (f[0] - c[0])
                    }
                }
            })
        },
        normal: function (d, a) {
            return -1 / this.gradient(d, a)
        },
        lineLength: function (d, a) {
            return b(d, a, function (c, f) {
                return Math.sqrt(Math.pow(f[1] - c[1], 2) + Math.pow(f[0] - c[0], 2))
            })
        },
        segment: function (d, a) {
            return b(d, a, function (c, f) {
                if (f[0] > c[0]) {
                    return (f[1] > c[1]) ? 2 : 1
                } else {
                    if (f[0] == c[0]) {
                        return f[1] > c[1] ? 2 : 1
                    } else {
                        return (f[1] > c[1]) ? 3 : 4
                    }
                }
            })
        },
        theta: function (d, a) {
            return b(d, a, function (l, m) {
                var n = jsPlumbUtil.gradient(l, m), k = Math.atan(n), c = jsPlumbUtil.segment(l, m);
                if ((c == 4 || c == 3)) {
                    k += Math.PI
                }
                if (k < 0) {
                    k += (2 * Math.PI)
                }
                return k
            })
        },
        intersects: function (q, r) {
            var u = q.x, w = q.x + q.w, a = q.y, o = q.y + q.h, s = r.x, v = r.x + r.w, n = r.y, p = r.y + r.h;
            return ((u <= s && s <= w) && (a <= n && n <= o)) || ((u <= v && v <= w) && (a <= n && n <= o)) || ((u <= s && s <= w) && (a <= p && p <= o)) || ((u <= v && s <= w) && (a <= p && p <= o)) || ((s <= u && u <= v) && (n <= a && a <= p)) || ((s <= w && w <= v) && (n <= a && a <= p)) || ((s <= u && u <= v) && (n <= o && o <= p)) || ((s <= w && u <= v) && (n <= o && o <= p))
        },
        segmentMultipliers: [null, [1, -1], [1, 1], [-1, 1], [-1, -1]],
        inverseSegmentMultipliers: [null, [-1, -1], [-1, 1], [1, 1], [1, -1]],
        pointOnLine: function (u, p, s) {
            var q = jsPlumbUtil.gradient(u, p), a = jsPlumbUtil.segment(u, p), m = s > 0 ? jsPlumbUtil.segmentMultipliers[a] : jsPlumbUtil.inverseSegmentMultipliers[a], r = Math.atan(q), o = Math.abs(s * Math.sin(r)) * m[1], n = Math.abs(s * Math.cos(r)) * m[0];
            return {x: u.x + n, y: u.y + o}
        },
        perpendicularLineTo: function (o, n, m) {
            var p = jsPlumbUtil.gradient(o, n), l = Math.atan(-1 / p), k = m / 2 * Math.sin(l), a = m / 2 * Math.cos(l);
            return [{x: n.x + a, y: n.y + k}, {x: n.x - a, y: n.y - k}]
        },
        findWithFunction: function (a, e) {
            if (a) {
                for (var f = 0; f < a.length; f++) {
                    if (e(a[f])) {
                        return f
                    }
                }
            }
            return -1
        },
        clampToGrid: function (a, h, m, k, l) {
            var n = function (g, f) {
                var c = g % f, e = Math.floor(g / f), d = c >= (f / 2) ? 1 : 0;
                return (e + d) * f
            };
            return [k || m == null ? a : n(a, m[0]), l || m == null ? h : n(h, m[1])]
        },
        indexOf: function (a, d) {
            return jsPlumbUtil.findWithFunction(a, function (c) {
                return c == d
            })
        },
        removeWithFunction: function (f, e) {
            var a = jsPlumbUtil.findWithFunction(f, e);
            if (a > -1) {
                f.splice(a, 1)
            }
            return a != -1
        },
        remove: function (f, e) {
            var a = jsPlumbUtil.indexOf(f, e);
            if (a > -1) {
                f.splice(a, 1)
            }
            return a != -1
        },
        addWithFunction: function (e, f, a) {
            if (jsPlumbUtil.findWithFunction(e, a) == -1) {
                e.push(f)
            }
        },
        addToList: function (f, h, g) {
            var a = f[h];
            if (a == null) {
                a = [], f[h] = a
            }
            a.push(g);
            return a
        },
        EventGenerator: function () {
            var g = {}, h = this, f = false;
            var a = ["ready"];
            this.bind = function (d, c) {
                jsPlumbUtil.addToList(g, d, c);
                return h
            };
            this.fire = function (e, d, n) {
                if (!f && g[e]) {
                    for (var m = 0; m < g[e].length; m++) {
                        if (jsPlumbUtil.findWithFunction(a, function (k) {
                            return k === e
                        }) != -1) {
                            g[e][m](d, n)
                        } else {
                            try {
                                g[e][m](d, n)
                            } catch (c) {
                                jsPlumbUtil.log("jsPlumb: fire failed for event " + e + " : " + c)
                            }
                        }
                    }
                }
                return h
            };
            this.unbind = function (c) {
                if (c) {
                    delete g[c]
                } else {
                    g = {}
                }
                return h
            };
            this.getListener = function (c) {
                return g[c]
            };
            this.setSuspendEvents = function (c) {
                f = c
            };
            this.isSuspendEvents = function () {
                return f
            }
        },
        logEnabled: true,
        log: function () {
            if (jsPlumbUtil.logEnabled && typeof console != "undefined") {
                try {
                    var d = arguments[arguments.length - 1];
                    console.log(d)
                } catch (a) {
                }
            }
        },
        group: function (a) {
            if (jsPlumbUtil.logEnabled && typeof console != "undefined") {
                console.group(a)
            }
        },
        groupEnd: function (a) {
            if (jsPlumbUtil.logEnabled && typeof console != "undefined") {
                console.groupEnd(a)
            }
        },
        time: function (a) {
            if (jsPlumbUtil.logEnabled && typeof console != "undefined") {
                console.time(a)
            }
        },
        timeEnd: function (a) {
            if (jsPlumbUtil.logEnabled && typeof console != "undefined") {
                console.timeEnd(a)
            }
        },
        removeElement: function (a) {
            if (a != null && a.parentNode != null) {
                a.parentNode.removeChild(a)
            }
        },
        removeElements: function (d) {
            for (var a = 0; a < d.length; a++) {
                jsPlumbUtil.removeElement(d[a])
            }
        }
    }
})();
(function () {
    var e = !!document.createElement("canvas").getContext, f = !!window.SVGAngle || document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1"), g = function () {
        if (g.vml == undefined) {
            var a = document.body.appendChild(document.createElement("div"));
            a.innerHTML = '<v:shape id="vml_flag1" adj="1" />';
            var b = a.firstChild;
            b.style.behavior = "url(#default#VML)";
            g.vml = b ? typeof b.adj == "object" : true;
            a.parentNode.removeChild(a)
        }
        return g.vml
    };
    var h = function (a) {
        var b = {}, c = [], m = {}, n = {}, d = {};
        this.register = function (u) {
            var v = jsPlumb.CurrentLibrary;
            u = v.getElementObject(u);
            var l = a.getId(u), w = v.getDOMElement(u), k = v.getOffset(u);
            if (!b[l]) {
                b[l] = u;
                c.push(u);
                m[l] = {}
            }
            var s = function (o, A) {
                if (o) {
                    for (var r = 0; r < o.childNodes.length; r++) {
                        if (o.childNodes[r].nodeType != 3 && o.childNodes[r].nodeType != 8) {
                            var p = v.getElementObject(o.childNodes[r]), B = a.getId(p, null, true);
                            if (B && n[B] && n[B] > 0) {
                                var q = v.getOffset(p);
                                m[l][B] = {
                                    id: B,
                                    offset: {
                                        left: q.left - k.left,
                                        top: q.top - k.top
                                    }
                                };
                                d[B] = l
                            }
                            s(o.childNodes[r])
                        }
                    }
                }
            };
            s(w)
        };
        this.updateOffsets = function (l) {
            var C = jsPlumb.CurrentLibrary, x = C.getElementObject(l), y = a.getId(x), w = m[y], A = C.getOffset(x);
            if (w) {
                for (var D in w) {
                    var B = C.getElementObject(D), k = C.getOffset(B);
                    m[y][D] = {
                        id: D,
                        offset: {left: k.left - A.left, top: k.top - A.top}
                    };
                    d[D] = y
                }
            }
        };
        this.endpointAdded = function (B) {
            var I = jsPlumb.CurrentLibrary, F = document.body, D = a.getId(B), G = I.getDOMElement(B), C = G.parentNode, k = C == F;
            n[D] = n[D] ? n[D] + 1 : 1;
            while (C != null && C != F) {
                var J = a.getId(C, null, true);
                if (J && b[J]) {
                    var p = -1, H = I.getElementObject(C), l = I.getOffset(H);
                    if (m[J][D] == null) {
                        var E = jsPlumb.CurrentLibrary.getOffset(B);
                        m[J][D] = {
                            id: D,
                            offset: {left: E.left - l.left, top: E.top - l.top}
                        };
                        d[D] = J
                    }
                    break
                }
                C = C.parentNode
            }
        };
        this.endpointDeleted = function (l) {
            if (n[l.elementId]) {
                n[l.elementId]--;
                if (n[l.elementId] <= 0) {
                    for (var k in m) {
                        if (m[k]) {
                            delete m[k][l.elementId];
                            delete d[l.elementId]
                        }
                    }
                }
            }
        };
        this.changeId = function (l, k) {
            m[k] = m[l];
            m[l] = {};
            d[k] = d[l];
            d[l] = null
        };
        this.getElementsForDraggable = function (k) {
            return m[k]
        };
        this.elementRemoved = function (k) {
            var l = d[k];
            if (l) {
                delete m[l][k];
                delete d[k]
            }
        };
        this.reset = function () {
            b = {};
            c = [];
            m = {};
            n = {}
        }
    };
    if (!window.console) {
        window.console = {
            time: function () {
            }, timeEnd: function () {
            }, group: function () {
            }, groupEnd: function () {
            }, log: function () {
            }
        }
    }
    window.jsPlumbAdapter = {
        headless: false, appendToRoot: function (a) {
            document.body.appendChild(a)
        }, getRenderModes: function () {
            return ["canvas", "svg", "vml"]
        }, isRenderModeAvailable: function (a) {
            return {canvas: e, svg: f, vml: g()}[a]
        }, getDragManager: function (a) {
            return new h(a)
        }, setRenderMode: function (a) {
            var b;
            if (a) {
                a = a.toLowerCase();
                var d = this.isRenderModeAvailable("canvas"), l = this.isRenderModeAvailable("svg"), c = this.isRenderModeAvailable("vml");
                if (a === "svg") {
                    if (l) {
                        b = "svg"
                    } else {
                        if (d) {
                            b = "canvas"
                        } else {
                            if (c) {
                                b = "vml"
                            }
                        }
                    }
                } else {
                    if (a === "canvas" && d) {
                        b = "canvas"
                    } else {
                        if (c) {
                            b = "vml"
                        }
                    }
                }
            }
            return b
        }
    }
})();
(function () {
    var ad = jsPlumbUtil.findWithFunction, K = jsPlumbUtil.indexOf, U = jsPlumbUtil.removeWithFunction, aa = jsPlumbUtil.remove, S = jsPlumbUtil.addWithFunction, ab = jsPlumbUtil.addToList, Z = jsPlumbUtil.isArray, W = jsPlumbUtil.isString, L = jsPlumbUtil.isObject;
    var Q = function (b, a) {
        return X.CurrentLibrary.getAttribute(af(b), a)
    }, ah = function (b, a, c) {
        X.CurrentLibrary.setAttribute(af(b), a, c)
    }, H = function (a, b) {
        X.CurrentLibrary.addClass(af(a), b)
    }, ac = function (a, b) {
        return X.CurrentLibrary.hasClass(af(a), b)
    }, Y = function (a, b) {
        X.CurrentLibrary.removeClass(af(a), b)
    }, af = function (a) {
        return X.CurrentLibrary.getElementObject(a)
    }, P = function (c, d) {
        var a = X.CurrentLibrary.getOffset(af(c));
        if (d != null) {
            var b = d.getZoom();
            return {left: a.left / b, top: a.top / b}
        } else {
            return a
        }
    }, aj = function (a) {
        return X.CurrentLibrary.getSize(af(a))
    }, V = jsPlumbUtil.log, N = jsPlumbUtil.group, ae = jsPlumbUtil.groupEnd, O = jsPlumbUtil.time, M = jsPlumbUtil.timeEnd, T = function () {
        return "" + (new Date()).getTime()
    }, R = window.jsPlumbUIComponent = function (y) {
        var f = this, x = arguments, h = false, p = y.parameters || {}, r = f.idPrefix, d = r + (new Date()).getTime(), e = null, w = null;
        f._jsPlumb = y._jsPlumb;
        f.getId = function () {
            return d
        };
        f.hoverClass = y.hoverClass || f._jsPlumb.Defaults.HoverClass || X.Defaults.HoverClass;
        jsPlumbUtil.EventGenerator.apply(this);
        if (y.events) {
            for (var c in y.events) {
                f.bind(c, y.events[c])
            }
        }
        this.clone = function () {
            var A = new Object();
            f.constructor.apply(A, x);
            return A
        };
        this.getParameter = function (A) {
            return p[A]
        }, this.getParameters = function () {
            return p
        }, this.setParameter = function (B, A) {
            p[B] = A
        }, this.setParameters = function (A) {
            p = A
        }, this.overlayPlacements = [];
        var q = y.beforeDetach;
        this.isDetachAllowed = function (C) {
            var B = true;
            if (q) {
                try {
                    B = q(C)
                } catch (A) {
                    V("jsPlumb: beforeDetach callback failed", A)
                }
            }
            return B
        };
        var n = y.beforeDrop;
        this.isDropAllowed = function (A, D, C, F, E) {
            var B = f._jsPlumb.checkCondition("beforeDrop", {
                sourceId: A,
                targetId: D,
                scope: C,
                connection: F,
                dropEndpoint: E
            });
            if (n) {
                try {
                    B = n({
                        sourceId: A,
                        targetId: D,
                        scope: C,
                        connection: F,
                        dropEndpoint: E
                    })
                } catch (G) {
                    V("jsPlumb: beforeDrop callback failed", G)
                }
            }
            return B
        };
        var b = function () {
            if (e && w) {
                var A = {};
                X.extend(A, e);
                X.extend(A, w);
                delete f.hoverPaintStyle;
                if (A.gradient && e.fillStyle) {
                    delete A.gradient
                }
                w = A
            }
        };
        this.setPaintStyle = function (B, A) {
            e = B;
            f.paintStyleInUse = e;
            b();
            if (!A) {
                f.repaint()
            }
        };
        this.getPaintStyle = function () {
            return e
        };
        this.setHoverPaintStyle = function (B, A) {
            w = B;
            b();
            if (!A) {
                f.repaint()
            }
        };
        this.getHoverPaintStyle = function () {
            return w
        };
        this.setHover = function (C, A, B) {
            if (!f._jsPlumb.currentlyDragging && !f._jsPlumb.isHoverSuspended()) {
                h = C;
                if (f.canvas != null) {
                    if (f.hoverClass != null) {
                        if (C) {
                            s.addClass(f.canvas, f.hoverClass)
                        } else {
                            s.removeClass(f.canvas, f.hoverClass)
                        }
                    }
                    if (C) {
                        s.addClass(f.canvas, f._jsPlumb.hoverClass)
                    } else {
                        s.removeClass(f.canvas, f._jsPlumb.hoverClass)
                    }
                }
                if (w != null) {
                    f.paintStyleInUse = C ? w : e;
                    if (!f._jsPlumb.isSuspendDrawing()) {
                        B = B || T();
                        f.repaint({timestamp: B, recalc: false})
                    }
                }
                if (f.getAttachedElements && !A) {
                    a(C, T(), f)
                }
            }
        };
        this.isHover = function () {
            return h
        };
        this.bindListeners = function (A, C, B) {
            A.bind("click", function (E, D) {
                C.fire("click", C, D)
            });
            A.bind("dblclick", function (E, D) {
                C.fire("dblclick", C, D)
            });
            A.bind("contextmenu", function (E, D) {
                C.fire("contextmenu", C, D)
            });
            A.bind("mouseenter", function (E, D) {
                if (!C.isHover()) {
                    B(true);
                    C.fire("mouseenter", C, D)
                }
            });
            A.bind("mouseexit", function (E, D) {
                if (C.isHover()) {
                    B(false);
                    C.fire("mouseexit", C, D)
                }
            });
            A.bind("mousedown", function (E, D) {
                C.fire("mousedown", C, D)
            });
            A.bind("mouseup", function (E, D) {
                C.fire("mouseup", C, D)
            })
        };
        var s = X.CurrentLibrary, u = ["click", "dblclick", "mouseenter", "mouseout", "mousemove", "mousedown", "mouseup", "contextmenu"], g = {mouseout: "mouseexit"}, o = function (B, A, C) {
            var D = g[C] || C;
            s.bind(B, C, function (E) {
                A.fire(D, A, E)
            })
        }, k = function (A, B) {
            var C = g[B] || B;
            s.unbind(A, B)
        };
        this.attachListeners = function (B, A) {
            for (var C = 0, D = u.length; C < D; C++) {
                o(B, A, u[C])
            }
        };
        var a = function (A, B, E) {
            var C = f.getAttachedElements();
            if (C) {
                for (var D = 0, F = C.length; D < F; D++) {
                    if (!E || E != C[D]) {
                        C[D].setHover(A, true, B)
                    }
                }
            }
        };
        this.reattachListenersForElement = function (A) {
            if (arguments.length > 1) {
                for (var B = 0, C = u.length; B < C; B++) {
                    k(A, u[B])
                }
                for (var B = 1, C = arguments.length; B < C; B++) {
                    f.attachListeners(A, arguments[B])
                }
            }
        };
        var v = [], m = function (A) {
            return A == null ? null : A.split(" ")
        }, l = function (B, D) {
            if (f.getDefaultType) {
                var A = f.getTypeDescriptor();
                var C = jsPlumbUtil.merge({}, f.getDefaultType());
                for (var E = 0, F = v.length; E < F; E++) {
                    C = jsPlumbUtil.merge(C, f._jsPlumb.getType(v[E], A))
                }
                if (B) {
                    C = jsPlumbUtil.populate(C, B)
                }
                f.applyType(C, D);
                if (!D) {
                    f.repaint()
                }
            }
        };
        f.setType = function (C, A, B) {
            v = m(C) || [];
            l(A, B)
        };
        f.getType = function () {
            return v
        };
        f.reapplyTypes = function (A, B) {
            l(A, B)
        };
        f.hasType = function (A) {
            return jsPlumbUtil.indexOf(v, A) != -1
        };
        f.addType = function (C, G, B) {
            var D = m(C), A = false;
            if (D != null) {
                for (var E = 0, F = D.length; E < F; E++) {
                    if (!f.hasType(D[E])) {
                        v.push(D[E]);
                        A = true
                    }
                }
                if (A) {
                    l(G, B)
                }
            }
        };
        f.removeType = function (B, A) {
            var D = m(B), G = false, C = function (am) {
                var an = jsPlumbUtil.indexOf(v, am);
                if (an != -1) {
                    v.splice(an, 1);
                    return true
                }
                return false
            };
            if (D != null) {
                for (var E = 0, F = D.length; E < F; E++) {
                    G = C(D[E]) || G
                }
                if (G) {
                    l(null, A)
                }
            }
        };
        f.toggleType = function (B, G, A) {
            var C = m(B);
            if (C != null) {
                for (var D = 0, E = C.length; D < E; D++) {
                    var F = jsPlumbUtil.indexOf(v, C[D]);
                    if (F != -1) {
                        v.splice(F, 1)
                    } else {
                        v.push(C[D])
                    }
                }
                l(G, A)
            }
        };
        this.applyType = function (B, A) {
            f.setPaintStyle(B.paintStyle, A);
            f.setHoverPaintStyle(B.hoverPaintStyle, A);
            if (B.parameters) {
                for (var C in B.parameters) {
                    f.setParameter(C, B.parameters[C])
                }
            }
        };
        this.addClass = function (A) {
            if (f.canvas != null) {
                H(f.canvas, A)
            }
        };
        this.removeClass = function (A) {
            if (f.canvas != null) {
                Y(f.canvas, A)
            }
        }
    }, I = window.overlayCapableJsPlumbUIComponent = function (k) {
        R.apply(this, arguments);
        var a = this;
        this.overlays = [];
        var m = function (s) {
            var q = null;
            if (Z(s)) {
                var r = s[0], u = X.extend({
                    component: a,
                    _jsPlumb: a._jsPlumb
                }, s[1]);
                if (s.length == 3) {
                    X.extend(u, s[2])
                }
                q = new X.Overlays[a._jsPlumb.getRenderMode()][r](u)
            } else {
                if (s.constructor == String) {
                    q = new X.Overlays[a._jsPlumb.getRenderMode()][s]({
                        component: a,
                        _jsPlumb: a._jsPlumb
                    })
                } else {
                    q = s
                }
            }
            a.overlays.push(q)
        }, l = function (u) {
            var r = a.defaultOverlayKeys || [], s = u.overlays, q = function (x) {
                return a._jsPlumb.Defaults[x] || X.Defaults[x] || []
            };
            if (!s) {
                s = []
            }
            for (var v = 0, w = r.length; v < w; v++) {
                s.unshift.apply(s, q(r[v]))
            }
            return s
        };
        var o = l(k);
        if (o) {
            for (var f = 0, h = o.length; f < h; f++) {
                m(o[f])
            }
        }
        var p = function (s) {
            var r = -1;
            for (var u = 0, q = a.overlays.length; u < q; u++) {
                if (s === a.overlays[u].id) {
                    r = u;
                    break
                }
            }
            return r
        };
        this.addOverlay = function (r, q) {
            m(r);
            if (!q) {
                a.repaint()
            }
        };
        this.getOverlay = function (q) {
            var r = p(q);
            return r >= 0 ? a.overlays[r] : null
        };
        this.getOverlays = function () {
            return a.overlays
        };
        this.hideOverlay = function (q) {
            var r = a.getOverlay(q);
            if (r) {
                r.hide()
            }
        };
        this.hideOverlays = function () {
            for (var q = 0, r = a.overlays.length; q < r; q++) {
                a.overlays[q].hide()
            }
        };
        this.showOverlay = function (q) {
            var r = a.getOverlay(q);
            if (r) {
                r.show()
            }
        };
        this.showOverlays = function () {
            for (var q = 0, r = a.overlays.length; q < r; q++) {
                a.overlays[q].show()
            }
        };
        this.removeAllOverlays = function () {
            for (var q = 0, r = a.overlays.length; q < r; q++) {
                if (a.overlays[q].cleanup) {
                    a.overlays[q].cleanup()
                }
            }
            a.overlays.splice(0, a.overlays.length);
            a.repaint()
        };
        this.removeOverlay = function (q) {
            var r = p(q);
            if (r != -1) {
                var s = a.overlays[r];
                if (s.cleanup) {
                    s.cleanup()
                }
                a.overlays.splice(r, 1)
            }
        };
        this.removeOverlays = function () {
            for (var q = 0, r = arguments.length; q < r; q++) {
                a.removeOverlay(arguments[q])
            }
        };
        var n = "__label", b = function (s) {
            var r = {
                cssClass: s.cssClass,
                labelStyle: this.labelStyle,
                id: n,
                component: a,
                _jsPlumb: a._jsPlumb
            }, q = X.extend(r, s);
            return new X.Overlays[a._jsPlumb.getRenderMode()].Label(q)
        };
        if (k.label) {
            var e = k.labelLocation || a.defaultLabelLocation || 0.5, d = k.labelStyle || a._jsPlumb.Defaults.LabelStyle || X.Defaults.LabelStyle;
            this.overlays.push(b({label: k.label, location: e, labelStyle: d}))
        }
        this.setLabel = function (r) {
            var q = a.getOverlay(n);
            if (!q) {
                var s = r.constructor == String || r.constructor == Function ? {label: r} : r;
                q = b(s);
                this.overlays.push(q)
            } else {
                if (r.constructor == String || r.constructor == Function) {
                    q.setLabel(r)
                } else {
                    if (r.label) {
                        q.setLabel(r.label)
                    }
                    if (r.location) {
                        q.setLocation(r.location)
                    }
                }
            }
            if (!a._jsPlumb.isSuspendDrawing()) {
                a.repaint()
            }
        };
        this.getLabel = function () {
            var q = a.getOverlay(n);
            return q != null ? q.getLabel() : null
        };
        this.getLabelOverlay = function () {
            return a.getOverlay(n)
        };
        var g = this.applyType;
        this.applyType = function (u, s) {
            g(u, s);
            a.removeAllOverlays();
            if (u.overlays) {
                for (var q = 0, r = u.overlays.length; q < r; q++) {
                    a.addOverlay(u.overlays[q], true)
                }
            }
        };
        var c = this.setHover;
        this.setHover = function (v, s, u) {
            c.apply(a, arguments);
            for (var q = 0, r = a.overlays.length; q < r; q++) {
                a.overlays[q][v ? "addClass" : "removeClass"](a._jsPlumb.hoverClass)
            }
        }
    };
    var ag = 0, ai = function () {
        var a = ag + 1;
        ag++;
        return a
    };
    var J = function (v) {
        this.Defaults = {
            Anchor: "BottomCenter",
            Anchors: [null, null],
            ConnectionsDetachable: true,
            ConnectionOverlays: [],
            Connector: "Bezier",
            Container: null,
            DoNotThrowErrors: false,
            DragOptions: {},
            DropOptions: {},
            Endpoint: "Dot",
            EndpointOverlays: [],
            Endpoints: [null, null],
            EndpointStyle: {fillStyle: "#456"},
            EndpointStyles: [null, null],
            EndpointHoverStyle: null,
            EndpointHoverStyles: [null, null],
            HoverPaintStyle: null,
            LabelStyle: {color: "black"},
            LogEnabled: false,
            Overlays: [],
            MaxConnections: 1,
            PaintStyle: {lineWidth: 8, strokeStyle: "#456"},
            ReattachConnections: false,
            RenderMode: "svg",
            Scope: "jsPlumb_DefaultScope"
        };
        if (v) {
            X.extend(this.Defaults, v)
        }
        this.logEnabled = this.Defaults.LogEnabled;
        var bt = {}, bb = {};
        this.registerConnectionType = function (ak, al) {
            bt[ak] = X.extend({}, al)
        };
        this.registerConnectionTypes = function (ak) {
            for (var al in ak) {
                bt[al] = X.extend({}, ak[al])
            }
        };
        this.registerEndpointType = function (ak, al) {
            bb[ak] = X.extend({}, al)
        };
        this.registerEndpointTypes = function (ak) {
            for (var al in ak) {
                bb[al] = X.extend({}, ak[al])
            }
        };
        this.getType = function (ak, al) {
            return al === "connection" ? bt[ak] : bb[ak]
        };
        jsPlumbUtil.EventGenerator.apply(this);
        var bM = this, w = ai(), bD = bM.bind, G = {}, d = 1;
        this.getInstanceIndex = function () {
            return w
        };
        this.setZoom = function (ak, al) {
            d = ak;
            if (al) {
                bM.repaintEverything()
            }
        };
        this.getZoom = function () {
            return d
        };
        for (var aq in this.Defaults) {
            G[aq] = this.Defaults[aq]
        }
        this.bind = function (ak, al) {
            if ("ready" === ak && s) {
                al()
            } else {
                bD.apply(bM, [ak, al])
            }
        };
        bM.importDefaults = function (ak) {
            for (var al in ak) {
                bM.Defaults[al] = ak[al]
            }
        };
        bM.restoreDefaults = function () {
            bM.Defaults = X.extend({}, G)
        };
        var p = null, bi = null, s = false, bd = null, bv = {}, bA = {}, by = {}, bc = {}, bK = {}, bh = {}, bN = {}, bG = [], bf = [], m = this.Defaults.Scope, g = null, bx = function (ak, al) {
            if (bM.Defaults.Container) {
                X.CurrentLibrary.appendElement(ak, bM.Defaults.Container)
            } else {
                if (!al) {
                    jsPlumbAdapter.appendToRoot(ak)
                } else {
                    X.CurrentLibrary.appendElement(ak, al)
                }
            }
        }, F = 1, a0 = function () {
            return "" + F++
        }, A = function (ak) {
            return ak._nodes ? ak._nodes : ak
        }, bm = function (ao, al, am, an) {
            if (!jsPlumbAdapter.headless && !bq) {
                var ak = Q(ao, "id"), aA = bM.dragManager.getElementsForDraggable(ak);
                if (am == null) {
                    am = T()
                }
                bM.anchorManager.redraw(ak, al, am, null, an);
                if (aA) {
                    for (var ap in aA) {
                        bM.anchorManager.redraw(aA[ap].id, al, am, aA[ap].offset, an)
                    }
                }
            }
        }, C = function (ao, am) {
            var al = null;
            if (Z(ao)) {
                al = [];
                for (var ap = 0, aA = ao.length; ap < aA; ap++) {
                    var an = af(ao[ap]), ak = Q(an, "id");
                    al.push(am(an, ak))
                }
            } else {
                var an = af(ao), ak = Q(an, "id");
                al = am(an, ak)
            }
            return al
        }, aw = function (ak) {
            return by[ak]
        }, bk = function (ap, aD, am) {
            if (!jsPlumbAdapter.headless) {
                var ak = aD == null ? false : aD, ao = X.CurrentLibrary;
                if (ak) {
                    if (ao.isDragSupported(ap) && !ao.isAlreadyDraggable(ap)) {
                        var al = am || bM.Defaults.DragOptions || X.Defaults.DragOptions;
                        al = X.extend({}, al);
                        var an = ao.dragEvents.drag, aC = ao.dragEvents.stop, aA = ao.dragEvents.start;
                        al[aA] = az(al[aA], function () {
                            bM.setHoverSuspended(true);
                            bM.select({source: ap}).addClass(bM.elementDraggingClass + " " + bM.sourceElementDraggingClass, true);
                            bM.select({target: ap}).addClass(bM.elementDraggingClass + " " + bM.targetElementDraggingClass, true)
                        });
                        al[an] = az(al[an], function () {
                            var aE = ao.getUIPosition(arguments, bM.getZoom());
                            bm(ap, aE, null, true);
                            H(ap, "jsPlumb_dragged")
                        });
                        al[aC] = az(al[aC], function () {
                            var aE = ao.getUIPosition(arguments, bM.getZoom());
                            bm(ap, aE);
                            Y(ap, "jsPlumb_dragged");
                            bM.setHoverSuspended(false);
                            bM.select({source: ap}).removeClass(bM.elementDraggingClass + " " + bM.sourceElementDraggingClass, true);
                            bM.select({target: ap}).removeClass(bM.elementDraggingClass + " " + bM.targetElementDraggingClass, true)
                        });
                        var aB = x(ap);
                        bN[aB] = true;
                        var ak = bN[aB];
                        al.disabled = ak == null ? false : !ak;
                        ao.initDraggable(ap, al, false, bM);
                        bM.dragManager.register(ap)
                    }
                }
            }
        }, ar = function (ap, al) {
            var aC = X.extend({sourceIsNew: true, targetIsNew: true}, ap);
            if (al) {
                X.extend(aC, al)
            }
            if (aC.source && aC.source.endpoint) {
                aC.sourceEndpoint = aC.source
            }
            if (aC.target && aC.target.endpoint) {
                aC.targetEndpoint = aC.target
            }
            if (ap.uuids) {
                aC.sourceEndpoint = aw(ap.uuids[0]);
                aC.targetEndpoint = aw(ap.uuids[1])
            }
            if (aC.sourceEndpoint && aC.sourceEndpoint.isFull()) {
                V(bM, "could not add connection; source endpoint is full");
                return
            }
            if (aC.targetEndpoint && aC.targetEndpoint.isFull()) {
                V(bM, "could not add connection; target endpoint is full");
                return
            }
            if (aC.sourceEndpoint && !aC.sourceEndpoint.addedViaMouse) {
                aC.sourceIsNew = false
            }
            if (aC.targetEndpoint && !aC.targetEndpoint.addedViaMouse) {
                aC.targetIsNew = false
            }
            if (!aC.type && aC.sourceEndpoint) {
                aC.type = aC.sourceEndpoint.connectionType
            }
            if (aC.sourceEndpoint && aC.sourceEndpoint.connectorOverlays) {
                aC.overlays = aC.overlays || [];
                for (var am = 0, an = aC.sourceEndpoint.connectorOverlays.length; am < an; am++) {
                    aC.overlays.push(aC.sourceEndpoint.connectorOverlays[am])
                }
            }
            if (!aC["pointer-events"] && aC.sourceEndpoint && aC.sourceEndpoint.connectorPointerEvents) {
                aC["pointer-events"] = aC.sourceEndpoint.connectorPointerEvents
            }
            if (aC.target && !aC.target.endpoint && !aC.targetEndpoint && !aC.newConnection) {
                var ao = x(aC.target), aB = bu[ao], aA = B[ao];
                if (aB) {
                    if (!ba[ao]) {
                        return
                    }
                    var ak = aA != null ? aA : bM.addEndpoint(aC.target, aB);
                    if (bg[ao]) {
                        B[ao] = ak
                    }
                    aC.targetEndpoint = ak;
                    ak._makeTargetCreator = true;
                    aC.targetIsNew = true
                }
            }
            if (aC.source && !aC.source.endpoint && !aC.sourceEndpoint && !aC.newConnection) {
                var ao = x(aC.source), aB = at[ao], aA = br[ao];
                if (aB) {
                    if (!c[ao]) {
                        return
                    }
                    var ak = aA != null ? aA : bM.addEndpoint(aC.source, aB);
                    if (bo[ao]) {
                        br[ao] = ak
                    }
                    aC.sourceEndpoint = ak;
                    aC.sourceIsNew = true
                }
            }
            return aC
        }, b = function (ak) {
            var al = bM.Defaults.ConnectionType || bM.getDefaultConnectionType(), am = bM.Defaults.EndpointType || X.Endpoint, an = X.CurrentLibrary.getParent;
            if (ak.container) {
                ak.parent = ak.container
            } else {
                if (ak.sourceEndpoint) {
                    ak.parent = ak.sourceEndpoint.parent
                } else {
                    if (ak.source.constructor == am) {
                        ak.parent = ak.source.parent
                    } else {
                        ak.parent = an(ak.source)
                    }
                }
            }
            ak._jsPlumb = bM;
            ak.newConnection = b;
            ak.newEndpoint = E;
            ak.endpointsByUUID = by;
            ak.endpointsByElement = bA;
            ak.finaliseConnection = bH;
            var ao = new al(ak);
            ao.id = "con_" + a0();
            bI("click", "click", ao);
            bI("dblclick", "dblclick", ao);
            bI("contextmenu", "contextmenu", ao);
            return ao
        }, bH = function (al, ak, an) {
            ak = ak || {};
            if (!al.suspendedEndpoint) {
                ab(bv, al.scope, al)
            }
            bM.anchorManager.newConnection(al);
            bm(al.source);
            if (!ak.doNotFireConnectionEvent && ak.fireEvent !== false) {
                var am = {
                    connection: al,
                    source: al.source,
                    target: al.target,
                    sourceId: al.sourceId,
                    targetId: al.targetId,
                    sourceEndpoint: al.endpoints[0],
                    targetEndpoint: al.endpoints[1]
                };
                bM.fire("jsPlumbConnection", am, an);
                bM.fire("connection", am, an)
            }
        }, bI = function (am, al, ak) {
            ak.bind(am, function (an, ao) {
                bM.fire(al, ak, ao)
            })
        }, av = function (ak) {
            if (ak.container) {
                return ak.container
            } else {
                var am = X.CurrentLibrary.getTagName(ak.source), al = X.CurrentLibrary.getParent(ak.source);
                if (am && am.toLowerCase() === "td") {
                    return X.CurrentLibrary.getParent(al)
                } else {
                    return al
                }
            }
        }, E = function (ak) {
            var al = bM.Defaults.EndpointType || X.Endpoint;
            var an = X.extend({}, ak);
            an.parent = av(an);
            an._jsPlumb = bM;
            an.newConnection = b;
            an.newEndpoint = E;
            an.endpointsByUUID = by;
            an.endpointsByElement = bA;
            an.finaliseConnection = bH;
            an.fireDetachEvent = bp;
            an.floatingConnections = bh;
            an.getParentFromParams = av;
            an.connectionsByScope = bv;
            var am = new al(an);
            am.id = "ep_" + a0();
            bI("click", "endpointClick", am);
            bI("dblclick", "endpointDblClick", am);
            bI("contextmenu", "contextmenu", am);
            if (!jsPlumbAdapter.headless) {
                bM.dragManager.endpointAdded(ak.source)
            }
            return am
        }, k = function (ap, aA, an) {
            var aB = bA[ap];
            if (aB && aB.length) {
                for (var am = 0, ak = aB.length; am < ak; am++) {
                    for (var ao = 0, al = aB[am].connections.length; ao < al; ao++) {
                        var aC = aA(aB[am].connections[ao]);
                        if (aC) {
                            return
                        }
                    }
                    if (an) {
                        an(aB[am])
                    }
                }
            }
        }, f = function (ak) {
            for (var al in bA) {
                k(al, ak)
            }
        }, bO = function (ak, al) {
            return C(ak, function (an, am) {
                bN[am] = al;
                if (X.CurrentLibrary.isDragSupported(an)) {
                    X.CurrentLibrary.setDraggable(an, al)
                }
            })
        }, bs = function (am, al, ao) {
            al = al === "block";
            var an = null;
            if (ao) {
                if (al) {
                    an = function (ap) {
                        ap.setVisible(true, true, true)
                    }
                } else {
                    an = function (ap) {
                        ap.setVisible(false, true, true)
                    }
                }
            }
            var ak = Q(am, "id");
            k(ak, function (ap) {
                if (al && ao) {
                    var aA = ap.sourceId === ak ? 1 : 0;
                    if (ap.endpoints[aA].isVisible()) {
                        ap.setVisible(true)
                    }
                } else {
                    ap.setVisible(al)
                }
            }, an)
        }, a = function (ak) {
            return C(ak, function (am, an) {
                var al = bN[an] == null ? false : bN[an];
                al = !al;
                bN[an] = al;
                X.CurrentLibrary.setDraggable(am, al);
                return al
            })
        }, bC = function (am, ak) {
            var al = null;
            if (ak) {
                al = function (ao) {
                    var an = ao.isVisible();
                    ao.setVisible(!an)
                }
            }
            k(am, function (an) {
                var ao = an.isVisible();
                an.setVisible(!ao)
            }, al)
        }, h = function (ak) {
            var am = ak.timestamp, ap = ak.recalc, al = ak.offset, ao = ak.elId;
            if (bq && !am) {
                am = bn
            }
            if (!ap) {
                if (am && am === bK[ao]) {
                    return {o: bc[ao], s: bf[ao]}
                }
            }
            if (ap || !al) {
                var an = af(ao);
                if (an != null) {
                    bf[ao] = aj(an);
                    bc[ao] = P(an, bM);
                    bK[ao] = am
                }
            } else {
                bc[ao] = al;
                if (bf[ao] == null) {
                    var an = af(ao);
                    if (an != null) {
                        bf[ao] = aj(an)
                    }
                }
            }
            if (bc[ao] && !bc[ao].right) {
                bc[ao].right = bc[ao].left + bf[ao][0];
                bc[ao].bottom = bc[ao].top + bf[ao][1];
                bc[ao].width = bf[ao][0];
                bc[ao].height = bf[ao][1];
                bc[ao].centerx = bc[ao].left + (bc[ao].width / 2);
                bc[ao].centery = bc[ao].top + (bc[ao].height / 2)
            }
            return {o: bc[ao], s: bf[ao]}
        }, bE = function (al) {
            var ak = bc[al];
            if (!ak) {
                return h({elId: al})
            } else {
                return {o: ak, s: bf[al]}
            }
        }, x = function (ao, an, al) {
            var am = af(ao);
            var ak = Q(am, "id");
            if (!ak || ak == "undefined") {
                if (arguments.length == 2 && arguments[1] != undefined) {
                    ak = an
                } else {
                    if (arguments.length == 1 || (arguments.length == 3 && !arguments[2])) {
                        ak = "jsPlumb_" + w + "_" + a0()
                    }
                }
                if (!al) {
                    ah(am, "id", ak)
                }
            }
            return ak
        }, az = function (ak, am, al) {
            ak = ak || function () {
            };
            am = am || function () {
            };
            return function () {
                var ao = null;
                try {
                    ao = am.apply(this, arguments)
                } catch (an) {
                    V(bM, "jsPlumb function failed : " + an)
                }
                if (al == null || (ao !== al)) {
                    try {
                        ak.apply(this, arguments)
                    } catch (an) {
                        V(bM, "wrapped function failed : " + an)
                    }
                }
                return ao
            }
        };
        this.isConnectionBeingDragged = function () {
            return bd != null
        };
        this.setConnectionBeingDragged = function (ak) {
            bd = ak
        };
        this.connectorClass = "_jsPlumb_connector";
        this.hoverClass = "_jsPlumb_hover";
        this.endpointClass = "_jsPlumb_endpoint";
        this.endpointConnectedClass = "_jsPlumb_endpoint_connected";
        this.endpointFullClass = "_jsPlumb_endpoint_full";
        this.endpointDropAllowedClass = "_jsPlumb_endpoint_drop_allowed";
        this.endpointDropForbiddenClass = "_jsPlumb_endpoint_drop_forbidden";
        this.overlayClass = "_jsPlumb_overlay";
        this.draggingClass = "_jsPlumb_dragging";
        this.elementDraggingClass = "_jsPlumb_element_dragging";
        this.sourceElementDraggingClass = "_jsPlumb_source_element_dragging";
        this.targetElementDraggingClass = "_jsPlumb_target_element_dragging";
        this.endpointAnchorClassPrefix = "_jsPlumb_endpoint_anchor";
        this.Anchors = {};
        this.Connectors = {canvas: {}, svg: {}, vml: {}};
        this.Endpoints = {canvas: {}, svg: {}, vml: {}};
        this.Overlays = {canvas: {}, svg: {}, vml: {}};
        this.ConnectorRenderers = {};
        this.addClass = function (ak, al) {
            return X.CurrentLibrary.addClass(ak, al)
        };
        this.removeClass = function (ak, al) {
            return X.CurrentLibrary.removeClass(ak, al)
        };
        this.hasClass = function (ak, al) {
            return X.CurrentLibrary.hasClass(ak, al)
        };
        this.addEndpoint = function (aF, aE, ak) {
            ak = ak || {};
            var aG = X.extend({}, ak);
            X.extend(aG, aE);
            aG.endpoint = aG.endpoint || bM.Defaults.Endpoint || X.Defaults.Endpoint;
            aG.paintStyle = aG.paintStyle || bM.Defaults.EndpointStyle || X.Defaults.EndpointStyle;
            aF = A(aF);
            var aC = [], ap = (Z(aF) || (aF.length != null && !W(aF))) ? aF : [aF];
            for (var aB = 0, aD = ap.length; aB < aD; aB++) {
                var am = af(ap[aB]), aH = x(am);
                aG.source = am;
                h({elId: aH, timestamp: bn});
                var an = E(aG);
                if (aG.parentAnchor) {
                    an.parentAnchor = aG.parentAnchor
                }
                ab(bA, aH, an);
                var ao = bc[aH], aA = bf[aH];
                var al = an.anchor.compute({
                    xy: [ao.left, ao.top],
                    wh: aA,
                    element: an,
                    timestamp: bn
                });
                var aI = {anchorLoc: al, timestamp: bn};
                if (bq) {
                    aI.recalc = false
                }
                if (!bq) {
                    an.paint(aI)
                }
                aC.push(an)
            }
            return aC.length == 1 ? aC[0] : aC
        };
        this.addEndpoints = function (al, ap, aA) {
            var am = [];
            for (var an = 0, ao = ap.length; an < ao; an++) {
                var ak = bM.addEndpoint(al, ap[an], aA);
                if (Z(ak)) {
                    Array.prototype.push.apply(am, ak)
                } else {
                    am.push(ak)
                }
            }
            return am
        };
        this.animate = function (ao, ap, aA) {
            var an = af(ao), ak = Q(ao, "id");
            aA = aA || {};
            var al = X.CurrentLibrary.dragEvents.step;
            var am = X.CurrentLibrary.dragEvents.complete;
            aA[al] = az(aA[al], function () {
                bM.repaint(ak)
            });
            aA[am] = az(aA[am], function () {
                bM.repaint(ak)
            });
            X.CurrentLibrary.animate(an, ap, aA)
        };
        this.checkCondition = function (an, al) {
            var aA = bM.getListener(an), am = true;
            if (aA && aA.length > 0) {
                try {
                    for (var ao = 0, ap = aA.length; ao < ap; ao++) {
                        am = am && aA[ao](al)
                    }
                } catch (ak) {
                    V(bM, "cannot check condition [" + an + "]" + ak)
                }
            }
            return am
        };
        this.checkASyncCondition = function (an, al, am, ao) {
            var ap = bM.getListener(an);
            if (ap && ap.length > 0) {
                try {
                    ap[0](al, am, ao)
                } catch (ak) {
                    V(bM, "cannot asynchronously check condition [" + an + "]" + ak)
                }
            }
        };
        this.connect = function (ak, am) {
            var an = ar(ak, am), al;
            if (an) {
                if (an.deleteEndpointsOnDetach == null) {
                    an.deleteEndpointsOnDetach = true
                }
                al = b(an);
                bH(al, an)
            }
            return al
        };
        this.deleteEndpoint = function (ak, al) {
            bM.doWhileSuspended(function () {
                var am = (typeof ak == "string") ? by[ak] : ak;
                if (am) {
                    var ap = am.getUuid();
                    if (ap) {
                        by[ap] = null
                    }
                    am.detachAll().cleanup();
                    if (am.endpoint.cleanup) {
                        am.endpoint.cleanup()
                    }
                    jsPlumbUtil.removeElements(am.endpoint.getDisplayElements());
                    bM.anchorManager.deleteEndpoint(am);
                    for (var an in bA) {
                        var aC = bA[an];
                        if (aC) {
                            var ao = [];
                            for (var aA = 0, aB = aC.length; aA < aB; aA++) {
                                if (aC[aA] != am) {
                                    ao.push(aC[aA])
                                }
                            }
                            bA[an] = ao
                        }
                        if (bA[an].length < 1) {
                            delete bA[an]
                        }
                    }
                    if (!jsPlumbAdapter.headless) {
                        bM.dragManager.endpointDeleted(am)
                    }
                }
                return bM
            }, al)
        };
        this.deleteEveryEndpoint = function () {
            bM.doWhileSuspended(function () {
                for (var ak in bA) {
                    var an = bA[ak];
                    if (an && an.length) {
                        for (var al = 0, am = an.length; al < am; al++) {
                            bM.deleteEndpoint(an[al], true)
                        }
                    }
                }
                bA = {};
                by = {};
                bM.anchorManager.reset();
                bM.dragManager.reset()
            });
            return bM
        };
        var bp = function (am, ak, ap) {
            var an = bM.Defaults.ConnectionType || bM.getDefaultConnectionType(), ao = am.constructor == an, al = ao ? {
                connection: am,
                source: am.source,
                target: am.target,
                sourceId: am.sourceId,
                targetId: am.targetId,
                sourceEndpoint: am.endpoints[0],
                targetEndpoint: am.endpoints[1]
            } : am;
            if (ak) {
                bM.fire("jsPlumbConnectionDetached", al, ap);
                bM.fire("connectionDetached", al, ap)
            }
            bM.anchorManager.connectionDetached(al)
        };
        this.detach = function () {
            if (arguments.length == 0) {
                return
            }
            var ao = bM.Defaults.ConnectionType || bM.getDefaultConnectionType(), an = arguments[0].constructor == ao, ap = arguments.length == 2 ? an ? (arguments[1] || {}) : arguments[0] : arguments[0], ak = (ap.fireEvent !== false), aB = ap.forceDetach, aA = an ? arguments[0] : ap.connection;
            if (aA) {
                if (aB || jsPlumbUtil.functionChain(true, false, [[aA.endpoints[0], "isDetachAllowed", [aA]], [aA.endpoints[1], "isDetachAllowed", [aA]], [aA, "isDetachAllowed", [aA]], [bM, "checkCondition", ["beforeDetach", aA]]])) {
                    aA.endpoints[0].detach(aA, false, true, ak)
                }
            } else {
                var aC = X.extend({}, ap);
                if (aC.uuids) {
                    aw(aC.uuids[0]).detachFrom(aw(aC.uuids[1]), ak)
                } else {
                    if (aC.sourceEndpoint && aC.targetEndpoint) {
                        aC.sourceEndpoint.detachFrom(aC.targetEndpoint)
                    } else {
                        var al = x(aC.source), am = x(aC.target);
                        k(al, function (aD) {
                            if ((aD.sourceId == al && aD.targetId == am) || (aD.targetId == al && aD.sourceId == am)) {
                                if (bM.checkCondition("beforeDetach", aD)) {
                                    aD.endpoints[0].detach(aD, false, true, ak)
                                }
                            }
                        })
                    }
                }
            }
        };
        this.detachAllConnections = function (am, al) {
            al = al || {};
            am = af(am);
            var ak = Q(am, "id"), ap = bA[ak];
            if (ap && ap.length) {
                for (var an = 0, ao = ap.length; an < ao; an++) {
                    ap[an].detachAll(al.fireEvent)
                }
            }
            return bM
        };
        this.detachEveryConnection = function (al) {
            al = al || {};
            for (var ak in bA) {
                var ao = bA[ak];
                if (ao && ao.length) {
                    for (var am = 0, an = ao.length; am < an; am++) {
                        ao[am].detachAll(al.fireEvent)
                    }
                }
            }
            bv = {};
            return bM
        };
        this.draggable = function (al, an) {
            if (typeof al == "object" && al.length) {
                for (var am = 0, ao = al.length; am < ao; am++) {
                    var ak = af(al[am]);
                    if (ak) {
                        bk(ak, true, an)
                    }
                }
            } else {
                if (al._nodes) {
                    for (var am = 0, ao = al._nodes.length; am < ao; am++) {
                        var ak = af(al._nodes[am]);
                        if (ak) {
                            bk(ak, true, an)
                        }
                    }
                } else {
                    var ak = af(al);
                    if (ak) {
                        bk(ak, true, an)
                    }
                }
            }
            return bM
        };
        this.extend = function (ak, al) {
            return X.CurrentLibrary.extend(ak, al)
        };
        this.getDefaultEndpointType = function () {
            return X.Endpoint
        };
        this.getDefaultConnectionType = function () {
            return X.Connection
        };
        var bL = function (ak, al, an, ap) {
            for (var am = 0, ao = ak.length; am < ao; am++) {
                ak[am][al].apply(ak[am], an)
            }
            return ap(ak)
        }, l = function (ak, al, an) {
            var ao = [];
            for (var am = 0, ap = ak.length; am < ap; am++) {
                ao.push([ak[am][al].apply(ak[am], an), ak[am]])
            }
            return ao
        }, ay = function (ak, al, am) {
            return function () {
                return bL(ak, al, arguments, am)
            }
        }, au = function (ak, al) {
            return function () {
                return l(ak, al, arguments)
            }
        }, bJ = function (ao, ak) {
            var al = [];
            if (ao) {
                if (typeof ao == "string") {
                    if (ao === "*") {
                        return ao
                    }
                    al.push(ao)
                } else {
                    if (ak) {
                        al = ao
                    } else {
                        for (var am = 0, an = ao.length; am < an; am++) {
                            al.push(x(af(ao[am])))
                        }
                    }
                }
            }
            return al
        }, D = function (ak, al, am) {
            if (ak === "*") {
                return true
            }
            return ak.length > 0 ? K(ak, al) != -1 : !am
        };
        this.getConnections = function (al, aE) {
            if (!al) {
                al = {}
            } else {
                if (al.constructor == String) {
                    al = {scope: al}
                }
            }
            var am = al.scope || bM.getDefaultScope(), an = bJ(am, true), aF = bJ(al.source), ap = bJ(al.target), aB = (!aE && an.length > 1) ? {} : [], ak = function (aH, aG) {
                if (!aE && an.length > 1) {
                    var aI = aB[aH];
                    if (aI == null) {
                        aI = [];
                        aB[aH] = aI
                    }
                    aI.push(aG)
                } else {
                    aB.push(aG)
                }
            };
            for (var aC in bv) {
                if (D(an, aC)) {
                    for (var aD = 0, aA = bv[aC].length; aD < aA; aD++) {
                        var ao = bv[aC][aD];
                        if (D(aF, ao.sourceId) && D(ap, ao.targetId)) {
                            ak(aC, ao)
                        }
                    }
                }
            }
            return aB
        };
        var r = function (al, ak) {
            return function (am) {
                for (var ao = 0, an = al.length; ao < an; ao++) {
                    am(al[ao])
                }
                return ak(al)
            }
        }, o = function (ak) {
            return function (al) {
                return ak[al]
            }
        };
        var n = function (al, ak) {
            var aA = {
                length: al.length,
                each: r(al, ak),
                get: o(al)
            }, am = ["setHover", "removeAllOverlays", "setLabel", "addClass", "addOverlay", "removeOverlay", "removeOverlays", "showOverlay", "hideOverlay", "showOverlays", "hideOverlays", "setPaintStyle", "setHoverPaintStyle", "setSuspendEvents", "setParameter", "setParameters", "setVisible", "repaint", "addType", "toggleType", "removeType", "removeClass", "setType", "bind", "unbind"], an = ["getLabel", "getOverlay", "isHover", "getParameter", "getParameters", "getPaintStyle", "getHoverPaintStyle", "isVisible", "hasType", "getType", "isSuspendEvents"];
            for (var ap = 0, ao = am.length; ap < ao; ap++) {
                aA[am[ap]] = ay(al, am[ap], ak)
            }
            for (var ap = 0, ao = an.length; ap < ao; ap++) {
                aA[an[ap]] = au(al, an[ap])
            }
            return aA
        };
        var y = function (ak) {
            var al = n(ak, y);
            return X.CurrentLibrary.extend(al, {
                setDetachable: ay(ak, "setDetachable", y),
                setReattach: ay(ak, "setReattach", y),
                setConnector: ay(ak, "setConnector", y),
                detach: function () {
                    for (var an = 0, am = ak.length; an < am; an++) {
                        bM.detach(ak[an])
                    }
                },
                isDetachable: au(ak, "isDetachable"),
                isReattach: au(ak, "isReattach")
            })
        };
        var bl = function (ak) {
            var al = n(ak, bl);
            return X.CurrentLibrary.extend(al, {
                setEnabled: ay(ak, "setEnabled", bl),
                setAnchor: ay(ak, "setAnchor", bl),
                isEnabled: au(ak, "isEnabled"),
                detachAll: function () {
                    for (var an = 0, am = ak.length; an < am; an++) {
                        ak[an].detachAll()
                    }
                },
                remove: function () {
                    for (var an = 0, am = ak.length; an < am; an++) {
                        bM.deleteEndpoint(ak[an])
                    }
                }
            })
        };
        this.select = function (al) {
            al = al || {};
            al.scope = al.scope || "*";
            var ak = al.connections || bM.getConnections(al, true);
            return y(ak)
        };
        this.selectEndpoints = function (am) {
            am = am || {};
            am.scope = am.scope || "*";
            var aE = !am.element && !am.source && !am.target, aB = aE ? "*" : bJ(am.element), aH = aE ? "*" : bJ(am.source), aK = aE ? "*" : bJ(am.target), ap = bJ(am.scope, true);
            var aI = [];
            for (var aG in bA) {
                var al = D(aB, aG, true), ao = D(aH, aG, true), aL = aH != "*", aJ = D(aK, aG, true), aC = aK != "*";
                if (al || ao || aJ) {
                    inner:for (var an = 0, aD = bA[aG].length; an < aD; an++) {
                        var aA = bA[aG][an];
                        if (D(ap, aA.scope, true)) {
                            var ak = (aL && aH.length > 0 && !aA.isSource), aF = (aC && aK.length > 0 && !aA.isTarget);
                            if (ak || aF) {
                                continue inner
                            }
                            aI.push(aA)
                        }
                    }
                }
            }
            return bl(aI)
        };
        this.getAllConnections = function () {
            return bv
        };
        this.getDefaultScope = function () {
            return m
        };
        this.getEndpoint = aw;
        this.getEndpoints = function (ak) {
            return bA[x(ak)]
        };
        this.getId = x;
        this.getOffset = function (ak) {
            var al = bc[ak];
            return h({elId: ak})
        };
        this.getSelector = function () {
            return X.CurrentLibrary.getSelector.apply(null, arguments)
        };
        this.getSize = function (ak) {
            var al = bf[ak];
            if (!al) {
                h({elId: ak})
            }
            return bf[ak]
        };
        this.appendElement = bx;
        var bB = false;
        this.isHoverSuspended = function () {
            return bB
        };
        this.setHoverSuspended = function (ak) {
            bB = ak
        };
        var bw = function (ak) {
            return function () {
                return jsPlumbAdapter.isRenderModeAvailable(ak)
            }
        };
        this.isCanvasAvailable = bw("canvas");
        this.isSVGAvailable = bw("svg");
        this.isVMLAvailable = bw("vml");
        this.hide = function (al, ak) {
            bs(al, "none", ak);
            return bM
        };
        this.idstamp = a0;
        this.init = function () {
            if (!s) {
                bM.anchorManager = new X.AnchorManager({jsPlumbInstance: bM});
                bM.setRenderMode(bM.Defaults.RenderMode);
                s = true;
                bM.fire("ready", bM)
            }
        };
        this.log = p;
        this.jsPlumbUIComponent = R;
        this.makeAnchor = function () {
            var am = function (aC, aB) {
                if (X.Anchors[aC]) {
                    return new X.Anchors[aC](aB)
                }
                if (!bM.Defaults.DoNotThrowErrors) {
                    throw {msg: "jsPlumb: unknown anchor type '" + aC + "'"}
                }
            };
            if (arguments.length == 0) {
                return null
            }
            var ak = arguments[0], ao = arguments[1], ap = arguments[2], an = null;
            if (ak.compute && ak.getOrientation) {
                return ak
            } else {
                if (typeof ak == "string") {
                    an = am(arguments[0], {elementId: ao, jsPlumbInstance: bM})
                } else {
                    if (Z(ak)) {
                        if (Z(ak[0]) || W(ak[0])) {
                            if (ak.length == 2 && W(ak[0]) && L(ak[1])) {
                                var aA = X.extend({
                                    elementId: ao,
                                    jsPlumbInstance: bM
                                }, ak[1]);
                                an = am(ak[0], aA)
                            } else {
                                an = new X.DynamicAnchor({
                                    anchors: ak,
                                    selector: null,
                                    elementId: ao,
                                    jsPlumbInstance: ap
                                })
                            }
                        } else {
                            var al = {
                                x: ak[0],
                                y: ak[1],
                                orientation: (ak.length >= 4) ? [ak[2], ak[3]] : [0, 0],
                                offsets: (ak.length >= 6) ? [ak[4], ak[5]] : [0, 0],
                                elementId: ao,
                                jsPlumbInstance: ap,
                                cssClass: ak.length == 7 ? ak[6] : null
                            };
                            an = new X.Anchor(al);
                            an.clone = function () {
                                return new X.Anchor(al)
                            }
                        }
                    }
                }
            }
            if (!an.id) {
                an.id = "anchor_" + a0()
            }
            return an
        };
        this.makeAnchors = function (am, ao, ap) {
            var ak = [];
            for (var an = 0, al = am.length; an < al; an++) {
                if (typeof am[an] == "string") {
                    ak.push(X.Anchors[am[an]]({
                        elementId: ao,
                        jsPlumbInstance: ap
                    }))
                } else {
                    if (Z(am[an])) {
                        ak.push(bM.makeAnchor(am[an], ao, ap))
                    }
                }
            }
            return ak
        };
        this.makeDynamicAnchor = function (al, ak) {
            return new X.DynamicAnchor({
                anchors: al,
                selector: ak,
                elementId: null,
                jsPlumbInstance: bM
            })
        };
        var bu = {}, B = {}, bg = {}, ax = {}, e = function (al, ak) {
            al.paintStyle = al.paintStyle || bM.Defaults.EndpointStyles[ak] || bM.Defaults.EndpointStyle || X.Defaults.EndpointStyles[ak] || X.Defaults.EndpointStyle;
            al.hoverPaintStyle = al.hoverPaintStyle || bM.Defaults.EndpointHoverStyles[ak] || bM.Defaults.EndpointHoverStyle || X.Defaults.EndpointHoverStyles[ak] || X.Defaults.EndpointHoverStyle;
            al.anchor = al.anchor || bM.Defaults.Anchors[ak] || bM.Defaults.Anchor || X.Defaults.Anchors[ak] || X.Defaults.Anchor;
            al.endpoint = al.endpoint || bM.Defaults.Endpoints[ak] || bM.Defaults.Endpoint || X.Defaults.Endpoints[ak] || X.Defaults.Endpoint
        };
        this.makeTarget = function (aC, aB, ak) {
            var aE = X.extend({_jsPlumb: bM}, ak);
            X.extend(aE, aB);
            e(aE, 1);
            var an = X.CurrentLibrary, am = aE.scope || bM.Defaults.Scope, aA = !(aE.deleteEndpointsOnDetach === false), aD = aE.maxConnections || -1, aF = aE.onMaxConnections;
            _doOne = function (aG) {
                var aI = x(aG);
                bu[aI] = aE;
                bg[aI] = aE.uniqueEndpoint, ax[aI] = aD, ba[aI] = true, proxyComponent = new R(aE);
                var aJ = X.extend({}, aE.dropOptions || {}), aK = function () {
                    var aU = X.CurrentLibrary.getDropEvent(arguments), aR = bM.select({target: aI}).length;
                    bM.currentlyDragging = false;
                    var aT = af(an.getDragObject(arguments)), aS = Q(aT, "dragId"), aX = Q(aT, "originalScope"), aM = bh[aS], aW = aM.endpoints[0], aY = aE.endpoint ? X.extend({}, aE.endpoint) : {};
                    if (!ba[aI] || ax[aI] > 0 && aR >= ax[aI]) {
                        if (aF) {
                            aF({element: aG, connection: aM}, aU)
                        }
                        return false
                    }
                    aW.anchor.locked = false;
                    if (aX) {
                        an.setDragScope(aT, aX)
                    }
                    var aO = proxyComponent.isDropAllowed(aM.sourceId, x(aG), aM.scope, aM, null);
                    if (aM.endpointsToDeleteOnDetach) {
                        if (aW === aM.endpointsToDeleteOnDetach[0]) {
                            aM.endpointsToDeleteOnDetach[0] = null
                        } else {
                            if (aW === aM.endpointsToDeleteOnDetach[1]) {
                                aM.endpointsToDeleteOnDetach[1] = null
                            }
                        }
                    }
                    if (aM.suspendedEndpoint) {
                        aM.targetId = aM.suspendedEndpoint.elementId;
                        aM.target = an.getElementObject(aM.suspendedEndpoint.elementId);
                        aM.endpoints[1] = aM.suspendedEndpoint
                    }
                    if (aO) {
                        aW.detach(aM, false, true, false);
                        var aV = B[aI] || bM.addEndpoint(aG, aE);
                        if (aE.uniqueEndpoint) {
                            B[aI] = aV
                        }
                        aV._makeTargetCreator = true;
                        if (aV.anchor.positionFinder != null) {
                            var aL = an.getUIPosition(arguments, bM.getZoom()), aP = P(aG, bM), aZ = aj(aG), aQ = aV.anchor.positionFinder(aL, aP, aZ, aV.anchor.constructorParams);
                            aV.anchor.x = aQ[0];
                            aV.anchor.y = aQ[1]
                        }
                        var aN = bM.connect({
                            source: aW,
                            target: aV,
                            scope: aX,
                            previousConnection: aM,
                            container: aM.parent,
                            deleteEndpointsOnDetach: aA,
                            endpointsToDeleteOnDetach: aA ? [aW, aV] : null,
                            doNotFireConnectionEvent: aW.endpointWillMoveAfterConnection
                        });
                        if (aM.endpoints[1]._makeTargetCreator && aM.endpoints[1].connections.length < 2) {
                            bM.deleteEndpoint(aM.endpoints[1])
                        }
                        aN.repaint()
                    } else {
                        if (aM.suspendedEndpoint) {
                            if (aM.isReattach()) {
                                aM.setHover(false);
                                aM.floatingAnchorIndex = null;
                                aM.suspendedEndpoint.addConnection(aM);
                                bM.repaint(aW.elementId)
                            } else {
                                aW.detach(aM, false, true, true, aU)
                            }
                        }
                    }
                };
                var aH = an.dragEvents.drop;
                aJ.scope = aJ.scope || am;
                aJ[aH] = az(aJ[aH], aK);
                an.initDroppable(aG, aJ, true)
            };
            aC = A(aC);
            var ao = aC.length && aC.constructor != String ? aC : [aC];
            for (var ap = 0, al = ao.length; ap < al; ap++) {
                _doOne(af(ao[ap]))
            }
            return bM
        };
        this.unmakeTarget = function (al, ak) {
            al = X.CurrentLibrary.getElementObject(al);
            var am = x(al);
            if (!ak) {
                delete bu[am];
                delete bg[am];
                delete ax[am];
                delete ba[am]
            }
            return bM
        };
        this.makeTargets = function (am, ak, ao) {
            for (var an = 0, al = am.length; an < al; an++) {
                bM.makeTarget(am[an], ak, ao)
            }
        };
        var at = {}, br = {}, bo = {}, c = {}, u = {}, q = {}, ba = {}, bz = function (ap, ak, aA) {
            var am = ap.target || ap.srcElement, an = false, al = bM.getSelector(ak, aA);
            for (var ao = 0; ao < al.length; ao++) {
                if (al[ao] == am) {
                    an = true;
                    break
                }
            }
            return an
        };
        this.makeSource = function (aA, ap, ak) {
            var aC = X.extend({}, ak);
            X.extend(aC, ap);
            e(aC, 0);
            var am = X.CurrentLibrary, aB = aC.maxConnections || -1, aD = aC.onMaxConnections, aE = function (aO) {
                var aI = x(aO), aM = function () {
                    return aC.parent == null ? aC.parent : aC.parent === "parent" ? am.getElementObject(am.getDOMElement(aO).parentNode) : am.getElementObject(aC.parent)
                }, aJ = aC.parent != null ? bM.getId(aM()) : aI;
                at[aJ] = aC;
                bo[aJ] = aC.uniqueEndpoint;
                c[aJ] = true;
                var aH = am.dragEvents.stop, aP = am.dragEvents.drag, aN = X.extend({}, aC.dragOptions || {}), aF = aN.drag, aL = aN.stop, aK = null, aQ = false;
                q[aJ] = aB;
                aN.scope = aN.scope || aC.scope;
                aN[aP] = az(aN[aP], function () {
                    if (aF) {
                        aF.apply(this, arguments)
                    }
                    aQ = false
                });
                aN[aH] = az(aN[aH], function () {
                    if (aL) {
                        aL.apply(this, arguments)
                    }
                    bM.currentlyDragging = false;
                    if (aK.connections.length == 0) {
                        bM.deleteEndpoint(aK)
                    } else {
                        am.unbind(aK.canvas, "mousedown");
                        var aU = aC.anchor || bM.Defaults.Anchor, aS = aK.anchor, aT = aK.connections[0];
                        aK.setAnchor(bM.makeAnchor(aU, aI, bM));
                        if (aC.parent) {
                            var aW = aM();
                            if (aW) {
                                var aV = aK.elementId, aR = aC.container || bM.Defaults.Container || X.Defaults.Container;
                                aK.setElement(aW, aR);
                                aK.endpointWillMoveAfterConnection = false;
                                bM.anchorManager.rehomeEndpoint(aV, aW);
                                aT.previousConnection = null;
                                U(bv[aT.scope], function (aX) {
                                    return aX.id === aT.id
                                });
                                bM.anchorManager.connectionDetached({
                                    sourceId: aT.sourceId,
                                    targetId: aT.targetId,
                                    connection: aT
                                });
                                bH(aT)
                            }
                        }
                        aK.repaint();
                        bM.repaint(aK.elementId);
                        bM.repaint(aT.targetId)
                    }
                });
                var aG = function (aR) {
                    if (!c[aJ]) {
                        return
                    }
                    if (aC.filter) {
                        var aZ = am.getOriginalEvent(aR), aY = jsPlumbUtil.isString(aC.filter) ? bz(aZ, aO, aC.filter) : aC.filter(aZ, aO);
                        if (aY === false) {
                            return
                        }
                    }
                    var aU = bM.select({source: aJ}).length;
                    if (q[aJ] >= 0 && aU >= q[aJ]) {
                        if (aD) {
                            aD({element: aO, maxConnections: aB}, aR)
                        }
                        return false
                    }
                    var a2 = h({elId: aI}).o, a5 = bM.getZoom(), a3 = (((aR.pageX || aR.page.x) / a5) - a2.left) / a2.width, a4 = (((aR.pageY || aR.page.y) / a5) - a2.top) / a2.height, aT = a3, aW = a4;
                    if (aC.parent) {
                        var a6 = aM(), aS = x(a6);
                        a2 = h({elId: aS}).o;
                        aT = ((aR.pageX || aR.page.x) - a2.left) / a2.width, aW = ((aR.pageY || aR.page.y) - a2.top) / a2.height
                    }
                    var aX = {};
                    X.extend(aX, aC);
                    aX.isSource = true;
                    aX.anchor = [a3, a4, 0, 0];
                    aX.parentAnchor = [aT, aW, 0, 0];
                    aX.dragOptions = aN;
                    if (aC.parent) {
                        var aV = aX.container || bM.Defaults.Container || X.Defaults.Container;
                        if (aV) {
                            aX.container = aV
                        } else {
                            aX.container = X.CurrentLibrary.getParent(aM())
                        }
                    }
                    aK = bM.addEndpoint(aI, aX);
                    aQ = true;
                    aK.endpointWillMoveAfterConnection = aC.parent != null;
                    aK.endpointWillMoveTo = aC.parent ? aM() : null;
                    aK.addedViaMouse = true;
                    var a1 = function () {
                        if (aQ) {
                            bM.deleteEndpoint(aK)
                        }
                    };
                    bM.registerListener(aK.canvas, "mouseup", a1);
                    bM.registerListener(aO, "mouseup", a1);
                    am.trigger(aK.canvas, "mousedown", aR)
                };
                bM.registerListener(aO, "mousedown", aG);
                u[aI] = aG;
                if (aC.filter && jsPlumbUtil.isString(aC.filter)) {
                    am.setDragFilter(aO, aC.filter)
                }
            };
            aA = A(aA);
            var an = aA.length && aA.constructor != String ? aA : [aA];
            for (var ao = 0, al = an.length; ao < al; ao++) {
                aE(af(an[ao]))
            }
            return bM
        };
        this.unmakeSource = function (am, al) {
            am = X.CurrentLibrary.getElementObject(am);
            var ak = x(am), an = u[ak];
            if (an) {
                bM.unregisterListener(am, "mousedown", an)
            }
            if (!al) {
                delete at[ak];
                delete bo[ak];
                delete c[ak];
                delete u[ak];
                delete q[ak]
            }
            return bM
        };
        this.unmakeEverySource = function () {
            for (var ak in c) {
                bM.unmakeSource(ak, true)
            }
            at = {};
            bo = {};
            c = {};
            u = {}
        };
        this.unmakeEveryTarget = function () {
            for (var ak in ba) {
                bM.unmakeTarget(ak, true)
            }
            bu = {};
            bg = {};
            ax = {};
            ba = {};
            return bM
        };
        this.makeSources = function (am, ak, ao) {
            for (var an = 0, al = am.length; an < al; an++) {
                bM.makeSource(am[an], ak, ao)
            }
            return bM
        };
        var bF = function (am, an, al, aB) {
            var aA = am == "source" ? c : ba;
            if (W(an)) {
                aA[an] = aB ? !aA[an] : al
            } else {
                if (an.length) {
                    an = A(an);
                    for (var ap = 0, ao = an.length; ap < ao; ap++) {
                        var ak = _el = X.CurrentLibrary.getElementObject(an[ap]), ak = x(_el);
                        aA[ak] = aB ? !aA[ak] : al
                    }
                }
            }
            return bM
        };
        this.setSourceEnabled = function (al, ak) {
            return bF("source", al, ak)
        };
        this.toggleSourceEnabled = function (ak) {
            bF("source", ak, null, true);
            return bM.isSourceEnabled(ak)
        };
        this.isSource = function (ak) {
            ak = X.CurrentLibrary.getElementObject(ak);
            return c[x(ak)] != null
        };
        this.isSourceEnabled = function (ak) {
            ak = X.CurrentLibrary.getElementObject(ak);
            return c[x(ak)] === true
        };
        this.setTargetEnabled = function (al, ak) {
            return bF("target", al, ak)
        };
        this.toggleTargetEnabled = function (ak) {
            bF("target", ak, null, true);
            return bM.isTargetEnabled(ak)
        };
        this.isTarget = function (ak) {
            ak = X.CurrentLibrary.getElementObject(ak);
            return ba[x(ak)] != null
        };
        this.isTargetEnabled = function (ak) {
            ak = X.CurrentLibrary.getElementObject(ak);
            return ba[x(ak)] === true
        };
        this.ready = function (ak) {
            bM.bind("ready", ak)
        };
        this.repaint = function (am, ak, al) {
            if (typeof am == "object" && am.length) {
                for (var ao = 0, an = am.length; ao < an; ao++) {
                    bm(af(am[ao]), ak, al)
                }
            } else {
                bm(af(am), ak, al)
            }
            return bM
        };
        this.repaintEverything = function () {
            var ak = null;
            for (var al in bA) {
                bm(af(al), null, ak)
            }
            return bM
        };
        this.removeAllEndpoints = function (al, ak) {
            var am = function (an) {
                var aB = jsPlumbUtil.isString(an) ? an : x(af(an)), ao = bA[aB];
                if (ao) {
                    for (var aA = 0, ap = ao.length; aA < ap; aA++) {
                        bM.deleteEndpoint(ao[aA])
                    }
                }
                delete bA[aB];
                if (ak) {
                    var aC = X.CurrentLibrary.getDOMElement(af(an));
                    if (aC && aC.nodeType != 3 && aC.nodeType != 8) {
                        for (var aA = 0, ap = aC.childNodes.length; aA < ap; aA++) {
                            am(aC.childNodes[aA])
                        }
                    }
                }
            };
            am(al);
            return bM
        };
        this.remove = function (am) {
            var ak = af(am);
            var al = jsPlumbUtil.isString(am) ? am : x(ak);
            bM.doWhileSuspended(function () {
                bM.removeAllEndpoints(al, true);
                bM.dragManager.elementRemoved(al)
            });
            X.CurrentLibrary.removeElement(ak)
        };
        var be = {}, bj = function () {
            for (var am in be) {
                for (var an = 0, al = be[am].length; an < al; an++) {
                    var ak = be[am][an];
                    X.CurrentLibrary.unbind(ak.el, ak.event, ak.listener)
                }
            }
            be = {}
        };
        this.registerListener = function (al, am, ak) {
            X.CurrentLibrary.bind(al, am, ak);
            ab(be, am, {el: al, event: am, listener: ak})
        };
        this.unregisterListener = function (al, am, ak) {
            X.CurrentLibrary.unbind(al, am, ak);
            U(be, function (an) {
                return an.type == am && an.listener == ak
            })
        };
        this.reset = function () {
            bM.deleteEveryEndpoint();
            bM.unbind();
            bu = {};
            B = {};
            bg = {};
            ax = {};
            at = {};
            br = {};
            bo = {};
            q = {};
            bj();
            bM.anchorManager.reset();
            if (!jsPlumbAdapter.headless) {
                bM.dragManager.reset()
            }
        };
        this.setDefaultScope = function (ak) {
            m = ak;
            return bM
        };
        this.setDraggable = bO;
        this.setId = function (aA, ap, al) {
            var aC = aA.constructor == String ? aA : bM.getId(aA), aB = bM.getConnections({
                source: aC,
                scope: "*"
            }, true), an = bM.getConnections({target: aC, scope: "*"}, true);
            ap = "" + ap;
            if (!al) {
                aA = X.CurrentLibrary.getElementObject(aC);
                X.CurrentLibrary.setAttribute(aA, "id", ap)
            }
            aA = X.CurrentLibrary.getElementObject(ap);
            bA[ap] = bA[aC] || [];
            for (var am = 0, ak = bA[ap].length; am < ak; am++) {
                bA[ap][am].setElementId(ap);
                bA[ap][am].setReferenceElement(aA)
            }
            delete bA[aC];
            bM.anchorManager.changeId(aC, ap);
            if (!jsPlumbAdapter.headless) {
                bM.dragManager.changeId(aC, ap)
            }
            var ao = function (aF, aE, aG) {
                for (var aD = 0, aH = aF.length; aD < aH; aD++) {
                    aF[aD].endpoints[aE].setElementId(ap);
                    aF[aD].endpoints[aE].setReferenceElement(aA);
                    aF[aD][aG + "Id"] = ap;
                    aF[aD][aG] = aA
                }
            };
            ao(aB, 0, "source");
            ao(an, 1, "target");
            bM.repaint(ap)
        };
        this.setIdChanged = function (ak, al) {
            bM.setId(ak, al, true)
        };
        this.setDebugLog = function (ak) {
            p = ak
        };
        var bq = false, bn = null;
        this.setSuspendDrawing = function (ak, al) {
            bq = ak;
            if (ak) {
                bn = new Date().getTime()
            } else {
                bn = null
            }
            if (al) {
                bM.repaintEverything()
            }
        };
        this.isSuspendDrawing = function () {
            return bq
        };
        this.getSuspendedAt = function () {
            return bn
        };
        this.doWhileSuspended = function (al, am) {
            bM.setSuspendDrawing(true);
            try {
                al()
            } catch (ak) {
                V("Function run while suspended failed", ak)
            }
            bM.setSuspendDrawing(false, !am)
        };
        this.updateOffset = h;
        this.getOffset = function (ak) {
            return bc[ak]
        };
        this.getSize = function (ak) {
            return bf[ak]
        };
        this.getCachedData = bE;
        this.timestamp = T;
        this.SVG = "svg";
        this.CANVAS = "canvas";
        this.VML = "vml";
        this.setRenderMode = function (ak) {
            g = jsPlumbAdapter.setRenderMode(ak);
            return g
        };
        this.getRenderMode = function () {
            return g
        };
        this.show = function (al, ak) {
            bs(al, "block", ak);
            return bM
        };
        this.sizeCanvas = function (am, ao, ak, an, al) {
            if (am) {
                am.style.height = al + "px";
                am.height = al;
                am.style.width = an + "px";
                am.width = an;
                am.style.left = ao + "px";
                am.style.top = ak + "px"
            }
            return bM
        };
        this.getTestHarness = function () {
            return {
                endpointsByElement: bA,
                endpointCount: function (al) {
                    var ak = bA[al];
                    return ak ? ak.length : 0
                },
                connectionCount: function (al) {
                    al = al || m;
                    var ak = bv[al];
                    return ak ? ak.length : 0
                },
                getId: x,
                makeAnchor: self.makeAnchor,
                makeDynamicAnchor: self.makeDynamicAnchor
            }
        };
        this.toggleVisible = bC;
        this.toggleDraggable = a;
        this.wrap = az;
        this.addListener = this.bind;
        this.adjustForParentOffsetAndScroll = function (ak, an) {
            var am = null, ap = ak;
            if (an.tagName.toLowerCase() === "svg" && an.parentNode) {
                am = an.parentNode
            } else {
                if (an.offsetParent) {
                    am = an.offsetParent
                }
            }
            if (am != null) {
                var ao = am.tagName.toLowerCase() === "body" ? {
                    left: 0,
                    top: 0
                } : P(am, bM), al = am.tagName.toLowerCase() === "body" ? {
                    left: 0,
                    top: 0
                } : {left: am.scrollLeft, top: am.scrollTop};
                ap[0] = ak[0] - ao.left + al.left;
                ap[1] = ak[1] - ao.top + al.top
            }
            return ap
        };
        if (!jsPlumbAdapter.headless) {
            bM.dragManager = jsPlumbAdapter.getDragManager(bM);
            bM.recalculateOffsets = bM.dragManager.updateOffsets
        }
    };
    var X = new J();
    if (typeof window != "undefined") {
        window.jsPlumb = X
    }
    X.getInstance = function (a) {
        var b = new J(a);
        b.init();
        return b
    };
    if (typeof define === "function") {
        define("jsplumb", [], function () {
            return X
        });
        define("jsplumbinstance", [], function () {
            return X.getInstance()
        })
    }
    if (typeof exports !== "undefined") {
        exports.jsPlumb = X
    }
})();
(function () {
    jsPlumb.AnchorManager = function (a) {
        var V = {}, G = {}, D = {}, O = {}, J = {}, b = {
            HORIZONTAL: "horizontal",
            VERTICAL: "vertical",
            DIAGONAL: "diagonal",
            IDENTITY: "identity"
        }, T = {}, M = this, S = {}, K = a.jsPlumbInstance, U = jsPlumb.CurrentLibrary, N = {}, Q = function (g, f, m, q, l, e) {
            if (g === f) {
                return {orientation: b.IDENTITY, a: ["top", "top"]}
            }
            var r = Math.atan2((q.centery - m.centery), (q.centerx - m.centerx)), n = Math.atan2((m.centery - q.centery), (m.centerx - q.centerx)), o = ((m.left <= q.left && m.right >= q.left) || (m.left <= q.right && m.right >= q.right) || (m.left <= q.left && m.right >= q.right) || (q.left <= m.left && q.right >= m.right)), h = ((m.top <= q.top && m.bottom >= q.top) || (m.top <= q.bottom && m.bottom >= q.bottom) || (m.top <= q.top && m.bottom >= q.bottom) || (q.top <= m.top && q.bottom >= m.bottom)), k = function (s) {
                return [l.isContinuous ? l.verifyEdge(s[0]) : s[0], e.isContinuous ? e.verifyEdge(s[1]) : s[1]]
            }, p = {orientation: b.DIAGONAL, theta: r, theta2: n};
            if (!(o || h)) {
                if (q.left > m.left && q.top > m.top) {
                    p.a = ["right", "top"]
                } else {
                    if (q.left > m.left && m.top > q.top) {
                        p.a = ["top", "left"]
                    } else {
                        if (q.left < m.left && q.top < m.top) {
                            p.a = ["top", "right"]
                        } else {
                            if (q.left < m.left && q.top > m.top) {
                                p.a = ["left", "top"]
                            }
                        }
                    }
                }
            } else {
                if (o) {
                    p.orientation = b.HORIZONTAL;
                    p.a = m.top < q.top ? ["bottom", "top"] : ["top", "bottom"]
                } else {
                    p.orientation = b.VERTICAL;
                    p.a = m.left < q.left ? ["right", "left"] : ["left", "right"]
                }
            }
            p.a = k(p.a);
            return p
        }, I = function (h, n, p, o, g, m, w) {
            var f = [], x = n[g ? 0 : 1] / (o.length + 1);
            for (var l = 0; l < o.length; l++) {
                var e = (l + 1) * x, y = m * n[g ? 1 : 0];
                if (w) {
                    e = n[g ? 0 : 1] - e
                }
                var q = (g ? e : y), u = p[0] + q, r = q / n[0], s = (g ? y : e), v = p[1] + s, k = s / n[1];
                f.push([u, v, r, k, o[l][1], o[l][2]])
            }
            return f
        }, L = function (e) {
            return function (g, h) {
                var f = true;
                if (e) {
                    f = g[0][0] < h[0][0]
                } else {
                    f = g[0][0] > h[0][0]
                }
                return f === false ? -1 : 1
            }
        }, X = function (g, h) {
            var e = g[0][0] < 0 ? -Math.PI - g[0][0] : Math.PI - g[0][0], f = h[0][0] < 0 ? -Math.PI - h[0][0] : Math.PI - h[0][0];
            if (e > f) {
                return 1
            } else {
                return g[0][1] > h[0][1] ? 1 : -1
            }
        }, F = {
            top: function (e, f) {
                return e[0] > f[0] ? 1 : -1
            }, right: L(true), bottom: L(true), left: X
        }, H = function (f, e) {
            return f.sort(e)
        }, R = function (k, l) {
            var e = K.getCachedData(k), g = e.s, f = e.o, h = function (v, n, A, w, p, q, B) {
                if (w.length > 0) {
                    var r = H(w, F[v]), u = v === "right" || v === "top", C = I(v, n, A, r, p, q, u);
                    var m = function (ac, ab) {
                        var ad = K.adjustForParentOffsetAndScroll([ab[0], ab[1]], ac.canvas);
                        D[ac.id] = [ad[0], ad[1], ab[2], ab[3]];
                        J[ac.id] = B
                    };
                    for (var y = 0; y < C.length; y++) {
                        var s = C[y][4], o = s.endpoints[0].elementId === k, x = s.endpoints[1].elementId === k;
                        if (o) {
                            m(s.endpoints[0], C[y])
                        } else {
                            if (x) {
                                m(s.endpoints[1], C[y])
                            }
                        }
                    }
                }
            };
            h("bottom", g, [f.left, f.top], l.bottom, true, 1, [0, 1]);
            h("top", g, [f.left, f.top], l.top, true, 0, [0, -1]);
            h("left", g, [f.left, f.top], l.left, false, 0, [-1, 0]);
            h("right", g, [f.left, f.top], l.right, false, 1, [1, 0])
        };
        this.reset = function () {
            V = {};
            T = {};
            S = {}
        };
        this.addFloatingConnection = function (f, e) {
            N[f] = e
        };
        this.removeFloatingConnection = function (e) {
            delete N[e]
        };
        this.newConnection = function (g) {
            var e = g.sourceId, h = g.targetId, l = g.endpoints, f = true, k = function (o, n, q, m, p) {
                if ((e == h) && q.isContinuous) {
                    U.removeElement(l[1].canvas);
                    f = false
                }
                jsPlumbUtil.addToList(T, m, [p, n, q.constructor == jsPlumb.DynamicAnchor])
            };
            k(0, l[0], l[0].anchor, h, g);
            if (f) {
                k(1, l[1], l[1].anchor, e, g)
            }
        };
        var W = function (e) {
            (function (f, h) {
                if (f) {
                    var g = function (k) {
                        return k[4] == h
                    };
                    jsPlumbUtil.removeWithFunction(f.top, g);
                    jsPlumbUtil.removeWithFunction(f.left, g);
                    jsPlumbUtil.removeWithFunction(f.bottom, g);
                    jsPlumbUtil.removeWithFunction(f.right, g)
                }
            })(S[e.elementId], e.id)
        };
        this.connectionDetached = function (e) {
            var k = e.connection || e, f = e.sourceId, h = e.targetId, l = k.endpoints, g = function (o, n, q, m, p) {
                if (q.constructor == jsPlumb.FloatingAnchor) {
                } else {
                    jsPlumbUtil.removeWithFunction(T[m], function (r) {
                        return r[0].id == p.id
                    })
                }
            };
            g(1, l[1], l[1].anchor, f, k);
            g(0, l[0], l[0].anchor, h, k);
            W(k.endpoints[0]);
            W(k.endpoints[1]);
            M.redraw(k.sourceId);
            M.redraw(k.targetId)
        };
        this.add = function (e, f) {
            jsPlumbUtil.addToList(V, f, e)
        };
        this.changeId = function (e, f) {
            T[f] = T[e];
            V[f] = V[e];
            delete T[e];
            delete V[e]
        };
        this.getConnectionsFor = function (e) {
            return T[e] || []
        };
        this.getEndpointsFor = function (e) {
            return V[e] || []
        };
        this.deleteEndpoint = function (e) {
            jsPlumbUtil.removeWithFunction(V[e.elementId], function (f) {
                return f.id == e.id
            });
            W(e)
        };
        this.clearFor = function (e) {
            delete V[e];
            V[e] = []
        };
        var P = function (g, w, n, A, s, r, p, u, e, q, B, h) {
            var l = -1, C = -1, y = A.endpoints[p], o = y.id, v = [1, 0][p], ab = [[w, n], A, s, r, o], aa = g[e], f = y._continuousAnchorEdge ? g[y._continuousAnchorEdge] : null;
            if (f) {
                var k = jsPlumbUtil.findWithFunction(f, function (Y) {
                    return Y[4] == o
                });
                if (k != -1) {
                    f.splice(k, 1);
                    for (var m = 0; m < f.length; m++) {
                        jsPlumbUtil.addWithFunction(B, f[m][1], function (Y) {
                            return Y.id == f[m][1].id
                        });
                        jsPlumbUtil.addWithFunction(h, f[m][1].endpoints[p], function (Y) {
                            return Y.id == f[m][1].endpoints[p].id
                        });
                        jsPlumbUtil.addWithFunction(h, f[m][1].endpoints[v], function (Y) {
                            return Y.id == f[m][1].endpoints[v].id
                        })
                    }
                }
            }
            for (var m = 0; m < aa.length; m++) {
                if (a.idx == 1 && aa[m][3] === r && C == -1) {
                    C = m
                }
                jsPlumbUtil.addWithFunction(B, aa[m][1], function (Y) {
                    return Y.id == aa[m][1].id
                });
                jsPlumbUtil.addWithFunction(h, aa[m][1].endpoints[p], function (Y) {
                    return Y.id == aa[m][1].endpoints[p].id
                });
                jsPlumbUtil.addWithFunction(h, aa[m][1].endpoints[v], function (Y) {
                    return Y.id == aa[m][1].endpoints[v].id
                })
            }
            if (l != -1) {
                aa[l] = ab
            } else {
                var x = u ? C != -1 ? C : 0 : aa.length;
                aa.splice(x, 0, ab)
            }
            y._continuousAnchorEdge = e
        };
        this.redraw = function (o, l, C, y, e) {
            if (!K.isSuspendDrawing()) {
                var ah = V[o] || [], ai = T[o] || [], af = [], aj = [], B = [];
                C = C || K.timestamp();
                y = y || {left: 0, top: 0};
                if (l) {
                    l = {left: l.left + y.left, top: l.top + y.top}
                }
                var u = K.updateOffset({
                    elId: o,
                    offset: l,
                    recalc: false,
                    timestamp: C
                }), r = {};
                for (var al = 0; al < ai.length; al++) {
                    var x = ai[al][0], v = x.sourceId, A = x.targetId, w = x.endpoints[0].anchor.isContinuous, p = x.endpoints[1].anchor.isContinuous;
                    if (w || p) {
                        var ak = v + "_" + A, g = A + "_" + v, h = r[ak], q = x.sourceId == o ? 1 : 0;
                        if (w && !S[v]) {
                            S[v] = {top: [], right: [], bottom: [], left: []}
                        }
                        if (p && !S[A]) {
                            S[A] = {top: [], right: [], bottom: [], left: []}
                        }
                        if (o != A) {
                            K.updateOffset({elId: A, timestamp: C})
                        }
                        if (o != v) {
                            K.updateOffset({elId: v, timestamp: C})
                        }
                        var s = K.getCachedData(A), ag = K.getCachedData(v);
                        if (A == v && (w || p)) {
                            P(S[v], -Math.PI / 2, 0, x, false, A, 0, false, "top", v, af, aj)
                        } else {
                            if (!h) {
                                h = Q(v, A, ag.o, s.o, x.endpoints[0].anchor, x.endpoints[1].anchor);
                                r[ak] = h
                            }
                            if (w) {
                                P(S[v], h.theta, 0, x, false, A, 0, false, h.a[0], v, af, aj)
                            }
                            if (p) {
                                P(S[A], h.theta2, -1, x, true, v, 1, true, h.a[1], A, af, aj)
                            }
                        }
                        if (w) {
                            jsPlumbUtil.addWithFunction(B, v, function (Y) {
                                return Y === v
                            })
                        }
                        if (p) {
                            jsPlumbUtil.addWithFunction(B, A, function (Y) {
                                return Y === A
                            })
                        }
                        jsPlumbUtil.addWithFunction(af, x, function (Y) {
                            return Y.id == x.id
                        });
                        if ((w && q == 0) || (p && q == 1)) {
                            jsPlumbUtil.addWithFunction(aj, x.endpoints[q], function (Y) {
                                return Y.id == x.endpoints[q].id
                            })
                        }
                    }
                }
                for (var al = 0; al < ah.length; al++) {
                    if (ah[al].connections.length == 0 && ah[al].anchor.isContinuous) {
                        if (!S[o]) {
                            S[o] = {top: [], right: [], bottom: [], left: []}
                        }
                        P(S[o], -Math.PI / 2, 0, {
                            endpoints: [ah[al], ah[al]],
                            paint: function () {
                            }
                        }, false, o, 0, false, "top", o, af, aj);
                        jsPlumbUtil.addWithFunction(B, o, function (Y) {
                            return Y === o
                        })
                    }
                }
                for (var al = 0; al < B.length; al++) {
                    R(B[al], S[B[al]])
                }
                for (var al = 0; al < ah.length; al++) {
                    ah[al].paint({timestamp: C, offset: u, dimensions: u.s})
                }
                for (var al = 0; al < aj.length; al++) {
                    var m = K.getCachedData(aj[al].elementId);
                    aj[al].paint({timestamp: C, offset: m, dimensions: m.s})
                }
                for (var al = 0; al < ai.length; al++) {
                    var n = ai[al][1];
                    if (n.anchor.constructor == jsPlumb.DynamicAnchor) {
                        n.paint({elementWithPrecedence: o});
                        jsPlumbUtil.addWithFunction(af, ai[al][0], function (Y) {
                            return Y.id == ai[al][0].id
                        });
                        for (var f = 0; f < n.connections.length; f++) {
                            if (n.connections[f] !== ai[al][0]) {
                                jsPlumbUtil.addWithFunction(af, n.connections[f], function (Y) {
                                    return Y.id == n.connections[f].id
                                })
                            }
                        }
                    } else {
                        if (n.anchor.constructor == jsPlumb.Anchor) {
                            jsPlumbUtil.addWithFunction(af, ai[al][0], function (Y) {
                                return Y.id == ai[al][0].id
                            })
                        }
                    }
                }
                var k = N[o];
                if (k) {
                    k.paint({timestamp: C, recalc: false, elId: o})
                }
                for (var al = 0; al < af.length; al++) {
                    af[al].paint({
                        elId: o,
                        timestamp: C,
                        recalc: false,
                        clearEdits: e
                    })
                }
            }
        };
        this.rehomeEndpoint = function (k, e) {
            var h = V[k] || [], g = K.getId(e);
            if (g !== k) {
                for (var f = 0; f < h.length; f++) {
                    M.add(h[f], g)
                }
                h.splice(0, h.length)
            }
        };
        var E = function (p) {
            jsPlumbUtil.EventGenerator.apply(this);
            this.type = "Continuous";
            this.isDynamic = true;
            this.isContinuous = true;
            var m = p.faces || ["top", "right", "bottom", "left"], q = !(p.clockwise === false), f = {}, h = {
                top: "bottom",
                right: "left",
                left: "right",
                bottom: "top"
            }, n = {
                top: "right",
                right: "bottom",
                left: "top",
                bottom: "left"
            }, l = {
                top: "left",
                right: "top",
                left: "bottom",
                bottom: "right"
            }, o = q ? n : l, e = q ? l : n, g = p.cssClass || "";
            for (var k = 0; k < m.length; k++) {
                f[m[k]] = true
            }
            this.verifyEdge = function (r) {
                if (f[r]) {
                    return r
                } else {
                    if (f[h[r]]) {
                        return h[r]
                    } else {
                        if (f[o[r]]) {
                            return o[r]
                        } else {
                            if (f[e[r]]) {
                                return e[r]
                            }
                        }
                    }
                }
                return r
            };
            this.compute = function (r) {
                return O[r.element.id] || D[r.element.id] || [0, 0]
            };
            this.getCurrentLocation = function (r) {
                return O[r.id] || D[r.id] || [0, 0]
            };
            this.getOrientation = function (r) {
                return J[r.id] || [0, 0]
            };
            this.clearUserDefinedLocation = function () {
                delete O[p.elementId]
            };
            this.setUserDefinedLocation = function (r) {
                O[p.elementId] = r
            };
            this.getCssClass = function () {
                return g
            };
            this.setCssClass = function (r) {
                g = r
            }
        };
        K.continuousAnchorFactory = {
            get: function (e) {
                var f = G[e.elementId];
                if (!f) {
                    f = new E(e);
                    G[e.elementId] = f
                }
                return f
            }
        }
    };
    jsPlumb.Anchor = function (a) {
        var m = this;
        this.x = a.x || 0;
        this.y = a.y || 0;
        this.elementId = a.elementId;
        jsPlumbUtil.EventGenerator.apply(this);
        var n = a.orientation || [0, 0], o = a.jsPlumbInstance, b = null, p = null, q = null, r = a.cssClass || "";
        this.getCssClass = function () {
            return r
        };
        this.offsets = a.offsets || [0, 0];
        m.timestamp = null;
        this.compute = function (f) {
            var g = f.xy, e = f.wh, k = f.element, h = f.timestamp;
            if (f.clearUserDefinedLocation) {
                q = null
            }
            if (h && h === m.timestamp) {
                return p
            }
            if (q != null) {
                p = q
            } else {
                p = [g[0] + (m.x * e[0]) + m.offsets[0], g[1] + (m.y * e[1]) + m.offsets[1]];
                p = o.adjustForParentOffsetAndScroll(p, k.canvas)
            }
            m.timestamp = h;
            return p
        };
        this.getOrientation = function (e) {
            return n
        };
        this.equals = function (e) {
            if (!e) {
                return false
            }
            var g = e.getOrientation();
            var f = this.getOrientation();
            return this.x == e.x && this.y == e.y && this.offsets[0] == e.offsets[0] && this.offsets[1] == e.offsets[1] && f[0] == g[0] && f[1] == g[1]
        };
        this.getCurrentLocation = function () {
            return p
        };
        this.getUserDefinedLocation = function () {
            return q
        };
        this.setUserDefinedLocation = function (e) {
            q = e
        };
        this.clearUserDefinedLocation = function () {
            q = null
        }
    };
    jsPlumb.FloatingAnchor = function (u) {
        jsPlumb.Anchor.apply(this, arguments);
        var v = u.reference, s = jsPlumb.CurrentLibrary, q = u.jsPlumbInstance, p = u.referenceCanvas, b = s.getSize(s.getElementObject(p)), a = 0, r = 0, w = null, o = null;
        this.x = 0;
        this.y = 0;
        this.isFloating = true;
        this.compute = function (e) {
            var f = e.xy, g = e.element, h = [f[0] + (b[0] / 2), f[1] + (b[1] / 2)];
            h = q.adjustForParentOffsetAndScroll(h, g.canvas);
            o = h;
            return h
        };
        this.getOrientation = function (e) {
            if (w) {
                return w
            } else {
                var f = v.getOrientation(e);
                return [Math.abs(f[0]) * a * -1, Math.abs(f[1]) * r * -1]
            }
        };
        this.over = function (e) {
            w = e.getOrientation()
        };
        this.out = function () {
            w = null
        };
        this.getCurrentLocation = function () {
            return o
        }
    };
    jsPlumb.DynamicAnchor = function (r) {
        jsPlumb.Anchor.apply(this, arguments);
        this.isSelective = true;
        this.isDynamic = true;
        var a = [], b = this, o = function (e) {
            return e.constructor == jsPlumb.Anchor ? e : r.jsPlumbInstance.makeAnchor(e, r.elementId, r.jsPlumbInstance)
        };
        for (var p = 0; p < r.anchors.length; p++) {
            a[p] = o(r.anchors[p])
        }
        this.addAnchor = function (e) {
            a.push(o(e))
        };
        this.getAnchors = function () {
            return a
        };
        this.locked = false;
        var v = a.length > 0 ? a[0] : null, s = a.length > 0 ? 0 : -1, q = v, b = this, u = function (m, e, f, l, g) {
            var h = l[0] + (m.x * g[0]), k = l[1] + (m.y * g[1]), n = l[0] + (g[0] / 2), y = l[1] + (g[1] / 2);
            return (Math.sqrt(Math.pow(e - h, 2) + Math.pow(f - k, 2)) + Math.sqrt(Math.pow(n - h, 2) + Math.pow(y - k, 2)))
        }, w = r.selector || function (k, g, f, e, h) {
            var C = f[0] + (e[0] / 2), D = f[1] + (e[1] / 2);
            var n = -1, l = Infinity;
            for (var B = 0; B < h.length; B++) {
                var m = u(h[B], C, D, k, g);
                if (m < l) {
                    n = B + 0;
                    l = m
                }
            }
            return h[n]
        };
        this.compute = function (f) {
            var g = f.xy, m = f.wh, k = f.timestamp, l = f.txy, e = f.twh;
            if (f.clearUserDefinedLocation) {
                userDefinedLocation = null
            }
            var h = b.getUserDefinedLocation();
            if (h != null) {
                return h
            }
            if (b.locked || l == null || e == null) {
                return v.compute(f)
            } else {
                f.timestamp = null
            }
            v = w(g, m, l, e, a);
            b.x = v.x;
            b.y = v.y;
            if (v != q) {
                b.fire("anchorChanged", v)
            }
            q = v;
            return v.compute(f)
        };
        this.getCurrentLocation = function () {
            return b.getUserDefinedLocation() || (v != null ? v.getCurrentLocation() : null)
        };
        this.getOrientation = function (e) {
            return v != null ? v.getOrientation(e) : [0, 0]
        };
        this.over = function (e) {
            if (v != null) {
                v.over(e)
            }
        };
        this.out = function () {
            if (v != null) {
                v.out()
            }
        };
        this.getCssClass = function () {
            return (v && v.getCssClass()) || ""
        }
    };
    var c = function (n, a, l, m, b, k) {
        jsPlumb.Anchors[b] = function (e) {
            var f = e.jsPlumbInstance.makeAnchor([n, a, l, m, 0, 0], e.elementId, e.jsPlumbInstance);
            f.type = b;
            if (k) {
                k(f, e)
            }
            return f
        }
    };
    c(0.5, 0, 0, -1, "TopCenter");
    c(0.5, 1, 0, 1, "BottomCenter");
    c(0, 0.5, -1, 0, "LeftMiddle");
    c(1, 0.5, 1, 0, "RightMiddle");
    c(0.5, 0, 0, -1, "Top");
    c(0.5, 1, 0, 1, "Bottom");
    c(0, 0.5, -1, 0, "Left");
    c(1, 0.5, 1, 0, "Right");
    c(0.5, 0.5, 0, 0, "Center");
    c(1, 0, 0, -1, "TopRight");
    c(1, 1, 0, 1, "BottomRight");
    c(0, 0, 0, -1, "TopLeft");
    c(0, 1, 0, 1, "BottomLeft");
    jsPlumb.Defaults.DynamicAnchors = function (a) {
        return a.jsPlumbInstance.makeAnchors(["TopCenter", "RightMiddle", "BottomCenter", "LeftMiddle"], a.elementId, a.jsPlumbInstance)
    };
    jsPlumb.Anchors.AutoDefault = function (a) {
        var b = a.jsPlumbInstance.makeDynamicAnchor(jsPlumb.Defaults.DynamicAnchors(a));
        b.type = "AutoDefault";
        return b
    };
    var d = function (a, b) {
        jsPlumb.Anchors[a] = function (g) {
            var h = g.jsPlumbInstance.makeAnchor(["Continuous", {faces: b}], g.elementId, g.jsPlumbInstance);
            h.type = a;
            return h
        }
    };
    jsPlumb.Anchors.Continuous = function (a) {
        return a.jsPlumbInstance.continuousAnchorFactory.get(a)
    };
    d("ContinuousLeft", ["left"]);
    d("ContinuousTop", ["top"]);
    d("ContinuousBottom", ["bottom"]);
    d("ContinuousRight", ["right"]);
    jsPlumb.Anchors.Assign = c(0, 0, 0, 0, "Assign", function (b, a) {
        var f = a.position || "Fixed";
        b.positionFinder = f.constructor == String ? a.jsPlumbInstance.AnchorPositionFinders[f] : f;
        b.constructorParams = a
    });
    jsPlumb.AnchorPositionFinders = {
        Fixed: function (a, h, b, g) {
            return [(a.left - h.left) / b[0], (a.top - h.top) / b[1]]
        }, Grid: function (w, a, r, v) {
            var b = w.left - a.left, o = w.top - a.top, p = r[0] / (v.grid[0]), q = r[1] / (v.grid[1]), s = Math.floor(b / p), u = Math.floor(o / q);
            return [((s * p) + (p / 2)) / r[0], ((u * q) + (q / 2)) / r[1]]
        }
    };
    jsPlumb.Anchors.Perimeter = function (y) {
        y = y || {};
        var x = y.anchorCount || 60, u = y.shape;
        if (!u) {
            throw new Error("no shape supplied to Perimeter Anchor type")
        }
        var w = function () {
            var g = 0.5, h = Math.PI * 2 / x, f = 0, l = [];
            for (var k = 0; k < x; k++) {
                var m = g + (g * Math.sin(f)), e = g + (g * Math.cos(f));
                l.push([m, e, 0, 0]);
                f += h
            }
            return l
        }, s = function (g) {
            var e = x / g.length, k = [], h = function (H, F, m, G, E) {
                e = x * E;
                var n = (m - H) / e, o = (G - F) / e;
                for (var l = 0; l < e; l++) {
                    k.push([H + (n * l), F + (o * l), 0, 0])
                }
            };
            for (var f = 0; f < g.length; f++) {
                h.apply(null, g[f])
            }
            return k
        }, p = function (g) {
            var e = [];
            for (var f = 0; f < g.length; f++) {
                e.push([g[f][0], g[f][1], g[f][2], g[f][3], 1 / g.length])
            }
            return s(e)
        }, r = function () {
            return p([[0, 0, 1, 0], [1, 0, 1, 1], [1, 1, 0, 1], [0, 1, 0, 0]])
        };
        var v = {
            Circle: w, Ellipse: w, Diamond: function () {
                return p([[0.5, 0, 1, 0.5], [1, 0.5, 0.5, 1], [0.5, 1, 0, 0.5], [0, 0.5, 0.5, 0]])
            }, Rectangle: r, Square: r, Triangle: function () {
                return p([[0.5, 0, 1, 1], [1, 1, 0, 1], [0, 1, 0.5, 0]])
            }, Path: function (e) {
                var g = e.points, f = [], k = 0;
                for (var h = 0; h < g.length - 1; h++) {
                    var l = Math.sqrt(Math.pow(g[h][2] - g[h][0]) + Math.pow(g[h][3] - g[h][1]));
                    k += l;
                    f.push([g[h][0], g[h][1], g[h + 1][0], g[h + 1][1], l])
                }
                for (var h = 0; h < f.length; h++) {
                    f[h][4] = f[h][4] / k
                }
                return s(f)
            }
        }, b = function (f, g) {
            var e = [], h = g / 180 * Math.PI;
            for (var k = 0; k < f.length; k++) {
                var l = f[k][0] - 0.5, m = f[k][1] - 0.5;
                e.push([0.5 + ((l * Math.cos(h)) - (m * Math.sin(h))), 0.5 + ((l * Math.sin(h)) + (m * Math.cos(h))), f[k][2], f[k][3]])
            }
            return e
        };
        if (!v[u]) {
            throw new Error("Shape [" + u + "] is unknown by Perimeter Anchor type")
        }
        var a = v[u](y);
        if (y.rotation) {
            a = b(a, y.rotation)
        }
        var q = y.jsPlumbInstance.makeDynamicAnchor(a);
        q.type = "Perimeter";
        return q
    }
})();
(function () {
    var h = function (a, c) {
        var b = false;
        return {
            drag: function () {
                if (b) {
                    b = false;
                    return true
                }
                var d = jsPlumb.CurrentLibrary.getUIPosition(arguments, c.getZoom());
                if (a.element) {
                    jsPlumb.CurrentLibrary.setOffset(a.element, d);
                    c.repaint(a.element, d)
                }
            }, stopDrag: function () {
                b = true
            }
        }
    };
    var f = function (c, m, n) {
        var a = document.createElement("div");
        a.style.position = "absolute";
        var d = jsPlumb.CurrentLibrary.getElementObject(a);
        jsPlumb.CurrentLibrary.appendElement(a, m);
        var b = n.getId(d);
        n.updateOffset({elId: b});
        c.id = b;
        c.element = d
    };
    var g = function (a, b, r, c, o, p, q) {
        var d = new jsPlumb.FloatingAnchor({
            reference: b,
            referenceCanvas: c,
            jsPlumbInstance: p
        });
        return q({
            paintStyle: a,
            endpoint: r,
            anchor: d,
            source: o,
            scope: "__floating"
        })
    };
    var e = ["connectorStyle", "connectorHoverStyle", "connectorOverlays", "connector", "connectionType", "connectorClass", "connectorHoverClass"];
    jsPlumb.Endpoint = function (aR) {
        var aJ = this, aH = aR._jsPlumb, aQ = jsPlumb.CurrentLibrary, ap = aQ.getAttribute, aB = aQ.getElementObject, aK = jsPlumbUtil, aS = aQ.getOffset, aC = aR.newConnection, ah = aR.newEndpoint, b = aR.finaliseConnection, a = aR.fireDetachEvent, aw = aR.floatingConnections;
        aJ.idPrefix = "_jsplumb_e_";
        aJ.defaultLabelLocation = [0.5, 0.5];
        aJ.defaultOverlayKeys = ["Overlays", "EndpointOverlays"];
        this.parent = aR.parent;
        overlayCapableJsPlumbUIComponent.apply(this, arguments);
        aR = aR || {};
        this.getTypeDescriptor = function () {
            return "endpoint"
        };
        this.getDefaultType = function () {
            return {
                parameters: {},
                scope: null,
                maxConnections: aJ._jsPlumb.Defaults.MaxConnections,
                paintStyle: aJ._jsPlumb.Defaults.EndpointStyle || jsPlumb.Defaults.EndpointStyle,
                endpoint: aJ._jsPlumb.Defaults.Endpoint || jsPlumb.Defaults.Endpoint,
                hoverPaintStyle: aJ._jsPlumb.Defaults.EndpointHoverStyle || jsPlumb.Defaults.EndpointHoverStyle,
                overlays: aJ._jsPlumb.Defaults.EndpointOverlays || jsPlumb.Defaults.EndpointOverlays,
                connectorStyle: aR.connectorStyle,
                connectorHoverStyle: aR.connectorHoverStyle,
                connectorClass: aR.connectorClass,
                connectorHoverClass: aR.connectorHoverClass,
                connectorOverlays: aR.connectorOverlays,
                connector: aR.connector,
                connectorTooltip: aR.connectorTooltip
            }
        };
        var ag = this.applyType;
        this.applyType = function (l, k) {
            ag(l, k);
            if (l.maxConnections != null) {
                al = l.maxConnections
            }
            if (l.scope) {
                aJ.scope = l.scope
            }
            aK.copyValues(e, l, aJ)
        };
        var az = true, aP = !(aR.enabled === false);
        this.isVisible = function () {
            return az
        };
        this.setVisible = function (n, k, o) {
            az = n;
            if (aJ.canvas) {
                aJ.canvas.style.display = n ? "block" : "none"
            }
            aJ[n ? "showOverlays" : "hideOverlays"]();
            if (!k) {
                for (var l = 0; l < aJ.connections.length; l++) {
                    aJ.connections[l].setVisible(n);
                    if (!o) {
                        var m = aJ === aJ.connections[l].endpoints[0] ? 1 : 0;
                        if (aJ.connections[l].endpoints[m].connections.length == 1) {
                            aJ.connections[l].endpoints[m].setVisible(n, true, true)
                        }
                    }
                }
            }
        };
        this.isEnabled = function () {
            return aP
        };
        this.setEnabled = function (k) {
            aP = k
        };
        var ao = aR.source, aM = aR.uuid, aT = null, an = null;
        if (aM) {
            aR.endpointsByUUID[aM] = aJ
        }
        var c = ap(ao, "id");
        this.elementId = c;
        this.element = ao;
        aJ.setElementId = function (k) {
            c = k;
            aJ.elementId = k;
            aJ.anchor.elementId = k
        };
        aJ.setReferenceElement = function (k) {
            ao = k;
            aJ.element = k
        };
        var aU = aR.connectionCost;
        this.getConnectionCost = function () {
            return aU
        };
        this.setConnectionCost = function (k) {
            aU = k
        };
        var am = aR.connectionsDirected;
        this.areConnectionsDirected = function () {
            return am
        };
        this.setConnectionsDirected = function (k) {
            am = k
        };
        var at = "", ak = function () {
            aQ.removeClass(ao, aH.endpointAnchorClassPrefix + "_" + at);
            aJ.removeClass(aH.endpointAnchorClassPrefix + "_" + at);
            at = aJ.anchor.getCssClass();
            aJ.addClass(aH.endpointAnchorClassPrefix + "_" + at);
            aQ.addClass(ao, aH.endpointAnchorClassPrefix + "_" + at)
        };
        this.setAnchor = function (l, k) {
            aJ.anchor = aH.makeAnchor(l, c, aH);
            ak();
            aJ.anchor.bind("anchorChanged", function (m) {
                aJ.fire("anchorChanged", {endpoint: aJ, anchor: m});
                ak()
            });
            if (!k) {
                aH.repaint(c)
            }
        };
        this.cleanup = function () {
            aQ.removeClass(ao, aH.endpointAnchorClassPrefix + "_" + at)
        };
        var ai = aR.anchor ? aR.anchor : aR.anchors ? aR.anchors : (aH.Defaults.Anchor || "Top");
        aJ.setAnchor(ai, true);
        if (!aR._transient) {
            aH.anchorManager.add(aJ, c)
        }
        var d = null, af = null;
        this.setEndpoint = function (m) {
            var n = function (p, q) {
                var o = aH.getRenderMode();
                if (jsPlumb.Endpoints[o][p]) {
                    return new jsPlumb.Endpoints[o][p](q)
                }
                if (!aH.Defaults.DoNotThrowErrors) {
                    throw {msg: "jsPlumb: unknown endpoint type '" + p + "'"}
                }
            };
            var l = {
                _jsPlumb: aJ._jsPlumb,
                cssClass: aR.cssClass,
                parent: aR.parent,
                container: aR.container,
                tooltip: aR.tooltip,
                connectorTooltip: aR.connectorTooltip,
                endpoint: aJ
            };
            if (aK.isString(m)) {
                d = n(m, l)
            } else {
                if (aK.isArray(m)) {
                    l = aK.merge(m[1], l);
                    d = n(m[0], l)
                } else {
                    d = m.clone()
                }
            }
            var k = jsPlumb.extend({}, l);
            d.clone = function () {
                var o = new Object();
                d.constructor.apply(o, [k]);
                return o
            };
            aJ.endpoint = d;
            aJ.type = aJ.endpoint.type
        };
        this.setEndpoint(aR.endpoint || aH.Defaults.Endpoint || jsPlumb.Defaults.Endpoint || "Dot");
        af = d;
        var aF = aJ.setHover;
        aJ.setHover = function () {
            aJ.endpoint.setHover.apply(aJ.endpoint, arguments);
            aF.apply(aJ, arguments)
        };
        var aq = function (k) {
            if (aJ.connections.length > 0) {
                aJ.connections[0].setHover(k, false)
            } else {
                aJ.setHover(k)
            }
        };
        aJ.bindListeners(aJ.endpoint, aJ, aq);
        this.setPaintStyle(aR.paintStyle || aR.style || aH.Defaults.EndpointStyle || jsPlumb.Defaults.EndpointStyle, true);
        this.setHoverPaintStyle(aR.hoverPaintStyle || aH.Defaults.EndpointHoverStyle || jsPlumb.Defaults.EndpointHoverStyle, true);
        this.paintStyleInUse = this.getPaintStyle();
        var aE = this.getPaintStyle();
        aK.copyValues(e, aR, this);
        this.isSource = aR.isSource || false;
        this.isTarget = aR.isTarget || false;
        var al = aR.maxConnections || aH.Defaults.MaxConnections;
        this.getAttachedElements = function () {
            return aJ.connections
        };
        this.canvas = this.endpoint.canvas;
        aJ.addClass(aH.endpointAnchorClassPrefix + "_" + at);
        aQ.addClass(ao, aH.endpointAnchorClassPrefix + "_" + at);
        this.connections = aR.connections || [];
        this.connectorPointerEvents = aR["connector-pointer-events"];
        this.scope = aR.scope || aH.getDefaultScope();
        this.timestamp = null;
        aJ.reattachConnections = aR.reattach || aH.Defaults.ReattachConnections;
        aJ.connectionsDetachable = aH.Defaults.ConnectionsDetachable;
        if (aR.connectionsDetachable === false || aR.detachable === false) {
            aJ.connectionsDetachable = false
        }
        var au = aR.dragAllowedWhenFull || true;
        if (aR.onMaxConnections) {
            aJ.bind("maxConnections", aR.onMaxConnections)
        }
        this.computeAnchor = function (k) {
            return aJ.anchor.compute(k)
        };
        this.addConnection = function (k) {
            aJ.connections.push(k);
            aJ[(aJ.connections.length > 0 ? "add" : "remove") + "Class"](aH.endpointConnectedClass);
            aJ[(aJ.isFull() ? "add" : "remove") + "Class"](aH.endpointFullClass)
        };
        this.detach = function (s, o, r, l, u) {
            var m = aK.findWithFunction(aJ.connections, function (v) {
                return v.id == s.id
            }), n = false;
            l = (l !== false);
            if (m >= 0) {
                if (r || s._forceDetach || s.isDetachable() || s.isDetachAllowed(s)) {
                    var k = s.endpoints[0] == aJ ? s.endpoints[1] : s.endpoints[0];
                    if (r || s._forceDetach || (aJ.isDetachAllowed(s))) {
                        aJ.connections.splice(m, 1);
                        if (!o) {
                            k.detach(s, true, r);
                            if (s.endpointsToDeleteOnDetach) {
                                for (var p = 0; p < s.endpointsToDeleteOnDetach.length; p++) {
                                    var q = s.endpointsToDeleteOnDetach[p];
                                    if (q && q.connections.length == 0) {
                                        aH.deleteEndpoint(q)
                                    }
                                }
                            }
                        }
                        if (s.getConnector() != null) {
                            aK.removeElements(s.getConnector().getDisplayElements(), s.parent)
                        }
                        aK.removeWithFunction(aR.connectionsByScope[s.scope], function (v) {
                            return v.id == s.id
                        });
                        aJ[(aJ.connections.length > 0 ? "add" : "remove") + "Class"](aH.endpointConnectedClass);
                        aJ[(aJ.isFull() ? "add" : "remove") + "Class"](aH.endpointFullClass);
                        n = true;
                        a(s, (!o && l), u)
                    }
                }
            }
            return n
        };
        this.detachAll = function (k, l) {
            while (aJ.connections.length > 0) {
                aJ.detach(aJ.connections[0], false, true, k, l)
            }
            return aJ
        };
        this.detachFrom = function (l, m, o) {
            var k = [];
            for (var n = 0; n < aJ.connections.length; n++) {
                if (aJ.connections[n].endpoints[1] == l || aJ.connections[n].endpoints[0] == l) {
                    k.push(aJ.connections[n])
                }
            }
            for (var n = 0; n < k.length; n++) {
                if (aJ.detach(k[n], false, true, m, o)) {
                    k[n].setHover(false, false)
                }
            }
            return aJ
        };
        this.detachFromConnection = function (k) {
            var l = aK.findWithFunction(aJ.connections, function (m) {
                return m.id == k.id
            });
            if (l >= 0) {
                aJ.connections.splice(l, 1);
                aJ[(aJ.connections.length > 0 ? "add" : "remove") + "Class"](aH.endpointConnectedClass);
                aJ[(aJ.isFull() ? "add" : "remove") + "Class"](aH.endpointFullClass)
            }
        };
        this.getElement = function () {
            return ao
        };
        this.setElement = function (m, p) {
            var k = aH.getId(m);
            aK.removeWithFunction(aR.endpointsByElement[aJ.elementId], function (q) {
                return q.id == aJ.id
            });
            ao = aB(m);
            c = aH.getId(ao);
            aJ.elementId = c;
            var l = aR.getParentFromParams({
                source: k,
                container: p
            }), n = aQ.getParent(aJ.canvas);
            aQ.removeElement(aJ.canvas, n);
            aQ.appendElement(aJ.canvas, l);
            for (var o = 0; o < aJ.connections.length; o++) {
                aJ.connections[o].moveParent(l);
                aJ.connections[o].sourceId = c;
                aJ.connections[o].source = ao
            }
            aK.addToList(aR.endpointsByElement, k, aJ)
        };
        this.getUuid = function () {
            return aM
        };
        aJ.makeInPlaceCopy = function () {
            var k = aJ.anchor.getCurrentLocation(aJ), l = aJ.anchor.getOrientation(aJ), m = aJ.anchor.getCssClass(), n = {
                bind: function () {
                }, compute: function () {
                    return [k[0], k[1]]
                }, getCurrentLocation: function () {
                    return [k[0], k[1]]
                }, getOrientation: function () {
                    return l
                }, getCssClass: function () {
                    return m
                }
            };
            return ah({
                anchor: n,
                source: ao,
                paintStyle: this.getPaintStyle(),
                endpoint: aR.hideOnDrag ? "Blank" : d,
                _transient: true,
                scope: aJ.scope
            })
        };
        this.isConnectedTo = function (k) {
            var l = false;
            if (k) {
                for (var m = 0; m < aJ.connections.length; m++) {
                    if (aJ.connections[m].endpoints[1] == k) {
                        l = true;
                        break
                    }
                }
            }
            return l
        };
        this.isFloating = function () {
            return aT != null
        };
        this.connectorSelector = function () {
            var k = aJ.connections[0];
            if (aJ.isTarget && k) {
                return k
            } else {
                return (aJ.connections.length < al) || al == -1 ? null : k
            }
        };
        this.isFull = function () {
            return !(aJ.isFloating() || al < 1 || aJ.connections.length < al)
        };
        this.setDragAllowedWhenFull = function (k) {
            au = k
        };
        this.setStyle = aJ.setPaintStyle;
        this.equals = function (k) {
            return this.anchor.equals(k.anchor)
        };
        var av = function (l) {
            var m = 0;
            if (l != null) {
                for (var k = 0; k < aJ.connections.length; k++) {
                    if (aJ.connections[k].sourceId == l || aJ.connections[k].targetId == l) {
                        m = k;
                        break
                    }
                }
            }
            return aJ.connections[m]
        };
        this.paint = function (x) {
            x = x || {};
            var q = x.timestamp, r = !(x.recalc === false);
            if (!q || aJ.timestamp !== q) {
                var y = aH.updateOffset({elId: c, timestamp: q, recalc: r});
                var k = x.offset ? x.offset.o : y.o;
                if (k) {
                    var u = x.anchorPoint, w = x.connectorPaintStyle;
                    if (u == null) {
                        var C = x.dimensions || y.s;
                        if (k == null || C == null) {
                            y = aH.updateOffset({elId: c, timestamp: q});
                            k = y.o;
                            C = y.s
                        }
                        var A = {
                            xy: [k.left, k.top],
                            wh: C,
                            element: aJ,
                            timestamp: q
                        };
                        if (r && aJ.anchor.isDynamic && aJ.connections.length > 0) {
                            var o = av(x.elementWithPrecedence), l = o.endpoints[0] == aJ ? 1 : 0, v = l == 0 ? o.sourceId : o.targetId, m = aH.getCachedData(v), p = m.o, n = m.s;
                            A.txy = [p.left, p.top];
                            A.twh = n;
                            A.tElement = o.endpoints[l]
                        }
                        u = aJ.anchor.compute(A)
                    }
                    d.compute(u, aJ.anchor.getOrientation(aJ), aJ.paintStyleInUse, w || aJ.paintStyleInUse);
                    d.paint(aJ.paintStyleInUse, aJ.anchor);
                    aJ.timestamp = q;
                    for (var s = 0; s < aJ.overlays.length; s++) {
                        var B = aJ.overlays[s];
                        if (B.isVisible()) {
                            aJ.overlayPlacements[s] = B.draw(aJ.endpoint, aJ.paintStyleInUse);
                            B.paint(aJ.overlayPlacements[s])
                        }
                    }
                }
            }
        };
        this.repaint = this.paint;
        if (aQ.isDragSupported(ao)) {
            var ay = {
                id: null,
                element: null
            }, aO = null, aI = false, aD = null, aA = h(ay, aH);
            var ax = function () {
                aO = aJ.connectorSelector();
                var p = true;
                if (!aJ.isEnabled()) {
                    p = false
                }
                if (aO == null && !aR.isSource) {
                    p = false
                }
                if (aR.isSource && aJ.isFull() && !au) {
                    p = false
                }
                if (aO != null && !aO.isDetachable()) {
                    p = false
                }
                if (p === false) {
                    if (aQ.stopDrag) {
                        aQ.stopDrag()
                    }
                    aA.stopDrag();
                    return false
                }
                aJ.addClass("endpointDrag");
                if (aO && !aJ.isFull() && aR.isSource) {
                    aO = null
                }
                aH.updateOffset({elId: c});
                an = aJ.makeInPlaceCopy();
                an.referenceEndpoint = aJ;
                an.paint();
                f(ay, aJ.parent, aH);
                var q = aB(an.canvas), k = aS(q, aH), n = aH.adjustForParentOffsetAndScroll([k.left, k.top], an.canvas), o = aB(aJ.canvas);
                aQ.setOffset(ay.element, {left: n[0], top: n[1]});
                if (aJ.parentAnchor) {
                    aJ.anchor = aH.makeAnchor(aJ.parentAnchor, aJ.elementId, aH)
                }
                aQ.setAttribute(o, "dragId", ay.id);
                aQ.setAttribute(o, "elId", c);
                aT = g(aJ.getPaintStyle(), aJ.anchor, d, aJ.canvas, ay.element, aH, ah);
                aJ.canvas.style.visibility = "hidden";
                if (aO == null) {
                    aJ.anchor.locked = true;
                    aJ.setHover(false, false);
                    aO = aC({
                        sourceEndpoint: aJ,
                        targetEndpoint: aT,
                        source: aJ.endpointWillMoveTo || ao,
                        target: ay.element,
                        anchors: [aJ.anchor, aT.anchor],
                        paintStyle: aR.connectorStyle,
                        hoverPaintStyle: aR.connectorHoverStyle,
                        connector: aR.connector,
                        overlays: aR.connectorOverlays,
                        type: aJ.connectionType,
                        cssClass: aJ.connectorClass,
                        hoverClass: aJ.connectorHoverClass
                    });
                    aO.addClass(aH.draggingClass);
                    aT.addClass(aH.draggingClass);
                    aH.fire("connectionDrag", aO)
                } else {
                    aI = true;
                    aO.setHover(false);
                    aL(q, false, true);
                    var l = aO.endpoints[0].id == aJ.id ? 0 : 1;
                    aO.floatingAnchorIndex = l;
                    aJ.detachFromConnection(aO);
                    var r = jsPlumb.CurrentLibrary.getDragScope(o);
                    aQ.setAttribute(o, "originalScope", r);
                    var m = aQ.getDropScope(o);
                    aQ.setDragScope(o, m);
                    if (l == 0) {
                        aD = [aO.source, aO.sourceId, ae, r];
                        aO.source = ay.element;
                        aO.sourceId = ay.id
                    } else {
                        aD = [aO.target, aO.targetId, ae, r];
                        aO.target = ay.element;
                        aO.targetId = ay.id
                    }
                    aO.endpoints[l == 0 ? 1 : 0].anchor.locked = true;
                    aO.suspendedEndpoint = aO.endpoints[l];
                    aO.suspendedElement = aO.endpoints[l].getElement();
                    aO.suspendedElementId = aO.endpoints[l].elementId;
                    aO.suspendedElementType = l == 0 ? "source" : "target";
                    aO.suspendedEndpoint.setHover(false);
                    aT.referenceEndpoint = aO.suspendedEndpoint;
                    aO.endpoints[l] = aT;
                    aO.addClass(aH.draggingClass);
                    aT.addClass(aH.draggingClass);
                    aH.fire("connectionDrag", aO)
                }
                aw[ay.id] = aO;
                aH.anchorManager.addFloatingConnection(ay.id, aO);
                aT.addConnection(aO);
                aK.addToList(aR.endpointsByElement, ay.id, aT);
                aH.currentlyDragging = true
            };
            var aN = aR.dragOptions || {}, aG = jsPlumb.extend({}, aQ.defaultDragOptions), aj = aQ.dragEvents.start, ad = aQ.dragEvents.stop, ar = aQ.dragEvents.drag;
            aN = jsPlumb.extend(aG, aN);
            aN.scope = aN.scope || aJ.scope;
            aN[aj] = aH.wrap(aN[aj], ax);
            aN[ar] = aH.wrap(aN[ar], aA.drag);
            aN[ad] = aH.wrap(aN[ad], function () {
                var k = aQ.getDropEvent(arguments);
                aK.removeWithFunction(aR.endpointsByElement[ay.id], function (m) {
                    return m.id == aT.id
                });
                aK.removeElement(an.canvas, ao);
                aH.anchorManager.clearFor(ay.id);
                var l = aO.floatingAnchorIndex == null ? 1 : aO.floatingAnchorIndex;
                aO.endpoints[l == 0 ? 1 : 0].anchor.locked = false;
                if (aO.endpoints[l] == aT) {
                    if (aI && aO.suspendedEndpoint) {
                        if (l == 0) {
                            aO.source = aD[0];
                            aO.sourceId = aD[1]
                        } else {
                            aO.target = aD[0];
                            aO.targetId = aD[1]
                        }
                        aQ.setDragScope(aD[2], aD[3]);
                        aO.endpoints[l] = aO.suspendedEndpoint;
                        if (aO.isReattach() || aO._forceReattach || aO._forceDetach || !aO.endpoints[l == 0 ? 1 : 0].detach(aO, false, false, true, k)) {
                            aO.setHover(false);
                            aO.floatingAnchorIndex = null;
                            aO.suspendedEndpoint.addConnection(aO);
                            aH.repaint(aD[1])
                        }
                        aO._forceDetach = null;
                        aO._forceReattach = null
                    } else {
                        aK.removeElements(aO.getConnector().getDisplayElements(), aJ.parent);
                        aJ.detachFromConnection(aO)
                    }
                }
                aK.removeElements([ay.element[0], aT.canvas], ao);
                aH.dragManager.elementRemoved(aT.elementId);
                aJ.canvas.style.visibility = "visible";
                aJ.anchor.locked = false;
                aJ.paint({recalc: false});
                aO.removeClass(aH.draggingClass);
                aT.removeClass(aH.draggingClass);
                aH.fire("connectionDragStop", aO);
                aO = null;
                an = null;
                delete aR.endpointsByElement[aT.elementId];
                aT.anchor = null;
                aT = null;
                aH.currentlyDragging = false
            });
            var ae = aB(aJ.canvas);
            aQ.initDraggable(ae, aN, true, aH)
        }
        var aL = function (q, l, n, k) {
            if ((aR.isTarget || l) && aQ.isDropSupported(ao)) {
                var p = aR.dropOptions || aH.Defaults.DropOptions || jsPlumb.Defaults.DropOptions;
                p = jsPlumb.extend({}, p);
                p.scope = p.scope || aJ.scope;
                var r = aQ.dragEvents.drop, m = aQ.dragEvents.over, s = aQ.dragEvents.out, o = function () {
                    aJ.removeClass(aH.endpointDropAllowedClass);
                    aJ.removeClass(aH.endpointDropForbiddenClass);
                    var G = aQ.getDropEvent(arguments), D = aB(aQ.getDragObject(arguments)), E = ap(D, "dragId"), B = ap(D, "elId"), H = ap(D, "originalScope"), x = aw[E];
                    var A = x.suspendedEndpoint && (x.suspendedEndpoint.id == aJ.id || aJ.referenceEndpoint && x.suspendedEndpoint.id == aJ.referenceEndpoint.id);
                    if (A) {
                        x._forceReattach = true;
                        return
                    }
                    if (x != null) {
                        var v = x.floatingAnchorIndex == null ? 1 : x.floatingAnchorIndex, u = v == 0 ? 1 : 0;
                        if (H) {
                            jsPlumb.CurrentLibrary.setDragScope(D, H)
                        }
                        var I = k != null ? k.isEnabled() : true;
                        if (aJ.isFull()) {
                            aJ.fire("maxConnections", {
                                endpoint: aJ,
                                connection: x,
                                maxConnections: al
                            }, G)
                        }
                        if (!aJ.isFull() && !(v == 0 && !aJ.isSource) && !(v == 1 && !aJ.isTarget) && I) {
                            var y = true;
                            if (x.suspendedEndpoint && x.suspendedEndpoint.id != aJ.id) {
                                if (v == 0) {
                                    x.source = x.suspendedEndpoint.element;
                                    x.sourceId = x.suspendedEndpoint.elementId
                                } else {
                                    x.target = x.suspendedEndpoint.element;
                                    x.targetId = x.suspendedEndpoint.elementId
                                }
                                if (!x.isDetachAllowed(x) || !x.endpoints[v].isDetachAllowed(x) || !x.suspendedEndpoint.isDetachAllowed(x) || !aH.checkCondition("beforeDetach", x)) {
                                    y = false
                                }
                            }
                            if (v == 0) {
                                x.source = aJ.element;
                                x.sourceId = aJ.elementId
                            } else {
                                x.target = aJ.element;
                                x.targetId = aJ.elementId
                            }
                            var w = function () {
                                x.floatingAnchorIndex = null
                            };
                            var F = function () {
                                x.endpoints[v].detachFromConnection(x);
                                if (x.suspendedEndpoint) {
                                    x.suspendedEndpoint.detachFromConnection(x)
                                }
                                x.endpoints[v] = aJ;
                                aJ.addConnection(x);
                                var J = aJ.getParameters();
                                for (var L in J) {
                                    x.setParameter(L, J[L])
                                }
                                if (!x.suspendedEndpoint) {
                                    if (J.draggable) {
                                        jsPlumb.CurrentLibrary.initDraggable(aJ.element, aN, true, aH)
                                    }
                                } else {
                                    var K = x.suspendedEndpoint.getElement(), M = x.suspendedEndpoint.elementId;
                                    a({
                                        source: v == 0 ? K : x.source,
                                        target: v == 1 ? K : x.target,
                                        sourceId: v == 0 ? M : x.sourceId,
                                        targetId: v == 1 ? M : x.targetId,
                                        sourceEndpoint: v == 0 ? x.suspendedEndpoint : x.endpoints[0],
                                        targetEndpoint: v == 1 ? x.suspendedEndpoint : x.endpoints[1],
                                        connection: x
                                    }, true, G)
                                }
                                if (x.endpoints[0].addedViaMouse) {
                                    x.endpointsToDeleteOnDetach[0] = x.endpoints[0]
                                }
                                if (x.endpoints[1].addedViaMouse) {
                                    x.endpointsToDeleteOnDetach[1] = x.endpoints[1]
                                }
                                b(x, null, G);
                                w()
                            };
                            var C = function () {
                                if (x.suspendedEndpoint) {
                                    x.endpoints[v] = x.suspendedEndpoint;
                                    x.setHover(false);
                                    x._forceDetach = true;
                                    if (v == 0) {
                                        x.source = x.suspendedEndpoint.element;
                                        x.sourceId = x.suspendedEndpoint.elementId
                                    } else {
                                        x.target = x.suspendedEndpoint.element;
                                        x.targetId = x.suspendedEndpoint.elementId
                                    }
                                    x.suspendedEndpoint.addConnection(x);
                                    x.endpoints[0].repaint();
                                    x.repaint();
                                    aH.repaint(x.sourceId);
                                    x._forceDetach = false
                                }
                                w()
                            };
                            y = y && aJ.isDropAllowed(x.sourceId, x.targetId, x.scope, x, aJ);
                            if (y) {
                                F()
                            } else {
                                C()
                            }
                        }
                        aH.currentlyDragging = false;
                        delete aw[E];
                        aH.anchorManager.removeFloatingConnection(E)
                    }
                };
                p[r] = aH.wrap(p[r], o);
                p[m] = aH.wrap(p[m], function () {
                    var y = aQ.getDragObject(arguments), u = ap(aB(y), "dragId"), v = aw[u];
                    if (v != null) {
                        var A = v.floatingAnchorIndex == null ? 1 : v.floatingAnchorIndex;
                        var w = (aJ.isTarget && v.floatingAnchorIndex != 0) || (v.suspendedEndpoint && aJ.referenceEndpoint && aJ.referenceEndpoint.id == v.suspendedEndpoint.id);
                        if (w) {
                            var x = aH.checkCondition("checkDropAllowed", {
                                sourceEndpoint: v.endpoints[A],
                                targetEndpoint: aJ,
                                connection: v
                            });
                            aJ[(x ? "add" : "remove") + "Class"](aH.endpointDropAllowedClass);
                            aJ[(x ? "remove" : "add") + "Class"](aH.endpointDropForbiddenClass);
                            v.endpoints[A].anchor.over(aJ.anchor)
                        }
                    }
                });
                p[s] = aH.wrap(p[s], function () {
                    var x = aQ.getDragObject(arguments), u = ap(aB(x), "dragId"), v = aw[u];
                    if (v != null) {
                        var y = v.floatingAnchorIndex == null ? 1 : v.floatingAnchorIndex;
                        var w = (aJ.isTarget && v.floatingAnchorIndex != 0) || (v.suspendedEndpoint && aJ.referenceEndpoint && aJ.referenceEndpoint.id == v.suspendedEndpoint.id);
                        if (w) {
                            aJ.removeClass(aH.endpointDropAllowedClass);
                            aJ.removeClass(aH.endpointDropForbiddenClass);
                            v.endpoints[y].anchor.out()
                        }
                    }
                });
                aQ.initDroppable(q, p, true, n)
            }
        };
        aL(aB(aJ.canvas), true, !(aR._transient || aJ.anchor.isFloating), aJ);
        if (aR.type) {
            aJ.addType(aR.type, aR.data, aH.isSuspendDrawing())
        }
        return aJ
    }
})();
(function () {
    jsPlumb.Connection = function (aD) {
        var au = this, an = true, T, Y, aq = aD._jsPlumb, az = jsPlumb.CurrentLibrary, ai = az.getAttribute, U = az.getElementObject, aw = jsPlumbUtil, aE = az.getOffset, V = aD.newConnection, aa = aD.newEndpoint, aB = null;
        au.idPrefix = "_jsplumb_c_";
        au.defaultLabelLocation = 0.5;
        au.defaultOverlayKeys = ["Overlays", "ConnectionOverlays"];
        this.parent = aD.parent;
        overlayCapableJsPlumbUIComponent.apply(this, arguments);
        this.isVisible = function () {
            return an
        };
        this.setVisible = function (a) {
            an = a;
            au[a ? "showOverlays" : "hideOverlays"]();
            if (aB && aB.canvas) {
                aB.canvas.style.display = a ? "block" : "none"
            }
            au.repaint()
        };
        var ag = aD.editable === true;
        this.setEditable = function (a) {
            if (aB && aB.isEditable()) {
                ag = a
            }
            return ag
        };
        this.isEditable = function () {
            return ag
        };
        this.editStarted = function () {
            au.fire("editStarted", {path: aB.getPath()});
            aq.setHoverSuspended(true)
        };
        this.editCompleted = function () {
            au.fire("editCompleted", {path: aB.getPath()});
            au.setHover(false);
            aq.setHoverSuspended(false)
        };
        this.editCanceled = function () {
            au.fire("editCanceled", {path: aB.getPath()});
            au.setHover(false);
            aq.setHoverSuspended(false)
        };
        var at = this.addClass, af = this.removeClass;
        this.addClass = function (a, b) {
            at(a);
            if (b) {
                au.endpoints[0].addClass(a);
                au.endpoints[1].addClass(a)
            }
        };
        this.removeClass = function (a, b) {
            af(a);
            if (b) {
                au.endpoints[0].removeClass(a);
                au.endpoints[1].removeClass(a)
            }
        };
        this.getTypeDescriptor = function () {
            return "connection"
        };
        this.getDefaultType = function () {
            return {
                parameters: {},
                scope: null,
                detachable: au._jsPlumb.Defaults.ConnectionsDetachable,
                rettach: au._jsPlumb.Defaults.ReattachConnections,
                paintStyle: au._jsPlumb.Defaults.PaintStyle || jsPlumb.Defaults.PaintStyle,
                connector: au._jsPlumb.Defaults.Connector || jsPlumb.Defaults.Connector,
                hoverPaintStyle: au._jsPlumb.Defaults.HoverPaintStyle || jsPlumb.Defaults.HoverPaintStyle,
                overlays: au._jsPlumb.Defaults.ConnectorOverlays || jsPlumb.Defaults.ConnectorOverlays
            }
        };
        var Z = this.applyType;
        this.applyType = function (b, a) {
            Z(b, a);
            if (b.detachable != null) {
                au.setDetachable(b.detachable)
            }
            if (b.reattach != null) {
                au.setReattach(b.reattach)
            }
            if (b.scope) {
                au.scope = b.scope
            }
            ag = b.editable;
            au.setConnector(b.connector, a)
        };
        Y = au.setHover;
        au.setHover = function (a) {
            aB.setHover.apply(aB, arguments);
            Y.apply(au, arguments)
        };
        T = function (a) {
            if (!aq.isConnectionBeingDragged()) {
                au.setHover(a, false)
            }
        };
        var ad = function (d, a, b) {
            var c = new Object();
            if (!aq.Defaults.DoNotThrowErrors && jsPlumb.Connectors[a] == null) {
                throw {msg: "jsPlumb: unknown connector type '" + a + "'"}
            }
            jsPlumb.Connectors[a].apply(c, [b]);
            jsPlumb.ConnectorRenderers[d].apply(c, [b]);
            return c
        };
        this.setConnector = function (b, d) {
            if (aB != null) {
                aw.removeElements(aB.getDisplayElements())
            }
            var a = {
                _jsPlumb: au._jsPlumb,
                parent: aD.parent,
                cssClass: aD.cssClass,
                container: aD.container,
                tooltip: au.tooltip,
                "pointer-events": aD["pointer-events"]
            }, c = aq.getRenderMode();
            if (aw.isString(b)) {
                aB = ad(c, b, a)
            } else {
                if (aw.isArray(b)) {
                    if (b.length == 1) {
                        aB = ad(c, b[0], a)
                    } else {
                        aB = ad(c, b[0], aw.merge(b[1], a))
                    }
                }
            }
            au.bindListeners(aB, au, T);
            au.canvas = aB.canvas;
            if (ag && jsPlumb.ConnectorEditors != null && jsPlumb.ConnectorEditors[aB.type] && aB.isEditable()) {
                new jsPlumb.ConnectorEditors[aB.type]({
                    connector: aB,
                    connection: au,
                    params: aD.editorParams || {}
                })
            } else {
                ag = false
            }
            if (!d) {
                au.repaint()
            }
        };
        this.getConnector = function () {
            return aB
        };
        this.source = U(aD.source);
        this.target = U(aD.target);
        if (aD.sourceEndpoint) {
            this.source = aD.sourceEndpoint.endpointWillMoveTo || aD.sourceEndpoint.getElement()
        }
        if (aD.targetEndpoint) {
            this.target = aD.targetEndpoint.getElement()
        }
        au.previousConnection = aD.previousConnection;
        this.sourceId = ai(this.source, "id");
        this.targetId = ai(this.target, "id");
        this.scope = aD.scope;
        this.endpoints = [];
        this.endpointStyles = [];
        var ah = function (a, b) {
            return (a) ? aq.makeAnchor(a, b, aq) : null
        }, ae = function (l, d, k, g, f, h, e) {
            var c;
            if (l) {
                au.endpoints[d] = l;
                l.addConnection(au)
            } else {
                if (!k.endpoints) {
                    k.endpoints = [null, null]
                }
                var m = k.endpoints[d] || k.endpoint || aq.Defaults.Endpoints[d] || jsPlumb.Defaults.Endpoints[d] || aq.Defaults.Endpoint || jsPlumb.Defaults.Endpoint;
                if (!k.endpointStyles) {
                    k.endpointStyles = [null, null]
                }
                if (!k.endpointHoverStyles) {
                    k.endpointHoverStyles = [null, null]
                }
                var o = k.endpointStyles[d] || k.endpointStyle || aq.Defaults.EndpointStyles[d] || jsPlumb.Defaults.EndpointStyles[d] || aq.Defaults.EndpointStyle || jsPlumb.Defaults.EndpointStyle;
                if (o.fillStyle == null && h != null) {
                    o.fillStyle = h.strokeStyle
                }
                if (o.outlineColor == null && h != null) {
                    o.outlineColor = h.outlineColor
                }
                if (o.outlineWidth == null && h != null) {
                    o.outlineWidth = h.outlineWidth
                }
                var a = k.endpointHoverStyles[d] || k.endpointHoverStyle || aq.Defaults.EndpointHoverStyles[d] || jsPlumb.Defaults.EndpointHoverStyles[d] || aq.Defaults.EndpointHoverStyle || jsPlumb.Defaults.EndpointHoverStyle;
                if (e != null) {
                    if (a == null) {
                        a = {}
                    }
                    if (a.fillStyle == null) {
                        a.fillStyle = e.strokeStyle
                    }
                }
                var b = k.anchors ? k.anchors[d] : k.anchor ? k.anchor : ah(aq.Defaults.Anchors[d], f) || ah(jsPlumb.Defaults.Anchors[d], f) || ah(aq.Defaults.Anchor, f) || ah(jsPlumb.Defaults.Anchor, f), n = k.uuids ? k.uuids[d] : null;
                c = aa({
                    paintStyle: o,
                    hoverPaintStyle: a,
                    endpoint: m,
                    connections: [au],
                    uuid: n,
                    anchor: b,
                    source: g,
                    scope: k.scope,
                    container: k.container,
                    reattach: k.reattach || aq.Defaults.ReattachConnections,
                    detachable: k.detachable || aq.Defaults.ConnectionsDetachable
                });
                au.endpoints[d] = c;
                if (k.drawEndpoints === false) {
                    c.setVisible(false, true, true)
                }
            }
            return c
        };
        var ax = ae(aD.sourceEndpoint, 0, aD, au.source, au.sourceId, aD.paintStyle, aD.hoverPaintStyle);
        if (ax) {
            aw.addToList(aD.endpointsByElement, this.sourceId, ax)
        }
        var ay = ae(aD.targetEndpoint, 1, aD, au.target, au.targetId, aD.paintStyle, aD.hoverPaintStyle);
        if (ay) {
            aw.addToList(aD.endpointsByElement, this.targetId, ay)
        }
        if (!this.scope) {
            this.scope = this.endpoints[0].scope
        }
        au.endpointsToDeleteOnDetach = [null, null];
        if (aD.deleteEndpointsOnDetach) {
            if (aD.sourceIsNew) {
                au.endpointsToDeleteOnDetach[0] = au.endpoints[0]
            }
            if (aD.targetIsNew) {
                au.endpointsToDeleteOnDetach[1] = au.endpoints[1]
            }
        }
        if (aD.endpointsToDeleteOnDetach) {
            au.endpointsToDeleteOnDetach = aD.endpointsToDeleteOnDetach
        }
        au.setConnector(this.endpoints[0].connector || this.endpoints[1].connector || aD.connector || aq.Defaults.Connector || jsPlumb.Defaults.Connector, true);
        if (aD.path) {
            aB.setPath(aD.path)
        }
        this.setPaintStyle(this.endpoints[0].connectorStyle || this.endpoints[1].connectorStyle || aD.paintStyle || aq.Defaults.PaintStyle || jsPlumb.Defaults.PaintStyle, true);
        this.setHoverPaintStyle(this.endpoints[0].connectorHoverStyle || this.endpoints[1].connectorHoverStyle || aD.hoverPaintStyle || aq.Defaults.HoverPaintStyle || jsPlumb.Defaults.HoverPaintStyle, true);
        this.paintStyleInUse = this.getPaintStyle();
        var al = aq.getSuspendedAt();
        aq.updateOffset({elId: this.sourceId, timestamp: al});
        aq.updateOffset({elId: this.targetId, timestamp: al});
        if (!aq.isSuspendDrawing()) {
            var X = aq.getCachedData(this.sourceId), aA = X.o, ak = X.s, ap = aq.getCachedData(this.targetId), ar = ap.o, ac = ap.s, S = al || aq.timestamp(), av = this.endpoints[0].anchor.compute({
                xy: [aA.left, aA.top],
                wh: ak,
                element: this.endpoints[0],
                elementId: this.endpoints[0].elementId,
                txy: [ar.left, ar.top],
                twh: ac,
                tElement: this.endpoints[1],
                timestamp: S
            });
            this.endpoints[0].paint({anchorLoc: av, timestamp: S});
            av = this.endpoints[1].anchor.compute({
                xy: [ar.left, ar.top],
                wh: ac,
                element: this.endpoints[1],
                elementId: this.endpoints[1].elementId,
                txy: [aA.left, aA.top],
                twh: ak,
                tElement: this.endpoints[0],
                timestamp: S
            });
            this.endpoints[1].paint({anchorLoc: av, timestamp: S})
        }
        var ao = aq.Defaults.ConnectionsDetachable;
        if (aD.detachable === false) {
            ao = false
        }
        if (au.endpoints[0].connectionsDetachable === false) {
            ao = false
        }
        if (au.endpoints[1].connectionsDetachable === false) {
            ao = false
        }
        this.isDetachable = function () {
            return ao === true
        };
        this.setDetachable = function (a) {
            ao = a === true
        };
        var W = aD.reattach || au.endpoints[0].reattachConnections || au.endpoints[1].reattachConnections || aq.Defaults.ReattachConnections;
        this.isReattach = function () {
            return W === true
        };
        this.setReattach = function (a) {
            W = a === true
        };
        var aC = aD.cost || au.endpoints[0].getConnectionCost();
        au.getCost = function () {
            return aC
        };
        au.setCost = function (a) {
            aC = a
        };
        var am = aD.directed;
        if (aD.directed == null) {
            am = au.endpoints[0].areConnectionsDirected()
        }
        au.isDirected = function () {
            return am === true
        };
        var ab = jsPlumb.extend({}, this.endpoints[0].getParameters());
        jsPlumb.extend(ab, this.endpoints[1].getParameters());
        jsPlumb.extend(ab, au.getParameters());
        au.setParameters(ab);
        this.getAttachedElements = function () {
            return au.endpoints
        };
        this.moveParent = function (a) {
            var b = jsPlumb.CurrentLibrary, c = b.getParent(aB.canvas);
            if (aB.bgCanvas) {
                b.removeElement(aB.bgCanvas);
                b.appendElement(aB.bgCanvas, a)
            }
            b.removeElement(aB.canvas);
            b.appendElement(aB.canvas, a);
            for (var d = 0; d < au.overlays.length; d++) {
                if (au.overlays[d].isAppendedAtTopLevel) {
                    b.removeElement(au.overlays[d].canvas);
                    b.appendElement(au.overlays[d].canvas, a);
                    if (au.overlays[d].reattachListeners) {
                        au.overlays[d].reattachListeners(aB)
                    }
                }
            }
            if (aB.reattachListeners) {
                aB.reattachListeners()
            }
        };
        var aj = null;
        this.paint = function (o) {
            if (an) {
                o = o || {};
                var y = o.elId, x = o.ui, c = o.recalc, g = o.timestamp, w = false, p = w ? this.sourceId : this.targetId, d = w ? this.targetId : this.sourceId, f = w ? 0 : 1, m = w ? 1 : 0;
                if (g == null || g != aj) {
                    var l = aq.updateOffset({
                        elId: y,
                        offset: x,
                        recalc: c,
                        timestamp: g
                    }).o, b = aq.updateOffset({
                        elId: p,
                        timestamp: g
                    }).o, u = this.endpoints[m], h = this.endpoints[f];
                    if (o.clearEdits) {
                        u.anchor.clearUserDefinedLocation();
                        h.anchor.clearUserDefinedLocation();
                        aB.setEdited(false)
                    }
                    var e = u.anchor.getCurrentLocation(u), q = h.anchor.getCurrentLocation(h);
                    aB.resetBounds();
                    aB.compute({
                        sourcePos: e,
                        targetPos: q,
                        sourceEndpoint: this.endpoints[m],
                        targetEndpoint: this.endpoints[f],
                        lineWidth: au.paintStyleInUse.lineWidth,
                        sourceInfo: l,
                        targetInfo: b,
                        clearEdits: o.clearEdits === true
                    });
                    var a = {
                        minX: Infinity,
                        minY: Infinity,
                        maxX: -Infinity,
                        maxY: -Infinity
                    };
                    for (var r = 0; r < au.overlays.length; r++) {
                        var v = au.overlays[r];
                        if (v.isVisible()) {
                            au.overlayPlacements[r] = v.draw(aB, au.paintStyleInUse);
                            a.minX = Math.min(a.minX, au.overlayPlacements[r].minX);
                            a.maxX = Math.max(a.maxX, au.overlayPlacements[r].maxX);
                            a.minY = Math.min(a.minY, au.overlayPlacements[r].minY);
                            a.maxY = Math.max(a.maxY, au.overlayPlacements[r].maxY)
                        }
                    }
                    var k = parseFloat(au.paintStyleInUse.lineWidth || 1) / 2, n = parseFloat(au.paintStyleInUse.lineWidth || 0), s = {
                        xmin: Math.min(aB.bounds.minX - (k + n), a.minX),
                        ymin: Math.min(aB.bounds.minY - (k + n), a.minY),
                        xmax: Math.max(aB.bounds.maxX + (k + n), a.maxX),
                        ymax: Math.max(aB.bounds.maxY + (k + n), a.maxY)
                    };
                    aB.paint(au.paintStyleInUse, null, s);
                    for (var r = 0; r < au.overlays.length; r++) {
                        var v = au.overlays[r];
                        if (v.isVisible()) {
                            v.paint(au.overlayPlacements[r], s)
                        }
                    }
                }
                aj = g
            }
        };
        this.repaint = function (a) {
            a = a || {};
            var b = !(a.recalc === false);
            this.paint({
                elId: this.sourceId,
                recalc: b,
                timestamp: a.timestamp,
                clearEdits: a.clearEdits
            })
        };
        var R = aD.type || au.endpoints[0].connectionType || au.endpoints[1].connectionType;
        if (R) {
            au.addType(R, aD.data, aq.isSuspendDrawing())
        }
    }
})();
(function () {
    jsPlumb.DOMElementComponent = function (a) {
        jsPlumb.jsPlumbUIComponent.apply(this, arguments);
        this.mousemove = this.dblclick = this.click = this.mousedown = this.mouseup = function (b) {
        }
    };
    jsPlumb.Segments = {
        AbstractSegment: function (a) {
            this.params = a;
            this.findClosestPointOnPath = function (c, b) {
                return {d: Infinity, x: null, y: null, l: null}
            };
            this.getBounds = function () {
                return {
                    minX: Math.min(a.x1, a.x2),
                    minY: Math.min(a.y1, a.y2),
                    maxX: Math.max(a.x1, a.x2),
                    maxY: Math.max(a.y1, a.y2)
                }
            }
        }, Straight: function (s) {
            var a = this, c = jsPlumb.Segments.AbstractSegment.apply(this, arguments), v, u, b, w, x, d, m, y = function () {
                v = Math.sqrt(Math.pow(x - w, 2) + Math.pow(m - d, 2));
                u = jsPlumbUtil.gradient({x: w, y: d}, {x: x, y: m});
                b = -1 / u
            };
            this.type = "Straight";
            a.getLength = function () {
                return v
            };
            a.getGradient = function () {
                return u
            };
            this.getCoordinates = function () {
                return {x1: w, y1: d, x2: x, y2: m}
            };
            this.setCoordinates = function (k) {
                w = k.x1;
                d = k.y1;
                x = k.x2;
                m = k.y2;
                y()
            };
            this.setCoordinates({x1: s.x1, y1: s.y1, x2: s.x2, y2: s.y2});
            this.getBounds = function () {
                return {
                    minX: Math.min(w, x),
                    minY: Math.min(d, m),
                    maxX: Math.max(w, x),
                    maxY: Math.max(d, m)
                }
            };
            this.pointOnPath = function (n, l) {
                if (n == 0 && !l) {
                    return {x: w, y: d}
                } else {
                    if (n == 1 && !l) {
                        return {x: x, y: m}
                    } else {
                        var k = l ? n > 0 ? n : v + n : n * v;
                        return jsPlumbUtil.pointOnLine({x: w, y: d}, {
                            x: x,
                            y: m
                        }, k)
                    }
                }
            };
            this.gradientAtPoint = function (k) {
                return u
            };
            this.pointAlongPathFrom = function (k, l, n) {
                var o = a.pointOnPath(k, n), p = k == 1 ? {
                    x: w + ((x - w) * 10),
                    y: d + ((d - m) * 10)
                } : l <= 0 ? {x: w, y: d} : {x: x, y: m};
                if (l <= 0 && Math.abs(l) > 1) {
                    l *= -1
                }
                return jsPlumbUtil.pointOnLine(o, p, l)
            };
            this.findClosestPointOnPath = function (q, r) {
                if (u == 0) {
                    return {x: q, y: d, d: Math.abs(r - d)}
                } else {
                    if (u == Infinity || u == -Infinity) {
                        return {x: w, y: r, d: Math.abs(q - 1)}
                    } else {
                        var l = d - (u * w), p = r - (b * q), o = (p - l) / (u - b), D = (u * o) + l, k = jsPlumbUtil.lineLength([q, r], [o, D]), n = jsPlumbUtil.lineLength([o, D], [w, d]);
                        return {d: k, x: o, y: D, l: n / v}
                    }
                }
            }
        }, Arc: function (w) {
            var a = this, b = jsPlumb.Segments.AbstractSegment.apply(this, arguments), u = function (k, l) {
                return jsPlumbUtil.theta([w.cx, w.cy], [k, l])
            }, B = function (m) {
                if (a.anticlockwise) {
                    var n = a.startAngle < a.endAngle ? a.startAngle + x : a.startAngle, k = Math.abs(n - a.endAngle);
                    return n - (k * m)
                } else {
                    var l = a.endAngle < a.startAngle ? a.endAngle + x : a.endAngle, k = Math.abs(l - a.startAngle);
                    return a.startAngle + (k * m)
                }
            }, x = 2 * Math.PI;
            this.radius = w.r;
            this.anticlockwise = w.ac;
            this.type = "Arc";
            if (w.startAngle && w.endAngle) {
                this.startAngle = w.startAngle;
                this.endAngle = w.endAngle;
                this.x1 = w.cx + (a.radius * Math.cos(w.startAngle));
                this.y1 = w.cy + (a.radius * Math.sin(w.startAngle));
                this.x2 = w.cx + (a.radius * Math.cos(w.endAngle));
                this.y2 = w.cy + (a.radius * Math.sin(w.endAngle))
            } else {
                this.startAngle = u(w.x1, w.y1);
                this.endAngle = u(w.x2, w.y2);
                this.x1 = w.x1;
                this.y1 = w.y1;
                this.x2 = w.x2;
                this.y2 = w.y2
            }
            if (this.endAngle < 0) {
                this.endAngle += x
            }
            if (this.startAngle < 0) {
                this.startAngle += x
            }
            this.segment = jsPlumbUtil.segment([this.x1, this.y1], [this.x2, this.y2]);
            var s = a.endAngle < a.startAngle ? a.endAngle + x : a.endAngle;
            a.sweep = Math.abs(s - a.startAngle);
            if (a.anticlockwise) {
                a.sweep = x - a.sweep
            }
            var c = 2 * Math.PI * a.radius, A = a.sweep / x, y = c * A;
            this.getLength = function () {
                return y
            };
            this.getBounds = function () {
                return {
                    minX: w.cx - w.r,
                    maxX: w.cx + w.r,
                    minY: w.cy - w.r,
                    maxY: w.cy + w.r
                }
            };
            var v = 1e-10, d = function (k) {
                var l = Math.floor(k), m = Math.ceil(k);
                if (k - l < v) {
                    return l
                } else {
                    if (m - k < v) {
                        return m
                    }
                }
                return k
            };
            this.pointOnPath = function (o, k) {
                if (o == 0) {
                    return {x: a.x1, y: a.y1, theta: a.startAngle}
                } else {
                    if (o == 1) {
                        return {x: a.x2, y: a.y2, theta: a.endAngle}
                    }
                }
                if (k) {
                    o = o / y
                }
                var l = B(o), m = w.cx + (w.r * Math.cos(l)), n = w.cy + (w.r * Math.sin(l));
                return {x: d(m), y: d(n), theta: l}
            };
            this.gradientAtPoint = function (m, k) {
                var l = a.pointOnPath(m, k);
                var n = jsPlumbUtil.normal([w.cx, w.cy], [l.x, l.y]);
                if (!a.anticlockwise && (n == Infinity || n == -Infinity)) {
                    n *= -1
                }
                return n
            };
            this.pointAlongPathFrom = function (k, D, l) {
                var q = a.pointOnPath(k, l), r = D / c * 2 * Math.PI, p = a.anticlockwise ? -1 : 1, m = q.theta + (p * r), n = w.cx + (a.radius * Math.cos(m)), o = w.cy + (a.radius * Math.sin(m));
                return {x: n, y: o}
            }
        }, Bezier: function (a) {
            var n = this, d = jsPlumb.Segments.AbstractSegment.apply(this, arguments), b = [{
                x: a.x1,
                y: a.y1
            }, {x: a.cp1x, y: a.cp1y}, {x: a.cp2x, y: a.cp2y}, {
                x: a.x2,
                y: a.y2
            }], m = {
                minX: Math.min(a.x1, a.x2, a.cp1x, a.cp2x),
                minY: Math.min(a.y1, a.y2, a.cp1y, a.cp2y),
                maxX: Math.max(a.x1, a.x2, a.cp1x, a.cp2x),
                maxY: Math.max(a.y1, a.y2, a.cp1y, a.cp2y)
            };
            this.type = "Bezier";
            var c = function (p, k, l) {
                if (l) {
                    k = jsBezier.locationAlongCurveFrom(p, k > 0 ? 0 : 1, k)
                }
                return k
            };
            this.pointOnPath = function (k, l) {
                k = c(b, k, l);
                return jsBezier.pointOnCurve(b, k)
            };
            this.gradientAtPoint = function (k, l) {
                k = c(b, k, l);
                return jsBezier.gradientAtPoint(b, k)
            };
            this.pointAlongPathFrom = function (k, l, p) {
                k = c(b, k, p);
                return jsBezier.pointAlongCurveFrom(b, k, l)
            };
            this.getLength = function () {
                return jsBezier.getLength(b)
            };
            this.getBounds = function () {
                return m
            }
        }
    };
    var g = function () {
        var a = this;
        a.resetBounds = function () {
            a.bounds = {
                minX: Infinity,
                minY: Infinity,
                maxX: -Infinity,
                maxY: -Infinity
            }
        };
        a.resetBounds()
    };
    jsPlumb.Connectors.AbstractConnector = function (I) {
        g.apply(this, arguments);
        var c = this, L = [], U = false, K = 0, S = [], E = [], V = I.stub || 0, P = jsPlumbUtil.isArray(V) ? V[0] : V, G = jsPlumbUtil.isArray(V) ? V[1] : V, b = I.gap || 0, O = jsPlumbUtil.isArray(b) ? b[0] : b, M = jsPlumbUtil.isArray(b) ? b[1] : b, N = null, R = false, Q = null;
        this.isEditable = function () {
            return false
        };
        this.setEdited = function (k) {
            R = k
        };
        this.getPath = function () {
        };
        this.setPath = function (k) {
        };
        this.findSegmentForPoint = function (o, k) {
            var n = {d: Infinity, s: null, x: null, y: null, l: null};
            for (var m = 0; m < L.length; m++) {
                var l = L[m].findClosestPointOnPath(o, k);
                if (l.d < n.d) {
                    n.d = l.d;
                    n.l = l.l;
                    n.x = l.x;
                    n.y = l.y;
                    n.s = L[m]
                }
            }
            return n
        };
        var J = function () {
            var k = 0;
            for (var l = 0; l < L.length; l++) {
                var m = L[l].getLength();
                E[l] = m / K;
                S[l] = [k, (k += (m / K))]
            }
        }, T = function (m, k) {
            if (k) {
                m = m > 0 ? m / K : (K + m) / K
            }
            var o = S.length - 1, n = 1;
            for (var l = 0; l < S.length; l++) {
                if (S[l][1] >= m) {
                    o = l;
                    n = m == 1 ? 1 : m == 0 ? 0 : (m - S[l][0]) / E[l];
                    break
                }
            }
            return {segment: L[o], proportion: n, index: o}
        }, a = function (l, k) {
            var m = new jsPlumb.Segments[l](k);
            L.push(m);
            K += m.getLength();
            c.updateBounds(m)
        }, F = function () {
            K = 0;
            L.splice(0, L.length);
            S.splice(0, S.length);
            E.splice(0, E.length)
        };
        this.setSegments = function (k) {
            N = [];
            K = 0;
            for (var l = 0; l < k.length; l++) {
                N.push(k[l]);
                K += k[l].getLength()
            }
        };
        var H = function (m) {
            c.lineWidth = m.lineWidth;
            var X = jsPlumbUtil.segment(m.sourcePos, m.targetPos), q = m.targetPos[0] < m.sourcePos[0], s = m.targetPos[1] < m.sourcePos[1], B = m.lineWidth || 1, n = m.sourceEndpoint.anchor.orientation || m.sourceEndpoint.anchor.getOrientation(m.sourceEndpoint), D = m.targetEndpoint.anchor.orientation || m.targetEndpoint.anchor.getOrientation(m.targetEndpoint), x = q ? m.targetPos[0] : m.sourcePos[0], y = s ? m.targetPos[1] : m.sourcePos[1], v = Math.abs(m.targetPos[0] - m.sourcePos[0]), o = Math.abs(m.targetPos[1] - m.sourcePos[1]);
            if (n[0] == 0 && n[1] == 0 || D[0] == 0 && D[1] == 0) {
                var A = v > o ? 0 : 1, C = [1, 0][A];
                n = [];
                D = [];
                n[A] = m.sourcePos[A] > m.targetPos[A] ? -1 : 1;
                D[A] = m.sourcePos[A] > m.targetPos[A] ? 1 : -1;
                n[C] = 0;
                D[C] = 0
            }
            var r = q ? v + (O * n[0]) : O * n[0], u = s ? o + (O * n[1]) : O * n[1], k = q ? M * D[0] : v + (M * D[0]), l = s ? M * D[1] : o + (M * D[1]), p = ((n[0] * D[0]) + (n[1] * D[1]));
            var w = {
                sx: r,
                sy: u,
                tx: k,
                ty: l,
                lw: B,
                xSpan: Math.abs(k - r),
                ySpan: Math.abs(l - u),
                mx: (r + k) / 2,
                my: (u + l) / 2,
                so: n,
                to: D,
                x: x,
                y: y,
                w: v,
                h: o,
                segment: X,
                startStubX: r + (n[0] * P),
                startStubY: u + (n[1] * P),
                endStubX: k + (D[0] * G),
                endStubY: l + (D[1] * G),
                isXGreaterThanStubTimes2: Math.abs(r - k) > (P + G),
                isYGreaterThanStubTimes2: Math.abs(u - l) > (P + G),
                opposite: p == -1,
                perpendicular: p == 0,
                orthogonal: p == 1,
                sourceAxis: n[0] == 0 ? "y" : "x",
                points: [x, y, v, o, r, u, k, l]
            };
            w.anchorOrientation = w.opposite ? "opposite" : w.orthogonal ? "orthogonal" : "perpendicular";
            return w
        };
        this.getSegments = function () {
            return L
        };
        c.updateBounds = function (k) {
            var l = k.getBounds();
            c.bounds.minX = Math.min(c.bounds.minX, l.minX);
            c.bounds.maxX = Math.max(c.bounds.maxX, l.maxX);
            c.bounds.minY = Math.min(c.bounds.minY, l.minY);
            c.bounds.maxY = Math.max(c.bounds.maxY, l.maxY)
        };
        var d = function () {
            console.log("SEGMENTS:");
            for (var k = 0; k < L.length; k++) {
                console.log(L[k].type, L[k].getLength(), S[k])
            }
        };
        this.pointOnPath = function (l, k) {
            var m = T(l, k);
            return m.segment.pointOnPath(m.proportion, k)
        };
        this.gradientAtPoint = function (k) {
            var l = T(k, absolute);
            return l.segment.gradientAtPoint(l.proportion, absolute)
        };
        this.pointAlongPathFrom = function (m, k, l) {
            var n = T(m, l);
            return n.segment.pointAlongPathFrom(n.proportion, k, false)
        };
        this.compute = function (k) {
            if (!R) {
                Q = H(k)
            }
            F();
            this._compute(Q, k);
            c.x = Q.points[0];
            c.y = Q.points[1];
            c.w = Q.points[2];
            c.h = Q.points[3];
            c.segment = Q.segment;
            J()
        };
        return {
            addSegment: a,
            prepareCompute: H,
            sourceStub: P,
            targetStub: G,
            maxStub: Math.max(P, G),
            sourceGap: O,
            targetGap: M,
            maxGap: Math.max(O, M)
        }
    };
    jsPlumb.Connectors.Straight = function () {
        this.type = "Straight";
        var a = jsPlumb.Connectors.AbstractConnector.apply(this, arguments);
        this._compute = function (b, c) {
            a.addSegment("Straight", {
                x1: b.sx,
                y1: b.sy,
                x2: b.startStubX,
                y2: b.startStubY
            });
            a.addSegment("Straight", {
                x1: b.startStubX,
                y1: b.startStubY,
                x2: b.endStubX,
                y2: b.endStubY
            });
            a.addSegment("Straight", {
                x1: b.endStubX,
                y1: b.endStubY,
                x2: b.tx,
                y2: b.ty
            })
        }
    };
    jsPlumb.Connectors.Bezier = function (a) {
        a = a || {};
        var m = this, c = jsPlumb.Connectors.AbstractConnector.apply(this, arguments), b = a.stub || 50, n = a.curviness || 150, d = 10;
        this.type = "Bezier";
        this.getCurviness = function () {
            return n
        };
        this._findControlPoint = function (A, y, D, x, l) {
            var C = x.anchor.getOrientation(x), B = l.anchor.getOrientation(l), k = C[0] != B[0] || C[1] == B[1], p = [];
            if (!k) {
                if (C[0] == 0) {
                    p.push(y[0] < D[0] ? A[0] + d : A[0] - d)
                } else {
                    p.push(A[0] - (n * C[0]))
                }
                if (C[1] == 0) {
                    p.push(y[1] < D[1] ? A[1] + d : A[1] - d)
                } else {
                    p.push(A[1] + (n * B[1]))
                }
            } else {
                if (B[0] == 0) {
                    p.push(D[0] < y[0] ? A[0] + d : A[0] - d)
                } else {
                    p.push(A[0] + (n * B[0]))
                }
                if (B[1] == 0) {
                    p.push(D[1] < y[1] ? A[1] + d : A[1] - d)
                } else {
                    p.push(A[1] + (n * C[1]))
                }
            }
            return p
        };
        this._compute = function (k, B) {
            var l = B.sourcePos, F = B.targetPos, E = Math.abs(l[0] - F[0]), I = Math.abs(l[1] - F[1]), H = l[0] < F[0] ? E : 0, J = l[1] < F[1] ? I : 0, C = l[0] < F[0] ? 0 : E, D = l[1] < F[1] ? 0 : I, p = m._findControlPoint([H, J], l, F, B.sourceEndpoint, B.targetEndpoint), G = m._findControlPoint([C, D], F, l, B.targetEndpoint, B.sourceEndpoint);
            c.addSegment("Bezier", {
                x1: H,
                y1: J,
                x2: C,
                y2: D,
                cp1x: p[0],
                cp1y: p[1],
                cp2x: G[0],
                cp2y: G[1]
            })
        }
    };
    jsPlumb.Endpoints.AbstractEndpoint = function (a) {
        g.apply(this, arguments);
        var b = this;
        this.compute = function (d, p, c, n) {
            var o = b._compute.apply(b, arguments);
            b.x = o[0];
            b.y = o[1];
            b.w = o[2];
            b.h = o[3];
            b.bounds.minX = b.x;
            b.bounds.minY = b.y;
            b.bounds.maxX = b.x + b.w;
            b.bounds.maxY = b.y + b.h;
            return o
        };
        return {compute: b.compute, cssClass: a.cssClass}
    };
    jsPlumb.Endpoints.Dot = function (a) {
        this.type = "Dot";
        var c = this, b = jsPlumb.Endpoints.AbstractEndpoint.apply(this, arguments);
        a = a || {};
        this.radius = a.radius || 10;
        this.defaultOffset = 0.5 * this.radius;
        this.defaultInnerRadius = this.radius / 3;
        this._compute = function (d, B, A, x) {
            c.radius = A.radius || c.radius;
            var u = d[0] - c.radius, v = d[1] - c.radius, C = c.radius * 2, w = c.radius * 2;
            if (A.strokeStyle) {
                var y = A.lineWidth || 1;
                u -= y;
                v -= y;
                C += (y * 2);
                w += (y * 2)
            }
            return [u, v, C, w, c.radius]
        }
    };
    jsPlumb.Endpoints.Rectangle = function (a) {
        this.type = "Rectangle";
        var c = this, b = jsPlumb.Endpoints.AbstractEndpoint.apply(this, arguments);
        a = a || {};
        this.width = a.width || 20;
        this.height = a.height || 20;
        this._compute = function (x, r, v, y) {
            var d = v.width || c.width, s = v.height || c.height, u = x[0] - (d / 2), w = x[1] - (s / 2);
            return [u, w, d, s]
        }
    };
    var e = function (a) {
        jsPlumb.DOMElementComponent.apply(this, arguments);
        var c = this;
        var b = [];
        this.getDisplayElements = function () {
            return b
        };
        this.appendDisplayElement = function (d) {
            b.push(d)
        }
    };
    jsPlumb.Endpoints.Image = function (s) {
        this.type = "Image";
        e.apply(this, arguments);
        var a = this, b = jsPlumb.Endpoints.AbstractEndpoint.apply(this, arguments), u = false, v = false, w = s.width, x = s.height, d = null, y = s.endpoint;
        this.img = new Image();
        a.ready = false;
        this.img.onload = function () {
            a.ready = true;
            w = w || a.img.width;
            x = x || a.img.height;
            if (d) {
                d(a)
            }
        };
        y.setImage = function (m, k) {
            var l = m.constructor == String ? m : m.src;
            d = k;
            a.img.src = m;
            if (a.canvas != null) {
                a.canvas.setAttribute("src", m)
            }
        };
        y.setImage(s.src || s.url, s.onload);
        this._compute = function (l, n, k, m) {
            a.anchorPoint = l;
            if (a.ready) {
                return [l[0] - w / 2, l[1] - x / 2, w, x]
            } else {
                return [0, 0, 0, 0]
            }
        };
        a.canvas = document.createElement("img"), u = false;
        a.canvas.style.margin = 0;
        a.canvas.style.padding = 0;
        a.canvas.style.outline = 0;
        a.canvas.style.position = "absolute";
        var r = s.cssClass ? " " + s.cssClass : "";
        a.canvas.className = jsPlumb.endpointClass + r;
        if (w) {
            a.canvas.setAttribute("width", w)
        }
        if (x) {
            a.canvas.setAttribute("height", x)
        }
        jsPlumb.appendElement(a.canvas, s.parent);
        a.attachListeners(a.canvas, a);
        a.cleanup = function () {
            v = true
        };
        var c = function (l, m, n) {
            if (!v) {
                if (!u) {
                    a.canvas.setAttribute("src", a.img.src);
                    a.appendDisplayElement(a.canvas);
                    u = true
                }
                var o = a.anchorPoint[0] - (w / 2), k = a.anchorPoint[1] - (x / 2);
                jsPlumb.sizeCanvas(a.canvas, o, k, w, x)
            }
        };
        this.paint = function (k, l) {
            if (a.ready) {
                c(k, l)
            } else {
                window.setTimeout(function () {
                    a.paint(k, l)
                }, 200)
            }
        }
    };
    jsPlumb.Endpoints.Blank = function (a) {
        var c = this, b = jsPlumb.Endpoints.AbstractEndpoint.apply(this, arguments);
        this.type = "Blank";
        e.apply(this, arguments);
        this._compute = function (n, p, d, o) {
            return [n[0], n[1], 10, 0]
        };
        c.canvas = document.createElement("div");
        c.canvas.style.display = "block";
        c.canvas.style.width = "1px";
        c.canvas.style.height = "1px";
        c.canvas.style.background = "transparent";
        c.canvas.style.position = "absolute";
        c.canvas.className = c._jsPlumb.endpointClass;
        jsPlumb.appendElement(c.canvas, a.parent);
        this.paint = function (d, l) {
            jsPlumb.sizeCanvas(c.canvas, c.x, c.y, c.w, c.h)
        }
    };
    jsPlumb.Endpoints.Triangle = function (a) {
        this.type = "Triangle";
        var c = this, b = jsPlumb.Endpoints.AbstractEndpoint.apply(this, arguments);
        a = a || {};
        a.width = a.width || 55;
        a.height = a.height || 55;
        this.width = a.width;
        this.height = a.height;
        this._compute = function (x, r, v, y) {
            var d = v.width || c.width, s = v.height || c.height, u = x[0] - (d / 2), w = x[1] - (s / 2);
            return [u, w, d, s]
        }
    };
    var h = jsPlumb.Overlays.AbstractOverlay = function (a) {
        var b = true, c = this;
        this.isAppendedAtTopLevel = true;
        this.component = a.component;
        this.loc = a.location == null ? 0.5 : a.location;
        this.endpointLoc = a.endpointLocation == null ? [0.5, 0.5] : a.endpointLocation;
        this.setVisible = function (d) {
            b = d;
            c.component.repaint()
        };
        this.isVisible = function () {
            return b
        };
        this.hide = function () {
            c.setVisible(false)
        };
        this.show = function () {
            c.setVisible(true)
        };
        this.incrementLocation = function (d) {
            c.loc += d;
            c.component.repaint()
        };
        this.setLocation = function (d) {
            c.loc = d;
            c.component.repaint()
        };
        this.getLocation = function () {
            return c.loc
        }
    };
    jsPlumb.Overlays.Arrow = function (a) {
        this.type = "Arrow";
        h.apply(this, arguments);
        this.isAppendedAtTopLevel = false;
        a = a || {};
        var d = this, n = jsPlumbUtil;
        this.length = a.length || 20;
        this.width = a.width || 20;
        this.id = a.id;
        var b = (a.direction || 1) < 0 ? -1 : 1, c = a.paintStyle || {lineWidth: 1}, m = a.foldback || 0.623;
        this.computeMaxSize = function () {
            return d.width * 1.5
        };
        this.cleanup = function () {
        };
        this.draw = function (K, D) {
            var k, J, G, N, l;
            if (K.pointAlongPathFrom) {
                if (n.isString(d.loc) || d.loc > 1 || d.loc < 0) {
                    var F = parseInt(d.loc);
                    k = K.pointAlongPathFrom(F, b * d.length / 2, true), J = K.pointOnPath(F, true), G = n.pointOnLine(k, J, d.length)
                } else {
                    if (d.loc == 1) {
                        k = K.pointOnPath(d.loc);
                        J = K.pointAlongPathFrom(d.loc, -(d.length));
                        G = n.pointOnLine(k, J, d.length);
                        if (b == -1) {
                            var E = G;
                            G = k;
                            k = E
                        }
                    } else {
                        if (d.loc == 0) {
                            G = K.pointOnPath(d.loc);
                            J = K.pointAlongPathFrom(d.loc, d.length);
                            k = n.pointOnLine(G, J, d.length);
                            if (b == -1) {
                                var E = G;
                                G = k;
                                k = E
                            }
                        } else {
                            k = K.pointAlongPathFrom(d.loc, b * d.length / 2), J = K.pointOnPath(d.loc), G = n.pointOnLine(k, J, d.length)
                        }
                    }
                }
                N = n.perpendicularLineTo(k, G, d.width);
                l = n.pointOnLine(k, G, m * d.length);
                var M = {
                    hxy: k,
                    tail: N,
                    cxy: l
                }, L = c.strokeStyle || D.strokeStyle, I = c.fillStyle || D.strokeStyle, C = c.lineWidth || D.lineWidth, H = {
                    component: K,
                    d: M,
                    lineWidth: C,
                    strokeStyle: L,
                    fillStyle: I,
                    minX: Math.min(k.x, N[0].x, N[1].x),
                    maxX: Math.max(k.x, N[0].x, N[1].x),
                    minY: Math.min(k.y, N[0].y, N[1].y),
                    maxY: Math.max(k.y, N[0].y, N[1].y)
                };
                return H
            } else {
                return {component: K, minX: 0, maxX: 0, minY: 0, maxY: 0}
            }
        }
    };
    jsPlumb.Overlays.PlainArrow = function (a) {
        a = a || {};
        var b = jsPlumb.extend(a, {foldback: 1});
        jsPlumb.Overlays.Arrow.call(this, b);
        this.type = "PlainArrow"
    };
    jsPlumb.Overlays.Diamond = function (a) {
        a = a || {};
        var c = a.length || 40, b = jsPlumb.extend(a, {
            length: c / 2,
            foldback: 2
        });
        jsPlumb.Overlays.Arrow.call(this, b);
        this.type = "Diamond"
    };
    var f = function (s) {
        jsPlumb.DOMElementComponent.apply(this, arguments);
        h.apply(this, arguments);
        var b = this, p = false, q = jsPlumb.CurrentLibrary;
        s = s || {};
        this.id = s.id;
        var u;
        var r = function () {
            u = s.create(s.component);
            u = q.getDOMElement(u);
            u.style.position = "absolute";
            var k = s._jsPlumb.overlayClass + " " + (b.cssClass ? b.cssClass : s.cssClass ? s.cssClass : "");
            u.className = k;
            s._jsPlumb.appendElement(u, s.component.parent);
            s._jsPlumb.getId(u);
            b.attachListeners(u, b);
            b.canvas = u
        };
        this.getElement = function () {
            if (u == null) {
                r()
            }
            return u
        };
        this.getDimensions = function () {
            return q.getSize(q.getElementObject(b.getElement()))
        };
        var d = null, c = function (k) {
            if (d == null) {
                d = b.getDimensions()
            }
            return d
        };
        this.clearCachedDimensions = function () {
            d = null
        };
        this.computeMaxSize = function () {
            var k = c();
            return Math.max(k[0], k[1])
        };
        var a = b.setVisible;
        b.setVisible = function (k) {
            a(k);
            u.style.display = k ? "block" : "none"
        };
        this.cleanup = function () {
            if (u != null) {
                q.removeElement(u)
            }
        };
        this.paint = function (k, l) {
            if (!p) {
                b.getElement();
                k.component.appendDisplayElement(u);
                b.attachListeners(u, k.component);
                p = true
            }
            u.style.left = (k.component.x + k.d.minx) + "px";
            u.style.top = (k.component.y + k.d.miny) + "px"
        };
        this.draw = function (D, n) {
            var m = c();
            if (m != null && m.length == 2) {
                var l = {x: 0, y: 0};
                if (D.pointOnPath) {
                    var k = b.loc, C = false;
                    if (jsPlumbUtil.isString(b.loc) || b.loc < 0 || b.loc > 1) {
                        k = parseInt(b.loc);
                        C = true
                    }
                    l = D.pointOnPath(k, C)
                } else {
                    var A = b.loc.constructor == Array ? b.loc : b.endpointLoc;
                    l = {x: A[0] * D.w, y: A[1] * D.h}
                }
                var o = l.x - (m[0] / 2), B = l.y - (m[1] / 2);
                return {
                    component: D,
                    d: {minx: o, miny: B, td: m, cxy: l},
                    minX: o,
                    maxX: o + m[0],
                    minY: B,
                    maxY: B + m[1]
                }
            } else {
                return {minX: 0, maxX: 0, minY: 0, maxY: 0}
            }
        };
        this.reattachListeners = function (k) {
            if (u) {
                b.reattachListenersForElement(u, b, k)
            }
        }
    };
    jsPlumb.Overlays.Custom = function (a) {
        this.type = "Custom";
        f.apply(this, arguments)
    };
    jsPlumb.Overlays.GuideLines = function () {
        var a = this;
        a.length = 50;
        a.lineWidth = 5;
        this.type = "GuideLines";
        h.apply(this, arguments);
        jsPlumb.jsPlumbUIComponent.apply(this, arguments);
        this.draw = function (p, r) {
            var b = p.pointAlongPathFrom(a.loc, a.length / 2), c = p.pointOnPath(a.loc), d = jsPlumbUtil.pointOnLine(b, c, a.length), o = jsPlumbUtil.perpendicularLineTo(b, d, 40), q = jsPlumbUtil.perpendicularLineTo(d, b, 20);
            return {
                connector: p,
                head: b,
                tail: d,
                headLine: q,
                tailLine: o,
                minX: Math.min(b.x, d.x, q[0].x, q[1].x),
                minY: Math.min(b.y, d.y, q[0].y, q[1].y),
                maxX: Math.max(b.x, d.x, q[0].x, q[1].x),
                maxY: Math.max(b.y, d.y, q[0].y, q[1].y)
            }
        };
        this.cleanup = function () {
        }
    };
    jsPlumb.Overlays.Label = function (b) {
        var n = this;
        this.labelStyle = b.labelStyle || jsPlumb.Defaults.LabelStyle;
        this.cssClass = this.labelStyle != null ? this.labelStyle.cssClass : null;
        b.create = function () {
            return document.createElement("div")
        };
        jsPlumb.Overlays.Custom.apply(this, arguments);
        this.type = "Label";
        var d = b.label || "", n = this, c = null;
        this.setLabel = function (k) {
            d = k;
            c = null;
            n.clearCachedDimensions();
            m();
            n.component.repaint()
        };
        var m = function () {
            if (typeof d == "function") {
                var k = d(n);
                n.getElement().innerHTML = k.replace(/\r\n/g, "<br/>")
            } else {
                if (c == null) {
                    c = d;
                    n.getElement().innerHTML = c.replace(/\r\n/g, "<br/>")
                }
            }
        };
        this.getLabel = function () {
            return d
        };
        var a = this.getDimensions;
        this.getDimensions = function () {
            m();
            return a()
        }
    }
})();
(function () {
    var f = function (c, a, h, b) {
        this.m = (b - a) / (h - c);
        this.b = -1 * ((this.m * c) - a);
        this.rectIntersect = function (g, w, F, x) {
            var y = [];
            var C = (w - this.b) / this.m;
            if (C >= g && C <= (g + F)) {
                y.push([C, (this.m * C) + this.b])
            }
            var E = (this.m * (g + F)) + this.b;
            if (E >= w && E <= (w + x)) {
                y.push([(E - this.b) / this.m, E])
            }
            var C = ((w + x) - this.b) / this.m;
            if (C >= g && C <= (g + F)) {
                y.push([C, (this.m * C) + this.b])
            }
            var E = (this.m * g) + this.b;
            if (E >= w && E <= (w + x)) {
                y.push([(E - this.b) / this.m, E])
            }
            if (y.length == 2) {
                var A = (y[0][0] + y[1][0]) / 2, B = (y[0][1] + y[1][1]) / 2;
                y.push([A, B]);
                var D = A <= g + (F / 2) ? -1 : 1, G = B <= w + (x / 2) ? -1 : 1;
                y.push([D, G]);
                return y
            }
            return null
        }
    }, e = function (c, a, h, b) {
        if (c <= h && b <= a) {
            return 1
        } else {
            if (c <= h && a <= b) {
                return 2
            } else {
                if (h <= c && b >= a) {
                    return 3
                }
            }
        }
        return 4
    }, d = function (q, r, o, s, p, a, b, u, c) {
        if (u <= c) {
            return [q, r]
        }
        if (o === 1) {
            if (s[3] <= 0 && p[3] >= 1) {
                return [q + (s[2] < 0.5 ? -1 * a : a), r]
            } else {
                if (s[2] >= 1 && p[2] <= 0) {
                    return [q, r + (s[3] < 0.5 ? -1 * b : b)]
                } else {
                    return [q + (-1 * a), r + (-1 * b)]
                }
            }
        } else {
            if (o === 2) {
                if (s[3] >= 1 && p[3] <= 0) {
                    return [q + (s[2] < 0.5 ? -1 * a : a), r]
                } else {
                    if (s[2] >= 1 && p[2] <= 0) {
                        return [q, r + (s[3] < 0.5 ? -1 * b : b)]
                    } else {
                        return [q + (1 * a), r + (-1 * b)]
                    }
                }
            } else {
                if (o === 3) {
                    if (s[3] >= 1 && p[3] <= 0) {
                        return [q + (s[2] < 0.5 ? -1 * a : a), r]
                    } else {
                        if (s[2] <= 0 && p[2] >= 1) {
                            return [q, r + (s[3] < 0.5 ? -1 * b : b)]
                        } else {
                            return [q + (-1 * a), r + (-1 * b)]
                        }
                    }
                } else {
                    if (o === 4) {
                        if (s[3] <= 0 && p[3] >= 1) {
                            return [q + (s[2] < 0.5 ? -1 * a : a), r]
                        } else {
                            if (s[2] <= 0 && p[2] >= 1) {
                                return [q, r + (s[3] < 0.5 ? -1 * b : b)]
                            } else {
                                return [q + (1 * a), r + (-1 * b)]
                            }
                        }
                    }
                }
            }
        }
    };
    jsPlumb.Connectors.StateMachine = function (q) {
        q = q || {};
        this.type = "StateMachine";
        var a = this, c = jsPlumb.Connectors.AbstractConnector.apply(this, arguments), u = q.curviness || 10, p = q.margin || 5, o = q.proximityLimit || 80, s = q.orientation && q.orientation === "clockwise", r = q.loopbackRadius || 25, b = q.showLoopback !== false;
        this._compute = function (ae, g) {
            var y = Math.abs(g.sourcePos[0] - g.targetPos[0]), k = Math.abs(g.sourcePos[1] - g.targetPos[1]), T = Math.min(g.sourcePos[0], g.targetPos[0]), X = Math.min(g.sourcePos[1], g.targetPos[1]);
            if (!b || (g.sourceEndpoint.elementId !== g.targetEndpoint.elementId)) {
                var af = g.sourcePos[0] < g.targetPos[0] ? 0 : y, Z = g.sourcePos[1] < g.targetPos[1] ? 0 : k, n = g.sourcePos[0] < g.targetPos[0] ? y : 0, x = g.sourcePos[1] < g.targetPos[1] ? k : 0;
                if (g.sourcePos[2] === 0) {
                    af -= p
                }
                if (g.sourcePos[2] === 1) {
                    af += p
                }
                if (g.sourcePos[3] === 0) {
                    Z -= p
                }
                if (g.sourcePos[3] === 1) {
                    Z += p
                }
                if (g.targetPos[2] === 0) {
                    n -= p
                }
                if (g.targetPos[2] === 1) {
                    n += p
                }
                if (g.targetPos[3] === 0) {
                    x -= p
                }
                if (g.targetPos[3] === 1) {
                    x += p
                }
                var V = (af + n) / 2, aa = (Z + x) / 2, ah = (-1 * V) / aa, m = Math.atan(ah), S = (ah == Infinity || ah == -Infinity) ? 0 : Math.abs(u / 2 * Math.sin(m)), w = (ah == Infinity || ah == -Infinity) ? 0 : Math.abs(u / 2 * Math.cos(m)), ag = e(af, Z, n, x), ac = Math.sqrt(Math.pow(n - af, 2) + Math.pow(x - Z, 2)), Y = d(V, aa, ag, g.sourcePos, g.targetPos, u, u, ac, o);
                c.addSegment("Bezier", {
                    x1: n,
                    y1: x,
                    x2: af,
                    y2: Z,
                    cp1x: Y[0],
                    cp1y: Y[1],
                    cp2x: Y[0],
                    cp2y: Y[1]
                })
            } else {
                var h = g.sourcePos[0], l = g.sourcePos[0], U = g.sourcePos[1] - p, ab = g.sourcePos[1] - p, W = h, ad = U - r;
                y = 2 * r, k = 2 * r, T = W - r, X = ad - r;
                ae.points[0] = T;
                ae.points[1] = X;
                ae.points[2] = y;
                ae.points[3] = k;
                c.addSegment("Arc", {
                    x1: (h - T) + 4,
                    y1: U - X,
                    startAngle: 0,
                    endAngle: 2 * Math.PI,
                    r: r,
                    ac: !s,
                    x2: (h - T) - 4,
                    y2: U - X,
                    cx: W - T,
                    cy: ad - X
                })
            }
        }
    }
})();
(function () {
    jsPlumb.Connectors.Flowchart = function (params) {
        this.type = "Flowchart";
        params = params || {};
        params.stub = params.stub || 30;
        var self = this, _super = jsPlumb.Connectors.AbstractConnector.apply(this, arguments), midpoint = params.midpoint || 0.5, points = [], segments = [], grid = params.grid, alwaysRespectStubs = params.alwaysRespectStubs, userSuppliedSegments = null, lastx = null, lasty = null, lastOrientation, cornerRadius = params.cornerRadius != null ? params.cornerRadius : 0, sgn = function (n) {
            return n < 0 ? -1 : n == 0 ? 0 : 1
        }, addSegment = function (segments, x, y, paintInfo) {
            if (lastx == x && lasty == y) {
                return
            }
            var lx = lastx == null ? paintInfo.sx : lastx, ly = lasty == null ? paintInfo.sy : lasty, o = lx == x ? "v" : "h", sgnx = sgn(x - lx), sgny = sgn(y - ly);
            lastx = x;
            lasty = y;
            segments.push([lx, ly, x, y, o, sgnx, sgny])
        }, segLength = function (s) {
            return Math.sqrt(Math.pow(s[0] - s[2], 2) + Math.pow(s[1] - s[3], 2))
        }, _cloneArray = function (a) {
            var _a = [];
            _a.push.apply(_a, a);
            return _a
        }, updateMinMax = function (a1) {
            self.bounds.minX = Math.min(self.bounds.minX, a1[2]);
            self.bounds.maxX = Math.max(self.bounds.maxX, a1[2]);
            self.bounds.minY = Math.min(self.bounds.minY, a1[3]);
            self.bounds.maxY = Math.max(self.bounds.maxY, a1[3])
        }, writeSegments = function (segments, paintInfo) {
            var current, next;
            for (var i = 0; i < segments.length - 1; i++) {
                current = current || _cloneArray(segments[i]);
                next = _cloneArray(segments[i + 1]);
                if (cornerRadius > 0 && current[4] != next[4]) {
                    var radiusToUse = Math.min(cornerRadius, segLength(current), segLength(next));
                    current[2] -= current[5] * radiusToUse;
                    current[3] -= current[6] * radiusToUse;
                    next[0] += next[5] * radiusToUse;
                    next[1] += next[6] * radiusToUse;
                    var ac = (current[6] == next[5] && next[5] == 1) || ((current[6] == next[5] && next[5] == 0) && current[5] != next[6]) || (current[6] == next[5] && next[5] == -1), sgny = next[1] > current[3] ? 1 : -1, sgnx = next[0] > current[2] ? 1 : -1, sgnEqual = sgny == sgnx, cx = (sgnEqual && ac || (!sgnEqual && !ac)) ? next[0] : current[2], cy = (sgnEqual && ac || (!sgnEqual && !ac)) ? current[3] : next[1];
                    _super.addSegment("Straight", {
                        x1: current[0],
                        y1: current[1],
                        x2: current[2],
                        y2: current[3]
                    });
                    _super.addSegment("Arc", {
                        r: radiusToUse,
                        x1: current[2],
                        y1: current[3],
                        x2: next[0],
                        y2: next[1],
                        cx: cx,
                        cy: cy,
                        ac: ac
                    })
                } else {
                    var dx = (current[2] == current[0]) ? 0 : (current[2] > current[0]) ? (paintInfo.lw / 2) : -(paintInfo.lw / 2), dy = (current[3] == current[1]) ? 0 : (current[3] > current[1]) ? (paintInfo.lw / 2) : -(paintInfo.lw / 2);
                    _super.addSegment("Straight", {
                        x1: current[0] - dx,
                        y1: current[1] - dy,
                        x2: current[2] + dx,
                        y2: current[3] + dy
                    })
                }
                current = next
            }
            _super.addSegment("Straight", {
                x1: next[0],
                y1: next[1],
                x2: next[2],
                y2: next[3]
            })
        };
        this.setSegments = function (s) {
            userSuppliedSegments = s
        };
        this.isEditable = function () {
            return true
        };
        this.getOriginalSegments = function () {
            return userSuppliedSegments || segments
        };
        this._compute = function (paintInfo, params) {
            if (params.clearEdits) {
                userSuppliedSegments = null
            }
            if (userSuppliedSegments != null) {
                writeSegments(userSuppliedSegments, paintInfo);
                return
            }
            segments = [];
            lastx = null;
            lasty = null;
            lastOrientation = null;
            var midx = paintInfo.startStubX + ((paintInfo.endStubX - paintInfo.startStubX) * midpoint), midy = paintInfo.startStubY + ((paintInfo.endStubY - paintInfo.startStubY) * midpoint);
            var findClearedLine = function (start, mult, anchorPos, dimension) {
                return start + (mult * ((1 - anchorPos) * dimension) + _super.maxStub)
            }, orientations = {
                x: [0, 1],
                y: [1, 0]
            }, commonStubCalculator = function (axis) {
                return [paintInfo.startStubX, paintInfo.startStubY, paintInfo.endStubX, paintInfo.endStubY]
            }, stubCalculators = {
                perpendicular: commonStubCalculator,
                orthogonal: commonStubCalculator,
                opposite: function (axis) {
                    var pi = paintInfo, idx = axis == "x" ? 0 : 1, areInProximity = {
                        x: function () {
                            return ((pi.so[idx] == 1 && (((pi.startStubX > pi.endStubX) && (pi.tx > pi.startStubX)) || ((pi.sx > pi.endStubX) && (pi.tx > pi.sx))))) || ((pi.so[idx] == -1 && (((pi.startStubX < pi.endStubX) && (pi.tx < pi.startStubX)) || ((pi.sx < pi.endStubX) && (pi.tx < pi.sx)))))
                        }, y: function () {
                            return ((pi.so[idx] == 1 && (((pi.startStubY > pi.endStubY) && (pi.ty > pi.startStubY)) || ((pi.sy > pi.endStubY) && (pi.ty > pi.sy))))) || ((pi.so[idx] == -1 && (((pi.startStubY < pi.endStubY) && (pi.ty < pi.startStubY)) || ((pi.sy < pi.endStubY) && (pi.ty < pi.sy)))))
                        }
                    };
                    if (!alwaysRespectStubs && areInProximity[axis]()) {
                        return {
                            x: [(paintInfo.sx + paintInfo.tx) / 2, paintInfo.startStubY, (paintInfo.sx + paintInfo.tx) / 2, paintInfo.endStubY],
                            y: [paintInfo.startStubX, (paintInfo.sy + paintInfo.ty) / 2, paintInfo.endStubX, (paintInfo.sy + paintInfo.ty) / 2]
                        }[axis]
                    } else {
                        return [paintInfo.startStubX, paintInfo.startStubY, paintInfo.endStubX, paintInfo.endStubY]
                    }
                }
            }, lineCalculators = {
                perpendicular: function (axis, ss, oss, es, oes) {
                    var sis = {
                            x: [[[1, 2, 3, 4], null, [2, 1, 4, 3]], null, [[4, 3, 2, 1], null, [3, 4, 1, 2]]],
                            y: [[[3, 2, 1, 4], null, [2, 3, 4, 1]], null, [[4, 1, 2, 3], null, [1, 4, 3, 2]]]
                        },
                        stubs = {
                            x: [[paintInfo.startStubX, paintInfo.endStubX], null, [paintInfo.endStubX, paintInfo.startStubX]],
                            y: [[paintInfo.startStubY, paintInfo.endStubY], null, [paintInfo.endStubY, paintInfo.startStubY]]
                        },
                        midLines = {
                            x: [[paintInfo.midx, paintInfo.startStubY], [paintInfo.midx, paintInfo.endStubY]],
                            y: [[paintInfo.startStubX, paintInfo.midy], [paintInfo.endStubX, paintInfo.midy]]
                        },
                        linesToEnd = {
                            x: [[paintInfo.endStubX, paintInfo.startStubY]],
                            y: [[paintInfo.startStubX, paintInfo.endStubY]]
                        },
                        startToEnd = {
                            x: [[paintInfo.startStubX, paintInfo.endStubY], [paintInfo.endStubX, paintInfo.endStubY]],
                            y: [[paintInfo.endStubX, paintInfo.startStubY], [paintInfo.endStubX, paintInfo.endStubY]]
                        },
                        startToMidToEnd = {
                            x: [[paintInfo.startStubX, paintInfo.midy], [paintInfo.endStubX, paintInfo.midy], [paintInfo.endStubX, paintInfo.endStubY]],
                            y: [[paintInfo.midx, paintInfo.startStubY], [paintInfo.midx, paintInfo.endStubY], [paintInfo.endStubX, paintInfo.endStubY]]
                        },
                        otherStubs = {
                            x: [paintInfo.startStubY, paintInfo.endStubY],
                            y: [paintInfo.startStubX, paintInfo.endStubX]
                        },
                        orientations = paintInfo.orientations,
                        so = paintInfo.so,
                        to = paintInfo.to,
                        soIdx = orientations[axis][0],
                        toIdx = orientations[axis][1],
                        _so = so[soIdx] + 1,
                        _to = to[toIdx] + 1,
                        otherFlipped = (to[toIdx] == -1 && (otherStubs[axis][1] < otherStubs[axis][0])) || (to[toIdx] == 1 && (otherStubs[axis][1] > otherStubs[axis][0])),
                        stub1 = stubs[axis][_so][0],
                        stub2 = stubs[axis][_so][1],
                        segmentIndexes = sis[axis][_so][_to];
                },
                orthogonal: function (axis, startStub, otherStartStub, endStub, otherEndStub) {
                    var pi = paintInfo, extent = {
                        x: pi.so[0] == -1 ? Math.min(startStub, endStub) : Math.max(startStub, endStub),
                        y: pi.so[1] == -1 ? Math.min(startStub, endStub) : Math.max(startStub, endStub)
                    }[axis];
                    return {
                        x: [[extent, otherStartStub], [extent, otherEndStub], [endStub, otherEndStub]],
                        y: [[otherStartStub, extent], [otherEndStub, extent], [otherEndStub, endStub]]
                    }[axis]
                },
                opposite: function (axis, ss, oss, es, oes) {
                    var pi = paintInfo, otherAxis = {
                        x: "y",
                        y: "x"
                    }[axis], dim = {
                        x: "height",
                        y: "width"
                    }[axis], comparator = pi["is" + axis.toUpperCase() + "GreaterThanStubTimes2"];
                    if (params.sourceEndpoint.elementId == params.targetEndpoint.elementId) {
                        var _val = oss + ((1 - params.sourceEndpoint.anchor[otherAxis]) * params.sourceInfo[dim]) + _super.maxStub;
                        return {
                            x: [[ss, _val], [es, _val]],
                            y: [[_val, ss], [_val, es]]
                        }[axis]
                    } else {
                        if (!comparator || (pi.so[idx] == 1 && ss > es) || (pi.so[idx] == -1 && ss < es)) {
                            return {
                                x: [[ss, midy], [es, midy]],
                                y: [[midx, ss], [midx, es]]
                            }[axis]
                        } else {
                            if ((pi.so[idx] == 1 && ss < es) || (pi.so[idx] == -1 && ss > es)) {
                                return {
                                    x: [[midx, pi.sy], [midx, pi.ty]],
                                    y: [[pi.sx, midy], [pi.tx, midy]]
                                }[axis]
                            }
                        }
                    }
                }
            };
            var stubs = stubCalculators[paintInfo.anchorOrientation](paintInfo.sourceAxis), idx = paintInfo.sourceAxis == "x" ? 0 : 1, oidx = paintInfo.sourceAxis == "x" ? 1 : 0, ss = stubs[idx], oss = stubs[oidx], es = stubs[idx + 2], oes = stubs[oidx + 2];
            addSegment(segments, stubs[0], stubs[1], paintInfo);
            var p = lineCalculators[paintInfo.anchorOrientation](paintInfo.sourceAxis, ss, oss, es, oes);
            if (p) {
                for (var i = 0; i < p.length; i++) {
                    addSegment(segments, p[i][0], p[i][1], paintInfo)
                }
            }
            addSegment(segments, stubs[2], stubs[3], paintInfo);
            addSegment(segments, paintInfo.tx, paintInfo.ty, paintInfo);
            writeSegments(segments, paintInfo)
        };
        this.getPath = function () {
            var _last = null, _lastAxis = null, s = [], segs = userSuppliedSegments || segments;
            for (var i = 0; i < segs.length; i++) {
                var seg = segs[i], axis = seg[4], axisIndex = (axis == "v" ? 3 : 2);
                if (_last != null && _lastAxis === axis) {
                    _last[axisIndex] = seg[axisIndex]
                } else {
                    if (seg[0] != seg[2] || seg[1] != seg[3]) {
                        s.push({
                            start: [seg[0], seg[1]],
                            end: [seg[2], seg[3]]
                        });
                        _last = seg;
                        _lastAxis = seg[4]
                    }
                }
            }
            return s
        };
        this.setPath = function (path) {
            userSuppliedSegments = [];
            for (var i = 0; i < path.length; i++) {
                var lx = path[i].start[0], ly = path[i].start[1], x = path[i].end[0], y = path[i].end[1], o = lx == x ? "v" : "h", sgnx = sgn(x - lx), sgny = sgn(y - ly);
                userSuppliedSegments.push([lx, ly, x, y, o, sgnx, sgny])
            }
        }
    }
})();
(function () {
    var G = {
        "stroke-linejoin": "joinstyle",
        joinstyle: "joinstyle",
        endcap: "endcap",
        miterlimit: "miterlimit"
    }, L = null;
    if (document.createStyleSheet && document.namespaces) {
        var D = [".jsplumb_vml", "jsplumb\\:textbox", "jsplumb\\:oval", "jsplumb\\:rect", "jsplumb\\:stroke", "jsplumb\\:shape", "jsplumb\\:group"], H = "behavior:url(#default#VML);position:absolute;";
        L = document.createStyleSheet();
        for (var x = 0; x < D.length; x++) {
            L.addRule(D[x], H)
        }
        document.namespaces.add("jsplumb", "urn:schemas-microsoft-com:vml")
    }
    jsPlumb.vml = {};
    var v = 1000, w = {}, N = function (b, c) {
        var d = jsPlumb.getId(b), a = w[d];
        if (!a) {
            a = I("group", [0, 0, v, v], {"class": c});
            a.style.backgroundColor = "red";
            w[d] = a;
            jsPlumb.appendElement(a, b)
        }
        return a
    }, J = function (b, a) {
        for (var c in a) {
            b[c] = a[c]
        }
    }, I = function (c, g, e, f, d, b) {
        e = e || {};
        var a = document.createElement("jsplumb:" + c);
        if (b) {
            d.appendElement(a, f)
        } else {
            jsPlumb.CurrentLibrary.appendElement(a, f)
        }
        a.className = (e["class"] ? e["class"] + " " : "") + "jsplumb_vml";
        F(a, g);
        J(a, e);
        return a
    }, F = function (b, c, a) {
        b.style.left = c[0] + "px";
        b.style.top = c[1] + "px";
        b.style.width = c[2] + "px";
        b.style.height = c[3] + "px";
        b.style.position = "absolute";
        if (a) {
            b.style.zIndex = a
        }
    }, A = jsPlumb.vml.convertValue = function (a) {
        return Math.floor(a * v)
    }, M = function (d, b, a, c) {
        if ("transparent" === b) {
            c.setOpacity(a, "0.0")
        } else {
            c.setOpacity(a, "1.0")
        }
    }, y = function (d, h, a, m) {
        var e = {};
        if (h.strokeStyle) {
            e.stroked = "true";
            var l = jsPlumbUtil.convertStyle(h.strokeStyle, true);
            e.strokecolor = l;
            M(e, l, "stroke", a);
            e.strokeweight = h.lineWidth + "px"
        } else {
            e.stroked = "false"
        }
        if (h.fillStyle) {
            e.filled = "true";
            var g = jsPlumbUtil.convertStyle(h.fillStyle, true);
            e.fillcolor = g;
            M(e, g, "fill", a)
        } else {
            e.filled = "false"
        }
        if (h.dashstyle) {
            if (a.strokeNode == null) {
                a.strokeNode = I("stroke", [0, 0, 0, 0], {dashstyle: h.dashstyle}, d, m)
            } else {
                a.strokeNode.dashstyle = h.dashstyle
            }
        } else {
            if (h["stroke-dasharray"] && h.lineWidth) {
                var k = h["stroke-dasharray"].indexOf(",") == -1 ? " " : ",", c = h["stroke-dasharray"].split(k), f = "";
                for (var b = 0; b < c.length; b++) {
                    f += (Math.floor(c[b] / h.lineWidth) + k)
                }
                if (a.strokeNode == null) {
                    a.strokeNode = I("stroke", [0, 0, 0, 0], {dashstyle: f}, d, m)
                } else {
                    a.strokeNode.dashstyle = f
                }
            }
        }
        J(d, e)
    }, C = function () {
        var c = this, a = {};
        jsPlumb.jsPlumbUIComponent.apply(this, arguments);
        this.opacityNodes = {stroke: null, fill: null};
        this.initOpacityNodes = function (d) {
            c.opacityNodes.stroke = I("stroke", [0, 0, 1, 1], {opacity: "0.0"}, d, c._jsPlumb);
            c.opacityNodes.fill = I("fill", [0, 0, 1, 1], {opacity: "0.0"}, d, c._jsPlumb)
        };
        this.setOpacity = function (f, e) {
            var d = c.opacityNodes[f];
            if (d) {
                d.opacity = "" + e
            }
        };
        var b = [];
        this.getDisplayElements = function () {
            return b
        };
        this.appendDisplayElement = function (d, e) {
            if (!e) {
                c.canvas.parentNode.appendChild(d)
            }
            b.push(d)
        }
    }, K = jsPlumb.ConnectorRenderers.vml = function (d) {
        var c = this;
        c.strokeNode = null;
        c.canvas = null;
        var a = C.apply(this, arguments);
        var b = c._jsPlumb.connectorClass + (d.cssClass ? (" " + d.cssClass) : "");
        this.paint = function (g) {
            if (g !== null) {
                var o = c.getSegments(), f = {path: ""}, n = [c.x, c.y, c.w, c.h];
                for (var e = 0; e < o.length; e++) {
                    f.path += jsPlumb.Segments.vml.SegmentRenderer.getPath(o[e]);
                    f.path += " "
                }
                if (g.outlineColor) {
                    var l = g.outlineWidth || 1, k = g.lineWidth + (2 * l), m = {
                        strokeStyle: jsPlumbUtil.convertStyle(g.outlineColor),
                        lineWidth: k
                    };
                    for (var h in G) {
                        m[h] = g[h]
                    }
                    if (c.bgCanvas == null) {
                        f["class"] = b;
                        f.coordsize = (n[2] * v) + "," + (n[3] * v);
                        c.bgCanvas = I("shape", n, f, d.parent, c._jsPlumb, true);
                        F(c.bgCanvas, n);
                        c.appendDisplayElement(c.bgCanvas, true);
                        c.attachListeners(c.bgCanvas, c);
                        c.initOpacityNodes(c.bgCanvas, ["stroke"])
                    } else {
                        f.coordsize = (n[2] * v) + "," + (n[3] * v);
                        F(c.bgCanvas, n);
                        J(c.bgCanvas, f)
                    }
                    y(c.bgCanvas, m, c)
                }
                if (c.canvas == null) {
                    f["class"] = b;
                    f.coordsize = (n[2] * v) + "," + (n[3] * v);
                    c.canvas = I("shape", n, f, d.parent, c._jsPlumb, true);
                    c.appendDisplayElement(c.canvas, true);
                    c.attachListeners(c.canvas, c);
                    c.initOpacityNodes(c.canvas, ["stroke"])
                } else {
                    f.coordsize = (n[2] * v) + "," + (n[3] * v);
                    F(c.canvas, n);
                    J(c.canvas, f)
                }
                y(c.canvas, g, c, c._jsPlumb)
            }
        };
        this.reattachListeners = function () {
            if (c.canvas) {
                c.reattachListenersForElement(c.canvas, c)
            }
        }
    }, E = window.VmlEndpoint = function (f) {
        C.apply(this, arguments);
        var d = null, b = this, c = null, a = null;
        b.canvas = document.createElement("div");
        b.canvas.style.position = "absolute";
        var e = b._jsPlumb.endpointClass + (f.cssClass ? (" " + f.cssClass) : "");
        f._jsPlumb.appendElement(b.canvas, f.parent);
        this.paint = function (h, k) {
            var g = {};
            jsPlumb.sizeCanvas(b.canvas, b.x, b.y, b.w, b.h);
            if (d == null) {
                g["class"] = e;
                d = b.getVml([0, 0, b.w, b.h], g, k, b.canvas, b._jsPlumb);
                b.attachListeners(d, b);
                b.appendDisplayElement(d, true);
                b.appendDisplayElement(b.canvas, true);
                b.initOpacityNodes(d, ["fill"])
            } else {
                F(d, [0, 0, b.w, b.h]);
                J(d, g)
            }
            y(d, h, b)
        };
        this.reattachListeners = function () {
            if (d) {
                b.reattachListenersForElement(d, b)
            }
        }
    };
    jsPlumb.Segments.vml = {
        SegmentRenderer: {
            getPath: function (a) {
                return ({
                    Straight: function (c) {
                        var b = c.params;
                        return "m" + A(b.x1) + "," + A(b.y1) + " l" + A(b.x2) + "," + A(b.y2) + " e"
                    }, Bezier: function (c) {
                        var b = c.params;
                        return "m" + A(b.x1) + "," + A(b.y1) + " c" + A(b.cp1x) + "," + A(b.cp1y) + "," + A(b.cp2x) + "," + A(b.cp2y) + "," + A(b.x2) + "," + A(b.y2) + " e"
                    }, Arc: function (c) {
                        var l = c.params, h = Math.min(l.x1, l.x2), d = Math.max(l.x1, l.x2), k = Math.min(l.y1, l.y2), f = Math.max(l.y1, l.y2), b = c.anticlockwise ? 1 : 0, g = (c.anticlockwise ? "at " : "wa "), e = function () {
                            var m = [null, [function () {
                                return [h, k]
                            }, function () {
                                return [h - l.r, k - l.r]
                            }], [function () {
                                return [h - l.r, k]
                            }, function () {
                                return [h, k - l.r]
                            }], [function () {
                                return [h - l.r, k - l.r]
                            }, function () {
                                return [h, k]
                            }], [function () {
                                return [h, k - l.r]
                            }, function () {
                                return [h - l.r, k]
                            }]][c.segment][b]();
                            return A(m[0]) + "," + A(m[1]) + "," + A(m[0] + (2 * l.r)) + "," + A(m[1] + (2 * l.r))
                        };
                        return g + e() + "," + A(l.x1) + "," + A(l.y1) + "," + A(l.x2) + "," + A(l.y2) + " e"
                    }
                })[a.type](a)
            }
        }
    };
    jsPlumb.Endpoints.vml.Dot = function () {
        jsPlumb.Endpoints.Dot.apply(this, arguments);
        E.apply(this, arguments);
        this.getVml = function (e, b, c, a, d) {
            return I("oval", e, b, a, d)
        }
    };
    jsPlumb.Endpoints.vml.Rectangle = function () {
        jsPlumb.Endpoints.Rectangle.apply(this, arguments);
        E.apply(this, arguments);
        this.getVml = function (e, b, c, a, d) {
            return I("rect", e, b, a, d)
        }
    };
    jsPlumb.Endpoints.vml.Image = jsPlumb.Endpoints.Image;
    jsPlumb.Endpoints.vml.Blank = jsPlumb.Endpoints.Blank;
    jsPlumb.Overlays.vml.Label = jsPlumb.Overlays.Label;
    jsPlumb.Overlays.vml.Custom = jsPlumb.Overlays.Custom;
    var B = function (b, a) {
        b.apply(this, a);
        C.apply(this, a);
        var c = this, e = null;
        c.canvas = null;
        c.isAppendedAtTopLevel = true;
        var d = function (f) {
            return "m " + A(f.hxy.x) + "," + A(f.hxy.y) + " l " + A(f.tail[0].x) + "," + A(f.tail[0].y) + " " + A(f.cxy.x) + "," + A(f.cxy.y) + " " + A(f.tail[1].x) + "," + A(f.tail[1].y) + " x e"
        };
        this.paint = function (u, s) {
            var g = {}, n = u.d, P = u.component;
            if (u.strokeStyle) {
                g.stroked = "true";
                g.strokecolor = jsPlumbUtil.convertStyle(u.strokeStyle, true)
            }
            if (u.lineWidth) {
                g.strokeweight = u.lineWidth + "px"
            }
            if (u.fillStyle) {
                g.filled = "true";
                g.fillcolor = u.fillStyle
            }
            var h = Math.min(n.hxy.x, n.tail[0].x, n.tail[1].x, n.cxy.x), k = Math.min(n.hxy.y, n.tail[0].y, n.tail[1].y, n.cxy.y), r = Math.max(n.hxy.x, n.tail[0].x, n.tail[1].x, n.cxy.x), f = Math.max(n.hxy.y, n.tail[0].y, n.tail[1].y, n.cxy.y), l = Math.abs(r - h), p = Math.abs(f - k), q = [h, k, l, p];
            g.path = d(n);
            g.coordsize = (P.w * v) + "," + (P.h * v);
            q[0] = P.x;
            q[1] = P.y;
            q[2] = P.w;
            q[3] = P.h;
            if (c.canvas == null) {
                var m = P._jsPlumb.overlayClass || "";
                var o = a && (a.length == 1) ? (a[0].cssClass || "") : "";
                g["class"] = o + " " + m;
                c.canvas = I("shape", q, g, P.canvas.parentNode, P._jsPlumb, true);
                P.appendDisplayElement(c.canvas, true);
                c.attachListeners(c.canvas, P);
                c.attachListeners(c.canvas, c)
            } else {
                F(c.canvas, q);
                J(c.canvas, g)
            }
        };
        this.reattachListeners = function () {
            if (c.canvas) {
                c.reattachListenersForElement(c.canvas, c)
            }
        };
        this.cleanup = function () {
            if (c.canvas != null) {
                jsPlumb.CurrentLibrary.removeElement(c.canvas)
            }
        }
    };
    jsPlumb.Overlays.vml.Arrow = function () {
        B.apply(this, [jsPlumb.Overlays.Arrow, arguments])
    };
    jsPlumb.Overlays.vml.PlainArrow = function () {
        B.apply(this, [jsPlumb.Overlays.PlainArrow, arguments])
    };
    jsPlumb.Overlays.vml.Diamond = function () {
        B.apply(this, [jsPlumb.Overlays.Diamond, arguments])
    }
})();
(function () {
    var Z = {
        joinstyle: "stroke-linejoin",
        "stroke-linejoin": "stroke-linejoin",
        "stroke-dashoffset": "stroke-dashoffset",
        "stroke-linecap": "stroke-linecap"
    }, H = "stroke-dasharray", O = "dashstyle", af = "linearGradient", ai = "radialGradient", ah = "fill", aj = "stop", Q = "stroke", S = "stroke-width", ac = "style", Y = "none", L = "jsplumb_gradient_", W = "lineWidth", K = {
        svg: "http://www.w3.org/2000/svg",
        xhtml: "http://www.w3.org/1999/xhtml"
    }, ad = function (a, c) {
        for (var b in c) {
            a.setAttribute(b, "" + c[b])
        }
    }, ae = function (b, c) {
        var a = document.createElementNS(K.svg, b);
        c = c || {};
        c.version = "1.1";
        c.xmlns = K.xhtml;
        ad(a, c);
        return a
    }, X = function (a) {
        return "position:absolute;left:" + a[0] + "px;top:" + a[1] + "px"
    }, ab = function (a) {
        for (var b = 0; b < a.childNodes.length; b++) {
            if (a.childNodes[b].tagName == af || a.childNodes[b].tagName == ai) {
                a.removeChild(a.childNodes[b])
            }
        }
    }, I = function (b, g, l, n, f) {
        var k = L + f._jsPlumb.idstamp();
        ab(b);
        var d;
        if (!l.gradient.offset) {
            d = ae(af, {id: k, gradientUnits: "userSpaceOnUse"})
        } else {
            d = ae(ai, {id: k})
        }
        b.appendChild(d);
        for (var e = 0; e < l.gradient.stops.length; e++) {
            var h = f.segment == 1 || f.segment == 2 ? e : l.gradient.stops.length - 1 - e, c = jsPlumbUtil.convertStyle(l.gradient.stops[h][1], true), a = ae(aj, {
                offset: Math.floor(l.gradient.stops[e][0] * 100) + "%",
                "stop-color": c
            });
            d.appendChild(a)
        }
        var m = l.strokeStyle ? Q : ah;
        g.setAttribute(ac, m + ":url(#" + k + ")")
    }, V = function (b, f, h, k, e) {
        if (h.gradient) {
            I(b, f, h, k, e)
        } else {
            ab(b);
            f.setAttribute(ac, "")
        }
        f.setAttribute(ah, h.fillStyle ? jsPlumbUtil.convertStyle(h.fillStyle, true) : Y);
        f.setAttribute(Q, h.strokeStyle ? jsPlumbUtil.convertStyle(h.strokeStyle, true) : Y);
        if (h.lineWidth) {
            f.setAttribute(S, h.lineWidth)
        }
        if (h[O] && h[W] && !h[H]) {
            var a = h[O].indexOf(",") == -1 ? " " : ",", d = h[O].split(a), g = "";
            d.forEach(function (l) {
                g += (Math.floor(l * h.lineWidth) + a)
            });
            f.setAttribute(H, g)
        } else {
            if (h[H]) {
                f.setAttribute(H, h[H])
            }
        }
        for (var c in Z) {
            if (h[c]) {
                f.setAttribute(Z[c], h[c])
            }
        }
    }, N = function (a) {
        var c = /([0-9].)(p[xt])\s(.*)/, b = a.match(c);
        return {size: b[1] + b[2], font: b[3]}
    }, P = function (h, g, d) {
        var f = d.split(" "), a = h.className, b = a.baseVal.split(" ");
        for (var c = 0; c < f.length; c++) {
            if (g) {
                if (b.indexOf(f[c]) == -1) {
                    b.push(f[c])
                }
            } else {
                var e = b.indexOf(f[c]);
                if (e != -1) {
                    b.splice(e, 1)
                }
            }
        }
        h.className.baseVal = b.join(" ")
    }, J = function (a, b) {
        P(a, true, b)
    }, aa = function (a, b) {
        P(a, false, b)
    }, R = function (b, a, c) {
        if (b.childNodes.length > c) {
            b.insertBefore(a, b.childNodes[c])
        } else {
            b.appendChild(a)
        }
    };
    jsPlumbUtil.svg = {
        addClass: J,
        removeClass: aa,
        node: ae,
        attr: ad,
        pos: X
    };
    var M = function (g) {
        var e = this, b = g.pointerEventsSpec || "all", a = {};
        jsPlumb.jsPlumbUIComponent.apply(this, g.originalArgs);
        e.canvas = null, e.path = null, e.svg = null;
        var c = g.cssClass + " " + (g.originalArgs[0].cssClass || ""), f = {
            style: "",
            width: 0,
            height: 0,
            "pointer-events": b,
            position: "absolute"
        };
        e.svg = ae("svg", f);
        if (g.useDivWrapper) {
            e.canvas = document.createElement("div");
            e.canvas.style.position = "absolute";
            jsPlumb.sizeCanvas(e.canvas, 0, 0, 1, 1);
            e.canvas.className = c
        } else {
            ad(e.svg, {"class": c});
            e.canvas = e.svg
        }
        g._jsPlumb.appendElement(e.canvas, g.originalArgs[0]["parent"]);
        if (g.useDivWrapper) {
            e.canvas.appendChild(e.svg)
        }
        var d = [e.canvas];
        this.getDisplayElements = function () {
            return d
        };
        this.appendDisplayElement = function (h) {
            d.push(h)
        };
        this.paint = function (m, n, l) {
            if (m != null) {
                var h = [e.x, e.y], o = [e.w, e.h], k;
                if (l != null) {
                    if (l.xmin < 0) {
                        h[0] += l.xmin
                    }
                    if (l.ymin < 0) {
                        h[1] += l.ymin
                    }
                    o[0] = l.xmax + ((l.xmin < 0) ? -l.xmin : 0);
                    o[1] = l.ymax + ((l.ymin < 0) ? -l.ymin : 0)
                }
                if (g.useDivWrapper) {
                    jsPlumb.sizeCanvas(e.canvas, h[0], h[1], o[0], o[1]);
                    h[0] = 0, h[1] = 0;
                    k = X([0, 0])
                } else {
                    k = X([h[0], h[1]])
                }
                a.paint.apply(this, arguments);
                ad(e.svg, {style: k, width: o[0], height: o[1]})
            }
        };
        return {renderer: a}
    };
    var ag = jsPlumb.ConnectorRenderers.svg = function (a) {
        var c = this, b = M.apply(this, [{
            cssClass: a._jsPlumb.connectorClass,
            originalArgs: arguments,
            pointerEventsSpec: "none",
            _jsPlumb: a._jsPlumb
        }]);
        b.renderer.paint = function (q, m, f) {
            var l = c.getSegments(), p = "", o = [0, 0];
            if (f.xmin < 0) {
                o[0] = -f.xmin
            }
            if (f.ymin < 0) {
                o[1] = -f.ymin
            }
            for (var n = 0; n < l.length; n++) {
                p += jsPlumb.Segments.svg.SegmentRenderer.getPath(l[n]);
                p += " "
            }
            var e = {
                d: p,
                transform: "translate(" + o[0] + "," + o[1] + ")",
                "pointer-events": a["pointer-events"] || "visibleStroke"
            }, h = null, k = [c.x, c.y, c.w, c.h];
            if (q.outlineColor) {
                var g = q.outlineWidth || 1, d = q.lineWidth + (2 * g), h = jsPlumb.CurrentLibrary.extend({}, q);
                h.strokeStyle = jsPlumbUtil.convertStyle(q.outlineColor);
                h.lineWidth = d;
                if (c.bgPath == null) {
                    c.bgPath = ae("path", e);
                    R(c.svg, c.bgPath, 0);
                    c.attachListeners(c.bgPath, c)
                } else {
                    ad(c.bgPath, e)
                }
                V(c.svg, c.bgPath, h, k, c)
            }
            if (c.path == null) {
                c.path = ae("path", e);
                R(c.svg, c.path, q.outlineColor ? 1 : 0);
                c.attachListeners(c.path, c)
            } else {
                ad(c.path, e)
            }
            V(c.svg, c.path, q, k, c)
        };
        this.reattachListeners = function () {
            if (c.bgPath) {
                c.reattachListenersForElement(c.bgPath, c)
            }
            if (c.path) {
                c.reattachListenersForElement(c.path, c)
            }
        }
    };
    jsPlumb.Segments.svg = {
        SegmentRenderer: {
            getPath: function (a) {
                return ({
                    Straight: function () {
                        var b = a.getCoordinates();
                        return "M " + b.x1 + " " + b.y1 + " L " + b.x2 + " " + b.y2
                    }, Bezier: function () {
                        var b = a.params;
                        return "M " + b.x1 + " " + b.y1 + " C " + b.cp1x + " " + b.cp1y + " " + b.cp2x + " " + b.cp2y + " " + b.x2 + " " + b.y2
                    }, Arc: function () {
                        var b = a.params, d = a.sweep > Math.PI ? 1 : 0, c = a.anticlockwise ? 0 : 1;
                        return "M" + a.x1 + " " + a.y1 + " A " + a.radius + " " + b.r + " 0 " + d + "," + c + " " + a.x2 + " " + a.y2
                    }
                })[a.type]()
            }
        }
    };
    var U = window.SvgEndpoint = function (a) {
        var c = this, b = M.apply(this, [{
            cssClass: a._jsPlumb.endpointClass,
            originalArgs: arguments,
            pointerEventsSpec: "all",
            useDivWrapper: true,
            _jsPlumb: a._jsPlumb
        }]);
        b.renderer.paint = function (d) {
            var e = jsPlumb.extend({}, d);
            if (e.outlineColor) {
                e.strokeWidth = e.outlineWidth;
                e.strokeStyle = jsPlumbUtil.convertStyle(e.outlineColor, true)
            }
            if (c.node == null) {
                c.node = c.makeNode(e);
                c.svg.appendChild(c.node);
                c.attachListeners(c.node, c)
            } else {
                if (c.updateNode != null) {
                    c.updateNode(c.node)
                }
            }
            V(c.svg, c.node, e, [c.x, c.y, c.w, c.h], c);
            X(c.node, [c.x, c.y])
        };
        this.reattachListeners = function () {
            if (c.node) {
                c.reattachListenersForElement(c.node, c)
            }
        }
    };
    jsPlumb.Endpoints.svg.Dot = function () {
        jsPlumb.Endpoints.Dot.apply(this, arguments);
        U.apply(this, arguments);
        this.makeNode = function (a) {
            return ae("circle", {
                cx: this.w / 2,
                cy: this.h / 2,
                r: this.radius
            })
        };
        this.updateNode = function (a) {
            ad(a, {cx: this.w / 2, cy: this.h / 2, r: this.radius})
        }
    };
    jsPlumb.Endpoints.svg.Rectangle = function () {
        jsPlumb.Endpoints.Rectangle.apply(this, arguments);
        U.apply(this, arguments);
        this.makeNode = function (a) {
            return ae("rect", {width: this.w, height: this.h})
        };
        this.updateNode = function (a) {
            ad(a, {width: this.w, height: this.h})
        }
    };
    jsPlumb.Endpoints.svg.Image = jsPlumb.Endpoints.Image;
    jsPlumb.Endpoints.svg.Blank = jsPlumb.Endpoints.Blank;
    jsPlumb.Overlays.svg.Label = jsPlumb.Overlays.Label;
    jsPlumb.Overlays.svg.Custom = jsPlumb.Overlays.Custom;
    var T = function (a, c) {
        a.apply(this, c);
        jsPlumb.jsPlumbUIComponent.apply(this, c);
        this.isAppendedAtTopLevel = false;
        var e = this, b = null;
        this.paint = function (f, k) {
            if (f.component.svg && k) {
                if (b == null) {
                    b = ae("path", {"pointer-events": "all"});
                    f.component.svg.appendChild(b);
                    e.attachListeners(b, f.component);
                    e.attachListeners(b, e)
                }
                var h = c && (c.length == 1) ? (c[0].cssClass || "") : "", g = [0, 0];
                if (k.xmin < 0) {
                    g[0] = -k.xmin
                }
                if (k.ymin < 0) {
                    g[1] = -k.ymin
                }
                ad(b, {
                    d: d(f.d),
                    "class": h,
                    stroke: f.strokeStyle ? f.strokeStyle : null,
                    fill: f.fillStyle ? f.fillStyle : null,
                    transform: "translate(" + g[0] + "," + g[1] + ")"
                })
            }
        };
        var d = function (f) {
            return "M" + f.hxy.x + "," + f.hxy.y + " L" + f.tail[0].x + "," + f.tail[0].y + " L" + f.cxy.x + "," + f.cxy.y + " L" + f.tail[1].x + "," + f.tail[1].y + " L" + f.hxy.x + "," + f.hxy.y
        };
        this.reattachListeners = function () {
            if (b) {
                e.reattachListenersForElement(b, e)
            }
        };
        this.cleanup = function () {
            if (b != null) {
                jsPlumb.CurrentLibrary.removeElement(b)
            }
        }
    };
    jsPlumb.Overlays.svg.Arrow = function () {
        T.apply(this, [jsPlumb.Overlays.Arrow, arguments])
    };
    jsPlumb.Overlays.svg.PlainArrow = function () {
        T.apply(this, [jsPlumb.Overlays.PlainArrow, arguments])
    };
    jsPlumb.Overlays.svg.Diamond = function () {
        T.apply(this, [jsPlumb.Overlays.Diamond, arguments])
    };
    jsPlumb.Overlays.svg.GuideLines = function () {
        var a = null, e = this, b, c;
        jsPlumb.Overlays.GuideLines.apply(this, arguments);
        this.paint = function (f, h) {
            if (a == null) {
                a = ae("path");
                f.connector.svg.appendChild(a);
                e.attachListeners(a, f.connector);
                e.attachListeners(a, e);
                b = ae("path");
                f.connector.svg.appendChild(b);
                e.attachListeners(b, f.connector);
                e.attachListeners(b, e);
                c = ae("path");
                f.connector.svg.appendChild(c);
                e.attachListeners(c, f.connector);
                e.attachListeners(c, e)
            }
            var g = [0, 0];
            if (h.xmin < 0) {
                g[0] = -h.xmin
            }
            if (h.ymin < 0) {
                g[1] = -h.ymin
            }
            ad(a, {
                d: d(f.head, f.tail),
                stroke: "red",
                fill: null,
                transform: "translate(" + g[0] + "," + g[1] + ")"
            });
            ad(b, {
                d: d(f.tailLine[0], f.tailLine[1]),
                stroke: "blue",
                fill: null,
                transform: "translate(" + g[0] + "," + g[1] + ")"
            });
            ad(c, {
                d: d(f.headLine[0], f.headLine[1]),
                stroke: "green",
                fill: null,
                transform: "translate(" + g[0] + "," + g[1] + ")"
            })
        };
        var d = function (f, g) {
            return "M " + f.x + "," + f.y + " L" + g.x + "," + g.y
        }
    }
})();
(function (c) {
    var d = function (a) {
        return typeof(a) == "string" ? c("#" + a) : c(a)
    };
    jsPlumb.CurrentLibrary = {
        addClass: function (b, e) {
            b = d(b);
            try {
                if (b[0].className.constructor == SVGAnimatedString) {
                    jsPlumbUtil.svg.addClass(b[0], e)
                }
            } catch (a) {
            }
            try {
                b.addClass(e)
            } catch (a) {
            }
        },
        animate: function (a, b, f) {
            a.animate(b, f)
        },
        appendElement: function (a, b) {
            d(b).append(a)
        },
        ajax: function (a) {
            a = a || {};
            a.type = a.type || "get";
            c.ajax(a)
        },
        bind: function (f, b, a) {
            f = d(f);
            f.bind(b, a)
        },
        dragEvents: {
            start: "start",
            stop: "stop",
            drag: "drag",
            step: "step",
            over: "over",
            out: "out",
            drop: "drop",
            complete: "complete"
        },
        extend: function (a, b) {
            return c.extend(a, b)
        },
        getAttribute: function (b, a) {
            return b.attr(a)
        },
        getClientXY: function (a) {
            return [a.clientX, a.clientY]
        },
        getDragObject: function (a) {
            return a[1].draggable || a[1].helper
        },
        getDragScope: function (a) {
            return a.draggable("option", "scope")
        },
        getDropEvent: function (a) {
            return a[0]
        },
        getDropScope: function (a) {
            return a.droppable("option", "scope")
        },
        getDOMElement: function (a) {
            if (typeof(a) == "string") {
                return document.getElementById(a)
            } else {
                if (a.context || a.length != null) {
                    return a[0]
                } else {
                    return a
                }
            }
        },
        getElementObject: d,
        getOffset: function (a) {
            return a.offset()
        },
        getOriginalEvent: function (a) {
            return a.originalEvent
        },
        getPageXY: function (a) {
            return [a.pageX, a.pageY]
        },
        getParent: function (a) {
            return d(a).parent()
        },
        getScrollLeft: function (a) {
            return a.scrollLeft()
        },
        getScrollTop: function (a) {
            return a.scrollTop()
        },
        getSelector: function (a, b) {
            if (arguments.length == 2) {
                return d(a).find(b)
            } else {
                return c(a)
            }
        },
        getSize: function (a) {
            return [a.outerWidth(), a.outerHeight()]
        },
        getTagName: function (b) {
            var a = d(b);
            return a.length > 0 ? a[0].tagName : null
        },
        getUIPosition: function (g, b) {
            b = b || 1;
            if (g.length == 1) {
                ret = {left: g[0].pageX, top: g[0].pageY}
            } else {
                var a = g[1], h = a.offset;
                ret = h || a.absolutePosition;
                a.position.left /= b;
                a.position.top /= b
            }
            return {left: ret.left / b, top: ret.top / b}
        },
        hasClass: function (a, b) {
            return a.hasClass(b)
        },
        initDraggable: function (b, g, a, h) {
            g = g || {};
            if (!g.doNotRemoveHelper) {
                g.helper = null
            }
            if (a) {
                g.scope = g.scope || jsPlumb.Defaults.Scope
            }
            b.draggable(g)
        },
        initDroppable: function (a, b) {
            b.scope = b.scope || jsPlumb.Defaults.Scope;
            a.droppable(b)
        },
        isAlreadyDraggable: function (a) {
            return d(a).hasClass("ui-draggable")
        },
        isDragSupported: function (a, b) {
            return a.draggable
        },
        isDropSupported: function (a, b) {
            return a.droppable
        },
        removeClass: function (b, e) {
            b = d(b);
            try {
                if (b[0].className.constructor == SVGAnimatedString) {
                    jsPlumbUtil.svg.removeClass(b[0], e);
                    return
                }
            } catch (a) {
            }
            b.removeClass(e)
        },
        removeElement: function (a) {
            d(a).remove()
        },
        setAttribute: function (b, a, f) {
            b.attr(a, f)
        },
        setDragFilter: function (a, b) {
            if (jsPlumb.CurrentLibrary.isAlreadyDraggable(a)) {
                a.draggable("option", "cancel", b)
            }
        },
        setDraggable: function (a, b) {
            a.draggable("option", "disabled", !b)
        },
        setDragScope: function (a, b) {
            a.draggable("option", "scope", b)
        },
        setOffset: function (b, a) {
            d(b).offset(a)
        },
        trigger: function (b, a, h) {
            var g = jQuery._data(d(b)[0], "handle");
            g(h)
        },
        unbind: function (f, b, a) {
            f = d(f);
            f.unbind(b, a)
        }
    };
    c(document).ready(jsPlumb.init)
})(jQuery);
(function () {
    "undefined" == typeof Math.sgn && (Math.sgn = function (a) {
        return 0 == a ? 0 : 0 < a ? 1 : -1
    });
    var y = {
        subtract: function (a, b) {
            return {x: a.x - b.x, y: a.y - b.y}
        }, dotProduct: function (a, b) {
            return a.x * b.x + a.y * b.y
        }, square: function (a) {
            return Math.sqrt(a.x * a.x + a.y * a.y)
        }, scale: function (a, b) {
            return {x: a.x * b, y: a.y * b}
        }
    }, x = Math.pow(2, -65), s = function (c, f) {
        for (var l = [], h = f.length - 1, m = 2 * h - 1, C = [], k = [], d = [], a = [], b = [[1, 0.6, 0.3, 0.1], [0.4, 0.6, 0.6, 0.4], [0.1, 0.3, 0.6, 1]], g = 0; g <= h; g++) {
            C[g] = y.subtract(f[g], c)
        }
        for (g = 0; g <= h - 1; g++) {
            k[g] = y.subtract(f[g + 1], f[g]), k[g] = y.scale(k[g], 3)
        }
        for (g = 0; g <= h - 1; g++) {
            for (var e = 0; e <= h; e++) {
                d[g] || (d[g] = []), d[g][e] = y.dotProduct(k[g], C[e])
            }
        }
        for (g = 0; g <= m; g++) {
            a[g] || (a[g] = []), a[g].y = 0, a[g].x = parseFloat(g) / m
        }
        m = h - 1;
        for (C = 0; C <= h + m; C++) {
            g = Math.max(0, C - m);
            for (k = Math.min(C, h); g <= k; g++) {
                j = C - g, a[g + j].y += d[j][g] * b[j][g]
            }
        }
        h = f.length - 1;
        a = p(a, 2 * h - 1, l, 0);
        m = y.subtract(c, f[0]);
        d = y.square(m);
        for (g = b = 0; g < a; g++) {
            m = y.subtract(c, q(f, h, l[g], null, null)), m = y.square(m), m < d && (d = m, b = l[g])
        }
        m = y.subtract(c, f[h]);
        m = y.square(m);
        m < d && (d = m, b = 1);
        return {location: b, distance: d}
    }, p = function (G, I, f, c) {
        var g = [], h = [], d = [], m = [], k = 0, l, a;
        a = Math.sgn(G[0].y);
        for (var H = 1; H <= I; H++) {
            l = Math.sgn(G[H].y), l != a && k++, a = l
        }
        switch (k) {
            case 0:
                return 0;
            case 1:
                if (64 <= c) {
                    return f[0] = (G[0].x + G[I].x) / 2, 1
                }
                var e, b, k = G[0].y - G[I].y;
                a = G[I].x - G[0].x;
                H = G[0].x * G[I].y - G[I].x * G[0].y;
                l = max_distance_below = 0;
                for (e = 1; e < I; e++) {
                    b = k * G[e].x + a * G[e].y + H, b > l ? l = b : b < max_distance_below && (max_distance_below = b)
                }
                b = a;
                e = 0 * b - 1 * k;
                l = (1 * (H - l) - 0 * b) * (1 / e);
                b = a;
                a = H - max_distance_below;
                e = 0 * b - 1 * k;
                k = (1 * a - 0 * b) * (1 / e);
                a = Math.min(l, k);
                if (Math.max(l, k) - a < x) {
                    return d = G[I].x - G[0].x, m = G[I].y - G[0].y, f[0] = 0 + 1 * (d * (G[0].y - 0) - m * (G[0].x - 0)) * (1 / (0 * d - 1 * m)), 1
                }
        }
        q(G, I, 0.5, g, h);
        G = p(g, I, d, c + 1);
        I = p(h, I, m, c + 1);
        for (c = 0; c < G; c++) {
            f[c] = d[c]
        }
        for (c = 0; c < I; c++) {
            f[c + G] = m[c]
        }
        return G + I
    }, q = function (f, g, c, a, d) {
        for (var e = [[]], b = 0; b <= g; b++) {
            e[0][b] = f[b]
        }
        for (f = 1; f <= g; f++) {
            for (b = 0; b <= g - f; b++) {
                e[f] || (e[f] = []), e[f][b] || (e[f][b] = {}), e[f][b].x = (1 - c) * e[f - 1][b].x + c * e[f - 1][b + 1].x, e[f][b].y = (1 - c) * e[f - 1][b].y + c * e[f - 1][b + 1].y
            }
        }
        if (null != a) {
            for (b = 0; b <= g; b++) {
                a[b] = e[b][0]
            }
        }
        if (null != d) {
            for (b = 0; b <= g; b++) {
                d[b] = e[g - b][b]
            }
        }
        return e[g][0]
    }, u = {}, n = function (f, g) {
        var m, k = f.length - 1;
        m = u[k];
        if (!m) {
            m = [];
            var a = function (B) {
                return function () {
                    return B
                }
            }, b = function () {
                return function (B) {
                    return B
                }
            }, l = function () {
                return function (B) {
                    return 1 - B
                }
            }, e = function (B) {
                return function (F) {
                    for (var A = 1, E = 0; E < B.length; E++) {
                        A *= B[E](F)
                    }
                    return A
                }
            };
            m.push(new function () {
                return function (B) {
                    return Math.pow(B, k)
                }
            });
            for (var c = 1; c < k; c++) {
                for (var d = [new a(k)], h = 0; h < k - c; h++) {
                    d.push(new b)
                }
                for (h = 0; h < c; h++) {
                    d.push(new l)
                }
                m.push(new e(d))
            }
            m.push(new function () {
                return function (B) {
                    return Math.pow(1 - B, k)
                }
            });
            u[k] = m
        }
        for (l = b = a = 0; l < f.length; l++) {
            a += f[l].x * m[l](g), b += f[l].y * m[l](g)
        }
        return {x: a, y: b}
    }, v = function (a, b) {
        return Math.sqrt(Math.pow(a.x - b.x, 2) + Math.pow(a.y - b.y, 2))
    }, w = function (a) {
        return a[0].x == a[1].x && a[0].y == a[1].y
    }, o = function (f, g, c) {
        if (w(f)) {
            return {point: f[0], location: g}
        }
        for (var a = n(f, g), d = 0, e = 0 < c ? 1 : -1, b = null; d < Math.abs(c);) {
            g += 0.005 * e, b = n(f, g), d += v(b, a), a = b
        }
        return {point: b, location: g}
    }, r = function (d, e) {
        var b = n(d, e), a = n(d.slice(0, d.length - 1), e), c = a.y - b.y, b = a.x - b.x;
        return 0 == c ? Infinity : Math.atan(c / b)
    };
    window.jsBezier = {
        distanceFromCurve: s,
        gradientAtPoint: r,
        gradientAtPointAlongCurveFrom: function (b, c, a) {
            c = o(b, c, a);
            1 < c.location && (c.location = 1);
            0 > c.location && (c.location = 0);
            return r(b, c.location)
        },
        nearestPointOnCurve: function (b, c) {
            var a = s(b, c);
            return {
                point: q(c, c.length - 1, a.location, null, null),
                location: a.location
            }
        },
        pointOnCurve: n,
        pointAlongCurveFrom: function (b, c, a) {
            return o(b, c, a).point
        },
        perpendicularToCurveAt: function (c, d, b, a) {
            d = o(c, d, null == a ? 0 : a);
            c = r(c, d.location);
            a = Math.atan(-1 / c);
            c = b / 2 * Math.sin(a);
            b = b / 2 * Math.cos(a);
            return [{x: d.point.x + b, y: d.point.y + c}, {
                x: d.point.x - b,
                y: d.point.y - c
            }]
        },
        locationAlongCurveFrom: function (b, c, a) {
            return o(b, c, a).location
        },
        getLength: function (d) {
            if (w(d)) {
                return 0
            }
            for (var e = n(d, 0), b = 0, a = 0, c = null; 1 > a;) {
                a += 0.005, c = n(d, a), b += v(c, e), e = c
            }
            return b
        }
    }
})();
(function ($) {
    $.fn.markItUp = function (settings, extraSettings) {
        var method, params, options, ctrlKey, shiftKey, altKey;
        ctrlKey = shiftKey = altKey = false;
        if (typeof settings == "string") {
            method = settings;
            params = extraSettings
        }
        options = {
            id: "",
            nameSpace: "",
            root: "",
            previewHandler: false,
            previewInWindow: "",
            previewInElement: "",
            previewAutoRefresh: true,
            previewPosition: "after",
            previewTemplatePath: "~/templates/preview.html",
            previewParser: false,
            previewParserPath: "",
            previewParserVar: "data",
            resizeHandle: true,
            beforeInsert: "",
            afterInsert: "",
            onEnter: {},
            onShiftEnter: {},
            onCtrlEnter: {},
            onTab: {},
            markupSet: [{}]
        };
        $.extend(options, settings, extraSettings);
        if (!options.root) {
            $("script").each(function (a, tag) {
                miuScript = $(tag).get(0).src.match(/(.*)jquery\.markitup(\.pack)?\.js$/);
                if (miuScript !== null) {
                    options.root = miuScript[1]
                }
            })
        }
        var uaMatch = function (ua) {
            ua = ua.toLowerCase();
            var match = /(chrome)[ \/]([\w.]+)/.exec(ua) || /(webkit)[ \/]([\w.]+)/.exec(ua) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) || /(msie) ([\w.]+)/.exec(ua) || ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) || [];
            return {browser: match[1] || "", version: match[2] || "0"}
        };
        var matched = uaMatch(navigator.userAgent);
        var browser = {};
        if (matched.browser) {
            browser[matched.browser] = true;
            browser.version = matched.version
        }
        if (browser.chrome) {
            browser.webkit = true
        } else {
            if (browser.webkit) {
                browser.safari = true
            }
        }
        return this.each(function () {
            var $$, textarea, levels, scrollPosition, caretPosition, caretOffset, clicked, hash, header, footer, previewWindow, template, iFrame, abort;
            $$ = $(this);
            textarea = this;
            levels = [];
            abort = false;
            scrollPosition = caretPosition = 0;
            caretOffset = -1;
            options.previewParserPath = localize(options.previewParserPath);
            options.previewTemplatePath = localize(options.previewTemplatePath);
            if (method) {
                switch (method) {
                    case"remove":
                        remove();
                        break;
                    case"insert":
                        markup(params);
                        break;
                    default:
                        $.error("Method " + method + " does not exist on jQuery.markItUp")
                }
                return
            }
            function localize(data, inText) {
                if (inText) {
                    return data.replace(/("|')~\//g, "$1" + options.root)
                }
                return data.replace(/^~\//, options.root)
            }

            function init() {
                id = "";
                nameSpace = "";
                if (options.id) {
                    id = 'id="' + options.id + '"'
                } else {
                    if ($$.attr("id")) {
                        id = 'id="markItUp' + ($$.attr("id").substr(0, 1).toUpperCase()) + ($$.attr("id").substr(1)) + '"'
                    }
                }
                if (options.nameSpace) {
                    nameSpace = 'class="' + options.nameSpace + '"'
                }
                $$.wrap("<div " + nameSpace + "></div>");
                $$.wrap("<div " + id + ' class="markItUp"></div>');
                $$.wrap('<div class="markItUpContainer"></div>');
                $$.addClass("markItUpEditor");
                header = $('<div class="markItUpHeader"></div>').insertBefore($$);
                var $basicSet = $(dropMenus(options.markupSet[0], "miu_basic"));
                $basicSet.append('<li class="txt_link fright li_miu_advanced"><span class="toggle_miu_advanced show_miu_advanced awesome-button" role="button">' + locaKeys.moreopts + "</span></li>");
                $basicSet.appendTo(header);
                var $advancedSet = $(dropMenus(options.markupSet[1], "miu_advanced"));
                $advancedSet.appendTo(header).hide();
                footer = $('<div class="markItUpFooter"></div>').insertAfter($$);
                if (options.resizeHandle === true && browser.safari !== true) {
                    resizeHandle = $('<div class="markItUpResizeHandle"></div>').insertAfter($$).bind("mousedown.markItUp", function (e) {
                        var h = $$.height(), y = e.clientY, mouseMove, mouseUp;
                        mouseMove = function (e) {
                            $$.css("height", Math.max(20, e.clientY + h - y) + "px");
                            return false
                        };
                        mouseUp = function (e) {
                            $("html").unbind("mousemove.markItUp", mouseMove).unbind("mouseup.markItUp", mouseUp);
                            return false
                        };
                        $("html").bind("mousemove.markItUp", mouseMove).bind("mouseup.markItUp", mouseUp)
                    });
                    footer.append(resizeHandle)
                }
                $$.bind("keydown.markItUp", keyPressed).bind("keyup", keyPressed);
                $$.bind("insertion.markItUp", function (e, settings) {
                    if (settings.target !== false) {
                        get()
                    }
                    if (textarea === $.markItUp.focused) {
                        markup(settings)
                    }
                });
                $$.bind("focus.markItUp", function () {
                    $.markItUp.focused = this
                });
                if (options.previewInElement) {
                    refreshPreview()
                }
            }

            function dropMenus(markupSet, mSetClass) {
                if (!mSetClass) {
                    mSetClass = ""
                }
                var ul = $('<ul class="' + mSetClass + '"></ul>'), i = 0;
                $.each(markupSet, function () {
                    var button = this, t = "", title, li, j;
                    title = (button.key) ? (button.name || "") + " [Ctrl+" + button.key + "]" : (button.name || "");
                    key = (button.key) ? 'accesskey="' + button.key + '"' : "";
                    if (button.separator) {
                        li = $('<li class="markItUpSeparator">' + (button.separator || "") + "</li>").appendTo(ul)
                    } else {
                        i++;
                        for (j = levels.length - 1; j >= 0; j--) {
                            t += levels[j] + "-"
                        }
                        li = $('<li class="markItUpButton markItUpButton' + t + (i) + " " + (button.className || "") + '"><a href="" ' + key + ' title="' + title + '">' + (button.name || "") + "</a></li>").bind("contextmenu.markItUp", function () {
                            return false
                        }).appendTo(ul);
                        if (!isMobile) {
                            li.unbind("click.markItUp").bind("click.markItUp", function () {
                                if (button.call) {
                                    eval(button.call)()
                                } else {
                                    var $innerUL = $(">ul", li);
                                    if ($innerUL.length > 0) {
                                        $innerUL.parents(".ui-dialog").find(".ui-dialog-titlebar-close").on("click", function () {
                                            $innerUL.hide()
                                        });
                                        $innerUL.addClass("markItUpOutpost");
                                        $("body").append($innerUL);
                                        var randomId = Math.ceil(Math.random() * 10000);
                                        li.attr("id", "markitUpDropdown" + randomId);
                                        $innerUL.attr("rel", "markitUpDropdown" + randomId);
                                        $innerUL.find(">li").bind("click.markItUp", function () {
                                            $innerUL.hide();
                                            li.attr("data-opened", 0)
                                        });
                                        $(window).on("resize", function (e) {
                                            repositionDropdowns($innerUL, li)
                                        })
                                    } else {
                                        $innerUL = $('body>ul[rel="' + li.attr("id") + '"]')
                                    }
                                    $("html").one("click.markItUp2", function () {
                                        $innerUL.hide();
                                        li.attr("data-opened", 0)
                                    });
                                    repositionDropdowns($innerUL, li);
                                    if ($innerUL.filter(":visible").length) {
                                        $innerUL.hide();
                                        li.attr("data-opened", 0)
                                    } else {
                                        $innerUL.show();
                                        li.attr("data-opened", 1)
                                    }
                                }
                                setTimeout(function () {
                                    markup(button)
                                }, 1);
                                return false
                            }).bind("focusin.markItUp", function () {
                                $$.focus()
                            })
                        } else {
                            li.bind("click.markItUp", function () {
                                $(header).find("ul ul").hide();
                                if ($(this).find("> ul").length) {
                                    $(this).find("> ul").show()
                                } else {
                                    if (button.call) {
                                        eval(button.call)()
                                    }
                                    setTimeout(function () {
                                        markup(button)
                                    }, 1)
                                }
                                return false
                            })
                        }
                        if (button.dropMenu) {
                            levels.push(i);
                            $(li).addClass("markItUpDropMenu").append(dropMenus(button.dropMenu));
                            var dropDownArr = $('<span class="dropdown_arr"></span>');
                            $(li).append(dropDownArr)
                        }
                    }
                });
                levels.pop();
                return ul
            }

            function repositionDropdowns($innerUL, li) {
                var ulHeight = $innerUL.outerHeight();
                var top;
                var dropDownTop = Math.ceil(li.offset().top);
                if (dropDownTop + li.height() + ulHeight + $("#siteFooter").outerHeight() >= $(window).innerHeight() + $(window).scrollTop()) {
                    top = dropDownTop - ulHeight - 2
                } else {
                    top = dropDownTop + 29
                }
                $innerUL.css({top: top, left: Math.floor(li.offset()["left"])})
            }

            function magicMarkups(string) {
                if (string) {
                    string = string.toString();
                    string = string.replace(/\(\!\(([\s\S]*?)\)\!\)/g, function (x, a) {
                        var b = a.split("|!|");
                        if (altKey === true) {
                            return (b[1] !== undefined) ? b[1] : b[0]
                        } else {
                            return (b[1] === undefined) ? "" : b[0]
                        }
                    });
                    string = string.replace(/\[\!\[([\s\S]*?)\]\!\]/g, function (x, a) {
                        var b = a.split(":!:");
                        if (abort === true) {
                            return false
                        }
                        value = prompt(b[0], (b[1]) ? b[1] : "");
                        if (value === null) {
                            abort = true
                        }
                        return value
                    });
                    return string
                }
                return ""
            }

            function prepare(action) {
                if ($.isFunction(action)) {
                    action = action(hash)
                }
                return magicMarkups(action)
            }

            function build(string) {
                var openWith = prepare(clicked.openWith);
                var placeHolder = prepare(clicked.placeHolder);
                var replaceWith = prepare(clicked.replaceWith);
                var closeWith = prepare(clicked.closeWith);
                var openBlockWith = prepare(clicked.openBlockWith);
                var closeBlockWith = prepare(clicked.closeBlockWith);
                var multiline = clicked.multiline;
                if (replaceWith !== "") {
                    block = openWith + replaceWith + closeWith
                } else {
                    if (selection === "" && placeHolder !== "") {
                        block = openWith + placeHolder + closeWith
                    } else {
                        string = string || selection;
                        var lines = [string], blocks = [];
                        if (multiline === true) {
                            lines = string.split(/\r?\n/)
                        }
                        for (var l = 0; l < lines.length; l++) {
                            line = lines[l];
                            var trailingSpaces;
                            if (trailingSpaces = line.match(/ *$/)) {
                                blocks.push(openWith + line.replace(/ *$/g, "") + closeWith + trailingSpaces)
                            } else {
                                blocks.push(openWith + line + closeWith)
                            }
                        }
                        block = blocks.join("\n")
                    }
                }
                block = openBlockWith + block + closeBlockWith;
                return {
                    block: block,
                    openBlockWith: openBlockWith,
                    openWith: openWith,
                    replaceWith: replaceWith,
                    placeHolder: placeHolder,
                    closeWith: closeWith,
                    closeBlockWith: closeBlockWith
                }
            }

            function markup(button) {
                var len, j, n, i;
                hash = clicked = button;
                get();
                $.extend(hash, {
                    line: "",
                    root: options.root,
                    textarea: textarea,
                    selection: (selection || ""),
                    caretPosition: caretPosition,
                    ctrlKey: ctrlKey,
                    shiftKey: shiftKey,
                    altKey: altKey
                });
                prepare(options.beforeInsert);
                prepare(clicked.beforeInsert);
                if ((ctrlKey === true && shiftKey === true) || button.multiline === true) {
                    prepare(clicked.beforeMultiInsert)
                }
                $.extend(hash, {line: 1});
                if ((ctrlKey === true && shiftKey === true)) {
                    lines = selection.split(/\r?\n/);
                    for (j = 0, n = lines.length, i = 0; i < n; i++) {
                        if ($.trim(lines[i]) !== "") {
                            $.extend(hash, {line: ++j, selection: lines[i]});
                            lines[i] = build(lines[i]).block
                        } else {
                            lines[i] = ""
                        }
                    }
                    string = {block: lines.join("\n")};
                    start = caretPosition;
                    len = string.block.length + ((browser.opera) ? n - 1 : 0)
                } else {
                    if (ctrlKey === true) {
                        string = build(selection);
                        start = caretPosition + string.openWith.length;
                        len = string.block.length - string.openWith.length - string.closeWith.length;
                        len = len - (string.block.match(/ $/) ? 1 : 0);
                        len -= fixIeBug(string.block)
                    } else {
                        if (shiftKey === true) {
                            string = build(selection);
                            start = caretPosition;
                            len = string.block.length;
                            len -= fixIeBug(string.block)
                        } else {
                            string = build(selection);
                            start = caretPosition + string.block.length;
                            len = 0;
                            start -= fixIeBug(string.block)
                        }
                    }
                }
                if ((selection === "" && string.replaceWith === "")) {
                    caretOffset += fixOperaBug(string.block);
                    start = caretPosition + string.openBlockWith.length + string.openWith.length;
                    len = string.block.length - string.openBlockWith.length - string.openWith.length - string.closeWith.length - string.closeBlockWith.length;
                    caretOffset = $$.val().substring(caretPosition, $$.val().length).length;
                    caretOffset -= fixOperaBug($$.val().substring(0, caretPosition))
                }
                $.extend(hash, {
                    caretPosition: caretPosition,
                    scrollPosition: scrollPosition
                });
                if (string.block !== selection && abort === false) {
                    insert(string.block);
                    set(start, len)
                } else {
                    caretOffset = -1
                }
                get();
                $.extend(hash, {line: "", selection: selection});
                if ((ctrlKey === true && shiftKey === true) || button.multiline === true) {
                    prepare(clicked.afterMultiInsert)
                }
                prepare(clicked.afterInsert);
                prepare(options.afterInsert);
                if (previewWindow && options.previewAutoRefresh) {
                    refreshPreview()
                }
                shiftKey = altKey = ctrlKey = abort = false
            }

            function fixOperaBug(string) {
                if (browser.opera) {
                    return string.length - string.replace(/\n*/g, "").length
                }
                return 0
            }

            function fixIeBug(string) {
                if (browser.msie) {
                    return string.length - string.replace(/\r*/g, "").length
                }
                return 0
            }

            function insert(block) {
                if (document.selection) {
                    var newSelection = document.selection.createRange();
                    newSelection.text = block
                } else {
                    textarea.value = textarea.value.substring(0, caretPosition) + block + textarea.value.substring(caretPosition + selection.length, textarea.value.length)
                }
            }

            function set(start, len) {
                if (textarea.createTextRange) {
                    if (browser.opera && browser.version >= 9.5 && len == 0) {
                        return false
                    }
                    range = textarea.createTextRange();
                    range.collapse(true);
                    range.moveStart("character", start);
                    range.moveEnd("character", len);
                    range.select()
                } else {
                    if (textarea.setSelectionRange) {
                        textarea.setSelectionRange(start, start + len)
                    }
                }
                textarea.scrollTop = scrollPosition;
                textarea.focus()
            }

            function get() {
                textarea.focus();
                scrollPosition = textarea.scrollTop;
                if (document.selection) {
                    selection = document.selection.createRange().text;
                    if (browser.msie) {
                        var range = document.selection.createRange(), rangeCopy = range.duplicate();
                        rangeCopy.moveToElementText(textarea);
                        caretPosition = -1;
                        while (rangeCopy.inRange(range)) {
                            rangeCopy.moveStart("character");
                            caretPosition++
                        }
                    } else {
                        caretPosition = textarea.selectionStart
                    }
                } else {
                    caretPosition = textarea.selectionStart;
                    selection = textarea.value.substring(caretPosition, textarea.selectionEnd)
                }
                return selection
            }

            function preview() {
                if (typeof options.previewHandler === "function") {
                    previewWindow = true
                } else {
                    if (options.previewInElement) {
                        previewWindow = $(options.previewInElement)
                    } else {
                        if (!previewWindow || previewWindow.closed) {
                            if (options.previewInWindow) {
                                previewWindow = window.open("", "preview", options.previewInWindow);
                                $(window).unload(function () {
                                    previewWindow.close()
                                })
                            } else {
                                iFrame = $('<iframe class="markItUpPreviewFrame"></iframe>');
                                if (options.previewPosition == "after") {
                                    iFrame.insertAfter(footer)
                                } else {
                                    iFrame.insertBefore(header)
                                }
                                previewWindow = iFrame[iFrame.length - 1].contentWindow || frame[iFrame.length - 1]
                            }
                        } else {
                            if (altKey === true) {
                                if (iFrame) {
                                    iFrame.remove()
                                } else {
                                    previewWindow.close()
                                }
                                previewWindow = iFrame = false
                            }
                        }
                    }
                }
                if (!options.previewAutoRefresh) {
                    refreshPreview()
                }
                if (options.previewInWindow) {
                    previewWindow.focus()
                }
            }

            function refreshPreview() {
                renderPreview()
            }

            function renderPreview() {
                var phtml;
                if (options.previewHandler && typeof options.previewHandler === "function") {
                    options.previewHandler($$.val())
                } else {
                    if (options.previewParser && typeof options.previewParser === "function") {
                        var data = options.previewParser($$.val());
                        writeInPreview(localize(data, 1))
                    } else {
                        if (options.previewParserPath !== "") {
                            $.ajax({
                                type: "POST",
                                dataType: "text",
                                global: false,
                                url: options.previewParserPath,
                                data: options.previewParserVar + "=" + encodeURIComponent($$.val()),
                                success: function (data) {
                                    writeInPreview(localize(data, 1))
                                }
                            })
                        } else {
                            if (!template) {
                                $.ajax({
                                    url: options.previewTemplatePath,
                                    dataType: "text",
                                    global: false,
                                    success: function (data) {
                                        writeInPreview(localize(data, 1).replace(/<!-- content -->/g, $$.val()))
                                    }
                                })
                            }
                        }
                    }
                }
                return false
            }

            function writeInPreview(data) {
                if (options.previewInElement) {
                    $(options.previewInElement).html(data)
                } else {
                    if (previewWindow && previewWindow.document) {
                        try {
                            sp = previewWindow.document.documentElement.scrollTop
                        } catch (e) {
                            sp = 0
                        }
                        previewWindow.document.open();
                        previewWindow.document.write(data);
                        previewWindow.document.close();
                        previewWindow.document.documentElement.scrollTop = sp
                    }
                }
            }

            function keyPressed(e) {
                shiftKey = e.shiftKey;
                altKey = e.altKey;
                ctrlKey = (!(e.altKey && e.ctrlKey)) ? (e.ctrlKey || e.metaKey) : false;
                if (e.type === "keydown") {
                    if (ctrlKey === true) {
                        li = $('a[accesskey="' + ((e.keyCode == 13) ? "\\n" : String.fromCharCode(e.keyCode)) + '"]', header).parent("li");
                        if (li.length !== 0) {
                            ctrlKey = false;
                            setTimeout(function () {
                                li.triggerHandler("mouseup")
                            }, 1);
                            return false
                        }
                    }
                    if (e.keyCode === 13 || e.keyCode === 10) {
                        if (ctrlKey === true) {
                            ctrlKey = false;
                            markup(options.onCtrlEnter);
                            return options.onCtrlEnter.keepDefault
                        } else {
                            if (shiftKey === true) {
                                shiftKey = false;
                                markup(options.onShiftEnter);
                                return options.onShiftEnter.keepDefault
                            } else {
                                markup(options.onEnter);
                                return options.onEnter.keepDefault
                            }
                        }
                    }
                    if (e.keyCode === 9) {
                        if (shiftKey == true || ctrlKey == true || altKey == true) {
                            return false
                        }
                        if (caretOffset !== -1) {
                            get();
                            caretOffset = $$.val().length - caretOffset;
                            set(caretOffset, 0);
                            caretOffset = -1;
                            return false
                        } else {
                            markup(options.onTab);
                            return options.onTab.keepDefault
                        }
                    }
                }
            }

            function remove() {
                $$.unbind(".markItUp").removeClass("markItUpEditor");
                $$.parent("div").parent("div.markItUp").parent("div").replaceWith($$);
                $$.data("markItUp", null)
            }

            init()
        })
    };
    $.fn.markItUpRemove = function () {
        return this.each(function () {
            $(this).markItUp("remove")
        })
    };
    $.markItUp = function (settings) {
        var options = {target: false};
        $.extend(options, settings);
        if (options.target) {
            return $(options.target).each(function () {
                $(this).focus();
                $(this).trigger("insertion", [options])
            })
        } else {
            $("textarea").trigger("insertion", [options])
        }
    }
})(jQuery);
/*
 * jQuery Mousewheel 3.1.13
 *
 * Copyright 2015 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function (a) {
    "function" == typeof define && define.amd ? define(["./jquery.js"], a) : "object" == typeof exports ? module.exports = a : a(jQuery)
}(function (h) {
    function k(m) {
        var n = m || window.event, o = e.call(arguments, 1), v = 0, E = 0, p = 0, q = 0, r = 0, s = 0;
        if (m = h.event.fix(n), m.type = "mousewheel", "detail" in n && (p = -1 * n.detail), "wheelDelta" in n && (p = n.wheelDelta), "wheelDeltaY" in n && (p = n.wheelDeltaY), "wheelDeltaX" in n && (E = -1 * n.wheelDeltaX), "axis" in n && n.axis === n.HORIZONTAL_AXIS && (E = -1 * p, p = 0), v = 0 === p ? E : p, "deltaY" in n && (p = -1 * n.deltaY, v = p), "deltaX" in n && (E = n.deltaX, 0 === p && (v = -1 * E)), 0 !== p || 0 !== E) {
            if (1 === n.deltaMode) {
                var u = h.data(this, "mousewheel-line-height");
                v *= u, p *= u, E *= u
            } else {
                if (2 === n.deltaMode) {
                    var w = h.data(this, "mousewheel-page-height");
                    v *= w, p *= w, E *= w
                }
            }
            if (q = Math.max(Math.abs(p), Math.abs(E)), (!b || b > q) && (b = q, y(n, q) && (b /= 40)), y(n, q) && (v /= 40, E /= 40, p /= 40), v = Math[v >= 1 ? "floor" : "ceil"](v / b), E = Math[E >= 1 ? "floor" : "ceil"](E / b), p = Math[p >= 1 ? "floor" : "ceil"](p / b), g.settings.normalizeOffset && this.getBoundingClientRect) {
                var l = this.getBoundingClientRect();
                r = m.clientX - l.left, s = m.clientY - l.top
            }
            return m.deltaX = E, m.deltaY = p, m.deltaFactor = b, m.offsetX = r, m.offsetY = s, m.deltaMode = 0, o.unshift(m, v, E, p), a && clearTimeout(a), a = setTimeout(x, 200), (h.event.dispatch || h.event.handle).apply(this, o)
        }
    }

    function x() {
        b = null
    }

    function y(l, m) {
        return g.settings.adjustOldDeltas && "mousewheel" === l.type && m % 120 === 0
    }

    var a, b, c = ["wheel", "mousewheel", "DOMMouseScroll", "MozMousePixelScroll"], d = "onwheel" in document || document.documentMode >= 9 ? ["wheel"] : ["mousewheel", "DomMouseScroll", "MozMousePixelScroll"], e = Array.prototype.slice;
    if (h.event.fixHooks) {
        for (var f = c.length; f;) {
            h.event.fixHooks[c[--f]] = h.event.mouseHooks
        }
    }
    var g = h.event.special.mousewheel = {
        version: "3.1.12",
        setup: function () {
            if (this.addEventListener) {
                for (var l = d.length; l;) {
                    this.addEventListener(d[--l], k, !1)
                }
            } else {
                this.onmousewheel = k
            }
            h.data(this, "mousewheel-line-height", g.getLineHeight(this)), h.data(this, "mousewheel-page-height", g.getPageHeight(this))
        },
        teardown: function () {
            if (this.removeEventListener) {
                for (var l = d.length; l;) {
                    this.removeEventListener(d[--l], k, !1)
                }
            } else {
                this.onmousewheel = null
            }
            h.removeData(this, "mousewheel-line-height"), h.removeData(this, "mousewheel-page-height")
        },
        getLineHeight: function (l) {
            var m = h(l), n = m["offsetParent" in h.fn ? "offsetParent" : "parent"]();
            return n.length || (n = h("body")), parseInt(n.css("fontSize"), 10) || parseInt(m.css("fontSize"), 10) || 16
        },
        getPageHeight: function (l) {
            return h(l).height()
        },
        settings: {adjustOldDeltas: !0, normalizeOffset: !0}
    };
    h.fn.extend({
        mousewheel: function (l) {
            return l ? this.bind("mousewheel", l) : this.trigger("mousewheel")
        }, unmousewheel: function (l) {
            return this.unbind("mousewheel", l)
        }
    })
});
(function ($) {
    $.extend({
        tablesorter: new function () {
            var parsers = [], widgets = [];
            this.defaults = {
                cssHeader: "header",
                cssAsc: "headerSortUp",
                cssDesc: "headerSortDown",
                cssChildRow: "expand-child",
                sortInitialOrder: "asc",
                sortMultiSortKey: "shiftKey",
                sortForce: null,
                sortAppend: null,
                sortLocaleCompare: true,
                textExtraction: "simple",
                parsers: {},
                widgets: [],
                widgetZebra: {css: ["even", "odd"]},
                headers: {},
                widthFixed: false,
                cancelSelection: true,
                sortList: [],
                headerList: [],
                dateFormat: "us",
                decimal: "/.|,/g",
                onRenderHeader: null,
                selectorHeaders: "thead th",
                debug: false
            };
            function benchmark(s, d) {
                log(s + "," + (new Date().getTime() - d.getTime()) + "ms")
            }

            this.benchmark = benchmark;
            function log(s) {
                if (typeof console != "undefined" && typeof console.debug != "undefined") {
                    console.log(s)
                } else {
                    alert(s)
                }
            }

            function buildParserCache(table, $headers) {
                if (table.config.debug) {
                    var parsersDebug = ""
                }
                if (table.tBodies.length == 0) {
                    return
                }
                var rows = table.tBodies[0].rows;
                if (rows[0]) {
                    var list = [], cells = rows[0].cells, l = cells.length;
                    for (var i = 0; i < l; i++) {
                        var p = false;
                        if ($.metadata && ($($headers[i]).metadata() && $($headers[i]).metadata().sorter)) {
                            p = getParserById($($headers[i]).metadata().sorter)
                        } else {
                            if ((table.config.headers[i] && table.config.headers[i].sorter)) {
                                p = getParserById(table.config.headers[i].sorter)
                            }
                        }
                        if (!p) {
                            p = detectParserForColumn(table, rows, -1, i)
                        }
                        if (table.config.debug) {
                            parsersDebug += "column:" + i + " parser:" + p.id + "\n"
                        }
                        list.push(p)
                    }
                }
                if (table.config.debug) {
                    log(parsersDebug)
                }
                return list
            }

            function detectParserForColumn(table, rows, rowIndex, cellIndex) {
                var l = parsers.length, node = false, nodeValue = false, keepLooking = true;
                while (nodeValue == "" && keepLooking) {
                    rowIndex++;
                    if (rows[rowIndex]) {
                        node = getNodeFromRowAndCellIndex(rows, rowIndex, cellIndex);
                        nodeValue = trimAndGetNodeText(table.config, node);
                        if (table.config.debug) {
                            log("Checking if value was empty on row:" + rowIndex)
                        }
                    } else {
                        keepLooking = false
                    }
                }
                for (var i = 1; i < l; i++) {
                    if (parsers[i].is(nodeValue, table, node)) {
                        return parsers[i]
                    }
                }
                return parsers[0]
            }

            function getNodeFromRowAndCellIndex(rows, rowIndex, cellIndex) {
                return rows[rowIndex].cells[cellIndex]
            }

            function trimAndGetNodeText(config, node) {
                return $.trim(getElementText(config, node))
            }

            function getParserById(name) {
                var l = parsers.length;
                for (var i = 0; i < l; i++) {
                    if (parsers[i].id.toLowerCase() == name.toLowerCase()) {
                        return parsers[i]
                    }
                }
                return false
            }

            function buildCache(table) {
                if (table.config.debug) {
                    var cacheTime = new Date()
                }
                var totalRows = (table.tBodies[0] && table.tBodies[0].rows.length) || 0, totalCells = (table.tBodies[0].rows[0] && table.tBodies[0].rows[0].cells.length) || 0, parsers = table.config.parsers, cache = {
                    row: [],
                    normalized: []
                };
                for (var i = 0; i < totalRows; ++i) {
                    var c = $(table.tBodies[0].rows[i]), cols = [];
                    if (c.hasClass(table.config.cssChildRow)) {
                        cache.row[cache.row.length - 1] = cache.row[cache.row.length - 1].add(c);
                        continue
                    }
                    cache.row.push(c);
                    for (var j = 0; j < totalCells; ++j) {
                        cols.push(parsers[j].format(getElementText(table.config, c[0].cells[j]), table, c[0].cells[j]))
                    }
                    cols.push(cache.normalized.length);
                    cache.normalized.push(cols);
                    cols = null
                }
                if (table.config.debug) {
                    benchmark("Building cache for " + totalRows + " rows:", cacheTime)
                }
                return cache
            }

            function getElementText(config, node) {
                var text = "";
                if (!node) {
                    return ""
                }
                if (!config.supportsTextContent) {
                    config.supportsTextContent = node.textContent || false
                }
                if (config.textExtraction == "simple") {
                    if (config.supportsTextContent) {
                        text = node.textContent
                    } else {
                        if (node.childNodes[0] && node.childNodes[0].hasChildNodes()) {
                            text = node.childNodes[0].innerHTML
                        } else {
                            text = node.innerHTML
                        }
                    }
                } else {
                    if (typeof(config.textExtraction) == "function") {
                        text = config.textExtraction(node)
                    } else {
                        text = $(node).text()
                    }
                }
                return text
            }

            function appendToTable(table, cache) {
                if (table.config.debug) {
                    var appendTime = new Date()
                }
                var c = cache, r = c.row, n = c.normalized, totalRows = n.length, checkCell = (n[0].length - 1), tableBody = $(table.tBodies[0]), rows = [];
                for (var i = 0; i < totalRows; i++) {
                    var pos = n[i][checkCell];
                    rows.push(r[pos]);
                    if (!table.config.appender) {
                        var l = r[pos].length;
                        for (var j = 0; j < l; j++) {
                            tableBody[0].appendChild(r[pos][j])
                        }
                    }
                }
                if (table.config.appender) {
                    table.config.appender(table, rows)
                }
                rows = null;
                if (table.config.debug) {
                    benchmark("Rebuilt table:", appendTime)
                }
                applyWidget(table);
                setTimeout(function () {
                    $(table).trigger("sortEnd")
                }, 0)
            }

            function buildHeaders(table) {
                if (table.config.debug) {
                    var time = new Date()
                }
                var meta = ($.metadata) ? true : false;
                var header_index = computeTableHeaderCellIndexes(table);
                $tableHeaders = $(table.config.selectorHeaders, table).each(function (index) {
                    this.column = header_index[this.parentNode.rowIndex + "-" + this.cellIndex];
                    this.order = formatSortingOrder(table.config.sortInitialOrder);
                    this.count = this.order;
                    if (checkHeaderMetadata(this) || checkHeaderOptions(table, index)) {
                        this.sortDisabled = true
                    }
                    if (checkHeaderOptionsSortingLocked(table, index)) {
                        this.order = this.lockedOrder = checkHeaderOptionsSortingLocked(table, index)
                    }
                    if (!this.sortDisabled) {
                        var $th = $(this).addClass(table.config.cssHeader);
                        if (table.config.onRenderHeader) {
                            table.config.onRenderHeader.apply($th)
                        }
                    }
                    table.config.headerList[index] = this
                });
                if (table.config.debug) {
                    benchmark("Built headers:", time);
                    log($tableHeaders)
                }
                return $tableHeaders
            }

            function computeTableHeaderCellIndexes(t) {
                var matrix = [];
                var lookup = {};
                var thead = t.getElementsByTagName("THEAD")[0];
                var trs = thead.getElementsByTagName("TR");
                for (var i = 0; i < trs.length; i++) {
                    var cells = trs[i].cells;
                    for (var j = 0; j < cells.length; j++) {
                        var c = cells[j];
                        var rowIndex = c.parentNode.rowIndex;
                        var cellId = rowIndex + "-" + c.cellIndex;
                        var rowSpan = c.rowSpan || 1;
                        var colSpan = c.colSpan || 1;
                        var firstAvailCol;
                        if (typeof(matrix[rowIndex]) == "undefined") {
                            matrix[rowIndex] = []
                        }
                        for (var k = 0; k < matrix[rowIndex].length + 1; k++) {
                            if (typeof(matrix[rowIndex][k]) == "undefined") {
                                firstAvailCol = k;
                                break
                            }
                        }
                        lookup[cellId] = firstAvailCol;
                        for (var k = rowIndex; k < rowIndex + rowSpan; k++) {
                            if (typeof(matrix[k]) == "undefined") {
                                matrix[k] = []
                            }
                            var matrixrow = matrix[k];
                            for (var l = firstAvailCol; l < firstAvailCol + colSpan; l++) {
                                matrixrow[l] = "x"
                            }
                        }
                    }
                }
                return lookup
            }

            function checkCellColSpan(table, rows, row) {
                var arr = [], r = table.tHead.rows, c = r[row].cells;
                for (var i = 0; i < c.length; i++) {
                    var cell = c[i];
                    if (cell.colSpan > 1) {
                        arr = arr.concat(checkCellColSpan(table, headerArr, row++))
                    } else {
                        if (table.tHead.length == 1 || (cell.rowSpan > 1 || !r[row + 1])) {
                            arr.push(cell)
                        }
                    }
                }
                return arr
            }

            function checkHeaderMetadata(cell) {
                if (($.metadata) && ($(cell).metadata().sorter === false)) {
                    return true
                }
                return false
            }

            function checkHeaderOptions(table, i) {
                if ((table.config.headers[i]) && (table.config.headers[i].sorter === false)) {
                    return true
                }
                return false
            }

            function checkHeaderOptionsSortingLocked(table, i) {
                if ((table.config.headers[i]) && (table.config.headers[i].lockedOrder)) {
                    return table.config.headers[i].lockedOrder
                }
                return false
            }

            function applyWidget(table) {
                var c = table.config.widgets;
                var l = c.length;
                for (var i = 0; i < l; i++) {
                    getWidgetById(c[i]).format(table)
                }
            }

            function getWidgetById(name) {
                var l = widgets.length;
                for (var i = 0; i < l; i++) {
                    if (widgets[i].id.toLowerCase() == name.toLowerCase()) {
                        return widgets[i]
                    }
                }
            }

            function formatSortingOrder(v) {
                if (typeof(v) != "Number") {
                    return (v.toLowerCase() == "desc") ? 1 : 0
                } else {
                    return (v == 1) ? 1 : 0
                }
            }

            function isValueInArray(v, a) {
                var l = a.length;
                for (var i = 0; i < l; i++) {
                    if (a[i][0] == v) {
                        return true
                    }
                }
                return false
            }

            function setHeadersCss(table, $headers, list, css) {
                $headers.removeClass(css[0]).removeClass(css[1]);
                var h = [];
                $headers.each(function (offset) {
                    if (!this.sortDisabled) {
                        h[this.column] = $(this)
                    }
                });
                var l = list.length;
                for (var i = 0; i < l; i++) {
                    h[list[i][0]].addClass(css[list[i][1]])
                }
            }

            function fixColumnWidth(table, $headers) {
                var c = table.config;
                if (c.widthFixed) {
                    var colgroup = $("<colgroup>");
                    $("tr:first td", table.tBodies[0]).each(function () {
                        colgroup.append($("<col>").css("width", $(this).width()))
                    });
                    $(table).prepend(colgroup)
                }
            }

            function updateHeaderSortCount(table, sortList) {
                var c = table.config, l = sortList.length;
                for (var i = 0; i < l; i++) {
                    var s = sortList[i], o = c.headerList[s[0]];
                    o.count = s[1];
                    o.count++
                }
            }

            function multisort(table, sortList, cache) {
                if (table.config.debug) {
                    var sortTime = new Date()
                }
                var dynamicExp = "var sortWrapper = function(a,b) {", l = sortList.length;
                for (var i = 0; i < l; i++) {
                    var c = sortList[i][0];
                    var order = sortList[i][1];
                    var s = (table.config.parsers[c].type == "text") ? ((order == 0) ? makeSortFunction("text", "asc", c) : makeSortFunction("text", "desc", c)) : ((order == 0) ? makeSortFunction("numeric", "asc", c) : makeSortFunction("numeric", "desc", c));
                    var e = "e" + i;
                    dynamicExp += "var " + e + " = " + s;
                    dynamicExp += "if(" + e + ") { return " + e + "; } ";
                    dynamicExp += "else { "
                }
                var orgOrderCol = cache.normalized[0].length - 1;
                dynamicExp += "return a[" + orgOrderCol + "]-b[" + orgOrderCol + "];";
                for (var i = 0; i < l; i++) {
                    dynamicExp += "}; "
                }
                dynamicExp += "return 0; ";
                dynamicExp += "}; ";
                if (table.config.debug) {
                    benchmark("Evaling expression:" + dynamicExp, new Date())
                }
                eval(dynamicExp);
                cache.normalized.sort(sortWrapper);
                if (table.config.debug) {
                    benchmark("Sorting on " + sortList.toString() + " and dir " + order + " time:", sortTime)
                }
                return cache
            }

            function makeSortFunction(type, direction, index) {
                var a = "a[" + index + "]", b = "b[" + index + "]";
                if (type == "text" && direction == "asc") {
                    return "(" + a + " == " + b + " ? 0 : (" + a + " === null ? Number.POSITIVE_INFINITY : (" + b + " === null ? Number.NEGATIVE_INFINITY : (" + a + " < " + b + ") ? -1 : 1 )));"
                } else {
                    if (type == "text" && direction == "desc") {
                        return "(" + a + " == " + b + " ? 0 : (" + a + " === null ? Number.POSITIVE_INFINITY : (" + b + " === null ? Number.NEGATIVE_INFINITY : (" + b + " < " + a + ") ? -1 : 1 )));"
                    } else {
                        if (type == "numeric" && direction == "asc") {
                            return "(" + a + " === null && " + b + " === null) ? 0 :(" + a + " === null ? Number.POSITIVE_INFINITY : (" + b + " === null ? Number.NEGATIVE_INFINITY : " + a + " - " + b + "));"
                        } else {
                            if (type == "numeric" && direction == "desc") {
                                return "(" + a + " === null && " + b + " === null) ? 0 :(" + a + " === null ? Number.POSITIVE_INFINITY : (" + b + " === null ? Number.NEGATIVE_INFINITY : " + b + " - " + a + "));"
                            }
                        }
                    }
                }
            }

            function makeSortText(i) {
                return "((a[" + i + "] < b[" + i + "]) ? -1 : ((a[" + i + "] > b[" + i + "]) ? 1 : 0));"
            }

            function makeSortTextDesc(i) {
                return "((b[" + i + "] < a[" + i + "]) ? -1 : ((b[" + i + "] > a[" + i + "]) ? 1 : 0));"
            }

            function makeSortNumeric(i) {
                return "a[" + i + "]-b[" + i + "];"
            }

            function makeSortNumericDesc(i) {
                return "b[" + i + "]-a[" + i + "];"
            }

            function sortText(a, b) {
                if (table.config.sortLocaleCompare) {
                    return a.localeCompare(b)
                }
                return ((a < b) ? -1 : ((a > b) ? 1 : 0))
            }

            function sortTextDesc(a, b) {
                if (table.config.sortLocaleCompare) {
                    return b.localeCompare(a)
                }
                return ((b < a) ? -1 : ((b > a) ? 1 : 0))
            }

            function sortNumeric(a, b) {
                return a - b
            }

            function sortNumericDesc(a, b) {
                return b - a
            }

            function getCachedSortType(parsers, i) {
                return parsers[i].type
            }

            this.construct = function (settings) {
                return this.each(function () {
                    if (!this.tHead || !this.tBodies) {
                        return
                    }
                    var $this, $document, $headers, cache, config, shiftDown = 0, sortOrder;
                    this.config = {};
                    config = $.extend(this.config, $.tablesorter.defaults, settings);
                    $this = $(this);
                    $.data(this, "tablesorter", config);
                    $headers = buildHeaders(this);
                    this.config.parsers = buildParserCache(this, $headers);
                    cache = buildCache(this);
                    var sortCSS = [config.cssDesc, config.cssAsc];
                    fixColumnWidth(this);
                    $headers.click(function (e) {
                        var totalRows = ($this[0].tBodies[0] && $this[0].tBodies[0].rows.length) || 0;
                        if (!this.sortDisabled && totalRows > 0) {
                            $this.trigger("sortStart");
                            var $cell = $(this);
                            var i = this.column;
                            this.order = this.count++ % 2;
                            if (this.lockedOrder) {
                                this.order = this.lockedOrder
                            }
                            if (!e[config.sortMultiSortKey]) {
                                config.sortList = [];
                                if (config.sortForce != null) {
                                    var a = config.sortForce;
                                    for (var j = 0; j < a.length; j++) {
                                        if (a[j][0] != i) {
                                            config.sortList.push(a[j])
                                        }
                                    }
                                }
                                config.sortList.push([i, this.order])
                            } else {
                                if (isValueInArray(i, config.sortList)) {
                                    for (var j = 0; j < config.sortList.length; j++) {
                                        var s = config.sortList[j], o = config.headerList[s[0]];
                                        if (s[0] == i) {
                                            o.count = s[1];
                                            o.count++;
                                            s[1] = o.count % 2
                                        }
                                    }
                                } else {
                                    config.sortList.push([i, this.order])
                                }
                            }
                            setTimeout(function () {
                                setHeadersCss($this[0], $headers, config.sortList, sortCSS);
                                appendToTable($this[0], multisort($this[0], config.sortList, cache))
                            }, 1);
                            return false
                        }
                    }).mousedown(function () {
                        if (config.cancelSelection) {
                            this.onselectstart = function () {
                                return false
                            };
                            return false
                        }
                    });
                    $this.bind("update", function () {
                        var me = this;
                        setTimeout(function () {
                            me.config.parsers = buildParserCache(me, $headers);
                            cache = buildCache(me)
                        }, 1)
                    }).bind("updateCell", function (e, cell) {
                        var config = this.config;
                        var pos = [(cell.parentNode.rowIndex - 1), cell.cellIndex];
                        cache.normalized[pos[0]][pos[1]] = config.parsers[pos[1]].format(getElementText(config, cell), cell)
                    }).bind("sorton", function (e, list) {
                        $(this).trigger("sortStart");
                        config.sortList = list;
                        var sortList = config.sortList;
                        updateHeaderSortCount(this, sortList);
                        setHeadersCss(this, $headers, sortList, sortCSS);
                        appendToTable(this, multisort(this, sortList, cache))
                    }).bind("appendCache", function () {
                        appendToTable(this, cache)
                    }).bind("applyWidgetId", function (e, id) {
                        getWidgetById(id).format(this)
                    }).bind("applyWidgets", function () {
                        applyWidget(this)
                    });
                    if ($.metadata && ($(this).metadata() && $(this).metadata().sortlist)) {
                        config.sortList = $(this).metadata().sortlist
                    }
                    if (config.sortList.length > 0) {
                        $this.trigger("sorton", [config.sortList])
                    }
                    applyWidget(this)
                })
            };
            this.addParser = function (parser) {
                var l = parsers.length, a = true;
                for (var i = 0; i < l; i++) {
                    if (parsers[i].id.toLowerCase() == parser.id.toLowerCase()) {
                        a = false
                    }
                }
                if (a) {
                    parsers.push(parser)
                }
            };
            this.addWidget = function (widget) {
                widgets.push(widget)
            };
            this.formatFloat = function (s) {
                var i = parseFloat(s);
                return (isNaN(i)) ? 0 : i
            };
            this.formatInt = function (s) {
                var i = parseInt(s);
                return (isNaN(i)) ? 0 : i
            };
            this.isDigit = function (s, config) {
                return /^[-+]?\d*$/.test($.trim(s.replace(/[,.']/g, "")))
            };
            this.clearTableBody = function (table) {
                if ($.browser.msie) {
                    function empty() {
                        while (this.firstChild) {
                            this.removeChild(this.firstChild)
                        }
                    }

                    empty.apply(table.tBodies[0])
                } else {
                    table.tBodies[0].innerHTML = ""
                }
            }
        }
    });
    $.fn.extend({tablesorter: $.tablesorter.construct});
    var ts = $.tablesorter;
    ts.addParser({
        id: "text", is: function (s) {
            return true
        }, format: function (s) {
            return $.trim(s.toLocaleLowerCase())
        }, type: "text"
    });
    ts.addParser({
        id: "digit", is: function (s, table) {
            var c = table.config;
            return $.tablesorter.isDigit(s, c)
        }, format: function (s) {
            return $.tablesorter.formatFloat(s)
        }, type: "numeric"
    });
    ts.addParser({
        id: "currency", is: function (s) {
            return /^[£$€?.]/.test(s)
        }, format: function (s) {
            return $.tablesorter.formatFloat(s.replace(new RegExp(/[£$€]/g), ""))
        }, type: "numeric"
    });
    ts.addParser({
        id: "ipAddress", is: function (s) {
            return /^\d{2,3}[\.]\d{2,3}[\.]\d{2,3}[\.]\d{2,3}$/.test(s)
        }, format: function (s) {
            var a = s.split("."), r = "", l = a.length;
            for (var i = 0; i < l; i++) {
                var item = a[i];
                if (item.length == 2) {
                    r += "0" + item
                } else {
                    r += item
                }
            }
            return $.tablesorter.formatFloat(r)
        }, type: "numeric"
    });
    ts.addParser({
        id: "url", is: function (s) {
            return /^(https?|ftp|file):\/\/$/.test(s)
        }, format: function (s) {
            return jQuery.trim(s.replace(new RegExp(/(https?|ftp|file):\/\//), ""))
        }, type: "text"
    });
    ts.addParser({
        id: "isoDate", is: function (s) {
            return /^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(s)
        }, format: function (s) {
            return $.tablesorter.formatFloat((s != "") ? new Date(s.replace(new RegExp(/-/g), "/")).getTime() : "0")
        }, type: "numeric"
    });
    ts.addParser({
        id: "percent", is: function (s) {
            return /\%$/.test($.trim(s))
        }, format: function (s) {
            return $.tablesorter.formatFloat(s.replace(new RegExp(/%/g), ""))
        }, type: "numeric"
    });
    ts.addParser({
        id: "usLongDate", is: function (s) {
            return s.match(new RegExp(/^[A-Za-z]{3,10}\.? [0-9]{1,2}, ([0-9]{4}|'?[0-9]{2}) (([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(AM|PM)))$/))
        }, format: function (s) {
            return $.tablesorter.formatFloat(new Date(s).getTime())
        }, type: "numeric"
    });
    ts.addParser({
        id: "shortDate", is: function (s) {
            return /\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/.test(s)
        }, format: function (s, table) {
            var c = table.config;
            s = s.replace(/\-/g, "/");
            if (c.dateFormat == "us") {
                s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$1/$2")
            } else {
                if (c.dateFormat == "uk") {
                    s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$2/$1")
                } else {
                    if (c.dateFormat == "dd/mm/yy" || c.dateFormat == "dd-mm-yy") {
                        s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})/, "$1/$2/$3")
                    }
                }
            }
            return $.tablesorter.formatFloat(new Date(s).getTime())
        }, type: "numeric"
    });
    ts.addParser({
        id: "time", is: function (s) {
            return /^(([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(am|pm)))$/.test(s)
        }, format: function (s) {
            return $.tablesorter.formatFloat(new Date("2000/01/01 " + s).getTime())
        }, type: "numeric"
    });
    ts.addParser({
        id: "metadata", is: function (s) {
            return false
        }, format: function (s, table, cell) {
            var c = table.config, p = (!c.parserMetadataName) ? "sortValue" : c.parserMetadataName;
            return $(cell).metadata()[p]
        }, type: "numeric"
    });
    ts.addWidget({
        id: "zebra", format: function (table) {
            if (table.config.debug) {
                var time = new Date()
            }
            var $tr, row = -1, odd;
            $("tr:visible", table.tBodies[0]).each(function (i) {
                $tr = $(this);
                if (!$tr.hasClass(table.config.cssChildRow)) {
                    row++
                }
                odd = (row % 2 == 0);
                $tr.removeClass(table.config.widgetZebra.css[odd ? 0 : 1]).addClass(table.config.widgetZebra.css[odd ? 1 : 0])
            });
            if (table.config.debug) {
                $.tablesorter.benchmark("Applying Zebra widget", time)
            }
        }
    })
})(jQuery);
(function (f) {
    var d = {
        init: function (b) {
            var a = this;
            if (!a.data("jqv") || a.data("jqv") == null) {
                b = d._saveOptions(a, b);
                f(document).on("click", ".formError", function () {
                    f(this).fadeOut(150, function () {
                        f(this).parent(".formErrorOuter").remove();
                        f(this).remove()
                    })
                })
            }
            return this
        },
        attach: function (a) {
            var b = this;
            var c;
            if (a) {
                c = d._saveOptions(b, a)
            } else {
                c = b.data("jqv")
            }
            c.validateAttribute = (b.find("[data-validation-engine*=validate]").length) ? "data-validation-engine" : "class";
            if (c.binded) {
                b.on(c.validationEventTrigger, "[" + c.validateAttribute + "*=validate]:not([type=checkbox]):not([type=radio]):not(.datepicker)", d._onFieldEvent);
                b.on("click", "[" + c.validateAttribute + "*=validate][type=checkbox],[" + c.validateAttribute + "*=validate][type=radio]", d._onFieldEvent);
                b.on(c.validationEventTrigger, "[" + c.validateAttribute + "*=validate][class*=datepicker]", {delay: 300}, d._onFieldEvent)
            }
            if (c.autoPositionUpdate) {
                f(window).bind("resize", {
                    noAnimation: true,
                    formElem: b
                }, d.updatePromptsPosition)
            }
            b.on("click", "a[data-validation-engine-skip], a[class*='validate-skip'], button[data-validation-engine-skip], button[class*='validate-skip'], input[data-validation-engine-skip], input[class*='validate-skip']", d._submitButtonClick);
            b.removeData("jqv_submitButton");
            b.on("submit", d._onSubmitEvent);
            return this
        },
        detach: function () {
            var a = this;
            var b = a.data("jqv");
            a.find("[" + b.validateAttribute + "*=validate]").not("[type=checkbox]").off(b.validationEventTrigger, d._onFieldEvent);
            a.find("[" + b.validateAttribute + "*=validate][type=checkbox],[class*=validate][type=radio]").off("click", d._onFieldEvent);
            a.off("submit", d._onSubmitEvent);
            a.removeData("jqv");
            a.off("click", "a[data-validation-engine-skip], a[class*='validate-skip'], button[data-validation-engine-skip], button[class*='validate-skip'], input[data-validation-engine-skip], input[class*='validate-skip']", d._submitButtonClick);
            a.removeData("jqv_submitButton");
            if (b.autoPositionUpdate) {
                f(window).off("resize", d.updatePromptsPosition)
            }
            return this
        },
        validate: function () {
            var c = f(this);
            var a = null;
            if (c.is("form") || c.hasClass("validationEngineContainer")) {
                if (c.hasClass("validating")) {
                    return false
                } else {
                    c.addClass("validating");
                    var h = c.data("jqv");
                    var a = d._validateFields(this);
                    setTimeout(function () {
                        c.removeClass("validating")
                    }, 100);
                    if (a && h.onSuccess) {
                        h.onSuccess()
                    } else {
                        if (!a && h.onFailure) {
                            h.onFailure()
                        }
                    }
                }
            } else {
                if (c.is("form") || c.hasClass("validationEngineContainer")) {
                    c.removeClass("validating")
                } else {
                    var b = c.closest("form, .validationEngineContainer"), h = (b.data("jqv")) ? b.data("jqv") : f.validationEngine.defaults, a = d._validateField(c, h);
                    if (a && h.onFieldSuccess) {
                        h.onFieldSuccess()
                    } else {
                        if (h.onFieldFailure && h.InvalidFields.length > 0) {
                            h.onFieldFailure()
                        }
                    }
                }
            }
            if (h.onValidationComplete) {
                return !!h.onValidationComplete(b, a)
            }
            return a
        },
        updatePromptsPosition: function (a) {
            if (a && this == window) {
                var b = a.data.formElem;
                var h = a.data.noAnimation
            } else {
                var b = f(this.closest("form, .validationEngineContainer"))
            }
            var c = b.data("jqv");
            b.find("[" + c.validateAttribute + "*=validate]").not(":disabled").each(function () {
                var g = f(this);
                if (c.prettySelect && g.is(":hidden")) {
                    g = b.find("#" + c.usePrefix + g.attr("id") + c.useSuffix)
                }
                var n = d._getPrompt(g);
                var m = f(n).find(".formErrorContent").html();
                if (n) {
                    d._updatePrompt(g, f(n), m, undefined, false, c, h)
                }
            });
            return this
        },
        showPrompt: function (m, c, a, l) {
            var b = this.closest("form, .validationEngineContainer");
            var n = b.data("jqv");
            if (!n) {
                n = d._saveOptions(this, n)
            }
            if (a) {
                n.promptPosition = a
            }
            n.showArrow = l == true;
            d._showPrompt(this, m, c, false, n);
            return this
        },
        hide: function () {
            var a = f(this).closest("form, .validationEngineContainer");
            var c = a.data("jqv");
            var b = (c && c.fadeDuration) ? c.fadeDuration : 0.3;
            var h;
            if (f(this).is("form") || f(this).hasClass("validationEngineContainer")) {
                h = "parentForm" + d._getClassName(f(this).attr("id"))
            } else {
                h = d._getClassName(f(this).attr("id")) + "formError"
            }
            f("." + h).fadeTo(b, 0.3, function () {
                f(this).parent(".formErrorOuter").remove();
                f(this).remove()
            });
            return this
        },
        hideAll: function () {
            var b = this;
            var c = b.data("jqv");
            var a = c ? c.fadeDuration : 300;
            f(".formError").fadeTo(a, 300, function () {
                f(this).parent(".formErrorOuter").remove();
                f(this).remove()
            });
            return this
        },
        _onFieldEvent: function (b) {
            var a = f(this);
            var c = a.closest("form, .validationEngineContainer");
            var h = c.data("jqv");
            h.eventTrigger = "field";
            window.setTimeout(function () {
                d._validateField(a, h);
                if (h.InvalidFields.length == 0 && h.onFieldSuccess) {
                    h.onFieldSuccess()
                } else {
                    if (h.InvalidFields.length > 0 && h.onFieldFailure) {
                        h.onFieldFailure()
                    }
                }
            }, (b.data) ? b.data.delay : 0)
        },
        _onSubmitEvent: function () {
            var a = f(this);
            var h = a.data("jqv");
            if (a.data("jqv_submitButton")) {
                var c = f("#" + a.data("jqv_submitButton"));
                if (c) {
                    if (c.length > 0) {
                        if (c.hasClass("validate-skip") || c.attr("data-validation-engine-skip") == "true") {
                            return true
                        }
                    }
                }
            }
            h.eventTrigger = "submit";
            var b = d._validateFields(a);
            if (b && h.ajaxFormValidation) {
                d._validateFormWithAjax(a, h);
                return false
            }
            if (h.onValidationComplete) {
                return !!h.onValidationComplete(a, b)
            }
            return b
        },
        _checkAjaxStatus: function (a) {
            var b = true;
            f.each(a.ajaxValidCache, function (h, c) {
                if (!c) {
                    b = false;
                    return false
                }
            });
            return b
        },
        _checkAjaxFieldStatus: function (b, a) {
            return a.ajaxValidCache[b] == true
        },
        _validateFields: function (y) {
            var b = y.data("jqv");
            var x = false;
            y.trigger("jqv.form.validating");
            var a = null;
            y.find("[" + b.validateAttribute + "*=validate]").not(":disabled").each(function () {
                var g = f(this);
                var h = [];
                if (f.inArray(g.attr("name"), h) < 0) {
                    x |= d._validateField(g, b);
                    if (x && a == null) {
                        if (g.is(":hidden") && b.prettySelect) {
                            a = g = y.find("#" + b.usePrefix + d._jqSelector(g.attr("id")) + b.useSuffix)
                        } else {
                            if (g.data("jqv-prompt-at") instanceof jQuery) {
                                g = g.data("jqv-prompt-at")
                            } else {
                                if (g.data("jqv-prompt-at")) {
                                    g = f(g.data("jqv-prompt-at"))
                                }
                            }
                            a = g
                        }
                    }
                    if (b.doNotShowAllErrosOnSubmit) {
                        return false
                    }
                    h.push(g.attr("name"));
                    if (b.showOneMessage == true && x) {
                        return false
                    }
                }
            });
            y.trigger("jqv.form.result", [x]);
            if (x) {
                if (b.scroll) {
                    var c = a.offset().top;
                    var v = a.offset().left;
                    var s = b.promptPosition;
                    if (typeof(s) == "string" && s.indexOf(":") != -1) {
                        s = s.substring(0, s.indexOf(":"))
                    }
                    if (s != "bottomRight" && s != "bottomLeft") {
                        var u = d._getPrompt(a);
                        if (u) {
                            c = u.offset().top
                        }
                    }
                    if (b.scrollOffset) {
                        c -= b.scrollOffset
                    }
                    if (b.isOverflown) {
                        var B = f(b.overflownDIV);
                        if (!B.length) {
                            return false
                        }
                        var A = B.scrollTop();
                        var w = -parseInt(B.offset().top);
                        c += A + w - 5;
                        var r = f(b.overflownDIV + ":not(:animated)");
                        r.animate({scrollTop: c}, 1100, function () {
                            if (b.focusFirstField) {
                                a.focus()
                            }
                        })
                    } else {
                        f("html, body").animate({scrollTop: c}, 1100, function () {
                            if (b.focusFirstField) {
                                a.focus()
                            }
                        });
                        f("html, body").animate({scrollLeft: v}, 1100)
                    }
                } else {
                    if (b.focusFirstField) {
                        a.focus()
                    }
                }
                return false
            }
            return true
        },
        _validateFormWithAjax: function (b, l) {
            var a = b.serialize();
            var c = (l.ajaxFormValidationMethod) ? l.ajaxFormValidationMethod : "GET";
            var m = (l.ajaxFormValidationURL) ? l.ajaxFormValidationURL : b.attr("action");
            var n = (l.dataType) ? l.dataType : "json";
            f.ajax({
                type: c,
                url: m,
                cache: false,
                dataType: n,
                data: a,
                form: b,
                methods: d,
                options: l,
                beforeSend: function () {
                    return l.onBeforeAjaxFormValidation(b, l)
                },
                error: function (h, g) {
                    d._ajaxError(h, g)
                },
                success: function (w) {
                    if ((n == "json") && (w !== true)) {
                        var y = false;
                        for (var x = 0; x < w.length; x++) {
                            var v = w[x];
                            var k = v[0];
                            var g = f(f("#" + k)[0]);
                            if (g.length == 1) {
                                var u = v[2];
                                if (v[1] == true) {
                                    if (u == "" || !u) {
                                        d._closePrompt(g)
                                    } else {
                                        if (l.allrules[u]) {
                                            var h = l.allrules[u].alertTextOk;
                                            if (h) {
                                                u = h
                                            }
                                        }
                                        if (l.showPrompts) {
                                            d._showPrompt(g, u, "pass", false, l, true)
                                        }
                                    }
                                } else {
                                    y |= true;
                                    if (l.allrules[u]) {
                                        var h = l.allrules[u].alertText;
                                        if (h) {
                                            u = h
                                        }
                                    }
                                    if (l.showPrompts) {
                                        d._showPrompt(g, u, "", false, l, true)
                                    }
                                }
                            }
                        }
                        l.onAjaxFormComplete(!y, b, w, l)
                    } else {
                        l.onAjaxFormComplete(true, b, w, l)
                    }
                }
            })
        },
        _validateField: function (Y, R, F) {
            if (!Y.attr("id")) {
                Y.attr("id", "form-validation-field-" + f.validationEngine.fieldIdCounter);
                ++f.validationEngine.fieldIdCounter
            }
            if (!R.validateNonVisibleFields && (Y.is(":hidden") && !R.prettySelect || Y.parent().is(":hidden"))) {
                return false
            }
            var b = Y.attr(R.validateAttribute);
            var J = /validate\[(.*)\]/.exec(b);
            if (!J) {
                return false
            }
            var a = J[1];
            var G = a.split(/\[|,|\]/);
            var N = false;
            var T = Y.attr("name");
            var U = "";
            var L = "";
            var c = false;
            var H = false;
            R.isError = false;
            R.showArrow = true;
            if (R.maxErrorsPerField > 0) {
                H = true
            }
            var X = f(Y.closest("form, .validationEngineContainer"));
            for (var M = 0; M < G.length; M++) {
                G[M] = G[M].replace(" ", "");
                if (G[M] === "") {
                    delete G[M]
                }
            }
            for (var M = 0, P = 0; M < G.length; M++) {
                if (H && P >= R.maxErrorsPerField) {
                    if (!c) {
                        var S = f.inArray("required", G);
                        c = (S != -1 && S >= M)
                    }
                    break
                }
                var V = undefined;
                switch (G[M]) {
                    case"required":
                        c = true;
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._required);
                        break;
                    case"custom":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._custom);
                        break;
                    case"groupRequired":
                        var O = "[" + R.validateAttribute + "*=" + G[M + 1] + "]";
                        var W = X.find(O).eq(0);
                        if (W[0] != Y[0]) {
                            d._validateField(W, R, F);
                            R.showArrow = true
                        }
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._groupRequired);
                        if (V) {
                            c = true
                        }
                        R.showArrow = false;
                        break;
                    case"ajax":
                        V = d._ajax(Y, G, M, R);
                        if (V) {
                            L = "load"
                        }
                        break;
                    case"minSize":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._minSize);
                        break;
                    case"maxSize":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._maxSize);
                        break;
                    case"min":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._min);
                        break;
                    case"max":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._max);
                        break;
                    case"past":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._past);
                        break;
                    case"future":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._future);
                        break;
                    case"dateRange":
                        var O = "[" + R.validateAttribute + "*=" + G[M + 1] + "]";
                        R.firstOfGroup = X.find(O).eq(0);
                        R.secondOfGroup = X.find(O).eq(1);
                        if (R.firstOfGroup[0].value || R.secondOfGroup[0].value) {
                            V = d._getErrorMessage(X, Y, G[M], G, M, R, d._dateRange)
                        }
                        if (V) {
                            c = true
                        }
                        R.showArrow = false;
                        break;
                    case"dateTimeRange":
                        var O = "[" + R.validateAttribute + "*=" + G[M + 1] + "]";
                        R.firstOfGroup = X.find(O).eq(0);
                        R.secondOfGroup = X.find(O).eq(1);
                        if (R.firstOfGroup[0].value || R.secondOfGroup[0].value) {
                            V = d._getErrorMessage(X, Y, G[M], G, M, R, d._dateTimeRange)
                        }
                        if (V) {
                            c = true
                        }
                        R.showArrow = false;
                        break;
                    case"maxCheckbox":
                        Y = f(X.find("input[name='" + T + "']"));
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._maxCheckbox);
                        break;
                    case"minCheckbox":
                        Y = f(X.find("input[name='" + T + "']"));
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._minCheckbox);
                        break;
                    case"equals":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._equals);
                        break;
                    case"funcCall":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._funcCall);
                        break;
                    case"creditCard":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._creditCard);
                        break;
                    case"condRequired":
                        V = d._getErrorMessage(X, Y, G[M], G, M, R, d._condRequired);
                        if (V !== undefined) {
                            c = true
                        }
                        break;
                    default:
                }
                var Q = false;
                if (typeof V == "object") {
                    switch (V.status) {
                        case"_break":
                            Q = true;
                            break;
                        case"_error":
                            V = V.message;
                            break;
                        case"_error_no_prompt":
                            return true;
                            break;
                        default:
                            break
                    }
                }
                if (Q) {
                    break
                }
                if (typeof V == "string") {
                    U += V + "<br/>";
                    R.isError = true;
                    P++
                }
            }
            if (!c && !(Y.val()) && Y.val().length < 1) {
                R.isError = false
            }
            var K = Y.prop("type");
            var Z = Y.data("promptPosition") || R.promptPosition;
            if ((K == "radio" || K == "checkbox") && X.find("input[name='" + T + "']").size() > 1) {
                if (Z === "inline") {
                    Y = f(X.find("input[name='" + T + "'][type!=hidden]:last"))
                } else {
                    Y = f(X.find("input[name='" + T + "'][type!=hidden]:first"))
                }
                R.showArrow = false
            }
            if (Y.is(":hidden") && R.prettySelect) {
                Y = X.find("#" + R.usePrefix + d._jqSelector(Y.attr("id")) + R.useSuffix)
            }
            if (R.isError && R.showPrompts) {
                d._showPrompt(Y, U, L, false, R)
            } else {
                if (!N) {
                    d._closePrompt(Y)
                }
            }
            if (!N) {
                Y.trigger("jqv.field.result", [Y, R.isError, U])
            }
            var I = f.inArray(Y[0], R.InvalidFields);
            if (I == -1) {
                if (R.isError) {
                    R.InvalidFields.push(Y[0])
                }
            } else {
                if (!R.isError) {
                    R.InvalidFields.splice(I, 1)
                }
            }
            d._handleStatusCssClasses(Y, R);
            if (R.isError && R.onFieldFailure) {
                R.onFieldFailure(Y)
            }
            if (!R.isError && R.onFieldSuccess) {
                R.onFieldSuccess(Y)
            }
            return R.isError
        },
        _handleStatusCssClasses: function (a, b) {
            if (b.addSuccessCssClassToField) {
                a.removeClass(b.addSuccessCssClassToField)
            }
            if (b.addFailureCssClassToField) {
                a.removeClass(b.addFailureCssClassToField)
            }
            if (b.addSuccessCssClassToField && !b.isError) {
                a.addClass(b.addSuccessCssClassToField)
            }
            if (b.addFailureCssClassToField && b.isError) {
                a.addClass(b.addFailureCssClassToField)
            }
        },
        _getErrorMessage: function (E, c, v, a, A, F, u) {
            var x = jQuery.inArray(v, a);
            if (v === "custom" || v === "funcCall") {
                var b = a[x + 1];
                v = v + "[" + b + "]";
                delete (a[x])
            }
            var D = v;
            var C = (c.attr("data-validation-engine")) ? c.attr("data-validation-engine") : c.attr("class");
            var y = C.split(" ");
            var w;
            if (v == "future" || v == "past" || v == "maxCheckbox" || v == "minCheckbox") {
                w = u(E, c, a, A, F)
            } else {
                w = u(c, a, A, F)
            }
            if (w != undefined) {
                var B = d._getCustomErrorMessage(f(c), y, D, F);
                if (B) {
                    w = B
                }
            }
            return w
        },
        _getCustomErrorMessage: function (c, r, o, a) {
            var q = false;
            var s = /^custom\[.*\]$/.test(o) ? d._validityProp.custom : d._validityProp[o];
            if (s != undefined) {
                q = c.attr("data-errormessage-" + s);
                if (q != undefined) {
                    return q
                }
            }
            q = c.attr("data-errormessage");
            if (q != undefined) {
                return q
            }
            var u = "#" + c.attr("id");
            if (typeof a.custom_error_messages[u] != "undefined" && typeof a.custom_error_messages[u][o] != "undefined") {
                q = a.custom_error_messages[u][o]["message"]
            } else {
                if (r.length > 0) {
                    for (var p = 0; p < r.length && r.length > 0; p++) {
                        var b = "." + r[p];
                        if (typeof a.custom_error_messages[b] != "undefined" && typeof a.custom_error_messages[b][o] != "undefined") {
                            q = a.custom_error_messages[b][o]["message"];
                            break
                        }
                    }
                }
            }
            if (!q && typeof a.custom_error_messages[o] != "undefined" && typeof a.custom_error_messages[o]["message"] != "undefined") {
                q = a.custom_error_messages[o]["message"]
            }
            return q
        },
        _validityProp: {
            required: "value-missing",
            custom: "custom-error",
            groupRequired: "value-missing",
            ajax: "custom-error",
            minSize: "range-underflow",
            maxSize: "range-overflow",
            min: "range-underflow",
            max: "range-overflow",
            past: "type-mismatch",
            future: "type-mismatch",
            dateRange: "type-mismatch",
            dateTimeRange: "type-mismatch",
            maxCheckbox: "range-overflow",
            minCheckbox: "range-underflow",
            equals: "pattern-mismatch",
            funcCall: "custom-error",
            creditCard: "pattern-mismatch",
            condRequired: "value-missing"
        },
        _required: function (o, c, q, a, p) {
            switch (o.prop("type")) {
                case"text":
                case"password":
                case"textarea":
                case"file":
                case"select-one":
                case"select-multiple":
                default:
                    var b = f.trim(o.val());
                    var r = f.trim(o.attr("data-validation-placeholder"));
                    if ((!b) || (r && b == r)) {
                        return a.allrules[c[q]].alertText
                    }
                    break;
                case"radio":
                case"checkbox":
                    if (p) {
                        if (!o.attr("checked")) {
                            return a.allrules[c[q]].alertTextCheckboxMultiple
                        }
                        break
                    }
                    var s = o.closest("form, .validationEngineContainer");
                    var u = o.attr("name");
                    if (s.find("input[name='" + u + "']:checked").size() == 0) {
                        if (s.find("input[name='" + u + "']:visible").size() == 1) {
                            return a.allrules[c[q]].alertTextCheckboxe
                        } else {
                            return a.allrules[c[q]].alertTextCheckboxMultiple
                        }
                    }
                    break
            }
        },
        _groupRequired: function (c, a, m, n) {
            var b = "[" + n.validateAttribute + "*=" + a[m + 1] + "]";
            var l = false;
            c.closest("form, .validationEngineContainer").find(b).each(function () {
                if (!d._required(f(this), a, m, n)) {
                    l = true;
                    return false
                }
            });
            if (!l) {
                return n.allrules[a[m]].alertText
            }
        },
        _custom: function (c, b, s, a) {
            var u = b[s + 1];
            var p = a.allrules[u];
            var o;
            if (!p) {
                alert("jqv:custom rule not found - " + u);
                return
            }
            if (p.regex) {
                var q = p.regex;
                if (!q) {
                    alert("jqv:custom regex not found - " + u);
                    return
                }
                var r = new RegExp(q);
                if (!r.test(c.val())) {
                    return a.allrules[u].alertText
                }
            } else {
                if (p.func) {
                    o = p.func;
                    if (typeof(o) !== "function") {
                        alert("jqv:custom parameter 'function' is no function - " + u);
                        return
                    }
                    if (!o(c, b, s, a)) {
                        return a.allrules[u].alertText
                    }
                } else {
                    alert("jqv:custom type not allowed " + u);
                    return
                }
            }
        },
        _funcCall: function (b, a, q, r) {
            var c = a[q + 1];
            var o;
            if (c.indexOf(".") > -1) {
                var n = c.split(".");
                var p = window;
                while (n.length) {
                    p = p[n.shift()]
                }
                o = p
            } else {
                o = window[c] || r.customFunctions[c]
            }
            if (typeof(o) == "function") {
                return o(b, a, q, r)
            }
        },
        _equals: function (b, a, c, k) {
            var l = a[c + 1];
            if (b.val() != f("#" + l).val()) {
                return k.allrules.equals.alertText
            }
        },
        _maxSize: function (b, a, m, n) {
            var o = a[m + 1];
            var p = b.val().length;
            if (p > o) {
                var c = n.allrules.maxSize;
                if (typeof c.alertText2 == "string") {
                    return c.alertText + min + c.alertText2
                } else {
                    return c.alertText
                }
            }
        },
        _minSize: function (b, a, m, o) {
            var n = a[m + 1];
            var p = b.val().length;
            if (p < n) {
                var c = o.allrules.minSize;
                if (typeof c.alertText2 == "string") {
                    return c.alertText + n + c.alertText2
                } else {
                    return c.alertText
                }
            }
        },
        _min: function (b, a, m, o) {
            var n = parseFloat(a[m + 1]);
            var p = parseFloat(b.val());
            if (p < n) {
                var c = o.allrules.min;
                if (c.alertText2) {
                    return c.alertText + n + c.alertText2
                }
                return c.alertText + n
            }
        },
        _max: function (b, a, m, n) {
            var o = parseFloat(a[m + 1]);
            var p = parseFloat(b.val());
            if (p > o) {
                var c = n.allrules.max;
                if (c.alertText2) {
                    return c.alertText + o + c.alertText2
                }
                return c.alertText + o
            }
        },
        _past: function (v, p, c, u, a) {
            var w = c[u + 1];
            var r = f(v.find("input[name='" + w.replace(/^#+/, "") + "']"));
            var s;
            if (w.toLowerCase() == "now") {
                s = new Date()
            } else {
                if (undefined != r.val()) {
                    if (r.is(":disabled")) {
                        return
                    }
                    s = d._parseDate(r.val())
                } else {
                    s = d._parseDate(w)
                }
            }
            var b = d._parseDate(p.val());
            if (b > s) {
                var q = a.allrules.past;
                if (q.alertText2) {
                    return q.alertText + d._dateToString(s) + q.alertText2
                }
                return q.alertText + d._dateToString(s)
            }
        },
        _future: function (v, p, c, u, a) {
            var w = c[u + 1];
            var r = f(v.find("input[name='" + w.replace(/^#+/, "") + "']"));
            var s;
            if (w.toLowerCase() == "now") {
                s = new Date()
            } else {
                if (undefined != r.val()) {
                    if (r.is(":disabled")) {
                        return
                    }
                    s = d._parseDate(r.val())
                } else {
                    s = d._parseDate(w)
                }
            }
            var b = d._parseDate(p.val());
            if (b < s) {
                var q = a.allrules.future;
                if (q.alertText2) {
                    return q.alertText + d._dateToString(s) + q.alertText2
                }
                return q.alertText + d._dateToString(s)
            }
        },
        _isDate: function (a) {
            var b = new RegExp(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/);
            return b.test(a)
        },
        _isDateTime: function (a) {
            var b = new RegExp(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1})$/);
            return b.test(a)
        },
        _dateCompare: function (a, b) {
            return (new Date(a.toString()) < new Date(b.toString()))
        },
        _dateRange: function (b, a, c, h) {
            if ((!h.firstOfGroup[0].value && h.secondOfGroup[0].value) || (h.firstOfGroup[0].value && !h.secondOfGroup[0].value)) {
                return h.allrules[a[c]].alertText + h.allrules[a[c]].alertText2
            }
            if (!d._isDate(h.firstOfGroup[0].value) || !d._isDate(h.secondOfGroup[0].value)) {
                return h.allrules[a[c]].alertText + h.allrules[a[c]].alertText2
            }
            if (!d._dateCompare(h.firstOfGroup[0].value, h.secondOfGroup[0].value)) {
                return h.allrules[a[c]].alertText + h.allrules[a[c]].alertText2
            }
        },
        _dateTimeRange: function (b, a, c, h) {
            if ((!h.firstOfGroup[0].value && h.secondOfGroup[0].value) || (h.firstOfGroup[0].value && !h.secondOfGroup[0].value)) {
                return h.allrules[a[c]].alertText + h.allrules[a[c]].alertText2
            }
            if (!d._isDateTime(h.firstOfGroup[0].value) || !d._isDateTime(h.secondOfGroup[0].value)) {
                return h.allrules[a[c]].alertText + h.allrules[a[c]].alertText2
            }
            if (!d._dateCompare(h.firstOfGroup[0].value, h.secondOfGroup[0].value)) {
                return h.allrules[a[c]].alertText + h.allrules[a[c]].alertText2
            }
        },
        _maxCheckbox: function (c, b, a, n, o) {
            var q = a[n + 1];
            var p = b.attr("name");
            var r = c.find("input[name='" + p + "']:checked").size();
            if (r > q) {
                o.showArrow = false;
                if (o.allrules.maxCheckbox.alertText2) {
                    return o.allrules.maxCheckbox.alertText + " " + q + " " + o.allrules.maxCheckbox.alertText2
                }
                return o.allrules.maxCheckbox.alertText
            }
        },
        _minCheckbox: function (c, b, a, n, o) {
            var q = a[n + 1];
            var p = b.attr("name");
            var r = c.find("input[name='" + p + "']:checked").size();
            if (r < q) {
                o.showArrow = false;
                return o.allrules.minCheckbox.alertText + " " + q + " " + o.allrules.minCheckbox.alertText2
            }
        },
        _creditCard: function (q, c, v, a) {
            var x = false, b = q.val().replace(/ +/g, "").replace(/-+/g, "");
            var y = b.length;
            if (y >= 14 && y <= 16 && parseInt(b) > 0) {
                var u = 0, v = y - 1, r = 1, s, w = new String();
                do {
                    s = parseInt(b.charAt(v));
                    w += (r++ % 2 == 0) ? s * 2 : s
                } while (--v >= 0);
                for (v = 0; v < w.length; v++) {
                    u += parseInt(w.charAt(v))
                }
                x = u % 10 == 0
            }
            if (!x) {
                return a.allrules.creditCard.alertText
            }
        },
        _ajax: function (b, I, A, H) {
            var J = I[A + 1];
            var c = H.allrules[J];
            var E = c.extraData;
            var x = c.extraDataDynamic;
            var B = {fieldId: b.attr("id"), fieldValue: b.val()};
            if (typeof E === "object") {
                f.extend(B, E)
            } else {
                if (typeof E === "string") {
                    var y = E.split("&");
                    for (var A = 0; A < y.length; A++) {
                        var a = y[A].split("=");
                        if (a[0] && a[0]) {
                            B[a[0]] = a[1]
                        }
                    }
                }
            }
            if (x) {
                var C = [];
                var w = String(x).split(",");
                for (var A = 0; A < w.length; A++) {
                    var G = w[A];
                    if (f(G).length) {
                        var F = b.closest("form, .validationEngineContainer").find(G).val();
                        var D = G.replace("#", "") + "=" + escape(F);
                        B[G.replace("#", "")] = F
                    }
                }
            }
            if (H.eventTrigger == "field") {
                delete (H.ajaxValidCache[b.attr("id")])
            }
            if (!H.isError && !d._checkAjaxFieldStatus(b.attr("id"), H)) {
                f.ajax({
                    type: H.ajaxFormValidationMethod,
                    url: c.url,
                    cache: false,
                    dataType: "json",
                    data: B,
                    field: b,
                    rule: c,
                    methods: d,
                    options: H,
                    beforeSend: function () {
                    },
                    error: function (h, g) {
                        d._ajaxError(h, g)
                    },
                    success: function (h) {
                        var l = h[0];
                        var n = f("#" + l).eq(0);
                        if (n.length == 1) {
                            var g = h[1];
                            var m = h[2];
                            if (!g) {
                                H.ajaxValidCache[l] = false;
                                H.isError = true;
                                if (m) {
                                    if (H.allrules[m]) {
                                        var k = H.allrules[m].alertText;
                                        if (k) {
                                            m = k
                                        }
                                    }
                                } else {
                                    m = c.alertText
                                }
                                if (H.showPrompts) {
                                    d._showPrompt(n, m, "", true, H)
                                }
                            } else {
                                H.ajaxValidCache[l] = true;
                                if (m) {
                                    if (H.allrules[m]) {
                                        var k = H.allrules[m].alertTextOk;
                                        if (k) {
                                            m = k
                                        }
                                    }
                                } else {
                                    m = c.alertTextOk
                                }
                                if (H.showPrompts) {
                                    if (m) {
                                        d._showPrompt(n, m, "pass", true, H)
                                    } else {
                                        d._closePrompt(n)
                                    }
                                }
                                if (H.eventTrigger == "submit") {
                                    b.closest("form").submit()
                                }
                            }
                        }
                        n.trigger("jqv.field.result", [n, H.isError, m])
                    }
                });
                return c.alertTextLoad
            }
        },
        _ajaxError: function (b, a) {
            if (b.status == 0 && a == null) {
                alert("The page is not served from a server! ajax call failed")
            } else {
                if (typeof console != "undefined") {
                    console.log("Ajax error: " + b.status + " " + a)
                }
            }
        },
        _dateToString: function (a) {
            return a.getFullYear() + "-" + (a.getMonth() + 1) + "-" + a.getDate()
        },
        _parseDate: function (a) {
            var b = a.split("-");
            if (b == a) {
                b = a.split("/")
            }
            if (b == a) {
                b = a.split(".");
                return new Date(b[2], (b[1] - 1), b[0])
            }
            return new Date(b[0], (b[1] - 1), b[2])
        },
        _showPrompt: function (a, c, b, m, n, o) {
            if (a.data("jqv-prompt-at") instanceof jQuery) {
                a = a.data("jqv-prompt-at")
            } else {
                if (a.data("jqv-prompt-at")) {
                    a = f(a.data("jqv-prompt-at"))
                }
            }
            var p = d._getPrompt(a);
            if (o) {
                p = false
            }
            if (f.trim(c)) {
                if (p) {
                    d._updatePrompt(a, p, c, b, m, n)
                } else {
                    d._buildPrompt(a, c, b, m, n)
                }
            }
        },
        _buildPrompt: function (u, B, w, r, b) {
            var A = f("<div>");
            A.addClass(d._getClassName(u.attr("id")) + "formError");
            A.addClass("parentForm" + d._getClassName(u.closest("form, .validationEngineContainer").attr("id")));
            A.addClass("formError");
            switch (w) {
                case"pass":
                    A.addClass("greenPopup");
                    break;
                case"load":
                    A.addClass("blackPopup");
                    break;
                default:
            }
            if (r) {
                A.addClass("ajaxed")
            }
            var a = f("<div>").addClass("formErrorContent").html(B).appendTo(A);
            var x = u.data("promptPosition") || b.promptPosition;
            if (b.showArrow) {
                var s = f("<div>").addClass("formErrorArrow");
                if (typeof(x) == "string") {
                    var v = x.indexOf(":");
                    if (v != -1) {
                        x = x.substring(0, v)
                    }
                }
                switch (x) {
                    case"bottomLeft":
                    case"bottomRight":
                        A.find(".formErrorContent").before(s);
                        s.addClass("formErrorArrowBottom").html('<div class="line1"><!-- --></div><div class="line2"><!-- --></div><div class="line3"><!-- --></div><div class="line4"><!-- --></div><div class="line5"><!-- --></div><div class="line6"><!-- --></div><div class="line7"><!-- --></div><div class="line8"><!-- --></div><div class="line9"><!-- --></div><div class="line10"><!-- --></div>');
                        break;
                    case"topLeft":
                    case"topRight":
                        s.html('<div class="line10"><!-- --></div><div class="line9"><!-- --></div><div class="line8"><!-- --></div><div class="line7"><!-- --></div><div class="line6"><!-- --></div><div class="line5"><!-- --></div><div class="line4"><!-- --></div><div class="line3"><!-- --></div><div class="line2"><!-- --></div><div class="line1"><!-- --></div>');
                        A.append(s);
                        break
                }
            }
            if (b.addPromptClass) {
                A.addClass(b.addPromptClass)
            }
            var c = u.attr("data-required-class");
            if (c !== undefined) {
                A.addClass(c)
            } else {
                if (b.prettySelect) {
                    if (f("#" + u.attr("id")).next().is("select")) {
                        var y = f("#" + u.attr("id").substr(b.usePrefix.length).substring(b.useSuffix.length)).attr("data-required-class");
                        if (y !== undefined) {
                            A.addClass(y)
                        }
                    }
                }
            }
            A.css({opacity: 0});
            if (x === "inline") {
                A.addClass("inline");
                if (typeof u.attr("data-prompt-target") !== "undefined" && f("#" + u.attr("data-prompt-target")).length > 0) {
                    A.appendTo(f("#" + u.attr("data-prompt-target")))
                } else {
                    u.after(A)
                }
            } else {
                u.before(A)
            }
            var v = d._calculatePosition(u, A, b);
            A.css({
                position: x === "inline" ? "relative" : "absolute",
                top: v.callerTopPosition,
                left: v.callerleftPosition,
                marginTop: v.marginTopSize,
                opacity: 0
            }).data("callerField", u);
            if (b.autoHidePrompt) {
                setTimeout(function () {
                    A.animate({opacity: 0}, function () {
                        A.closest(".formErrorOuter").remove();
                        A.remove()
                    })
                }, b.autoHideDelay)
            }
            return A.animate({opacity: 0.87})
        },
        _updatePrompt: function (c, s, u, p, b, a, r) {
            if (s) {
                if (typeof p !== "undefined") {
                    if (p == "pass") {
                        s.addClass("greenPopup")
                    } else {
                        s.removeClass("greenPopup")
                    }
                    if (p == "load") {
                        s.addClass("blackPopup")
                    } else {
                        s.removeClass("blackPopup")
                    }
                }
                if (b) {
                    s.addClass("ajaxed")
                } else {
                    s.removeClass("ajaxed")
                }
                s.find(".formErrorContent").html(u);
                var o = d._calculatePosition(c, s, a);
                var q = {
                    top: o.callerTopPosition,
                    left: o.callerleftPosition,
                    marginTop: o.marginTopSize
                };
                if (r) {
                    s.css(q)
                } else {
                    s.animate(q)
                }
            }
        },
        _closePrompt: function (a) {
            var b = d._getPrompt(a);
            if (b) {
                b.fadeTo("fast", 0, function () {
                    b.parent(".formErrorOuter").remove();
                    b.remove()
                })
            }
        },
        closePrompt: function (a) {
            return d._closePrompt(a)
        },
        _getPrompt: function (b) {
            var a = f(b).closest("form, .validationEngineContainer").attr("id");
            var c = d._getClassName(b.attr("id")) + "formError";
            var h = f("." + d._escapeExpression(c) + ".parentForm" + d._getClassName(a))[0];
            if (h) {
                return f(h)
            }
        },
        _escapeExpression: function (a) {
            return a.replace(/([#;&,\.\+\*\~':"\!\^$\[\]\(\)=>\|])/g, "\\$1")
        },
        isRTL: function (b) {
            var a = f(document);
            var h = f("body");
            var c = (b && b.hasClass("rtl")) || (b && (b.attr("dir") || "").toLowerCase() === "rtl") || a.hasClass("rtl") || (a.attr("dir") || "").toLowerCase() === "rtl" || h.hasClass("rtl") || (h.attr("dir") || "").toLowerCase() === "rtl";
            return Boolean(c)
        },
        _calculatePosition: function (c, D, H) {
            var E, b, x;
            var C = c.width();
            var G = c.position().left;
            var J = c.position().top;
            var F = c.height();
            var I = D.height();
            E = b = 0;
            x = -I;
            var y = c.data("promptPosition") || H.promptPosition;
            var A = "";
            var B = "";
            var a = 0;
            var w = 0;
            if (typeof(y) == "string") {
                if (y.indexOf(":") != -1) {
                    A = y.substring(y.indexOf(":") + 1);
                    y = y.substring(0, y.indexOf(":"));
                    if (A.indexOf(",") != -1) {
                        B = A.substring(A.indexOf(",") + 1);
                        A = A.substring(0, A.indexOf(","));
                        w = parseInt(B);
                        if (isNaN(w)) {
                            w = 0
                        }
                    }
                    a = parseInt(A);
                    if (isNaN(A)) {
                        A = 0
                    }
                }
            }
            switch (y) {
                default:
                case"topRight":
                    b += G + C - 30;
                    E += J;
                    break;
                case"topLeft":
                    E += J;
                    b += G;
                    break;
                case"centerRight":
                    E = J + 4;
                    x = 0;
                    b = G + c.outerWidth(true) + 5;
                    break;
                case"centerLeft":
                    b = G - (D.width() + 2);
                    E = J + 4;
                    x = 0;
                    break;
                case"bottomLeft":
                    E = J + c.height() + 5;
                    x = 0;
                    b = G;
                    break;
                case"bottomRight":
                    b = G + C - 30;
                    E = J + c.height() + 5;
                    x = 0;
                    break;
                case"inline":
                    b = 0;
                    E = 0;
                    x = 0
            }
            b += a;
            E += w;
            return {
                callerTopPosition: E + "px",
                callerleftPosition: b + "px",
                marginTopSize: x + "px"
            }
        },
        _saveOptions: function (b, c) {
            if (f.validationEngineLanguage) {
                var h = f.validationEngineLanguage.allRules
            } else {
                f.error("jQuery.validationEngine rules are not loaded, plz add localization files to the page")
            }
            f.validationEngine.defaults.allrules = h;
            var a = f.extend(true, {}, f.validationEngine.defaults, c);
            b.data("jqv", a);
            return a
        },
        _getClassName: function (a) {
            if (a) {
                return a.replace(/:/g, "_").replace(/\./g, "_")
            }
        },
        _jqSelector: function (a) {
            return a.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, "\\$1")
        },
        _condRequired: function (b, a, l, m) {
            var n, c;
            for (n = (l + 1); n < a.length; n++) {
                c = jQuery("#" + a[n]).first();
                if (c.length && d._required(c, ["required"], 0, m, true) == undefined) {
                    return d._required(b, ["required"], 0, m)
                }
            }
        },
        _submitButtonClick: function (a) {
            var c = f(this);
            var b = c.closest("form, .validationEngineContainer");
            b.data("jqv_submitButton", c.attr("id"))
        }
    };
    f.fn.validationEngine = function (a) {
        var b = f(this);
        if (!b[0]) {
            return b
        }
        if (typeof(a) == "string" && a.charAt(0) != "_" && d[a]) {
            if (a != "showPrompt" && a != "hide" && a != "hideAll") {
                d.init.apply(b)
            }
            return d[a].apply(b, Array.prototype.slice.call(arguments, 1))
        } else {
            if (typeof a == "object" || !a) {
                d.init.apply(b, arguments);
                return d.attach.apply(b)
            } else {
                f.error("Method " + a + " does not exist in jQuery.validationEngine")
            }
        }
    };
    f.validationEngine = {
        fieldIdCounter: 0,
        defaults: {
            validationEventTrigger: "blur",
            scroll: true,
            focusFirstField: true,
            showPrompts: true,
            validateNonVisibleFields: false,
            promptPosition: "topRight",
            bindMethod: "bind",
            inlineAjax: false,
            ajaxFormValidation: false,
            ajaxFormValidationURL: false,
            ajaxFormValidationMethod: "get",
            onAjaxFormComplete: f.noop,
            onBeforeAjaxFormValidation: f.noop,
            onValidationComplete: false,
            doNotShowAllErrosOnSubmit: false,
            custom_error_messages: {},
            binded: true,
            showArrow: true,
            isError: false,
            maxErrorsPerField: false,
            ajaxValidCache: {},
            autoPositionUpdate: false,
            InvalidFields: [],
            onFieldSuccess: false,
            onFieldFailure: false,
            onSuccess: false,
            onFailure: false,
            validateAttribute: "class",
            addSuccessCssClassToField: "",
            addFailureCssClassToField: "",
            autoHidePrompt: false,
            autoHideDelay: 10000,
            fadeDuration: 0.3,
            prettySelect: false,
            addPromptClass: "",
            usePrefix: "",
            useSuffix: "",
            showOneMessage: false
        }
    };
    var e = {
        hook: "rightmiddle",
        hideOn: false,
        skin: "cloud",
        hideOthers: false
    };
    d._buildPrompt = function (a, c, b, k, l) {
        a.data("promptText", c);
        Tipped.create(a[0], c, e);
        Tipped.show(a[0])
    };
    d._closePrompt = function (a) {
        a.data("promptText", "");
        Tipped.remove(a[0])
    };
    d._updatePrompt = function (a, p, c, b, m, n, o) {
        if (a.data("promptText") != c) {
            d._closePrompt(a);
            d._buildPrompt(a, c)
        }
    };
    d._getPrompt = function (a) {
        return Tipped.get(a[0])
    };
    f(function () {
        f.validationEngine.defaults.promptPosition = d.isRTL() ? "topLeft" : "topRight"
    })
})(jQuery);
(function (b) {
    b.fn.wipetouch = function (d) {
        var a = {
            moveX: 40,
            moveY: 40,
            tapToClick: false,
            preventDefault: true,
            allowDiagonal: false,
            preventDefaultWhenTriggering: true,
            wipeLeft: false,
            wipeRight: false,
            wipeUp: false,
            wipeDown: false,
            wipeUpLeft: false,
            wipeDownLeft: false,
            wipeUpRight: false,
            wipeDownRight: false,
            wipeMove: false,
            wipeTopLeft: false,
            wipeBottomLeft: false,
            wipeTopRight: false,
            wipeBottomRight: false
        };
        if (d) {
            b.extend(a, d)
        }
        this.each(function () {
            var B;
            var C;
            var E = false;
            var F;
            var G;
            var x = false;
            var I = false;
            var J = false;
            var c = false;

            function D(f) {
                A();
                var e = J || (f.originalEvent.touches && f.originalEvent.touches.length > 0);
                if (!x && e) {
                    if (a.preventDefault) {
                        f.preventDefault()
                    }
                    if (a.allowDiagonal) {
                        if (!a.wipeDownLeft) {
                            a.wipeDownLeft = a.wipeBottomLeft
                        }
                        if (!a.wipeDownRight) {
                            a.wipeDownRight = a.wipeBottomRight
                        }
                        if (!a.wipeUpLeft) {
                            a.wipeUpLeft = a.wipeTopLeft
                        }
                        if (!a.wipeUpRight) {
                            a.wipeUpRight = a.wipeTopRight
                        }
                    }
                    if (J) {
                        B = f.pageX;
                        C = f.pageY;
                        b(this).bind("mousemove", H);
                        b(this).one("mouseup", v)
                    } else {
                        B = f.originalEvent.touches[0].pageX;
                        C = f.originalEvent.touches[0].pageY;
                        b(this).bind("touchmove", H)
                    }
                    E = new Date().getTime();
                    F = B;
                    G = C;
                    x = true;
                    I = b(f.target)
                }
            }

            function v(e) {
                if (a.preventDefault) {
                    e.preventDefault()
                }
                if (J) {
                    b(this).unbind("mousemove", H)
                } else {
                    b(this).unbind("touchmove", H)
                }
                if (x) {
                    y(e)
                } else {
                    A()
                }
            }

            function H(e) {
                if (a.preventDefault) {
                    e.preventDefault()
                }
                if (J && !x) {
                    D(e)
                }
                if (x) {
                    if (J) {
                        F = e.pageX;
                        G = e.pageY
                    } else {
                        F = e.originalEvent.touches[0].pageX;
                        G = e.originalEvent.touches[0].pageY
                    }
                    if (a.wipeMove) {
                        w(a.wipeMove, {curX: F, curY: G})
                    }
                }
            }

            function y(f) {
                var g = new Date().getTime();
                var p = E - g;
                var N = F;
                var e = G;
                var k = N - B;
                var o = e - C;
                var r = Math.abs(k);
                var l = Math.abs(o);
                if (r < 15 && l < 15 && p < 100) {
                    c = false;
                    if (a.preventDefault) {
                        A();
                        I.trigger("click");
                        return
                    }
                } else {
                    if (J) {
                        var q = I.data("events");
                        if (q) {
                            var s = q.click;
                            if (s && s.length > 0) {
                                b.each(s, function (K, L) {
                                    c = L;
                                    return
                                });
                                I.unbind("click")
                            }
                        }
                    }
                }
                var u = k > 0;
                var h = o > 0;
                var m = ((r + l) * 60) / ((p) / 6 * (p));
                if (m < 1) {
                    m = 1
                }
                if (m > 5) {
                    m = 5
                }
                var n = {speed: parseInt(m), x: r, y: l, source: I};
                if (r >= a.moveX) {
                    if (a.allowDiagonal && l >= a.moveY) {
                        if (u && h) {
                            w(a.wipeDownRight, n, f)
                        } else {
                            if (u && !h) {
                                w(a.wipeUpRight, n, f)
                            } else {
                                if (!u && h) {
                                    w(a.wipeDownLeft, n, f)
                                } else {
                                    w(a.wipeUpLeft, n, f)
                                }
                            }
                        }
                    } else {
                        if (r >= l) {
                            if (u) {
                                w(a.wipeRight, n, f)
                            } else {
                                w(a.wipeLeft, n, f)
                            }
                        }
                    }
                } else {
                    if (l >= a.moveY && l > r) {
                        if (h) {
                            w(a.wipeDown, n, f)
                        } else {
                            w(a.wipeUp, n, f)
                        }
                    }
                }
                A()
            }

            function A() {
                B = false;
                C = false;
                E = false;
                x = false;
                if (c) {
                    window.setTimeout(function () {
                        I.bind("click", c);
                        c = false
                    }, 50)
                }
            }

            function w(e, g, f) {
                if (e) {
                    if (a.preventDefaultWhenTriggering) {
                        f.preventDefault()
                    }
                    e(g)
                }
            }

            if ("ontouchstart" in document.documentElement) {
                b(this).bind("touchstart", D);
                b(this).bind("touchend", v)
            } else {
                J = true;
                b(this).bind("mousedown", D);
                b(this).bind("mouseout mouseup", v)
            }
        });
        return this
    }
})(jQuery);
!function (b) {
    "function" == typeof define && define.amd ? define(["./jquery.js"], b) : "undefined" != typeof module && module.exports ? module.exports = b : b(jQuery, window, document)
}(function (b) {
    !function (k) {
        var h = "function" == typeof define && define.amd, a = "undefined" != typeof module && module.exports, g = "https:" == document.location.protocol ? "https:" : "http:", l = "cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js";
        h || (a ? require("jquery-mousewheel")(b) : b.event.special.mousewheel || b("head").append(decodeURI("%3Cscript src=" + g + "//" + l + "%3E%3C/script%3E"))), k()
    }(function () {
        var R, M = "mCustomScrollbar", af = "mCS", L = ".mCustomScrollbar", G = {
            setTop: 0,
            setLeft: 0,
            axis: "y",
            scrollbarPosition: "inside",
            scrollInertia: 950,
            autoDraggerLength: !0,
            alwaysShowScrollbar: 0,
            snapOffset: 0,
            mouseWheel: {
                enable: !0,
                scrollAmount: "auto",
                axis: "y",
                deltaFactor: "auto",
                disableOver: ["select", "option", "keygen", "datalist", "textarea"]
            },
            scrollButtons: {scrollType: "stepless", scrollAmount: "auto"},
            keyboard: {
                enable: !0,
                scrollType: "stepless",
                scrollAmount: "auto"
            },
            contentTouchScroll: 25,
            documentTouchScroll: !0,
            advanced: {
                autoScrollOnFocus: "input,textarea,select,button,datalist,keygen,a[tabindex],area,object,[contenteditable='true']",
                updateOnContentResize: !0,
                updateOnImageLoad: "auto",
                autoUpdateTimeout: 60
            },
            theme: "light",
            callbacks: {
                onTotalScrollOffset: 0,
                onTotalScrollBackOffset: 0,
                alwaysTriggerOffsets: !0
            }
        }, P = 0, J = {}, Q = window.attachEvent && !window.addEventListener ? 1 : 0, ai = !1, aj = ["mCSB_dragger_onDrag", "mCSB_scrollTools_onDrag", "mCS_img_loaded", "mCS_disabled", "mCS_destroyed", "mCS_no_scrollbar", "mCS-autoHide", "mCS-dir-rtl", "mCS_no_scrollbar_y", "mCS_no_scrollbar_x", "mCS_y_hidden", "mCS_x_hidden", "mCSB_draggerContainer", "mCSB_buttonUp", "mCSB_buttonDown", "mCSB_buttonLeft", "mCSB_buttonRight"], S = {
            init: function (e) {
                var e = b.extend(!0, {}, G, e), an = ak.call(this);
                if (e.live) {
                    var ao = e.liveSelector || this.selector || L, am = b(ao);
                    if ("off" === e.live) {
                        return void K(ao)
                    }
                    J[ao] = setTimeout(function () {
                        am.mCustomScrollbar(e), "once" === e.live && am.length && K(ao)
                    }, 500)
                } else {
                    K(ao)
                }
                return e.setWidth = e.set_width ? e.set_width : e.setWidth, e.setHeight = e.set_height ? e.set_height : e.setHeight, e.axis = e.horizontalScroll ? "x" : N(e.axis), e.scrollInertia = e.scrollInertia > 0 && e.scrollInertia < 17 ? 17 : e.scrollInertia, "object" != typeof e.mouseWheel && 1 == e.mouseWheel && (e.mouseWheel = {
                    enable: !0,
                    scrollAmount: "auto",
                    axis: "y",
                    preventDefault: !1,
                    deltaFactor: "auto",
                    normalizeDelta: !1,
                    invert: !1
                }), e.mouseWheel.scrollAmount = e.mouseWheelPixels ? e.mouseWheelPixels : e.mouseWheel.scrollAmount, e.mouseWheel.normalizeDelta = e.advanced.normalizeMouseWheelDelta ? e.advanced.normalizeMouseWheelDelta : e.mouseWheel.normalizeDelta, e.scrollButtons.scrollType = E(e.scrollButtons.scrollType), F(e), b(an).each(function () {
                    var av = b(this);
                    if (!av.data(af)) {
                        av.data(af, {
                            idx: ++P,
                            opt: e,
                            scrollRatio: {y: null, x: null},
                            overflowed: null,
                            contentReset: {y: null, x: null},
                            bindEvents: !1,
                            tweenRunning: !1,
                            sequential: {},
                            langDir: av.css("direction"),
                            cbOffsets: null,
                            trigger: null,
                            poll: {
                                size: {o: 0, n: 0},
                                img: {o: 0, n: 0},
                                change: {o: 0, n: 0}
                            }
                        });
                        var at = av.data(af), aq = at.opt, ar = av.data("mcs-axis"), ap = av.data("mcs-scrollbar-position"), au = av.data("mcs-theme");
                        ar && (aq.axis = ar), ap && (aq.scrollbarPosition = ap), au && (aq.theme = au, F(aq)), T.call(this), at && aq.callbacks.onCreate && "function" == typeof aq.callbacks.onCreate && aq.callbacks.onCreate.call(this), b("#mCSB_" + at.idx + "_container img:not(." + aj[2] + ")").addClass(aj[2]), S.update.call(null, av)
                    }
                })
            }, update: function (e, an) {
                var am = e || ak.call(this);
                return b(am).each(function () {
                    var aq = b(this);
                    if (aq.data(af)) {
                        var au = aq.data(af), ar = au.opt, ao = b("#mCSB_" + au.idx + "_container"), at = b("#mCSB_" + au.idx), ap = [b("#mCSB_" + au.idx + "_dragger_vertical"), b("#mCSB_" + au.idx + "_dragger_horizontal")];
                        if (!ao.length) {
                            return
                        }
                        au.tweenRunning && r(aq), an && au && ar.callbacks.onBeforeUpdate && "function" == typeof ar.callbacks.onBeforeUpdate && ar.callbacks.onBeforeUpdate.call(this), aq.hasClass(aj[3]) && aq.removeClass(aj[3]), aq.hasClass(aj[4]) && aq.removeClass(aj[4]), at.css("max-height", "none"), at.height() !== aq.height() && at.css("max-height", aq.height()), ae.call(this), "y" === ar.axis || ar.advanced.autoExpandHorizontalScroll || ao.css("width", V(ao)), au.overflowed = W.call(this), n.call(this), ar.autoDraggerLength && u.call(this), ah.call(this), v.call(this);
                        var av = [Math.abs(ao[0].offsetTop), Math.abs(ao[0].offsetLeft)];
                        "x" !== ar.axis && (au.overflowed[0] ? ap[0].height() > ap[0].parent().height() ? ab.call(this) : (f(aq, av[0].toString(), {
                            dir: "y",
                            dur: 0,
                            overwrite: "none"
                        }), au.contentReset.y = null) : (ab.call(this), "y" === ar.axis ? I.call(this) : "yx" === ar.axis && au.overflowed[1] && f(aq, av[1].toString(), {
                            dir: "x",
                            dur: 0,
                            overwrite: "none"
                        }))), "y" !== ar.axis && (au.overflowed[1] ? ap[1].width() > ap[1].parent().width() ? ab.call(this) : (f(aq, av[1].toString(), {
                            dir: "x",
                            dur: 0,
                            overwrite: "none"
                        }), au.contentReset.x = null) : (ab.call(this), "x" === ar.axis ? I.call(this) : "yx" === ar.axis && au.overflowed[0] && f(aq, av[0].toString(), {
                            dir: "y",
                            dur: 0,
                            overwrite: "none"
                        }))), an && au && (2 === an && ar.callbacks.onImageLoad && "function" == typeof ar.callbacks.onImageLoad ? ar.callbacks.onImageLoad.call(this) : 3 === an && ar.callbacks.onSelectorChange && "function" == typeof ar.callbacks.onSelectorChange ? ar.callbacks.onSelectorChange.call(this) : ar.callbacks.onUpdate && "function" == typeof ar.callbacks.onUpdate && ar.callbacks.onUpdate.call(this)), o.call(this)
                    }
                })
            }, scrollTo: function (e, an) {
                if ("undefined" != typeof e && null != e) {
                    var am = ak.call(this);
                    return b(am).each(function () {
                        var au = b(this);
                        if (au.data(af)) {
                            var ar = au.data(af), ap = ar.opt, at = {
                                trigger: "external",
                                scrollInertia: ap.scrollInertia,
                                scrollEasing: "mcsEaseInOut",
                                moveDragger: !1,
                                timeout: 60,
                                callbacks: !0,
                                onStart: !0,
                                onUpdate: !0,
                                onComplete: !0
                            }, aq = b.extend(!0, {}, at, an), av = C.call(this, e), ao = aq.scrollInertia > 0 && aq.scrollInertia < 17 ? 17 : aq.scrollInertia;
                            av[0] = B.call(this, av[0], "y"), av[1] = B.call(this, av[1], "x"), aq.moveDragger && (av[0] *= ar.scrollRatio.y, av[1] *= ar.scrollRatio.x), aq.dur = al() ? 0 : ao, setTimeout(function () {
                                null !== av[0] && "undefined" != typeof av[0] && "x" !== ap.axis && ar.overflowed[0] && (aq.dir = "y", aq.overwrite = "all", f(au, av[0].toString(), aq)), null !== av[1] && "undefined" != typeof av[1] && "y" !== ap.axis && ar.overflowed[1] && (aq.dir = "x", aq.overwrite = "none", f(au, av[1].toString(), aq))
                            }, aq.timeout)
                        }
                    })
                }
            }, stop: function () {
                var e = ak.call(this);
                return b(e).each(function () {
                    var am = b(this);
                    am.data(af) && r(am)
                })
            }, disable: function (e) {
                var am = ak.call(this);
                return b(am).each(function () {
                    var an = b(this);
                    if (an.data(af)) {
                        an.data(af);
                        o.call(this, "remove"), I.call(this), e && ab.call(this), n.call(this, !0), an.addClass(aj[3])
                    }
                })
            }, destroy: function () {
                var e = ak.call(this);
                return b(e).each(function () {
                    var am = b(this);
                    if (am.data(af)) {
                        var aq = am.data(af), ao = aq.opt, ar = b("#mCSB_" + aq.idx), ap = b("#mCSB_" + aq.idx + "_container"), an = b(".mCSB_" + aq.idx + "_scrollbar");
                        ao.live && K(ao.liveSelector || b(e).selector), o.call(this, "remove"), I.call(this), ab.call(this), am.removeData(af), Z(this, "mcs"), an.remove(), ap.find("img." + aj[2]).removeClass(aj[2]), ar.replaceWith(ap.contents()), am.removeClass(M + " _" + af + "_" + aq.idx + " " + aj[6] + " " + aj[7] + " " + aj[5] + " " + aj[3]).addClass(aj[4])
                    }
                })
            }
        }, ak = function () {
            return "object" != typeof b(this) || b(this).length < 1 ? L : this
        }, F = function (ap) {
            var an = ["rounded", "rounded-dark", "rounded-dots", "rounded-dots-dark"], e = ["rounded-dots", "rounded-dots-dark", "3d", "3d-dark", "3d-thick", "3d-thick-dark", "inset", "inset-dark", "inset-2", "inset-2-dark", "inset-3", "inset-3-dark"], am = ["minimal", "minimal-dark"], aq = ["minimal", "minimal-dark"], ao = ["minimal", "minimal-dark"];
            ap.autoDraggerLength = b.inArray(ap.theme, an) > -1 ? !1 : ap.autoDraggerLength, ap.autoExpandScrollbar = b.inArray(ap.theme, e) > -1 ? !1 : ap.autoExpandScrollbar, ap.scrollButtons.enable = b.inArray(ap.theme, am) > -1 ? !1 : ap.scrollButtons.enable, ap.autoHideScrollbar = b.inArray(ap.theme, aq) > -1 ? !0 : ap.autoHideScrollbar, ap.scrollbarPosition = b.inArray(ap.theme, ao) > -1 ? "outside" : ap.scrollbarPosition
        }, K = function (e) {
            J[e] && (clearTimeout(J[e]), Z(J, e))
        }, N = function (e) {
            return "yx" === e || "xy" === e || "auto" === e ? "yx" : "x" === e || "horizontal" === e ? "x" : "y"
        }, E = function (e) {
            return "stepped" === e || "pixels" === e || "step" === e || "click" === e ? "stepped" : "stepless"
        }, T = function () {
            var am = b(this), ar = am.data(af), ay = ar.opt, av = ay.autoExpandScrollbar ? " " + aj[1] + "_expand" : "", an = ["<div id='mCSB_" + ar.idx + "_scrollbar_vertical' class='mCSB_scrollTools mCSB_" + ar.idx + "_scrollbar mCS-" + ay.theme + " mCSB_scrollTools_vertical" + av + "'><div class='" + aj[12] + "'><div id='mCSB_" + ar.idx + "_dragger_vertical' class='mCSB_dragger' style='position:absolute;'><div class='mCSB_dragger_bar' /></div><div class='mCSB_draggerRail' /></div></div>", "<div id='mCSB_" + ar.idx + "_scrollbar_horizontal' class='mCSB_scrollTools mCSB_" + ar.idx + "_scrollbar mCS-" + ay.theme + " mCSB_scrollTools_horizontal" + av + "'><div class='" + aj[12] + "'><div id='mCSB_" + ar.idx + "_dragger_horizontal' class='mCSB_dragger' style='position:absolute;'><div class='mCSB_dragger_bar' /></div><div class='mCSB_draggerRail' /></div></div>"], e = "yx" === ay.axis ? "mCSB_vertical_horizontal" : "x" === ay.axis ? "mCSB_horizontal" : "mCSB_vertical", aq = "yx" === ay.axis ? an[0] + an[1] : "x" === ay.axis ? an[1] : an[0], ao = "yx" === ay.axis ? "<div id='mCSB_" + ar.idx + "_container_wrapper' class='mCSB_container_wrapper' />" : "", au = ay.autoHideScrollbar ? " " + aj[6] : "", ax = "x" !== ay.axis && "rtl" === ar.langDir ? " " + aj[7] : "";
            ay.setWidth && am.css("width", ay.setWidth), ay.setHeight && am.css("height", ay.setHeight), ay.setLeft = "y" !== ay.axis && "rtl" === ar.langDir ? "989999px" : ay.setLeft, am.addClass(M + " _" + af + "_" + ar.idx + au + ax).wrapInner("<div id='mCSB_" + ar.idx + "' class='mCustomScrollBox mCS-" + ay.theme + " " + e + "'><div id='mCSB_" + ar.idx + "_container' class='mCSB_container' style='position:relative; top:" + ay.setTop + "; left:" + ay.setLeft + ";' dir='" + ar.langDir + "' /></div>");
            var ap = b("#mCSB_" + ar.idx), at = b("#mCSB_" + ar.idx + "_container");
            "y" === ay.axis || ay.advanced.autoExpandHorizontalScroll || at.css("width", V(at)), "outside" === ay.scrollbarPosition ? ("static" === am.css("position") && am.css("position", "relative"), am.css("overflow", "visible"), ap.addClass("mCSB_outside").after(aq)) : (ap.addClass("mCSB_inside").append(aq), at.wrap(ao)), U.call(this);
            var aw = [b("#mCSB_" + ar.idx + "_dragger_vertical"), b("#mCSB_" + ar.idx + "_dragger_horizontal")];
            aw[0].css("min-height", aw[0].height()), aw[1].css("min-width", aw[1].width())
        }, V = function (an) {
            var am = [an[0].scrollWidth, Math.max.apply(Math, an.children().map(function () {
                return b(this).outerWidth(!0)
            }).get())], e = an.parent().width();
            return am[0] > e ? am[0] : am[1] > e ? am[1] : "100%"
        }, ae = function () {
            var ap = b(this), an = ap.data(af), am = an.opt, e = b("#mCSB_" + an.idx + "_container");
            if (am.advanced.autoExpandHorizontalScroll && "y" !== am.axis) {
                e.css({width: "auto", "min-width": 0, "overflow-x": "scroll"});
                var ao = Math.ceil(e[0].scrollWidth);
                3 === am.advanced.autoExpandHorizontalScroll || 2 !== am.advanced.autoExpandHorizontalScroll && ao > e.parent().width() ? e.css({
                    width: ao,
                    "min-width": "100%",
                    "overflow-x": "inherit"
                }) : e.css({
                    "overflow-x": "inherit",
                    position: "absolute"
                }).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({
                    width: Math.ceil(e[0].getBoundingClientRect().right + 0.4) - Math.floor(e[0].getBoundingClientRect().left),
                    "min-width": "100%",
                    position: "relative"
                }).unwrap()
            }
        }, U = function () {
            var aq = b(this), an = aq.data(af), am = an.opt, ar = b(".mCSB_" + an.idx + "_scrollbar:first"), ao = Y(am.scrollButtons.tabindex) ? "tabindex='" + am.scrollButtons.tabindex + "'" : "", e = ["<a href='#' class='" + aj[13] + "' " + ao + " />", "<a href='#' class='" + aj[14] + "' " + ao + " />", "<a href='#' class='" + aj[15] + "' " + ao + " />", "<a href='#' class='" + aj[16] + "' " + ao + " />"], ap = ["x" === am.axis ? e[2] : e[0], "x" === am.axis ? e[3] : e[1], e[2], e[3]];
            am.scrollButtons.enable && ar.prepend(ap[0]).append(ap[1]).next(".mCSB_scrollTools").prepend(ap[2]).append(ap[3])
        }, u = function () {
            var au = b(this), ar = au.data(af), aq = b("#mCSB_" + ar.idx), ao = b("#mCSB_" + ar.idx + "_container"), at = [b("#mCSB_" + ar.idx + "_dragger_vertical"), b("#mCSB_" + ar.idx + "_dragger_horizontal")], ap = [aq.height() / ao.outerHeight(!1), aq.width() / ao.outerWidth(!1)], am = [parseInt(at[0].css("min-height")), Math.round(ap[0] * at[0].parent().height()), parseInt(at[1].css("min-width")), Math.round(ap[1] * at[1].parent().width())], an = Q && am[1] < am[0] ? am[0] : am[1], e = Q && am[3] < am[2] ? am[2] : am[3];
            at[0].css({
                height: an,
                "max-height": at[0].parent().height() - 10
            }).find(".mCSB_dragger_bar").css({"line-height": am[0] + "px"}), at[1].css({
                width: e,
                "max-width": at[1].parent().width() - 10
            })
        }, ah = function () {
            var aq = b(this), an = aq.data(af), am = b("#mCSB_" + an.idx), ar = b("#mCSB_" + an.idx + "_container"), ao = [b("#mCSB_" + an.idx + "_dragger_vertical"), b("#mCSB_" + an.idx + "_dragger_horizontal")], e = [ar.outerHeight(!1) - am.height(), ar.outerWidth(!1) - am.width()], ap = [e[0] / (ao[0].parent().height() - ao[0].height()), e[1] / (ao[1].parent().width() - ao[1].width())];
            an.scrollRatio = {y: ap[0], x: ap[1]}
        }, ac = function (ao, ap, an) {
            var e = an ? aj[0] + "_expanded" : "", am = ao.closest(".mCSB_scrollTools");
            "active" === ap ? (ao.toggleClass(aj[0] + " " + e), am.toggleClass(aj[1]), ao[0]._draggable = ao[0]._draggable ? 0 : 1) : ao[0]._draggable || ("hide" === ap ? (ao.removeClass(aj[0]), am.removeClass(aj[1])) : (ao.addClass(aj[0]), am.addClass(aj[1])))
        }, W = function () {
            var aq = b(this), an = aq.data(af), ar = b("#mCSB_" + an.idx), at = b("#mCSB_" + an.idx + "_container"), ao = null == an.overflowed ? at.height() : at.outerHeight(!1), am = null == an.overflowed ? at.width() : at.outerWidth(!1), ap = at[0].scrollHeight, e = at[0].scrollWidth;
            return ap > ao && (ao = ap), e > am && (am = e), [ao > ar.height(), am > ar.width()]
        }, ab = function () {
            var aq = b(this), an = aq.data(af), am = an.opt, ar = b("#mCSB_" + an.idx), ao = b("#mCSB_" + an.idx + "_container"), e = [b("#mCSB_" + an.idx + "_dragger_vertical"), b("#mCSB_" + an.idx + "_dragger_horizontal")];
            if (r(aq), ("x" !== am.axis && !an.overflowed[0] || "y" === am.axis && an.overflowed[0]) && (e[0].add(ao).css("top", 0), f(aq, "_resetY")), "y" !== am.axis && !an.overflowed[1] || "x" === am.axis && an.overflowed[1]) {
                var ap = dx = 0;
                "rtl" === an.langDir && (ap = ar.width() - ao.outerWidth(!1), dx = Math.abs(ap / an.scrollRatio.x)), ao.css("left", ap), e[1].css("left", dx), f(aq, "_resetX")
            }
        }, v = function () {
            function ap() {
                ao = setTimeout(function () {
                    b.event.special.mousewheel ? (clearTimeout(ao), y.call(an[0])) : ap()
                }, 100)
            }

            var an = b(this), am = an.data(af), e = am.opt;
            if (!am.bindEvents) {
                if (h.call(this), e.contentTouchScroll && ad.call(this), c.call(this), e.mouseWheel.enable) {
                    var ao;
                    ap()
                }
                q.call(this), w.call(this), e.advanced.autoScrollOnFocus && g.call(this), e.scrollButtons.enable && d.call(this), e.keyboard.enable && O.call(this), am.bindEvents = !0
            }
        }, I = function () {
            var aq = b(this), an = aq.data(af), am = an.opt, ar = af + "_" + an.idx, ao = ".mCSB_" + an.idx + "_scrollbar", e = b("#mCSB_" + an.idx + ",#mCSB_" + an.idx + "_container,#mCSB_" + an.idx + "_container_wrapper," + ao + " ." + aj[12] + ",#mCSB_" + an.idx + "_dragger_vertical,#mCSB_" + an.idx + "_dragger_horizontal," + ao + ">a"), ap = b("#mCSB_" + an.idx + "_container");
            am.advanced.releaseDraggableSelectors && e.add(b(am.advanced.releaseDraggableSelectors)), am.advanced.extraDraggableSelectors && e.add(b(am.advanced.extraDraggableSelectors)), an.bindEvents && (b(document).add(b(!aa() || top.document)).unbind("." + ar), e.each(function () {
                b(this).unbind("." + ar)
            }), clearTimeout(aq[0]._focusTimeout), Z(aq[0], "_focusTimeout"), clearTimeout(an.sequential.step), Z(an.sequential, "step"), clearTimeout(ap[0].onCompleteTimeout), Z(ap[0], "onCompleteTimeout"), an.bindEvents = !1)
        }, n = function (aq) {
            var an = b(this), ar = an.data(af), at = ar.opt, ao = b("#mCSB_" + ar.idx + "_container_wrapper"), am = ao.length ? ao : b("#mCSB_" + ar.idx + "_container"), ap = [b("#mCSB_" + ar.idx + "_scrollbar_vertical"), b("#mCSB_" + ar.idx + "_scrollbar_horizontal")], e = [ap[0].find(".mCSB_dragger"), ap[1].find(".mCSB_dragger")];
            "x" !== at.axis && (ar.overflowed[0] && !aq ? (ap[0].add(e[0]).add(ap[0].children("a")).css("display", "block"), am.removeClass(aj[8] + " " + aj[10])) : (at.alwaysShowScrollbar ? (2 !== at.alwaysShowScrollbar && e[0].css("display", "none"), am.removeClass(aj[10])) : (ap[0].css("display", "none"), am.addClass(aj[10])), am.addClass(aj[8]))), "y" !== at.axis && (ar.overflowed[1] && !aq ? (ap[1].add(e[1]).add(ap[1].children("a")).css("display", "block"), am.removeClass(aj[9] + " " + aj[11])) : (at.alwaysShowScrollbar ? (2 !== at.alwaysShowScrollbar && e[1].css("display", "none"), am.removeClass(aj[11])) : (ap[1].css("display", "none"), am.addClass(aj[11])), am.addClass(aj[9]))), ar.overflowed[0] || ar.overflowed[1] ? an.removeClass(aj[5]) : an.addClass(aj[5])
        }, p = function (ap) {
            var an = ap.type, e = ap.target.ownerDocument !== document && null !== frameElement ? [b(frameElement).offset().top, b(frameElement).offset().left] : null, am = aa() && ap.target.ownerDocument !== top.document && null !== frameElement ? [b(ap.view.frameElement).offset().top, b(ap.view.frameElement).offset().left] : [0, 0];
            switch (an) {
                case"pointerdown":
                case"MSPointerDown":
                case"pointermove":
                case"MSPointerMove":
                case"pointerup":
                case"MSPointerUp":
                    return e ? [ap.originalEvent.pageY - e[0] + am[0], ap.originalEvent.pageX - e[1] + am[1], !1] : [ap.originalEvent.pageY, ap.originalEvent.pageX, !1];
                case"touchstart":
                case"touchmove":
                case"touchend":
                    var aq = ap.originalEvent.touches[0] || ap.originalEvent.changedTouches[0], ao = ap.originalEvent.touches.length || ap.originalEvent.changedTouches.length;
                    return ap.target.ownerDocument !== document ? [aq.screenY, aq.screenX, ao > 1] : [aq.pageY, aq.pageX, ao > 1];
                default:
                    return e ? [ap.pageY - e[0] + am[0], ap.pageX - e[1] + am[1], !1] : [ap.pageY, ap.pageX, !1]
            }
        }, h = function () {
            function e(aA, aC, aE, az) {
                if (av[0].idleTimer = ao.scrollInertia < 233 ? 250 : 0, aq.attr("id") === ap[1]) {
                    var aD = "x", aB = (aq[0].offsetLeft - aC + az) * ax.scrollRatio.x
                } else {
                    var aD = "y", aB = (aq[0].offsetTop - aA + aE) * ax.scrollRatio.y
                }
                f(au, aB.toString(), {dir: aD, drag: !0})
            }

            var aq, an, aw, au = b(this), ax = au.data(af), ao = ax.opt, am = af + "_" + ax.idx, ap = ["mCSB_" + ax.idx + "_dragger_vertical", "mCSB_" + ax.idx + "_dragger_horizontal"], av = b("#mCSB_" + ax.idx + "_container"), ay = b("#" + ap[0] + ",#" + ap[1]), ar = ao.advanced.releaseDraggableSelectors ? ay.add(b(ao.advanced.releaseDraggableSelectors)) : ay, at = ao.advanced.extraDraggableSelectors ? b(!aa() || top.document).add(b(ao.advanced.extraDraggableSelectors)) : b(!aa() || top.document);
            ay.bind("contextmenu." + am, function (az) {
                az.preventDefault()
            }).bind("mousedown." + am + " touchstart." + am + " pointerdown." + am + " MSPointerDown." + am, function (aD) {
                if (aD.stopImmediatePropagation(), aD.preventDefault(), ag(aD)) {
                    ai = !0, Q && (document.onselectstart = function () {
                        return !1
                    }), m.call(av, !1), r(au), aq = b(this);
                    var az = aq.offset(), aA = p(aD)[0] - az.top, aE = p(aD)[1] - az.left, aB = aq.height() + az.top, aC = aq.width() + az.left;
                    aB > aA && aA > 0 && aC > aE && aE > 0 && (an = aA, aw = aE), ac(aq, "active", ao.autoExpandScrollbar)
                }
            }).bind("touchmove." + am, function (az) {
                az.stopImmediatePropagation(), az.preventDefault();
                var aB = aq.offset(), aA = p(az)[0] - aB.top, aC = p(az)[1] - aB.left;
                e(an, aw, aA, aC)
            }), b(document).add(at).bind("mousemove." + am + " pointermove." + am + " MSPointerMove." + am, function (az) {
                if (aq) {
                    var aB = aq.offset(), aA = p(az)[0] - aB.top, aC = p(az)[1] - aB.left;
                    if (an === aA && aw === aC) {
                        return
                    }
                    e(an, aw, aA, aC)
                }
            }).add(ar).bind("mouseup." + am + " touchend." + am + " pointerup." + am + " MSPointerUp." + am, function () {
                aq && (ac(aq, "active", ao.autoExpandScrollbar), aq = null), ai = !1, Q && (document.onselectstart = null), m.call(av, !0)
            })
        }, ad = function () {
            function ay(aV) {
                if (!A(aV) || ai || p(aV)[2]) {
                    return void (R = 0)
                }
                R = 1, aT = 0, aD = 0, aU = 1, aC.removeClass("mCS_touch_action");
                var aW = aw.offset();
                aP = p(aV)[0] - aW.top, e = p(aV)[1] - aW.left, aF = [p(aV)[0], p(aV)[1]]
            }

            function ax(aV) {
                if (A(aV) && !ai && !p(aV)[2] && (aM.documentTouchScroll || aV.preventDefault(), aV.stopImmediatePropagation(), (!aD || aT) && aU)) {
                    am = l();
                    var a2 = aH.offset(), a0 = p(aV)[0] - a2.top, a3 = p(aV)[1] - a2.left, aZ = "mcsLinearOut";
                    if (aO.push(a0), aR.push(a3), aF[2] = Math.abs(p(aV)[0] - aF[0]), aF[3] = Math.abs(p(aV)[1] - aF[1]), aA.overflowed[0]) {
                        var aX = aN[0].parent().height() - aN[0].height(), a1 = aP - a0 > 0 && a0 - aP > -(aX * aA.scrollRatio.y) && (2 * aF[3] < aF[2] || "yx" === aM.axis)
                    }
                    if (aA.overflowed[1]) {
                        var aY = aN[1].parent().width() - aN[1].width(), aW = e - a3 > 0 && a3 - e > -(aY * aA.scrollRatio.x) && (2 * aF[2] < aF[3] || "yx" === aM.axis)
                    }
                    a1 || aW ? (aL || aV.preventDefault(), aT = 1) : (aD = 1, aC.addClass("mCS_touch_action")), aL && aV.preventDefault(), ap = "yx" === aM.axis ? [aP - a0, e - a3] : "x" === aM.axis ? [null, e - a3] : [aP - a0, null], aw[0].idleTimer = 250, aA.overflowed[0] && aE(ap[0], aJ, aZ, "y", "all", !0), aA.overflowed[1] && aE(ap[1], aJ, aZ, "x", aG, !0)
                }
            }

            function ao(aV) {
                if (!A(aV) || ai || p(aV)[2]) {
                    return void (R = 0)
                }
                R = 1, aV.stopImmediatePropagation(), r(aC), az = l();
                var aW = aH.offset();
                an = p(aV)[0] - aW.top, av = p(aV)[1] - aW.left, aO = [], aR = []
            }

            function aB(aX) {
                if (A(aX) && !ai && !p(aX)[2]) {
                    aU = 0, aX.stopImmediatePropagation(), aT = 0, aD = 0, aQ = l();
                    var a3 = aH.offset(), a1 = p(aX)[0] - a3.top, aV = p(aX)[1] - a3.left;
                    if (!(aQ - am > 30)) {
                        aS = 1000 / (aQ - az);
                        var a0 = "mcsEaseOut", aZ = 2.5 > aS, a2 = aZ ? [aO[aO.length - 2], aR[aR.length - 2]] : [0, 0];
                        au = aZ ? [a1 - a2[0], aV - a2[1]] : [a1 - an, aV - av];
                        var a4 = [Math.abs(au[0]), Math.abs(au[1])];
                        aS = aZ ? [Math.abs(au[0] / 4), Math.abs(au[1] / 4)] : [aS, aS];
                        var aY = [Math.abs(aw[0].offsetTop) - au[0] * at(a4[0] / aS[0], aS[0]), Math.abs(aw[0].offsetLeft) - au[1] * at(a4[1] / aS[1], aS[1])];
                        ap = "yx" === aM.axis ? [aY[0], aY[1]] : "x" === aM.axis ? [null, aY[1]] : [aY[0], null], aK = [4 * a4[0] + aM.scrollInertia, 4 * a4[1] + aM.scrollInertia];
                        var aW = parseInt(aM.contentTouchScroll) || 0;
                        ap[0] = a4[0] > aW ? ap[0] : 0, ap[1] = a4[1] > aW ? ap[1] : 0, aA.overflowed[0] && aE(ap[0], aK[0], a0, "y", aG, !1), aA.overflowed[1] && aE(ap[1], aK[1], a0, "x", aG, !1)
                    }
                }
            }

            function at(aX, aV) {
                var aW = [1.5 * aV, 2 * aV, aV / 1.5, aV / 2];
                return aX > 90 ? aV > 4 ? aW[0] : aW[3] : aX > 60 ? aV > 3 ? aW[3] : aW[2] : aX > 30 ? aV > 8 ? aW[1] : aV > 6 ? aW[0] : aV > 4 ? aV : aW[2] : aV > 8 ? aV : aW[3]
            }

            function aE(aY, aZ, aX, aV, aW, a0) {
                aY && f(aC, aY.toString(), {
                    dur: aZ,
                    scrollEasing: aX,
                    dir: aV,
                    overwrite: aW,
                    drag: a0
                })
            }

            var aU, aP, e, an, av, az, am, aQ, au, aS, ap, aK, aT, aD, aC = b(this), aA = aC.data(af), aM = aA.opt, ar = af + "_" + aA.idx, aH = b("#mCSB_" + aA.idx), aw = b("#mCSB_" + aA.idx + "_container"), aN = [b("#mCSB_" + aA.idx + "_dragger_vertical"), b("#mCSB_" + aA.idx + "_dragger_horizontal")], aO = [], aR = [], aJ = 0, aG = "yx" === aM.axis ? "none" : "all", aF = [], aI = aw.find("iframe"), aq = ["touchstart." + ar + " pointerdown." + ar + " MSPointerDown." + ar, "touchmove." + ar + " pointermove." + ar + " MSPointerMove." + ar, "touchend." + ar + " pointerup." + ar + " MSPointerUp." + ar], aL = void 0 !== document.body.style.touchAction && "" !== document.body.style.touchAction;
            aw.bind(aq[0], function (aV) {
                ay(aV)
            }).bind(aq[1], function (aV) {
                ax(aV)
            }), aH.bind(aq[0], function (aV) {
                ao(aV)
            }).bind(aq[2], function (aV) {
                aB(aV)
            }), aI.length && aI.each(function () {
                b(this).bind("load", function () {
                    aa(this) && b(this.contentDocument || this.contentWindow.document).bind(aq[0], function (aV) {
                        ay(aV), ao(aV)
                    }).bind(aq[1], function (aV) {
                        ax(aV)
                    }).bind(aq[2], function (aV) {
                        aB(aV)
                    })
                })
            })
        }, c = function () {
            function aq() {
                return window.getSelection ? window.getSelection().toString() : document.selection && "Control" != document.selection.type ? document.selection.createRange().text : 0
            }

            function ap(ax, ay, aw) {
                av.type = aw && an ? "stepped" : "stepless", av.scrollAmount = 10, H(ar, ax, ay, "mcsLinearOut", aw ? 60 : null)
            }

            var an, ar = b(this), ao = ar.data(af), at = ao.opt, av = ao.sequential, au = af + "_" + ao.idx, e = b("#mCSB_" + ao.idx + "_container"), am = e.parent();
            e.bind("mousedown." + au, function () {
                R || an || (an = 1, ai = !0)
            }).add(document).bind("mousemove." + au, function (ax) {
                if (!R && an && aq()) {
                    var az = e.offset(), ay = p(ax)[0] - az.top + e[0].offsetTop, aw = p(ax)[1] - az.left + e[0].offsetLeft;
                    ay > 0 && ay < am.height() && aw > 0 && aw < am.width() ? av.step && ap("off", null, "stepped") : ("x" !== at.axis && ao.overflowed[0] && (0 > ay ? ap("on", 38) : ay > am.height() && ap("on", 40)), "y" !== at.axis && ao.overflowed[1] && (0 > aw ? ap("on", 37) : aw > am.width() && ap("on", 39)))
                }
            }).bind("mouseup." + au + " dragend." + au, function () {
                R || (an && (an = 0, ap("off", null)), ai = !1)
            })
        }, y = function () {
            function ap(au, aB) {
                if (r(am), !X(am, au.target)) {
                    var aA = "auto" !== aq.mouseWheel.deltaFactor ? parseInt(aq.mouseWheel.deltaFactor) : Q && au.deltaFactor < 100 ? 100 : au.deltaFactor || 100, aC = aq.scrollInertia;
                    if ("x" === aq.axis || "x" === aq.mouseWheel.axis) {
                        var av = "x", aD = [Math.round(aA * ar.scrollRatio.x), parseInt(aq.mouseWheel.scrollAmount)], ax = "auto" !== aq.mouseWheel.scrollAmount ? aD[1] : aD[0] >= e.width() ? 0.9 * e.width() : aD[0], ay = Math.abs(b("#mCSB_" + ar.idx + "_container")[0].offsetLeft), az = at[1][0].offsetLeft, aE = at[1].parent().width() - at[1].width(), aw = "y" === aq.mouseWheel.axis ? au.deltaY || aB : au.deltaX
                    } else {
                        var av = "y", aD = [Math.round(aA * ar.scrollRatio.y), parseInt(aq.mouseWheel.scrollAmount)], ax = "auto" !== aq.mouseWheel.scrollAmount ? aD[1] : aD[0] >= e.height() ? 0.9 * e.height() : aD[0], ay = Math.abs(b("#mCSB_" + ar.idx + "_container")[0].offsetTop), az = at[0][0].offsetTop, aE = at[0].parent().height() - at[0].height(), aw = au.deltaY || aB
                    }
                    "y" === av && !ar.overflowed[0] || "x" === av && !ar.overflowed[1] || ((aq.mouseWheel.invert || au.webkitDirectionInvertedFromDevice) && (aw = -aw), aq.mouseWheel.normalizeDelta && (aw = 0 > aw ? -1 : 1), (aw > 0 && 0 !== az || 0 > aw && az !== aE || aq.mouseWheel.preventDefault) && (au.stopImmediatePropagation(), au.preventDefault()), au.deltaFactor < 5 && !aq.mouseWheel.normalizeDelta && (ax = au.deltaFactor, aC = 17), f(am, (ay - aw * ax).toString(), {
                        dir: av,
                        dur: aC
                    }))
                }
            }

            if (b(this).data(af)) {
                var am = b(this), ar = am.data(af), aq = ar.opt, ao = af + "_" + ar.idx, e = b("#mCSB_" + ar.idx), at = [b("#mCSB_" + ar.idx + "_dragger_vertical"), b("#mCSB_" + ar.idx + "_dragger_horizontal")], an = b("#mCSB_" + ar.idx + "_container").find("iframe");
                an.length && an.each(function () {
                    b(this).bind("load", function () {
                        aa(this) && b(this.contentDocument || this.contentWindow.document).bind("mousewheel." + ao, function (av, au) {
                            ap(av, au)
                        })
                    })
                }), e.bind("mousewheel." + ao, function (av, au) {
                    ap(av, au)
                })
            }
        }, s = new Object, aa = function (ap) {
            var an = !1, e = !1, am = null;
            if (void 0 === ap ? e = "#empty" : void 0 !== b(ap).attr("id") && (e = b(ap).attr("id")), e !== !1 && void 0 !== s[e]) {
                return s[e]
            }
            if (ap) {
                try {
                    var aq = ap.contentDocument || ap.contentWindow.document;
                    am = aq.body.innerHTML
                } catch (ao) {
                }
                an = null !== am
            } else {
                try {
                    var aq = top.document;
                    am = aq.body.innerHTML
                } catch (ao) {
                }
                an = null !== am
            }
            return e !== !1 && (s[e] = an), an
        }, m = function (an) {
            var e = this.find("iframe");
            if (e.length) {
                var am = an ? "auto" : "none";
                e.css("pointer-events", am)
            }
        }, X = function (ap, an) {
            var am = an.nodeName.toLowerCase(), e = ap.data(af).opt.mouseWheel.disableOver, ao = ["select", "textarea"];
            return b.inArray(am, e) > -1 && !(b.inArray(am, ao) > -1 && !b(an).is(":focus"))
        }, q = function () {
            var aq, an = b(this), am = an.data(af), ar = af + "_" + am.idx, ao = b("#mCSB_" + am.idx + "_container"), e = ao.parent(), ap = b(".mCSB_" + am.idx + "_scrollbar ." + aj[12]);
            ap.bind("mousedown." + ar + " touchstart." + ar + " pointerdown." + ar + " MSPointerDown." + ar, function (at) {
                ai = !0, b(at.target).hasClass("mCSB_dragger") || (aq = 1)
            }).bind("touchend." + ar + " pointerup." + ar + " MSPointerUp." + ar, function () {
                ai = !1
            }).bind("click." + ar, function (au) {
                if (aq && (aq = 0, b(au.target).hasClass(aj[12]) || b(au.target).hasClass("mCSB_draggerRail"))) {
                    r(an);
                    var ay = b(this), ax = ay.find(".mCSB_dragger");
                    if (ay.parent(".mCSB_scrollTools_horizontal").length > 0) {
                        if (!am.overflowed[1]) {
                            return
                        }
                        var av = "x", at = au.pageX > ax.offset().left ? -1 : 1, aw = Math.abs(ao[0].offsetLeft) - at * (0.9 * e.width())
                    } else {
                        if (!am.overflowed[0]) {
                            return
                        }
                        var av = "y", at = au.pageY > ax.offset().top ? -1 : 1, aw = Math.abs(ao[0].offsetTop) - at * (0.9 * e.height())
                    }
                    f(an, aw.toString(), {
                        dir: av,
                        scrollEasing: "mcsEaseInOut"
                    })
                }
            })
        }, g = function () {
            var ap = b(this), an = ap.data(af), am = an.opt, aq = af + "_" + an.idx, ao = b("#mCSB_" + an.idx + "_container"), e = ao.parent();
            ao.bind("focusin." + aq, function () {
                var ar = b(document.activeElement), au = ao.find(".mCustomScrollBox").length, at = 0;
                ar.is(am.advanced.autoScrollOnFocus) && (r(ap), clearTimeout(ap[0]._focusTimeout), ap[0]._focusTimer = au ? (at + 17) * au : 0, ap[0]._focusTimeout = setTimeout(function () {
                    var aw = [a(ar)[0], a(ar)[1]], ay = [ao[0].offsetTop, ao[0].offsetLeft], ax = [ay[0] + aw[0] >= 0 && ay[0] + aw[0] < e.height() - ar.outerHeight(!1), ay[1] + aw[1] >= 0 && ay[0] + aw[1] < e.width() - ar.outerWidth(!1)], av = "yx" !== am.axis || ax[0] || ax[1] ? "all" : "none";
                    "x" === am.axis || ax[0] || f(ap, aw[0].toString(), {
                        dir: "y",
                        scrollEasing: "mcsEaseInOut",
                        overwrite: av,
                        dur: at
                    }), "y" === am.axis || ax[1] || f(ap, aw[1].toString(), {
                        dir: "x",
                        scrollEasing: "mcsEaseInOut",
                        overwrite: av,
                        dur: at
                    })
                }, ap[0]._focusTimer))
            })
        }, w = function () {
            var ao = b(this), an = ao.data(af), am = af + "_" + an.idx, e = b("#mCSB_" + an.idx + "_container").parent();
            e.bind("scroll." + am, function () {
                0 === e.scrollTop() && 0 === e.scrollLeft() || b(".mCSB_" + an.idx + "_scrollbar").css("visibility", "hidden")
            })
        }, d = function () {
            var aq = b(this), an = aq.data(af), am = an.opt, ar = an.sequential, ao = af + "_" + an.idx, e = ".mCSB_" + an.idx + "_scrollbar", ap = b(e + ">a");
            ap.bind("contextmenu." + ao, function (at) {
                at.preventDefault()
            }).bind("mousedown." + ao + " touchstart." + ao + " pointerdown." + ao + " MSPointerDown." + ao + " mouseup." + ao + " touchend." + ao + " pointerup." + ao + " MSPointerUp." + ao + " mouseout." + ao + " pointerout." + ao + " MSPointerOut." + ao + " click." + ao, function (av) {
                function au(aw, ax) {
                    ar.scrollAmount = am.scrollButtons.scrollAmount, H(aq, aw, ax)
                }

                if (av.preventDefault(), ag(av)) {
                    var at = b(this).attr("class");
                    switch (ar.type = am.scrollButtons.scrollType, av.type) {
                        case"mousedown":
                        case"touchstart":
                        case"pointerdown":
                        case"MSPointerDown":
                            if ("stepped" === ar.type) {
                                return
                            }
                            ai = !0, an.tweenRunning = !1, au("on", at);
                            break;
                        case"mouseup":
                        case"touchend":
                        case"pointerup":
                        case"MSPointerUp":
                        case"mouseout":
                        case"pointerout":
                        case"MSPointerOut":
                            if ("stepped" === ar.type) {
                                return
                            }
                            ai = !1, ar.dir && au("off", at);
                            break;
                        case"click":
                            if ("stepped" !== ar.type || an.tweenRunning) {
                                return
                            }
                            au("on", at)
                    }
                }
            })
        }, O = function () {
            function ao(aC) {
                switch (aC.type) {
                    case"blur":
                        aq.tweenRunning && au.dir && aE("off", null);
                        break;
                    case"keydown":
                    case"keyup":
                        var aB = aC.keyCode ? aC.keyCode : aC.which, ay = "on";
                        if ("x" !== an.axis && (38 === aB || 40 === aB) || "y" !== an.axis && (37 === aB || 39 === aB)) {
                            if ((38 === aB || 40 === aB) && !aq.overflowed[0] || (37 === aB || 39 === aB) && !aq.overflowed[1]) {
                                return
                            }
                            "keyup" === aC.type && (ay = "off"), b(document.activeElement).is(at) || (aC.preventDefault(), aC.stopImmediatePropagation(), aE(ay, aB))
                        } else {
                            if (33 === aB || 34 === aB) {
                                if ((aq.overflowed[0] || aq.overflowed[1]) && (aC.preventDefault(), aC.stopImmediatePropagation()), "keyup" === aC.type) {
                                    r(ar);
                                    var aA = 34 === aB ? -1 : 1;
                                    if ("x" === an.axis || "yx" === an.axis && aq.overflowed[1] && !aq.overflowed[0]) {
                                        var az = "x", aD = Math.abs(av[0].offsetLeft) - aA * (0.9 * aw.width())
                                    } else {
                                        var az = "y", aD = Math.abs(av[0].offsetTop) - aA * (0.9 * aw.height())
                                    }
                                    f(ar, aD.toString(), {
                                        dir: az,
                                        scrollEasing: "mcsEaseInOut"
                                    })
                                }
                            } else {
                                if ((35 === aB || 36 === aB) && !b(document.activeElement).is(at) && ((aq.overflowed[0] || aq.overflowed[1]) && (aC.preventDefault(), aC.stopImmediatePropagation()), "keyup" === aC.type)) {
                                    if ("x" === an.axis || "yx" === an.axis && aq.overflowed[1] && !aq.overflowed[0]) {
                                        var az = "x", aD = 35 === aB ? Math.abs(aw.width() - av.outerWidth(!1)) : 0
                                    } else {
                                        var az = "y", aD = 35 === aB ? Math.abs(aw.height() - av.outerHeight(!1)) : 0
                                    }
                                    f(ar, aD.toString(), {
                                        dir: az,
                                        scrollEasing: "mcsEaseInOut"
                                    })
                                }
                            }
                        }
                }

            }
            var ar = b(this), aq = ar.data(af), an = aq.opt, au = aq.sequential, ap = af + "_" + aq.idx, am = b("#mCSB_" + aq.idx), av = b("#mCSB_" + aq.idx + "_container"), aw = av.parent(), at = "input,textarea,select,datalist,keygen,[contenteditable='true']", ax = av.find("iframe"), e = ["blur." + ap + " keydown." + ap + " keyup." + ap];

            function aE(aF, aG) {
                au.type = an.keyboard.scrollType, au.scrollAmount = an.keyboard.scrollAmount, "stepped" === au.type && aq.tweenRunning || H(ar, aF, aG)
            }
            ax.length && ax.each(function () {
                b(this).bind("load", function () {
                    aa(this) && b(this.contentDocument || this.contentWindow.document).bind(e[0], function (ay) {
                        ao(ay)
                    })
                })
            }), am.attr("tabindex", "0").bind(e[0], function (ay) {
                ao(ay)
            })
        }, H = function (az, aq, an, av, au) {
            function aw(aD) {
                e.snapAmount && (ao.scrollAmount = e.snapAmount instanceof Array ? "x" === ao.dir[0] ? e.snapAmount[1] : e.snapAmount[0] : e.snapAmount);
                var aG = "stepped" !== ao.type, aA = au ? au : aD ? aG ? ar / 1.5 : ap : 1000 / 60, aF = aD ? aG ? 7.5 : 40 : 2.5, aH = [Math.abs(at[0].offsetTop), Math.abs(at[0].offsetLeft)], aC = [am.scrollRatio.y > 10 ? 10 : am.scrollRatio.y, am.scrollRatio.x > 10 ? 10 : am.scrollRatio.x], aE = "x" === ao.dir[0] ? aH[1] + ao.dir[1] * (aC[1] * aF) : aH[0] + ao.dir[1] * (aC[0] * aF), aI = "x" === ao.dir[0] ? aH[1] + ao.dir[1] * parseInt(ao.scrollAmount) : aH[0] + ao.dir[1] * parseInt(ao.scrollAmount), aB = "auto" !== ao.scrollAmount ? aI : aE, aK = av ? av : aD ? aG ? "mcsLinearOut" : "mcsEaseInOut" : "mcsLinear", aJ = !!aD;
                return aD && 17 > aA && (aB = "x" === ao.dir[0] ? aH[1] : aH[0]), f(az, aB.toString(), {
                    dir: ao.dir[0],
                    scrollEasing: aK,
                    dur: aA,
                    onComplete: aJ
                }), aD ? void (ao.dir = !1) : (clearTimeout(ao.step), void (ao.step = setTimeout(function () {
                    aw()
                }, aA)))
            }

            function ay() {
                clearTimeout(ao.step), Z(ao, "step"), r(az)
            }

            var am = az.data(af), e = am.opt, ao = am.sequential, at = b("#mCSB_" + am.idx + "_container"), ax = "stepped" === ao.type, ar = e.scrollInertia < 26 ? 26 : e.scrollInertia, ap = e.scrollInertia < 1 ? 17 : e.scrollInertia;
            switch (aq) {
                case"on":
                    if (ao.dir = [an === aj[16] || an === aj[15] || 39 === an || 37 === an ? "x" : "y", an === aj[13] || an === aj[15] || 38 === an || 37 === an ? -1 : 1], r(az), Y(an) && "stepped" === ao.type) {
                        return
                    }
                    aw(ax);
                    break;
                case"off":
                    ay(), (ax || am.tweenRunning && ao.dir) && aw(!0)
            }
        }, C = function (e) {
            var an = b(this).data(af).opt, am = [];
            return "function" == typeof e && (e = e()), e instanceof Array ? am = e.length > 1 ? [e[0], e[1]] : "x" === an.axis ? [null, e[0]] : [e[0], null] : (am[0] = e.y ? e.y : e.x || "x" === an.axis ? null : e, am[1] = e.x ? e.x : e.y || "y" === an.axis ? null : e), "function" == typeof am[0] && (am[0] = am[0]()), "function" == typeof am[1] && (am[1] = am[1]()), am
        }, B = function (am, aq) {
            if (null != am && "undefined" != typeof am) {
                var an = b(this), aw = an.data(af), au = aw.opt, ax = b("#mCSB_" + aw.idx + "_container"), e = ax.parent(), ao = typeof am;
                aq || (aq = "x" === au.axis ? "x" : "y");
                var ap = "x" === aq ? ax.outerWidth(!1) - e.width() : ax.outerHeight(!1) - e.height(), at = "x" === aq ? ax[0].offsetLeft : ax[0].offsetTop, av = "x" === aq ? "left" : "top";
                switch (ao) {
                    case"function":
                        return am();
                    case"object":
                        var ay = am.jquery ? am : b(am);
                        if (!ay.length) {
                            return
                        }
                        return "x" === aq ? a(ay)[1] : a(ay)[0];
                    case"string":
                    case"number":
                        if (Y(am)) {
                            return Math.abs(am)
                        }
                        if (-1 !== am.indexOf("%")) {
                            return Math.abs(ap * parseInt(am) / 100)
                        }
                        if (-1 !== am.indexOf("-=")) {
                            return Math.abs(at - parseInt(am.split("-=")[1]))
                        }
                        if (-1 !== am.indexOf("+=")) {
                            var ar = at + parseInt(am.split("+=")[1]);
                            return ar >= 0 ? 0 : Math.abs(ar)
                        }
                        if (-1 !== am.indexOf("px") && Y(am.split("px")[0])) {
                            return Math.abs(am.split("px")[0])
                        }
                        if ("top" === am || "left" === am) {
                            return 0
                        }
                        if ("bottom" === am) {
                            return Math.abs(e.height() - ax.outerHeight(!1))
                        }
                        if ("right" === am) {
                            return Math.abs(e.width() - ax.outerWidth(!1))
                        }
                        if ("first" === am || "last" === am) {
                            var ay = ax.find(":" + am);
                            return "x" === aq ? a(ay)[1] : a(ay)[0]
                        }
                        return b(am).length ? "x" === aq ? a(b(am))[1] : a(b(am))[0] : (ax.css(av, am), void S.update.call(null, an[0]))
                }
            }
        }, o = function (e) {
            function ar() {
                return clearTimeout(an[0].autoUpdate), 0 === ap.parents("html").length ? void (ap = null) : void (an[0].autoUpdate = setTimeout(function () {
                    return am.advanced.updateOnSelectorChange && (au.poll.change.n = ao(), au.poll.change.n !== au.poll.change.o) ? (au.poll.change.o = au.poll.change.n, void at(3)) : am.advanced.updateOnContentResize && (au.poll.size.n = ap[0].scrollHeight + ap[0].scrollWidth + an[0].offsetHeight + ap[0].offsetHeight + ap[0].offsetWidth, au.poll.size.n !== au.poll.size.o) ? (au.poll.size.o = au.poll.size.n, void at(1)) : !am.advanced.updateOnImageLoad || "auto" === am.advanced.updateOnImageLoad && "y" === am.axis || (au.poll.img.n = an.find("img").length, au.poll.img.n === au.poll.img.o) ? void ((am.advanced.updateOnSelectorChange || am.advanced.updateOnContentResize || am.advanced.updateOnImageLoad) && ar()) : (au.poll.img.o = au.poll.img.n, void an.find("img").each(function () {
                        aq(this)
                    }))
                }, am.advanced.autoUpdateTimeout))
            }

            function aq(ax) {
                function av(az, aA) {
                    return function () {
                        return aA.apply(az, arguments)
                    }
                }

                function ay() {
                    this.onload = null, b(ax).addClass(aj[2]), at(2)
                }

                if (b(ax).hasClass(aj[2])) {
                    return void at()
                }
                var aw = new Image;
                aw.onload = av(aw, ay), aw.src = ax.src
            }

            function ao() {
                am.advanced.updateOnSelectorChange === !0 && (am.advanced.updateOnSelectorChange = "*");
                var av = 0, aw = an.find(am.advanced.updateOnSelectorChange);
                return am.advanced.updateOnSelectorChange && aw.length > 0 && aw.each(function () {
                    av += this.offsetHeight + this.offsetWidth
                }), av
            }

            function at(av) {
                clearTimeout(an[0].autoUpdate), S.update.call(null, ap[0], av)
            }

            var ap = b(this), au = ap.data(af), am = au.opt, an = b("#mCSB_" + au.idx + "_container");
            return e ? (clearTimeout(an[0].autoUpdate), void Z(an[0], "autoUpdate")) : void ar()
        }, x = function (an, e, am) {
            return Math.round(an / e) * e - am
        }, r = function (e) {
            var an = e.data(af), am = b("#mCSB_" + an.idx + "_container,#mCSB_" + an.idx + "_container_wrapper,#mCSB_" + an.idx + "_dragger_vertical,#mCSB_" + an.idx + "_dragger_horizontal");
            am.each(function () {
                D.call(this)
            })
        }, f = function (aC, ax, aw) {
            function ar(aL) {
                return aB && am.callbacks[aL] && "function" == typeof am.callbacks[aL]
            }

            function aA() {
                return [am.callbacks.alwaysTriggerOffsets || aF >= aJ[0] + aH, am.callbacks.alwaysTriggerOffsets || -az >= aF]
            }

            function au() {
                var aN = [aq[0].offsetTop, aq[0].offsetLeft], aM = [aG[0].offsetTop, aG[0].offsetLeft], aL = [aq.outerHeight(!1), aq.outerWidth(!1)], aO = [ao.height(), ao.width()];
                aC[0].mcs = {
                    content: aq,
                    top: aN[0],
                    left: aN[1],
                    draggerTop: aM[0],
                    draggerLeft: aM[1],
                    topPct: Math.round(100 * Math.abs(aN[0]) / (Math.abs(aL[0]) - aO[0])),
                    leftPct: Math.round(100 * Math.abs(aN[1]) / (Math.abs(aL[1]) - aO[1])),
                    direction: aw.dir
                }
            }

            var aB = aC.data(af), am = aB.opt, an = {
                trigger: "internal",
                dir: "y",
                scrollEasing: "mcsEaseOut",
                drag: !1,
                dur: am.scrollInertia,
                overwrite: "all",
                callbacks: !0,
                onStart: !0,
                onUpdate: !0,
                onComplete: !0
            }, aw = b.extend(an, aw), aD = [aw.dur, aw.drag ? 0 : aw.dur], ao = b("#mCSB_" + aB.idx), aq = b("#mCSB_" + aB.idx + "_container"), av = aq.parent(), ay = am.callbacks.onTotalScrollOffset ? C.call(aC, am.callbacks.onTotalScrollOffset) : [0, 0], ap = am.callbacks.onTotalScrollBackOffset ? C.call(aC, am.callbacks.onTotalScrollBackOffset) : [0, 0];
            if (aB.trigger = aw.trigger, 0 === av.scrollTop() && 0 === av.scrollLeft() || (b(".mCSB_" + aB.idx + "_scrollbar").css("visibility", "visible"), av.scrollTop(0).scrollLeft(0)), "_resetY" !== ax || aB.contentReset.y || (ar("onOverflowYNone") && am.callbacks.onOverflowYNone.call(aC[0]), aB.contentReset.y = 1), "_resetX" !== ax || aB.contentReset.x || (ar("onOverflowXNone") && am.callbacks.onOverflowXNone.call(aC[0]), aB.contentReset.x = 1), "_resetY" !== ax && "_resetX" !== ax) {
                if (!aB.contentReset.y && aC[0].mcs || !aB.overflowed[0] || (ar("onOverflowY") && am.callbacks.onOverflowY.call(aC[0]), aB.contentReset.x = null), !aB.contentReset.x && aC[0].mcs || !aB.overflowed[1] || (ar("onOverflowX") && am.callbacks.onOverflowX.call(aC[0]), aB.contentReset.x = null), am.snapAmount) {
                    var aE = am.snapAmount instanceof Array ? "x" === aw.dir ? am.snapAmount[1] : am.snapAmount[0] : am.snapAmount;
                    ax = x(ax, aE, am.snapOffset)
                }
                switch (aw.dir) {
                    case"x":
                        var aG = b("#mCSB_" + aB.idx + "_dragger_horizontal"), aK = "left", aF = aq[0].offsetLeft, aJ = [ao.width() - aq.outerWidth(!1), aG.parent().width() - aG.width()], e = [ax, 0 === ax ? 0 : ax / aB.scrollRatio.x], aH = ay[1], az = ap[1], aI = aH > 0 ? aH / aB.scrollRatio.x : 0, at = az > 0 ? az / aB.scrollRatio.x : 0;
                        break;
                    case"y":
                        var aG = b("#mCSB_" + aB.idx + "_dragger_vertical"), aK = "top", aF = aq[0].offsetTop, aJ = [ao.height() - aq.outerHeight(!1), aG.parent().height() - aG.height()], e = [ax, 0 === ax ? 0 : ax / aB.scrollRatio.y], aH = ay[0], az = ap[0], aI = aH > 0 ? aH / aB.scrollRatio.y : 0, at = az > 0 ? az / aB.scrollRatio.y : 0
                }
                e[1] < 0 || 0 === e[0] && 0 === e[1] ? e = [0, 0] : e[1] >= aJ[1] ? e = [aJ[0], aJ[1]] : e[0] = -e[0], aC[0].mcs || (au(), ar("onInit") && am.callbacks.onInit.call(aC[0])), clearTimeout(aq[0].onCompleteTimeout), k(aG[0], aK, Math.round(e[1]), aD[1], aw.scrollEasing), !aB.tweenRunning && (0 === aF && e[0] >= 0 || aF === aJ[0] && e[0] <= aJ[0]) || k(aq[0], aK, Math.round(e[0]), aD[0], aw.scrollEasing, aw.overwrite, {
                    onStart: function () {
                        aw.callbacks && aw.onStart && !aB.tweenRunning && (ar("onScrollStart") && (au(), am.callbacks.onScrollStart.call(aC[0])), aB.tweenRunning = !0, ac(aG), aB.cbOffsets = aA())
                    }, onUpdate: function () {
                        aw.callbacks && aw.onUpdate && ar("whileScrolling") && (au(), am.callbacks.whileScrolling.call(aC[0]))
                    }, onComplete: function () {
                        if (aw.callbacks && aw.onComplete) {
                            "yx" === am.axis && clearTimeout(aq[0].onCompleteTimeout);
                            var aL = aq[0].idleTimer || 0;
                            aq[0].onCompleteTimeout = setTimeout(function () {
                                ar("onScroll") && (au(), am.callbacks.onScroll.call(aC[0])), ar("onTotalScroll") && e[1] >= aJ[1] - aI && aB.cbOffsets[0] && (au(), am.callbacks.onTotalScroll.call(aC[0])), ar("onTotalScrollBack") && e[1] <= at && aB.cbOffsets[1] && (au(), am.callbacks.onTotalScrollBack.call(aC[0])), aB.tweenRunning = !1, aq[0].idleTimer = 0, ac(aG, "hide")
                            }, aL)
                        }
                    }
                })
            }
        }, k = function (aq, aE, aA, am, az, av, aC) {
            function ax() {
                aI.stop || (aF || ay.call(), aF = l() - aH, aD(), aF >= aI.time && (aI.time = aF > aI.time ? aF + ar - (aF - aI.time) : aF + ar - 1, aI.time < aF + 1 && (aI.time = aF + 1)), aI.time < am ? aI.id = au(ax) : at.call())
            }

            function aD() {
                am > 0 ? (aI.currVal = aG(aI.time, e, an, am, az), aw[aE] = Math.round(aI.currVal) + "px") : aw[aE] = aA + "px", aB.call()
            }

            function ao() {
                ar = 1000 / 60, aI.time = aF + ar, au = window.requestAnimationFrame ? window.requestAnimationFrame : function (aJ) {
                    return aD(), setTimeout(aJ, 0.01)
                }, aI.id = au(ax)
            }

            function ap() {
                null != aI.id && (window.requestAnimationFrame ? window.cancelAnimationFrame(aI.id) : clearTimeout(aI.id), aI.id = null)
            }

            function aG(aM, aO, aL, aJ, aK) {
                switch (aK) {
                    case"linear":
                    case"mcsLinear":
                        return aL * aM / aJ + aO;
                    case"mcsLinearOut":
                        return aM /= aJ, aM--, aL * Math.sqrt(1 - aM * aM) + aO;
                    case"easeInOutSmooth":
                        return aM /= aJ / 2, 1 > aM ? aL / 2 * aM * aM + aO : (aM--, -aL / 2 * (aM * (aM - 2) - 1) + aO);
                    case"easeInOutStrong":
                        return aM /= aJ / 2, 1 > aM ? aL / 2 * Math.pow(2, 10 * (aM - 1)) + aO : (aM--, aL / 2 * (-Math.pow(2, -10 * aM) + 2) + aO);
                    case"easeInOut":
                    case"mcsEaseInOut":
                        return aM /= aJ / 2, 1 > aM ? aL / 2 * aM * aM * aM + aO : (aM -= 2, aL / 2 * (aM * aM * aM + 2) + aO);
                    case"easeOutSmooth":
                        return aM /= aJ, aM--, -aL * (aM * aM * aM * aM - 1) + aO;
                    case"easeOutStrong":
                        return aL * (-Math.pow(2, -10 * aM / aJ) + 1) + aO;
                    case"easeOut":
                    case"mcsEaseOut":
                    default:
                        var aP = (aM /= aJ) * aM, aN = aP * aM;
                        return aO + aL * (0.499999999999997 * aN * aP + -2.5 * aP * aP + 5.5 * aN + -6.5 * aP + 4 * aM)
                }
            }

            aq._mTween || (aq._mTween = {top: {}, left: {}});
            var ar, au, aC = aC || {}, ay = aC.onStart || function () {
            }, aB = aC.onUpdate || function () {
            }, at = aC.onComplete || function () {
            }, aH = l(), aF = 0, e = aq.offsetTop, aw = aq.style, aI = aq._mTween[aE];
            "left" === aE && (e = aq.offsetLeft);
            var an = aA - e;
            aI.stop = 0, "none" !== av && ap(), ao()
        }, l = function () {
            return window.performance && window.performance.now ? window.performance.now() : window.performance && window.performance.webkitNow ? window.performance.webkitNow() : Date.now ? Date.now() : (new Date).getTime()
        }, D = function () {
            var an = this;
            an._mTween || (an._mTween = {top: {}, left: {}});
            for (var ao = ["top", "left"], am = 0; am < ao.length; am++) {
                var e = ao[am];
                an._mTween[e].id && (window.requestAnimationFrame ? window.cancelAnimationFrame(an._mTween[e].id) : clearTimeout(an._mTween[e].id), an._mTween[e].id = null, an._mTween[e].stop = 1)
            }
        }, Z = function (an, e) {
            try {
                delete an[e]
            } catch (am) {
                an[e] = null
            }
        }, ag = function (e) {
            return !(e.which && 1 !== e.which)
        }, A = function (am) {
            var e = am.originalEvent.pointerType;
            return !(e && "touch" !== e && 2 !== e)
        }, Y = function (e) {
            return !isNaN(parseFloat(e)) && isFinite(e)
        }, a = function (am) {
            var e = am.parents(".mCSB_container");
            return [am.offset().top - e.offset().top, am.offset().left - e.offset().left]
        }, al = function () {
            function am() {
                var an = ["webkit", "moz", "ms", "o"];
                if ("hidden" in document) {
                    return "hidden"
                }
                for (var ao = 0; ao < an.length; ao++) {
                    if (an[ao] + "Hidden" in document) {
                        return an[ao] + "Hidden"
                    }
                }
                return null
            }

            var e = am();
            return e ? document[e] : !1
        };
        b.fn[M] = function (e) {
            return S[e] ? S[e].apply(this, Array.prototype.slice.call(arguments, 1)) : "object" != typeof e && e ? void b.error("Method " + e + " does not exist") : S.init.apply(this, arguments)
        }, b[M] = function (e) {
            return S[e] ? S[e].apply(this, Array.prototype.slice.call(arguments, 1)) : "object" != typeof e && e ? void b.error("Method " + e + " does not exist") : S.init.apply(this, arguments)
        }, b[M].defaults = G, window[M] = !0, b(window).bind("load", function () {
            b(L)[M](), b.extend(b.expr[":"], {
                mcsInView: b.expr[":"].mcsInView || function (ao) {
                    var an, e, am = b(ao), ap = am.parents(".mCSB_container");
                    if (ap.length) {
                        return an = ap.parent(), e = [ap[0].offsetTop, ap[0].offsetLeft], e[0] + a(am)[0] >= 0 && e[0] + a(am)[0] < an.height() - am.outerHeight(!1) && e[1] + a(am)[1] >= 0 && e[1] + a(am)[1] < an.width() - am.outerWidth(!1)
                    }
                }, mcsInSight: b.expr[":"].mcsInSight || function (av, ar, e) {
                    var aq, ao, at, ap, au = b(av), am = au.parents(".mCSB_container"), an = "exact" === e[3] ? [[1, 0], [1, 0]] : [[0.9, 0.1], [0.6, 0.4]];
                    if (am.length) {
                        return aq = [au.outerHeight(!1), au.outerWidth(!1)], at = [am[0].offsetTop + a(au)[0], am[0].offsetLeft + a(au)[1]], ao = [am.parent()[0].offsetHeight, am.parent()[0].offsetWidth], ap = [aq[0] < ao[0] ? an[0] : an[1], aq[1] < ao[1] ? an[0] : an[1]], at[0] - ao[0] * ap[0][0] < 0 && at[0] + aq[0] - ao[0] * ap[0][1] >= 0 && at[1] - ao[1] * ap[1][0] < 0 && at[1] + aq[1] - ao[1] * ap[1][1] >= 0
                    }
                }, mcsOverflow: b.expr[":"].mcsOverflow || function (e) {
                    var am = b(e).data(af);
                    if (am) {
                        return am.overflowed[0] || am.overflowed[1]
                    }
                }
            })
        })
    })
});
var ogame = ogame || {};
/*
 * Select2 4.0.0
 * https://select2.github.io
 *
 * Released under the MIT license
 * https://github.com/select2/select2/blob/master/LICENSE.md
 */
(function (b) {
    if (typeof define === "function" && define.amd) {
        define(["./jquery.js"], b)
    } else {
        if (typeof exports === "object") {
            b(require("./jquery.js"))
        } else {
            b(jQuery)
        }
    }
}(function (f) {
    var d = (function () {
        if (f && f.fn && f.fn.select2 && f.fn.select2.amd) {
            var a = f.fn.select2.amd
        }
        var a;
        (function () {
            if (!a || !a.requirejs) {
                if (!a) {
                    a = {}
                } else {
                    h = a
                }
                var c, h, b;
                (function (T) {
                    var P, X, H, G, U = {}, V = {}, L = {}, R = {}, I = Object.prototype.hasOwnProperty, N = [].slice, K = /\.js$/;

                    function J(l, k) {
                        return I.call(l, k)
                    }

                    function F(x, A) {
                        var o, s, v, r, n, w, l, k, p, q, u, m = A && A.split("/"), y = L.map, B = (y && y["*"]) || {};
                        if (x && x.charAt(0) === ".") {
                            if (A) {
                                m = m.slice(0, m.length - 1);
                                x = x.split("/");
                                n = x.length - 1;
                                if (L.nodeIdCompat && K.test(x[n])) {
                                    x[n] = x[n].replace(K, "")
                                }
                                x = m.concat(x);
                                for (p = 0; p < x.length; p += 1) {
                                    u = x[p];
                                    if (u === ".") {
                                        x.splice(p, 1);
                                        p -= 1
                                    } else {
                                        if (u === "..") {
                                            if (p === 1 && (x[2] === ".." || x[0] === "..")) {
                                                break
                                            } else {
                                                if (p > 0) {
                                                    x.splice(p - 1, 2);
                                                    p -= 2
                                                }
                                            }
                                        }
                                    }
                                }
                                x = x.join("/")
                            } else {
                                if (x.indexOf("./") === 0) {
                                    x = x.substring(2)
                                }
                            }
                        }
                        if ((m || B) && y) {
                            o = x.split("/");
                            for (p = o.length; p > 0; p -= 1) {
                                s = o.slice(0, p).join("/");
                                if (m) {
                                    for (q = m.length; q > 0; q -= 1) {
                                        v = y[m.slice(0, q).join("/")];
                                        if (v) {
                                            v = v[s];
                                            if (v) {
                                                r = v;
                                                w = p;
                                                break
                                            }
                                        }
                                    }
                                }
                                if (r) {
                                    break
                                }
                                if (!l && B && B[s]) {
                                    l = B[s];
                                    k = p
                                }
                            }
                            if (!r && l) {
                                r = l;
                                w = k
                            }
                            if (r) {
                                o.splice(0, w, r);
                                x = o.join("/")
                            }
                        }
                        return x
                    }

                    function M(l, k) {
                        return function () {
                            return X.apply(T, N.call(arguments, 0).concat([l, k]))
                        }
                    }

                    function g(k) {
                        return function (l) {
                            return F(l, k)
                        }
                    }

                    function S(k) {
                        return function (l) {
                            U[k] = l
                        }
                    }

                    function Q(k) {
                        if (J(V, k)) {
                            var l = V[k];
                            delete V[k];
                            R[k] = true;
                            P.apply(T, l)
                        }
                        if (!J(U, k) && !J(R, k)) {
                            throw new Error("No " + k)
                        }
                        return U[k]
                    }

                    function O(l) {
                        var k, m = l ? l.indexOf("!") : -1;
                        if (m > -1) {
                            k = l.substring(0, m);
                            l = l.substring(m + 1, l.length)
                        }
                        return [k, l]
                    }

                    H = function (n, o) {
                        var m, k = O(n), l = k[0];
                        n = k[1];
                        if (l) {
                            l = F(l, o);
                            m = Q(l)
                        }
                        if (l) {
                            if (m && m.normalize) {
                                n = m.normalize(n, g(o))
                            } else {
                                n = F(n, o)
                            }
                        } else {
                            n = F(n, o);
                            k = O(n);
                            l = k[0];
                            n = k[1];
                            if (l) {
                                m = Q(l)
                            }
                        }
                        return {f: l ? l + "!" + n : n, n: n, pr: l, p: m}
                    };
                    function W(k) {
                        return function () {
                            return (L && L.config && L.config[k]) || {}
                        }
                    }

                    G = {
                        require: function (k) {
                            return M(k)
                        }, exports: function (l) {
                            var k = U[l];
                            if (typeof k !== "undefined") {
                                return k
                            } else {
                                return (U[l] = {})
                            }
                        }, module: function (k) {
                            return {id: k, uri: "", exports: U[k], config: W(k)}
                        }
                    };
                    P = function (v, k, l, m) {
                        var r, n, q, w, s, p = [], u = typeof l, o;
                        m = m || v;
                        if (u === "undefined" || u === "function") {
                            k = !k.length && l.length ? ["require", "exports", "module"] : k;
                            for (s = 0; s < k.length; s += 1) {
                                w = H(k[s], m);
                                n = w.f;
                                if (n === "require") {
                                    p[s] = G.require(v)
                                } else {
                                    if (n === "exports") {
                                        p[s] = G.exports(v);
                                        o = true
                                    } else {
                                        if (n === "module") {
                                            r = p[s] = G.module(v)
                                        } else {
                                            if (J(U, n) || J(V, n) || J(R, n)) {
                                                p[s] = Q(n)
                                            } else {
                                                if (w.p) {
                                                    w.p.load(w.n, M(m, true), S(n), {});
                                                    p[s] = U[n]
                                                } else {
                                                    throw new Error(v + " missing " + n)
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            q = l ? l.apply(U[v], p) : undefined;
                            if (v) {
                                if (r && r.exports !== T && r.exports !== U[v]) {
                                    U[v] = r.exports
                                } else {
                                    if (q !== T || !o) {
                                        U[v] = q
                                    }
                                }
                            }
                        } else {
                            if (v) {
                                U[v] = l
                            }
                        }
                    };
                    c = h = X = function (l, k, o, n, m) {
                        if (typeof l === "string") {
                            if (G[l]) {
                                return G[l](k)
                            }
                            return Q(H(l, k).f)
                        } else {
                            if (!l.splice) {
                                L = l;
                                if (L.deps) {
                                    X(L.deps, L.callback)
                                }
                                if (!k) {
                                    return
                                }
                                if (k.splice) {
                                    l = k;
                                    k = o;
                                    o = null
                                } else {
                                    l = T
                                }
                            }
                        }
                        k = k || function () {
                        };
                        if (typeof o === "function") {
                            o = n;
                            n = m
                        }
                        if (n) {
                            P(T, l, k, o)
                        } else {
                            setTimeout(function () {
                                P(T, l, k, o)
                            }, 4)
                        }
                        return X
                    };
                    X.config = function (k) {
                        return X(k)
                    };
                    c._defined = U;
                    b = function (m, l, k) {
                        if (!l.splice) {
                            k = l;
                            l = []
                        }
                        if (!J(U, m) && !J(V, m)) {
                            V[m] = [m, l, k]
                        }
                    };
                    b.amd = {jQuery: true}
                }());
                a.requirejs = c;
                a.require = h;
                a.define = b
            }
        }());
        a.define("almond", function () {
        });
        a.define("jquery", [], function () {
            var b = f || $;
            if (b == null && console && console.error) {
                console.error("Select2: An instance of jQuery or a jQuery-compatible library was not found. Make sure that you are including jQuery before Select2 on your web page.")
            }
            return b
        });
        a.define("select2/utils", ["jquery"], function (b) {
            var c = {};
            c.Extend = function (q, h) {
                var g = {}.hasOwnProperty;

                function p() {
                    this.constructor = q
                }

                for (var r in h) {
                    if (g.call(h, r)) {
                        q[r] = h[r]
                    }
                }
                p.prototype = h.prototype;
                q.prototype = new p();
                q.__super__ = h.prototype;
                return q
            };
            function k(m) {
                var q = m.prototype;
                var r = [];
                for (var g in q) {
                    var h = q[g];
                    if (typeof h !== "function") {
                        continue
                    }
                    if (g === "constructor") {
                        continue
                    }
                    r.push(g)
                }
                return r
            }

            c.Decorate = function (F, g) {
                var h = k(g);
                var m = k(F);

                function C() {
                    var o = Array.prototype.unshift;
                    var p = g.prototype.constructor.length;
                    var n = F.prototype.constructor;
                    if (p > 0) {
                        o.call(arguments, F.prototype.constructor);
                        n = g.prototype.constructor
                    }
                    n.apply(this, arguments)
                }

                g.displayName = F.displayName;
                function B() {
                    this.constructor = C
                }

                C.prototype = new B();
                for (var x = 0; x < m.length; x++) {
                    var A = m[x];
                    C.prototype[A] = F.prototype[A]
                }
                var D = function (o) {
                    var n = function () {
                    };
                    if (o in C.prototype) {
                        n = C.prototype[o]
                    }
                    var p = g.prototype[o];
                    return function () {
                        var q = Array.prototype.unshift;
                        q.call(arguments, n);
                        return p.apply(this, arguments)
                    }
                };
                for (var E = 0; E < h.length; E++) {
                    var y = h[E];
                    C.prototype[y] = D(y)
                }
                return C
            };
            var l = function () {
                this.listeners = {}
            };
            l.prototype.on = function (h, g) {
                this.listeners = this.listeners || {};
                if (h in this.listeners) {
                    this.listeners[h].push(g)
                } else {
                    this.listeners[h] = [g]
                }
            };
            l.prototype.trigger = function (h) {
                var g = Array.prototype.slice;
                this.listeners = this.listeners || {};
                if (h in this.listeners) {
                    this.invoke(this.listeners[h], g.call(arguments, 1))
                }
                if ("*" in this.listeners) {
                    this.invoke(this.listeners["*"], arguments)
                }
            };
            l.prototype.invoke = function (g, p) {
                for (var h = 0, o = g.length; h < o; h++) {
                    g[h].apply(this, p)
                }
            };
            c.Observable = l;
            c.generateChars = function (p) {
                var g = "";
                for (var h = 0; h < p; h++) {
                    var o = Math.floor(Math.random() * 36);
                    g += o.toString(36)
                }
                return g
            };
            c.bind = function (g, h) {
                return function () {
                    g.apply(h, arguments)
                }
            };
            c._convertData = function (r) {
                for (var s in r) {
                    var u = s.split("-");
                    var h = r;
                    if (u.length === 1) {
                        continue
                    }
                    for (var g = 0; g < u.length; g++) {
                        var v = u[g];
                        v = v.substring(0, 1).toLowerCase() + v.substring(1);
                        if (!(v in h)) {
                            h[v] = {}
                        }
                        if (g == u.length - 1) {
                            h[v] = r[s]
                        }
                        h = h[v]
                    }
                    delete r[s]
                }
                return r
            };
            c.hasScroll = function (g, q) {
                var r = b(q);
                var h = q.style.overflowX;
                var p = q.style.overflowY;
                if (h === p && (p === "hidden" || p === "visible")) {
                    return false
                }
                if (h === "scroll" || p === "scroll") {
                    return true
                }
                return (r.innerHeight() < q.scrollHeight || r.innerWidth() < q.scrollWidth)
            };
            c.escapeMarkup = function (h) {
                var g = {
                    "\\": "&#92;",
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#39;",
                    "/": "&#47;"
                };
                if (typeof h !== "string") {
                    return h
                }
                return String(h).replace(/[&<>"'\/\\]/g, function (n) {
                    return g[n]
                })
            };
            c.appendMany = function (n, g) {
                if (b.fn.jquery.substr(0, 3) === "1.7") {
                    var h = b();
                    b.map(g, function (m) {
                        h = h.add(m)
                    });
                    g = h
                }
                n.append(g)
            };
            return c
        });
        a.define("select2/results", ["jquery", "./utils"], function (b, c) {
            function h(n, m, g) {
                this.$element = n;
                this.data = g;
                this.options = m;
                h.__super__.constructor.call(this)
            }

            c.Extend(h, c.Observable);
            h.prototype.render = function () {
                var g = b('<ul class="select2-results__options" role="tree"></ul>');
                if (this.options.get("multiple")) {
                    g.attr("aria-multiselectable", "true")
                }
                this.$results = g;
                return g
            };
            h.prototype.clear = function () {
                this.$results.empty()
            };
            h.prototype.displayMessage = function (g) {
                var p = this.options.get("escapeMarkup");
                this.clear();
                this.hideLoading();
                var o = b('<li role="treeitem" class="select2-results__option"></li>');
                var n = this.options.get("translations").get(g.message);
                o.append(p(n(g.args)));
                this.$results.append(o)
            };
            h.prototype.append = function (o) {
                this.hideLoading();
                var q = [];
                if (o.results == null || o.results.length === 0) {
                    if (this.$results.children().length === 0) {
                        this.trigger("results:message", {message: "noResults"})
                    }
                    return
                }
                o.results = this.sort(o.results);
                for (var r = 0; r < o.results.length; r++) {
                    var p = o.results[r];
                    var g = this.option(p);
                    q.push(g)
                }
                this.$results.append(q)
            };
            h.prototype.position = function (n, m) {
                var g = m.find(".select2-results");
                g.append(n)
            };
            h.prototype.sort = function (l) {
                var g = this.options.get("sorter");
                return g(l)
            };
            h.prototype.setClasses = function () {
                var g = this;
                this.data.current(function (r) {
                    var p = b.map(r, function (k) {
                        return k.id.toString()
                    });
                    var q = g.$results.find(".select2-results__option[aria-selected]");
                    q.each(function () {
                        var l = b(this);
                        var m = b.data(this, "data");
                        var k = "" + m.id;
                        if ((m.element != null && m.element.selected) || (m.element == null && b.inArray(k, p) > -1)) {
                            l.attr("aria-selected", "true")
                        } else {
                            l.attr("aria-selected", "false")
                        }
                    });
                    var o = q.filter("[aria-selected=true]");
                    if (o.length > 0) {
                        o.first().trigger("mouseenter")
                    } else {
                        q.first().trigger("mouseenter")
                    }
                })
            };
            h.prototype.showLoading = function (n) {
                this.hideLoading();
                var o = this.options.get("translations").get("searching");
                var g = {disabled: true, loading: true, text: o(n)};
                var p = this.option(g);
                p.className += " loading-results";
                this.$results.prepend(p)
            };
            h.prototype.hideLoading = function () {
                this.$results.find(".loading-results").remove()
            };
            h.prototype.option = function (B) {
                var A = document.createElement("li");
                A.className = "select2-results__option";
                var G = {role: "treeitem", "aria-selected": "false"};
                if (B.disabled) {
                    delete G["aria-selected"];
                    G["aria-disabled"] = "true"
                }
                if (B.id == null) {
                    delete G["aria-selected"]
                }
                if (B._resultId != null) {
                    A.id = B._resultId
                }
                if (B.title) {
                    A.title = B.title
                }
                if (B.children) {
                    G.role = "group";
                    G["aria-label"] = B.text;
                    delete G["aria-selected"]
                }
                for (var y in G) {
                    var C = G[y];
                    A.setAttribute(y, C)
                }
                if (B.children) {
                    var F = b(A);
                    var I = document.createElement("strong");
                    I.className = "select2-results__group";
                    var J = b(I);
                    this.template(B, I);
                    var H = [];
                    for (var g = 0; g < B.children.length; g++) {
                        var D = B.children[g];
                        var E = this.option(D);
                        H.push(E)
                    }
                    var x = b("<ul></ul>", {"class": "select2-results__options select2-results__options--nested"});
                    x.append(H);
                    F.append(I);
                    F.append(x)
                } else {
                    this.template(B, A)
                }
                b.data(A, "data", B);
                return A
            };
            h.prototype.bind = function (p, n) {
                var o = this;
                var g = p.id + "-results";
                this.$results.attr("id", g);
                p.on("results:all", function (k) {
                    o.clear();
                    o.append(k.data);
                    if (p.isOpen()) {
                        o.setClasses()
                    }
                });
                p.on("results:append", function (k) {
                    o.append(k.data);
                    if (p.isOpen()) {
                        o.setClasses()
                    }
                });
                p.on("query", function (k) {
                    o.showLoading(k)
                });
                p.on("select", function () {
                    if (!p.isOpen()) {
                        return
                    }
                    o.setClasses()
                });
                p.on("unselect", function () {
                    if (!p.isOpen()) {
                        return
                    }
                    o.setClasses()
                });
                p.on("open", function () {
                    o.$results.attr("aria-expanded", "true");
                    o.$results.attr("aria-hidden", "false");
                    o.setClasses();
                    o.ensureHighlightVisible()
                });
                p.on("close", function () {
                    o.$results.attr("aria-expanded", "false");
                    o.$results.attr("aria-hidden", "true");
                    o.$results.removeAttr("aria-activedescendant")
                });
                p.on("results:toggle", function () {
                    var k = o.getHighlightedResults();
                    if (k.length === 0) {
                        return
                    }
                    k.trigger("mouseup")
                });
                p.on("results:select", function () {
                    var l = o.getHighlightedResults();
                    if (l.length === 0) {
                        return
                    }
                    var k = l.data("data");
                    if (l.attr("aria-selected") == "true") {
                        o.trigger("close")
                    } else {
                        o.trigger("select", {data: k})
                    }
                });
                p.on("results:previous", function () {
                    var y = o.getHighlightedResults();
                    var A = o.$results.find("[aria-selected]");
                    var w = A.index(y);
                    if (w === 0) {
                        return
                    }
                    var B = w - 1;
                    if (y.length === 0) {
                        B = 0
                    }
                    var x = A.eq(B);
                    x.trigger("mouseenter");
                    var k = o.$results.offset().top;
                    var l = x.offset().top;
                    var m = o.$results.scrollTop() + (l - k);
                    if (B === 0) {
                        o.$results.scrollTop(0)
                    } else {
                        if (l - k < 0) {
                            o.$results.scrollTop(m)
                        }
                    }
                });
                p.on("results:next", function () {
                    var y = o.getHighlightedResults();
                    var A = o.$results.find("[aria-selected]");
                    var w = A.index(y);
                    var B = w + 1;
                    if (B >= A.length) {
                        return
                    }
                    var x = A.eq(B);
                    x.trigger("mouseenter");
                    var k = o.$results.offset().top + o.$results.outerHeight(false);
                    var l = x.offset().top + x.outerHeight(false);
                    var m = o.$results.scrollTop() + l - k;
                    if (B === 0) {
                        o.$results.scrollTop(0)
                    } else {
                        if (l > k) {
                            o.$results.scrollTop(m)
                        }
                    }
                });
                p.on("results:focus", function (k) {
                    k.element.addClass("select2-results__option--highlighted")
                });
                p.on("results:message", function (k) {
                    o.displayMessage(k)
                });
                if (b.fn.mousewheel) {
                    this.$results.on("mousewheel", function (l) {
                        var m = o.$results.scrollTop();
                        var u = (o.$results.get(0).scrollHeight - o.$results.scrollTop() + l.deltaY);
                        var k = l.deltaY > 0 && m - l.deltaY <= 0;
                        var s = l.deltaY < 0 && u <= o.$results.height();
                        if (k) {
                            o.$results.scrollTop(0);
                            l.preventDefault();
                            l.stopPropagation()
                        } else {
                            if (s) {
                                o.$results.scrollTop(o.$results.get(0).scrollHeight - o.$results.height());
                                l.preventDefault();
                                l.stopPropagation()
                            }
                        }
                    })
                }
                this.$results.on("mouseup", ".select2-results__option[aria-selected]", function (m) {
                    var k = b(this);
                    var l = k.data("data");
                    if (k.attr("aria-selected") === "true") {
                        if (o.options.get("multiple")) {
                            o.trigger("unselect", {originalEvent: m, data: l})
                        } else {
                            o.trigger("close")
                        }
                        return
                    }
                    o.trigger("select", {originalEvent: m, data: l})
                });
                this.$results.on("mouseenter", ".select2-results__option[aria-selected]", function (l) {
                    var k = b(this).data("data");
                    o.getHighlightedResults().removeClass("select2-results__option--highlighted");
                    o.trigger("results:focus", {data: k, element: b(this)})
                })
            };
            h.prototype.getHighlightedResults = function () {
                var g = this.$results.find(".select2-results__option--highlighted");
                return g
            };
            h.prototype.destroy = function () {
                this.$results.remove()
            };
            h.prototype.ensureHighlightVisible = function () {
                var r = this.getHighlightedResults();
                if (r.length === 0) {
                    return
                }
                var s = this.$results.find("[aria-selected]");
                var q = s.index(r);
                var v = this.$results.offset().top;
                var w = r.offset().top;
                var g = this.$results.scrollTop() + (w - v);
                var u = w - v;
                g -= r.outerHeight(false) * 2;
                if (q <= 2) {
                    this.$results.scrollTop(0)
                } else {
                    if (u > this.$results.outerHeight() || u < 0) {
                        this.$results.scrollTop(g)
                    }
                }
            };
            h.prototype.template = function (p, o) {
                var g = this.options.get("templateResult");
                var q = this.options.get("escapeMarkup");
                var r = g(p);
                if (r == null) {
                    o.style.display = "none"
                } else {
                    if (typeof r === "string") {
                        o.innerHTML = q(r)
                    } else {
                        b(o).append(r)
                    }
                }
            };
            return h
        });
        a.define("select2/keys", [], function () {
            var b = {
                BACKSPACE: 8,
                TAB: 9,
                ENTER: 13,
                SHIFT: 16,
                CTRL: 17,
                ALT: 18,
                ESC: 27,
                SPACE: 32,
                PAGE_UP: 33,
                PAGE_DOWN: 34,
                END: 35,
                HOME: 36,
                LEFT: 37,
                UP: 38,
                RIGHT: 39,
                DOWN: 40,
                DELETE: 46
            };
            return b
        });
        a.define("select2/selection/base", ["jquery", "../utils", "../keys"], function (b, c, l) {
            function k(h, g) {
                this.$element = h;
                this.options = g;
                k.__super__.constructor.call(this)
            }

            c.Extend(k, c.Observable);
            k.prototype.render = function () {
                var g = b('<span class="select2-selection" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false"></span>');
                this._tabindex = 0;
                if (this.$element.data("old-tabindex") != null) {
                    this._tabindex = this.$element.data("old-tabindex")
                } else {
                    if (this.$element.attr("tabindex") != null) {
                        this._tabindex = this.$element.attr("tabindex")
                    }
                }
                g.attr("title", this.$element.attr("title"));
                g.attr("tabindex", this._tabindex);
                this.$selection = g;
                return g
            };
            k.prototype.bind = function (p, r) {
                var g = this;
                var q = p.id + "-container";
                var h = p.id + "-results";
                this.container = p;
                this.$selection.on("focus", function (m) {
                    g.trigger("focus", m)
                });
                this.$selection.on("blur", function (m) {
                    g.trigger("blur", m)
                });
                this.$selection.on("keydown", function (m) {
                    g.trigger("keypress", m);
                    if (m.which === l.SPACE) {
                        m.preventDefault()
                    }
                });
                p.on("results:focus", function (m) {
                    g.$selection.attr("aria-activedescendant", m.data._resultId)
                });
                p.on("selection:update", function (m) {
                    g.update(m.data)
                });
                p.on("open", function () {
                    g.$selection.attr("aria-expanded", "true");
                    g.$selection.attr("aria-owns", h);
                    g._attachCloseHandler(p)
                });
                p.on("close", function () {
                    g.$selection.attr("aria-expanded", "false");
                    g.$selection.removeAttr("aria-activedescendant");
                    g.$selection.removeAttr("aria-owns");
                    g.$selection.focus();
                    g._detachCloseHandler(p)
                });
                p.on("enable", function () {
                    g.$selection.attr("tabindex", g._tabindex)
                });
                p.on("disable", function () {
                    g.$selection.attr("tabindex", "-1")
                })
            };
            k.prototype._attachCloseHandler = function (h) {
                var g = this;
                b(document.body).on("mousedown.select2." + h.id, function (r) {
                    var q = b(r.target);
                    var u = q.closest(".select2");
                    var s = b(".select2.select2-container--open");
                    s.each(function () {
                        var m = b(this);
                        if (this == u[0]) {
                            return
                        }
                        var n = m.data("element");
                        n.select2("close")
                    })
                })
            };
            k.prototype._detachCloseHandler = function (g) {
                b(document.body).off("mousedown.select2." + g.id)
            };
            k.prototype.position = function (n, g) {
                var h = g.find(".selection");
                h.append(n)
            };
            k.prototype.destroy = function () {
                this._detachCloseHandler(this.container)
            };
            k.prototype.update = function (g) {
                throw new Error("The `update` method must be defined in child classes.")
            };
            return k
        });
        a.define("select2/selection/single", ["jquery", "./base", "../utils", "../keys"], function (c, m, l, n) {
            function b() {
                b.__super__.constructor.apply(this, arguments)
            }

            l.Extend(b, m);
            b.prototype.render = function () {
                var g = b.__super__.render.call(this);
                g.addClass("select2-selection--single");
                g.html('<span class="select2-selection__rendered"></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>');
                return g
            };
            b.prototype.bind = function (h, p) {
                var g = this;
                b.__super__.bind.apply(this, arguments);
                var k = h.id + "-container";
                this.$selection.find(".select2-selection__rendered").attr("id", k);
                this.$selection.attr("aria-labelledby", k);
                this.$selection.on("mousedown", function (o) {
                    if (o.which !== 1) {
                        return
                    }
                    g.trigger("toggle", {originalEvent: o})
                });
                this.$selection.on("focus", function (o) {
                });
                this.$selection.on("blur", function (o) {
                });
                h.on("selection:update", function (o) {
                    g.update(o.data)
                })
            };
            b.prototype.clear = function () {
                this.$selection.find(".select2-selection__rendered").empty()
            };
            b.prototype.display = function (k) {
                var g = this.options.get("templateSelection");
                var h = this.options.get("escapeMarkup");
                return h(g(k))
            };
            b.prototype.selectionContainer = function () {
                return c("<span></span>")
            };
            b.prototype.update = function (p) {
                if (p.length === 0) {
                    this.clear();
                    return
                }
                var h = p[0];
                var g = this.display(h);
                var k = this.$selection.find(".select2-selection__rendered");
                k.empty().append(g);
                k.prop("title", h.title || h.text)
            };
            return b
        });
        a.define("select2/selection/multiple", ["jquery", "./base", "../utils"], function (b, l, c) {
            function k(h, g) {
                k.__super__.constructor.apply(this, arguments)
            }

            c.Extend(k, l);
            k.prototype.render = function () {
                var g = k.__super__.render.call(this);
                g.addClass("select2-selection--multiple");
                g.html('<ul class="select2-selection__rendered"></ul>');
                return g
            };
            k.prototype.bind = function (n, g) {
                var h = this;
                k.__super__.bind.apply(this, arguments);
                this.$selection.on("click", function (m) {
                    h.trigger("toggle", {originalEvent: m})
                });
                this.$selection.on("click", ".select2-selection__choice__remove", function (s) {
                    var r = b(this);
                    var u = r.parent();
                    var m = u.data("data");
                    h.trigger("unselect", {originalEvent: s, data: m})
                })
            };
            k.prototype.clear = function () {
                this.$selection.find(".select2-selection__rendered").empty()
            };
            k.prototype.display = function (g) {
                var h = this.options.get("templateSelection");
                var n = this.options.get("escapeMarkup");
                return n(h(g))
            };
            k.prototype.selectionContainer = function () {
                var g = b('<li class="select2-selection__choice"><span class="select2-selection__choice__remove" role="presentation">&times;</span></li>');
                return g
            };
            k.prototype.update = function (v) {
                this.clear();
                if (v.length === 0) {
                    return
                }
                var h = [];
                for (var s = 0; s < v.length; s++) {
                    var g = v[s];
                    var w = this.display(g);
                    var r = this.selectionContainer();
                    r.append(w);
                    r.prop("title", g.title || g.text);
                    r.data("data", g);
                    h.push(r)
                }
                var u = this.$selection.find(".select2-selection__rendered");
                c.appendMany(u, h)
            };
            return k
        });
        a.define("select2/selection/placeholder", ["../utils"], function (b) {
            function c(l, n, m) {
                this.placeholder = this.normalizePlaceholder(m.get("placeholder"));
                l.call(this, n, m)
            }

            c.prototype.normalizePlaceholder = function (l, k) {
                if (typeof k === "string") {
                    k = {id: "", text: k}
                }
                return k
            };
            c.prototype.createPlaceholder = function (m, l) {
                var n = this.selectionContainer();
                n.html(this.display(l));
                n.addClass("select2-selection__placeholder").removeClass("select2-selection__choice");
                return n
            };
            c.prototype.update = function (n, o) {
                var q = (o.length == 1 && o[0].id != this.placeholder.id);
                var p = o.length > 1;
                if (p || q) {
                    return n.call(this, o)
                }
                this.clear();
                var r = this.createPlaceholder(this.placeholder);
                this.$selection.find(".select2-selection__rendered").append(r)
            };
            return c
        });
        a.define("select2/selection/allowClear", ["jquery", "../keys"], function (c, h) {
            function b() {
            }

            b.prototype.bind = function (n, p, g) {
                var o = this;
                n.call(this, p, g);
                if (this.placeholder == null) {
                    if (this.options.get("debug") && window.console && console.error) {
                        console.error("Select2: The `allowClear` option should be used in combination with the `placeholder` option.")
                    }
                }
                this.$selection.on("mousedown", ".select2-selection__clear", function (k) {
                    o._handleClear(k)
                });
                p.on("keypress", function (k) {
                    o._handleKeyboardClear(k, p)
                })
            };
            b.prototype._handleClear = function (q, r) {
                if (this.options.get("disabled")) {
                    return
                }
                var u = this.$selection.find(".select2-selection__clear");
                if (u.length === 0) {
                    return
                }
                r.stopPropagation();
                var g = u.data("data");
                for (var s = 0; s < g.length; s++) {
                    var p = {data: g[s]};
                    this.trigger("unselect", p);
                    if (p.prevented) {
                        return
                    }
                }
                this.$element.val(this.placeholder.id).trigger("change");
                this.trigger("toggle")
            };
            b.prototype._handleKeyboardClear = function (g, m, n) {
                if (n.isOpen()) {
                    return
                }
                if (m.which == h.DELETE || m.which == h.BACKSPACE) {
                    this._handleClear(m)
                }
            };
            b.prototype.update = function (g, m) {
                g.call(this, m);
                if (this.$selection.find(".select2-selection__placeholder").length > 0 || m.length === 0) {
                    return
                }
                var n = c('<span class="select2-selection__clear">&times;</span>');
                n.data("data", m);
                this.$selection.find(".select2-selection__rendered").prepend(n)
            };
            return b
        });
        a.define("select2/selection/search", ["jquery", "../utils", "../keys"], function (b, c, l) {
            function k(g, n, h) {
                g.call(this, n, h)
            }

            k.prototype.render = function (h) {
                var n = b('<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" /></li>');
                this.$searchContainer = n;
                this.$search = n.find("input");
                var g = h.call(this);
                return g
            };
            k.prototype.bind = function (g, o, p) {
                var h = this;
                g.call(this, o, p);
                o.on("open", function () {
                    h.$search.attr("tabindex", 0);
                    h.$search.focus()
                });
                o.on("close", function () {
                    h.$search.attr("tabindex", -1);
                    h.$search.val("");
                    h.$search.focus()
                });
                o.on("enable", function () {
                    h.$search.prop("disabled", false)
                });
                o.on("disable", function () {
                    h.$search.prop("disabled", true)
                });
                this.$selection.on("focusin", ".select2-search--inline", function (m) {
                    h.trigger("focus", m)
                });
                this.$selection.on("focusout", ".select2-search--inline", function (m) {
                    h.trigger("blur", m)
                });
                this.$selection.on("keydown", ".select2-search--inline", function (u) {
                    u.stopPropagation();
                    h.trigger("keypress", u);
                    h._keyUpPrevented = u.isDefaultPrevented();
                    var n = u.which;
                    if (n === l.BACKSPACE && h.$search.val() === "") {
                        var s = h.$searchContainer.prev(".select2-selection__choice");
                        if (s.length > 0) {
                            var m = s.data("data");
                            h.searchRemoveChoice(m);
                            u.preventDefault()
                        }
                    }
                });
                this.$selection.on("input", ".select2-search--inline", function (m) {
                    h.$selection.off("keyup.search")
                });
                this.$selection.on("keyup.search input", ".select2-search--inline", function (m) {
                    h.handleSearch(m)
                })
            };
            k.prototype.createPlaceholder = function (h, g) {
                this.$search.attr("placeholder", g.text)
            };
            k.prototype.update = function (g, h) {
                this.$search.attr("placeholder", "");
                g.call(this, h);
                this.$selection.find(".select2-selection__rendered").append(this.$searchContainer);
                this.resizeSearch()
            };
            k.prototype.handleSearch = function () {
                this.resizeSearch();
                if (!this._keyUpPrevented) {
                    var g = this.$search.val();
                    this.trigger("query", {term: g})
                }
                this._keyUpPrevented = false
            };
            k.prototype.searchRemoveChoice = function (g, h) {
                this.trigger("unselect", {data: h});
                this.trigger("open");
                this.$search.val(h.text + " ")
            };
            k.prototype.resizeSearch = function () {
                this.$search.css("width", "25px");
                var h = "";
                if (this.$search.attr("placeholder") !== "") {
                    h = this.$selection.find(".select2-selection__rendered").innerWidth()
                } else {
                    var g = this.$search.val().length + 1;
                    h = (g * 0.75) + "em"
                }
                this.$search.css("width", h)
            };
            return k
        });
        a.define("select2/selection/eventRelay", ["jquery"], function (c) {
            function b() {
            }

            b.prototype.bind = function (o, s, u) {
                var r = this;
                var q = ["open", "opening", "close", "closing", "select", "selecting", "unselect", "unselecting"];
                var p = ["opening", "closing", "selecting", "unselecting"];
                o.call(this, s, u);
                s.on("*", function (h, g) {
                    if (c.inArray(h, q) === -1) {
                        return
                    }
                    g = g || {};
                    var k = c.Event("select2:" + h, {params: g});
                    r.$element.trigger(k);
                    if (c.inArray(h, p) === -1) {
                        return
                    }
                    g.prevented = k.isDefaultPrevented()
                })
            };
            return b
        });
        a.define("select2/translation", ["jquery", "require"], function (b, c) {
            function h(g) {
                this.dict = g || {}
            }

            h.prototype.all = function () {
                return this.dict
            };
            h.prototype.get = function (g) {
                return this.dict[g]
            };
            h.prototype.extend = function (g) {
                this.dict = b.extend({}, g.all(), this.dict)
            };
            h._cache = {};
            h.loadPath = function (g) {
                if (!(g in h._cache)) {
                    var l = c(g);
                    h._cache[g] = l
                }
                return new h(h._cache[g])
            };
            return h
        });
        a.define("select2/diacritics", [], function () {
            var b = {
                "\u24B6": "A",
                "\uFF21": "A",
                "\u00C0": "A",
                "\u00C1": "A",
                "\u00C2": "A",
                "\u1EA6": "A",
                "\u1EA4": "A",
                "\u1EAA": "A",
                "\u1EA8": "A",
                "\u00C3": "A",
                "\u0100": "A",
                "\u0102": "A",
                "\u1EB0": "A",
                "\u1EAE": "A",
                "\u1EB4": "A",
                "\u1EB2": "A",
                "\u0226": "A",
                "\u01E0": "A",
                "\u00C4": "A",
                "\u01DE": "A",
                "\u1EA2": "A",
                "\u00C5": "A",
                "\u01FA": "A",
                "\u01CD": "A",
                "\u0200": "A",
                "\u0202": "A",
                "\u1EA0": "A",
                "\u1EAC": "A",
                "\u1EB6": "A",
                "\u1E00": "A",
                "\u0104": "A",
                "\u023A": "A",
                "\u2C6F": "A",
                "\uA732": "AA",
                "\u00C6": "AE",
                "\u01FC": "AE",
                "\u01E2": "AE",
                "\uA734": "AO",
                "\uA736": "AU",
                "\uA738": "AV",
                "\uA73A": "AV",
                "\uA73C": "AY",
                "\u24B7": "B",
                "\uFF22": "B",
                "\u1E02": "B",
                "\u1E04": "B",
                "\u1E06": "B",
                "\u0243": "B",
                "\u0182": "B",
                "\u0181": "B",
                "\u24B8": "C",
                "\uFF23": "C",
                "\u0106": "C",
                "\u0108": "C",
                "\u010A": "C",
                "\u010C": "C",
                "\u00C7": "C",
                "\u1E08": "C",
                "\u0187": "C",
                "\u023B": "C",
                "\uA73E": "C",
                "\u24B9": "D",
                "\uFF24": "D",
                "\u1E0A": "D",
                "\u010E": "D",
                "\u1E0C": "D",
                "\u1E10": "D",
                "\u1E12": "D",
                "\u1E0E": "D",
                "\u0110": "D",
                "\u018B": "D",
                "\u018A": "D",
                "\u0189": "D",
                "\uA779": "D",
                "\u01F1": "DZ",
                "\u01C4": "DZ",
                "\u01F2": "Dz",
                "\u01C5": "Dz",
                "\u24BA": "E",
                "\uFF25": "E",
                "\u00C8": "E",
                "\u00C9": "E",
                "\u00CA": "E",
                "\u1EC0": "E",
                "\u1EBE": "E",
                "\u1EC4": "E",
                "\u1EC2": "E",
                "\u1EBC": "E",
                "\u0112": "E",
                "\u1E14": "E",
                "\u1E16": "E",
                "\u0114": "E",
                "\u0116": "E",
                "\u00CB": "E",
                "\u1EBA": "E",
                "\u011A": "E",
                "\u0204": "E",
                "\u0206": "E",
                "\u1EB8": "E",
                "\u1EC6": "E",
                "\u0228": "E",
                "\u1E1C": "E",
                "\u0118": "E",
                "\u1E18": "E",
                "\u1E1A": "E",
                "\u0190": "E",
                "\u018E": "E",
                "\u24BB": "F",
                "\uFF26": "F",
                "\u1E1E": "F",
                "\u0191": "F",
                "\uA77B": "F",
                "\u24BC": "G",
                "\uFF27": "G",
                "\u01F4": "G",
                "\u011C": "G",
                "\u1E20": "G",
                "\u011E": "G",
                "\u0120": "G",
                "\u01E6": "G",
                "\u0122": "G",
                "\u01E4": "G",
                "\u0193": "G",
                "\uA7A0": "G",
                "\uA77D": "G",
                "\uA77E": "G",
                "\u24BD": "H",
                "\uFF28": "H",
                "\u0124": "H",
                "\u1E22": "H",
                "\u1E26": "H",
                "\u021E": "H",
                "\u1E24": "H",
                "\u1E28": "H",
                "\u1E2A": "H",
                "\u0126": "H",
                "\u2C67": "H",
                "\u2C75": "H",
                "\uA78D": "H",
                "\u24BE": "I",
                "\uFF29": "I",
                "\u00CC": "I",
                "\u00CD": "I",
                "\u00CE": "I",
                "\u0128": "I",
                "\u012A": "I",
                "\u012C": "I",
                "\u0130": "I",
                "\u00CF": "I",
                "\u1E2E": "I",
                "\u1EC8": "I",
                "\u01CF": "I",
                "\u0208": "I",
                "\u020A": "I",
                "\u1ECA": "I",
                "\u012E": "I",
                "\u1E2C": "I",
                "\u0197": "I",
                "\u24BF": "J",
                "\uFF2A": "J",
                "\u0134": "J",
                "\u0248": "J",
                "\u24C0": "K",
                "\uFF2B": "K",
                "\u1E30": "K",
                "\u01E8": "K",
                "\u1E32": "K",
                "\u0136": "K",
                "\u1E34": "K",
                "\u0198": "K",
                "\u2C69": "K",
                "\uA740": "K",
                "\uA742": "K",
                "\uA744": "K",
                "\uA7A2": "K",
                "\u24C1": "L",
                "\uFF2C": "L",
                "\u013F": "L",
                "\u0139": "L",
                "\u013D": "L",
                "\u1E36": "L",
                "\u1E38": "L",
                "\u013B": "L",
                "\u1E3C": "L",
                "\u1E3A": "L",
                "\u0141": "L",
                "\u023D": "L",
                "\u2C62": "L",
                "\u2C60": "L",
                "\uA748": "L",
                "\uA746": "L",
                "\uA780": "L",
                "\u01C7": "LJ",
                "\u01C8": "Lj",
                "\u24C2": "M",
                "\uFF2D": "M",
                "\u1E3E": "M",
                "\u1E40": "M",
                "\u1E42": "M",
                "\u2C6E": "M",
                "\u019C": "M",
                "\u24C3": "N",
                "\uFF2E": "N",
                "\u01F8": "N",
                "\u0143": "N",
                "\u00D1": "N",
                "\u1E44": "N",
                "\u0147": "N",
                "\u1E46": "N",
                "\u0145": "N",
                "\u1E4A": "N",
                "\u1E48": "N",
                "\u0220": "N",
                "\u019D": "N",
                "\uA790": "N",
                "\uA7A4": "N",
                "\u01CA": "NJ",
                "\u01CB": "Nj",
                "\u24C4": "O",
                "\uFF2F": "O",
                "\u00D2": "O",
                "\u00D3": "O",
                "\u00D4": "O",
                "\u1ED2": "O",
                "\u1ED0": "O",
                "\u1ED6": "O",
                "\u1ED4": "O",
                "\u00D5": "O",
                "\u1E4C": "O",
                "\u022C": "O",
                "\u1E4E": "O",
                "\u014C": "O",
                "\u1E50": "O",
                "\u1E52": "O",
                "\u014E": "O",
                "\u022E": "O",
                "\u0230": "O",
                "\u00D6": "O",
                "\u022A": "O",
                "\u1ECE": "O",
                "\u0150": "O",
                "\u01D1": "O",
                "\u020C": "O",
                "\u020E": "O",
                "\u01A0": "O",
                "\u1EDC": "O",
                "\u1EDA": "O",
                "\u1EE0": "O",
                "\u1EDE": "O",
                "\u1EE2": "O",
                "\u1ECC": "O",
                "\u1ED8": "O",
                "\u01EA": "O",
                "\u01EC": "O",
                "\u00D8": "O",
                "\u01FE": "O",
                "\u0186": "O",
                "\u019F": "O",
                "\uA74A": "O",
                "\uA74C": "O",
                "\u01A2": "OI",
                "\uA74E": "OO",
                "\u0222": "OU",
                "\u24C5": "P",
                "\uFF30": "P",
                "\u1E54": "P",
                "\u1E56": "P",
                "\u01A4": "P",
                "\u2C63": "P",
                "\uA750": "P",
                "\uA752": "P",
                "\uA754": "P",
                "\u24C6": "Q",
                "\uFF31": "Q",
                "\uA756": "Q",
                "\uA758": "Q",
                "\u024A": "Q",
                "\u24C7": "R",
                "\uFF32": "R",
                "\u0154": "R",
                "\u1E58": "R",
                "\u0158": "R",
                "\u0210": "R",
                "\u0212": "R",
                "\u1E5A": "R",
                "\u1E5C": "R",
                "\u0156": "R",
                "\u1E5E": "R",
                "\u024C": "R",
                "\u2C64": "R",
                "\uA75A": "R",
                "\uA7A6": "R",
                "\uA782": "R",
                "\u24C8": "S",
                "\uFF33": "S",
                "\u1E9E": "S",
                "\u015A": "S",
                "\u1E64": "S",
                "\u015C": "S",
                "\u1E60": "S",
                "\u0160": "S",
                "\u1E66": "S",
                "\u1E62": "S",
                "\u1E68": "S",
                "\u0218": "S",
                "\u015E": "S",
                "\u2C7E": "S",
                "\uA7A8": "S",
                "\uA784": "S",
                "\u24C9": "T",
                "\uFF34": "T",
                "\u1E6A": "T",
                "\u0164": "T",
                "\u1E6C": "T",
                "\u021A": "T",
                "\u0162": "T",
                "\u1E70": "T",
                "\u1E6E": "T",
                "\u0166": "T",
                "\u01AC": "T",
                "\u01AE": "T",
                "\u023E": "T",
                "\uA786": "T",
                "\uA728": "TZ",
                "\u24CA": "U",
                "\uFF35": "U",
                "\u00D9": "U",
                "\u00DA": "U",
                "\u00DB": "U",
                "\u0168": "U",
                "\u1E78": "U",
                "\u016A": "U",
                "\u1E7A": "U",
                "\u016C": "U",
                "\u00DC": "U",
                "\u01DB": "U",
                "\u01D7": "U",
                "\u01D5": "U",
                "\u01D9": "U",
                "\u1EE6": "U",
                "\u016E": "U",
                "\u0170": "U",
                "\u01D3": "U",
                "\u0214": "U",
                "\u0216": "U",
                "\u01AF": "U",
                "\u1EEA": "U",
                "\u1EE8": "U",
                "\u1EEE": "U",
                "\u1EEC": "U",
                "\u1EF0": "U",
                "\u1EE4": "U",
                "\u1E72": "U",
                "\u0172": "U",
                "\u1E76": "U",
                "\u1E74": "U",
                "\u0244": "U",
                "\u24CB": "V",
                "\uFF36": "V",
                "\u1E7C": "V",
                "\u1E7E": "V",
                "\u01B2": "V",
                "\uA75E": "V",
                "\u0245": "V",
                "\uA760": "VY",
                "\u24CC": "W",
                "\uFF37": "W",
                "\u1E80": "W",
                "\u1E82": "W",
                "\u0174": "W",
                "\u1E86": "W",
                "\u1E84": "W",
                "\u1E88": "W",
                "\u2C72": "W",
                "\u24CD": "X",
                "\uFF38": "X",
                "\u1E8A": "X",
                "\u1E8C": "X",
                "\u24CE": "Y",
                "\uFF39": "Y",
                "\u1EF2": "Y",
                "\u00DD": "Y",
                "\u0176": "Y",
                "\u1EF8": "Y",
                "\u0232": "Y",
                "\u1E8E": "Y",
                "\u0178": "Y",
                "\u1EF6": "Y",
                "\u1EF4": "Y",
                "\u01B3": "Y",
                "\u024E": "Y",
                "\u1EFE": "Y",
                "\u24CF": "Z",
                "\uFF3A": "Z",
                "\u0179": "Z",
                "\u1E90": "Z",
                "\u017B": "Z",
                "\u017D": "Z",
                "\u1E92": "Z",
                "\u1E94": "Z",
                "\u01B5": "Z",
                "\u0224": "Z",
                "\u2C7F": "Z",
                "\u2C6B": "Z",
                "\uA762": "Z",
                "\u24D0": "a",
                "\uFF41": "a",
                "\u1E9A": "a",
                "\u00E0": "a",
                "\u00E1": "a",
                "\u00E2": "a",
                "\u1EA7": "a",
                "\u1EA5": "a",
                "\u1EAB": "a",
                "\u1EA9": "a",
                "\u00E3": "a",
                "\u0101": "a",
                "\u0103": "a",
                "\u1EB1": "a",
                "\u1EAF": "a",
                "\u1EB5": "a",
                "\u1EB3": "a",
                "\u0227": "a",
                "\u01E1": "a",
                "\u00E4": "a",
                "\u01DF": "a",
                "\u1EA3": "a",
                "\u00E5": "a",
                "\u01FB": "a",
                "\u01CE": "a",
                "\u0201": "a",
                "\u0203": "a",
                "\u1EA1": "a",
                "\u1EAD": "a",
                "\u1EB7": "a",
                "\u1E01": "a",
                "\u0105": "a",
                "\u2C65": "a",
                "\u0250": "a",
                "\uA733": "aa",
                "\u00E6": "ae",
                "\u01FD": "ae",
                "\u01E3": "ae",
                "\uA735": "ao",
                "\uA737": "au",
                "\uA739": "av",
                "\uA73B": "av",
                "\uA73D": "ay",
                "\u24D1": "b",
                "\uFF42": "b",
                "\u1E03": "b",
                "\u1E05": "b",
                "\u1E07": "b",
                "\u0180": "b",
                "\u0183": "b",
                "\u0253": "b",
                "\u24D2": "c",
                "\uFF43": "c",
                "\u0107": "c",
                "\u0109": "c",
                "\u010B": "c",
                "\u010D": "c",
                "\u00E7": "c",
                "\u1E09": "c",
                "\u0188": "c",
                "\u023C": "c",
                "\uA73F": "c",
                "\u2184": "c",
                "\u24D3": "d",
                "\uFF44": "d",
                "\u1E0B": "d",
                "\u010F": "d",
                "\u1E0D": "d",
                "\u1E11": "d",
                "\u1E13": "d",
                "\u1E0F": "d",
                "\u0111": "d",
                "\u018C": "d",
                "\u0256": "d",
                "\u0257": "d",
                "\uA77A": "d",
                "\u01F3": "dz",
                "\u01C6": "dz",
                "\u24D4": "e",
                "\uFF45": "e",
                "\u00E8": "e",
                "\u00E9": "e",
                "\u00EA": "e",
                "\u1EC1": "e",
                "\u1EBF": "e",
                "\u1EC5": "e",
                "\u1EC3": "e",
                "\u1EBD": "e",
                "\u0113": "e",
                "\u1E15": "e",
                "\u1E17": "e",
                "\u0115": "e",
                "\u0117": "e",
                "\u00EB": "e",
                "\u1EBB": "e",
                "\u011B": "e",
                "\u0205": "e",
                "\u0207": "e",
                "\u1EB9": "e",
                "\u1EC7": "e",
                "\u0229": "e",
                "\u1E1D": "e",
                "\u0119": "e",
                "\u1E19": "e",
                "\u1E1B": "e",
                "\u0247": "e",
                "\u025B": "e",
                "\u01DD": "e",
                "\u24D5": "f",
                "\uFF46": "f",
                "\u1E1F": "f",
                "\u0192": "f",
                "\uA77C": "f",
                "\u24D6": "g",
                "\uFF47": "g",
                "\u01F5": "g",
                "\u011D": "g",
                "\u1E21": "g",
                "\u011F": "g",
                "\u0121": "g",
                "\u01E7": "g",
                "\u0123": "g",
                "\u01E5": "g",
                "\u0260": "g",
                "\uA7A1": "g",
                "\u1D79": "g",
                "\uA77F": "g",
                "\u24D7": "h",
                "\uFF48": "h",
                "\u0125": "h",
                "\u1E23": "h",
                "\u1E27": "h",
                "\u021F": "h",
                "\u1E25": "h",
                "\u1E29": "h",
                "\u1E2B": "h",
                "\u1E96": "h",
                "\u0127": "h",
                "\u2C68": "h",
                "\u2C76": "h",
                "\u0265": "h",
                "\u0195": "hv",
                "\u24D8": "i",
                "\uFF49": "i",
                "\u00EC": "i",
                "\u00ED": "i",
                "\u00EE": "i",
                "\u0129": "i",
                "\u012B": "i",
                "\u012D": "i",
                "\u00EF": "i",
                "\u1E2F": "i",
                "\u1EC9": "i",
                "\u01D0": "i",
                "\u0209": "i",
                "\u020B": "i",
                "\u1ECB": "i",
                "\u012F": "i",
                "\u1E2D": "i",
                "\u0268": "i",
                "\u0131": "i",
                "\u24D9": "j",
                "\uFF4A": "j",
                "\u0135": "j",
                "\u01F0": "j",
                "\u0249": "j",
                "\u24DA": "k",
                "\uFF4B": "k",
                "\u1E31": "k",
                "\u01E9": "k",
                "\u1E33": "k",
                "\u0137": "k",
                "\u1E35": "k",
                "\u0199": "k",
                "\u2C6A": "k",
                "\uA741": "k",
                "\uA743": "k",
                "\uA745": "k",
                "\uA7A3": "k",
                "\u24DB": "l",
                "\uFF4C": "l",
                "\u0140": "l",
                "\u013A": "l",
                "\u013E": "l",
                "\u1E37": "l",
                "\u1E39": "l",
                "\u013C": "l",
                "\u1E3D": "l",
                "\u1E3B": "l",
                "\u017F": "l",
                "\u0142": "l",
                "\u019A": "l",
                "\u026B": "l",
                "\u2C61": "l",
                "\uA749": "l",
                "\uA781": "l",
                "\uA747": "l",
                "\u01C9": "lj",
                "\u24DC": "m",
                "\uFF4D": "m",
                "\u1E3F": "m",
                "\u1E41": "m",
                "\u1E43": "m",
                "\u0271": "m",
                "\u026F": "m",
                "\u24DD": "n",
                "\uFF4E": "n",
                "\u01F9": "n",
                "\u0144": "n",
                "\u00F1": "n",
                "\u1E45": "n",
                "\u0148": "n",
                "\u1E47": "n",
                "\u0146": "n",
                "\u1E4B": "n",
                "\u1E49": "n",
                "\u019E": "n",
                "\u0272": "n",
                "\u0149": "n",
                "\uA791": "n",
                "\uA7A5": "n",
                "\u01CC": "nj",
                "\u24DE": "o",
                "\uFF4F": "o",
                "\u00F2": "o",
                "\u00F3": "o",
                "\u00F4": "o",
                "\u1ED3": "o",
                "\u1ED1": "o",
                "\u1ED7": "o",
                "\u1ED5": "o",
                "\u00F5": "o",
                "\u1E4D": "o",
                "\u022D": "o",
                "\u1E4F": "o",
                "\u014D": "o",
                "\u1E51": "o",
                "\u1E53": "o",
                "\u014F": "o",
                "\u022F": "o",
                "\u0231": "o",
                "\u00F6": "o",
                "\u022B": "o",
                "\u1ECF": "o",
                "\u0151": "o",
                "\u01D2": "o",
                "\u020D": "o",
                "\u020F": "o",
                "\u01A1": "o",
                "\u1EDD": "o",
                "\u1EDB": "o",
                "\u1EE1": "o",
                "\u1EDF": "o",
                "\u1EE3": "o",
                "\u1ECD": "o",
                "\u1ED9": "o",
                "\u01EB": "o",
                "\u01ED": "o",
                "\u00F8": "o",
                "\u01FF": "o",
                "\u0254": "o",
                "\uA74B": "o",
                "\uA74D": "o",
                "\u0275": "o",
                "\u01A3": "oi",
                "\u0223": "ou",
                "\uA74F": "oo",
                "\u24DF": "p",
                "\uFF50": "p",
                "\u1E55": "p",
                "\u1E57": "p",
                "\u01A5": "p",
                "\u1D7D": "p",
                "\uA751": "p",
                "\uA753": "p",
                "\uA755": "p",
                "\u24E0": "q",
                "\uFF51": "q",
                "\u024B": "q",
                "\uA757": "q",
                "\uA759": "q",
                "\u24E1": "r",
                "\uFF52": "r",
                "\u0155": "r",
                "\u1E59": "r",
                "\u0159": "r",
                "\u0211": "r",
                "\u0213": "r",
                "\u1E5B": "r",
                "\u1E5D": "r",
                "\u0157": "r",
                "\u1E5F": "r",
                "\u024D": "r",
                "\u027D": "r",
                "\uA75B": "r",
                "\uA7A7": "r",
                "\uA783": "r",
                "\u24E2": "s",
                "\uFF53": "s",
                "\u00DF": "s",
                "\u015B": "s",
                "\u1E65": "s",
                "\u015D": "s",
                "\u1E61": "s",
                "\u0161": "s",
                "\u1E67": "s",
                "\u1E63": "s",
                "\u1E69": "s",
                "\u0219": "s",
                "\u015F": "s",
                "\u023F": "s",
                "\uA7A9": "s",
                "\uA785": "s",
                "\u1E9B": "s",
                "\u24E3": "t",
                "\uFF54": "t",
                "\u1E6B": "t",
                "\u1E97": "t",
                "\u0165": "t",
                "\u1E6D": "t",
                "\u021B": "t",
                "\u0163": "t",
                "\u1E71": "t",
                "\u1E6F": "t",
                "\u0167": "t",
                "\u01AD": "t",
                "\u0288": "t",
                "\u2C66": "t",
                "\uA787": "t",
                "\uA729": "tz",
                "\u24E4": "u",
                "\uFF55": "u",
                "\u00F9": "u",
                "\u00FA": "u",
                "\u00FB": "u",
                "\u0169": "u",
                "\u1E79": "u",
                "\u016B": "u",
                "\u1E7B": "u",
                "\u016D": "u",
                "\u00FC": "u",
                "\u01DC": "u",
                "\u01D8": "u",
                "\u01D6": "u",
                "\u01DA": "u",
                "\u1EE7": "u",
                "\u016F": "u",
                "\u0171": "u",
                "\u01D4": "u",
                "\u0215": "u",
                "\u0217": "u",
                "\u01B0": "u",
                "\u1EEB": "u",
                "\u1EE9": "u",
                "\u1EEF": "u",
                "\u1EED": "u",
                "\u1EF1": "u",
                "\u1EE5": "u",
                "\u1E73": "u",
                "\u0173": "u",
                "\u1E77": "u",
                "\u1E75": "u",
                "\u0289": "u",
                "\u24E5": "v",
                "\uFF56": "v",
                "\u1E7D": "v",
                "\u1E7F": "v",
                "\u028B": "v",
                "\uA75F": "v",
                "\u028C": "v",
                "\uA761": "vy",
                "\u24E6": "w",
                "\uFF57": "w",
                "\u1E81": "w",
                "\u1E83": "w",
                "\u0175": "w",
                "\u1E87": "w",
                "\u1E85": "w",
                "\u1E98": "w",
                "\u1E89": "w",
                "\u2C73": "w",
                "\u24E7": "x",
                "\uFF58": "x",
                "\u1E8B": "x",
                "\u1E8D": "x",
                "\u24E8": "y",
                "\uFF59": "y",
                "\u1EF3": "y",
                "\u00FD": "y",
                "\u0177": "y",
                "\u1EF9": "y",
                "\u0233": "y",
                "\u1E8F": "y",
                "\u00FF": "y",
                "\u1EF7": "y",
                "\u1E99": "y",
                "\u1EF5": "y",
                "\u01B4": "y",
                "\u024F": "y",
                "\u1EFF": "y",
                "\u24E9": "z",
                "\uFF5A": "z",
                "\u017A": "z",
                "\u1E91": "z",
                "\u017C": "z",
                "\u017E": "z",
                "\u1E93": "z",
                "\u1E95": "z",
                "\u01B6": "z",
                "\u0225": "z",
                "\u0240": "z",
                "\u2C6C": "z",
                "\uA763": "z",
                "\u0386": "\u0391",
                "\u0388": "\u0395",
                "\u0389": "\u0397",
                "\u038A": "\u0399",
                "\u03AA": "\u0399",
                "\u038C": "\u039F",
                "\u038E": "\u03A5",
                "\u03AB": "\u03A5",
                "\u038F": "\u03A9",
                "\u03AC": "\u03B1",
                "\u03AD": "\u03B5",
                "\u03AE": "\u03B7",
                "\u03AF": "\u03B9",
                "\u03CA": "\u03B9",
                "\u0390": "\u03B9",
                "\u03CC": "\u03BF",
                "\u03CD": "\u03C5",
                "\u03CB": "\u03C5",
                "\u03B0": "\u03C5",
                "\u03C9": "\u03C9",
                "\u03C2": "\u03C3"
            };
            return b
        });
        a.define("select2/data/base", ["../utils"], function (b) {
            function c(l, k) {
                c.__super__.constructor.call(this)
            }

            b.Extend(c, b.Observable);
            c.prototype.current = function (h) {
                throw new Error("The `current` method must be defined in child classes.")
            };
            c.prototype.query = function (l, k) {
                throw new Error("The `query` method must be defined in child classes.")
            };
            c.prototype.bind = function (l, k) {
            };
            c.prototype.destroy = function () {
            };
            c.prototype.generateResultId = function (n, m) {
                var l = n.id + "-result-";
                l += b.generateChars(4);
                if (m.id != null) {
                    l += "-" + m.id.toString()
                } else {
                    l += "-" + b.generateChars(4)
                }
                return l
            };
            return c
        });
        a.define("select2/data/select", ["./base", "../utils", "jquery"], function (l, b, c) {
            function k(h, g) {
                this.$element = h;
                this.options = g;
                k.__super__.constructor.call(this)
            }

            b.Extend(k, l);
            k.prototype.current = function (g) {
                var h = [];
                var n = this;
                this.$element.find(":selected").each(function () {
                    var m = c(this);
                    var p = n.item(m);
                    h.push(p)
                });
                g(h)
            };
            k.prototype.select = function (h) {
                var n = this;
                h.selected = true;
                if (c(h.element).is("option")) {
                    h.element.selected = true;
                    this.$element.trigger("change");
                    return
                }
                if (this.$element.prop("multiple")) {
                    this.current(function (u) {
                        var r = [];
                        h = [h];
                        h.push.apply(h, u);
                        for (var s = 0; s < h.length; s++) {
                            var m = h[s].id;
                            if (c.inArray(m, r) === -1) {
                                r.push(m)
                            }
                        }
                        n.$element.val(r);
                        n.$element.trigger("change")
                    })
                } else {
                    var g = h.id;
                    this.$element.val(g);
                    this.$element.trigger("change")
                }
            };
            k.prototype.unselect = function (g) {
                var h = this;
                if (!this.$element.prop("multiple")) {
                    return
                }
                g.selected = false;
                if (c(g.element).is("option")) {
                    g.element.selected = false;
                    this.$element.trigger("change");
                    return
                }
                this.current(function (q) {
                    var s = [];
                    for (var u = 0; u < q.length; u++) {
                        var r = q[u].id;
                        if (r !== g.id && c.inArray(r, s) === -1) {
                            s.push(r)
                        }
                    }
                    h.$element.val(s);
                    h.$element.trigger("change")
                })
            };
            k.prototype.bind = function (n, g) {
                var h = this;
                this.container = n;
                n.on("select", function (m) {
                    h.select(m.data)
                });
                n.on("unselect", function (m) {
                    h.unselect(m.data)
                })
            };
            k.prototype.destroy = function () {
                this.$element.find("*").each(function () {
                    c.removeData(this, "data")
                })
            };
            k.prototype.query = function (r, q) {
                var g = [];
                var h = this;
                var p = this.$element.children();
                p.each(function () {
                    var m = c(this);
                    if (!m.is("option") && !m.is("optgroup")) {
                        return
                    }
                    var o = h.item(m);
                    var n = h.matches(r, o);
                    if (n !== null) {
                        g.push(n)
                    }
                });
                q({results: g})
            };
            k.prototype.addOptions = function (g) {
                b.appendMany(this.$element, g)
            };
            k.prototype.option = function (h) {
                var o;
                if (h.children) {
                    o = document.createElement("optgroup");
                    o.label = h.text
                } else {
                    o = document.createElement("option");
                    if (o.textContent !== undefined) {
                        o.textContent = h.text
                    } else {
                        o.innerText = h.text
                    }
                }
                if (h.id) {
                    o.value = h.id
                }
                if (h.disabled) {
                    o.disabled = true
                }
                if (h.selected) {
                    o.selected = true
                }
                if (h.title) {
                    o.title = h.title
                }
                var g = c(o);
                var p = this._normalizeItem(h);
                p.element = o;
                c.data(o, "data", p);
                return g
            };
            k.prototype.item = function (v) {
                var w = {};
                w = c.data(v[0], "data");
                if (w != null) {
                    return w
                }
                if (v.is("option")) {
                    w = {
                        id: v.val(),
                        text: v.text(),
                        disabled: v.prop("disabled"),
                        selected: v.prop("selected"),
                        title: v.prop("title")
                    }
                } else {
                    if (v.is("optgroup")) {
                        w = {
                            text: v.prop("label"),
                            children: [],
                            title: v.prop("title")
                        };
                        var r = v.children("option");
                        var h = [];
                        for (var s = 0; s < r.length; s++) {
                            var g = c(r[s]);
                            var u = this.item(g);
                            h.push(u)
                        }
                        w.children = h
                    }
                }
                w = this._normalizeItem(w);
                w.element = v[0];
                c.data(v[0], "data", w);
                return w
            };
            k.prototype._normalizeItem = function (h) {
                if (!c.isPlainObject(h)) {
                    h = {id: h, text: h}
                }
                h = c.extend({}, {text: ""}, h);
                var g = {selected: false, disabled: false};
                if (h.id != null) {
                    h.id = h.id.toString()
                }
                if (h.text != null) {
                    h.text = h.text.toString()
                }
                if (h._resultId == null && h.id && this.container != null) {
                    h._resultId = this.generateResultId(this.container, h)
                }
                return c.extend({}, g, h)
            };
            k.prototype.matches = function (g, n) {
                var h = this.options.get("matcher");
                return h(g, n)
            };
            return k
        });
        a.define("select2/data/array", ["./select", "../utils", "jquery"], function (l, c, k) {
            function b(n, h) {
                var g = h.get("data") || [];
                b.__super__.constructor.call(this, n, h);
                this.addOptions(this.convertToOptions(g))
            }

            c.Extend(b, l);
            b.prototype.select = function (h) {
                var g = this.$element.find("option").filter(function (o, p) {
                    return p.value == h.id.toString()
                });
                if (g.length === 0) {
                    g = this.option(h);
                    this.addOptions(g)
                }
                b.__super__.select.call(this, h)
            };
            b.prototype.convertToOptions = function (D) {
                var H = this;
                var A = this.$element.find("option");
                var E = A.map(function () {
                    return H.item(k(this)).id
                }).get();
                var B = [];

                function K(m) {
                    return function () {
                        return k(this).val() == m.id
                    }
                }

                for (var L = 0; L < D.length; L++) {
                    var I = this._normalizeItem(D[L]);
                    if (k.inArray(I.id, E) >= 0) {
                        var h = A.filter(K(I));
                        var g = this.item(h);
                        var G = k.extend(true, {}, g, I);
                        var C = this.option(g);
                        h.replaceWith(C);
                        continue
                    }
                    var F = this.option(I);
                    if (I.children) {
                        var J = this.convertToOptions(I.children);
                        c.appendMany(F, J)
                    }
                    B.push(F)
                }
                return B
            };
            return b
        });
        a.define("select2/data/ajax", ["./array", "../utils", "jquery"], function (b, c, k) {
            function l(h, g) {
                this.ajaxOptions = this._applyDefaults(g.get("ajax"));
                if (this.ajaxOptions.processResults != null) {
                    this.processResults = this.ajaxOptions.processResults
                }
                b.__super__.constructor.call(this, h, g)
            }

            c.Extend(l, b);
            l.prototype._applyDefaults = function (h) {
                var g = {
                    data: function (n) {
                        return {q: n.term}
                    }, transport: function (r, s, u) {
                        var q = k.ajax(r);
                        q.then(s);
                        q.fail(u);
                        return q
                    }
                };
                return k.extend({}, g, h, true)
            };
            l.prototype.processResults = function (g) {
                return g
            };
            l.prototype.query = function (s, r) {
                var u = [];
                var q = this;
                if (this._request != null) {
                    if (k.isFunction(this._request.abort)) {
                        this._request.abort()
                    }
                    this._request = null
                }
                var h = k.extend({type: "GET"}, this.ajaxOptions);
                if (typeof h.url === "function") {
                    h.url = h.url(s)
                }
                if (typeof h.data === "function") {
                    h.data = h.data(s)
                }
                function g() {
                    var m = h.transport(h, function (n) {
                        var o = q.processResults(n, s);
                        if (q.options.get("debug") && window.console && console.error) {
                            if (!o || !o.results || !k.isArray(o.results)) {
                                console.error("Select2: The AJAX results did not return an array in the `results` key of the response.")
                            }
                        }
                        r(o)
                    }, function () {
                    });
                    q._request = m
                }

                if (this.ajaxOptions.delay && s.term !== "") {
                    if (this._queryTimeout) {
                        window.clearTimeout(this._queryTimeout)
                    }
                    this._queryTimeout = window.setTimeout(g, this.ajaxOptions.delay)
                } else {
                    g()
                }
            };
            return l
        });
        a.define("select2/data/tags", ["jquery"], function (b) {
            function c(A, x, u) {
                var s = u.get("tags");
                var y = u.get("createTag");
                if (y !== undefined) {
                    this.createTag = y
                }
                A.call(this, x, u);
                if (b.isArray(s)) {
                    for (var v = 0; v < s.length; v++) {
                        var r = s[v];
                        var w = this._normalizeItem(r);
                        var B = this.option(w);
                        this.$element.append(B)
                    }
                }
            }

            c.prototype.query = function (q, p, n) {
                var r = this;
                this._removeOldTags();
                if (p.term == null || p.page != null) {
                    q.call(this, p, n);
                    return
                }
                function o(g, l) {
                    var B = g.results;
                    for (var A = 0; A < B.length; A++) {
                        var y = B[A];
                        var k = (y.children != null && !o({results: y.children}, true));
                        var h = y.text === p.term;
                        if (h || k) {
                            if (l) {
                                return false
                            }
                            g.data = B;
                            n(g);
                            return
                        }
                    }
                    if (l) {
                        return true
                    }
                    var x = r.createTag(p);
                    if (x != null) {
                        var m = r.option(x);
                        m.attr("data-select2-tag", true);
                        r.addOptions([m]);
                        r.insertTag(B, x)
                    }
                    g.results = B;
                    n(g)
                }

                q.call(this, p, o)
            };
            c.prototype.createTag = function (m, l) {
                var n = b.trim(l.term);
                if (n === "") {
                    return null
                }
                return {id: n, text: n}
            };
            c.prototype.insertTag = function (m, l, n) {
                l.unshift(n)
            };
            c.prototype._removeOldTags = function (l) {
                var m = this._lastTag;
                var n = this.$element.find("option[data-select2-tag]");
                n.each(function () {
                    if (this.selected) {
                        return
                    }
                    b(this).remove()
                })
            };
            return c
        });
        a.define("select2/data/tokenizer", ["jquery"], function (b) {
            function c(m, p, o) {
                var n = o.get("tokenizer");
                if (n !== undefined) {
                    this.tokenizer = n
                }
                m.call(this, p, o)
            }

            c.prototype.bind = function (m, n, l) {
                m.call(this, n, l);
                this.$search = n.dropdown.$search || n.selection.$search || l.find(".select2-search__field")
            };
            c.prototype.query = function (q, p, u) {
                var r = this;

                function s(g) {
                    r.select(g)
                }

                p.term = p.term || "";
                var o = this.tokenizer(p, this.options, s);
                if (o.term !== p.term) {
                    if (this.$search.length) {
                        this.$search.val(o.term);
                        this.$search.focus()
                    }
                    p.term = o.term
                }
                q.call(this, p, u)
            };
            c.prototype.tokenizer = function (x, D, G, H) {
                var C = G.get("tokenSeparators") || [];
                var E = D.term;
                var A = 0;
                var y = this.createTag || function (g) {
                    return {id: g.term, text: g.term}
                };
                while (A < E.length) {
                    var v = E[A];
                    if (b.inArray(v, C) === -1) {
                        A++;
                        continue
                    }
                    var F = E.substr(0, A);
                    var w = b.extend({}, D, {term: F});
                    var B = y(w);
                    H(B);
                    E = E.substr(A + 1) || "";
                    A = 0
                }
                return {term: E}
            };
            return c
        });
        a.define("select2/data/minimumInputLength", [], function () {
            function b(c, k, l) {
                this.minimumInputLength = l.get("minimumInputLength");
                c.call(this, k, l)
            }

            b.prototype.query = function (l, k, c) {
                k.term = k.term || "";
                if (k.term.length < this.minimumInputLength) {
                    this.trigger("results:message", {
                        message: "inputTooShort",
                        args: {
                            minimum: this.minimumInputLength,
                            input: k.term,
                            params: k
                        }
                    });
                    return
                }
                l.call(this, k, c)
            };
            return b
        });
        a.define("select2/data/maximumInputLength", [], function () {
            function b(c, k, l) {
                this.maximumInputLength = l.get("maximumInputLength");
                c.call(this, k, l)
            }

            b.prototype.query = function (l, k, c) {
                k.term = k.term || "";
                if (this.maximumInputLength > 0 && k.term.length > this.maximumInputLength) {
                    this.trigger("results:message", {
                        message: "inputTooLong",
                        args: {
                            maximum: this.maximumInputLength,
                            input: k.term,
                            params: k
                        }
                    });
                    return
                }
                l.call(this, k, c)
            };
            return b
        });
        a.define("select2/data/maximumSelectionLength", [], function () {
            function b(c, k, l) {
                this.maximumSelectionLength = l.get("maximumSelectionLength");
                c.call(this, k, l)
            }

            b.prototype.query = function (m, l, c) {
                var n = this;
                this.current(function (h) {
                    var g = h != null ? h.length : 0;
                    if (n.maximumSelectionLength > 0 && g >= n.maximumSelectionLength) {
                        n.trigger("results:message", {
                            message: "maximumSelected",
                            args: {maximum: n.maximumSelectionLength}
                        });
                        return
                    }
                    m.call(n, l, c)
                })
            };
            return b
        });
        a.define("select2/dropdown", ["jquery", "./utils"], function (b, c) {
            function h(l, g) {
                this.$element = l;
                this.options = g;
                h.__super__.constructor.call(this)
            }

            c.Extend(h, c.Observable);
            h.prototype.render = function () {
                var g = b('<span class="select2-dropdown"><span class="select2-results"></span></span>');
                g.attr("dir", this.options.get("dir"));
                this.$dropdown = g;
                return g
            };
            h.prototype.position = function (l, g) {
            };
            h.prototype.destroy = function () {
                this.$dropdown.remove()
            };
            return h
        });
        a.define("select2/dropdown/search", ["jquery", "../utils"], function (b, c) {
            function h() {
            }

            h.prototype.render = function (m) {
                var g = m.call(this);
                var n = b('<span class="select2-search select2-search--dropdown"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" /></span>');
                this.$searchContainer = n;
                this.$search = n.find("input");
                g.prepend(n);
                return g
            };
            h.prototype.bind = function (n, p, g) {
                var o = this;
                n.call(this, p, g);
                this.$search.on("keydown", function (k) {
                    o.trigger("keypress", k);
                    o._keyUpPrevented = k.isDefaultPrevented()
                });
                this.$search.on("input", function (k) {
                    b(this).off("keyup")
                });
                this.$search.on("keyup input", function (k) {
                    o.handleSearch(k)
                });
                p.on("open", function () {
                    o.$search.attr("tabindex", 0);
                    o.$search.focus();
                    window.setTimeout(function () {
                        o.$search.focus()
                    }, 0)
                });
                p.on("close", function () {
                    o.$search.attr("tabindex", -1);
                    o.$search.val("")
                });
                p.on("results:all", function (k) {
                    if (k.query.term == null || k.query.term === "") {
                        var l = o.showSearch(k);
                        if (l) {
                            o.$searchContainer.removeClass("select2-search--hide")
                        } else {
                            o.$searchContainer.addClass("select2-search--hide")
                        }
                    }
                })
            };
            h.prototype.handleSearch = function (l) {
                if (!this._keyUpPrevented) {
                    var g = this.$search.val();
                    this.trigger("query", {term: g})
                }
                this._keyUpPrevented = false
            };
            h.prototype.showSearch = function (l, g) {
                return true
            };
            return h
        });
        a.define("select2/dropdown/hidePlaceholder", [], function () {
            function b(l, n, m, c) {
                this.placeholder = this.normalizePlaceholder(m.get("placeholder"));
                l.call(this, n, m, c)
            }

            b.prototype.append = function (c, h) {
                h.results = this.removePlaceholder(h.results);
                c.call(this, h)
            };
            b.prototype.normalizePlaceholder = function (h, c) {
                if (typeof c === "string") {
                    c = {id: "", text: c}
                }
                return c
            };
            b.prototype.removePlaceholder = function (p, m) {
                var n = m.slice(0);
                for (var c = m.length - 1; c >= 0; c--) {
                    var o = m[c];
                    if (this.placeholder.id === o.id) {
                        n.splice(c, 1)
                    }
                }
                return n
            };
            return b
        });
        a.define("select2/dropdown/infiniteScroll", ["jquery"], function (b) {
            function c(n, p, o, m) {
                this.lastParams = {};
                n.call(this, p, o, m);
                this.$loadingMore = this.createLoadingMore();
                this.loading = false
            }

            c.prototype.append = function (k, l) {
                this.$loadingMore.remove();
                this.loading = false;
                k.call(this, l);
                if (this.showLoadingMore(l)) {
                    this.$results.append(this.$loadingMore)
                }
            };
            c.prototype.bind = function (n, p, m) {
                var o = this;
                n.call(this, p, m);
                p.on("query", function (g) {
                    o.lastParams = g;
                    o.loading = true
                });
                p.on("query:append", function (g) {
                    o.lastParams = g;
                    o.loading = true
                });
                this.$results.on("scroll", function () {
                    var h = b.contains(document.documentElement, o.$loadingMore[0]);
                    if (o.loading || !h) {
                        return
                    }
                    var k = o.$results.offset().top + o.$results.outerHeight(false);
                    var g = o.$loadingMore.offset().top + o.$loadingMore.outerHeight(false);
                    if (k + 50 >= g) {
                        o.loadMore()
                    }
                })
            };
            c.prototype.loadMore = function () {
                this.loading = true;
                var h = b.extend({}, {page: 1}, this.lastParams);
                h.page++;
                this.trigger("query:append", h)
            };
            c.prototype.showLoadingMore = function (l, k) {
                return k.pagination && k.pagination.more
            };
            c.prototype.createLoadingMore = function () {
                var k = b('<li class="option load-more" role="treeitem"></li>');
                var l = this.options.get("translations").get("loadingMore");
                k.html(l(this.lastParams));
                return k
            };
            return c
        });
        a.define("select2/dropdown/attachBody", ["jquery", "../utils"], function (b, c) {
            function h(g, n, m) {
                this.$dropdownParent = m.get("dropdownParent") || document.body;
                g.call(this, n, m)
            }

            h.prototype.bind = function (g, q, r) {
                var o = this;
                var p = false;
                g.call(this, q, r);
                q.on("open", function () {
                    o._showDropdown();
                    o._attachPositioningHandler(q);
                    if (!p) {
                        p = true;
                        q.on("results:all", function () {
                            o._positionDropdown();
                            o._resizeDropdown()
                        });
                        q.on("results:append", function () {
                            o._positionDropdown();
                            o._resizeDropdown()
                        })
                    }
                });
                q.on("close", function () {
                    o._hideDropdown();
                    o._detachPositioningHandler(q)
                });
                this.$dropdownContainer.on("mousedown", function (k) {
                    k.stopPropagation()
                })
            };
            h.prototype.position = function (n, m, g) {
                m.attr("class", g.attr("class"));
                m.removeClass("select2");
                m.addClass("select2-container--open");
                m.css({position: "absolute", top: -999999});
                this.$container = g
            };
            h.prototype.render = function (n) {
                var g = b("<span></span>");
                var m = n.call(this);
                g.append(m);
                this.$dropdownContainer = g;
                return g
            };
            h.prototype._hideDropdown = function (g) {
                this.$dropdownContainer.detach()
            };
            h.prototype._attachPositioningHandler = function (r) {
                var q = this;
                var g = "scroll.select2." + r.id;
                var s = "resize.select2." + r.id;
                var u = "orientationchange.select2." + r.id;
                var p = this.$container.parents().filter(c.hasScroll);
                p.each(function () {
                    b(this).data("select2-scroll-position", {
                        x: b(this).scrollLeft(),
                        y: b(this).scrollTop()
                    })
                });
                p.on(g, function (k) {
                    var l = b(this).data("select2-scroll-position");
                    b(this).scrollTop(l.y)
                });
                b(window).on(g + " " + s + " " + u, function (k) {
                    q._positionDropdown();
                    q._resizeDropdown()
                })
            };
            h.prototype._detachPositioningHandler = function (q) {
                var o = "scroll.select2." + q.id;
                var r = "resize.select2." + q.id;
                var g = "orientationchange.select2." + q.id;
                var p = this.$container.parents().filter(c.hasScroll);
                p.off(o);
                b(window).off(o + " " + r + " " + g)
            };
            h.prototype._positionDropdown = function () {
                var C = b(window);
                var F = this.$dropdown.hasClass("select2-dropdown--above");
                var D = this.$dropdown.hasClass("select2-dropdown--below");
                var H = null;
                var A = this.$container.position();
                var B = this.$container.offset();
                B.bottom = B.top + this.$container.outerHeight(false);
                var E = {height: this.$container.outerHeight(false)};
                E.top = B.top;
                E.bottom = B.top + E.height;
                var G = {height: this.$dropdown.outerHeight(false)};
                var w = {
                    top: C.scrollTop(),
                    bottom: C.scrollTop() + C.height()
                };
                var x = w.top < (B.top - G.height);
                var g = w.bottom > (B.bottom + G.height);
                var y = {left: B.left, top: E.bottom};
                if (!F && !D) {
                    H = "below"
                }
                if (!g && x && !F) {
                    H = "above"
                } else {
                    if (!x && g && F) {
                        H = "below"
                    }
                }
                if (H == "above" || (F && H !== "below")) {
                    y.top = E.top - G.height
                }
                if (H != null) {
                    this.$dropdown.removeClass("select2-dropdown--below select2-dropdown--above").addClass("select2-dropdown--" + H);
                    this.$container.removeClass("select2-container--below select2-container--above").addClass("select2-container--" + H)
                }
                this.$dropdownContainer.css(y)
            };
            h.prototype._resizeDropdown = function () {
                this.$dropdownContainer.width();
                var g = {width: this.$container.outerWidth(false) + "px"};
                if (this.options.get("dropdownAutoWidth")) {
                    g.minWidth = g.width;
                    g.width = "auto"
                }
                this.$dropdown.css(g)
            };
            h.prototype._showDropdown = function (g) {
                this.$dropdownContainer.appendTo(this.$dropdownParent);
                this._positionDropdown();
                this._resizeDropdown()
            };
            return h
        });
        a.define("select2/dropdown/minimumResultsForSearch", [], function () {
            function b(n) {
                var o = 0;
                for (var m = 0; m < n.length; m++) {
                    var p = n[m];
                    if (p.children) {
                        o += b(p.children)
                    } else {
                        o++
                    }
                }
                return o
            }

            function c(n, p, o, m) {
                this.minimumResultsForSearch = o.get("minimumResultsForSearch");
                if (this.minimumResultsForSearch < 0) {
                    this.minimumResultsForSearch = Infinity
                }
                n.call(this, p, o, m)
            }

            c.prototype.showSearch = function (l, k) {
                if (b(k.data.results) < this.minimumResultsForSearch) {
                    return false
                }
                return l.call(this, k)
            };
            return c
        });
        a.define("select2/dropdown/selectOnClose", [], function () {
            function b() {
            }

            b.prototype.bind = function (l, n, c) {
                var m = this;
                l.call(this, n, c);
                n.on("close", function () {
                    m._handleSelectOnClose()
                })
            };
            b.prototype._handleSelectOnClose = function () {
                var c = this.getHighlightedResults();
                if (c.length < 1) {
                    return
                }
                this.trigger("select", {data: c.data("data")})
            };
            return b
        });
        a.define("select2/dropdown/closeOnSelect", [], function () {
            function b() {
            }

            b.prototype.bind = function (l, n, c) {
                var m = this;
                l.call(this, n, c);
                n.on("select", function (g) {
                    m._selectTriggered(g)
                });
                n.on("unselect", function (g) {
                    m._selectTriggered(g)
                })
            };
            b.prototype._selectTriggered = function (c, k) {
                var l = k.originalEvent;
                if (l && l.ctrlKey) {
                    return
                }
                this.trigger("close")
            };
            return b
        });
        a.define("select2/i18n/en", [], function () {
            return {
                errorLoading: function () {
                    return "The results could not be loaded."
                }, inputTooLong: function (h) {
                    var b = h.input.length - h.maximum;
                    var c = "Please delete " + b + " character";
                    if (b != 1) {
                        c += "s"
                    }
                    return c
                }, inputTooShort: function (h) {
                    var b = h.minimum - h.input.length;
                    var c = "Please enter " + b + " or more characters";
                    return c
                }, loadingMore: function () {
                    return "Loading more results…"
                }, maximumSelected: function (c) {
                    var b = "You can only select " + c.maximum + " item";
                    if (c.maximum != 1) {
                        b += "s"
                    }
                    return b
                }, noResults: function () {
                    return "No results found"
                }, searching: function () {
                    return "Searching…"
                }
            }
        });
        a.define("select2/defaults", ["jquery", "require", "./results", "./selection/single", "./selection/multiple", "./selection/placeholder", "./selection/allowClear", "./selection/search", "./selection/eventRelay", "./utils", "./translation", "./diacritics", "./data/select", "./data/array", "./data/ajax", "./data/tags", "./data/tokenizer", "./data/minimumInputLength", "./data/maximumInputLength", "./data/maximumSelectionLength", "./dropdown", "./dropdown/search", "./dropdown/hidePlaceholder", "./dropdown/infiniteScroll", "./dropdown/attachBody", "./dropdown/minimumResultsForSearch", "./dropdown/selectOnClose", "./dropdown/closeOnSelect", "./i18n/en"], function (an, aj, Q, ae, T, N, ak, U, P, ap, R, b, X, V, ao, ad, am, S, c, ag, Y, ah, aa, Z, W, O, ai, al, ac) {
            function ab() {
                this.reset()
            }

            ab.prototype.apply = function (x) {
                x = an.extend({}, this.defaults, x);
                if (x.dataAdapter == null) {
                    if (x.ajax != null) {
                        x.dataAdapter = ao
                    } else {
                        if (x.data != null) {
                            x.dataAdapter = V
                        } else {
                            x.dataAdapter = X
                        }
                    }
                    if (x.minimumInputLength > 0) {
                        x.dataAdapter = ap.Decorate(x.dataAdapter, S)
                    }
                    if (x.maximumInputLength > 0) {
                        x.dataAdapter = ap.Decorate(x.dataAdapter, c)
                    }
                    if (x.maximumSelectionLength > 0) {
                        x.dataAdapter = ap.Decorate(x.dataAdapter, ag)
                    }
                    if (x.tags) {
                        x.dataAdapter = ap.Decorate(x.dataAdapter, ad)
                    }
                    if (x.tokenSeparators != null || x.tokenizer != null) {
                        x.dataAdapter = ap.Decorate(x.dataAdapter, am)
                    }
                    if (x.query != null) {
                        var n = aj(x.amdBase + "compat/query");
                        x.dataAdapter = ap.Decorate(x.dataAdapter, n)
                    }
                    if (x.initSelection != null) {
                        var y = aj(x.amdBase + "compat/initSelection");
                        x.dataAdapter = ap.Decorate(x.dataAdapter, y)
                    }
                }
                if (x.resultsAdapter == null) {
                    x.resultsAdapter = Q;
                    if (x.ajax != null) {
                        x.resultsAdapter = ap.Decorate(x.resultsAdapter, Z)
                    }
                    if (x.placeholder != null) {
                        x.resultsAdapter = ap.Decorate(x.resultsAdapter, aa)
                    }
                    if (x.selectOnClose) {
                        x.resultsAdapter = ap.Decorate(x.resultsAdapter, ai)
                    }
                }
                if (x.dropdownAdapter == null) {
                    if (x.multiple) {
                        x.dropdownAdapter = Y
                    } else {
                        var u = ap.Decorate(Y, ah);
                        x.dropdownAdapter = u
                    }
                    if (x.minimumResultsForSearch !== 0) {
                        x.dropdownAdapter = ap.Decorate(x.dropdownAdapter, O)
                    }
                    if (x.closeOnSelect) {
                        x.dropdownAdapter = ap.Decorate(x.dropdownAdapter, al)
                    }
                    if (x.dropdownCssClass != null || x.dropdownCss != null || x.adaptDropdownCssClass != null) {
                        var v = aj(x.amdBase + "compat/dropdownCss");
                        x.dropdownAdapter = ap.Decorate(x.dropdownAdapter, v)
                    }
                    x.dropdownAdapter = ap.Decorate(x.dropdownAdapter, W)
                }
                if (x.selectionAdapter == null) {
                    if (x.multiple) {
                        x.selectionAdapter = T
                    } else {
                        x.selectionAdapter = ae
                    }
                    if (x.placeholder != null) {
                        x.selectionAdapter = ap.Decorate(x.selectionAdapter, N)
                    }
                    if (x.allowClear) {
                        x.selectionAdapter = ap.Decorate(x.selectionAdapter, ak)
                    }
                    if (x.multiple) {
                        x.selectionAdapter = ap.Decorate(x.selectionAdapter, U)
                    }
                    if (x.containerCssClass != null || x.containerCss != null || x.adaptContainerCssClass != null) {
                        var l = aj(x.amdBase + "compat/containerCss");
                        x.selectionAdapter = ap.Decorate(x.selectionAdapter, l)
                    }
                    x.selectionAdapter = ap.Decorate(x.selectionAdapter, P)
                }
                if (typeof x.language === "string") {
                    if (x.language.indexOf("-") > 0) {
                        var q = x.language.split("-");
                        var o = q[0];
                        x.language = [x.language, o]
                    } else {
                        x.language = [x.language]
                    }
                }
                if (an.isArray(x.language)) {
                    var r = new R();
                    x.language.push("en");
                    var g = x.language;
                    for (var s = 0; s < g.length; s++) {
                        var w = g[s];
                        var p = {};
                        try {
                            p = R.loadPath(w)
                        } catch (m) {
                            try {
                                w = this.defaults.amdLanguageBase + w;
                                p = R.loadPath(w)
                            } catch (h) {
                                if (x.debug && window.console && console.warn) {
                                    console.warn('Select2: The language file for "' + w + '" could not be automatically loaded. A fallback will be used instead.')
                                }
                                continue
                            }
                        }
                        r.extend(p)
                    }
                    x.translations = r
                } else {
                    var k = R.loadPath(this.defaults.amdLanguageBase + "en");
                    var A = new R(x.language);
                    A.extend(k);
                    x.translations = A
                }
                return x
            };
            ab.prototype.reset = function () {
                function g(k) {
                    function l(m) {
                        return b[m] || m
                    }

                    return k.replace(/[^\u0000-\u007E]/g, l)
                }

                function h(m, n) {
                    if (an.trim(m.term) === "") {
                        return n
                    }
                    if (n.children && n.children.length > 0) {
                        var r = an.extend(true, {}, n);
                        for (var k = n.children.length - 1; k >= 0; k--) {
                            var l = n.children[k];
                            var o = h(m, l);
                            if (o == null) {
                                r.children.splice(k, 1)
                            }
                        }
                        if (r.children.length > 0) {
                            return r
                        }
                        return h(m, r)
                    }
                    var p = g(n.text).toUpperCase();
                    var q = g(m.term).toUpperCase();
                    if (p.indexOf(q) > -1) {
                        return n
                    }
                    return null
                }

                this.defaults = {
                    amdBase: "./",
                    amdLanguageBase: "./i18n/",
                    closeOnSelect: true,
                    debug: false,
                    dropdownAutoWidth: false,
                    escapeMarkup: ap.escapeMarkup,
                    language: ac,
                    matcher: h,
                    minimumInputLength: 0,
                    maximumInputLength: 0,
                    maximumSelectionLength: 0,
                    minimumResultsForSearch: 0,
                    selectOnClose: false,
                    sorter: function (k) {
                        return k
                    },
                    templateResult: function (k) {
                        return k.text
                    },
                    templateSelection: function (k) {
                        return k.text
                    },
                    theme: "default",
                    width: "resolve"
                }
            };
            ab.prototype.set = function (l, h) {
                var m = an.camelCase(l);
                var k = {};
                k[m] = h;
                var g = ap._convertData(k);
                an.extend(this.defaults, g)
            };
            var af = new ab();
            return af
        });
        a.define("select2/options", ["require", "jquery", "./defaults", "./utils"], function (m, c, b, l) {
            function n(k, h) {
                this.options = k;
                if (h != null) {
                    this.fromElement(h)
                }
                this.options = b.apply(this.options);
                if (h && h.is("input")) {
                    var g = m(this.get("amdBase") + "compat/inputData");
                    this.options.dataAdapter = l.Decorate(this.options.dataAdapter, g)
                }
            }

            n.prototype.fromElement = function (h) {
                var r = ["select2"];
                if (this.options.multiple == null) {
                    this.options.multiple = h.prop("multiple")
                }
                if (this.options.disabled == null) {
                    this.options.disabled = h.prop("disabled")
                }
                if (this.options.language == null) {
                    if (h.prop("lang")) {
                        this.options.language = h.prop("lang").toLowerCase()
                    } else {
                        if (h.closest("[lang]").prop("lang")) {
                            this.options.language = h.closest("[lang]").prop("lang")
                        }
                    }
                }
                if (this.options.dir == null) {
                    if (h.prop("dir")) {
                        this.options.dir = h.prop("dir")
                    } else {
                        if (h.closest("[dir]").prop("dir")) {
                            this.options.dir = h.closest("[dir]").prop("dir")
                        } else {
                            this.options.dir = "ltr"
                        }
                    }
                }
                h.prop("disabled", this.options.disabled);
                h.prop("multiple", this.options.multiple);
                if (h.data("select2Tags")) {
                    if (this.options.debug && window.console && console.warn) {
                        console.warn('Select2: The `data-select2-tags` attribute has been changed to use the `data-data` and `data-tags="true"` attributes and will be removed in future versions of Select2.')
                    }
                    h.data("data", h.data("select2Tags"));
                    h.data("tags", true)
                }
                if (h.data("ajaxUrl")) {
                    if (this.options.debug && window.console && console.warn) {
                        console.warn("Select2: The `data-ajax-url` attribute has been changed to `data-ajax--url` and support for the old attribute will be removed in future versions of Select2.")
                    }
                    h.attr("ajax--url", h.data("ajaxUrl"));
                    h.data("ajax--url", h.data("ajaxUrl"))
                }
                var k = {};
                if (c.fn.jquery && c.fn.jquery.substr(0, 2) == "1." && h[0].dataset) {
                    k = c.extend(true, {}, h[0].dataset, h.data())
                } else {
                    k = h.data()
                }
                var q = c.extend(true, {}, k);
                q = l._convertData(q);
                for (var g in q) {
                    if (c.inArray(g, r) > -1) {
                        continue
                    }
                    if (c.isPlainObject(this.options[g])) {
                        c.extend(this.options[g], q[g])
                    } else {
                        this.options[g] = q[g]
                    }
                }
                return this
            };
            n.prototype.get = function (g) {
                return this.options[g]
            };
            n.prototype.set = function (h, g) {
                this.options[h] = g
            };
            return n
        });
        a.define("select2/core", ["jquery", "./options", "./utils", "./keys"], function (b, l, c, n) {
            var m = function (h, A) {
                if (h.data("select2") != null) {
                    h.data("select2").destroy()
                }
                this.$element = h;
                this.id = this._generateId(h);
                A = A || {};
                this.options = new l(A, h);
                m.__super__.constructor.call(this);
                var v = h.attr("tabindex") || 0;
                h.data("old-tabindex", v);
                h.attr("tabindex", "-1");
                var w = this.options.get("dataAdapter");
                this.dataAdapter = new w(h, this.options);
                var B = this.render();
                this._placeContainer(B);
                var k = this.options.get("selectionAdapter");
                this.selection = new k(h, this.options);
                this.$selection = this.selection.render();
                this.selection.position(this.$selection, B);
                var y = this.options.get("dropdownAdapter");
                this.dropdown = new y(h, this.options);
                this.$dropdown = this.dropdown.render();
                this.dropdown.position(this.$dropdown, B);
                var x = this.options.get("resultsAdapter");
                this.results = new x(h, this.options, this.dataAdapter);
                this.$results = this.results.render();
                this.results.position(this.$results, this.$dropdown);
                var g = this;
                this._bindAdapters();
                this._registerDomEvents();
                this._registerDataEvents();
                this._registerSelectionEvents();
                this._registerDropdownEvents();
                this._registerResultsEvents();
                this._registerEvents();
                this.dataAdapter.current(function (o) {
                    g.trigger("selection:update", {data: o})
                });
                h.addClass("select2-hidden-accessible");
                h.attr("aria-hidden", "true");
                this._syncAttributes();
                h.data("select2", this)
            };
            c.Extend(m, c.Observable);
            m.prototype._generateId = function (h) {
                var g = "";
                if (h.attr("id") != null) {
                    g = h.attr("id")
                } else {
                    if (h.attr("name") != null) {
                        g = h.attr("name") + "-" + c.generateChars(2)
                    } else {
                        g = c.generateChars(4)
                    }
                }
                g = "select2-" + g;
                return g
            };
            m.prototype._placeContainer = function (g) {
                g.insertAfter(this.$element);
                var h = this._resolveWidth(this.$element, this.options.get("width"));
                if (h != null) {
                    g.css("width", h)
                }
            };
            m.prototype._resolveWidth = function (C, B) {
                var E = /^width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i;
                if (B == "resolve") {
                    var k = this._resolveWidth(C, "style");
                    if (k != null) {
                        return k
                    }
                    return this._resolveWidth(C, "element")
                }
                if (B == "element") {
                    var F = C.outerWidth(false);
                    if (F <= 0) {
                        return "auto"
                    }
                    return F + "px"
                }
                if (B == "style") {
                    var A = C.attr("style");
                    if (typeof(A) !== "string") {
                        return null
                    }
                    var D = A.split(";");
                    for (var h = 0, y = D.length; h < y; h = h + 1) {
                        var G = D[h].replace(/\s/g, "");
                        var g = G.match(E);
                        if (g !== null && g.length >= 1) {
                            return g[1]
                        }
                    }
                    return null
                }
                return B
            };
            m.prototype._bindAdapters = function () {
                this.dataAdapter.bind(this, this.$container);
                this.selection.bind(this, this.$container);
                this.dropdown.bind(this, this.$container);
                this.results.bind(this, this.$container)
            };
            m.prototype._registerDomEvents = function () {
                var g = this;
                this.$element.on("change.select2", function () {
                    g.dataAdapter.current(function (k) {
                        g.trigger("selection:update", {data: k})
                    })
                });
                this._sync = c.bind(this._syncAttributes, this);
                if (this.$element[0].attachEvent) {
                    this.$element[0].attachEvent("onpropertychange", this._sync)
                }
                var h = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
                if (h != null) {
                    this._observer = new h(function (k) {
                        b.each(k, g._sync)
                    });
                    this._observer.observe(this.$element[0], {
                        attributes: true,
                        subtree: false
                    })
                } else {
                    if (this.$element[0].addEventListener) {
                        this.$element[0].addEventListener("DOMAttrModified", g._sync, false)
                    }
                }
            };
            m.prototype._registerDataEvents = function () {
                var g = this;
                this.dataAdapter.on("*", function (h, k) {
                    g.trigger(h, k)
                })
            };
            m.prototype._registerSelectionEvents = function () {
                var h = this;
                var g = ["toggle"];
                this.selection.on("toggle", function () {
                    h.toggleDropdown()
                });
                this.selection.on("*", function (p, k) {
                    if (b.inArray(p, g) !== -1) {
                        return
                    }
                    h.trigger(p, k)
                })
            };
            m.prototype._registerDropdownEvents = function () {
                var g = this;
                this.dropdown.on("*", function (h, k) {
                    g.trigger(h, k)
                })
            };
            m.prototype._registerResultsEvents = function () {
                var g = this;
                this.results.on("*", function (h, k) {
                    g.trigger(h, k)
                })
            };
            m.prototype._registerEvents = function () {
                var g = this;
                this.on("open", function () {
                    g.$container.addClass("select2-container--open")
                });
                this.on("close", function () {
                    g.$container.removeClass("select2-container--open")
                });
                this.on("enable", function () {
                    g.$container.removeClass("select2-container--disabled")
                });
                this.on("disable", function () {
                    g.$container.addClass("select2-container--disabled")
                });
                this.on("focus", function () {
                    g.$container.addClass("select2-container--focus")
                });
                this.on("blur", function () {
                    g.$container.removeClass("select2-container--focus")
                });
                this.on("query", function (h) {
                    if (!g.isOpen()) {
                        g.trigger("open")
                    }
                    this.dataAdapter.query(h, function (k) {
                        g.trigger("results:all", {data: k, query: h})
                    })
                });
                this.on("query:append", function (h) {
                    this.dataAdapter.query(h, function (k) {
                        g.trigger("results:append", {data: k, query: h})
                    })
                });
                this.on("keypress", function (h) {
                    var k = h.which;
                    if (g.isOpen()) {
                        if (k === n.ENTER) {
                            g.trigger("results:select");
                            h.preventDefault()
                        } else {
                            if ((k === n.SPACE && h.ctrlKey)) {
                                g.trigger("results:toggle");
                                h.preventDefault()
                            } else {
                                if (k === n.UP) {
                                    g.trigger("results:previous");
                                    h.preventDefault()
                                } else {
                                    if (k === n.DOWN) {
                                        g.trigger("results:next");
                                        h.preventDefault()
                                    } else {
                                        if (k === n.ESC || k === n.TAB) {
                                            g.close();
                                            h.preventDefault()
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if (k === n.ENTER || k === n.SPACE || ((k === n.DOWN || k === n.UP) && h.altKey)) {
                            g.open();
                            h.preventDefault()
                        }
                    }
                })
            };
            m.prototype._syncAttributes = function () {
                this.options.set("disabled", this.$element.prop("disabled"));
                if (this.options.get("disabled")) {
                    if (this.isOpen()) {
                        this.close()
                    }
                    this.trigger("disable")
                } else {
                    this.trigger("enable")
                }
            };
            m.prototype.trigger = function (s, u) {
                var r = m.__super__.trigger;
                var k = {
                    open: "opening",
                    close: "closing",
                    select: "selecting",
                    unselect: "unselecting"
                };
                if (s in k) {
                    var g = k[s];
                    var h = {prevented: false, name: s, args: u};
                    r.call(this, g, h);
                    if (h.prevented) {
                        u.prevented = true;
                        return
                    }
                }
                r.call(this, s, u)
            };
            m.prototype.toggleDropdown = function () {
                if (this.options.get("disabled")) {
                    return
                }
                if (this.isOpen()) {
                    this.close()
                } else {
                    this.open()
                }
            };
            m.prototype.open = function () {
                if (this.isOpen()) {
                    return
                }
                this.trigger("query", {});
                this.trigger("open")
            };
            m.prototype.close = function () {
                if (!this.isOpen()) {
                    return
                }
                this.trigger("close")
            };
            m.prototype.isOpen = function () {
                return this.$container.hasClass("select2-container--open")
            };
            m.prototype.enable = function (h) {
                if (this.options.get("debug") && window.console && console.warn) {
                    console.warn('Select2: The `select2("enable")` method has been deprecated and will be removed in later Select2 versions. Use $element.prop("disabled") instead.')
                }
                if (h == null || h.length === 0) {
                    h = [true]
                }
                var g = !h[0];
                this.$element.prop("disabled", g)
            };
            m.prototype.data = function () {
                if (this.options.get("debug") && arguments.length > 0 && window.console && console.warn) {
                    console.warn('Select2: Data can no longer be set using `select2("data")`. You should consider setting the value instead using `$element.val()`.')
                }
                var g = [];
                this.dataAdapter.current(function (h) {
                    g = h
                });
                return g
            };
            m.prototype.val = function (g) {
                if (this.options.get("debug") && window.console && console.warn) {
                    console.warn('Select2: The `select2("val")` method has been deprecated and will be removed in later Select2 versions. Use $element.val() instead.')
                }
                if (g == null || g.length === 0) {
                    return this.$element.val()
                }
                var h = g[0];
                if (b.isArray(h)) {
                    h = b.map(h, function (k) {
                        return k.toString()
                    })
                }
                this.$element.val(h).trigger("change")
            };
            m.prototype.destroy = function () {
                this.$container.remove();
                if (this.$element[0].detachEvent) {
                    this.$element[0].detachEvent("onpropertychange", this._sync)
                }
                if (this._observer != null) {
                    this._observer.disconnect();
                    this._observer = null
                } else {
                    if (this.$element[0].removeEventListener) {
                        this.$element[0].removeEventListener("DOMAttrModified", this._sync, false)
                    }
                }
                this._sync = null;
                this.$element.off(".select2");
                this.$element.attr("tabindex", this.$element.data("old-tabindex"));
                this.$element.removeClass("select2-hidden-accessible");
                this.$element.attr("aria-hidden", "false");
                this.$element.removeData("select2");
                this.dataAdapter.destroy();
                this.selection.destroy();
                this.dropdown.destroy();
                this.results.destroy();
                this.dataAdapter = null;
                this.selection = null;
                this.dropdown = null;
                this.results = null
            };
            m.prototype.render = function () {
                var g = b('<span class="select2 select2-container"><span class="selection"></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>');
                g.attr("dir", this.options.get("dir"));
                this.$container = g;
                this.$container.addClass("select2-container--" + this.options.get("theme"));
                g.data("element", this.$element);
                return g
            };
            return m
        });
        a.define("jquery.select2", ["jquery", "require", "./select2/core", "./select2/defaults"], function (c, l, n, b) {
            l("jquery.mousewheel");
            if (c.fn.select2 == null) {
                var m = ["open", "close", "destroy"];
                c.fn.select2 = function (k) {
                    k = k || {};
                    if (typeof k === "object") {
                        this.each(function () {
                            var o = c.extend({}, k, true);
                            var r = new n(c(this), o)
                        });
                        return this
                    } else {
                        if (typeof k === "string") {
                            var h = this.data("select2");
                            if (h == null && window.console && console.error) {
                                console.error("The select2('" + k + "') method was called on an element that is not using Select2.")
                            }
                            var p = Array.prototype.slice.call(arguments, 1);
                            var g = h[k](p);
                            if (c.inArray(k, m) > -1) {
                                return this
                            }
                            return g
                        } else {
                            throw new Error("Invalid arguments for Select2: " + k)
                        }
                    }
                }
            }
            if (c.fn.select2.defaults == null) {
                c.fn.select2.defaults = b
            }
            return n
        });
        a.define("jquery.mousewheel", ["jquery"], function (b) {
            return b
        });
        return {define: a.define, require: a.require}
    }());
    var e = d.require("jquery.select2");
    f.fn.select2.amd = d;
    return e
}));