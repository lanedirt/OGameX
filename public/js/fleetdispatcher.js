const FLEET_DISPATCH_PAGE1 = 'fleet1';
const FLEET_DISPATCH_PAGE2 = 'fleet2';

function FleetDispatcher(cfg) {
    this.fleetHelper = new FleetHelper(cfg);
    this.loading = false;
    this.lifeformEnabled = cfg.lifeformEnabled;
    this.checkTargetUrl = cfg.checkTargetUrl;
    this.sendFleetUrl = cfg.sendFleetUrl;
    this.saveSettingsUrl = cfg.saveSettingsUrl;
    this.fleetBoxOrder = cfg.fleetBoxOrder || {};
    this.token = cfg.token || null;
    this.currentPlanet = cfg.currentPlanet;
    this.targetPlanet = cfg.targetPlanet || null;
    this.targetInhabited = cfg.targetInhabited || false;
    this.targetPlayerId = cfg.targetPlayerId || this.fleetHelper.PLAYER_ID_SPACE;
    this.targetPlayerName = cfg.targetPlayerName || '';
    this.targetPlayerColorClass = cfg.targetPlayerColorClass || '';
    this.targetPlayerRankIcon = cfg.targetPlayerRankIcon || '';
    this.cargoCapacity = cfg.cargoCapacity;
    this.fuelCapacity = cfg.fuelCapacity;
    this.currentPage = FLEET_DISPATCH_PAGE1;
    this.shipsOnPlanet = cfg.shipsOnPlanet || [];
    this.shipsToSend = cfg.shipsToSend || [];
    this.useHalfSteps = cfg.useHalfSteps || false;
    this.planets = cfg.planets || [];
    this.standardFleets = cfg.standardFleets || [];
    this.unions = cfg.unions || [];
    this.orders = [];
    this.orderNames = cfg.orderNames || [];
    this.orderDescriptions = cfg.orderDescriptions || [];
    this.mission = cfg.mission || this.fleetHelper.MISSION_NONE;
    this.union = 0;
    this.targetIsStrong = false;
    this.targetIsOutlaw = false;
    this.targetIsBuddyOrAllyMember = false;
    this.playerIsOutlaw = false;
    this.retreatAfterDefenderRetreat = false;
    this.lootFoodOnAttack = cfg.lootFoodOnAttack;
    this.holdingTime = cfg.holdingTime;
    this.expeditionTime = cfg.expeditionTime;
    this.speedPercent = 10;
    this.cargoMetal = 0;
    this.cargoCrystal = 0;
    this.cargoDeuterium = 0;
    this.cargoFood = 0;
    this.prioMetal = cfg.LOOT_PRIO_METAL;
    this.prioCrystal = cfg.LOOT_PRIO_CRYSTAL;
    this.prioDeuterium = cfg.LOOT_PRIO_DEUTERIUM;
    this.prioFood = cfg.LOOT_PRIO_FOOD;
    this.metalOnPlanet = cfg.metalOnPlanet;
    this.crystalOnPlanet = cfg.crystalOnPlanet;
    this.deuteriumOnPlanet = cfg.deuteriumOnPlanet;
    this.foodOnPlanet = 0;

    if (this.lifeformEnabled) {
        this.foodOnPlanet = cfg.foodOnPlanet;
    }

    this.fleetCount = cfg.fleetCount;
    this.maxFleetCount = cfg.maxFleetCount;
    this.expeditionCount = cfg.expeditionCount;
    this.maxExpeditionCount = cfg.maxExpeditionCount;
    this.warningsEnabled = cfg.warningsEnabled;
    this.playerId = cfg.playerId;
    this.hasAdmiral = cfg.hasAdmiral;
    this.hasCommander = cfg.hasCommander;
    this.isOnVacation = cfg.isOnVacation;
    this.moveInProgress = cfg.moveInProgress;
    this.planetCount = cfg.planetCount;
    this.explorationCount = cfg.explorationCount;
    this.apiDataJson = cfg.apiDataJson;
    this.apiCommonData = cfg.apiCommonData;
    this.apiTechData = cfg.apiTechData;
    this.apiDefenseData = cfg.apiDefenseData;
    this.apiShipBaseData = cfg.apiShipBaseData;
    this.loca = cfg.loca;
    this.locadyn = cfg.locadyn;
    this.errorCodeMap = cfg.errorCodeMap;
    this.urlFleetCheck = cfg.urlFleetCheck;
    this.additionalFlightSpeed = 0;
    this.timerTimes = null;
    this.fetchTargetPlayerDataTimeout = null;
    this.deferred = [];
    this.emptySystems = cfg.emptySystems;
    this.inactiveSystems = cfg.inactiveSystems;
}

FleetDispatcher.prototype.init = function () {
    this.initFleet1();
    this.initFleet2();
    let that = this;
    $(function () {
        $(".sortable").sortable({
            handle: ".move-box",
            tolerance: "pointer",
            revert: true,
            update: function (event, ui) {
                a = $("#fleet2 #buttonz > div");
                param = {};
                that.appendTokenParams(param);

                for (let i = 0; i < a.length; i++) {
                    param[a[i].id] = i;
                }

                $.post(that.saveSettingsUrl, param, function (data) {
                    data = JSON.parse(data);
                    that.updateToken(data.newAjaxToken);
                    token = data.newAjaxToken;
                });
            }
        });
        $(".sortable").disableSelection();
    });
    $('#fleetdispatchcomponent').on('keypress', async function (e) {
        if (getKeyCode(e) === 13) {
            e.preventDefault();
            e.stopPropagation();

            if ($('#fleet1').is(':visible')) {
                await new Promise((resolve, reject) => setTimeout(() => resolve(), 250));
                that.trySubmitFleet1();
            } else if ($('#fleet2').is(':visible')) {
                // we have to wait to the update of a token because it already started communication with backend
                // should not trigger really but just as a safety measure
                if (!that.fetchTargetPlayerDataTimeout) {
                    await new Promise((resolve, reject) => setTimeout(() => resolve(), 250));
                    that.trySubmitFleet2();
                }
            }

            return false;
        }
    });
};

FleetDispatcher.prototype.displayErrors = function (errors) {
    // only display the first error
    let error = errors[0] || undefined;

    if (error) {
        fadeBox(error.message, true);
    }
};

FleetDispatcher.prototype.refresh = function () {
    switch (this.currentPage) {
        case FLEET_DISPATCH_PAGE1:
            this.refreshFleet1();
            break;

        case FLEET_DISPATCH_PAGE2:
            this.refreshFleet2();
            break;
    }
};

FleetDispatcher.prototype.switchToPage = function (page) {
    let that = this;

    if (page === this.currentPage) {
        return;
    }

    if (page === FLEET_DISPATCH_PAGE1) {
        this.currentPage = page;
        $('#' + FLEET_DISPATCH_PAGE1).show();
        $('#' + FLEET_DISPATCH_PAGE2).hide();
        that.focusSubmitFleet1();
    }

    if (page === FLEET_DISPATCH_PAGE2) {
        this.currentPage = page;
        $('#' + FLEET_DISPATCH_PAGE1).hide();
        $('#' + FLEET_DISPATCH_PAGE2).show();
        this.setTargetType(this.targetPlanet.type);
        that.focusSendFleet();
    }

    if (this.currentPage === FLEET_DISPATCH_PAGE1 && this.timerTimes !== null) {
        clearInterval(this.timerTimes);
        this.timerTimes = null;
    } // create timer to refresh fleet arrival and return times


    if (this.currentPage === FLEET_DISPATCH_PAGE2) {
        if (this.timerTimes === null) {
            this.timerTimes = setInterval(function () {
                that.refreshFleetTimes();
            }, 1000);
        }
    }

    this.refresh();
};

FleetDispatcher.prototype.startLoading = function () {
    this.loading = true;
    $('#fleetdispatchcomponent .ajax_loading').show();
};

FleetDispatcher.prototype.stopLoading = function () {
    this.loading = false;
    $('#fleetdispatchcomponent .ajax_loading').hide();
};

FleetDispatcher.prototype.updateToken = function (tokenNew) {
    token = tokenNew;
};

FleetDispatcher.prototype.appendTokenParams = function (params) {
    params.token = token;
};

FleetDispatcher.prototype.updateEmptySystems = function (newData) {
    this.emptySystems = newData;
};

FleetDispatcher.prototype.updateInactiveSystems = function (newData) {
    this.inactiveSystems = newData;
};

FleetDispatcher.prototype.appendShipParams = function (params) {
    this.shipsToSend.forEach(function (ship) {
        params['am' + ship.id] = ship.number;
    });
};

FleetDispatcher.prototype.appendTargetParams = function (params) {
    params.galaxy = this.targetPlanet.galaxy;
    params.system = this.targetPlanet.system;
    params.position = this.targetPlanet.position;
    params.type = this.targetPlanet.type;
};

FleetDispatcher.prototype.appendCargoParams = function (params) {
    params.metal = this.cargoMetal;
    params.crystal = this.cargoCrystal;
    params.deuterium = this.cargoDeuterium;

    if (this.lifeformEnabled) {
        params.food = this.cargoFood;
    }
};

FleetDispatcher.prototype.appendPrioParams = function (params) {
    params.prioMetal = this.prioMetal;
    params.prioCrystal = this.prioCrystal;
    params.prioDeuterium = this.prioDeuterium;

    if (this.lifeformEnabled) {
        params.prioFood = this.prioFood;
    }
};
/**
 * FLEET 1
 */


