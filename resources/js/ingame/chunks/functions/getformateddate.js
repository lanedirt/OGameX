function getFormatedDate(timestamp, format) {
  var currTime = new Date();
  currTime.setTime(timestamp);
  str = format;
  str = str.replace("[d]", dezInt(currTime.getDate(), 2));
  str = str.replace("[D]", days[currTime.getDay()]);
  str = str.replace("[m]", dezInt(currTime.getMonth() + 1, 2));
  str = str.replace("[M]", months[currTime.getMonth()]);
  str = str.replace("[j]", parseInt(currTime.getDate()));
  str = str.replace("[Y]", currTime.getFullYear());
  str = str.replace("[y]", currTime.getFullYear().toString().substr(2, 4));
  str = str.replace("[G]", currTime.getHours());
  str = str.replace("[H]", dezInt(currTime.getHours(), 2));
  str = str.replace("[i]", dezInt(currTime.getMinutes(), 2));
  str = str.replace("[s]", dezInt(currTime.getSeconds(), 2));
  return str;
}
