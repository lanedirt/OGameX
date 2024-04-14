/*
 * Spinners 3.0.0
 * (c) 2010-2012 Nick Stakenburg - http://www.nickstakenburg.com
 *
 * Spinners is freely distributable under the terms of an MIT-style license.
 *
 * GitHub: http://github.com/staaky/spinners
 */
var Spinners = {version: "3.0.0"};
(function (b) {
    function c(k) {
        return k * Math.PI / 180
    }

    function d(k) {
        this.element = k
    }

    function e(k, l) {
        k && (this.element = k, a.remove(k), a.removeDetached(), this._position = 0, this._state = "stopped", this.setOptions(b.extend({
            color: "#000",
            dashes: 12,
            radius: 5,
            height: 5,
            width: 1.8,
            opacity: 1,
            padding: 3,
            rotation: 700
        }, l || {})), this.drawPosition(0), a.add(this))
    }

    var f = {
        scroll: function (l, m) {
            if (!m) {
                return l
            }
            var k = l.slice(0, m);
            return l.slice(m, l.length).concat(k)
        }, isElement: function (k) {
            return k && 1 == k.nodeType
        }, element: {
            isAttached: function () {
                return function (k) {
                    for (; k && k.parentNode;) {
                        k = k.parentNode
                    }
                    return !!k && !!k.body
                }
            }()
        }
    }, g = {
        drawRoundedRectangle: function (l, n) {
            var o = b.extend({
                top: 0,
                left: 0,
                width: 0,
                height: 0,
                radius: 0
            }, n || {}), p = o.left, q = o.top, k = o.width, m = o.height, o = o.radius;
            l.beginPath(), l.moveTo(p + o, q), l.arc(p + k - o, q + o, o, c(-90), c(0), !1), l.arc(p + k - o, q + m - o, o, c(0), c(90), !1), l.arc(p + o, q + m - o, o, c(90), c(180), !1), l.arc(p + o, q + o, o, c(-180), c(-90), !1), l.closePath(), l.fill()
        }
    }, h = function () {
        function l(n) {
            var o = [];
            0 == n.indexOf("#") && (n = n.substring(1)), n = n.toLowerCase();
            if ("" != n.replace(m, "")) {
                return null
            }
            3 == n.length ? (o[0] = n.charAt(0) + n.charAt(0), o[1] = n.charAt(1) + n.charAt(1), o[2] = n.charAt(2) + n.charAt(2)) : (o[0] = n.substring(0, 2), o[1] = n.substring(2, 4), o[2] = n.substring(4));
            for (n = 0; n < o.length; n++) {
                o[n] = parseInt(o[n], 16)
            }
            return o.red = o[0], o.green = o[1], o.blue = o[2], o
        }

        var m = RegExp("[0123456789abcdef]", "g"), k = function () {
            function n(p, q, o) {
                return p = p.toString(o || 10), Array(q - p.length).join("0") + p
            }

            return function (o, p, q) {
                return "#" + n(o, 2, 16) + n(p, 2, 16) + n(q, 2, 16)
            }
        }();
        return {
            hex2rgb: l, hex2fill: function (n, p) {
                "undefined" == typeof p && (p = 1);
                var o = p, q = l(n);
                return q[3] = o, q.opacity = o, "rgba(" + q.join() + ")"
            }, rgb2hex: k
        }
    }();
    b.extend(Spinners, {
        enabled: !1, support: {
            canvas: function () {
                var k = b("<canvas>")[0];
                return !!k.getContext && !!k.getContext("2d")
            }()
        }, init: function () {
            if (this.support.canvas || window.G_vmlCanvasManager && window.attachEvent && -1 === navigator.userAgent.indexOf("Opera")) {
                window.G_vmlCanvasManager && window.G_vmlCanvasManager.init_(document), this.enabled = !0
            }
        }, create: function (k, l) {
            return d.create(k, l), this.get(k)
        }, get: function (k) {
            return new d(k)
        }, play: function (k) {
            return this.get(k).play(), this
        }, pause: function (k) {
            return this.get(k).pause(), this
        }, toggle: function (k) {
            return this.get(k).toggle(), this
        }, stop: function (k) {
            return this.get(k).stop(), this
        }, remove: function (k) {
            return this.get(k).remove(), this
        }, removeDetached: function () {
            return a.removeDetached(), this
        }, center: function (k) {
            return this.get(k).center(), this
        }, setOptions: function (k, l) {
            return this.get(k).setOptions(l), this
        }, getDimensions: function (k) {
            return k = 2 * a.get(k)[0].getLayout().workspace.radius, {
                width: k,
                height: k
            }
        }
    });
    var a = {
        spinners: [], get: function (k) {
            if (k) {
                var l = [];
                return b.each(this.spinners, function (n, m) {
                    m && (f.isElement(k) ? m.element == k : b(m.element).is(k)) && l.push(m)
                }), l
            }
        }, add: function (k) {
            this.spinners.push(k)
        }, remove: function (k) {
            b(b.map(this.spinners, function (l) {
                if (f.isElement(k) ? l.element == k : b(l.element).is(k)) {
                    return l.element
                }
            })).each(b.proxy(function (l, m) {
                this.removeByElement(m)
            }, this))
        }, removeByElement: function (k) {
            var l = this.get(k)[0];
            l && (l.remove(), this.spinners = b.grep(this.spinners, function (m) {
                return m.element != k
            }))
        }, removeDetached: function () {
            b.each(this.spinners, b.proxy(function (k, l) {
                l && l.element && !f.element.isAttached(l.element) && this.remove(l.element)
            }, this))
        }
    };
    b.extend(d, {
        create: function (k, l) {
            if (k) {
                var m = l || {}, n = [];
                return f.isElement(k) ? n.push(new e(k, m)) : b(k).each(function (o, p) {
                    n.push(new e(p, m))
                }), n
            }
        }
    }), b.extend(d.prototype, {
        items: function () {
            return a.get(this.element)
        }, play: function () {
            return b.each(this.items(), function (k, l) {
                l.play()
            }), this
        }, stop: function () {
            return b.each(this.items(), function (k, l) {
                l.stop()
            }), this
        }, pause: function () {
            return b.each(this.items(), function (k, l) {
                l.pause()
            }), this
        }, toggle: function () {
            return b.each(this.items(), function (k, l) {
                l.toggle()
            }), this
        }, center: function () {
            return b.each(this.items(), function (k, l) {
                l.center()
            }), this
        }, setOptions: function (k) {
            return b.each(this.items(), function (l, m) {
                m.setOptions(k)
            }), this
        }, remove: function () {
            return a.remove(this.element), this
        }
    }), b.extend(e.prototype, {
        setOptions: function (k) {
            this.options = b.extend({}, this.options, k || {}), this.options.radii && (k = this.options.radii, this.options.radius = Math.min(k[0], k[1]), this.options.height = Math.max(k[0], k[1]) - this.options.radius), this.options.dashWidth && (this.options.width = this.options.dashWidth), this.options.speed && (this.options.duration = 1000 * this.options.speed);
            var k = this._state, l = this._position;
            this._layout = null, this.build(), l && l >= this.options.dashes - 1 && (this._position = this.options.dashes - 1);
            switch (k) {
                case"playing":
                    this.play();
                    break;
                case"paused":
                case"stopped":
                    this.drawPosition(this._position)
            }
            this._centered && this.center()
        }, remove: function () {
            this.canvas && (this.pause(), b(this.canvas).remove(), this.ctx = this.canvas = null)
        }, build: function () {
            this.remove();
            var k = this.getLayout().workspace.radius;
            return b(document.body).append(this.canvas = b("<canvas>").attr({
                width: 2 * k,
                height: 2 * k
            }).css({zoom: 1})), window.G_vmlCanvasManager && G_vmlCanvasManager.initElement(this.canvas[0]), this.ctx = this.canvas[0].getContext("2d"), this.ctx.globalAlpha = this.options.opacity, b(this.element).append(this.canvas), this.ctx.translate(k, k), this
        }, drawPosition: function (k) {
            var l = this.getLayout().workspace, k = f.scroll(l.opacities, -1 * k), m = l.radius, l = this.options.dashes, n = c(360 / l);
            this.ctx.clearRect(-1 * m, -1 * m, 2 * m, 2 * m);
            for (m = 0; m < l; m++) {
                this.drawDash(k[m], this.options.color), this.ctx.rotate(n)
            }
        }, drawDash: function (n, o) {
            this.ctx.fillStyle = h.hex2fill(o, n);
            var k = this.getLayout(), m = k.workspace.radius, l = k.dash.position, k = k.dash.dimensions;
            g.drawRoundedRectangle(this.ctx, {
                top: l.top - m,
                left: l.left - m,
                width: k.width,
                height: k.height,
                radius: Math.min(k.height, k.width) / 2
            })
        }, _nextPosition: function () {
            var k = this.options.rotation / this.options.dashes;
            this.nextPosition(), this._playTimer = window.setTimeout(b.proxy(this._nextPosition, this), k)
        }, nextPosition: function () {
            this._position == this.options.dashes - 1 && (this._position = -1), this._position++, this.drawPosition(this._position)
        }, play: function () {
            if ("playing" != this._state) {
                this._state = "playing";
                var k = this.options.rotation / this.options.dashes;
                return this._playTimer = window.setTimeout(b.proxy(this._nextPosition, this), k), this
            }
        }, pause: function () {
            if ("paused" != this._state) {
                return this._pause(), this._state = "paused", this
            }
        }, _pause: function () {
            this._playTimer && (window.clearTimeout(this._playTimer), this._playTimer = null)
        }, stop: function () {
            if ("stopped" != this._state) {
                return this._pause(), this._position = 0, this.drawPosition(0), this._state = "stopped", this
            }
        }, toggle: function () {
            return this["playing" == this._state ? "pause" : "play"](), this
        }, getLayout: function () {
            if (this._layout) {
                return this._layout
            }
            for (var k = this.options, l = k.dashes, m = k.width, n = k.radius, o = k.radius + k.height, p = Math.max(m, o), p = Math.ceil(Math.max(p, Math.sqrt(o * o + m / 2 * (m / 2)))), k = p += k.padding, q = 1 / l, r = [], D = 0; D < l; D++) {
                r.push((D + 1) * q)
            }
            return this._layout = l = {
                workspace: {radius: k, opacities: r},
                dash: {
                    position: {top: p - o, left: p - m / 2},
                    dimensions: {width: m, height: o - n}
                }
            }
        }, center: function () {
            var k = 2 * this.getLayout().workspace.radius;
            b(this.element.parentNode).css({position: "relative"}), b(this.element).css({
                position: "absolute",
                height: k + "px",
                width: k + "px",
                top: "50%",
                left: "50%",
                marginLeft: -0.5 * k + "px",
                marginTop: -0.5 * k + "px"
            }), this._centered = !0
        }
    }), Spinners.init(), Spinners.enabled || (d.create = function () {
        return []
    })
})(jQuery);

