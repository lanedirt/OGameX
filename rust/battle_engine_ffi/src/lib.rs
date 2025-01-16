use serde::{Deserialize, Serialize};
use std::ffi::{CStr, CString};
use std::os::raw::c_char;
use rand::Rng;
use std::collections::HashMap;

#[derive(Debug, Serialize, Deserialize, Clone)]
struct BattleUnitMetadata {
    // TODO: optimize field types to make them as lean as possible.
    unit_id: i32,
    amount: i32,
    attack_power: f64,
    shield_points: f64,
    hull_plating: f64,
}

#[derive(Debug, Serialize, Deserialize, Clone)]
struct BattleUnitInstance {
    // TODO: optimize field types to make them as lean as possible.
    unit_id: i32,
    current_shield_points: f64,
    current_hull_plating: f64,
}

#[derive(Debug, Serialize, Deserialize, Clone)]
struct BattleRound {
    // Attacker and defender ships arrays are used by the battle logic to retrieve the metadata from
    // such as attack power, shield etc. So these need stay with metadata.
    attacker_ships: HashMap<i32, BattleUnitMetadata>,
    defender_ships: HashMap<i32, BattleUnitMetadata>,
    // TODO: the losses properties are just for keeping track of amount of units per type, so they
    // don't need to contain the full metadata. Check if we want to reduce amount of memory by using
    // more lean objects for this or if its negligible.
    attacker_losses: HashMap<i32, BattleUnitMetadata>,
    defender_losses: HashMap<i32, BattleUnitMetadata>,
    attacker_losses_in_round: HashMap<i32, BattleUnitMetadata>,
    defender_losses_in_round: HashMap<i32, BattleUnitMetadata>,
    absorbed_damage_attacker: f64,
    absorbed_damage_defender: f64,
    full_strength_attacker: f64,
    full_strength_defender: f64,
    hits_attacker: i32,
    hits_defender: i32,
}

#[derive(Serialize, Deserialize)]
pub struct BattleInput {
    attacker_units: Vec<BattleUnitMetadata>,
    defender_units: Vec<BattleUnitMetadata>,
}

#[derive(Debug, Serialize, Deserialize)]
pub struct BattleOutput {
    rounds: Vec<BattleRound>,
}

#[no_mangle]
pub extern "C" fn fight_battle_rounds(input_json: *const c_char) -> *mut c_char {
    let input_str = unsafe { CStr::from_ptr(input_json).to_str().unwrap() };
    let battle_input: BattleInput = serde_json::from_str(input_str).unwrap();
    let battle_output = process_battle_rounds(battle_input);
    let result_json = serde_json::to_string(&battle_output).unwrap();
    let c_str = CString::new(result_json).unwrap();
    c_str.into_raw()
}

/**
 * Expand unit information into separate unit objects as for every unit in the battle we need to
 * keep track of its own shield and hull plating information.
 */
fn expand_units(units: &Vec<BattleUnitMetadata>) -> Vec<BattleUnitInstance> {
    let mut expanded = Vec::new();
    for unit in units {
        for _ in 0..unit.amount {
            expanded.push(BattleUnitInstance {
                unit_id: unit.unit_id.clone(),
                current_shield_points: unit.shield_points,
                current_hull_plating: unit.hull_plating
            });
        }
    }

    expanded
}

/**
 * Compress hashmap of unit instances into a single unit metadata object which stores the amount of units
 * instead of having a separate object for each unit.
 */
fn compress_units(units: &Vec<BattleUnitInstance>) -> HashMap<i32, BattleUnitMetadata> {
    let mut unit_counts: HashMap<i32, i32> = HashMap::new();

    // Count occurrences of each unit_id.
    for unit in units {
        *unit_counts.entry(unit.unit_id).or_insert(0) += 1;
    }

    // Convert counts to metadata objects and return it.
    let compressed = unit_counts.into_iter()
        .map(|(unit_id, count)| {
            (unit_id, BattleUnitMetadata {
                unit_id,
                amount: count,
                attack_power: 0.0, // TODO: Not necessary here
                shield_points: 0.0, // TODO: Not necessary here
                hull_plating: 0.0, // TODO: Not necessary here
            })
        })
        .collect();

    compressed
}

