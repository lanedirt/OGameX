function initHighscore() {
  $("a.navButton, a.subnavButton").click(function () {
    var type = $(this).attr("rel");
    var $parent = $(this).parent();
    var searchRelString = "";

    if ($parent.attr("id") == "typeButtons") {
      $("#typeButtons > a.active").removeClass("active");
    } else if ($parent.attr("id") == "categoryButtons") {
      $("#categoryButtons > a.active").removeClass("active");
      $("#typeButtons a.active").each(function () {
        type = $(this).attr("rel");
      });
    }

    if ((searchRelId != null && $parent.attr("id") == "typeButtons") || $parent.attr("id") == "subnav_fleet") {
      searchRelString = "&searchRelId=" + searchRelId;
    }

    $(".subnavButton[rel!=" + type + "]").removeClass("active");
    $("#stat_list_content").html(LocalizationStrings["loading"]);
    $(this).addClass("active");
    var category = $("#categoryButtons > a.active").attr("rel");
    var url = highscoreContentUrl + "?category=" + category + "&type=" + type + searchRelString;

    if (($parent.attr("id") == "typeButtons" || $(this).hasClass("subnavButton")) && searchSite != site) {
      url = url + "&site=" + site;
    }

    removeTooltip(getTooltipSelector("#highscoreContent #ranks"));
    ajaxSubmit(url, "#send", "#stat_list_content", initHighscoreContent);

    if ($(".navButton.active").attr("rel") == 1) {
      $("#highscoreContent .header h2").text(highscoreLoca.playerHighscore);
    } else {
      $("#highscoreContent .header h2").text(highscoreLoca.allianceHighscore);
    }
  });
  $(".stat_filter").click(function () {
    var subnav = $(this).attr("id");
    $(".subnav").hide();
    $("#subnav_" + subnav).fadeIn("slow");
  }); // scroll to top buttons

  var threshold = $("#ranks").offset().top;
  var scrollToTopButton = $("#scrollToTop");
  $(window)
    .unbind("scroll.highscoreTop")
    .bind("scroll.highscoreTop", function (e) {
      var scrollTop = $(this).scrollTop();

      if (scrollTop > threshold) {
        scrollToTopButton.css("visibility", "visible");
      } else {
        scrollToTopButton.css("visibility", "hidden");
      }
    });
  $(document)
    .undelegate(".scrollToTop", "click")
    .delegate(".scrollToTop", "click", function () {
      $("html, body").animate(
        {
          scrollTop: 0,
        },
        50,
      );
    });
}
