function changeClass(target, selectedClass, type) {
  let classArr = characterClassArr;
  let classBonusArr = characterClassBonuses;

  if (type === "allianceclass") {
    classArr = allianceClassArr;
    classBonusArr = allianceClassBonuses;
  }

  const classId = findClassId(classArr, selectedClass);
  const classSelection = $(target).closest("class-selection");
  const inputField = classSelection.find("input").first();
  const fleetSection = target.closest("fleet-content");
  const currentClass = findClassName(
    classArr,
    inputField.data("classId") === "" ? 0 : parseInt(inputField.data("classId")),
  );
  inputField.data("classId", classId);
  inputField.attr("data-class-id", classId);
  inputField.prop("checked", false);
  const icons = classSelection.find(type + "-icon");
  icons.each((idx, icon) => {
    classArr.forEach((className) => {
      $(icon).removeAttr(className);
    });
    targetId = idx + 1;

    if (targetId === classId) {
      targetId = 0;
    }

    $(icon).attr(classArr[targetId], true);
  });

  if (classBonusArr[currentClass]) {
    Object.keys(classBonusArr[currentClass]).forEach((techId) => {
      const researchInput = $(fleetSection)
        .find(".technology-row input[name='amount[" + techId + "]']")
        .first();
      const currentValue = researchInput.val().length === 0 ? 0 : parseInt(researchInput.val());
      const currentMin = (researchInput.attr("min") ?? "").length === 0 ? 0 : parseInt(researchInput.attr("min"));
      researchInput.val(Math.max(0, currentValue - classBonusArr[currentClass][techId]));
      researchInput.attr("min", Math.max(0, currentMin - classBonusArr[currentClass][techId]));
    });
  }

  if (classBonusArr[selectedClass]) {
    Object.keys(classBonusArr[selectedClass]).forEach((techId) => {
      const researchInput = $(fleetSection)
        .find(".technology-row input[name='amount[" + techId + "]']")
        .first();
      const currentValue = researchInput.val().length === 0 ? 0 : parseInt(researchInput.val());
      const currentMin = (researchInput.attr("min") ?? "").length === 0 ? 0 : parseInt(researchInput.attr("min"));
      researchInput.val(currentValue + classBonusArr[selectedClass][techId]);
      researchInput.attr("min", currentMin + classBonusArr[selectedClass][techId]);
    });
  }

  simChanged(classSelection);
}
