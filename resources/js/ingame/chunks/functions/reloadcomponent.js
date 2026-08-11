function reloadComponent(component, addFleft, callback) {
  $.ajax({
    type: "GET",
    url: `${ajaxReloadComponentURI}&component=${component}&currentComponent=${currentPage}`,
    success: function (response) {
      try {
        let parsedData = JSON.parse(response);
        $("#" + parsedData.id).replaceWith(parsedData.html);

        if (addFleft) {
          $("#" + parsedData.id).addClass("fleft");
        }

        token = parsedData.newAjaxToken;

        if (callback && typeof callback === "function") {
          callback();
        }

        $("select").ogameDropDown();
      } catch (e) {
        window.location.reload();
      }
    },
    error: function (err) {
      window.location.reload();
    },
  });
}
