function incrementBuddyCount() {
  var buddyCount = parseInt($("#buddyCount").text()) + 1;
  var requestCount = parseInt($("#newRequestCount").text()) - 1;
  updateRequestCount(requestCount);
  updateBuddyCount(buddyCount);
}
