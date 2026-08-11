function getActivityElement(activityObject) {
  let { idleTime, showActivity } = activityObject;

  if (showActivity) {
    let idle;

    if (showActivity === 60) {
      idle = loca.LOCA_ALL_ACTIVITY + ": " + idleTime + loca.LOCA_ALL_TIME_MINUTE;
    } else {
      idle =
        loca.LOCA_ALL_ACTIVITY +
        ":<div class=\"alert_triangle\"><img src='/img/icons/b4c8503dd1f37dc9924909d28f3b26.gif'/></div>";
    }

    return `<li>${idle}</li>`;
  }

  return "";
}
