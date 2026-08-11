function showNotification(message, type = "info", headline = null) {
  let boxType = {
    error: {
      sign: "&#x2716;",
      cssClass: "notification-error",
      headline: headline ?? jsloca.LOCA_NOTIFY_ERROR,
    },
    info: {
      sign: "&#x2139;",
      cssClass: "notification-info",
      headline: headline ?? jsloca.LOCA_NOTIFY_INFO,
    },
    success: {
      sign: "&#x2714;",
      cssClass: "notification-success",
      headline: headline ?? jsloca.LOCA_NOTIFY_SUCCESS,
    },
    warning: {
      sign: "&#x2755;",
      cssClass: "notification-warning",
      headline: headline ?? jsloca.LOCA_NOTIFY_WARNING,
    },
  };
  let boxData = boxType[type] ?? boxType["info"];
  let notifyBoxId = Date.now();
  let notifyBox = `<notification id="${notifyBoxId}" class="${boxData.cssClass}">
            <notification-content>
                <notification-sign>${boxData.sign}</notification-sign>
                <notification-message>
                    <span class="headline">${boxData.headline}</span>
                    <span>${message}</span>
                </notification-message>
            </notification-content>
            <notification-close onclick="removeNotification(${notifyBoxId})">&#x2717;</notification-close>
            <notification-progress class=""></notification-progress>
        </notification>`;
  $("notification-container").append(notifyBox);
  let timer1 = setTimeout(() => {
    $("notification-container notification#" + notifyBoxId).addClass("active");
    $("notification-container notification#" + notifyBoxId + " notification-progress").addClass("active");
  }, 10);
  let timer2 = setTimeout(() => {
    $("notification-container notification#" + notifyBoxId).removeClass("active");
  }, 5010);
  let timer3 = setTimeout(() => {
    removeNotification(notifyBoxId);
  }, 5300);
  NotificationTimers[notifyBoxId] = {
    timer1: timer1,
    timer2: timer2,
    timer3: timer3,
  };
}
