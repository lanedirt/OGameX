function OGameLineChart(container, data) {
  this.container = container;
  this.dataPoints = data.dataPoints || [];
  this.deltaY = data.deltaY || 1;
  this.digitsY = data.digitsY || 0;
  this.title = data.title || "";
  this.titleFont = data.titleFont || "Bold 13px Verdana, Arial, SunSans-Regular, Sans-Serif";
  this.titleColor = data.titleColor || "#6f9fc8";
  this.titleBaseline = data.titleBaseline || "middle";
  this.labelFontY = data.labelFont || "11px Verdana, Arial, SunSans-Regular, Sans-Serif";
  this.labelColorY = data.labelColor || "#6f9fc8";
  this.labelBaselineY = data.labelBaseline || "middle";
  this.labelLineHeightY = data.labelLineHeightY || 11;
  this.labelSpacingY = data.labelSpacingY || 20;
  this.labelHeightY = data.labelHeightY || 20;
  this.labelFontX = data.labelFont || "11px Verdana, Arial, SunSans-Regular, Sans-Serif";
  this.labelColorX = data.labelColor || "#6f9fc8";
  this.labelBaselineX = data.labelBaseline || "middle";
  this.labelLineHeightX = data.labelLineHeightX || 11;
  this.guidesStyle = data.guidesStyle || "#6f9fc8";
  this.lineStyles = data.lineStyles || {};
  this.lineWidths = data.lineWidths || {};
  this.lineStyleHighlight = data.lineStyleHighlight || "#aaffaa";
  this.lineWidthHighlight = data.lineWidthHighlight || 5;
  this.lineThresholdHighlight = data.lineThresholdHighlight || 10;
  this.tooltips = data.tooltips || {};
  this.dataKeyHighlight = null;
  this.marginLeft = data.marginLeft || 60;
  this.marginRight = data.marginRight || 30;
  this.marginTop = data.marginTop || 20;
  this.marginBottom = data.marginBottom || 110;
  this.visibility = data.visibility || {};
}

OGameLineChart.prototype.init = function () {
  let html = '<div class="og-linechart"><canvas></canvas><div class="tooltip"></div></div>';
  this.container.html(html);
  this.canvas = this.container.find("canvas")[0];
  this.tooltip = this.container.find(".tooltip");
  this.fixDPI();
  this.context = this.canvas.getContext("2d");
  this.context.imageSmoothingEnabled = true;
  this.container.on("mousemove", this.handleMouseMove.bind(this));
  this.container.on("mouseleave", this.handleMouseLeave.bind(this));
};

OGameLineChart.prototype.handleMouseMove = function (e) {
  let mousePosition = this.transformEventToCanvas(e);
  this.updateHighlight(mousePosition);
  this.updateTooltip(mousePosition);

  if (this.onMouseMove) {
    this.onMouseMove(e, this);
  }

  this.render();
};

OGameLineChart.prototype.handleMouseLeave = function (e) {
  this.setHighlight(null);
  this.render();
};

OGameLineChart.prototype.updateHighlight = function (p) {
  let line = this.getClosestLine(p, this.lineThresholdHighlight);
  let key = line ? line.key : null;
  this.setHighlight(key);
};

OGameLineChart.prototype.updateTooltip = function (p) {
  let pnt = this.getClosestDataPoint(p, this.lineThresholdHighlight);

  if (pnt === null) {
    this.hideTooltip();
  } else {
    let a = this.transformDataPointToCanvas(pnt.index, pnt.dataPoint.y);
    let tooltipText = (this.tooltips[pnt.key] || "") + ":" + pnt.dataPoint.y.toFixed(2);
    this.tooltip.html(tooltipText);
    this.tooltip.css({
      left: a.x - 20,
      top: a.y,
    });
    this.showTooltip();
  } //let dataPoints = this.getClosestDataPoint(p,key)
};

OGameLineChart.prototype.showTooltip = function () {
  this.tooltip.show();
};

OGameLineChart.prototype.hideTooltip = function () {
  this.tooltip.hide();
};

