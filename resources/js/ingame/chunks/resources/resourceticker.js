function ResourceTicker() {
  this.timerObj = undefined;
  this.onClickActive = false;
}

ResourceTicker.prototype.reload = function (data) {
  this.techs = data.techs || {};
  this.resources = data.resources || {};
  changeTooltip($("#metal_box"), data.resources.metal.tooltip);
  changeTooltip($("#crystal_box"), data.resources.crystal.tooltip);
  changeTooltip($("#deuterium_box"), data.resources.deuterium.tooltip);
  changeTooltip($("#darkmatter_box"), data.resources.darkmatter.tooltip);
  changeTooltip($("#energy_box"), data.resources.energy.tooltip);

  if ($("#population_box").length) {
    changeTooltip($("#population_box"), data.resources.population.tooltip);
  }

  if ($("#food_box").length) {
    changeTooltip($("#food_box"), data.resources.food.tooltip);
  }

  this.refresh();
};

ResourceTicker.prototype.start = function () {
  if (this.timerObj === undefined) {
    this.timerObj = timerHandler.appendCallback(this.update.bind(this));
  }
};

ResourceTicker.prototype.stop = function () {
  if (this.timerObj !== undefined) {
    timerHandler.removeCallback(this.timerObj);
    delete this.timerObj;
  }
};

ResourceTicker.prototype.restart = function () {
  this.stop();
  this.start();
};

ResourceTicker.prototype.update = function () {
  let resourceProduction = {
    metal: 0,
    crystal: 0,
    deuterium: 0,
  };

  if ($("#population_box").length) {
    resourceProduction.population = 0;
  }

  for (let resource in resourceProduction) {
    if (resource === "population") {
      let extraProduction = 0;
      let foodToRemoveExtraPopulation = 0;
      let populationIncreaseBelowCap = 0;
      let populationIncreaseAboveCap = 0;
      let foodConsumptionAboveCap = 0;
      let maxPopulationToStarve = 0;
      let timeTillFoodRunsOut = 0;
      let populationChange = 0;
      let foodChange = 0;

      if (
        this.resources["population"].amount <
        Math.min(this.resources["population"].capableToFeed, this.resources["population"].storage)
      ) {
        populationIncreaseBelowCap = Math.min(
          this.resources["population"].capableToFeed - this.resources["population"].amount,
          Math.min(
            this.resources["population"].storage - this.resources["population"].amount,
            this.resources["population"].growthRate,
          ),
        );
        let currentFoodConsumption =
          this.resources["population"].amount * this.resources["population"].singleFoodConsumption;
        let extraFoodConsumption = populationIncreaseBelowCap * this.resources["population"].singleFoodConsumption;
        extraProduction = Math.max(
          0,
          Math.min(
            this.resources["food"].storage - this.resources["food"].amount,
            this.resources["food"].production - extraFoodConsumption - currentFoodConsumption,
          ),
        );
      }

      if (
        this.resources["population"].amount >=
        Math.min(this.resources["population"].capableToFeed, this.resources["population"].storage)
      ) {
        let populationNeedsFoodResource = Math.floor(
          Math.max(this.resources["population"].amount - this.resources["population"].capableToFeed, 0),
        );
        foodToRemoveExtraPopulation = Math.min(
          this.resources["food"].amount,
          populationNeedsFoodResource * this.resources["population"].singleFoodConsumption,
        );
        let foodLeft = Math.max(this.resources["food"].amount - foodToRemoveExtraPopulation, 0);

        if (populationNeedsFoodResource === 0) {
          let currentFoodConsumption =
            this.resources["population"].amount * this.resources["population"].singleFoodConsumption;
          extraProduction += Math.max(
            0,
            Math.min(
              this.resources["food"].storage - this.resources["population"].amount,
              this.resources["food"].production - currentFoodConsumption,
            ),
          );
        } else {
          timeTillFoodRunsOut = Math.floor(
            foodLeft / (populationNeedsFoodResource * this.resources["population"].singleFoodConsumption),
          );

          if (this.resources["food"].vacationMode.length > 1) {
            $(".resourceTooltip .timeTillFoodRunsOut").html(this.resources["food"].vacationMode);
          } else {
            $(".resourceTooltip .timeTillFoodRunsOut").html(
              "~" + formatTimeWrapper(timeTillFoodRunsOut, 2, true, " ", false, ""),
            );
          }
        }

        let starvingFactor = 0;

        if (foodLeft + extraProduction > 0) {
          populationIncreaseAboveCap = Math.min(
            Math.max(
              this.resources["population"].storage - this.resources["population"].amount - populationIncreaseBelowCap,
              0,
            ),
            this.resources["population"].growthRate,
          );
          populationIncreaseAboveCap = Math.min(
            (foodLeft + extraProduction) / this.resources["population"].singleFoodConsumption,
            populationIncreaseAboveCap,
          );
          foodConsumptionAboveCap = populationIncreaseAboveCap * this.resources["population"].singleFoodConsumption;
        } else {
          starvingFactor = 1;
        }

        let tooMuchPopulation = Math.max(
          this.resources["population"].amount +
            populationIncreaseBelowCap +
            populationIncreaseAboveCap -
            Math.min(this.resources["population"].storage, this.resources["population"].capableToFeed),
          0,
        );
        maxPopulationToStarve = Math.min(this.resources["population"].growthRate * starvingFactor, tooMuchPopulation);
      }

      populationChange += populationIncreaseBelowCap;
      populationChange += populationIncreaseAboveCap;
      populationChange -= maxPopulationToStarve;
      foodChange += extraProduction;
      foodChange -= foodToRemoveExtraPopulation;
      foodChange -= foodConsumptionAboveCap;
      let newAmountPopulation = this.resources["population"].amount + populationChange;

      if (this.resources["population"].amount < this.resources["population"].storage && populationChange > 0) {
        this.resources["population"].amount = Math.min(newAmountPopulation, this.resources["population"].storage);
      } else if (populationChange < 0) {
        this.resources["population"].amount = Math.max(newAmountPopulation, 0);
      } else if (this.resources["population"].amount >= this.resources["population"].storage && populationChange > 0) {
        this.resources["population"].amount = this.resources["population"].storage;
        this.resources["population"].growthRate = 0;
      }

      let newAmountFood = this.resources["food"].amount + foodChange;

      if (this.resources["food"].amount < this.resources["food"].storage && foodChange > 0) {
        this.resources["food"].amount = Math.min(newAmountFood, this.resources["food"].storage);
      } else if (foodChange <= 0) {
        this.resources["food"].amount = Math.max(newAmountFood, 0);
      } else if (newAmountFood === 0) {
        this.resources["food"].amount = 0;
      }
    } else {
      resourceProduction[resource] = this.resources[resource].production;
      let newAmount = this.resources[resource].amount + resourceProduction[resource];

      if (this.resources[resource].amount < this.resources[resource].storage && resourceProduction[resource] > 0) {
        this.resources[resource].amount = Math.min(newAmount, this.resources[resource].storage);
      } else if (resourceProduction[resource] < 0) {
        this.resources[resource].amount = Math.max(newAmount, 0);
      }
    }
  }

  this.refresh();
};