/**
 * Convert unit metadata vector to hashmap for quicker lookups via unit id as index.
 */
fn convert_unit_metadata_to_hashmap(units: &Vec<BattleUnitMetadata>) -> HashMap<i32, BattleUnitMetadata> {
    let mut expanded = HashMap::new();
    for unit in units {
        for _ in 0..unit.amount {
            expanded.insert(unit.unit_id.clone(), BattleUnitMetadata {
                unit_id: unit.unit_id.clone(),
                amount: unit.amount,
                attack_power: unit.attack_power,
                shield_points: unit.shield_points,
                hull_plating: unit.hull_plating
            });
        }
    }

    expanded
}

/*
fn compress_units(units: &Vec<BattleUnit>) -> Vec<BattleUnit> {
    let mut unit_counts: HashMap<String, (BattleUnit, i32)> = HashMap::new();

    for unit in units {
        if unit.current_hull_plating > 0.0 {
            unit_counts.entry(unit.unit_id.clone())
                .and_modify(|(_, count)| *count += 1)
                .or_insert((unit.clone(), 1));
        } else {
            // Ensure destroyed units are included with count 0
            unit_counts.entry(unit.unit_id.clone())
                .or_insert((unit.clone(), 0));
        }
    }

    unit_counts.into_iter()
        .map(|(_, (mut unit, count))| {
            unit.structural_integrity = count as f64;
            unit
        })
        .collect()
}*/

pub fn process_battle_rounds(input: BattleInput) -> BattleOutput {
    let mut rounds = Vec::new();

    // Create global static state for unit information such as shield, attack power etc.
    // These values don't change and are the same for all unit instances, so we only need to
    // track this globally and not per unit.

    // Expand units into individual ships
    let mut attacker_units = expand_units(&input.attacker_units);
    let mut defender_units = expand_units(&input.defender_units);

    // Convert unit metadata from vector to hashmap so they can be accessed via index of unit id.
    let mut attacker_remaining_ships = convert_unit_metadata_to_hashmap(&input.attacker_units);
    let mut defender_remaining_ships = convert_unit_metadata_to_hashmap(&input.defender_units);

    // Fight up to 6 rounds
    for _ in 0..6 {
        if attacker_units.is_empty() || defender_units.is_empty() {
            break;
        }

        // TODO: do we need to create an initial round above and copy the counts here?
        // Check PHP implementation, this doesn't seem right.
        let mut round = BattleRound {
            attacker_ships: attacker_remaining_ships.clone(),
            defender_ships: defender_remaining_ships.clone(),
            attacker_losses: HashMap::new(),
            defender_losses: HashMap::new(),
            attacker_losses_in_round: HashMap::new(),
            defender_losses_in_round: HashMap::new(),
            absorbed_damage_attacker: 0.0,
            absorbed_damage_defender: 0.0,
            full_strength_attacker: 0.0,
            full_strength_defender: 0.0,
            hits_attacker: 0,
            hits_defender: 0,
        };

        // Process combat
        process_combat(&mut attacker_units, &mut defender_units, &mut round, true);
        process_combat(&mut defender_units, &mut attacker_units, &mut round, false);

        // Cleanup round
        cleanup_round(&mut round, &mut attacker_units, &mut defender_units);

        // Update round statistics
        round.attacker_ships = compress_units(&attacker_units);
        round.defender_ships = compress_units(&defender_units);

        // Calculate losses
        calculate_losses(&mut round, &input.attacker_units, &input.defender_units);

        rounds.push(round);
    }

    BattleOutput { rounds }
}

