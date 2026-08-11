function initToggleHeader(name) {
  $("a.toggleHeader[data-name=" + name + "]").click(function (e) {
    e.preventDefault();
    let toggleState = $(e.currentTarget).closest(".planet-header").hasClass("shortHeader");
    $(e.currentTarget).closest(".planet-header").toggleClass("shortHeader");
    $(".c-left").toggleClass("shortCorner");
    $(".c-right").toggleClass("shortCorner");
    changeSetting("headerImage", name + "|" + toggleState);
  });
}
