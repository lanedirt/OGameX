ogame.messages.combatreport = {
  data: [
    {
      combatReportId: $(".detailReport").attr("data-combatreportid"),
    },
  ],
  loca: [
    {
      weapon: "",
      shield: "",
      cover: "",
    },
  ],
  // __________________________________________________________ATTRIBUTES__________________________________________________________

  /**
   * get the attributes by the active member(s)
   *
   * @see ogame.messages.combatreport.setCombatArray >> $this.data.combatArray, $this.data.activeMember must be set
   * @return object result 	>> 'armorPercentage': armorpower in percentage
   * 							>> 'weaponPercentage': weaponpower in percentage
   * 							>> 'shieldPercentage': shieldpower in percentage
   */
  getCombatValueByCombatMember: function () {
    var $this = ogame.messages.combatreport;
    var member = $this.data.activeMember;
    var result = {
      armorPercentage: 0,
      weaponPercentage: 0,
      shieldPercentage: 0,
      characterClassName: "-",
    };
    var countit = 0;
    $.each($this.data.combatArray, function (key, combatMember) {
      //if activeMember not selected and on default add all attributes of all combatMembers
      if (
        $this.check(true, member, {
          values: {
            is: {
              0: "all",
            },
          },
        })
      ) {
        result.armorPercentage += combatMember.armorPercentage;
        result.weaponPercentage += combatMember.weaponPercentage;
        result.shieldPercentage += combatMember.shieldPercentage;
        countit++;
      } else {
        if (combatMember.ownerName == member) {
          result.armorPercentage = combatMember.armorPercentage;
          result.weaponPercentage = combatMember.weaponPercentage;
          result.shieldPercentage = combatMember.shieldPercentage;
          result.characterClassName = combatMember.ownerCharacterClassName;
          countit = 1;
        }
      }
    });
    result.armorPercentage = Math.round(result.armorPercentage / countit);
    result.weaponPercentage = Math.round(result.weaponPercentage / countit);
    result.shieldPercentage = Math.round(result.shieldPercentage / countit);
    return result;
  },

  /**
   * Set the combatMember attributes
   *
   * @see ogame.messages.combatreport.getCombatValueByCombatMember >> resultArray with armor, weapon and shield must be set
   * @see ogame.messages.combatreport.setCombatArray >> combatside must be set
   */
  setCombatValue: function () {
    var $this = ogame.messages.combatreport; //set attributes by new combatmember

    var result = $this.getCombatValueByCombatMember();
    $("." + $this.data.combatside + "CharacterClass").text($this.loca.characterClass + " " + result.characterClassName);
    $("." + $this.data.combatside + "Weapon").text($this.loca.weapon + " " + result.weaponPercentage + "%");
    $("." + $this.data.combatside + "Shield").text($this.loca.shield + " " + result.shieldPercentage + "%");
    $("." + $this.data.combatside + "Cover").text($this.loca.cover + " " + result.armorPercentage + "%");
  },
  setCombatLoca: function (loca_weapon, loca_shield, loca_cover, loca_characterClass) {
    var $this = ogame.messages.combatreport;
    $this.loca.weapon = loca_weapon;
    $this.loca.shield = loca_shield;
    $this.loca.cover = loca_cover;
    $this.loca.characterClass = loca_characterClass;
  },
  // __________________________________________________________ACTIVESTATE__________________________________________________________

  /**
   * returns if a ship is active in the combat
   *
   * @param int id				>> shipid to proof
   * @param object membersArray	>> memberid: object	>> id
   *
   * @return string returningValue	>> 'on'||'off' -> for image-css-class
   */
  isActive: function (id, membersArray) {
    var $this = ogame.messages.combatreport; // if shipid is not in the membersArray the shipimage is not active (off)

    for (var key in membersArray) {
      if (key == "length") {
        continue;
      }

      if ($this.check(true, membersArray[key][id])) {
        return "on";
      }
    }

    if ($this.check(true, membersArray[id])) {
      return "on";
    }

    return "off";
  },

  /**
   * set the active class for all ships
   *
   * @param object allShipObjects	>> classes of all shipfields
   * @param object activeShips	>> all active ships
   */
  setActiveFlag4Fleet: function (allShipObjects, activeShips) {
    var $this = ogame.messages.combatreport;
    var allShipClasses = $this.getAllShipClasses(allShipObjects);
    $.each(allShipClasses, function (key, classname) {
      var currentShipId = $this.getShipIdByClass(classname);
      var isActiveClass = $this.isActive(currentShipId, activeShips);
      var selector = $this.getShipSelectors(currentShipId);
      $this.changeShipState(isActiveClass, selector["ship"]); //clear count

      if (isActiveClass == "off") {
        var shipSelector = selector["shipCount"];
        var lossSelector = selector["loss"];
        var counts = {
          ships: {},
          losses: {},
        };
        counts["ships"][shipSelector] = 0;
        counts["losses"][lossSelector] = "";
        $this.setShipCount(counts);
      }

      $this.toggleShipShowState(selector["ship"]);
    });
  },

  /**
   * Search for the right class of the given shipID
   *
   * @param object possibleCategories >> shipcategories to get the right classname for id
   * @param int id >> shipid for searching
   *
   * @return string|bool classname|false	>> returns the classname if it exists else false
   */
  search4Class: function (possibleCategories, id) {
    for (var index in possibleCategories) {
      if ($("." + possibleCategories[index] + id)[0]) {
        var classname = possibleCategories[index] + id;
        return classname;
      }
    }

    return false;
  },

  /**
   * Get an Object with ship- and lossselector
   *
   * @param mixed shipID	>> can be a string or a number, id of the ship
   *
   * @return object result	>> 'ship': selector
   * 								'shipCount': selector
   * 								'loss': selector
   */
  getShipSelectors: function (shipID) {
    var $this = ogame.messages.combatreport;
    var possibleShipClasses = ["military", "civil", "defense"];
    var classname = $this.search4Class(possibleShipClasses, shipID);
    var shipSelector = "." + $this.data.combatside + " ." + classname;
    var shipCountSelector = "." + $this.data.combatside + " ." + classname + " .ecke";
    var lossCountSelector = "." + $this.data.combatside + " ." + classname + " .lost_ships";
    var result = {
      ship: shipSelector,
      shipCount: shipCountSelector,
      loss: lossCountSelector,
    };
    return result;
  },

  /**
   * Get the shipid in the given class
   *
   * @param string classname	>> class of one shipobject
   *
   * @return string shipID
   */
  getShipIdByClass: function (classname) {
    var classlength = classname.length;
    var shipID = classname.substr(classlength - 3);
    return shipID;
  },

  /**
   * change the active state of shipfield
   *
   * @see ogame.messages.combatreport.setActiveFlag4Fleet
   * @param string newState >> the state to set
   * @param string selector >> the selector of the image
   */
  changeShipState: function (newState, selector) {
    if ($(selector).hasClass("off") && newState != "off") {
      $(selector).removeClass("off");
    }

    if ($(selector).hasClass("on") && newState != "on") {
      $(selector).removeClass("on");
    }

    if (!$(selector).hasClass(newState)) {
      $(selector).addClass(newState);
    }
  },

  /**
   * remove the ship if its marked as disabled and show all non disabled ships
   *
   * @param string selector >> the selector of the image
   */
  toggleShipShowState: function (selector) {
    if ($(selector).hasClass("off")) {
      $(selector).parent().hide();
    }

    if ($(selector).hasClass("on")) {
      $(selector).parent().show();
    }
  },
  // __________________________________________________________SHIPS__________________________________________________________

  /**
   * get only the ships of the active member(s)
   *
   * @param string member	>> selected combatMember
   *
   * @return object ships
   */
  getShipsByMembers: function (member) {
    var $this = ogame.messages.combatreport;
    var ships = [];

    if (!$this.check(true, $this.data.combatArray.shipDetails)) {
      $.each($this.data.combatArray, function (key, combatMember) {
        if (typeof combatMember == "object" && typeof combatMember.shipDetails != "undefined") {
          if (typeof ships[combatMember.ownerName] != "undefined") {
            $.extend(ships[combatMember.ownerName], combatMember.shipDetails);
          } else {
            ships[combatMember.ownerName] = combatMember.shipDetails;
          }
        } else {
          if (typeof ships[combatMember.ownerName] == "undefined") {
            ships[combatMember.ownerName] = {};
          }
        }
      });
    } else {
      if (typeof ships[$this.data.combatArray.ownerName] != "undefined") {
        $.extend(ships[$this.data.combatArray.ownerName], $this.data.combatArray.shipDetails);
      } else {
        ships[$this.data.combatArray.ownerName] = $this.data.combatArray.shipDetails;
      }
    }

    if (member == "all") {
      return ships;
    } else {
      return ships[member];
    }
  },

  /**
   * get only the ships of the active member(s) and selected planet/moon
   *
   * @param member string	>> selected combatMember
   * @param coords string >> coordinates of Planet/Moon
   * @param type int >> 1 = planet, 3 = moon
   * @param combatSim boolean
   * @return object ships
   */
  getShipsByMembersAndCoords: function (member, coords, type) {
    var $this = ogame.messages.combatreport;
    var ships = [];

    if (!$this.check(true, $this.data.combatArray.shipDetails)) {
      $.each($this.data.combatArray, function (key, combatMember) {
        if (coords !== 0) {
          // a location was selected , so we need to filter based on owner name, coordinates and type
          if (typeof combatMember == "object" && typeof combatMember.shipDetails != "undefined") {
            if (
              combatMember.ownerCoordinates === coords &&
              combatMember.ownerName == member &&
              combatMember.ownerPlanetType == type
            ) {
              if (typeof ships[combatMember.ownerName] != "undefined") {
                $.extend(ships[combatMember.ownerName], combatMember.shipDetails);
              } else {
                ships[combatMember.ownerName] = combatMember.shipDetails;
              }
            }
          }
        } else {
          // an owner name was selected incl. all, so we will group ships based on Owner names)
          if (typeof combatMember == "object" && typeof combatMember.shipDetails != "undefined") {
            if (typeof ships[combatMember.ownerName] != "undefined") {
              $.extend(ships[combatMember.ownerName], combatMember.shipDetails);
            } else {
              ships[combatMember.ownerName] = combatMember.shipDetails;
            }
          }
        }
      });
    } else {
      // This part of code was there, I have no clue why :D
      if (typeof ships[$this.data.combatArray.ownerName] != "undefined") {
        $.extend(ships[$this.data.combatArray.ownerName], $this.data.combatArray.shipDetails);
      } else {
        ships[$this.data.combatArray.ownerName] = $this.data.combatArray.shipDetails;
      }
    }

    if (member == "all") {
      return ships;
    } else {
      return ships[member];
    }
  },

  /**
   * Get the Array with counts und selectors to set the count of the ships
   *
   * @param object ships	>> shipID: count
   * @param object losses	>> shipID: count
   *
   * @return object combatCountArray 	>> 'ships': object >> selector: count
   * 									>> 'losses': object >> selector: -count
   */
  getShipCountArray: function (ships, losses) {
    var $this = ogame.messages.combatreport;
    var noLosses = $this.check(true, losses, {
      types: {
        0: "isEmpty",
      },
    })
      ? false
      : true; //var noLosses = losses == null || typeof losses == "undefined" || jQuery.isEmptyObject(losses) ? true : false;

    var combatShipsArray = {};
    var combatLossArray = {};

    for (var shipID in ships) {
      var selector = $this.getShipSelectors(shipID);
      var shipSelector = selector["shipCount"];
      combatShipsArray[shipSelector] = parseInt(ships[shipID]);

      if (!noLosses) {
        var lossSelector = selector["loss"];

        if ($this.check(true, losses[shipID])) {
          combatLossArray[lossSelector] = parseInt(losses[shipID]);
        }
      }
    }

    var combatCountArray = {
      ships: combatShipsArray,
      losses: combatLossArray,
    };
    return combatCountArray;
  },

  /**
   * set the count of active ships by shipcountarray
   *
   * @param object shipArray	>> 'ships': object	>> memberID: object	>> shipID: count
   */
  setShipCount4All: function (shipArray) {
    var $this = ogame.messages.combatreport;
    var ships = shipArray["ships"];
    var losses = $this.check(true, shipArray["losses"]) ? shipArray["losses"] : {};
    var combatResultArray = {};

    for (var index in ships) {
      var ship = ships[index];
      var loss =
        $this.check(true, losses, {
          types: {
            0: "isEmpty",
          },
        }) && $this.check(true, losses[index])
          ? losses[index]
          : {};
      combatResultArray[index] = $this.getShipCountArray(ship, loss);
    }

    var shipResult = {};
    var lossResult = {};

    for (var memberIndex in combatResultArray) {
      var memberShipArray = combatResultArray[memberIndex]["ships"];
      var memberLossArray = combatResultArray[memberIndex]["losses"];

      for (var shipid in memberShipArray) {
        if ($this.check(true, shipResult[shipid])) {
          shipResult[shipid] = shipResult[shipid] + memberShipArray[shipid];
        } else {
          shipResult[shipid] = memberShipArray[shipid];
        }
      }

      for (var shipid in memberLossArray) {
        if ($this.check(true, lossResult[shipid])) {
          lossResult[shipid] = lossResult[shipid] + memberLossArray[shipid];
        } else {
          lossResult[shipid] = memberLossArray[shipid];
        }
      }
    }

    var resultCountArray = {
      ships: shipResult,
      losses: lossResult,
    };
    $this.setShipCount(resultCountArray);
  },

  /**
   * set the Shipcount if a specific combatMember is selected
   *
   * @param resultCombatArray object 	>> 'ships': object	>> memberID: object	>> shipID: count
   * 								   	>> 'losses': object	.. (could be null or empty)
   * @param memberArray object		>> memberID: object >> 'ownerName': name
   * @param activeMember string		>> selected member in Dropdown
   * @param coords string             >> coordinates of planet/moon
   * @param planetType int                  >> 1= planet, 3= moon
   */
  setShipCountByActiveMember: function (resultCombatArray, memberArray, activeMember, coords, planetType) {
    coords = coords || 0;
    planetType = planetType || 1;
    var $this = ogame.messages.combatreport;
    var shipCounts = {};
    var lossCounts = {};

    for (var memberID in memberArray) {
      var ownerName = memberArray[memberID]["ownerName"];
      var ownerCoordinates = memberArray[memberID]["ownerCoordinates"];
      var ownerPlanetType = memberArray[memberID]["ownerPlanetType"];

      if (ownerName == activeMember) {
        for (var attr in resultCombatArray["ships"][memberID]) {
          if (coords !== 0) {
            if (coords == ownerCoordinates && planetType == ownerPlanetType) {
              if (!shipCounts.hasOwnProperty(attr)) {
                shipCounts[attr] = resultCombatArray["ships"][memberID][attr];
              } else {
                shipCounts[attr] = shipCounts[attr] + resultCombatArray["ships"][memberID][attr];
              }
            }
          } else {
            if (!shipCounts.hasOwnProperty(attr)) {
              shipCounts[attr] = resultCombatArray["ships"][memberID][attr];
            } else {
              shipCounts[attr] = shipCounts[attr] + resultCombatArray["ships"][memberID][attr];
            }
          }
        }

        if ($this.check(true, resultCombatArray["losses"])) {
          for (var attr in resultCombatArray["losses"][memberID]) {
            if (coords !== 0) {
              if (coords == ownerCoordinates && planetType == ownerPlanetType) {
                if (!lossCounts.hasOwnProperty(attr)) {
                  lossCounts[attr] = parseInt(resultCombatArray["losses"][memberID][attr]);
                } else {
                  lossCounts[attr] = lossCounts[attr] + parseInt(resultCombatArray["losses"][memberID][attr]);
                }
              }
            } else {
              if (!lossCounts.hasOwnProperty(attr)) {
                lossCounts[attr] = parseInt(resultCombatArray["losses"][memberID][attr]);
              } else {
                lossCounts[attr] = lossCounts[attr] + parseInt(resultCombatArray["losses"][memberID][attr]);
              }
            }
          }
        }
      }
    }

    if (
      $this.check(true, shipCounts, {
        types: {
          0: "isEmpty",
        },
      })
    ) {
      var countsByMemberArray = $this.getShipCountArray(shipCounts, lossCounts);
      $this.setShipCount(countsByMemberArray);
    }
  },

  /**
   * Set Count by given result array
   *
   * @param object resultArray	>> 'ships': { selectorstring: count}
   * 								>> 'losses': ...
   */
  setShipCount: function (resultArray) {
    var combatside = ogame.messages.combatreport.data.combatside;
    var shipResult = resultArray["ships"];
    var lossResult = resultArray["losses"];

    if (!$.isEmptyObject(shipResult)) {
      for (var selector in shipResult) {
        $(selector).text(shipResult[selector].toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
      }
    } else {
      // when there are no Ships, then reset all ecke column to 0 in the current combatside (attacker or defender)
      $("." + combatside + " .ecke").text("0");
    }

    if (!$.isEmptyObject(lossResult)) {
      for (var selector in lossResult) {
        $(selector).text("-" + lossResult[selector].toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
      }
    } else {
      // when there are no losses, then reset all lost_ships column to - in the current combatside (attacker or defender)
      $("." + combatside + " .lost_ships").text("-");
    }
  },

  /**
   * Get classes of all ships by jqueryobjects of them
   *
   * @param object allShips	>> objects
   *
   * @return object allShipClasses	>> all classes in array
   */
  getAllShipClasses: function (allShips) {
    var id = allShips
      .map(function () {
        var id = $(this).attr("class");
        return id;
      })
      .get()
      .join();
    var allShipClasses = [];
    $.each(id.split(","), function (key, value) {
      var classArray = value.split(" ");
      allShipClasses.push(classArray[1]);
    });
    return allShipClasses;
  },

  /**
   * Set the attributes to display ships
   *
   * @param activeMember object		>> selected member
   * @param combatside object			>> attacker||defender
   * @param resultCombatArray object	>> array of the last round
   * @param memberArray object		>> array of the specific member
   * @param coords string             >> coordinates of planet/moon
   * @param type int                  >> 1= planet, 3 = moon
   */
  displayShipData: function (activeMember, combatside, resultCombatArray, memberArray, coords, type) {
    coords = typeof coords !== "undefined" ? coords : 0;
    var $this = ogame.messages.combatreport;

    if (
      $this.check(true, activeMember, {
        values: {
          isNot: {
            0: "all",
          },
        },
      })
    ) {
      //if(activeMember != 'all' && typeof activeMember != 'undefined') {
      $this.setShipCountByActiveMember(resultCombatArray, memberArray, activeMember, coords, type);
    } else {
      $this.setShipCount4All($this.data.combatRounds[combatside][$this.data.combatRounds[combatside].length - 1]);
    }
  },
  // __________________________________________________________ROUNDTEXT__________________________________________________________

  /**
   * show the last combatRound first
   */
  initCombatText: function (combatData) {
    var $this = ogame.messages.combatreport;
    var round = combatData["combatRounds"].length - 1;
    $(".combat_round_list .round_id").find("a").removeClass("active");
    $(".combat_round_list .round_id[data-round=" + round + "]")
      .find("a")
      .addClass("active");
    $this.loadDataBySelectedRound(combatData["attackerJSON"], combatData["defenderJSON"], round);
  },

  /**
   * set the text that descripe the combat by selected round
   *
   * @param object attackerArray	>> round: object >> 'statistic': object >> 'hits': int hitpoints
   * 																		>> 'absorbedDamage': int absorbedDamagePoints
   * 																		>> 'fullStrength': int strength without any absorbance
   * @param object defenderArray	>> defenderArray @see attackerArray
   * @param int round				>> selected round
   */
  setCombatText: function (attackerArray, defenderArray, round) {
    var $this = ogame.messages.combatreport;
    var attackerClass = ".statistic_attacker";
    var defenderClass = ".statistic_defender";
    var hitsClass = ".hits";
    var strengthClass = ".strength";
    var absorbedClass = ".absorbed";
    var sumHitsAttacker = 0;
    var sumAbsorbedDamageAttacker = 0;
    var sumFullStrengthAttacker = 0;
    var sumHitsDefender = 0;
    var sumAbsorbedDamageDefender = 0;
    var sumFullStrengthDefender = 0;

    if (
      $this.check(true, attackerArray, {
        length: round,
      })
    ) {
      for (var roundindex in attackerArray) {
        sumHitsAttacker = sumHitsAttacker + parseInt(attackerArray[roundindex]["statistic"]["hits"]);
        sumAbsorbedDamageAttacker =
          sumAbsorbedDamageAttacker + parseInt(attackerArray[roundindex]["statistic"]["absorbedDamage"]);
        sumFullStrengthAttacker =
          sumFullStrengthAttacker + parseInt(attackerArray[roundindex]["statistic"]["fullStrength"]);
        sumHitsDefender = sumHitsDefender + parseInt(defenderArray[roundindex]["statistic"]["hits"]);
        sumAbsorbedDamageDefender =
          sumAbsorbedDamageDefender + parseInt(defenderArray[roundindex]["statistic"]["absorbedDamage"]);
        sumFullStrengthDefender =
          sumFullStrengthDefender + parseInt(defenderArray[roundindex]["statistic"]["fullStrength"]);
      }
    } else {
      sumHitsAttacker = parseInt(attackerArray[round]["statistic"]["hits"]);
      sumAbsorbedDamageAttacker = parseInt(attackerArray[round]["statistic"]["absorbedDamage"]);
      sumFullStrengthAttacker = parseInt(attackerArray[round]["statistic"]["fullStrength"]);
      sumHitsDefender = parseInt(defenderArray[round]["statistic"]["hits"]);
      sumAbsorbedDamageDefender = parseInt(defenderArray[round]["statistic"]["absorbedDamage"]);
      sumFullStrengthDefender = parseInt(defenderArray[round]["statistic"]["fullStrength"]);
    }

    $(attackerClass + hitsClass).text(sumHitsAttacker.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
    $(attackerClass + absorbedClass).text(sumAbsorbedDamageAttacker.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
    $(attackerClass + strengthClass).text(sumFullStrengthAttacker.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
    $(defenderClass + hitsClass).text(sumHitsDefender.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
    $(defenderClass + absorbedClass).text(sumAbsorbedDamageDefender.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
    $(defenderClass + strengthClass).text(sumFullStrengthDefender.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
  },
  // __________________________________________________________MAIN__________________________________________________________

  /**
   * Setup all important information for the combat
   *
   * @param object combatArray	>> all important information init
   * @param object combatside		>> for what side to set the information
   */
  setCombatArray: function (originalCombatArray, combatside) {
    // var combatArray = jQuery.extend({}, originalCombatArray); //shallow copy
    var combatArray = jQuery.extend(true, {}, originalCombatArray); //deep copy

    var $this = ogame.messages.combatreport;
    $this.data.combatside = combatside;
    $this.data.memberSelection = false;

    if ($("#" + $this.data.combatside + "_select_combatreport").find(":selected").length > 0) {
      $this.data.memberSelection = true;
    } // make sure that activeMember becomes a STRING, else all hell will break loose!

    if ($this.data.memberSelection) {
      $this.data.activeMember =
        "" +
        $("#" + $this.data.combatside + "_select_combatreport")
          .find(":selected")
          .val();
    } else {
      $this.data.activeMember = "" + $("#" + $this.data.combatside + "_select_combatreport").data("memberName");
    }

    $this.data.activeMember = $this.data.activeMember.split("|", 1)[0];
    $this.data.combatArray = combatArray.member;
    $this.data.combatRounds = [];
    $this.data.combatRounds[combatside] = combatArray.combatRounds;
  },

  /**
   * Setter for combatside
   *
   * @param object combatside		>> for what side to set the information
   */
  setCombatside: function (combatside) {
    var $this = ogame.messages.combatreport;
    $this.data.combatside = combatside;
  },

  /**
   * checks if the given object is correct
   *
   * @param bool easyCheck		>> the result should be only success or more information
   * @param object checks			>>
   *  mixed checkingObject	>> variable, array etc u wanna check - without any parameter check for undefined and null
   *  object values2check	>> values you wanna compare with the given object ({'values':{'is':{},'isNot'{}}})
   *  object types2check	>> types you wanna check with typeof (isEmpty, number, object, string)
   *
   * @return object result		>> success: bool			|| success
   * 								>> allErrors: int
   * 								>> valueErrors: int
   * 								>> typeErrors: int
   * 								>> type: string
   * 								>> length: int
   * 								>> lengthIsChecked: bool
   * 								>> lengthError: int (1||0)
   */
  check: function (easyCheck, checkingObject, checks) {
    var checks = checks != null && typeof checks != "undefined" && typeof checks == "object" ? checks : false;
    var success = false;
    var checkingValueErrorCount = 0;
    var checkingTypeErrorCount = 0;
    var checkingLengthErrorCount = 0;
    var length = 0;
    var lengthIsProofed = false; // only if any other checks are given

    if (checks != false) {
      var values2check =
        checks["values"] != null && typeof checks["values"] != "undefined" && typeof checks["values"] == "object"
          ? checks["values"]
          : {};
      var types2check =
        checks["types"] != null && typeof checks["types"] != "undefined" && typeof checks["types"] == "object"
          ? checks["types"]
          : {};
      var length2check = checks["length"] != null && typeof checks["length"] != "undefined" ? checks["length"] : false;

      if (!jQuery.isEmptyObject(values2check)) {
        for (index in values2check) {
          var isValueChecklist = values2check[index]; //checking for isValue or isNotValue

          if (index == "is" && !jQuery.isEmptyObject(isValueChecklist)) {
            for (var isValue in isValueChecklist) {
              if (checkingObject != isValueChecklist[isValue]) {
                checkingValueErrorCount = checkingValueErrorCount + 1;
              }
            }
          }

          if (index == "isNot" && !jQuery.isEmptyObject(isValueChecklist)) {
            for (var isNotValue in isValueChecklist) {
              if (checkingObject == isValueChecklist[isNotValue]) {
                checkingValueErrorCount = checkingValueErrorCount + 1;
              }
            }
          }
        }
      }

      if (!jQuery.isEmptyObject(types2check)) {
        if (types2check["isEmpty"]) {
          if (jQuery.isEmptyObject(checkingObject)) {
            checkingTypeErrorCount = checkingTypeErrorCount + 1;
          }
        }

        for (var index in types2check) {
          if (typeof checkingObject == types2check[index]) {
            checkingTypeErrorCount = checkingTypeErrorCount + 1;
          }
        }
      }

      if (typeof checkingObject == "object") {
        for (var index in checkingObject) {
          if (typeof checkingObject[index] != "undefined") {
            length = length + 1;
          }
        }
      } else {
        var checkingString = checkingObject + "";
        length = checkingString.length;
      }

      if (length2check !== false && typeof length2check == "number" && typeof length == "number") {
        if (length != length2check) {
          checkingLengthErrorCount = checkingLengthErrorCount + 1;
        }

        lengthIsProofed = true;
      }
    }

    if (typeof checkingObject == "undefined" || checkingObject == null) {
      checkingTypeErrorCount = checkingTypeErrorCount + 1;
    }

    var errorCount = checkingValueErrorCount + checkingTypeErrorCount;

    if (lengthIsProofed != false) {
      errorCount = errorCount + checkingLengthErrorCount;
    }

    if (errorCount == 0) {
      success = true;
    }

    var result = {
      success: success,
      allErrors: errorCount,
      valueErrors: checkingValueErrorCount,
      typeErrors: checkingTypeErrorCount,
      type: typeof checkingObject,
      length: length,
      lengthChecked: lengthIsProofed,
      lengthError: checkingLengthErrorCount,
    };

    if (easyCheck) {
      return success;
    } else {
      return result;
    }
  },

  /**
   * set the combatinformation for the report (main-function)
   *
   * @param object combatArray	>> all important information init
   * @param object combatside		>> for what side to set the information
   */
  loadData: function (combatArray, combatside) {
    var $this = ogame.messages.combatreport;
    $this.setCombatArray(combatArray, combatside);
    $this.loadDataBySelectedCombatMember(combatArray, combatside);
    $this.setCombatValue();
  },

  /**
   * set roundspecific information
   *
   * @see ogame.messages.combatreport.setCombatText for params
   */
  loadDataBySelectedRound: function (attackerCombatArray, defenderCombatArray, selectedRound) {
    var $this = ogame.messages.combatreport;
    var round2show = parseInt(selectedRound);
    var attacker = $(".attacker .participant_select option:selected").val(); // get the selected attacker

    var defender = $(".defender .participant_select option:selected").val(); // get the selected defender

    var attacker_coords = $(".attacker .participant_select option:selected").data("coords"); // get the coordinates of selected attacker planet (if planet selected)

    var defender_coords = $(".defender .participant_select option:selected").data("coords"); // get the coordinates of selected defender planet (if planet selected)

    var attacker_fleetID = [];
    var defender_fleetID = []; // check if planet was selected, if so we need to push its fleet Id in fleetIDs and sanitize the member name

    if (typeof attacker_coords != "undefined") {
      ids = attacker.split("|")[1].split(":");
      attacker_fleetID.push(ids);
      attacker = attacker.split("|")[0];
    } else if (attacker != "all" && typeof attacker != "undefined") {
      // else if a member selected then we need to push the Ids of all its fleets in fleetIDs
      attacker_coords = 0;
      $(".attacker .participant_select option").each(function () {
        if (attacker == $(this).val().split("|")[0] && typeof $(this).val().split("|")[1] != "undefined") {
          attacker_fleetID.push($(this).val().split("|")[1].split(":"));
        }
      });
    } else {
      // no specifications if all was selected, attacker_fleetID must remain empty
      attacker_fleetID = [];
    }

    if (typeof defender_coords != "undefined") {
      ids = defender.split("|")[1].split(":");
      defender_fleetID.push(ids);
      defender = defender.split("|")[0];
    } else if (defender != "all" && typeof defender != "undefined") {
      // else if a member selected then we need to push the Ids of all its fleets in fleetIDs
      defender_coords = 0;
      $(".defender .participant_select option").each(function () {
        if (defender == $(this).val().split("|")[0] && typeof $(this).val().split("|")[1] != "undefined") {
          defender_fleetID.push($(this).val().split("|")[1].split(":"));
        }
      });
    } else {
      // no specifications if all was selected, defender_fleetID must remain empty
      defender_fleetID = [];
    } // check if fleetID was not empty , then we need to get results of just this Fleets

    if (attacker_fleetID.length > 0) {
      for (var level1 in attackerCombatArray) {
        if (level1 == "combatRounds") {
          for (var level2 in attackerCombatArray[level1][round2show]) {
            if (level2 == "ships") {
              for (var shipIds in attackerCombatArray[level1][round2show][level2]) {
                var exist = $.inArray(shipIds, attacker_fleetID[0]); // check if we need the result of this fleet Id

                if (exist == -1) {
                  // if not , then remove its informations from the global result
                  delete attackerCombatArray[level1][round2show][level2][shipIds];
                }
              }
            }
          }
        }
      }
    }

    if (defender_fleetID.length > 0) {
      for (var level1 in defenderCombatArray) {
        if (level1 == "combatRounds") {
          for (var level2 in defenderCombatArray[level1][round2show]) {
            if (level2 == "ships") {
              for (var shipIds in defenderCombatArray[level1][round2show][level2]) {
                var exist = $.inArray(shipIds, defender_fleetID[0]); // check if we need the result of this fleet Id

                if (exist == -1) {
                  // if not , then remove its informations from the global result
                  delete defenderCombatArray[level1][round2show][level2][shipIds];
                }
              }
            }
          }
        }
      }
    }

    var attackerCombatRounds = attackerCombatArray["combatRounds"];
    var defenderCombatRounds = defenderCombatArray["combatRounds"];

    if (
      $this.check(true, attackerCombatRounds, {
        length: selectedRound,
      })
    ) {
      round2show = round2show - 1;
    }

    round2show = round2show + "";
    $this.setCombatside("attacker");
    $this.setShipCount4All(attackerCombatRounds[round2show]);
    $this.setCombatside("defender");
    $this.setShipCount4All(defenderCombatRounds[round2show]);

    if ($this.data.memberSelection) {
      //$this.resetDropDowns();
    }

    $this.setCombatText(attackerCombatRounds, defenderCombatRounds, selectedRound);
  },

  /**
   * set the 2 dropdowns to no selected member
   */
  resetDropDowns: function () {
    $("#attacker_select_combatreport").val("all").ogameDropDown("refresh");
    $("#defender_select_combatreport").val("all").ogameDropDown("refresh");
  },

  /**
   * set all memberspecific information
   *
   * @param combatArray object	>> all important information init
   * @param combatside object		>> for what side to set the information
   * @param coords string         >> coordinates of planet
   * @param planetType int              >> 1 = planet, 3 = moon
   */
  loadDataBySelectedCombatMember: function (originalCombatArray, combatside, coords, planetType) {
    // var combatArray = jQuery.extend({}, originalCombatArray); //shallow copy
    var combatArray = jQuery.extend(true, {}, originalCombatArray); //deep copy
    // make sure that these params have a "proper" value

    coords = coords || 0;
    planetType = planetType || 1;
    var $this = ogame.messages.combatreport;
    $this.setCombatArray(combatArray, combatside); // set all ships of a combatside and get all active ships

    var ships = $("." + $this.data.combatside + " .buildingimg"); // Add defenses as well

    var defense = $("." + $this.data.combatside + " .defenseimg");
    ships = $.merge(ships, defense);
    var activeShips = $this.getShipsByMembersAndCoords($this.data.activeMember, coords, planetType); //set isActive-Flag to shippictures
    $this.setActiveFlag4Fleet(ships, activeShips);
    $this.displayShipData(
      $this.data.activeMember,
      $this.data.combatside,
      combatArray["combatRounds"][combatArray["combatRounds"].length - 1],
      combatArray["member"],
      coords,
      planetType,
    );
    $this.setCombatValue();
  },
};
