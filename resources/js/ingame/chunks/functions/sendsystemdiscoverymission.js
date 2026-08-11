function sendSystemDiscoveryMission() {
  if (typeof sendDiscoverSystemUrl === "undefined" || !sendDiscoverSystemUrl) {
    return;
  }

  if (typeof galaxy === "undefined" || !galaxy) {
    return;
  }

  if (typeof system === "undefined" || !system) {
    return;
  }

  if (sendingSystemDiscoveryMission) {
    return;
  }

  sendingSystemDiscoveryMission = true;
  $.ajax({
    url: sendDiscoverSystemUrl,
    data: {
      galaxy: galaxy,
      system: system,
      _token: token,
    },
    type: "POST",
    dataType: "json",
    success: function (res) {
      token = res.newAjaxToken;

      if (res.response.success) {
        getAjaxEventbox();
        getAjaxResourcebox();
        res.response.sentToCoordinates.map((coords) => {
          displayMiniFleetMessage({ ...res.response, coordinates: coords }, false);
          const targetIcon = $(".planetDiscover.position" + coords.position);
          targetIcon.replaceWith(`
                        <div class="planetDiscoverIcons planetDiscoverUnavailable tooltip icon js_hideTipOnMobile"
                            title="${galaxyLoca.discoveryUnderway}">
                        </div>
                    `);
        });
      } else {
        fadeBox(res.response.message, true);
      }

      sendingSystemDiscoveryMission = false;
    },
    error: function () {
      sendingSystemDiscoveryMission = false;
    },
  });
}
