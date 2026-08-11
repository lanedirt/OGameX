$("[data-homepage-link]").on("click", function (e) {
  e.preventDefault();
  errorBoxDecision(
    LocalizationStrings.attention,
    LocalizationStrings.redirectMessage,
    LocalizationStrings.yes,
    LocalizationStrings.no,
    function () {
      window.location.href = "redir.php?url=" + encodeURIComponent(allyHome);
    },
    false,
    false,
  );
});