FleetDispatcher.prototype.initFleet1 = function () {
    initToggleHeader('fleet1');
    let that = this;
    let elem = $('#fleet1');
    elem.find('select.combatunits').ogameDropDown();
    that.refresh();
    elem.on('click', '#continueToFleet2', async function (e) {
        e.preventDefault();
        await new Promise((resolve, reject) => setTimeout(() => resolve(), 250));
        that.trySubmitFleet1();
    });
    elem.on('keyup', '#technologies li input', function (e) {
        e.preventDefault();
        let shipId = parseInt($(e.currentTarget).closest('li').data('technology'));
        let number = getValue($(e.currentTarget).val());
        that.selectShip(shipId, number);
    });
    elem.on('focusout', '#technologies li input', function (e) {
        e.preventDefault();
        let shipId = parseInt($(e.currentTarget).closest('li').data('technology'));
        let number = getValue($(e.currentTarget).val());
        that.selectShip(shipId, number);
        that.refresh();
    });
    elem.on('click', '#technologies li .icon', function (e) {
        e.preventDefault();
        let shipId = parseInt($(e.currentTarget).closest('li').data('technology'));

        if (that.getNumberOfShipsSelected(shipId) < that.getNumberOfShipsOnPlanet(shipId)) {
            that.selectMaxShips(shipId);
        } else {
            that.selectShip(shipId, 0);
        }

        that.refresh();
        that.focusSubmitFleet1();
    });
    elem.on('click', '#sendall', function (e) {
        e.preventDefault();
        that.selectAllShips();
        that.refresh();
        that.focusSubmitFleet1();
    });
    elem.on('click', '#resetall', function (e) {
        e.preventDefault();
        that.resetShips();
        that.refresh();
        that.focusSubmitFleet1();
    });
    elem.on('click', '#combatunits', function (e) {
        e.preventDefault();
        initStandardFleet();
    });
    elem.on('change', '#standardfleet', function (e) {
        let standardFleetId = getValue($('select.combatunits').val());
        that.selectStandardFleet(standardFleetId);
        that.refresh();
    });
};

FleetDispatcher.prototype.focusSubmitFleet1 = function () {
    $('#continueToFleet2').focus();
};

FleetDispatcher.prototype.hasShipsSelected = function () {
    return this.getTotalNumberOfShipsSelected() > 0;
};

FleetDispatcher.prototype.hasFreeSlots = function () {
    return this.maxFleetCount - this.fleetCount > 0;
};

FleetDispatcher.prototype.hasEnoughFuel = function () {
    return this.deuteriumOnPlanet >= this.getConsumption();
};

FleetDispatcher.prototype.validateFleet1 = function (onError, onSuccess) {
    if (!this.hasShipsSelected()) {
        this.displayErrors([{
            message: this.loca.LOCA_FLEET_NO_SELECTION
        }]);
        return false;
    }

    if (!this.hasFreeSlots()) {
        this.displayErrors([{
            message: this.loca.LOCA_FLEET_NO_FREE_SLOTS
        }]);
        return false;
    }

    return true;
};

FleetDispatcher.prototype.trySubmitFleet1 = function () {
    if (this.validateFleet1() === false) {
        return;
    }

    this.switchToPage(FLEET_DISPATCH_PAGE2);
};

FleetDispatcher.prototype.refreshFleet1 = function () {
    this.refreshNavigationFleet1();
    this.refreshShips();
    this.refreshAPIData();
    this.refreshStatusBarFleet();
};

FleetDispatcher.prototype.refreshNavigationFleet1 = function () {
    let invalidInfo = '';

    if (!this.hasShipsSelected()) {
        $('#continueToFleet2').attr('class', 'continue off');
        invalidInfo = this.loca.LOCA_FLEET_NO_SELECTION;
    } else if (!this.hasFreeSlots()) {
        $('#continueToFleet2').attr('class', 'continue off');
        invalidInfo = this.loca.LOCA_FLEET_NO_FREE_SLOTS;
    } else {
        $('#continueToFleet2').attr('class', 'continue on');
    }

    $('#allornone .info').html(invalidInfo);
};

FleetDispatcher.prototype.refreshShips = function () {
    let that = this;
    $('#fleet1 #technologies li').each(function (i, elem) {
        const shipId = $(elem).data('technology');
        const ship = that.findShip(shipId);
        const inputElem = $(elem).find('input');
        const oldValue = inputElem.val() === '' ? '' : parseInt(inputElem.val());
        const number = ship?.number ?? '';

        if (oldValue !== number) {
            inputElem.val(number);
            const event = new Event('change');
            inputElem.get(0).dispatchEvent(event);
        }
    });
};

FleetDispatcher.prototype.refreshAPIData = function () {
    let apiShipData = this.shipsToSend.map(function (ship) {
        return [ship.id, ship.number];
    });

    if (apiShipData.length < 1) {
        apiShipData = this.apiShipBaseData;
    }

    let apiDataOld = [].concat(this.apiCommonData).concat(this.apiTechData).concat(apiShipData).concat(this.apiDefenseData).map(function (item) {
        return item.join(';');
    }).join('|');
    let apiData = JSON.parse(JSON.stringify(this.apiDataJson));

    if (this.shipsToSend.length > 0) {
        Object.keys(apiData.ships).forEach(key => apiData.ships[key]['amount'] = 0);
        this.shipsToSend.forEach(ship => {
            apiData.ships[ship.id] = this.apiDataJson.ships[ship.id];
            apiData.ships[ship.id].amount = ship.number;
        });
    }

    let content = JSON.stringify(apiData);
    let tooltip = document.createElement('div');
    tooltip.textContent = this.loca.LOCA_API_FLEET_DATA;
    tooltip.append(document.createElement('br'));
    tooltip.append(document.createTextNode('API 1:'));
    let oldInput = document.createElement('input');
    oldInput.setAttribute('id', 'FLEETAPI');
    oldInput.setAttribute('readonly', '1');
    oldInput.setAttribute('onclick', 'select()');
    oldInput.setAttribute('value', apiDataOld ?? '');
    oldInput.value = apiDataOld ?? '';
    tooltip.appendChild(oldInput);
    tooltip.append(document.createElement('br'));
    tooltip.append(document.createTextNode('API 2:'));
    let jsonInput = document.createElement('input');
    jsonInput.setAttribute('id', 'FLEETAPI_JSON');
    jsonInput.setAttribute('readonly', '1');
    jsonInput.setAttribute('onclick', 'select()');
    jsonInput.setAttribute('value', content);
    jsonInput.value = content;
    tooltip.appendChild(jsonInput);
    changeTooltip($(".show_fleet_apikey"), tooltip.outerHTML);
};
/**
 * SHIP LOGIC
 */


FleetDispatcher.prototype.selectShip = function (shipId, number) {
    let shipsAvailable = this.getNumberOfShipsOnPlanet(shipId);
    number = Math.min(shipsAvailable, number);

    if (number <= 0) {
        this.removeShip(shipId);
    } else if (this.hasShip(shipId)) {
        this.updateShip(shipId, number);
    } else {
        this.addShip(shipId, number);
    }

    this.resetCargo();
};

FleetDispatcher.prototype.addShip = function (shipId, number) {
    this.shipsToSend.push({
        id: shipId,
        number: number
    });
};

FleetDispatcher.prototype.findShip = function (shipId) {
    return this.shipsToSend.find(function (ship) {
        return ship.id === shipId;
    });
};

FleetDispatcher.prototype.getNumberOfShipsSelected = function (shipId) {
    let ship = this.findShip(shipId);

    if (ship !== undefined) {
        return ship.number;
    }

    return 0;
};

FleetDispatcher.prototype.hasShip = function (shipId) {
    return this.findShip(shipId) !== undefined;
};

FleetDispatcher.prototype.hasColonizationShip = function () {
    return this.hasShip(this.SHIP_ID_COLONIZATION);
};

FleetDispatcher.prototype.hasRecycler = function () {
    return this.hasShip(this.SHIP_ID_RECYCLER);
};

FleetDispatcher.prototype.hasValidTarget = function () {
    return (this.targetPlanet.galaxy !== this.currentPlanet.galaxy || this.targetPlanet.system !== this.currentPlanet.system || this.targetPlanet.position !== this.currentPlanet.position || this.targetPlanet.type !== this.currentPlanet.type) && this.targetPlanet.galaxy > 0 && this.targetPlanet.system > 0 && this.targetPlanet.position > 0;
};

FleetDispatcher.prototype.removeShip = function (shipId) {
    let shipIndex = this.shipsToSend.findIndex(function (ship) {
        return ship.id === shipId;
    });

    if (shipIndex != -1) {
        this.shipsToSend.splice(shipIndex, 1);
    }
};

FleetDispatcher.prototype.updateShip = function (shipId, number) {
    let ship = this.findShip(shipId);

    if (ship) {
        ship.number = number;
    }
};

FleetDispatcher.prototype.getNumberOfShipsOnPlanet = function (shipId) {
    let ship = this.shipsOnPlanet.find(function (ship) {
        return ship.id === shipId;
    });

    if (ship) {
        return ship.number;
    }

    return 0;
};

FleetDispatcher.prototype.getTotalNumberOfShipsSelected = function () {
    let numberOfShipsSelected = 0;
    this.shipsToSend.forEach(function (ship) {
        numberOfShipsSelected += ship.number;
    });
    return numberOfShipsSelected;
};

FleetDispatcher.prototype.getShipIds = function () {
    return this.shipsToSend.map(function (ship) {
        return ship.id;
    });
};

FleetDispatcher.prototype.resetShips = function () {
    this.shipsToSend = [];
};

FleetDispatcher.prototype.selectAllShips = function () {
    let that = this;
    this.shipsOnPlanet.forEach(function (ship) {
        that.selectShip(ship.id, ship.number);
    });
};

FleetDispatcher.prototype.selectMaxShips = function (shipId) {
    let number = this.getNumberOfShipsOnPlanet(shipId);
    this.selectShip(shipId, number);
};

FleetDispatcher.prototype.selectShips = function (ships) {
    for (let shipId in ships) {
        let number = ships[shipId];
        this.selectShip(parseInt(shipId), number);
    }
};

FleetDispatcher.prototype.selectStandardFleet = function (standardFleetId) {
    let standardFleet = this.standardFleets.find(function (item) {
        return item.id === standardFleetId;
    });

    if (standardFleet === undefined || standardFleet.ships === undefined) {
        return;
    }

    this.selectShips(standardFleet.ships);
};
/**
 * FLEET 2
 */


