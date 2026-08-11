function selectAllianceClass(obj) {
  let selection = "neutral";
  allianceClassArr.forEach((allianceClass) => {
    if (obj.attributes[allianceClass]) selection = allianceClass;
  });
  changeClass(obj, selection, "allianceclass");
}
