/*
 * jQuery UI Touch Punch 0.2.3
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
!function(h){function i(f,e){if(!(f.originalEvent.touches.length>1)){f.preventDefault();var n=f.originalEvent.changedTouches[0],m=document.createEvent("MouseEvents");m.initMouseEvent(e,!0,!0,window,1,n.screenX,n.screenY,n.clientX,n.clientY,!1,!1,!1,!1,0,null),f.target.dispatchEvent(m)}}if(h.support.touch="ontouchend" in document,h.support.touch){var j,g=h.ui.mouse.prototype,l=g._mouseInit,k=g._mouseDestroy;g._touchStart=function(d){var c=this;!j&&c._mouseCapture(d.originalEvent.changedTouches[0])&&(j=!0,c._touchMoved=!1,i(d,"mouseover"),i(d,"mousemove"),i(d,"mousedown"))},g._touchMove=function(b){j&&(this._touchMoved=!0,i(b,"mousemove"))},g._touchEnd=function(b){j&&(i(b,"mouseup"),i(b,"mouseout"),this._touchMoved||i(b,"click"),j=!1)},g._mouseInit=function(){var a=this;a.element.bind({touchstart:h.proxy(a,"_touchStart"),touchmove:h.proxy(a,"_touchMove"),touchend:h.proxy(a,"_touchEnd")}),l.call(a)},g._mouseDestroy=function(){var a=this;a.element.unbind({touchstart:h.proxy(a,"_touchStart"),touchmove:h.proxy(a,"_touchMove"),touchend:h.proxy(a,"_touchEnd")}),k.call(a)}}}(jQuery);