OGameLineChart.prototype.getClosestLine = function (p, threshold) {
  let currentKey = null;
  let currentDistance = null;
  let currentIndex = null;

  for (let key in this.dataPoints) {
    let dataPoints = this.dataPoints[key];

    for (let i = 0; i < dataPoints.length - 1; ++i) {
      let a = this.transformDataPointToCanvas(i, dataPoints[i].y);
      let b = this.transformDataPointToCanvas(i + 1, dataPoints[i + 1].y);
      let d = this.orthogonalDistanceFromLineSegment(p, a, b);

      if (d === null || d > threshold) {
        continue;
      }

      if (currentDistance === null || currentDistance > d) {
        currentKey = key;
        currentDistance = d;
        currentIndex = i;
      }
    }
  }

  if (currentKey !== null) {
    return {
      key: currentKey,
      distance: currentDistance,
      index: currentIndex,
    };
  }

  return null;
};

OGameLineChart.prototype.getClosestDataPoint = function (p, threshold) {
  let line = this.getClosestLine(p, threshold);

  if (line === null) {
    return null;
  }

  let currentDistance = null;
  let currentDataPoint = null;
  let currentIndex = null;
  let dataPoints = this.getDataPoints(line.key);

  if (dataPoints) {
    for (let i = 0; i < dataPoints.length; ++i) {
      let a = this.transformDataPointToCanvas(i, dataPoints[i].y);
      let d = this.distance(a, p);

      if (d > threshold) {
        continue;
      }

      if (currentDistance === null || currentDistance > d) {
        currentDataPoint = dataPoints[i];
        currentDistance = d;
        currentIndex = i;
      }
    }
  }

  if (currentDataPoint !== null) {
    return {
      key: line.key,
      index: currentIndex,
      dataPoint: currentDataPoint,
    };
  }

  return null;
};

OGameLineChart.prototype.render = function () {
  this.height = $(this.canvas).outerHeight();
  this.width = $(this.canvas).outerWidth();
  this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
  this.renderTitle();
  this.renderLabels();
  this.renderData();
};

OGameLineChart.prototype.renderTitle = function () {
  this.context.save();
  this.context.font = this.titleFont;
  this.context.fillStyle = this.titleColor;
  this.context.textBaseline = this.titleBaseline;
  let px = this.marginLeft;
  let py = Math.floor(this.marginTop / 2);
  this.context.fillText(this.title, px, py);
  this.context.restore();
};

OGameLineChart.prototype.renderLabels = function () {
  this.renderAxis();
  this.renderVerticalLabels();
  this.renderHorizontalLabels();
};

OGameLineChart.prototype.renderAxis = function () {
  let sx = this.marginLeft;
  let sy = this.marginTop;
  let ex = this.marginLeft;
  let ey = this.height - this.marginBottom;
  this.context.save();
  this.context.strokeStyle = this.guidesStyle;
  this.context.beginPath();
  this.context.moveTo(sx, sy);
  this.context.lineTo(ex, ey);
  this.context.stroke();
  this.context.closePath();
  sx = ex;
  sy = ey;
  ex = this.width - this.marginRight;
  ey = this.height - this.marginBottom;
  this.context.beginPath();
  this.context.moveTo(sx, sy);
  this.context.lineTo(ex, ey);
  this.context.stroke();
  this.context.closePath();
  this.context.restore();
};

OGameLineChart.prototype.renderVerticalLabels = function () {
  let drawMinY = this.marginTop;
  let drawMaxY = this.height - this.marginBottom;
  let numLabels = Math.floor((drawMaxY - drawMinY) / (this.labelLineHeightY + this.labelSpacingY)) + 1;
  let drawDeltaY = Math.floor((drawMaxY - drawMinY) / (numLabels - 1));
  let minY = Math.floor(this.getMinY() / this.deltaY);
  let maxY = Math.ceil(this.getMaxY() / this.deltaY);
  let deltaY = Math.floor((maxY - minY) / (numLabels - 1));
  let px = 0;
  let py = drawMaxY;
  this.context.save();
  this.context.font = this.labelFontY;
  this.context.fillStyle = this.labelColorY;
  this.context.textBaseline = this.labelBaselineY;

  for (let i = 0; i < numLabels; ++i) {
    let y = minY + deltaY * i;
    let label = tsdpkt(y.toFixed(this.digitsY));
    let labelWidth = this.context.measureText(label).width;
    let dx = Math.floor(this.marginLeft / 2 - labelWidth / 2);
    let py = drawMaxY - drawDeltaY * i;
    this.context.fillText(label, px + dx, py);
  }

  this.context.restore();
};

