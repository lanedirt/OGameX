$(function () {
  $(document).on("click", "[data-homepage-link]", function (e) {
    e.preventDefault();
    errorBoxDecision(
      LocalizationStrings.attention,
      LocalizationStrings.redirectMessage,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      function () {
        window.open("redir.php?url=" + encodeURIComponent(allyHome), "_newtab");
      },
      false,
      false,
    );
  });
});
