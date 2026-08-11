function openCloseDetails(id, expireTime) {
  if ($("#fleet" + id).attr("class") == "fleetDetails detailsOpened") {
    closeDetails(id, expireTime);
  } else {
    openDetails(id, expireTime);
  }
}
