function OGameSortable(container, data) {
  this.container = container;
  this.state = (data.state || "active") === "active" ? "active" : "inactive";
  this.order = data.order || "";
  this.align = data.align || "";
  this.allowUnsorted = data.allowUnsorted || false;
  this.token = data.token || null;
  this.onChange = data.onChange || null;
}

OGameSortable.prototype.init = function () {
  let label = this.container.text();
  let html = '<div class="og-sortable"><h3>' + label + '</h3><div class="icon"></div></div>';
  this.element = $(html);
  this.container.html(this.element);
  this.icon = this.element.find(".icon");
  $(this.element).on("click", this.handleClick.bind(this));
  this.refresh();
};

OGameSortable.prototype.handleClick = function () {
  if (this.state === "active") {
    if (this.order === "asc") {
      this.order = "desc";
    } else if (this.order === "desc" && this.allowUnsorted === false) {
      this.order = "asc";
    } else if (this.order === "desc" && this.allowUnsorted === true) {
      this.order = "";
    } else if (this.order === "") {
      this.order = "asc";
    }

    this.refresh();
    this.notifyChange();
  }
};

OGameSortable.prototype.notifyChange = function () {
  if (this.onChange) {
    this.onChange({
      order: this.order,
      state: this.state,
      _token: this.token,
    });
  }
};

OGameSortable.prototype.activate = function () {
  this.state = "active";
  this.refresh();
  this.notifyChange();
};

OGameSortable.prototype.deactivate = function () {
  this.state = "inactive";
  this.refresh();
  this.notifyChange();
};

OGameSortable.prototype.isValidOrder = function (order) {
  if (this.allowUnsorted === true) {
    return ["", "asc", "desc"].indexOf(order) !== -1;
  } else {
    return ["asc", "desc"].indexOf(order) !== -1;
  }
};

OGameSortable.prototype.setOrder = function (order) {
  if (this.isValidOrder(order)) {
    this.order = order;
    this.refresh();
    this.notifyChange();
  }
};

OGameSortable.prototype.getOrder = function () {
  return this.order;
};

OGameSortable.prototype.refresh = function () {
  this.refreshAlign();
  this.refreshState();
  this.refreshOrder();
};

OGameSortable.prototype.refreshAlign = function () {
  this.element.toggleClass("left", this.align === "left");
  this.element.toggleClass("center", this.align === "center");
  this.element.toggleClass("right", this.align === "right");
};

OGameSortable.prototype.refreshState = function () {
  this.element.toggleClass("active", this.state === "active");
  this.element.toggleClass("inactive", this.state === "inactive");
};

OGameSortable.prototype.refreshOrder = function () {
  this.element.toggleClass("asc", this.order === "asc");
  this.element.toggleClass("desc", this.order === "desc");
};

OGameSortable.prototype.update = function (data) {
  this.token = data.token || this.token;
};
