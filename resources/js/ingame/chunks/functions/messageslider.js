function MessageSlider(obj) {
  var thisObj = this;
  thisObj.htmlobject = obj;
  var maxHeight = document.documentElement.clientHeight - 160;
  ((this.open = function () {
    if (!this.inAction) {
      thisObj.startTime = new Date().getTime();
      thisObj.inAction = true;
      thisObj.slideInStep();
    }
  }),
    (this.slideInStep = function () {
      time = new Date().getTime();
      height = parseInt(thisObj.currHeight * ((time - thisObj.startTime) / 500));

      if (height < thisObj.currHeight) {
        thisObj.htmlobject.style.height = height + "px";
        window.setTimeout(thisObj.slideInStep, 10);
      } else {
        thisObj.htmlobject.style.height = thisObj.currHeight + "px";
        thisObj.inAction = false;
      }
    }),
    (this.close = function () {
      if (!thisObj.inAction) {
        thisObj.startTime = new Date().getTime();
        thisObj.inAction = true;
        thisObj.htmlobject.style.height = "0px";
        thisObj.inAction = false;
      }
    }),
    (thisObj.inAction = false));

  if (document.getElementById("messages")) {
    thisObj.currHeight = Math.min(document.getElementById("messages").offsetHeight, maxHeight);
  } else {
    thisObj.currHeight = maxHeight;
  }
}
