ogame.Notify = function (title, text, options) {
  this.canNotify = false;
  this.title = title || "OGame";
  this.options = options || {};
  this.options.body = text || ""; // Let's check if the browser supports notifications

  if (!("Notification" in window)) {
    // alert("This browser does not support system notifications");
    return false;
  } // Let's check whether notification permissions have already been granted
  else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    this.canNotify = true;
  } // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== "denied") {
    Notification.requestPermission(function (permission) {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        this.canNotify = true;
      }
    });
  }
};
