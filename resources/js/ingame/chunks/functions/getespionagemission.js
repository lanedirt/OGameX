function getEspionageMission(galaxyContentObject, planet, systemData) {
  let { galaxy, system, position, player } = galaxyContentObject;
  let { settingsProbeCount } = systemData;
  let espionageMission = planet.availableMissions.find(
    (availMission) => availMission.missionType === constants.espionage,
  );
  let holdMissionAvailable = planet.availableMissions.find((availMission) => availMission.missionType === 5);

  if (
    espionageMission &&
    espionageMission.canSpy &&
    !player.isAdmin &&
    galaxy &&
    system &&
    position &&
    settingsProbeCount
  ) {
    if (systemData.showOutlawWarning && !systemData.isOutlaw && player.isStrong && !holdMissionAvailable) {
      return `outlawWarning(${espionageMission.missionType}, ${galaxy}, ${system}, ${position}, ${planet.planetType}, ${settingsProbeCount});return false;`;
    } else {
      return `sendShips(${espionageMission.missionType}, ${galaxy}, ${system}, ${position}, ${planet.planetType}, ${settingsProbeCount});return false;`;
    }
  }

  return "";
}
