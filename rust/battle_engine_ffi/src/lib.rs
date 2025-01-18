//! # Battle Engine FFI
//!
//! `battle_engine_ffi` is the Rust implementation of the OGameX battle engine.
//!
//! This Rust library is called from the PHP client RustBattleEngine.php via FFI (Foreign Function Interface)
//! and takes the battle input in JSON, processes the battle rounds and returns the battle output in JSON.
//!
//! This battle engine is functionally equivalent to the OGameX PHP battle engine but is optimized
//! for performance and memory usage. It is up to 200x faster than the equivalent PHP implementation
//! and uses up to 10x less memory.
use serde::{Deserialize, Serialize};
use std::ffi::{CStr, CString};
use std::os::raw::c_char;
use rand::Rng;
use std::collections::HashMap;
use memory_stats::memory_stats;

/// Battle input which is provided by the PHP client.
#[derive(Serialize, Deserialize)]
pub struct BattleInput {
    attacker_units: Vec<BattleUnitInfo>,
    defender_units: Vec<BattleUnitInfo>,
}

/// Battle unit info which is provided by the PHP client.
///
/// This contains static information about the input units and their amount.
#[derive(Debug, Serialize, Deserialize, Clone)]
struct BattleUnitInfo {
    unit_id: i16,
    amount: u32,
    attack_power: f32,
    shield_points: f32,
    hull_plating: f32,
    rapidfire: HashMap<i16, u16>,
}

/// Battle unit count to keep track of the amount of units of a certain type.
#[derive(Debug, Serialize, Deserialize, Clone)]
struct BattleUnitCount {
    unit_id: i16,
    amount: u32,
}

/// Battle unit instance which is used to keep track of indidivual units and their current health during battle.
#[derive(Debug, Serialize, Deserialize, Clone)]
struct BattleUnitInstance {
    unit_id: i16,
    current_shield_points: f32,
    current_hull_plating: f32,
}

/// Battle round which is used to keep track of the battle statistics for a single round.
#[derive(Debug, Serialize, Deserialize)]
struct BattleRound {
    attacker_ships: HashMap<i16, BattleUnitCount>,
    defender_ships: HashMap<i16, BattleUnitCount>,
    attacker_losses: HashMap<i16, BattleUnitCount>,
    defender_losses: HashMap<i16, BattleUnitCount>,
    attacker_losses_in_round: HashMap<i16, BattleUnitCount>,
    defender_losses_in_round: HashMap<i16, BattleUnitCount>,
    absorbed_damage_attacker: f64,
    absorbed_damage_defender: f64,
    full_strength_attacker: f64,
    full_strength_defender: f64,
    hits_attacker: u32,
    hits_defender: u32,
}

/// Memory metrics which is used to keep track of the peak memory usage during the battle.
///
/// This is only used for debugging purposes and not actually consumed by the PHP client.
#[derive(Debug, Serialize, Deserialize)]
struct MemoryMetrics {
    peak_memory: u64, // in kilobytes
}

/// Battle output which is returned to the PHP client.
///
/// This contains the battle statistics and memory metrics. Memory metrics are only used
/// for debugging purposes when called from battle_engine_debug Rust project.
#[derive(Debug, Serialize, Deserialize)]
pub struct BattleOutput {
    rounds: Vec<BattleRound>,
    memory_metrics: MemoryMetrics,
}

/// FFI interface to process the battle rounds and return the battle output.
///
/// This is the method which is called from the PHP client in RustBattleEngine.php.
#[no_mangle]
pub extern "C" fn fight_battle_rounds(input_json: *const c_char) -> *mut c_char {
    let input_str = unsafe { CStr::from_ptr(input_json).to_str().unwrap() };
    let battle_input: BattleInput = serde_json::from_str(input_str).unwrap();
    let battle_output = process_battle_rounds(battle_input);
    let result_json = serde_json::to_string(&battle_output).unwrap();
    let c_str = CString::new(result_json).unwrap();
    c_str.into_raw()
}

