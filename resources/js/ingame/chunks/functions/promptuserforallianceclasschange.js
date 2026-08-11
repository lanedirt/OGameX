function promptUserForAllianceClassChange(newClassName, upgradeItemAjax, questionType, price, response) {
  activatingItem = false;

  if (response.userDoesNotHaveAlliance) {
    return 0;
  }

  let localizationString = LocalizationStrings.allianceClassItem[questionType];
  localizationString = localizationString.replace("#allianceClassName#", newClassName);

  if (questionType === "buyAndActivateItemQuestion") {
    localizationString = localizationString.replace("#darkmatter#", tsdpkt(price));
  }

  if (response && response.currentAllianceClass && response.dateOfLastAllianceClassChange) {
    localizationString += LocalizationStrings.allianceClassItem.appendCurrentClassQuestion;
    localizationString = localizationString.replace("#currentAllianceClassName#", response.currentAllianceClass);
    localizationString = localizationString.replace(
      "#lastAllianceClassChange#",
      response.dateOfLastAllianceClassChange,
    );
  }

  errorBoxDecision(
    LocalizationStrings.notice,
    localizationString,
    LocalizationStrings.yes,
    LocalizationStrings.no,
    upgradeItemAjax,
  );
}
