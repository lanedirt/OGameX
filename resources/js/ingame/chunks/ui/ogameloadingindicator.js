function OGameLoadingIndicator(container) {
  this.container = container;
}

OGameLoadingIndicator.prototype.init = function () {
  let html =
    '<div class="og-loading"><div class="og-loading-overlay"><div class="og-loading-indicator"></div></div></div>';
  this.element = $(html);
  this.container.append(this.element);
};

OGameLoadingIndicator.prototype.show = function () {
  this.element.show();
};

OGameLoadingIndicator.prototype.hide = function () {
  this.element.hide();
};