fn process_combat(
    attackers: &mut Vec<BattleUnitInstance>,
    defenders: &mut Vec<BattleUnitInstance>,
    round: &mut BattleRound,
    is_attacker: bool,
) {
    let mut rng = rand::thread_rng();

    // Get attack power from metadata information which is available in round object in attacker
    // or defender ships based on which side we're on currently.
    let mut metadata = HashMap::new();
    if is_attacker {
        metadata = round.attacker_ships.clone();
    }
    else {
        metadata = round.defender_ships.clone();
    }

    attackers.retain(|unit| {
        if defenders.is_empty() {
            return true;
        }

        let target_idx = rng.gen_range(0..defenders.len());
        let target = &mut defenders[target_idx];

        // Get metadata of this unit.
        let unit_metadata = metadata.get(&unit.unit_id).unwrap();

        let damage = unit_metadata.attack_power;

        if damage < (0.01 * target.current_shield_points) {
            return true;
        }

        let mut shield_absorption = 0.0;

        if target.current_shield_points > 0.0 {
            if damage <= target.current_shield_points {
                shield_absorption = damage;
                target.current_shield_points -= damage;
            } else {
                shield_absorption = target.current_shield_points;
                target.current_hull_plating -= damage - target.current_shield_points;
                target.current_shield_points = 0.0;
            }
        } else {
            target.current_hull_plating -= damage;
        }

        // Update statistics
        if is_attacker {
            round.hits_attacker += 1;
            round.full_strength_attacker += damage;
            round.absorbed_damage_defender += shield_absorption;
        } else {
            round.hits_defender += 1;
            round.full_strength_defender += damage;
            round.absorbed_damage_attacker += shield_absorption;
        }

        // TODO: implement rapidfire mechanism
        true
    });
}

/**
 * Clean up the round after all units have attacked each other.
 *
 * This method handles:
 * - Removing destroyed units from the attacker and defender unit arrays.
 * - Rolling a dice for hull integrity < 70% of original if the unit is also destroyed.
 * - Applying shield regeneration.
 * - Calculate the total damage dealt by the attacker and defender and calculate shield absorption stats.
 */
fn cleanup_round(
    round: &mut BattleRound,
    attackers: &mut Vec<BattleUnitInstance>,
    defenders: &mut Vec<BattleUnitInstance>,
) {
    let mut rng = rand::thread_rng();

    // -------
    // Cleanup attacker units.
    // -------
    let metadata = round.attacker_ships.clone();
    attackers.retain(|unit| {
        // 1. Check if unit is fully destroyed.
        if unit.current_hull_plating <= 0.0 {
            increment_unit_metadata_amount(&mut round.attacker_losses_in_round, unit.unit_id, 1);
            return false;
        }

        // 2. Check hull integrity < 70%
        let unit_metadata = metadata.get(&unit.unit_id).unwrap();
        if unit.current_hull_plating / unit_metadata.hull_plating < 0.7 {
            let explosion_chance = 100.0 - ((unit.current_hull_plating / unit_metadata.hull_plating) * 100.0);
            let roll = rng.gen_range(0..=100);
            if roll < explosion_chance as i32 {
                increment_unit_metadata_amount(&mut round.attacker_losses_in_round, unit.unit_id, 1);
                return false;
            }
        }

        true
    });

    // Then update shields in separate pass
    for unit in attackers.iter_mut() {
        let unit_metadata = metadata.get(&unit.unit_id).unwrap();
        unit.current_shield_points = unit_metadata.shield_points;
    }

    // -------
    // Cleanup defender units.
    // -------

    // First remove destroyed units.
    let metadata = round.defender_ships.clone();
    defenders.retain(|unit| {
        // 1. Check if unit is fully destroyed.
        if unit.current_hull_plating <= 0.0 {
            increment_unit_metadata_amount(&mut round.defender_losses_in_round, unit.unit_id, 1);
            return false;
        }

        // 2. Check hull integrity < 70%
        let unit_metadata = metadata.get(&unit.unit_id).unwrap();
        if unit.current_hull_plating / unit_metadata.hull_plating < 0.7 {
            let explosion_chance = 100.0 - ((unit.current_hull_plating / unit_metadata.hull_plating) * 100.0);
            let roll = rng.gen_range(0..=100);
            if roll < explosion_chance as i32 {
                increment_unit_metadata_amount(&mut round.defender_losses_in_round, unit.unit_id, 1);
                return false;
            }
        }

        true
    });

    // Then update shields in separate pass for remaining units.
    for unit in defenders.iter_mut() {
        let unit_metadata = metadata.get(&unit.unit_id).unwrap();
        unit.current_shield_points = unit_metadata.shield_points;
    }

    // Cleanup defender units.
    /*let metadata = round.attacker_ships.clone();
    defenders.retain(|unit| {
        // 1. Check if unit is fully destroyed.
        if unit.current_hull_plating <= 0.0 {
            // Current unit is destroyed because hull plating reached 0.
            // Add unit to attacker losses hashmap.
            increment_unit_metadata_amount(&mut round.defender_losses_in_round, unit.unit_id, 1);
            // Remove unit from attacker units in this round by returning false to this parent retain.
            return false
        }

        // 2. Check if unit hull integrity is < 70% of original. If so, roll a dice to determine
        // if it's destroyed as well.
        let unit_metadata = metadata.get(&unit.unit_id).unwrap();
        if unit.current_hull_plating / unit_metadata.hull_plating < 0.7 {
            // When the hull plating of the unit is < 70% of original, the unit has 1 - currentHullPlating/originalHullPlating chance of exploding.
            // This method rolls a dice and returns TRUE if the unit explodes, FALSE otherwise.
            // TODO: implement rng, for now we return false (i.e. unit is considered destroyed)
            return false;
        }

        // Apply shield generation to the unit.
        unit.current_shield_points = unit_metadata.shield_points;

        true
    });*/

    /*
    // Cleanup attacker units.
        foreach ($attackerUnits as $key => $unit) {
            if ($unit->currentHullPlating <= 0) {
                // Remove destroyed units from the array.
                $round->attackerLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($attackerUnits[$key]);
            } elseif ($unit->damagedHullExplosion()) {
                // Hull was damaged and dice roll was successful, destroy the unit.
                $round->attackerLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($attackerUnits[$key]);
            } else {
                // Apply shield regeneration.
                $unit->currentShieldPoints = $unit->originalShieldPoints;
            }
        }

        // Cleanup defender units.
        foreach ($defenderUnits as $key => $unit) {
            if ($unit->currentHullPlating <= 0) {
                // Remove destroyed units from the array.
                $round->defenderLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($defenderUnits[$key]);
            } elseif ($unit->damagedHullExplosion()) {
                // Hull was damaged and dice roll was successful, destroy the unit.
                $round->defenderLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($defenderUnits[$key]);
            } else {
                // Apply shield regeneration.
                $unit->currentShieldPoints = $unit->originalShieldPoints;
            }
        }
     */
}

