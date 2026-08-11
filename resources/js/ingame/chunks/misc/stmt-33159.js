ogame.tools = {
  /**
   * adds a hover effect to given selectors
   * @param {String} selector - selector with elements to apply the style to
   * @returns {undefined}
   */
  addHover: function (selector) {
    $(selector).on({
      mouseenter: function () {
        $(this).addClass("over");
      },
      mouseleave: function () {
        $(this).removeClass("over");
      },
    });
  },

  /**
   * shows a "to top" button on long pages
   *
   * @returns {undefined}
   */
  scrollToTop: function () {
    var $scrollToTop = $(".scroll_to_top");
    $(window).on("scroll.scrollToTop", function () {
      $(".scroll_to_top").css(
        {
          visibility: $scrollToTop.offset().top > window.innerHeight ? "visible" : "hidden",
        },
        600,
      );
    });
    $scrollToTop.on("click.scrollToTop", function () {
      $("body, html").animate(
        {
          scrollTop: 0,
        },
        600,
      );
    });
  },
};
