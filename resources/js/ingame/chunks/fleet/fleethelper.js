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
  let war = [
    this.MISSION_ATTACK,
    this.MISSION_UNIONATTACK,
    this.MISSION_ESPIONAGE,
    this.MISSION_RECYCLE,
    this.MISSION_DESTROY,
    this.MISSION_MISSILEATTACK,
  ];
  let holding = [this.MISSION_HOLD];
  if (peaceful.includes(mission)) return this.SPEEDFAKTOR_FLEET_PEACEFUL;
  else if (war.includes(mission)) return this.SPEEDFAKTOR_FLEET_WAR;
  else if (holding.includes(mission)) return this.SPEEDFAKTOR_FLEET_HOLDING;
  else return 0;
};

FleetHelper.prototype.calcDuration = function (distance, maxSpeed, speedPercent, mission) {
  mission = mission || this.MISSION_NONE;
  return Math.max(
    Math.round(
      ((35000 / speedPercent) * Math.sqrt((distance * 10) / maxSpeed) + 10) / this.getFleetSpeedFaktor(mission),
    ),
    1,
  );
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
      let shipSpeedValue = (35000 / speedValue) * Math.sqrt((distance * 10) / shipData.speed);
      holdingCosts += shipData.fuelConsumption * ship.number * holdingTime;
      consumption += Math.max(
        ((shipData.fuelConsumption * ship.number * distance) / 35000) *
          (shipSpeedValue / 10 + 1) *
          (shipSpeedValue / 10 + 1),
        1,
      );
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
  if (typeof this.shipsData === "undefined") return null;
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
