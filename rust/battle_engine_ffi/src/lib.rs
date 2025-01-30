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
    attacker_units: HashMap<i16, BattleUnitInfo>,
    defender_units: HashMap<i16, BattleUnitInfo>,
}

/// Battle unit info which is provided by the PHP client.
///
/// This contains static information about the input units and their amount.
#[derive(Serialize, Deserialize, Clone)]
struct BattleUnitInfo {
    unit_id: i16,
    amount: u32,
    attack_power: f32,
    shield_points: f32,
    hull_plating: f32,
    rapidfire: HashMap<i16, u16>,
}

/// Battle unit count to keep track of the amount of units of a certain type.
#[derive(Serialize, Deserialize, Clone)]
struct BattleUnitCount {
    unit_id: i16,
    amount: u32,
}

/// Battle unit instance which is used to keep track of indidivual units and their current health during battle.
#[derive(Serialize, Deserialize, Clone)]
struct BattleUnitInstance {
    unit_id: i16,
    current_shield_points: f32,
    current_hull_plating: f32,
}

/// Battle round which is used to keep track of the battle statistics for a single round.
#[derive(Serialize, Deserialize)]
struct BattleRound {
    /// The units of the attacker remaining at the end of the round.
    attacker_ships: HashMap<i16, BattleUnitCount>,
    /// The units of the defender remaining at the end of the round.
    defender_ships: HashMap<i16, BattleUnitCount>,
    /// Unit losses of the attacker until now which includes previous rounds.
    attacker_losses: HashMap<i16, BattleUnitCount>,
    /// Unit losses of the defender until now which includes previous rounds.
    defender_losses: HashMap<i16, BattleUnitCount>,
    /// Unit losses of the attacker in this round.
    attacker_losses_in_round: HashMap<i16, BattleUnitCount>,
    /// Unit losses of the defender in this round.
    defender_losses_in_round: HashMap<i16, BattleUnitCount>,
    /// Total amount of damage absorbed by the attacker this round.
    absorbed_damage_attacker: f64,
    /// Total amount of damage absorbed by the defender this round.
    absorbed_damage_defender: f64,
    /// Total amount of full strength of the attacker at the start of the round.
    full_strength_attacker: f64,
    /// Total amount of full strength of the defender at the start of the round.
    full_strength_defender: f64,
    /// Total amount of hits the attacker made this round.
    hits_attacker: u32,
    /// Total amount of hits the defender made this round.
    hits_defender: u32,
}

/// Memory metrics which is used to keep track of the peak memory usage during the battle.
///
/// This is only used for debugging purposes and not actually consumed by the PHP client.
#[derive(Serialize, Deserialize)]
struct MemoryMetrics {
    peak_memory: u64, // in kilobytes
}

/// Battle output which is returned to the PHP client.
///
/// This contains the battle statistics and memory metrics. Memory metrics are only used
/// for debugging purposes when called from battle_engine_debug Rust project.
#[derive(Serialize, Deserialize)]
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

    // Create individual ships from provided battle unit info which contains the amount
    let mut attacker_units = expand_units(&input.attacker_units);
    let mut defender_units = expand_units(&input.defender_units);

    // Track peak memory usage for debugging purposes
    update_peak_memory(&mut peak_memory);

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
        process_combat(&mut attacker_units, &mut defender_units, &mut round, &input.attacker_units, &input.defender_units, true);
        process_combat(&mut defender_units, &mut attacker_units, &mut round, &input.defender_units, &input.attacker_units, false);

        // Cleanup round
        cleanup_round(&mut round, &mut attacker_units, &mut defender_units, &input.attacker_units, &input.defender_units);

        // Update round statistics
        round.attacker_ships = compress_units(&attacker_units);
        round.defender_ships = compress_units(&defender_units);

        // Calculate accumulated losses
        calculate_losses(&mut round, &input.attacker_units, &input.defender_units);

        rounds.push(round);

         // Track peak memory usage for debugging purposes
        update_peak_memory(&mut peak_memory);
    }

    BattleOutput {
        rounds,
        memory_metrics: MemoryMetrics {
            peak_memory,
        },
    }
}