ResourceTicker.prototype.refresh = function () {
  let elements = {
    metal: $("#resources_metal"),
    crystal: $("#resources_crystal"),
    deuterium: $("#resources_deuterium"),
    darkmatter: $("#resources_darkmatter"),
    energy: $("#resources_energy"),
    population: $("#resources_population"),
    food: $("#resources_food"),
  }; // metal

  elements.metal.html(gfNumberGetHumanReadable(Math.floor(this.resources.metal.amount), true));
  elements.metal.removeClass("overmark middlemark");
  storageClass = this.getStorageClass(this.resources.metal.amount, this.resources.metal.storage);

  if (storageClass) {
    elements.metal.toggleClass(storageClass, true);
  } // crystal

  elements.crystal.html(gfNumberGetHumanReadable(Math.floor(this.resources.crystal.amount), true));
  elements.crystal.removeClass("overmark middlemark");
  storageClass = this.getStorageClass(this.resources.crystal.amount, this.resources.crystal.storage);

  if (storageClass) {
    elements.crystal.toggleClass(storageClass, true);
  } // deuterium

  elements.deuterium.html(gfNumberGetHumanReadable(Math.floor(this.resources.deuterium.amount), true));
  elements.deuterium.removeClass("overmark middlemark");
  storageClass = this.getStorageClass(this.resources.deuterium.amount, this.resources.deuterium.storage);

  if (storageClass) {
    elements.deuterium.toggleClass(storageClass, true);
  } // darkmatter

  elements.darkmatter.html(gfNumberGetHumanReadable(this.resources.darkmatter.amount, true)); // energy

  elements.energy.html(gfNumberGetHumanReadable(Math.floor(this.resources.energy.amount), true));
  elements.energy.toggleClass("overmark", this.resources.energy.amount < 0); // population

  if (elements.population.length) {
    elements.population.html(gfNumberGetHumanReadable(Math.floor(this.resources.population.amount), true));
    elements.population.removeClass("overmark middlemark");
    storageClass = this.getStorageClass(this.resources.population.amount, this.resources.population.storage);

    if (storageClass) {
      elements.population.toggleClass(storageClass, true);
    }
  } // food

  if (elements.food.length) {
    elements.food.html(gfNumberGetHumanReadable(Math.floor(this.resources.food.amount), true));
    elements.food.removeClass("overmark middlemark");
    storageClass = this.getStorageClass(this.resources.food.amount, this.resources.food.storage);

    if (storageClass) {
      elements.food.toggleClass(storageClass, true);
    }
  }
};

ResourceTicker.prototype.getStorageClass = function (amount, storage) {
  if (amount >= storage) {
    return "overmark";
  } else if (amount >= storage * 0.9) {
    return "middlemark";
  }

  return undefined;
};

ResourceTicker.prototype.activateOnClick = function () {
  if (this.onClickActive === true) {
    return;
  }

  $("#metal_box, #crystal_box, #deuterium_box")
    .off("click")
    .on("click", function (event) {
      location.href = $(this).data("shopUrl");
    });
  this.onClickActive = true;
};
