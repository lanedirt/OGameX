function initSpySystem() {
  $("#galaxyHeader")
    .off("click")
    .on("click", ".spysystemlink", function (event) {
      event.preventDefault();
      event.stopPropagation();
      let $target = $(event.target);
      let targetUrl = $target.data("targetUrl");

      if (!targetUrl) {
        targetUrl = $target.parents().data("targetUrl");
      }

      if (!targetUrl) {
        return;
      }

      $.post(
        targetUrl,
        {
          galaxy: $("#galaxy_input").val(),
          system: $("#system_input").val(),
          _token: token,
        },
        "json",
      ).done(function (jsoned) {
        let data = JSON.parse(jsoned);
        token = data.newAjaxToken;
        updateOverlayToken("phalanxDialog", data.newAjaxToken);
        updateOverlayToken("phalanxSystemDialog", data.newAjaxToken);

        for (let i = 0; i < data.planets.length; ++i) {
          $("#ownFleetStatus_" + data.planets[i].position + "_" + data.planets[i].type)
            .removeClass("fleetNeutral")
            .attr("title", galaxyLoca.fleetAttacking)
            .addClass("fleetHostile")
            .addClass("tooltip");
        }

        addToTable(data.text, data.count <= 0 ? "error" : "success");

        if (data.count > 0) {
          getAjaxEventbox();
        }
      });
    });
}