OGameLineChart.prototype.renderHorizontalLabels = function () {
  let keys = this.getDataKeys();

  if (keys.length === 0) {
    return;
  }

  let key = keys[0];
  let dataPoints = this.getDataPoints(key);

  if (dataPoints.length < 2) {
    return;
  }

  let drawMinX = this.marginLeft;
  let drawMaxX = this.width - this.marginRight;
  let drawDeltaX = Math.floor((drawMaxX - drawMinX) / (dataPoints.length - 1));
  let px = this.marginLeft;
  let py = this.height - this.marginBottom + this.labelHeightY;
  this.context.save();
  this.context.font = this.labelFontX;
  this.context.fillStyle = this.labelColorX;
  this.context.textBaseline = this.labelBaselineX;

  for (let i = 0; i < dataPoints.length; ++i) {
    let label = dataPoints[i].x.toString();
    let labelWidth = this.context.measureText(label).width;
    let dx = Math.floor(labelWidth / 2); // draw rotated text

    this.context.save();
    this.context.translate(px, py);
    this.context.rotate(Math.PI * 0.375);
    this.context.fillText(label, 0, 0);
    this.context.restore();
    px += drawDeltaX;
  }

  this.context.restore();
};

OGameLineChart.prototype.renderData = function () {
  for (let key in this.dataPoints) {
    if (this.dataKeyHighlight === key) {
      continue;
    }

    this.renderDataPoints(key, this.dataPoints[key]);
  } // render highlighted data set on top

  if (this.dataKeyHighlight !== null) {
    this.renderDataPoints(this.dataKeyHighlight, this.dataPoints[this.dataKeyHighlight]);
  }
};

OGameLineChart.prototype.renderDataPoints = function (key, dataPoints) {
  if (!this.isDataSetVisible(key)) {
    return;
  }

  this.context.save();
  this.context.strokeStyle = this.getLineStyle(key);
  this.context.lineWidth = this.getLineWidth(key);
  this.context.beginPath();

  for (let i = 0; i < dataPoints.length - 1; ++i) {
    let s = this.transformDataPointToCanvas(i, dataPoints[i].y);
    let e = this.transformDataPointToCanvas(i + 1, dataPoints[i + 1].y);
    this.context.moveTo(s.x, s.y);
    this.context.lineTo(e.x, e.y);
  }

  this.context.stroke();
  this.context.closePath();
  this.context.restore();
};

OGameLineChart.prototype.transformDataPointToCanvas = function (x, y) {
  let minY = Math.ceil(this.getMinY() / this.deltaY);
  let maxY = Math.floor(this.getMaxY() / this.deltaY);
  let numDataPoints = this.getNumDataPoints();
  if (y === undefined) throw "Y is undefined";
  let drawMinY = this.marginTop;
  let drawMaxY = this.height - this.marginBottom;
  let drawMinX = this.marginLeft;
  let drawMaxX = this.width - this.marginRight;
  let drawDeltaX = Math.floor((drawMaxX - drawMinX) / (numDataPoints - 1));
  let scaleY = (drawMaxY - drawMinY) / (maxY - minY);
  let cx = Math.floor(drawDeltaX * x + drawMinX);
  let cy = Math.floor(-(y - minY) * scaleY + drawMaxY);
  return {
    x: cx,
    y: cy,
  };
};

OGameLineChart.prototype.transformEventToCanvas = function (e) {
  let canvasOffset = $(this.canvas).offset();
  let x = e.pageX - canvasOffset.left;
  let y = e.pageY - canvasOffset.top;
  return {
    x: x,
    y: y,
  };
};

OGameLineChart.prototype.getMinY = function () {
  let value = null;

  for (let k in this.dataPoints) {
    let points = this.dataPoints[k];

    for (let i = 0; i < points.length; ++i) {
      if (value === null || value > points[i].y) {
        value = points[i].y;
      }
    }
  }

  if (value === null) {
    return 0;
  }

  return value - 1;
};

