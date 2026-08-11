ogame.frontendActions = {
  socket: null,
  connected: false,
  connecting: false,
  timeout: null,
  retryInterval: 5000,
  initConnection: function () {
    var that = ogame.frontendActions;

    if (that.connecting || that.connected || that.isMobile) {
      if (this.socket) {
        that.socket.disconnect();
      }
    }

    that.connecting = true;

    try {
      that.socket = io.connect(":" + nodePort + "/frontendactions", nodeParams);
      that.socket.on("connect", function () {
        clearTimeout(this.timeout); // send session for authorize... on success set it to connected else disconnect socket

        that.socket.emit("authorize", session, function (success) {
          that.connecting = false;

          if (success) {
            that.connected = true;
          } else {
            that.socket.disconnect();
          }
        });
      });
      that.socket.on("refreshFrontendData", function (data) {
        that.refreshFrontendData(data);
      });
      that.socket.on("refreshSimulationData", function (data) {
        that.refreshSimulationData(data);
      });
      that.socket.on("disconnect", function () {
        that.connected = false;
        that.connecting = false;
      });
    } catch (e) {
      that.connecting = false;
    }
  },
  initialize: function () {
    if (typeof nodeUrl === "undefined") {
      return;
    }

    // TODO: re-enable later
    return;

    var that = ogame.frontendActions;
    loadScript(nodeUrl, that.initConnection);
  },
  retryConnection: function () {
    var that = ogame.frontendActions;
    setTimeout(function () {
      that.initConnection();
    }, 5000);
  },
  refreshFrontendData: function (wsData) {
    if (buildListActionCalled || speedingUpBuildListEntry) {
      return;
    }

    try {
      let currentUrl = window.location.href;
      let currentPageParams = new URLSearchParams(currentUrl.split("?")[1] ?? "");
      let currentComponent = currentPageParams.get("component");
      let currentIngamePage = currentPageParams.get("page");

      if (wsData.type && wsData.type === "buildList") {
        if (currentComponent === "empire") {
          window.location.reload();
          return;
        }

        if (
          (wsData.spaceObjectId &&
            wsData.spaceObjectId !== "any" &&
            typeof currentSpaceObjectId !== "undefined" &&
            wsData.spaceObjectId === currentSpaceObjectId) ||
          wsData.spaceObjectId === "any"
        ) {
          if (currentComponent === "resourcesettings" && wsData.actionType && wsData.actionType === "built") {
            window.location.reload();
            return;
          }

          if (wsData.component) {
            if (wsData.component.indexOf(currentComponent) > -1 || wsData.component.indexOf(currentIngamePage) > -1) {
              window.location.reload();
              return;
            }
          }

          if (wsData.productionBox) {
            if ($(`#productionbox${wsData.productionBox}component`).length > 0) {
              reloadComponent(`productionbox${wsData.productionBox}`);
            }
          }

          if (typeof currentSpaceObjectId !== "undefined" && wsData.spaceObjectId === currentSpaceObjectId) {
            getAjaxResourcebox();
          }
        }

        reloadComponent("planetbar", currentIngamePage !== "ingame");
      }
    } catch (e) {}
  },
  refreshSimulationData: function (wsData) {
    try {
      if (wsData.type && wsData.type === "refreshSimInfo" && wsData.remove) {
        removeCombatSim(wsData.simId);
        loadSimDetails();
      }

      if (wsData.type && wsData.type === "refreshSimInfo") {
        changeCombatSimState(wsData.simId, wsData.simState);
        loadSimDetails();
      }
    } catch (e) {}
  },
  initFrontendActions: function () {
    ogame.frontendActions.initialize();
  },
};
