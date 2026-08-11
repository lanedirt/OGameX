function setShips(ship, count) {
  var e = document.getElementById(ship);

  if (e !== null) {
    e.innerHTML = count;
  }
}
