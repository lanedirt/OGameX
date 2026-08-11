function movePlanet(url, data, reloadPage) {
  function movePlanetExecute() {
    $.post(
      url,
      data,
      function (res) {
        if (res.error == "") {
          fadeBox(galaxyLoca.reservationSuccess, false);
          setTimeout('reload_page("' + reloadPage + '")', 3000);
        } else {
          fadeBox(res.error, true);
        }
      },
      "json",
    );
  }

  errorBoxDecision(
    galaxyLoca.questionTitle,
    galaxyLoca.question,
    LocalizationStrings.yes,
    LocalizationStrings.no,
    movePlanetExecute,
  );
}
