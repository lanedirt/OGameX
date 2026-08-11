// jQuery WipeTouch 1.2.0
// ------------------------------------------------------------------------
//
// Developed and maintained by Igor Ramadas
// http://aboutigor.com
// http://devv.com
//
// USAGE
// ------------------------------------------------------------------------
//
// $(selector).wipetouch(config);
//
// The wipe events should expect the result object with the following properties:
// speed - the wipe speed from 1 to 5
// x - how many pixels moved on the horizontal axis
// y - how many pixels moved on the vertical axis
// source - the element which triggered the wipe gesture
//
// EXAMPLE
//		$(document).wipetouch({
//			allowDiagonal: true,
//			wipeLeft: function(result) { alert("Left on speed " + result.speed) },
//			wipeTopLeft: function(result) { alert("Top left on speed " + result.speed) },
//			wipeBottomLeft: function(result) { alert("Bottom left on speed " + result.speed) }
//		});
//
//
// More details at http://wipetouch.codeplex.com/
//
// CHANGE LOG
// ------------------------------------------------------------------------
// 1.2.0
// - New: wipeMove event, triggered while moving the mouse/finger.
// - New: added "source" to the result object.
// - Bug fix: sometimes vertical wipe events would not trigger correctly.
// - Bug fix: improved tapToClick handler.
// - General code refactoring.
// - Windows Phone 7 is not supported, yet! Its behaviour is completely broken and would require some special tricks to make it work. Maybe in the future...
//
// 1.1.0
// - New: tapToClick, if true will identify taps and and trigger a click on the touched element. Default is false.
// - Changed: events wipeBottom*** and wipeTop*** renamed to wipeDown*** and wipeUp***.
// - Changed: better touch speed calculation (was always too fast before).
// - Changed: speed will be an integer now (instead of float).
// - Changed: better wipe detection (if Y movement is more than X, do a vertical wipe instead of horizontal).
// - Bug fix: added preventDefault to touchStart and touchEnd internal events (this was missing).
// - Other general tweaks to the code.
//
// The minified version of WipeTouch can be generated using Jasc: http://jasc.codeplex.com
(function ($) {
  $.fn.wipetouch = function (settings) {
    // ------------------------------------------------------------------------
    // PLUGIN SETTINGS
    // ------------------------------------------------------------------------
    var config = {
      // Variables and options
      moveX: 40,
      // minimum amount of horizontal pixels to trigger a wipe event
      moveY: 40,
      // minimum amount of vertical pixels to trigger a wipe event
      tapToClick: false,
      // if user taps the screen it will fire a click event on the touched element
      preventDefault: true,
      // if true, prevents default events (click for example)
      allowDiagonal: false,
      // if false, will trigger horizontal and vertical movements so wipeUpLeft, wipeDownLeft, wipeUpRight, wipeDownRight are ignored,
      preventDefaultWhenTriggering: true,
      // Wipe events
      wipeLeft: false,
      // called on wipe left gesture
      wipeRight: false,
      // called on wipe right gesture
      wipeUp: false,
      // called on wipe up gesture
      wipeDown: false,
      // called on wipe down gesture
      wipeUpLeft: false,
      // called on wipe top and left gesture
      wipeDownLeft: false,
      // called on wipe bottom and left gesture
      wipeUpRight: false,
      // called on wipe top and right gesture
      wipeDownRight: false,
      // called on wipe bottom and right gesture
      wipeMove: false,
      // triggered whenever touchMove acts
      // DEPRECATED EVENTS
      wipeTopLeft: false,
      // USE WIPEUPLEFT
      wipeBottomLeft: false,
      // USE WIPEDOWNLEFT
      wipeTopRight: false,
      // USE WIPEUPRIGHT
      wipeBottomRight: false, // USE WIPEDOWNRIGHT
    };

    if (settings) {
      $.extend(config, settings);
    }

    this.each(function () {
      // ------------------------------------------------------------------------
      // INTERNAL VARIABLES
      // ------------------------------------------------------------------------
      var startX; // where touch has started, left

      var startY; // where touch has started, top

      var startDate = false; // used to calculate timing and aprox. acceleration

      var curX; // keeps touch X position while moving on the screen

      var curY; // keeps touch Y position while moving on the screen

      var isMoving = false; // is user touching and moving?

      var touchedElement = false; // element which user has touched
      // These are for non-touch devices!

      var useMouseEvents = false; // force using the mouse events to simulate touch

      var clickEvent = false; // holds the click event of the target, when used hasn't clicked
      // ------------------------------------------------------------------------
      // TOUCH EVENTS
      // ------------------------------------------------------------------------
      // Called when user touches the screen.

      function onTouchStart(e) {
        resetTouch();
        var start = useMouseEvents || (e.originalEvent.touches && e.originalEvent.touches.length > 0);

        if (!isMoving && start) {
          if (config.preventDefault) {
            e.preventDefault();
          } // Temporary fix for deprecated events, these will be removed on next version!

          if (config.allowDiagonal) {
            if (!config.wipeDownLeft) {
              config.wipeDownLeft = config.wipeBottomLeft;
            }

            if (!config.wipeDownRight) {
              config.wipeDownRight = config.wipeBottomRight;
            }

            if (!config.wipeUpLeft) {
              config.wipeUpLeft = config.wipeTopLeft;
            }

            if (!config.wipeUpRight) {
              config.wipeUpRight = config.wipeTopRight;
            }
          } // When touch events are not present, use mouse events.

          if (useMouseEvents) {
            startX = e.pageX;
            startY = e.pageY;
            $(this).bind("mousemove", onTouchMove);
            $(this).one("mouseup", onTouchEnd);
          } else {
            startX = e.originalEvent.touches[0].pageX;
            startY = e.originalEvent.touches[0].pageY;
            $(this).bind("touchmove", onTouchMove);
          } // Set the start date and current X/Y.

          startDate = new Date().getTime();
          curX = startX;
          curY = startY;
          isMoving = true;
          touchedElement = $(e.target);
        }
      } // Called when user untouches the screen.

      function onTouchEnd(e) {
        if (config.preventDefault) {
          e.preventDefault();
        } // When touch events are not present, use mouse events.

        if (useMouseEvents) {
          $(this).unbind("mousemove", onTouchMove);
        } else {
          $(this).unbind("touchmove", onTouchMove);
        } // If is moving then calculate the touch results, otherwise reset it.

        if (isMoving) {
          touchCalculate(e);
        } else {
          resetTouch();
        }
      } // Called when user is touching and moving on the screen.

      function onTouchMove(e) {
        if (config.preventDefault) {
          e.preventDefault();
        }

        if (useMouseEvents && !isMoving) {
          onTouchStart(e);
        }

        if (isMoving) {
          if (useMouseEvents) {
            curX = e.pageX;
            curY = e.pageY;
          } else {
            curX = e.originalEvent.touches[0].pageX;
            curY = e.originalEvent.touches[0].pageY;
          } // If there's a wipeMove event, call it passing
          // current X and Y position (curX and curY).

          if (config.wipeMove) {
            triggerEvent(config.wipeMove, {
              curX: curX,
              curY: curY,
            });
          }
        }
      } // ------------------------------------------------------------------------
      // CALCULATE TOUCH AND TRIGGER
      // ------------------------------------------------------------------------

      function touchCalculate(e) {
        var endDate = new Date().getTime(); // current date to calculate timing

        var ms = startDate - endDate; // duration of touch in milliseconds

        var x = curX; // current left position

        var y = curY; // current top position

        var dx = x - startX; // diff of current left to starting left

        var dy = y - startY; // diff of current top to starting top

        var ax = Math.abs(dx); // amount of horizontal movement

        var ay = Math.abs(dy); // amount of vertical movement
        // If moved less than 15 pixels, touch duration is less than 100ms,
        // and tapToClick is true then trigger a click event and stop processing.

        if (ax < 15 && ay < 15 && ms < 100) {
          clickEvent = false;

          if (config.preventDefault) {
            resetTouch();
            touchedElement.trigger("click");
            return;
          }
        } // When touch events are not present, use mouse events.
        else if (useMouseEvents) {
          var evts = touchedElement.data("events");

          if (evts) {
            // Save click event to the temp clickEvent variable.
            var clicks = evts.click;

            if (clicks && clicks.length > 0) {
              $.each(clicks, function (i, f) {
                clickEvent = f;
                return;
              });
              touchedElement.unbind("click");
            }
          }
        } // Is it moving to the right or left, top or bottom?

        var toright = dx > 0;
        var tobottom = dy > 0; // Calculate speed from 1 to 5, 1 being slower and 5 faster.

        var s = ((ax + ay) * 60) / ((ms / 6) * ms);
        if (s < 1) s = 1;
        if (s > 5) s = 5;
        var result = {
          speed: parseInt(s),
          x: ax,
          y: ay,
          source: touchedElement,
        };

        if (ax >= config.moveX) {
          // Check if it's allowed and trigger diagonal wipe events.
          if (config.allowDiagonal && ay >= config.moveY) {
            if (toright && tobottom) {
              triggerEvent(config.wipeDownRight, result, e);
            } else if (toright && !tobottom) {
              triggerEvent(config.wipeUpRight, result, e);
            } else if (!toright && tobottom) {
              triggerEvent(config.wipeDownLeft, result, e);
            } else {
              triggerEvent(config.wipeUpLeft, result, e);
            }
          } // Otherwise trigger horizontal events if X > Y.
          else if (ax >= ay) {
            if (toright) {
              triggerEvent(config.wipeRight, result, e);
            } else {
              triggerEvent(config.wipeLeft, result, e);
            }
          }
        } // If Y > X and no diagonal, trigger vertical events.
        else if (ay >= config.moveY && ay > ax) {
          if (tobottom) {
            triggerEvent(config.wipeDown, result, e);
          } else {
            triggerEvent(config.wipeUp, result, e);
          }
        }

        resetTouch();
      } // Resets the cached variables.

      function resetTouch() {
        startX = false;
        startY = false;
        startDate = false;
        isMoving = false; // If there's a click event, bind after a few miliseconds.

        if (clickEvent) {
          window.setTimeout(function () {
            touchedElement.bind("click", clickEvent);
            clickEvent = false;
          }, 50);
        }
      } // Trigger a wipe event passing a result object with
      // speed from 1 to 5, x / y movement amount in pixels,
      // and the source element.

      function triggerEvent(wipeEvent, result, e) {
        if (wipeEvent) {
          if (config.preventDefaultWhenTriggering) {
            e.preventDefault();
          }

          wipeEvent(result);
        }
      } // ------------------------------------------------------------------------
      // ADD TOUCHSTART AND TOUCHEND EVENT LISTENERS
      // ------------------------------------------------------------------------

      if ("ontouchstart" in document.documentElement) {
        $(this).bind("touchstart", onTouchStart);
        $(this).bind("touchend", onTouchEnd);
      } else {
        useMouseEvents = true;
        $(this).bind("mousedown", onTouchStart);
        $(this).bind("mouseout mouseup", onTouchEnd);
      }
    });
    return this;
  };
})(jQuery);