/// Process the battle rounds and return the battle output.
fn process_battle_rounds(input: BattleInput) -> BattleOutput {
    let mut peak_memory = 0;
    let mut rounds = Vec::new();

    // Expand units into individual ships
    let mut attacker_units = expand_units(&input.attacker_units);
    let mut defender_units = expand_units(&input.defender_units);

    // Track peak memory usage for debugging purposes
    peak_memory = peak_memory.max(get_process_memory_usage());
    // ---

    // Convert unit metadata from vector to hashmap so they can be accessed via index of unit id.
    let attacker_metadata = convert_unit_metadata_to_hashmap(&input.attacker_units);
    let defender_metadata = convert_unit_metadata_to_hashmap(&input.defender_units);

    // Fight up to 6 rounds
    for _ in 0..6 {
        if attacker_units.is_empty() || defender_units.is_empty() {
            break;
        }

        let mut round = BattleRound {
            attacker_ships: HashMap::new(),
            defender_ships: HashMap::new(),
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
        process_combat(&mut attacker_units, &mut defender_units, &mut round, &attacker_metadata, true);
        process_combat(&mut defender_units, &mut attacker_units, &mut round, &defender_metadata, false);

        // Cleanup round
        cleanup_round(&mut round, &mut attacker_units, &mut defender_units, &attacker_metadata, &defender_metadata);

        // Update round statistics
        round.attacker_ships = compress_units(&attacker_units);
        round.defender_ships = compress_units(&defender_units);

        // Calculate accumulated losses
        calculate_losses(&mut round, &attacker_metadata, &defender_metadata);

        rounds.push(round);

        // Track peak memory usage for debugging purposes
        peak_memory = peak_memory.max(get_process_memory_usage());
        // ---
    }

    BattleOutput {
        rounds,
        memory_metrics: MemoryMetrics {
            peak_memory,
        },
    }
}

/// Expand unit information into separate unit objects as for every unit in the battle we need to
/// keep track of its own shield and hull plating information.
fn expand_units(units: &Vec<BattleUnitInfo>) -> Vec<BattleUnitInstance> {
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

/// Compress individual unit instances into a single unit metadata object which stores the amount of units
/// instead of having a separate object for each unit. This is for only passing data about total amount
/// of units per type.
fn compress_units(units: &Vec<BattleUnitInstance>) -> HashMap<i16, BattleUnitCount> {
    let mut unit_counts: HashMap<i16, u32> = HashMap::new();

    // Count occurrences of each unit_id.
    for unit in units {
        *unit_counts.entry(unit.unit_id).or_insert(0) += 1;
    }

    // Convert counts to metadata objects and return it.
    let compressed = unit_counts.into_iter()
        .map(|(unit_id, count)| {
            (unit_id, BattleUnitCount {
                unit_id,
                amount: count,
            })
        })
        .collect();

    compressed
}

/// Convert unit metadata vector to hashmap for quicker lookups via index which is the unit id.
fn convert_unit_metadata_to_hashmap(units: &Vec<BattleUnitInfo>) -> HashMap<i16, BattleUnitInfo> {
    let mut expanded = HashMap::new();
    for unit in units {
        for _ in 0..unit.amount {
            expanded.insert(unit.unit_id.clone(), BattleUnitInfo {
                unit_id: unit.unit_id,
                amount: unit.amount,
                attack_power: unit.attack_power,
                shield_points: unit.shield_points,
                hull_plating: unit.hull_plating,
                rapidfire: unit.rapidfire.clone(),
            });
        }
    }

    expanded
}


/// Get the current memory usage in kilobytes. Only used for debugging purposes.
fn get_process_memory_usage() -> u64 {
    if let Some(usage) = memory_stats() {
        usage.physical_mem as u64 / 1024
    } else {
        0
    }
}

/// Process combat for a single round between attacker and defender.
/// This method is called twice, once for [attacker --> defender] and once for [defender --> attacker].
fn process_combat(
    attackers: &mut Vec<BattleUnitInstance>,
    defenders: &mut Vec<BattleUnitInstance>,
    round: &mut BattleRound,
    units_metadata: &HashMap<i16, BattleUnitInfo>,
    is_attacker: bool,
) {
    let mut rng = rand::thread_rng();

    for attacker in attackers.iter() {
        let mut should_continue = true;

        // Get metadata of this unit.
        let unit_metadata = units_metadata.get(&attacker.unit_id).unwrap();
        let damage = unit_metadata.attack_power;

        while should_continue {
            // Set should continue to false in case of early continue.
            should_continue = false;

            // Pick a random target from defenders.
            let target_idx = rng.gen_range(0..defenders.len());
            let target = &mut defenders[target_idx];

            // Check if the damage is less than 1% of the target's shield points. If so, attack is negated.
            if damage < (0.01 * target.current_shield_points) {
                continue
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
                round.full_strength_attacker += damage as f64;
                round.absorbed_damage_defender += shield_absorption as f64;
            } else {
                round.hits_defender += 1;
                round.full_strength_defender += damage as f64;
                round.absorbed_damage_attacker += shield_absorption as f64;
            }

            // Calculate rapidfire against the target unit which determines if this unit can attack again
            // and loop should continue.
            should_continue = if let Some(rapidfire_amount) = unit_metadata.rapidfire.get(&target.unit_id) {
                // Rapidfire chance is calculated as 100 - (100 / amount). For example:
                // - rapidfire amount of 4 means 100 - (100 / 4) = 75% chance.
                // - rapidfire amount of 10 means 100 - (100 / 10) = 90% chance.
                // - rapidfire amount of 33 means 100 - (100 / 33) = 96.97%
                let chance = 100.0 / *rapidfire_amount as f64;
                let rounded_chance = (chance * 100.0).floor() / 100.0;
                let rapidfire_chance = 100.0 - rounded_chance;

                // Roll for rapidfire
                let roll = rng.gen_range(0.0..100.0);

                // If the roll is less than or equal to the rapidfire chance, the unit can attack again
                // and rapidfire is set to true which will cause the loop to continue.
                roll <= rapidfire_chance
            } else {
                false
            }
        }
    }
}

/// Clean up the round after all units have attacked each other.
///
/// This method handles:
/// - Removing destroyed units from the attacker and defender unit arrays.
/// - Rolling a dice for hull integrity < 70% of original if the unit is also destroyed.
/// - Applying shield regeneration.
/// - Calculate the total damage dealt by the attacker and defender and calculate shield absorption stats.
fn cleanup_round(
    round: &mut BattleRound,
    attackers: &mut Vec<BattleUnitInstance>,
    defenders: &mut Vec<BattleUnitInstance>,
    units_metadata_attacker: &HashMap<i16, BattleUnitInfo>,
    units_metadata_defender: &HashMap<i16, BattleUnitInfo>,
) {
    let mut rng = rand::thread_rng();

    // -------
    // Cleanup attacker units.
    // -------
    // First remove destroyed units.
    attackers.retain(|unit| {
        // 1. Check if unit is fully destroyed.
        if unit.current_hull_plating <= 0.0 {
            increment_unit_metadata_amount(&mut round.attacker_losses_in_round, unit.unit_id, 1);
            return false;
        }

        // 2. Check hull integrity < 70%
        let unit_metadata = units_metadata_attacker.get(&unit.unit_id).unwrap();
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
        let unit_metadata = units_metadata_attacker.get(&unit.unit_id).unwrap();
        unit.current_shield_points = unit_metadata.shield_points;
    }

    // -------
    // Cleanup defender units.
    // -------
    // First remove destroyed units.
    defenders.retain(|unit| {
        // 1. Check if unit is fully destroyed.
        if unit.current_hull_plating <= 0.0 {
            increment_unit_metadata_amount(&mut round.defender_losses_in_round, unit.unit_id, 1);
            return false;
        }

        // 2. Check hull integrity < 70%
        let unit_metadata = units_metadata_defender.get(&unit.unit_id).unwrap();
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
        let unit_metadata = units_metadata_defender.get(&unit.unit_id).unwrap();
        unit.current_shield_points = unit_metadata.shield_points;
    }
}

/// Helper method to increment the amount property of a unit metadata struct.
fn increment_unit_metadata_amount(hash_map: &mut HashMap<i16, BattleUnitCount>, unit_id: i16, amount_to_increment: u32) {
    let count = hash_map.entry(unit_id).or_insert(BattleUnitCount {
        unit_id,
        amount: 0,
    });
    count.amount += amount_to_increment;
}

/// Calculate the losses for the attacker and defender.
fn calculate_losses(
    round: &mut BattleRound,
    initial_attacker: &HashMap<i16, BattleUnitInfo>,
    initial_defender: &HashMap<i16, BattleUnitInfo>,
) {
    // TODO: is it correct that we're using the initial metadata to calculate losses for every round?
    // So the attacker and defender losses for each round are accumulative?
    // E.g. round 1 loses 10 units = 10 units. Round 2 loses 10 units = 20 units. etc. Shouldn't
    // we be using the current round's metadata to calculate losses?
    // EDIT: should be okay now as the attacker_losses and defender_losses should be accumulative
    // and it is reset/recalculated for every round and does not get added to the previous or next round's losses.
    // Double check this though with PHP implementation.

    // Calculate losses by comparing current counts with initial counts
    for (_, unit) in initial_attacker {
        let initial_count = unit.amount;
        let current_count = round.attacker_ships.get(&unit.unit_id).map(|unit| unit.amount).unwrap_or(0);

        if current_count < initial_count {
            let loss_amount = initial_count - current_count;
            increment_unit_metadata_amount(&mut round.attacker_losses, unit.unit_id, loss_amount);
        }
    }

    // Do the same for defender
    for (_, unit) in initial_defender {
        let initial_count = unit.amount;
        let current_count = round.defender_ships.get(&unit.unit_id).map(|unit| unit.amount).unwrap_or(0);

        if current_count < initial_count {
            let loss_amount = initial_count - current_count;
            increment_unit_metadata_amount(&mut round.defender_losses, unit.unit_id, loss_amount);
        }
    }
}
