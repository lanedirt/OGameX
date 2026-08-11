function displayMessage(response) {
  // bei Upgrades steht in der response nur 1 oder nichts... nichts was man anzeigen sollte. also nur reload
  location.reload(true); // true == NICHT aus dem Cache ^^
}
