function executeBuildAction(technologyId, planetId, mode, listId) {
  if (mode === 1) {
    buildListActionBuild(technologyId, null, null, null, planetId);
  } else if (mode === 3) {
    buildListActionDemolish(technologyId, planetId);
  } else if (mode === 2 && listId) {
    buildListActionCancel(technologyId, listId, planetId);
  }
}
