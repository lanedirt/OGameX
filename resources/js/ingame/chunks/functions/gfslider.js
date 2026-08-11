function GFSlider(obj) {
  var thisObj = this; // gekapseltes JavaScript-Objekt
  //thisObj.sliderObj = obj; // betroffenes HTML Obj
  //config (allgemein)

  thisObj.duration = 500; // in ms - gesamtdauer des slidevorgangs

  thisObj.zIndex = 10;
  thisObj.intervalTime = 30; // in ms - framerate des slidens
  // initialisierung

  thisObj.lastTid = 0; //thisObj.sliderObj.style.zIndex = thisObj.lastZIndex;

  thisObj.inAction = false;
  thisObj.isOpen = false;
  thisObj.lastObj = false;
  thisObj.currHeight = obj.offsetHeight;
  thisObj.opacity = 1; // extra funktionalitaet

  thisObj.header = document.getElementById("header_text");
  thisObj.ressButton = document.getElementById("resources_button");
  thisObj.areaMap = document.getElementById("transImg");

  this.slideIn = function (obj, thisTid, reload) {
    if (!thisObj.inAction) {
      thisObj.slideInObj = obj;

      if (thisObj.lastTid != thisTid || reload) {
        //thisObj.header.style.position='absolute';
        //thisObj.header.style.display='none';
        obj.opacity = 1;
        thisObj.lastTid = thisTid;
        $("#detail").html('<div id="techDetailLoading"></div>');

        if (!thisObj.isOpen) {
          if (thisObj.ressButton) thisObj.ressButton.style.display = "none";
          obj.style.height = "1px";
          obj.style.display = "block";
          obj.style.overflow = "hidden";
          thisObj.inAction = true;
          thisObj.startTime = new Date().getTime();
          thisObj.slideInStep();
          thisObj.isOpen = true;
        } else {
          loadDetails(thisObj.lastTid);
        }
      } else {
        thisObj.header.style.display = "block";
        thisObj.opacity = 0;
        thisObj.lastTid = 0;
        thisObj.inAction = true;
        thisObj.isOpen = false;
        thisObj.startTime = new Date().getTime();
        thisObj.slideOutObj = thisObj.slideInObj;
        thisObj.slideOutStep();
      }
    }
  };

  this.slideInStep = function () {
    obj = thisObj.slideInObj;
    var time = new Date().getTime();
    var height = parseInt(thisObj.currHeight * ((time - thisObj.startTime) / thisObj.duration));

    if (height < thisObj.currHeight) {
      obj.style.height = height + "px";
      obj.style.marginTop = thisObj.currHeight - 1 - height + "px";
      window.setTimeout(thisObj.slideInStep, thisObj.intervalTime);
      thisObj.opacity = Math.max(thisObj.opacity - 0.1, 0);
      thisObj.header.style.opacity = thisObj.opacity; //header.style.filter='alpha(opacity='+(this.opacity*100)+')';

      thisObj.header.style.filter = "Alpha(opacity=" + 0.5 * 100 + ")";
    } else {
      // Ajax Call
      obj.style.height = thisObj.currHeight + "px";
      obj.style.marginTop = "0px";
      thisObj.inAction = false;
      thisObj.header.style.display = "none";
      loadDetails(thisObj.lastTid);

      if (thisObj.lastObj && obj != thisObj.lastObj) {
        thisObj.hideLast();
      }

      thisObj.lastObj = obj;
    }
  };

  this.slideOutStep = function () {
    obj = thisObj.slideInObj;
    var time = new Date().getTime();
    height = parseInt(thisObj.currHeight * ((time - thisObj.startTime) / thisObj.duration));

    if (height < thisObj.currHeight) {
      obj.style.height = thisObj.currHeight - 1 - height + "px";
      obj.style.marginTop = height + "px";
      window.setTimeout(thisObj.slideOutStep, thisObj.intervalTime);
      thisObj.opacity = Math.max(thisObj.opacity + 0.1, 0);
      thisObj.header.style.opacity = thisObj.opacity;
    } else {
      obj.style.height = thisObj.currHeight + "px";
      obj.style.marginTop = "0px";
      thisObj.opacity = 1;
      thisObj.header.style.opacity = thisObj.opacity;

      if (thisObj.ressButton) {
        thisObj.ressButton.style.display = "block";
      }

      obj.style.display = "none";
      thisObj.inAction = false;
      thisObj.hideLast();
    }
  };

  this.hideLast = function () {
    if (thisObj.lastObj) {
      $(".slideIn").removeClass("active");
      thisObj.lastObj.style.display = "none";
      thisObj.inAction = false;
    }

    $(document).trigger("ajaxHideElement", thisObj.lastTid);
  };

  this.hide = function (obj) {
    $(".slideIn").removeClass("active");
    thisObj.slideOutObj = obj;
    thisObj.opacity = 1;
    thisObj.header.style.opacity = thisObj.opacity;
    thisObj.header.style.display = "block";

    if (thisObj.ressButton) {
      thisObj.ressButton.style.display = "block";
    }

    if (thisObj.areaMap) {
      thisObj.areaMap.style.display = "block";
    }

    thisObj.slideOutObj.style.display = "none";
    thisObj.inAction = false;
    thisObj.lastTid = 0;
    thisObj.isOpen = false;
  };
}
