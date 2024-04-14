var TimerHandler = function (d, c) {
    if (typeof(d) == "undefined") {
        d = 1000
    }
    this._interval = d;
    this._callbacks = new Array();
    this._intervalId = null;
    this._lastId = 0;
    this.pageReloadAlreadyTriggered = false;
    this.checkEventsAlreadyQueued = false;
    if (c != false) {
        this.startTimer()
    }
};
TimerHandler.prototype.appendCallback = function (c) {
    var d = this._lastId++;
    this._callbacks[d] = c;
    return d
};
TimerHandler.prototype.removeCallback = function (b) {
    this._callbacks[b] = null
};
TimerHandler.prototype.startTimer = function () {
    var b = this;
    this._intervalId = window.setInterval(function () {
        b._timer()
    }, this._interval)
};
TimerHandler.prototype.stopTimer = function () {
    window.clearInterval(this._intervalId)
};
TimerHandler.prototype._timer = function () {
    for (var b in this._callbacks) {
        if (this._callbacks[b] != null) {
            this._callbacks[b]()
        }
    }
};