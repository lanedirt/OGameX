$(function () {
  jQuery(".planetlink").hover(
    function () {
      jQuery(this).parent().addClass("hoverPlanet");
    },
    function () {
      jQuery(this).parent().removeClass("hoverPlanet");
    },
  );
  jQuery(".moonlink").hover(
    function () {
      jQuery(this).parent().addClass("hoverMoon");
    },
    function () {
      jQuery(this).parent().removeClass("hoverMoon");
    },
  );
});
