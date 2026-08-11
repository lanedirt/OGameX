window.addEventListener("load", () => {
  const formElement = document.getElementById("resourcesettingsForm");

  if (formElement === null) {
    return;
  }

  formElement.addEventListener("submit", (event) => {
    event.preventDefault();
    const formData = new FormData(formElement, formElement.querySelector("input[type=submit]"));
    let postData = {};

    for (const entry of formData.entries()) {
      postData[entry[0]] = entry[1];
    }

    postData["_token"] = token;
    $.post(saveResourcesettingsUrl, postData, (data) => {
      const result = JSON.parse(data);
      token = result.newAjaxToken ?? token;

      if (typeof result.status === "undefined" || result.status !== "success") {
        if (typeof result.errors === "object" && result.errors.length > 0) {
          console.log(result.errors);
          fadeBox(result.errors[0].message, true);
        }

        return;
      }

      fadeBox(result.message ?? "", false);
      getAjaxResourcebox();

      if (typeof result.changes === "undefined" || result.changes.length <= 0) {
        return;
      }

      document.querySelector("#resourcesettingscomponent .productionFactorValue").textContent = Math.floor(
        (result.prodfactor ?? 0) * 100,
      );

      const updateRow = (row, data) => {
        Object.entries(data).forEach(([resourceIdx, value]) => {
          const productionSpan = row.querySelector('td[data-resourceIdx="' + resourceIdx + '"] span');
          productionSpan.textContent = value.number ?? value.value ?? 0;
          changeTooltip(productionSpan, value.tooltipNumber ?? value.title ?? 0);

          if (productionSpan.classList.contains(value.class) === false) {
            productionSpan.classList.remove("undermark");
            productionSpan.classList.remove("overmark");
            productionSpan.classList.remove("normalmark");
            productionSpan.classList.add(value.class);
          }
        });
      };

      result.changes.forEach((techId) => {
        if (typeof result.techlist[techId] === "undefined") {
          return;
        }

        const techRow = document.querySelector('#resourcesettingscomponent tr[data-techid="' + techId + '"]');

        if (techRow === null) {
          return;
        }

        updateRow(techRow, result.techlist[techId].techProduction);
      });
      updateRow(document.querySelector("#resourcesettingscomponent .summaryHourly"), result.hourly);
      updateRow(document.querySelector("#resourcesettingscomponent .summaryDaily"), result.daily);
      updateRow(document.querySelector("#resourcesettingscomponent .summaryWeekly"), result.weekly); // handle ipi menu content update

      if (typeof result.ipiMenuData === "object") {
        IPI.updateMenuContent(result.ipiMenuData);
      }
    });
    return false;
  });
});