/// Expands unit information into individual unit objects, allowing the engine to track the state
/// of each unit (e.g., shields and hull points) independently during combat.
fn expand_units(units: &HashMap<i16, BattleUnitInfo>) -> Vec<BattleUnitInstance> {
    let mut expanded = Vec::new();
    for (_, unit) in units {
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
    units.iter()
        // Loop over all units and count the amount of units per unit_id.
        .fold(HashMap::new(), |mut counts, unit| {
            // Increment count for each unit_id
            *counts.entry(unit.unit_id).or_insert(0) += 1;
            counts
        })
        .into_iter()
        // Convert counts hashmap to expected BattleUnitCount hashmap
        .map(|(unit_id, count)| {
            (unit_id, BattleUnitCount {
                unit_id,
                amount: count,
            })
        })
        .collect()
}

/// Simulates combat for a single round between two groups of units.
///
/// # Why:
/// This function handles the core mechanics of combat by calculating damage, updating
/// unit health, and determining if a unit can attack again (via rapidfire). It also
/// updates statistics for the battle round to reflect the results.
///
/// # Parameters:
/// - `attackers`: Units attacking in this phase.
/// - `defenders`: Units being attacked in this phase.
/// - `round`: Stores round statistics, such as hits and absorbed damage.
/// - `attacker_unit_metadata`: Metadata for attacker units to determine damage, rapidfire, etc.
/// - `defender_unit_metadata`: Metadata for defender units to determine max shield points etc.
/// - `is_attacker`: Whether the current phase is attacker-to-defender or vice versa.
fn process_combat(
    attackers: &mut Vec<BattleUnitInstance>,
    defenders: &mut Vec<BattleUnitInstance>,
    round: &mut BattleRound,
    attacker_unit_metadata: &HashMap<i16, BattleUnitInfo>,
    defender_unit_metadata: &HashMap<i16, BattleUnitInfo>,
    is_attacker: bool,
) {
    let mut rng = rand::thread_rng();

    for attacker in attackers.iter() {
        let mut continue_attacking = true;

        // Get metadata of the attacking unit.
        let attacker_metadata = attacker_unit_metadata.get(&attacker.unit_id).unwrap();
        let damage = attacker_metadata.attack_power;

        while continue_attacking {
            continue_attacking = false;

            // Select a random defender as a target
            let target_idx = rng.gen_range(0..defenders.len());
            let target = &mut defenders[target_idx];

            // Get metadata of the defending unit.
            let target_metadata = defender_unit_metadata.get(&target.unit_id).unwrap();

            // Check if the damage is less than 1% of the target's shield points. If so,
            // attack is negated.
            if damage < (0.01 * target_metadata.shield_points) {
                continue
            }

            // Apply damage to shields first, then hull plating
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

            // If hull integrity < 70%, then unit can explode randomly. Roll dice to see if it does.
            if target.current_hull_plating / target_metadata.hull_plating < 0.7 {
                let explosion_chance = 100.0 - ((target.current_hull_plating / target_metadata.hull_plating) * 100.0);
                let roll = rng.gen_range(0..=100);
                if roll < explosion_chance as i32 {
                    // Unit explodes, set current hull plating and shield points to 0.
                    target.current_hull_plating = 0.0;
                    target.current_shield_points = 0.0;
                }
            }

            // Update round statistics for hits and damage absorbed
            if is_attacker {
                round.hits_attacker += 1;
                round.full_strength_attacker += damage as f64;
                round.absorbed_damage_defender += shield_absorption as f64;
            } else {
                round.hits_defender += 1;
                round.full_strength_defender += damage as f64;
                round.absorbed_damage_attacker += shield_absorption as f64;
            }

            // Check if the current unit has rapidfire against the target unit. If so, then
            // roll dice to see if the current unit can attack again.
            continue_attacking = if let Some(rapidfire_amount) = attacker_metadata.rapidfire.get(&target.unit_id) {
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
                // and continue_attacking is set to true which will cause the loop to continue.
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
/// - Rolling dice for hull integrity < 70% of original if the unit is also destroyed.
/// - Applying shield regeneration.
/// - Calculate the total damage dealt by the attacker and defender and calculate shield absorption stats.
fn cleanup_round(
    round: &mut BattleRound,
    attackers: &mut Vec<BattleUnitInstance>,
    defenders: &mut Vec<BattleUnitInstance>,
    units_metadata_attacker: &HashMap<i16, BattleUnitInfo>,
    units_metadata_defender: &HashMap<i16, BattleUnitInfo>,
) {
    // -------
    // Cleanup attacker units.
    // -------
    // First remove destroyed units.
    attackers.retain(|unit| {
        // Check if unit is fully destroyed.
        if unit.current_hull_plating <= 0.0 {
            increment_battle_unit_count_amount(&mut round.attacker_losses_in_round, unit.unit_id, 1);
            return false;
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
        // Check if unit is fully destroyed.
        if unit.current_hull_plating <= 0.0 {
            increment_battle_unit_count_amount(&mut round.defender_losses_in_round, unit.unit_id, 1);
            return false;
        }

        true
    });

    // Then update shields in separate pass for remaining units.
    for unit in defenders.iter_mut() {
        let unit_metadata = units_metadata_defender.get(&unit.unit_id).unwrap();
        unit.current_shield_points = unit_metadata.shield_points;
    }
}

/// Calculate the losses for the attacker and defender in this round compared to the starting
/// units before the battle.
fn calculate_losses(
    round: &mut BattleRound,
    initial_attacker: &HashMap<i16, BattleUnitInfo>,
    initial_defender: &HashMap<i16, BattleUnitInfo>,
) {
    // Calculate losses by comparing current counts with initial counts
    for (_, unit) in initial_attacker {
        let initial_count = unit.amount;
        let current_count = round.attacker_ships.get(&unit.unit_id).map(|unit| unit.amount).unwrap_or(0);

        if current_count < initial_count {
            let loss_amount = initial_count - current_count;
            increment_battle_unit_count_amount(&mut round.attacker_losses, unit.unit_id, loss_amount);
        }
    }

    // Do the same for defender
    for (_, unit) in initial_defender {
        let initial_count = unit.amount;
        let current_count = round.defender_ships.get(&unit.unit_id).map(|unit| unit.amount).unwrap_or(0);

        if current_count < initial_count {
            let loss_amount = initial_count - current_count;
            increment_battle_unit_count_amount(&mut round.defender_losses, unit.unit_id, loss_amount);
        }
    }
}

/// Helper method to increment the amount property of a BattleUnitCount struct.
fn increment_battle_unit_count_amount(hash_map: &mut HashMap<i16, BattleUnitCount>, unit_id: i16, amount_to_increment: u32) {
    let count = hash_map.entry(unit_id).or_insert(BattleUnitCount {
        unit_id,
        amount: 0,
    });
    count.amount += amount_to_increment;
}

/// Update the peak memory usage statistics. Only used for debugging purposes.
fn update_peak_memory(current_peak: &mut u64) {
    if let Some(usage) = memory_stats() {
        *current_peak = (*current_peak).max(usage.physical_mem as u64 / 1024);
    }
}