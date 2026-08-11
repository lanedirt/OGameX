function selectCharacterClass(obj) {
  let selection = "neutral";
  characterClassArr.forEach((characterClass) => {
    if (obj.attributes[characterClass]) selection = characterClass;
  });
  changeClass(obj, selection, "characterclass");
}