FleetDispatcher.prototype.initFleet2 = function () {
    // @todo jquery is loaded twice
    addPercentageBarPlugin();
    initToggleHeader('fleet2'); // reorder fleet-box snippets based on settings

    let reorderFleetBox = Object.fromEntries(Object.entries(this.fleetBoxOrder).sort(([, a], [, b]) => a - b));
    let parent = $("#fleet2 #buttonz");

    for (let fleetBox in reorderFleetBox) {
        let child = $("#fleet2 #buttonz #" + fleetBox);
        $(parent).append(child);
    }

    let that = this;
    let elem = $('#fleet2').off();
    $('#speedPercentage').percentageBar().on('change', function (e) {
        that.setFleetPercent(e.value);
        that.refresh();
    });
    elem.find('#slbox').ogameDropDown();
    elem.find('#aksbox').ogameDropDown();
    elem.on('click', '#backToFleet1', function (e) {
        e.preventDefault();
        that.switchToPage(FLEET_DISPATCH_PAGE1);
    }); // clear inputs on focus

    elem.on('focus', '#galaxy', function () {
        clearInput('#galaxy');
        that.targetPlanet.galaxy = '';
        that.refreshFleet2();
    });
    elem.on('focus', '#system', function () {
        clearInput('#system');
        that.targetPlanet.system = '';
        that.refreshFleet2();
    });
    elem.on('focus', '#position', function () {
        clearInput('#position');
        that.targetPlanet.position = '';
        that.refreshFleet2();
    });
    elem.on('keyup', '#galaxy, #system, #position', function (e) {
        let coordinatesCount = (1 * $("#galaxy").val() > 0 ? 1 : 0) + (1 * $("#system").val() > 0 ? 1 : 0) + (1 * $("#position").val() > 0 ? 1 : 0);
        that.updateTarget(coordinatesCount === 3);
        that.updateTargetDropDowns();
        that.refresh();

        if (coordinatesCount !== 3) {
            that.clearMissions();
            that.updateTargetDropDowns();
        }
    });
    elem.on('click', '#pbutton', function (e) {
        e.preventDefault();
        that.clearMissions();
        that.setTargetType(that.fleetHelper.PLANETTYPE_PLANET);
        that.updateTargetDropDowns();
        that.refresh();
    });
    elem.on('click', '#mbutton', function (e) {
        e.preventDefault();
        that.clearMissions();
        that.setTargetType(that.fleetHelper.PLANETTYPE_MOON);
        that.updateTargetDropDowns();
        that.refresh();
    });
    elem.on('click', '#dbutton', function (e) {
        e.preventDefault();
        that.clearMissions();
        that.setTargetType(that.fleetHelper.PLANETTYPE_DEBRIS);
        that.updateTargetDropDowns();
        that.refresh();
    });
    elem.on('change', '#slbox', function (e) {
        e.preventDefault();
        that.selectShortLink($(e.currentTarget));
        that.updateTarget();
        that.refresh();
    });
    elem.on('change', '#aksbox', function (e) {
        e.preventDefault();
        that.selectCombatUnion($(e.currentTarget));
        that.updateTarget();
        that.refresh();
    });
    elem.on('click', '#selectMaxMetal', function (e) {
        e.preventDefault();
        that.selectMaxMetal();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('click', '#selectMinMetal', function (e) {
        e.preventDefault();
        that.selectMinMetal();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('click', '#selectMaxCrystal', function (e) {
        e.preventDefault();
        that.selectMaxCrystal();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('click', '#selectMinCrystal', function (e) {
        e.preventDefault();
        that.selectMinCrystal();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('click', '#selectMaxDeuterium', function (e) {
        e.preventDefault();
        that.selectMaxDeuterium();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('click', '#selectMinDeuterium', function (e) {
        e.preventDefault();
        that.selectMinDeuterium();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('click', '#selectMaxFood', function (e) {
        e.preventDefault();
        that.selectMaxFood();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('click', '#selectMinFood', function (e) {
        e.preventDefault();
        that.selectMinFood();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('click', '#allresources', function (e) {
        e.preventDefault();
        that.selectMaxAll();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('keyup', '#metal', function (e) {
        that.updateMetal();
        that.refresh();
    });
    elem.on('change', '#metal', function (e) {
        that.updateMetal();
        that.refresh();
    });
    elem.on('keyup', '#crystal', function (e) {
        that.updateCrystal();
        that.refresh();
    });
    elem.on('change', '#crystal', function (e) {
        that.updateCrystal();
        that.refresh();
    });
    elem.on('keyup', '#deuterium', function (e) {
        that.updateDeuterium();
        that.refresh();
    });
    elem.on('change', '#deuterium', function (e) {
        that.updateDeuterium();
        that.refresh();
    });
    elem.on('keyup', '#food', function (e) {
        that.updateFood();
        that.refresh();
    });
    elem.on('change', '#food', function (e) {
        that.updateFood();
        that.refresh();
    });
    elem.on('click', '#sendFleet', async function (e) {
        e.preventDefault();
        await new Promise((resolve, reject) => setTimeout(() => resolve(), 250));
        that.trySubmitFleet2();
    });
    elem.on('click', '#missions > li > a', function (e) {
        e.preventDefault();
        let mission = parseInt($(e.currentTarget).data('mission') || this.fleetHelper.MISSION_NONE);
        that.selectMission(mission);
        that.focusSendFleet();
    });
    elem.on('click', '.prioButton', function (e) {
        e.preventDefault();
        let type = $(e.currentTarget).attr('data-resource-type');
        let priority = parseInt($(e.currentTarget).attr('data-resource-prio'));
        that.selectPriority(type, priority);
        that.refresh();
        that.focusSendFleet();
    });

    FleetDispatcher.prototype.focusSendFleet = function () {
        $('#sendFleet').focus();
    };

    elem.on('change', 'input[name=retreatAfterDefenderRetreat]', function (e) {
        that.selectRetreatAfterDefenderRetreat($(e.currentTarget).is(':checked'));
    });
    elem.on('change', 'input[name=lootFoodOnAttack]', function (e) {
        that.selectLootFoodOnAttack($(e.currentTarget).is(':checked'));
    });
    elem.on('change keyup', '#holdingtime', function () {
        that.updateHoldingTime();
        that.refresh();
        that.focusSendFleet();
    });
    elem.on('change keyup', '#expeditiontime', function (e) {
        that.updateExpeditionTime();
        that.refresh();
        that.focusSendFleet();
    });
    this.fetchTargetPlayerData();
};

FleetDispatcher.prototype.validateFleet2 = function () {
    if (!this.hasValidTarget() || !this.hasMission()) {
        return false;
    }

    return true;
};

FleetDispatcher.prototype.trySubmitFleet2 = function () {
    clearTimeout(this.fetchTargetPlayerDataTimeout);
    this.fetchTargetPlayerDataTimeout = null;
    let that = this; // call refreshNavigationFleet2 to show error messages if any

    this.refreshNavigationFleet2(true);

    if ($("#sendFleet.off").length === 1) {
        return;
    }

    if (this.validateFleet2() === false) {
        return;
    }

    if (this.moveInProgress) {
        errorBoxDecision(this.loca.LOCA_ALL_NETWORK_ATTENTION, this.loca.LOCA_PLANETMOVE_BREAKUP_WARNING, this.loca.LOCA_ALL_YES, this.loca.LOCA_ALL_NO, function () {
            that.submitFleet2();
        });
    } else if (this.warningsEnabled && this.targetIsStrong && !this.targetIsOutlaw && !this.targetIsBuddyOrAllyMember && !this.playerIsOutlaw && this.fleetHelper.isAggressiveMission(this.mission)) {
        errorBoxDecision(this.loca.LOCA_ALL_NETWORK_ATTENTION, this.locadyn.locaAllOutlawWarning, this.loca.LOCA_ALL_YES, this.loca.LOCA_ALL_NO, function () {
            that.submitFleet2();
        });
    } else if (this.mission === this.fleetHelper.MISSION_COLONIZE && this.fleetHelper.COLONIZATION_ENABLED === true && !this.hasFreePlanetSlots()) {
        errorBoxDecision(this.loca.LOCA_ALL_NOTICE, this.loca.LOCA_FLEETSENDING_MAX_PLANET_WARNING, this.loca.LOCA_ALL_YES, this.loca.LOCA_ALL_NO, function () {
            that.submitFleet2();
        });
    } else {
        this.submitFleet2();
    }
};

FleetDispatcher.prototype.refreshFleet2 = function () {
    this.refreshNavigationFleet2();
    this.refreshTarget();
    this.refreshBriefing();
    this.refreshCargo();
    this.refreshPriorities();
};

FleetDispatcher.prototype.refreshTarget = function () {
    if (this.targetPlanet.type === this.fleetHelper.PLANETTYPE_PLANET) {
        $('#pbutton').attr('class', 'planet_selected');
    } else {
        $('#pbutton').attr('class', 'planet');
    }

    if (this.targetPlanet.type === this.fleetHelper.PLANETTYPE_DEBRIS) {
        $('#dbutton').attr('class', 'debris_selected');
    } else {
        $('#dbutton').attr('class', 'debris');
    }

    if (this.targetPlanet.type === this.fleetHelper.PLANETTYPE_MOON) {
        $('#mbutton').attr('class', 'moon_selected');
    } else {
        $('#mbutton').attr('class', 'moon');
    }

    $('#galaxy').val(this.targetPlanet.galaxy);
    $('#system').val(this.targetPlanet.system);
    $('#position').val(this.targetPlanet.position);
    $('#type').val(this.targetPlanet.type);
    $('#distanceValue').html(tsdpkt(this.getDistance()));
    let planetName = this.getOwnPlanetName(this.targetPlanet, this.targetPlanet.type);

    if (planetName !== undefined && planetName !== '') {
        $('#targetPlanetName').html(planetName);
    } else if (this.targetPlanet.type === this.fleetHelper.PLANETTYPE_PLANET) {
        $('#targetPlanetName').html(this.loca.LOCA_ALL_PLANET);
    } else if (this.targetPlanet.type === this.fleetHelper.PLANETTYPE_DEBRIS) {
        $('#targetPlanetName').html(this.loca.LOCA_FLEET_DEBRIS);
    } else if (this.targetPlanet.type === this.fleetHelper.PLANETTYPE_MOON) {
        $('#targetPlanetName').html(this.loca.LOCA_ALL_MOON);
    } // After we've chosen another Planet update the Missions


    this.refreshMissions();
};

FleetDispatcher.prototype.refreshBriefing = function () {
    this.refreshDuration();
    this.refreshConsumption();
    this.refreshStorage();
    this.refreshFleetTimes();
    this.refreshMaxSpeed();
    this.refreshEmptySystems();
    this.refreshInactiveSystems();
};

FleetDispatcher.prototype.refreshTargetPlanet = function () {
    let targetName = '[' + this.targetPlanet.galaxy + ':' + this.targetPlanet.system + ':' + this.targetPlanet.position + '] ' + (this.targetPlanet.type === this.fleetHelper.PLANETTYPE_DEBRIS ? this.loca.LOCA_FLEET_DEBRIS : this.targetPlanet.name);
    let elem = $('#fleet2 #targetPlanet');
    let tooltip = this.targetInhabited === true ? this.loca.LOCA_ALL_PLAYER + ': ' + this.targetPlayerName : '';
    elem.toggleClass('tooltip', this.targetInhabited).toggleClass('active', this.targetInhabited).attr('title', tooltip).html(targetName);
    changeTooltip(elem);
};

FleetDispatcher.prototype.refreshDuration = function () {
    let duration = this.getDuration();
    duration = !isNaN(duration) && isFinite(duration) ? duration : 0;
    $('#fleet2 #duration').html(formatTime(duration) + ' h');
};

FleetDispatcher.prototype.refreshConsumption = function () {
    let fuelCapacity = this.getFuelCapacity();
    let deuterium = getResourcesFromHeader('deuterium');
    let consumption = this.getConsumption();
    consumption = !isNaN(consumption) && isFinite(consumption) ? consumption : 0;
    let styleClass = consumption > fuelCapacity || consumption > deuterium ? 'overmark' : 'undermark';
    let fuelLevel = Math.ceil(100 * consumption / fuelCapacity);
    let htmlConsumption = '<span class="' + styleClass + '">' + tsdpkt(consumption) + ' (' + fuelLevel + '%)</span>';
    $('#fleet2 #consumption').html(htmlConsumption);
};

FleetDispatcher.prototype.refreshStorage = function () {
    let cargoSpace = this.getFreeCargoSpace();
    let styleClass = cargoSpace < 0 ? 'overmark' : 'undermark';
    let htmlStorage = '<span class="' + styleClass + '">' + tsdpkt(cargoSpace) + '</span>';
    $('#storage').html(htmlStorage);
};

FleetDispatcher.prototype.refreshFleetTimes = function () {
    let duration = this.getDuration();
    let holdingTime = 0;

    if (this.mission === this.fleetHelper.MISSION_EXPEDITION) {
        holdingTime = this.expeditionTime * 3600;
    }

    if (this.mission === this.fleetHelper.MISSION_HOLD) {
        holdingTime = this.holdingTime * 3600;
    }

    duration = !isNaN(duration) && isFinite(duration) ? duration : 0;
    holdingTime = !isNaN(holdingTime) && isFinite(holdingTime) ? holdingTime : 0;
    let arrivalTime = getFormatedDate(serverTime.getTime() + duration * 1000, '[d].[m].[y] [G]:[i]:[s]');
    let returnTime = getFormatedDate(serverTime.getTime() + (2 * duration + holdingTime) * 1000, '[d].[m].[y] [G]:[i]:[s]');
    $('#fleet2 #arrivalTime').html(arrivalTime);
    $('#fleet2 #returnTime').html(returnTime);

    if (this.mission === this.fleetHelper.MISSION_UNIONATTACK) {
        let union = this.getUnionData(this.union);

        if (union !== null) {
            let durationAKS = parseInt(union.time - serverTime.getTime() / 1000);
            let unionArrivalTime = formatTime(durationAKS);
            $('#durationAKS').html(unionArrivalTime);
        }
    }
};

FleetDispatcher.prototype.refreshMaxSpeed = function () {
    let maxSpeed = this.getMaxSpeed();
    $('#maxspeed').html(tsdpkt(maxSpeed));
};

FleetDispatcher.prototype.refreshEmptySystems = function () {
    $('#emptySystems').html(this.emptySystems);
};

FleetDispatcher.prototype.refreshInactiveSystems = function () {
    $('#inactiveSystems').html(this.inactiveSystems);
};

FleetDispatcher.prototype.getPlanetIcon = function (planetType, showTooltip) {
    showTooltip = showTooltip || true;
    let className = '';
    let name = '';

    switch (planetType) {
        case this.fleetHelper.PLANETTYPE_MOON:
            className = "moon";
            name = this.loca.LOCA_ALL_MOON;
            break;

        case this.fleetHelper.PLANETTYPE_DEBRIS:
            className = "tf";
            name = this.loca.LOCA_FLEET_DEBRIS;
            break;

        case this.fleetHelper.PLANETTYPE_PLANET:
        default:
            className = "planet";
            name = this.loca.LOCA_ALL_PLANET;
    }

    let title = '';

    if (showTooltip) {
        className += " tooltip js_hideTipOnMobile";
        title = ' title="' + name + '"';
    }

    return '<figure class="planetIcon ' + className + '"' + title + '></figure>';
};

FleetDispatcher.prototype.updateTarget = function (fetch = true) {
    let galaxy = clampInt(getValue($('#galaxy').val()), 1, this.fleetHelper.MAX_GALAXY, true);
    let system = clampInt(getValue($('#system').val()), 1, this.fleetHelper.MAX_SYSTEM, true);
    let position = clampInt(getValue($('#position').val()), 1, this.fleetHelper.MAX_POSITION, true);
    this.targetPlanet.galaxy = galaxy;
    this.targetPlanet.system = system;
    this.targetPlanet.position = position;

    if (this.targetPlanet.position === this.fleetHelper.EXPEDITION_POSITION) {
        this.targetPlanet.type = this.fleetHelper.PLANETTYPE_PLANET;
    }

    clearTimeout(this.fetchTargetPlayerDataTimeout);

    if (fetch && $('#fleet2').is(':visible')) {
        let that = this;
        this.fetchTargetPlayerDataTimeout = setTimeout(() => {
            that.deferred.push($.Deferred()); // check if this is the only target fetch in queue or there are other pending calls

            if (that.deferred.length === 1) {
                that.fetchTargetPlayerData();
            }

            that.deferred[that.deferred.length - 1].done(() => {
                if (that.deferred.length !== 0) {
                    that.fetchTargetPlayerData();
                }
            });
        }, 500);
    }
};

FleetDispatcher.prototype.updateTargetDropDowns = function () {
    this.resetDropDown("#slbox");
    this.resetDropDown("#aksbox");
};

FleetDispatcher.prototype.resetDropDown = function (elementId) {
    let coords = [this.targetPlanet.galaxy, this.targetPlanet.system, this.targetPlanet.position, this.targetPlanet.type].join('#');
    let selection = $(elementId).find("option[value^=\"" + coords + "\"]");

    if (selection.length === 0) {
        $(elementId).ogameDropDown('select', '-');
    }
};

FleetDispatcher.prototype.selectShortLink = function (elem) {
    let value = elem.val();
    let parts = value.split('#');

    if (parts instanceof Array && parts.length >= 5) {
        $('#galaxy').val(parts[0]);
        $('#system').val(parts[1]);
        $('#position').val(parts[2]);
        this.setTargetType(parseInt(parts[3]), true);
    }
};

FleetDispatcher.prototype.setTargetType = function (type, doNotFetchDataAboutTarget) {
    this.targetPlanet.type = type;

    if (!doNotFetchDataAboutTarget && $('#fleet2').is(':visible')) {
        clearTimeout(this.fetchTargetPlayerDataTimeout);
        this.fetchTargetPlayerDataTimeout = null;
        this.fetchTargetPlayerData();
    }
};

FleetDispatcher.prototype.selectCombatUnion = function (elem) {
    let value = elem.val();
    let parts = value.split('#');

    if (parts instanceof Array && parts.length >= 5) {
        $('#galaxy').val(parts[0]);
        $('#system').val(parts[1]);
        $('#position').val(parts[2]);
        this.setTargetType(parseInt(parts[3]), true);
        this.union = parseInt(parts[5]);
    } else {
        this.mission = this.fleetHelper.MISSION_NONE;
        this.union = 0;
    }
};

FleetDispatcher.prototype.setFleetPercent = function (speedPercent) {
    this.speedPercent = speedPercent;
};

FleetDispatcher.prototype.findOwnPlanet = function (coords, type) {
    if (!coords) return undefined;
    if (!type) return undefined;
    let planet = this.planets.find(function (elem) {
        if (elem.galaxy != coords.galaxy) return false;
        if (elem.system != coords.system) return false;
        if (elem.position != coords.position) return false;
        if (elem.type != type) return false;
        return true;
    });
    return planet;
};

FleetDispatcher.prototype.getOwnPlanetName = function (coords, type) {
    if (!coords) return undefined;
    if (!type) return undefined;
    let planet = this.findOwnPlanet(coords, type);

    if (planet) {
        return planet.name;
    }

    return undefined;
};

FleetDispatcher.prototype.getDistance = function () {
    return this.fleetHelper.calcDistance(this.currentPlanet, this.targetPlanet, this.emptySystems, this.inactiveSystems);
};

FleetDispatcher.prototype.getConsumption = function () {
    return this.fleetHelper.calcConsumption(this.shipsToSend, this.getDistance(), this.speedPercent, this.getHoldingTime(), this.mission);
};

FleetDispatcher.prototype.getDuration = function () {
    let distance = this.getDistance();
    let maxSpeed = this.getMaxSpeed();
    return this.fleetHelper.calcDuration(distance, maxSpeed, this.speedPercent, this.mission);
};

FleetDispatcher.prototype.getHoldingTime = function () {
    switch (this.mission) {
        case this.fleetHelper.MISSION_EXPEDITION:
            return this.expeditionTime;

        case this.fleetHelper.MISSION_HOLD:
            return this.holdingTime;

        default:
            return 0;
    }
};

FleetDispatcher.prototype.getMaxSpeed = function () {
    let shipIds = this.getShipIds();
    return this.fleetHelper.getMaxSpeed(shipIds);
};

FleetDispatcher.prototype.getCargoCapacity = function () {
    let that = this;
    let cargoCapacity = 0;
    this.shipsToSend.forEach(function (ship) {
        cargoCapacity += that.fleetHelper.calcCargoCapacity(ship.id, ship.number);
    });
    return Math.floor(cargoCapacity);
};

FleetDispatcher.prototype.getFuelCapacity = function () {
    let that = this;
    let fuelCapacity = 0;
    this.shipsToSend.forEach(function (ship) {
        fuelCapacity += that.fleetHelper.calcFuelCapacity(ship.id, ship.number);
    });
    return Math.floor(fuelCapacity);
};

FleetDispatcher.prototype.getFreeCargoSpace = function () {
    return this.getCargoCapacity() - this.cargoMetal - this.cargoCrystal - this.cargoDeuterium - this.cargoFood;
};

FleetDispatcher.prototype.getUsedCargoSpace = function () {
    return this.cargoMetal + this.cargoCrystal + this.cargoDeuterium + this.cargoFood;
};

FleetDispatcher.prototype.setOrders = function (ordersNew) {
    this.orders = ordersNew;
};

FleetDispatcher.prototype.setTargetInhabited = function (inhabitedNew) {
    this.targetInhabited = inhabitedNew;
};

FleetDispatcher.prototype.setTargetPlayerId = function (targetPlayerIdNew) {
    this.targetPlayerId = targetPlayerIdNew;
};

FleetDispatcher.prototype.setTargetPlayerName = function (targetPlayerNameNew) {
    this.targetPlayerName = targetPlayerNameNew;
};

FleetDispatcher.prototype.setTargetIsStrong = function (targetIsStrongNew) {
    this.targetIsStrong = targetIsStrongNew;
};

FleetDispatcher.prototype.setTargetIsOutlaw = function (targetIsOutlawNew) {
    this.targetIsOutlaw = targetIsOutlawNew;
};

FleetDispatcher.prototype.setTargetIsBuddyOrAllyMember = function (targetIsBuddyOrAllyMemberNew) {
    this.targetIsBuddyOrAllyMember = targetIsBuddyOrAllyMemberNew;
};

FleetDispatcher.prototype.setPlayerIsOutlaw = function (playerIsOutlawNew) {
    this.playerIsOutlaw = playerIsOutlawNew;
};

FleetDispatcher.prototype.setTargetPlayerColorClass = function (targetPlayerColorClassNew) {
    this.targetPlayerColorClass = targetPlayerColorClassNew;
};

FleetDispatcher.prototype.setTargetPlayerRankIcon = function (targetPlayerRankIconNew) {
    this.targetPlayerRankIcon = targetPlayerRankIconNew;
};

FleetDispatcher.prototype.setTargetPlanet = function (targetPlanetNew) {
    this.targetPlanet = targetPlanetNew;
};

FleetDispatcher.prototype.fetchTargetPlayerData = function () {
    if (!this.fetchTargetPlayerDataTimeout) {
        this.fetchTargetPlayerDataTimeout = true;
    }

    let that = this; // Prevent spaming the Planets (Destination)

    this.startLoading();
    let params = {};
    this.appendShipParams(params);
    this.appendTargetParams(params);
    this.appendTokenParams(params);
    params.union = this.union;

    if (this.hasColonizationShip()) {
        params.cs = 1;
    }

    if (this.hasRecycler()) {
        params.recycler = 1;
    }

    $.post(this.checkTargetUrl, params, function (response) {
        let data = JSON.parse(response);
        let status = data.status || 'failure';
        $("#additionalFleetSpeedInfo").html(data.additionalFlightSpeedinfo);
        that.fleetHelper.shipsData = data.shipsData;

        if (status === 'success') {
            let {
                targetPlanet
            } = data;

            if (targetPlanet && parseInt($('#galaxy').val()) === targetPlanet.galaxy && parseInt($('#system').val()) === targetPlanet.system && parseInt($('#position').val()) === targetPlanet.position) {
                that.refreshDataAfterAjax(data);
                that.refreshStatusBarFleet();
            }
        } else {
            that.setTargetPlayerNameOnStatusBarFleet();
            that.setTargetPlayerPlanetNameOnStatusBarFleet();

            if (that.currentPage === "fleet2") {
                that.clearMissions();
                that.updateTargetDropDowns();
                that.displayErrors(data.errors);
            }
        }

        if (that.currentPage === "fleet2") {
            if (that.mission !== that.fleetHelper.MISSION_NONE && !that.isMissionAvailable(that.mission)) {
                that.mission = that.fleetHelper.MISSION_NONE;
            } //select ACS attack if no mission is selected and if union is selected


            if (that.union !== 0 && that.hasMission() === false && that.isMissionAvailable(that.fleetHelper.MISSION_UNIONATTACK)) {
                that.selectMission(that.fleetHelper.MISSION_UNIONATTACK);
            }

            that.validateMissions();
        }

        that.updateToken(data.newAjaxToken);
        that.updateEmptySystems(data.emptySystems);
        that.updateInactiveSystems(data.inactiveSystems);
        that.refreshTargetPlanet();
        that.refreshFleet2();
        that.fetchTargetPlayerDataTimeout = null;
        that.stopLoading();

        if (data.bashingSystemLimitReached) {
            $('#attackMissionsDisabledBashingLimit').show();
        } else {
            $('#attackMissionsDisabledBashingLimit').hide();
        }

        let deferred = that.deferred.shift();

        if (deferred) {
            deferred.resolve();
        }
    });
};

FleetDispatcher.prototype.setTargetPlayerNameOnStatusBarFleet = function (serverResponse) {
    let elemTargetPlayerName = $('#statusBarFleet .targetPlayerName');
    let targetPlayerName = '';

    if (!serverResponse) {
        targetPlayerName = this.loca.LOCA_EVENTH_ENEMY_INFINITELY_SPACE;
    } else {
        if (serverResponse.targetPlanet && serverResponse.targetPlanet.type === this.fleetHelper.PLANETTYPE_DEBRIS) {
            targetPlayerName = this.loca.LOCA_EVENTH_ENEMY_INFINITELY_SPACE;
        } else {
            if (!serverResponse.targetPlayerName) {
                targetPlayerName = this.loca.LOCA_EVENTH_ENEMY_INFINITELY_SPACE;
            } else if (serverResponse.targetPlayerId === this.playerId) {
                targetPlayerName = serverResponse.targetPlayerName;
            } else {
                targetPlayerName = serverResponse.targetPlayerRankIcon + '<span class="status_abbr_{color}">{name}</span>';
                targetPlayerName = targetPlayerName.replace('{color}', serverResponse.targetPlayerColorClass);
                targetPlayerName = targetPlayerName.replace('{name}', serverResponse.targetPlayerName);
            }
        }
    }

    elemTargetPlayerName.closest('li').show();
    elemTargetPlayerName.html(targetPlayerName);
};

FleetDispatcher.prototype.setTargetPlayerPlanetNameOnStatusBarFleet = function (serverResponse) {
    let targetName;

    if (!serverResponse || serverResponse && !serverResponse.targetPlanet) {
        let planetIcon = this.getPlanetIcon(this.fleetHelper.PLANETTYPE_PLANET, false);
        targetName = `[${$('#galaxy').val()}:${$('#system').val()}:${$('#position').val()}] ${planetIcon} ?`;
    } else {
        let planetIcon = this.getPlanetIcon(serverResponse.targetPlanet.type, false);
        targetName = '[' + serverResponse.targetPlanet.galaxy + ':' + serverResponse.targetPlanet.system + ':' + serverResponse.targetPlanet.position + '] ' + planetIcon + serverResponse.targetPlanet.name;
    }

    $('#statusBarFleet .targetName').html(targetName);
};

FleetDispatcher.prototype.submitFleet2 = function (force) {
    force = force || false;
    let that = this;
    let params = {};
    this.appendTokenParams(params);
    this.appendShipParams(params);
    this.appendTargetParams(params);
    this.appendCargoParams(params);
    this.appendPrioParams(params);
    params.mission = this.mission;
    params.speed = this.speedPercent;
    params.retreatAfterDefenderRetreat = this.retreatAfterDefenderRetreat === true ? 1 : 0;
    params.lootFoodOnAttack = this.lootFoodOnAttack === true ? 1 : 0;
    params.union = this.union;
    if (force) params.force = force;
    params.holdingtime = this.getHoldingTime();
    this.startLoading();
    $.post(this.sendFleetUrl, params, function (response) {
        let data = JSON.parse(response); // request successful

        if (data.success === true) {
            fadeBox(data.message, false);
            $("#sendFleet").removeAttr("disabled");
            window.location = data.redirectUrl;
        } // request failed
        else {
            // @TODO display confirmation popup to infringe bashlimit rules
            if (data.responseArray && data.responseArray.limitReached && !data.responseArray.force) {
                that.updateToken(data.newAjaxToken || '');
                errorBoxDecision(that.loca.LOCA_ALL_NETWORK_ATTENTION, that.locadyn.localBashWarning, that.loca.LOCA_ALL_YES, that.loca.LOCA_ALL_NO, function () {
                    that.submitFleet2(true);
                });
            } else {
                that.displayErrors(data.errors);
                that.updateToken(data.newAjaxToken || '');
                $("#sendFleet").removeAttr("disabled");
                that.stopLoading();
            }
        }
    });
};

FleetDispatcher.prototype.refreshNavigationFleet2 = function (displayErrors = false) {
    let invalidInfo = null;
    const sendFleetElement = document.getElementById('sendFleet');

    if (!this.hasShipsSelected()) {
        sendFleetElement.classList.add('off');
        sendFleetElement.classList.remove('on');
        invalidInfo = this.loca.LOCA_FLEET_NO_SELECTION;
    } else if (!this.hasMission()) {
        sendFleetElement.classList.add('off');
        sendFleetElement.classList.remove('on');
        invalidInfo = this.loca.LOCA_FLEETSENDING_NO_MISSION_SELECTED;
    } else if (!this.hasFreeSlots()) {
        sendFleetElement.classList.add('off');
        sendFleetElement.classList.remove('on');
        invalidInfo = this.loca.LOCA_FLEET_NO_FREE_SLOTS;
    } else if (!this.hasEnoughFuel()) {
        sendFleetElement.classList.add('off');
        sendFleetElement.classList.remove('on');
        invalidInfo = this.loca.LOCA_FLEETSENDING_NOT_ENOUGH_FOIL;
    } else {
        sendFleetElement.classList.add('on');
        sendFleetElement.classList.remove('off');
        sendFleetElement.removeAttribute('disabled');
    }

    if (displayErrors && invalidInfo != null) {
        this.displayErrors([{
            message: invalidInfo
        }]);
    }
};

FleetDispatcher.prototype.refreshStatusBarFleet = function () {
    this.hasValidTarget();
    let missionData = this.getMissionData(this.mission);
    let missionName = missionData !== null ? missionData.name : this.loca.LOCA_FLEET_NO_SELECTION;
    let planetIcon = this.getPlanetIcon(this.targetPlanet.type, false);
    let targetName = '[' + this.targetPlanet.galaxy + ':' + this.targetPlanet.system + ':' + this.targetPlanet.position + '] ' + planetIcon + (this.targetPlanet.type === this.fleetHelper.PLANETTYPE_DEBRIS ? this.loca.LOCA_FLEET_DEBRIS : this.targetPlanet.name);
    $('#statusBarFleet .missionName').text(missionName);
    $('#statusBarFleet .targetName').html(targetName);
    let elemTargetPlayerName = $('#statusBarFleet .targetPlayerName');

    if (this.targetPlanet.type === this.fleetHelper.PLANETTYPE_DEBRIS) {
        elemTargetPlayerName.closest('li').hide();
        elemTargetPlayerName.html('');
    } else {
        let targetPlayerName = '';

        if (this.targetPlayerId === 0 || this.fleetHelper.isPlayerSpace(this.targetPlayerId)) {
            targetPlayerName = this.loca.LOCA_EVENTH_ENEMY_INFINITELY_SPACE;
        } else if (this.targetPlayerId === this.playerId) {
            targetPlayerName = this.targetPlayerName;
        } else {
            targetPlayerName = this.targetPlayerRankIcon + '<span class="status_abbr_{color}">{name}</span>';
            targetPlayerName = targetPlayerName.replace('{color}', this.targetPlayerColorClass);
            targetPlayerName = targetPlayerName.replace('{name}', this.targetPlayerName);
        }

        elemTargetPlayerName.closest('li').show();
        elemTargetPlayerName.html(targetPlayerName);
    }
};

FleetDispatcher.prototype.clearMissions = function () {
    this.mission = this.fleetHelper.MISSION_NONE;

    for (let order in this.orders) {
        this.orders[order] = false;
    }
};

FleetDispatcher.prototype.validateMissions = function () {
    let invalid = false;

    if (!this.hasShipsSelected()) {
        invalid = true;
    } else if (!this.hasFreeSlots()) {
        invalid = true;
    } else if (!this.hasValidTarget() || Object.values(this.orders).indexOf(true) === -1) {
        invalid = true;
    }

    if (invalid) {
        this.clearMissions();
    }
};

FleetDispatcher.prototype.refreshMissions = function () {
    $('#missions>li>a.selected').removeClass('selected'); //select expedition if no mission is selected and if it is the only one available

    if (this.isOnlyMissionAvailable(this.fleetHelper.MISSION_EXPEDITION)) {
        if (this.hasMission() === false) {
            this.selectMission(this.fleetHelper.MISSION_EXPEDITION);
        }

        this.updateExpeditionTime();
    } // refresh mission buttons


    for (let mission in this.orders) {
        let missionData = this.getMissionData(mission);
        $('#missions>li#button' + mission).toggleClass('on', missionData.isAvailable === true).toggleClass('off', missionData.isAvailable === false);
    } // @TODO LOCA_FLEET_NO_SELECTION when no mission selected
    // refresh mission


    let missionData = this.getMissionData(this.mission);

    if (missionData !== null) {
        $('.missionName').text(missionData.name);
        $('.mission_description').text(missionData.description);
        $('#missions>li#button' + this.mission + '>a').toggleClass('selected', true);

        if (missionData.isAvailable === false) {
            $('.briefing_overlay').show();
            $('#missionNameWrapper').addClass('off');
        } else {
            $('.briefing_overlay').hide();
            $('#missionNameWrapper').removeClass('off');
        }
    } else {
        $('.briefing_overlay').show();
        $('#missionNameWrapper').addClass('off');
        $('.missionName').text(this.loca.LOCA_FLEET_NO_SELECTION);
        $('.mission_description').text('');
    }

    $('form input[name="mission"]').val(this.mission);
    $('#fightAfterRetreat,' + '#aks,' + '#holdtimeline,' + '#expeditiontimeline,' + '.prioButton,' + '.fleet_dispatch_toggle_wrap').hide();

    if (this.mission === this.fleetHelper.MISSION_ATTACK) {
        $('#fightAfterRetreat').show();
        $('.prioButton').show();
        $('.fleet_dispatch_toggle_wrap').show();
    }

    if (this.mission === this.fleetHelper.MISSION_UNIONATTACK) {
        $('#aks').show();
        $('.prioButton').show();
        $('.fleet_dispatch_toggle_wrap').show();
    }

    if (this.mission === this.fleetHelper.MISSION_DESTROY) {
        $('.prioButton').show();
        $('.fleet_dispatch_toggle_wrap').show();
    }

    if (this.mission === this.fleetHelper.MISSION_HOLD) {
        $('#holdtimeline').show();
    }

    if (this.mission === this.fleetHelper.MISSION_EXPEDITION) {
        $('#expeditiontimeline').show();
    }
};

FleetDispatcher.prototype.refreshCargo = function () {
    formatNumber($('#metal'), this.cargoMetal);
    formatNumber($('#crystal'), this.cargoCrystal);
    formatNumber($('#deuterium'), this.cargoDeuterium);

    if (this.lifeformEnabled) {
        formatNumber($('#food'), this.cargoFood);
    }

    let cargoSpaceUsed = this.getUsedCargoSpace();
    let cargoSpaceFree = this.getFreeCargoSpace();
    let cargoCapacity = this.getCargoCapacity();
    let styleClass = cargoSpaceFree < 0 ? 'overmark' : 'undermark';
    $('#remainingresources').html('<span class="' + styleClass + '">' + tsdpkt(cargoSpaceFree) + '</style>');
    $('#maxresources').html(tsdpkt(cargoCapacity));
    $('#loadRoom .bar_container').data('currentAmount', cargoSpaceUsed).data('capacity', cargoCapacity);
    refreshBars('bar_container', 'filllevel_bar');
};

FleetDispatcher.prototype.refreshPriorities = function () {
    $('form input[name="prioMetal"]').val(this.prioMetal);
    $('form input[name="prioCrystal"]').val(this.prioCrystal);
    $('form input[name="prioDeuterium"]').val(this.prioDeuterium);

    if (this.lifeformEnabled) {
        $('form input[name="prioFood"]').val(this.prioFood);
    }

    $('#prioM1').attr('src', '/img/icons/4b53e83f8b8583ea279fd26f3a55a5.gif');
    $('#prioM2').attr('src', '/img/icons/8afbd59ffe091239a7c6f1e961b267.gif');
    $('#prioM3').attr('src', '/img/icons/4acc67e1ca4d8debb1b114abcb7c1e.gif');

    if (this.lifeformEnabled) {
        $('#prioM4').attr('src', '/img/icons/8860dee24c03537549ad782922b6b5.gif');
    }

    switch (this.prioMetal) {
        case 1:
            $('#prioM1').attr('src', '/img/icons/b357323b99e20a46fc0b2495728351.gif');
            break;

        case 2:
            $('#prioM2').attr('src', '/img/icons/f8959fe540cd329f3a764ad9aeaf93.gif');
            break;

        case 3:
            $('#prioM3').attr('src', '/img/icons/823b3270ed0f4a243287c12d4ee5f8.gif');
            break;

        case 4:
            $('#prioM4').attr('src', '/img/icons/43bf98a73ba1abb53860f2c5b8edc3.gif');
            break;
    }

    $('#prioC1').attr('src', '/img/icons/4b53e83f8b8583ea279fd26f3a55a5.gif');
    $('#prioC2').attr('src', '/img/icons/8afbd59ffe091239a7c6f1e961b267.gif');
    $('#prioC3').attr('src', '/img/icons/4acc67e1ca4d8debb1b114abcb7c1e.gif');

    if (this.lifeformEnabled) {
        $('#prioC4').attr('src', '/img/icons/8860dee24c03537549ad782922b6b5.gif');
    }

    switch (this.prioCrystal) {
        case 1:
            $('#prioC1').attr('src', '/img/icons/b357323b99e20a46fc0b2495728351.gif');
            break;

        case 2:
            $('#prioC2').attr('src', '/img/icons/f8959fe540cd329f3a764ad9aeaf93.gif');
            break;

        case 3:
            $('#prioC3').attr('src', '/img/icons/823b3270ed0f4a243287c12d4ee5f8.gif');
            break;

        case 4:
            $('#prioC4').attr('src', '/img/icons/43bf98a73ba1abb53860f2c5b8edc3.gif');
            break;
    }

    $('#prioD1').attr('src', '/img/icons/4b53e83f8b8583ea279fd26f3a55a5.gif');
    $('#prioD2').attr('src', '/img/icons/8afbd59ffe091239a7c6f1e961b267.gif');
    $('#prioD3').attr('src', '/img/icons/4acc67e1ca4d8debb1b114abcb7c1e.gif');

    if (this.lifeformEnabled) {
        $('#prioD4').attr('src', '/img/icons/8860dee24c03537549ad782922b6b5.gif');
    }

    switch (this.prioDeuterium) {
        case 1:
            $('#prioD1').attr('src', '/img/icons/b357323b99e20a46fc0b2495728351.gif');
            break;

        case 2:
            $('#prioD2').attr('src', '/img/icons/f8959fe540cd329f3a764ad9aeaf93.gif');
            break;

        case 3:
            $('#prioD3').attr('src', '/img/icons/823b3270ed0f4a243287c12d4ee5f8.gif');
            break;

        case 4:
            $('#prioD4').attr('src', '/img/icons/43bf98a73ba1abb53860f2c5b8edc3.gif');
            break;
    }

    if (this.lifeformEnabled) {
        $('#prioF1').attr('src', '/img/icons/4b53e83f8b8583ea279fd26f3a55a5.gif');
        $('#prioF2').attr('src', '/img/icons/8afbd59ffe091239a7c6f1e961b267.gif');
        $('#prioF3').attr('src', '/img/icons/4acc67e1ca4d8debb1b114abcb7c1e.gif');
        $('#prioF4').attr('src', '/img/icons/8860dee24c03537549ad782922b6b5.gif');

        switch (this.prioFood) {
            case 1:
                $('#prioF1').attr('src', '/img/icons/b357323b99e20a46fc0b2495728351.gif');
                break;

            case 2:
                $('#prioF2').attr('src', '/img/icons/f8959fe540cd329f3a764ad9aeaf93.gif');
                break;

            case 3:
                $('#prioF3').attr('src', '/img/icons/823b3270ed0f4a243287c12d4ee5f8.gif');
                break;

            case 4:
                $('#prioF4').attr('src', '/img/icons/43bf98a73ba1abb53860f2c5b8edc3.gif');
                break;
        }
    }
};

FleetDispatcher.prototype.isMissionAvailable = function (missionId) {
    return this.orders[missionId] === true;
};

FleetDispatcher.prototype.hasMission = function () {
    return this.fleetHelper.isMissionValid(this.mission) && this.isMissionAvailable(this.mission);
};

FleetDispatcher.prototype.hasFreePlanetSlots = function () {
    return this.planetCount < this.fleetHelper.MAX_NUMBER_OF_PLANETS;
};

FleetDispatcher.prototype.getAvailableMissions = function () {
    let missions = [];

    for (let mission in this.orders) {
        if (this.orders[mission] === true) {
            missions.push(parseInt(mission));
        }
    }

    return missions;
};

FleetDispatcher.prototype.isOnlyMissionAvailable = function (missionId) {
    let missionsAvailable = this.getAvailableMissions();
    return missionsAvailable.length === 1 && missionsAvailable[0] === missionId;
};

FleetDispatcher.prototype.getMissionData = function (missionId) {
    if (missionId === this.fleetHelper.MISSION_NONE) {
        return null;
    }

    return {
        isAvailable: this.orders[missionId] || false,
        name: this.orderNames[missionId] || '',
        description: this.orderDescriptions[missionId] || ''
    };
};

FleetDispatcher.prototype.getUnionData = function (unionId) {
    for (let i = 0; i < this.unions.length; ++i) {
        if (this.unions[i].id === unionId) {
            return this.unions[i];
        }
    }

    return null;
};

FleetDispatcher.prototype.selectMaxMetal = function () {
    let amount;
    amount = this.getCargoCapacity() - this.cargoCrystal - this.cargoDeuterium - this.cargoFood;
    amount = Math.max(amount, 0);
    amount = Math.min(amount, this.metalOnPlanet);
    this.cargoMetal = Math.max(this.cargoMetal, amount);
};

FleetDispatcher.prototype.selectMinMetal = function () {
    this.cargoMetal = 0;
};

FleetDispatcher.prototype.selectMaxCrystal = function () {
    let amount;
    amount = this.getCargoCapacity() - this.cargoMetal - this.cargoDeuterium - this.cargoFood;
    amount = Math.max(amount, 0);
    amount = Math.min(amount, this.crystalOnPlanet);
    this.cargoCrystal = Math.max(this.cargoCrystal, amount);
};

FleetDispatcher.prototype.getDeuteriumOnPlanetWithoutConsumption = function () {
    return Math.max(0, this.deuteriumOnPlanet - this.getConsumption());
};

FleetDispatcher.prototype.selectMinCrystal = function () {
    this.cargoCrystal = 0;
};

FleetDispatcher.prototype.selectMaxDeuterium = function () {
    let amount;
    amount = this.getCargoCapacity() - this.cargoMetal - this.cargoCrystal - this.cargoFood;
    amount = Math.max(amount, 0);
    amount = Math.min(amount, this.getDeuteriumOnPlanetWithoutConsumption());
    this.cargoDeuterium = Math.max(this.cargoDeuterium, amount);
};

FleetDispatcher.prototype.selectMinDeuterium = function () {
    this.cargoDeuterium = 0;
};

FleetDispatcher.prototype.selectMaxFood = function () {
    let amount;
    amount = this.getCargoCapacity() - this.cargoMetal - this.cargoCrystal - this.cargoDeuterium;
    amount = Math.max(amount, 0);
    amount = Math.min(amount, this.foodOnPlanet);
    this.cargoFood = Math.max(this.cargoFood, amount);
};

FleetDispatcher.prototype.selectMinFood = function () {
    this.cargoFood = 0;
};

FleetDispatcher.prototype.selectMaxAll = function () {
    this.cargoMetal = 0;
    this.cargoCrystal = 0;
    this.cargoDeuterium = 0;
    this.selectMaxDeuterium();
    this.selectMaxCrystal();
    this.selectMaxMetal();

    if ($("#food_box").length) {
        this.cargoFood = 0;
        this.selectMaxFood();
    }
};

FleetDispatcher.prototype.resetCargo = function () {
    this.cargoMetal = 0;
    this.cargoCrystal = 0;
    this.cargoDeuterium = 0;
    this.cargoFood = 0;
};

FleetDispatcher.prototype.updateCargo = function () {
    this.updateMetal();
    this.updateCrystal();
    this.updateDeuterium();

    if ($("#food_box").length) {
        this.updateFood();
    }
};

FleetDispatcher.prototype.updateMetal = function () {
    let amount = getValue($('#metal').val());
    let cargoSpace = this.getCargoCapacity() - this.cargoCrystal - this.cargoDeuterium - this.cargoFood;
    this.cargoMetal = Math.min(amount, this.metalOnPlanet, cargoSpace);
};

FleetDispatcher.prototype.updateCrystal = function () {
    let amount = getValue($('#crystal').val());
    let cargoSpace = this.getCargoCapacity() - this.cargoMetal - this.cargoDeuterium - this.cargoFood;
    this.cargoCrystal = Math.min(amount, this.crystalOnPlanet, cargoSpace);
};

FleetDispatcher.prototype.updateDeuterium = function () {
    let amount = getValue($('#deuterium').val());
    let cargoSpace = this.getCargoCapacity() - this.cargoMetal - this.cargoCrystal - this.cargoFood;
    let deuteriumOnPlanetWithoutConsumption = this.getDeuteriumOnPlanetWithoutConsumption();
    this.cargoDeuterium = Math.min(amount, this.deuteriumOnPlanet, cargoSpace, deuteriumOnPlanetWithoutConsumption);
};

FleetDispatcher.prototype.updateFood = function () {
    if (!this.lifeformEnabled) return;
    let amount = getValue($('#food').val());
    let cargoSpace = this.getCargoCapacity() - this.cargoMetal - this.cargoCrystal - this.cargoDeuterium;
    this.cargoFood = Math.min(amount, this.foodOnPlanet, cargoSpace);
};

FleetDispatcher.prototype.selectMission = function (mission) {
    if (this.fleetHelper.isMissionValid(mission)) {
        this.mission = mission;
    }

    this.updateHoldingTime();
    this.updateExpeditionTime();
    this.refresh();
};

FleetDispatcher.prototype.selectRetreatAfterDefenderRetreat = function (retreatAfterDefenderRetreat) {
    this.retreatAfterDefenderRetreat = retreatAfterDefenderRetreat;
};

FleetDispatcher.prototype.selectLootFoodOnAttack = function (lootFoodOnAttack) {
    this.lootFoodOnAttack = lootFoodOnAttack;
};

FleetDispatcher.prototype.updateHoldingTime = function () {
    if (this.mission === this.fleetHelper.MISSION_HOLD) {
        this.holdingTime = getValue($('#fleet2 #holdingtime').val());
    } else {
        this.holdingTime = 0;
    }
};

FleetDispatcher.prototype.updateExpeditionTime = function () {
    if (this.mission === this.fleetHelper.MISSION_EXPEDITION) {
        this.expeditionTime = getValue($('#fleet2 #expeditiontime').val());
    } else {
        this.expeditionTime = 0;
    }
};

FleetDispatcher.prototype.selectPriority = function (type, priority) {
    if (!this.lifeformEnabled && priority === 4) return;

    switch (type) {
        case 'metal':
            if (this.prioMetal === priority) break;
            if (this.prioCrystal === priority) this.prioCrystal = this.prioMetal;
            if (this.prioDeuterium === priority) this.prioDeuterium = this.prioMetal;
            if (this.prioFood === priority) this.prioFood = this.prioMetal;
            this.prioMetal = priority;
            break;

        case 'crystal':
            if (this.prioCrystal === priority) break;
            if (this.prioMetal === priority) this.prioMetal = this.prioCrystal;
            if (this.prioDeuterium === priority) this.prioDeuterium = this.prioCrystal;
            if (this.prioFood === priority) this.prioFood = this.prioCrystal;
            this.prioCrystal = priority;
            break;

        case 'deuterium':
            if (this.prioDeuterium === priority) break;
            if (this.prioMetal === priority) this.prioMetal = this.prioDeuterium;
            if (this.prioCrystal === priority) this.prioCrystal = this.prioDeuterium;
            if (this.prioFood === priority) this.prioFood = this.prioDeuterium;
            this.prioDeuterium = priority;
            break;

        case 'food':
            if (this.prioFood === priority) break;
            if (this.prioMetal === priority) this.prioMetal = this.prioFood;
            if (this.prioCrystal === priority) this.prioCrystal = this.prioFood;
            if (this.prioDeuterium === priority) this.prioDeuterium = this.prioFood;
            this.prioFood = priority;
            break;
    }
};

FleetDispatcher.prototype.refreshDataAfterAjax = function (data) {
    this.setOrders(data.orders);
    this.setTargetInhabited(data.targetInhabited);
    this.setTargetPlayerId(data.targetPlayerId);
    this.setTargetPlayerName(data.targetPlayerName);
    this.setTargetIsStrong(data.targetIsStrong);
    this.setTargetIsOutlaw(data.targetIsOutlaw);
    this.setTargetIsBuddyOrAllyMember(data.targetIsBuddyOrAllyMember);
    this.setTargetPlayerColorClass(data.targetPlayerColorClass);
    this.setTargetPlayerRankIcon(data.targetPlayerRankIcon);
    this.setPlayerIsOutlaw(data.playerIsOutlaw);
    this.setTargetPlanet(data.targetPlanet);
};
function FleetHelper(cfg) {
    // player specific ship data
    this.shipsData = cfg.shipsData; // game constants

    this.COLONIZATION_ENABLED = cfg.COLONIZATION_ENABLED;
    this.MAX_NUMBER_OF_PLANETS = cfg.MAX_NUMBER_OF_PLANETS;
    this.PLAYER_ID_LEGOR = cfg.PLAYER_ID_LEGOR;
    this.PLAYER_ID_SPACE = cfg.PLAYER_ID_SPACE; // mission constants

    this.MISSION_NONE = cfg.missions.MISSION_NONE;
    this.MISSION_ATTACK = cfg.missions.MISSION_ATTACK;
    this.MISSION_UNIONATTACK = cfg.missions.MISSION_UNIONATTACK;
    this.MISSION_TRANSPORT = cfg.missions.MISSION_TRANSPORT;
    this.MISSION_DEPLOY = cfg.missions.MISSION_DEPLOY;
    this.MISSION_HOLD = cfg.missions.MISSION_HOLD;
    this.MISSION_ESPIONAGE = cfg.missions.MISSION_ESPIONAGE;
    this.MISSION_COLONIZE = cfg.missions.MISSION_COLONIZE;
    this.MISSION_RECYCLE = cfg.missions.MISSION_RECYCLE;
    this.MISSION_DESTROY = cfg.missions.MISSION_DESTROY;
    this.MISSION_MISSILEATTACK = cfg.missions.MISSION_MISSILEATTACK;
    this.MISSION_EXPEDITION = cfg.missions.MISSION_EXPEDITION; // galaxy related constants

    this.DONUT_GALAXY = cfg.DONUT_GALAXY;
    this.DONUT_SYSTEM = cfg.DONUT_SYSTEM;
    this.MAX_GALAXY = cfg.MAX_GALAXY;
    this.MAX_SYSTEM = cfg.MAX_SYSTEM;
    this.MAX_POSITION = cfg.MAX_POSITION;
    this.EXPEDITION_POSITION = cfg.EXPEDITION_POSITION; // fleet related constants

    this.SPEEDFAKTOR_FLEET_PEACEFUL = cfg.SPEEDFAKTOR_FLEET_PEACEFUL;
    this.SPEEDFAKTOR_FLEET_WAR = cfg.SPEEDFAKTOR_FLEET_WAR;
    this.SPEEDFAKTOR_FLEET_HOLDING = cfg.SPEEDFAKTOR_FLEET_HOLDING;
    this.FLEET_DEUTERIUM_SAVE_FACTOR = cfg.FLEET_DEUTERIUM_SAVE_FACTOR; // target types

    this.PLANETTYPE_PLANET = cfg.PLANETTYPE_PLANET;
    this.PLANETTYPE_DEBRIS = cfg.PLANETTYPE_DEBRIS;
    this.PLANETTYPE_MOON = cfg.PLANETTYPE_MOON;
}

FleetHelper.prototype.isPlayerSpace = function (playerId) {
    return playerId === this.PLAYER_ID_SPACE;
};

FleetHelper.prototype.isPlayerLegor = function (playerId) {
    return playerId === this.PLAYER_ID_LEGOR;
};

FleetHelper.prototype.calcDistance = function (from, to, emptySystems, inactiveSystems) {
    let diffGalaxy = Math.abs(from.galaxy - to.galaxy);
    let diffSystem = Math.abs(from.system - to.system);
    let diffPlanet = Math.abs(from.position - to.position);

    if (diffGalaxy != 0) {
        let diff2 = Math.abs(diffGalaxy - this.MAX_GALAXY);

        if (this.DONUT_GALAXY == 1 && diff2 < diffGalaxy) {
            return diff2 * 20000;
        } else {
            return diffGalaxy * 20000;
        }
    } else if (diffSystem != 0) {
        let diff2 = Math.abs(diffSystem - this.MAX_SYSTEM);
        let deltaSystem = 0;

        if (this.DONUT_SYSTEM == 1 && diff2 < diffSystem) {
            deltaSystem = diff2;
        } else {
            deltaSystem = diffSystem;
        }

        deltaSystem = Math.max(deltaSystem - emptySystems - inactiveSystems, 1);
        return deltaSystem * 5 * 19 + 2700;
    } else if (diffPlanet != 0) {
        return diffPlanet * 5 + 1000;
    } else {
        return 5;
    }
};

FleetHelper.prototype.getFleetSpeedFaktor = function (mission) {
    let peaceful = [this.MISSION_TRANSPORT, this.MISSION_DEPLOY, this.MISSION_COLONIZE, this.MISSION_EXPEDITION];
    let war = [this.MISSION_ATTACK, this.MISSION_UNIONATTACK, this.MISSION_ESPIONAGE, this.MISSION_RECYCLE, this.MISSION_DESTROY, this.MISSION_MISSILEATTACK];
    let holding = [this.MISSION_HOLD];
    if (peaceful.includes(mission)) return this.SPEEDFAKTOR_FLEET_PEACEFUL;else if (war.includes(mission)) return this.SPEEDFAKTOR_FLEET_WAR;else if (holding.includes(mission)) return this.SPEEDFAKTOR_FLEET_HOLDING;else return 0;
};

FleetHelper.prototype.calcDuration = function (distance, maxSpeed, speedPercent, mission) {
    mission = mission || this.MISSION_NONE;
    return Math.max(Math.round((35000 / speedPercent * Math.sqrt(distance * 10 / maxSpeed) + 10) / this.getFleetSpeedFaktor(mission)), 1);
};

FleetHelper.prototype.calcConsumption = function (ships, distance, speedPercent, holdingTime, mission) {
    mission = mission || this.MISSION_NONE;
    let that = this;
    let consumption = 0;
    let holdingCosts = 0;
    let shipIds = ships.map(function (ship) {
        return ship.id;
    });
    let maxSpeed = this.getMaxSpeed(shipIds);
    let duration = this.calcDuration(distance, maxSpeed, speedPercent, mission);
    let speedValue = Math.max(0.5, duration * this.getFleetSpeedFaktor(mission) - 10);
    ships.forEach(function (ship) {
        if (ship.number) {
            let shipData = that.getShipData(ship.id);
            let shipSpeedValue = 35000 / speedValue * Math.sqrt(distance * 10 / shipData.speed);
            holdingCosts += shipData.fuelConsumption * ship.number * holdingTime;
            consumption += Math.max(shipData.fuelConsumption * ship.number * distance / 35000 * (shipSpeedValue / 10 + 1) * (shipSpeedValue / 10 + 1), 1);
        }
    });
    consumption = Math.round(consumption);
    consumption += holdingTime > 0 ? Math.max(Math.floor(holdingCosts / 10), 1) : 0;
    return consumption;
};

FleetHelper.prototype.calcCargoCapacity = function (shipId, number) {
    let shipData = this.getShipData(shipId);
    return Math.floor(shipData.baseCargoCapacity * number);
};

FleetHelper.prototype.calcFuelCapacity = function (shipId, number) {
    let shipData = this.getShipData(shipId);
    return shipData.baseFuelCapacity * number;
};

FleetHelper.prototype.getMaxSpeed = function (shipIds) {
    let that = this;
    let speeds = [];
    shipIds.forEach(function (shipId) {
        let shipData = that.getShipData(shipId);
        if (shipData && shipData.speed) speeds.push(shipData.speed);
    });
    let maxSpeed = speeds.reduce(function (a, b) {
        return Math.min(a, b);
    }, 1000000000);
    return maxSpeed;
};

FleetHelper.prototype.getShipData = function (shipId) {
    // shipsData is undefined when you switch to fast
    // the Planets (Target Planet)
    if (typeof this.shipsData === 'undefined') return null;
    return this.shipsData[shipId];
};

FleetHelper.prototype.isAggressiveMission = function (mission) {
    switch (mission) {
        case this.MISSION_ATTACK:
        case this.MISSION_UNIONATTACK:
        case this.MISSION_ESPIONAGE:
        case this.MISSION_DESTROY:
            return true;
    }

    return false;
};

FleetHelper.prototype.isMissionValid = function (mission) {
    switch (mission) {
        case this.MISSION_ATTACK:
        case this.MISSION_UNIONATTACK:
        case this.MISSION_TRANSPORT:
        case this.MISSION_DEPLOY:
        case this.MISSION_HOLD:
        case this.MISSION_ESPIONAGE:
        case this.MISSION_COLONIZE:
        case this.MISSION_RECYCLE:
        case this.MISSION_DESTROY:
        case this.MISSION_MISSILEATTACK:
        case this.MISSION_EXPEDITION:
            return true;
    }

    return false;
};