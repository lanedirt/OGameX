function adjustResearchClassBonuses(planetData) {
  let className = findClassName(characterClassArr, planetData.characterClassId ?? 0);

  if (characterClassBonuses[className]) {
    Object.keys(characterClassBonuses[className]).forEach((researchId) => {
      if (planetData.researches[researchId] > 0) {
        planetData.researches[researchId] = Math.max(
          0,
          planetData.researches[researchId] - characterClassBonuses[className][researchId],
        );
      }
    });
  }

  className = findClassName(allianceClassArr, planetData.allianceClassId ?? 0);

  if (allianceClassBonuses[className]) {
    Object.keys(allianceClassBonuses[className]).forEach((researchId) => {
      if (planetData.researches[researchId] > 0) {
        planetData.researches[researchId] = Math.max(
          0,
          planetData.researches[researchId] - allianceClassBonuses[className][researchId],
        );
      }
    });
  }
}
