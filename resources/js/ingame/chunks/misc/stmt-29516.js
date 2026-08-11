ogame.Notify.prototype.show = function () {
  if (this.canNotify) {
    this.notification = new Notification(this.title, this.options);
    setTimeout(this.notification.close.bind(this.notification), 5000);
  }
};
