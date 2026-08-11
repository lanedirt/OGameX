function getActivityStar(data) {
  if (data.showActivity === false) {
    return "";
  }

  if (data.showMinutes && data.showActivity === 60) {
    return `<div class="activity showMinutes tooltip js_hideTipOnMobile hideTooltipOnMouseenter"
                title="${loca.LOCA_ALL_ACTIVITY}">
                ${data.idleTime}
            </div>`;
  }

  return `<div class="activity minute${data.showActivity} tooltip js_hideTipOnMobile hideTooltipOnMouseenter"
            title="${loca.LOCA_ALL_ACTIVITY}">
        </div>`;
}
