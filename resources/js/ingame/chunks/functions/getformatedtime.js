function getFormatedTime(time) {
  hours = Math.floor(time / 3600);
  timeleft = time % 3600;
  minutes = Math.floor(timeleft / 60);
  timeleft = timeleft % 60;
  seconds = timeleft;
  return dezInt(hours, 2) + ":" + dezInt(minutes, 2) + ":" + dezInt(seconds, 2);
}
