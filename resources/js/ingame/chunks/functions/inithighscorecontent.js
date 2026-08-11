function initHighscoreContent() {
  if (userWantsFocus) {
    if ($("#position" + searchPosition).length > 0) {
      $("html, body").animate(
        {
          scrollTop: Math.max(0, $("#position" + searchPosition).offset().top - 200),
        },
        1000,
      );
    }
  }

  $(".changeSite").change(function () {
    var value = $(this).val();
    $("#stat_list_content").html('<div class="ajaxLoad">' + LocalizationStrings.loading + "</div>");
    ajaxCall(
      highscoreContentUrl + "?category=" + currentCategory + "&type=" + currentType + "&page=" + value,
      "#stat_list_content",
      initHighscoreContent,
    );
  }); // scroll to top buttons

  var scrollToTopButton = $("#scrollToTop");
  var positionCell = $("#ranks thead .score");

  function positionScrollButton() {
    if (positionCell.length) {
      scrollToTopButton.css("left", positionCell.offset().left);
    }
  }

  positionScrollButton();
  $(window).unbind("resize.highscoreTop").bind("resize.highscoreTop", positionScrollButton);
}