/**
 * Helper method to increment the amount property of a unit metadata struct.
 */
fn increment_unit_metadata_amount(hash_map: &mut HashMap<i32, BattleUnitMetadata>, unit_id: i32, amount_to_increment: i32) {
    let count = hash_map.entry(unit_id).or_insert(BattleUnitMetadata {
        unit_id: unit_id,
        amount: 0,
        attack_power: 0.0, // TODO: Not necessary here
        shield_points: 0.0, // TODO: Not necessary here
        hull_plating: 0.0, // TODO: Not necessary here
    });
    count.amount += amount_to_increment;
}

fn calculate_losses(
    round: &mut BattleRound,
    initial_attacker: &Vec<BattleUnitMetadata>,
    initial_defender: &Vec<BattleUnitMetadata>,
) {
    // Calculate losses by comparing current counts with initial counts
    for unit in initial_attacker {
        let initial_count = unit.amount;
        let current_count = round.attacker_ships.get(&unit.unit_id).map(|unit| unit.amount).unwrap_or(0);

        if current_count < initial_count {
            let loss_amount = initial_count - current_count;
            increment_unit_metadata_amount(&mut round.attacker_losses_in_round, unit.unit_id, loss_amount);
        }
    }

    // Do the same for defender
    for unit in initial_defender {
        let initial_count = unit.amount;
        let current_count = round.defender_ships.get(&unit.unit_id).map(|unit| unit.amount).unwrap_or(0);

        if current_count < initial_count {
            let loss_amount = initial_count - current_count;
            increment_unit_metadata_amount(&mut round.defender_losses_in_round, unit.unit_id, loss_amount);
        }
    }

    // Update total losses
    round.attacker_losses.extend(round.attacker_losses_in_round.clone());
    round.defender_losses.extend(round.defender_losses_in_round.clone());
}
