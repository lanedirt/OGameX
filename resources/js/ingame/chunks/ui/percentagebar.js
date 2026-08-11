function PercentageBar(elem) {
  this.elem = $(elem);
  this.elem.data("percentageBarInstance", this);
  this.elem.disableSelection();
  this.value = parseFloat(this.elem.attr("value") || 10 * 2);
  this.minValue = parseInt(this.elem.attr("minValue") || 1 / 2);
  this.steps = parseInt(this.elem.attr("steps") || 10 * 2);
  this.stepSize = parseInt(this.elem.attr("stepSize") || 10 * 2);
  this.useHalfStep = this.elem.attr("useHalfStep") === "true" || false;
  this.barFactor = 1;
  this.valueOnEnter = null;
  this.isDragging = false;
  this.init();
  this.updateBar();
}

PercentageBar.prototype.init = function () {
  if (this.useHalfStep) {
    this.barFactor = 2;
  }

  this.initBar();
  this.initSteps();

  if (document.createTouch === undefined) {
    this.elem.on("mouseup", this.onMouseUp.bind(this));
    this.elem.on("mousemove", this.onMouseMove.bind(this));
    this.elem.on("mouseenter", this.onMouseEnter.bind(this));
    this.elem.on("mouseleave", this.onMouseLeave.bind(this));
  } else {
    this.elem.on("touchstart", this.onTouchStart.bind(this));
    this.elem.on("touchmove", this.onTouchMove.bind(this));
    this.elem.on("touchend", this.onTouchEnd.bind(this));
  }
};

PercentageBar.prototype.initSteps = function () {
  let html = '<div class="steps">';

  for (let i = 0; i < this.steps; ++i) {
    let label = (i + 1) * this.stepSize;
    html +=
      '<div class="step' +
      (this.stepSize === 10 ? " step2" : "") +
      '"  style="cursor: pointer" onclick="" data-step="' +
      (i + 1) / this.barFactor +
      '">' +
      label +
      "</div>";
  }

  this.elem.append(html);
};

PercentageBar.prototype.initBar = function () {
  let html = '<div class="bar"></bar>';
  this.elem.append(html);
};

PercentageBar.prototype.updateDrag = function (e) {
  let xMin = this.elem.offset().left;
  let xMax = xMin + this.elem.outerWidth();
  let xCurrent = e.pageX;
  let value = this.calcValue(xCurrent, xMin, xMax);
  this.setValue(value);
};

PercentageBar.prototype.onMouseUp = function (e) {
  if (this.valueOnEnter) {
    this.valueOnEnter = this.getValue();
    this.setValue(this.valueOnEnter);
  }

  this.updateDrag(e);
};

PercentageBar.prototype.onMouseMove = function (e) {
  this.updateDrag(e);
};

PercentageBar.prototype.onMouseEnter = function (e) {
  this.valueOnEnter = this.getValue();
};

PercentageBar.prototype.onMouseLeave = function (e) {
  if (this.valueOnEnter > 0) {
    this.setValue(this.valueOnEnter);
  }
};
/**
 * TOUCH EVENTS
 */

PercentageBar.prototype.onTouchStart = function (e) {
  this.startDrag();
};

PercentageBar.prototype.onTouchMove = function (e) {};

PercentageBar.prototype.onTouchEnd = function (e) {
  if (this.valueOnEnter) {
    this.valueOnEnter = this.getValue();
    this.setValue(this.valueOnEnter);
  }

  this.updateDragTouch(e);
  this.stopDrag();
};

PercentageBar.prototype.startDrag = function () {
  this.isDragging = true;
  this.valueOnEnter = this.getValue();
};

PercentageBar.prototype.stopDrag = function () {
  this.isDragging = false;
};

PercentageBar.prototype.updateDragTouch = function (e) {
  if (this.isDragging === true) {
    let xMin = this.elem.offset().left;
    let xMax = xMin + this.elem.outerWidth();
    let xCurrent = e.originalEvent.pageX;

    if (xCurrent === 0) {
      xCurrent = e.originalEvent.changedTouches[0].pageX;
    }

    let value = this.calcValue(xCurrent, xMin, xMax);
    this.setValue(value);
  }
};

PercentageBar.prototype.calcValue = function (xCurrent, xMin, xMax) {
  let x = clampInt(xCurrent, xMin, xMax);
  let percent = (x - xMin) / (xMax - xMin);
  let valueMin = Math.floor(percent * this.steps);
  let valueMax = Math.ceil(percent * this.steps);
  let value = Math.round((valueMax + valueMin) / 2) / this.barFactor;
  return value;
};

PercentageBar.prototype.setValue = function (valueNew) {
  let valueOld = this.value;
  this.value = clampFloat(valueNew, this.minValue, this.steps);

  if (valueOld !== valueNew) {
    this.elem.trigger(
      $.Event("change", {
        value: this.value,
      }),
    );
  }

  this.updateBar();
};

PercentageBar.prototype.getValue = function () {
  return this.value;
};

PercentageBar.prototype.updateBar = function () {
  let xMin = this.elem.offset().left;
  let xMax = xMin + this.elem.outerWidth();
  let width = parseInt((this.value / this.steps) * (xMax - xMin) * this.barFactor);
  this.elem.find(".bar").width(width);
  this.elem.find(".step").toggleClass("selected", false);
  this.elem.find('.step[data-step="' + this.value + '"]').toggleClass("selected", true);
};
