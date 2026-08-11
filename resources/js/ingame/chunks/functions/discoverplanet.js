function discoverPlanet(url, data, success = () => {}) {
  const discover = () => {
    $.post(
      url,
      data,
      function (res) {
        token = res.newAjaxToken;

        if (typeof res.response.success !== "undefined" && res.response.success === true) {
          getAjaxEventbox();
          success();
          getAjaxResourcebox();
        }

        displayMiniFleetMessage(res.response);
        const discoveryIcons = Array.from(document.getElementsByClassName("planetDiscover"));
        discoveryIcons.forEach((icon) => {
          if (icon.classList.contains("position" + res.response.coordinates.position)) {
            return;
          }

          if (res.response.discovery.canSendDiscovery !== true) {
            $(icon).replaceWith(`
                        <div class="planetDiscoverIcons planetDiscoverUnavailable tooltip icon js_hideTipOnMobile"
                            title="${res.response.discovery.canSendDiscovery}">
                        </div>
                    `);
            return;
          }

          const titleText = galaxyLoca.discoverySend + " " + res.response.discovery.discoveryCount;
          icon.title = titleText;
          changeTooltip(icon, titleText);
        });
        const targetIcon = $(".planetDiscover.position" + res.response.coordinates.position);
        targetIcon.replaceWith(`<div class="planetDiscoverIcons planetDiscoverUnavailable tooltip icon js_hideTipOnMobile"
                    title="${galaxyLoca.discoveryUnderway}">
                </div>`);
        document.getElementById("galaxyHeaderDiscoveryCount").innerHTML =
          res.response.discovery.galaxyHeader.LOCA_GALAXY_LIFEFORM_DISCOVERY_COUNT;
      },
      "json",
    );
  };

  if (showDiscoveryWarning) {
    errorBoxDecision(
      galaxyLoca.discoverQuestionTitle,
      galaxyLoca.discoverQuestionText,
      LocalizationStrings.yes,
      LocalizationStrings.no,
      discover,
    );
  } else {
    discover();
  }
}
