function createExpireTime(timestamp) {
  var date = new Date();
  timestamp = timestamp * 1000;
  date.setTime(timestamp);
  return date;
}
