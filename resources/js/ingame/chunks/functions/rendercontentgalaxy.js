function renderContentGalaxy(json) {
  token = json.token;
  updateOverlayToken("phalanxSystemDialog", json.token);
  updateOverlayToken("phalanxDialog", json.token);
  toGalaxyLink = json.system.toGalaxyLink;
  $("#amountColonized").html(json.system.slotsColonized);
  $("#probeValue").html(json.system.availableProbes);
  $("#recyclerValue").html(json.system.availableRecyclers);
  $("#missileValue").html(json.system.availableMissiles);
  $("#slotUsed").html(json.system.usedFleetSlots);
  $("#slotValue").html(json.system.maximumFleetSlots);
  $("input#galaxy_input").val(json.system.galaxy);
  $("input#system_input").val(json.system.system);
  canSwitchGalaxy = json.system.canSwitchGalaxy;
  // TODO: re-enable
  //getAjaxResourcebox();
  $.each(json.filterSettings, function (key, value) {
    if (value) {
      $(`#filterCell #${key}`).addClass("filter_active");
    }
  });

  if (!canSwitchGalaxy) {
    fadeBox(notEnoughDeuteriumMessage, true);
  }

  if (preserveSystemOnPlanetChange) {
    $(".planetlink, .moonlink").querystring({
      galaxy: json.system.galaxy,
      system: json.system.system,
    });
  }

  $("#expeditionDebris").remove();
  $("#galaxyRow17planet").remove();
  $("#galaxyRow17debris").remove();
  buildListCountdowns.map((countdownObject) => {
    timerHandler.removeCallback(countdownObject.getTimer);
  });
  buildListCountdowns = [];

  for (const galaxyContentObject of json.system.galaxyContent) {
    clearPosition(galaxyContentObject.position);

    if (galaxyContentObject.position === 16) {
      $("#expeditionDebrisSlotDebrisContainer").append(`
                <div id="expeditionDebris" class="name float_left tooltipRel tooltipClose tooltipRight js_hideTipOnMobile js_bday_debris tpd-hideOnClickOutside" rel="debris16">
                    <div style="position: relative;width: 30px;height: 30px;display: inline-block;">
                        <img class="float_left" src="/img/icons/fa3e396b8af2ae31e28ef3b44eca91.gif" width="30" height="30"/>
                        ${addFleetContainer(galaxyContentObject.position, galaxyContentObject.planets.planetType)}
                    </div>
                </div>
            `);
      $("#expeditionDebris").append(getDebrisTooltip(galaxyContentObject.planets, galaxyContentObject, json.system));
      getFleetIcon(
        galaxyContentObject.planets.fleet,
        galaxyContentObject.position,
        galaxyContentObject.planets.planetType,
      );
      continue;
    }

    if (galaxyContentObject.position === 17) {
      renderEventSpaceObjects(galaxyContentObject, json.system);
      continue;
    }

    $("#galaxyRow" + galaxyContentObject.position).addClass(galaxyContentObject.positionFilters);

    if (galaxyContentObject.planets.length > 0) {
      let shouldLoadPlayerToo = false;

      for (const planet of galaxyContentObject.planets) {
        switch (planet.planetType) {
          case 1:
            renderPlanet(galaxyContentObject, planet, json.system);
            shouldLoadPlayerToo = true;
            break;

          case 2:
            renderDebris(galaxyContentObject, planet, json.system);
            break;

          case 3:
            renderMoon(galaxyContentObject, planet, json.system);
            shouldLoadPlayerToo = true;
            break;
        }
      }

      if (shouldLoadPlayerToo) {
        renderPlayer(galaxyContentObject, json.system);
        colorNumberInFrontOfFriendsPlanet(galaxyContentObject);
        renderPhalanx(galaxyContentObject);
        renderAlliance(galaxyContentObject, json.system);
        renderActions(galaxyContentObject, json.system);
      } else {
        renderEmptySlot(galaxyContentObject, json.system, json.reservedPositions);
      }
    } else {
      renderEmptySlot(galaxyContentObject, json.system, json.reservedPositions);
    }
  }

  $("#galaxyLoading").hide();
  inProgress = false;

  if (typeof IPI !== "undefined") {
    IPI.refreshHighlights();
  }
}
