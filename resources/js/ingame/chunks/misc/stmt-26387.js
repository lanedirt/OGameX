$(document).on("click", "[data-toggable]", function () {
  let curr = $(this);
  let targetElement = curr.attr("data-toggable");
  let targetElementParent = curr.parent();
  $(targetElementParent)
    .find('[data-toggable-target="' + targetElement + '"]')
    .slideToggle({
      start: function () {
        if (curr.hasClass("active")) {
          curr.removeClass("active");
        } else {
          curr.addClass("active");
        }
      },
    });
});