OGameLineChart.prototype.getMaxY = function () {
  let value = null;

  for (let k in this.dataPoints) {
    let points = this.dataPoints[k];

    for (let i = 0; i < points.length; ++i) {
      if (value === null || value < points[i].y) {
        value = points[i].y;
      }
    }
  }

  if (value === null) {
    return 4;
  }

  return value + 1;
};

OGameLineChart.prototype.getDataKeys = function () {
  let keys = [];

  for (let k in this.dataPoints) {
    keys.push(k);
  }

  return keys;
};

OGameLineChart.prototype.getDataPoints = function (key) {
  if (this.dataPoints[key] !== undefined) {
    return this.dataPoints[key];
  }

  return [];
};

OGameLineChart.prototype.getNumDataPoints = function () {
  let numDataPoints = 0;

  for (let k in this.dataPoints) {
    if (numDataPoints < this.dataPoints[k].length) {
      numDataPoints = this.dataPoints[k].length;
    }
  }

  return numDataPoints;
};

OGameLineChart.prototype.setHighlight = function (key) {
  this.dataKeyHighlight = key;
};

OGameLineChart.prototype.setDataSetVisible = function (key, visible) {
  this.visibility[key] = visible === true;
};

OGameLineChart.prototype.isDataSetVisible = function (key) {
  if (this.visibility[key] !== undefined) {
    return this.visibility[key];
  }

  return true;
};

OGameLineChart.prototype.getLineStyle = function (key) {
  if (key === this.dataKeyHighlight) {
    return this.lineStyleHighlight;
  }

  return this.lineStyles[key] || "#ffffff";
};

OGameLineChart.prototype.getLineWidth = function (key) {
  if (key === this.dataKeyHighlight) {
    return this.lineWidthHighlight;
  }

  return this.lineWidths[key] || 1;
};

OGameLineChart.prototype.fixDPI = function () {
  let dpi = window.devicePixelRatio;
  let that = this; //create a style object that returns width and height

  let style = {
    height: function () {
      return +getComputedStyle(that.canvas).getPropertyValue("height").slice(0, -2);
    },
    width: function () {
      return +getComputedStyle(that.canvas).getPropertyValue("width").slice(0, -2);
    },
  }; //set the correct attributes for a crystal clear image!

  this.canvas.setAttribute("width", style.width() * dpi);
  this.canvas.setAttribute("height", style.height() * dpi);
};

OGameLineChart.prototype.crossProduct = function (a, b, c) {
  return (c.y - a.y) * (b.x - a.x) - (c.x - a.x) * (b.y - a.y);
};

OGameLineChart.prototype.dotProduct = function (a, b) {
  return a.x * b.x + a.y * b.y;
};

OGameLineChart.prototype.addVector2 = function (a, b) {
  return {
    x: a.x + b.x,
    y: a.y + b.y,
  };
};

OGameLineChart.prototype.subVector2 = function (a, b) {
  return {
    x: a.x - b.x,
    y: a.y - b.y,
  };
};

OGameLineChart.prototype.scaleVector2 = function (v, s) {
  return {
    x: v.x * s,
    y: v.y * s,
  };
};

OGameLineChart.prototype.distance = function (a, b) {
  let delta = this.subVector2(a, b);
  return Math.sqrt(this.dotProduct(delta, delta));
};

OGameLineChart.prototype.projectOntoLineSegment = function (p, a, b) {
  let ap = this.subVector2(p, a);
  let ab = this.subVector2(b, a);
  let c = this.dotProduct(ap, ab) / this.dotProduct(ab, ab);

  if (c < 0 || c > 1) {
    return null;
  }

  return this.addVector2(a, this.scaleVector2(ab, c));
};

OGameLineChart.prototype.orthogonalDistanceFromLineSegment = function (p, a, b) {
  let c = this.projectOntoLineSegment(p, a, b);

  if (c === null) {
    return null;
  }

  let cp = this.subVector2(p, c);
  let sq = this.dotProduct(cp, cp);
  return Math.sqrt(sq);
};
